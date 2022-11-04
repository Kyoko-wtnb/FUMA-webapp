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
		// auth is set in routes.php this is duplicate
		// $this->middleware('auth');

		// Store user
		// Replace by Auth::user()
		// $this->user = Auth::user();
	}

	public function authcheck($jobID){
		$email = Auth::user()->email;
		$check = DB::table('gene2func')->where('jobID', $jobID)->first();
		if($check->email==$email){
			return view('pages.gene2func', ['status'=>'getJob', 'id'=>$jobID, 'page'=>'gene2func', 'prefix'=>'gene2func']);
		}else{
			return view('pages.gene2func', ['status'=>null, 'id'=>$jobID, 'page'=>'gene2func', 'prefix'=>'gene2func']);
		}
	}

	public function getJobList(){
		$email = Auth::user()->email;

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

	public function gene2funcSubmit(Request $request){
		$date = date('Y-m-d H:i:s');
		$jobID;
		$filedir;
		$email = Auth::user()->email;

		if($request->filled('title')){
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

		if($request -> filled('genes')){
			$gtype = "text";
			$gval = $request -> input('genes');
			$gval = preg_split('/[\n\r]+/', $gval);
			$gval = implode(':', $gval);
		}else{
			$gtype = "file";
			$gval = $_FILES["genesfile"]["name"];
			$request -> file('genesfile')->move($filedir, $_FILES["genesfile"]["name"]);
		}

		if($request -> filled('genetype')){
			$bkgtype = "select";
			$bkgval = $request -> input('genetype');
			$bkgval = implode(':', $bkgval);
		}else if($request -> filled('bkgenes')){
			$bkgtype = "text";
			$bkgval = $request -> input('bkgenes');
			$bkgval = preg_split('/[\n\r]+/', $bkgval);
			$bkgval = implode(':', $bkgval);
		}else{
			$bkgtype ="file";
			$bkgval = $_FILES["bkgenesfile"]["name"];
			$request -> file('bkgenesfile') -> move($filedir, $_FILES["bkgenesfile"]["name"]);
		}

		$ensembl = $request->input('ensembl');

		$gsFileN = (int)$request -> input('gsFileN');
		$gsFiles = "NA";
		if($gsFileN>0){
			$gsFiles = [];
			$n = 1;
			while(count($gsFiles)<$gsFileN){
				$id = (string) $n;
				if($request->hasFile("gsFile".$id)){
					$tmp_filename = $_FILES["gsFile".$id]["name"];
					$request -> file("gsFile".$id)->move($filedir, $tmp_filename);
					$gsFiles[] = $tmp_filename;
				}
				$n++;
			}
			$gsFiles = implode(":", $gsFiles);
		}

		$gene_exp = implode(":", $request->input("gene_exp"));

		if($request -> filled('MHC')){
			$MHC = 1;
		}else{
			$MHC = 0;
		}

		$adjPmeth = $request -> input('adjPmeth');
		$adjPcut = $request -> input('adjPcut');
		$minOverlap = $request -> input('minOverlap');

		$app_config = parse_ini_file(scripts_path('app.config'), false, INI_SCANNER_RAW);

		// write parameters to config file
		$paramfile = $filedir.'params.config';
		File::put($paramfile, "[jobinfo]\n");
		File::append($paramfile, "created_at=$date\n");
		File::append($paramfile, "title=$title\n");

		File::append($paramfile, "\n[version]\n");
		File::append($paramfile, "FUMA=".$app_config['FUMA']."\n");
		File::append($paramfile, "MsigDB=".$app_config['MsigDB']."\n");
		File::append($paramfile, "WikiPathways=".$app_config['WikiPathways']."\n");
		File::append($paramfile, "GWAScatalog=".$app_config['GWAScatalog']."\n");

		File::append($paramfile, "\n[params]\n");
		File::append($paramfile, "gtype=$gtype\n");
		File::append($paramfile, "gval=$gval\n");
		File::append($paramfile, "bkgtype=$bkgtype\n");
		File::append($paramfile, "bkgval=$bkgval\n");
		File::append($paramfile, "ensembl=$ensembl\n");
		File::append($paramfile, "gsFileN=$gsFileN\n");
		File::append($paramfile, "gsFiles=$gsFiles\n");
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
			'ensembl' => $ensembl,
			'gene_exp' => $gene_exp,
			'MHC' => $MHC,
			'adjPmeth' => $adjPmeth,
			'adjPcut' => $adjPcut,
			'minOverlap' => $minOverlap
		]);

		return view('pages.gene2func', ['status'=>'query', 'id'=>$jobID, 'page'=>'gene2func', 'prefix'=>'gene2func']);
	}

	public function geneQuery(Request $request){
		$filedir = $request -> input('filedir');

		$script = scripts_path('gene2func.R');
		exec("Rscript $script $filedir", $output, $error);

		$script = scripts_path('GeneSet.py');
		exec("python $script $filedir", $output2, $error2);
		exec("find ".$filedir." -type d -exec chmod 775 {} \;");
		exec("find ".$filedir." -type f -exec chmod 664 {} \;");
	}

	public function snp2geneGeneQuery(Request $request){
		$s2gID = $request -> input('jobID');

		$checkExists = DB::table('gene2func')->where('snp2gene', $s2gID)->first();
		if($checkExists==null){
			$date = date('Y-m-d H:i:s');
			$jobID;
			$filedir;
			$email = Auth::user()->email;

			if($request->filled('title')){
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
			$ensembl = $params['ensembl'];
			$gene_exp = $params['magma_exp'];
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
			File::append($paramfile, "ensembl=$ensembl\n");
			File::append($paramfile, "gsFileN=0\n");
			File::append($paramfile, "gsFiles=NA\n");
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
				'ensembl' => $ensembl,
				'gene_exp' => $gene_exp,
				'MHC' => $MHC,
				'adjPmeth' => $adjPmeth,
				'adjPcut' => $adjPcut,
				'minOverlap' => $minOverlap
			]);
			return view('pages.gene2func', ['status'=>'query', 'id'=>$jobID, 'page'=>'gene2func', 'prefix'=>'gene2func']);
		}else{
			$jobID = $checkExists->jobID;
			return redirect("gene2func/".$jobID);
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
