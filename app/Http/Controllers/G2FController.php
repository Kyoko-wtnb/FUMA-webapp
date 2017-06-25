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
		$file = $request -> input('file');
		$id = $request -> input('id');
		$filedir = config('app.jobdir').'/gene2func/'.$id.'/'.$file;
		return response() -> download($filedir);
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
			$params = parse_ini_file($s2gfiledir.'params.config');
			// $Xchr = preg_split("/[\t]/", chop($params[9]))[1];
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

	public function DEGPlot($type, $jobID){
		$filedir = config('app.jobdir').'/gene2func/'.$jobID.'/';
		$file = "";
		if($type=="general"){
			$file = $filedir."DEGgeneral.txt";
		}else{
			$file = $filedir."DEG.txt";
		}
		if(file_exists($file)){
			$f = fopen($file, 'r');
			fgetcsv($f, 0, "\t");
			$data = [];
			$upp = [];
			$downp = [];
			$twop = [];
			$alph = [];
			$i = 0;
			while($row=fgetcsv($f, 0, "\t")){
				$p[$row[1]] = $row[4];
				$data[] = [$row[0], $row[1], $row[4], $row[5]];
				if($row[0]=="DEG.up"){
					$upp[$row[1]] = $row[4];
					$alph[$row[1]] = $i;
					$i++;
				}else if($row[0]=="DEG.down"){
					$downp[$row[1]] = $row[4];
				}else{
					$twop[$row[1]] = $row[4];
				}
			}
			asort($upp);
			asort($downp);
			asort($twop);
			$order_up = [];
			$order_down = [];
			$order_two = [];
			$i = 0;
			foreach ($upp as $key => $value) {
				$order_up[$key] = $i;
				$i++;
			}
			$i = 0;
			foreach ($downp as $key => $value) {
				$order_down[$key] = $i;
				$i++;
			}
			$i = 0;
			foreach ($twop as $key => $value) {
				$order_two[$key] = $i;
				$i++;
			}

			$r = ["data"=>$data, "order"=>["up"=>$order_up, "down"=>$order_down, "two"=>$order_two, "alph"=>$alph]];
			return json_encode($r);
		}else{
			return;
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
