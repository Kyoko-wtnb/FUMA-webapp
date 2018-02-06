<?php

namespace fuma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use fuma\SubmitJob;
use fuma\Http\Requests;
use fuma\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use View;
use Auth;
use Storage;
use File;
use JavaScript;
use Mail;
use fuma\User;
use fuma\Jobs\snp2geneProcess;

class G2FController extends Controller
{
	protected $user;

	public function __construct(){
		// Protect this Controller
		$this->middleware('auth');

		// Store user
		$this->user = Auth::user();
	}

	public function authcheck($jobID){
		$email = $this->user->email;
		$check = DB::table('gene2func')->where('jobID', $jobID)->first();
		if($check->email==$email){
			return view('pages.gene2func', ['status'=>'getJob', 'id'=>$jobID]);
		}else{
			return view('pages.gene2func', ['status'=>null, 'id'=>$jobID]);
		}
	}

	public function getJobList(){
		$email = $this->user->email;

		if($email){
			$results = DB::table('gene2func')->where('email', $email)
				->orderBy('created_at', 'desc')
				->get();
		}else{
			$results = array();
		}

		return response()->json($results);
	}

	public function deleteJob(Request $request){
		$jobID = $request->input('jobID');
		File::deleteDirectory(config('app.jobdir').'/gene2func/'.$jobID);
		DB::table('gene2func')->where('jobID', $jobID)->delete();
		return;
	}

	public function filedown(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/gene2func/'.$id.'/';
		$files = [];
		if($request->has('summaryfile')){$files[] = "summary.txt";}
		if($request->has('paramfile')){$files[] = "params.config";}
		if($request->has('geneIDfile')){$files[] = "geneIDs.txt";}
		if($request->has('expfile')){
			$tmp = File::glob($filedir."*_exp.txt");
			for($i=0; $i<count($tmp); $i++){
				$files[] = preg_replace("/.*\/(.*_exp.txt)/", "$1", $tmp[$i]);
			}
		}
		if($request->has('DEGfile')){
			$tmp = File::glob($filedir."*_DEG.txt");
			for($i=0; $i<count($tmp); $i++){
				$files[] = preg_replace("/.*\/(.*_DEG.txt)/", "$1", $tmp[$i]);
			}
		}
		if($request->has('gsfile')){$files[] = "GS.txt";}

		$zip = new \ZipArchive();
		if($prefix=="gwas/g2f"){
			$zipfile = $filedir."FUMA_gene2func_gwas".$id.".zip";
		}else{
			$zipfile = $filedir."FUMA_gene2func".$id.".zip";
		}
		if(File::exists($zipfile)){
			File::delete($zipfile);
		}
		$zip -> open($zipfile, \ZipArchive::CREATE);
		$zip->addFile(storage_path().'/README_g2f', "README_g2f");
		foreach($files as $f){
			$zip->addFile($filedir.$f, $f);
		}
		$zip -> close();
		return response() -> download($zipfile);
	}

	public function gene2funcSubmit(Request $request){
		$date = date('Y-m-d H:i:s');
		$jobID;
		$filedir;
		$email = $this->user->email;

		if($request->has('title')){
			$title = $request->input('title');
		}else{
			$title = "None";
		}

		DB::table('gene2func')->insert(
			['title'=>$title, 'email'=>$email, 'created_at'=>$date]
		);

		// Get jobID (automatically generated)
		$jobID = DB::table('gene2func')->where('email', $email)->where('created_at', $date)->first()->jobID;
		$filedir = config('app.jobdir').'/gene2func/'.$jobID;
		File::makeDirectory($filedir);
		$filedir = $filedir.'/';

		if($request -> has('genes')){
			$gtype = "text";
			$gval = $request -> input('genes');
			$gval = preg_split('/[\n\r]+/', $gval);
			$gval = implode(':', $gval);
		}else{
			$gtype = "file";
			$gval = "genesQuery.txt";
			$request -> file('genesfile')->move($filedir, "genesQuery.txt");
		}

		if($request -> has('genetype')){
			$bkgtype = "select";
			$bkgval = $request -> input('genetype');
			$bkgval = implode(':', $bkgval);
		}else if($request -> has('bkgenes')){
			$bkgtype = "text";
			$bkgval = $request -> input('bkgenes');
			$bkgval = preg_split('/[\n\r]+/', $bkgval);
			$bkgval = implode(':', $bkgval);
		}else{
			$bkgtype ="file";
			$bkgval = "bkgenes.txt";
			$request -> file('bkgenesfile') -> move($filedir, "bkgenes.txt");
		}

		$gene_exp = implode(":", $request->input("gene_exp"));

		if($request -> has('MHC')){
			$MHC = 1;
		}else{
			$MHC = 0;
		}

		$adjPmeth = $request -> input('adjPmeth');
		$adjPcut = $request -> input('adjPcut');
		$minOverlap = $request -> input('minOverlap');

		// write parameters to config file
		$paramfile = $filedir.'params.config';
		File::put($paramfile, "[jobinfo]\n");
		File::append($paramfile, "created_at=$date\n");
		File::append($paramfile, "title=$title\n");
		File::append($paramfile, "\n[params]\n");
		File::append($paramfile, "gtype=$gtype\n");
		File::append($paramfile, "gval=$gval\n");
		File::append($paramfile, "bkgtype=$bkgtype\n");
		File::append($paramfile, "bkgval=$bkgval\n");
		File::append($paramfile, "gene_exp=$gene_exp\n");
		File::append($paramfile, "MHC=$MHC\n");
		File::append($paramfile, "adjPmeth=$adjPmeth\n");
		File::append($paramfile, "adjPcut=$adjPcut\n");
		File::append($paramfile, "minOverlap=$minOverlap\n");

		JavaScript::put([
			'id' => $jobID,
			'filedir' => $filedir,
			'gtype' => $gtype,
			'gval' => $gval,
			'bkgtype' => $bkgtype,
			'bkgval' => $bkgval,
			// 'Xchr' => $Xchr,
			'gene_exp' => $gene_exp,
			'MHC' => $MHC,
			'adjPmeth' => $adjPmeth,
			'adjPcut' => $adjPcut,
			'minOverlap' => $minOverlap
		]);

		return view('pages.gene2func', ['status'=>'query', 'id'=>$jobID]);
	}

	public function geneQuery(Request $request){
		$filedir = $request -> input('filedir');
		$gtype = $request -> input('gtype');
		$gval = $request -> input('gval');
		$bkgtype = $request -> input('bkgtype');
		$bkgval = $request -> input('bkgval');
		$MHC = $request -> input('MHC');
		$adjPmeth = $request -> input('adjPmeth');
		$adjPcut = $request -> input('adjPcut');
		$minOverlap = $request -> input('minOverlap');

		$script = storage_path()."/scripts/gene2func.R";
		exec("Rscript $script $filedir", $output, $error);

		$script = storage_path()."/scripts/GeneSet.py";
		exec("python $script $filedir", $output2, $error2);
	}

	public function snp2geneGeneQuery(Request $request){
		$s2gID = $request -> input('jobID');

		$checkExists = DB::table('gene2func')->where('snp2gene', $s2gID)->first();
		if($checkExists==null){
			$date = date('Y-m-d H:i:s');
			$jobID;
			$filedir;
			$email = $this->user->email;

			if($request->has('title')){
				$title = $request->input('title');
			}else{
				$title = "None";
			}

			$s2gTitle = DB::table('SubmitJobs')->where('jobID', $s2gID)->first()->title;

			DB::table('gene2func')->insert(
				['title'=>$title,'email'=>$email, 'snp2gene'=>$s2gID, 'snp2geneTitle'=>$s2gTitle, 'created_at'=>$date]
			);

			// Get jobID (automatically generated)
			$jobID = DB::table('gene2func')->where('snp2gene', $s2gID)->first()->jobID;
			$filedir = config('app.jobdir').'/gene2func/'.$jobID;
			File::makeDirectory($filedir);
			$filedir = $filedir.'/';

			$s2gfiledir = config('app.jobdir').'/jobs/'.$s2gID.'/';
			$gtype="text";
			$bkgtype="select";
			$params = parse_ini_file($s2gfiledir.'params.config', false, INI_SCANNER_RAW);
			// $Xchr = preg_split("/[\t]/", chop($params[9]))[1];
			$gene_exp = $params['gene_exp'];
			$MHC = $params['exMHC'];
			$bkgval = $params['genetype'];
			$adjPmeth = "fdr_bh";
			$adjPcut = 0.05;
			$minOverlap = 2;

			$gval = null;
			$f = fopen($s2gfiledir."genes.txt", 'r');
			fgetcsv($f, 0, "\t");
			while($row = fgetcsv($f, 0, "\t")){
				if($gval==null){
					$gval = $row[0];
				}else{
					$gval = $gval.":".$row[0];
				}
			}

			$paramfile = $filedir.'params.config';
			File::put($paramfile, "[jobinfo]\n");
			File::append($paramfile, "created_at=$date\n");
			File::append($paramfile, "title=$title\n");
			File::append($paramfile, "snp2geneID=$s2gID\n");
			File::append($paramfile, "snp2geneTitle=$s2gTitle\n");
			File::append($paramfile, "\n[params]\n");
			File::append($paramfile, "gtype=$gtype\n");
			File::append($paramfile, "gval=$gval\n");
			File::append($paramfile, "bkgtype=$bkgtype\n");
			File::append($paramfile, "bkgval=$bkgval\n");
			File::append($paramfile, "MHC=$MHC\n");
			File::append($paramfile, "gene_exp=$gene_exp\n");
			File::append($paramfile, "adjPmeth=$adjPmeth\n");
			File::append($paramfile, "adjPcut=$adjPcut\n");
			File::append($paramfile, "minOverlap=$minOverlap\n");

			JavaScript::put([
				'id' => $jobID,
				'filedir' => $filedir,
				'gtype' => $gtype,
				'gval' => $gval,
				'bkgtype' => $bkgtype,
				'bkgval' => $bkgval,
				'gene_exp' => $gene_exp,
				'MHC' => $MHC,
				'adjPmeth' => $adjPmeth,
				'adjPcut' => $adjPcut,
				'minOverlap' => $minOverlap
			]);
			return view('pages.gene2func', ['status'=>'query', 'id'=>$jobID]);
		}else{
			$jobID = $checkExists->jobID;
			return redirect("gene2func/".$jobID);
		}
	}

	public function paramTable(Request $request){
		$id = $request -> input('id');
		$filedir = config('app.jobdir').'/gene2func/'.$id.'/';
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);
		$out = [];
		foreach($params as $key=>$value){
			$out[] = [$key, $value];
		}
		return json_encode($out);
    }

	public function sumTable(Request $request){
		$id = $request -> input('id');
		$filedir = config('app.jobdir').'/gene2func/'.$id.'/';
		$lines = file($filedir."summary.txt");
		$out = [];
		foreach($lines as $l){
			$l = preg_split("/\t/", chop($l));
			$out[] = [$l[0], $l[1]];
		}
		return json_encode($out);
	}

	public function expDataOption(Request $request){
		$id = $request -> input('id');
		$filedir = config('app.jobdir').'/gene2func/'.$id.'/';
		$params = parse_ini_file($filedir.'params.config', false, INI_SCANNER_RAW);
		return $params['gene_exp'];
	}

	public function expPlot($prefix, $id, $dataset){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$script = storage_path()."/scripts/g2f_expPlot.py";
	    $data = shell_exec("python $script $filedir $dataset");
		return $data;
	}

	public function DEGPlot($prefix, $id){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$script = storage_path()."/scripts/g2f_DEGPlot.py";
	    $data = shell_exec("python $script $filedir");
		return $data;
	}

	public function geneTable(Request $request){
		$jobID = $request->input('id');
		$filedir = config('app.jobdir').'/gene2func/'.$jobID.'/';
		if(file_exists($filedir."geneTable.txt")){
			$f = fopen($filedir."geneTable.txt", 'r');
			$head = fgetcsv($f, 0, "\t");
			$head[] = "GeneCard";
			$all_rows = [];
			while($row = fgetcsv($f, 0, "\t")){
				if(strcmp($row[3], "NA")!=0){
					$row[3] = '<a href="https://www.omim.org/entry/'.$row[3].'" target="_blank">'.$row[3].'</a>';
				}
				if(strcmp($row[5], "NA")!=0){
					$db = explode(":", $row[5]);
					$row[5] = "";
					foreach ($db as $i){
						if(strlen($row[5])==0){
							$row[5] = '<a href="https://www.drugbank.ca/drugs/'.$i.'" target="_blank">'.$i.'</a>';
						}else{
							$row[5] .= ', <a href="https://www.drugbank.ca/drugs/'.$i.'" target="_blank">'.$i.'</a>';
						}
					}
				}
				$row[] = '<a href="http://www.genecards.org/cgi-bin/carddisp.pl?gene='.$row[2].'" target="_blank">GeneCard</a>';
				$all_rows[] = array_combine($head, $row);
			}

			$json = array('data'=>$all_rows);
			return json_encode($json);
		}else{
			return '{"data": []}';
		}
	}

	public function ExpTsPlot($type, $jobID){
		$filedir = config('app.jobdir').'/gene2func/'.$jobID.'/';
		$file = "";
		if($type=="general"){
			$file = $filedir."ExpTsGeneral.txt";
		}else{
			$file = $filedir."ExpTs.txt";
		}
		if(file_exists($file)){
			$f = fopen($file, 'r');
			fgetcsv($f, 0, "\t");
			$data = [];
			$p = [];
			while($row=fgetcsv($f, 0, "\t")){
				$p[$row[0]] = $row[3];
				$data[] = [$row[0], $row[3], $row[4]];
			}
			asort($p);
			$order_p = [];
			$i = 0;
			foreach ($p as $key => $value) {
				$order_p[$key] = $i;
				$i++;
			}
			ksort($p);
			$order_alph = [];
			$i = 0;
			foreach ($p as $key => $value) {
				$order_alph[$key] = $i;
				$i++;
			}
			$r = ["data"=>$data, "order"=>["p"=>$order_p, "alph"=>$order_alph]];
			return json_encode($r);
		}else{
			return;
		}
  }
}
