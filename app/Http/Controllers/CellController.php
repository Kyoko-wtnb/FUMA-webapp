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
use fuma\User;
use fuma\Jobs\celltypeProcess;

class CellController extends Controller
{
	protected $user;

    public function __construct(){
		// Protect this Controller
		// auth is set in routes.php this is duplicate
		//$this->middleware('auth');
		// Store user
		// Replace by Auth::user()
		// $this->user = Auth::user();
    }

	public function authcheck($jobID){
		$email = Auth::user()->email;
		$check = DB::table('celltype')->where('jobID', $jobID)->first();
		if($check->email==$email){
			return view('pages.celltype', ['id'=>$jobID, 'status'=>'jobquery', 'page'=>'celltype', 'prefix'=>'celltype']);
		}else{
			return view('pages.celltype', ['id'=>null, 'status'=>null, 'page'=>'celltype', 'prefix'=>'celltype']);
		}
	}

	public function checkJobStatus($jobID){
        $job = DB::table('celltype')->where('jobID', $jobID)
            ->where('email', Auth::user()->email)->first();
        if(! $job){
            return "Notfound";
        }
        return $job->status;
    }

	public function getS2GIDs(){
		$email = Auth::user()->email;
		$results = DB::select('SELECT jobID, title FROM SubmitJobs WHERE email=? AND status="OK"', [$email]);
		return $results;
	}

	public function checkMagmaFile(Request $request){
		$id = $request->input('id');
		if(File::exists(config('app.jobdir').'/jobs/'.$id."/magma.genes.raw")){
			return 1;
		}else{
			return 0;
		}
	}

	public function getJobList(){
		$email = Auth::user()->email;

		if($email){
		    $results = DB::table('celltype')->where('email', $email)
		        ->orderBy('created_at', 'desc')
		        ->get();
		}else{
		    $results = array();
		}

		$this->queueNewJobs();

		return response()->json($results);
    }

	public function queueNewJobs(){
		$user = Auth::user();
		$email = $user->email;
		$newJobs = DB::table('celltype')->where('email', $email)->where('status', 'NEW')->get()->all();
		if(count($newJobs)>0){
			foreach($newJobs as $job){
				$jobID = $job->jobID;
				DB::transaction(function () {
					DB::table('celltype') -> where('jobID', $jobID)
						-> update(['status'=>'QUEUED']);	
				});
				$this->dispatch(new celltypeProcess($user, $jobID));
			}
		}
		return;
	}

	public function deleteJob(Request $request){
		$jobID = $request->input('jobID');
		File::deleteDirectory(config('app.jobdir').'/celltype/'.$jobID);
		DB::table('celltype')->where('jobID', $jobID)->delete();
		return;
	}

	public function newJob(Request $request){
		$date = date('Y-m-d H:i:s');
		$email = Auth::user()->email;
		$s2gID = $request->input('s2gID');
		$ensg = 0;
		if($request->filled('ensg_id')){$ensg=1;}
		if($s2gID>0){$ensg=1;}
		$ds = implode(":", $request -> input('cellDataSets'));
		$adjPmeth = $request -> input('adjPmeth');
		$step2 = 0;
		if($request->filled('step2')){$step2=1;}
		$step3 = 0;
		if($request->filled('step3')){$step3=1;}
		if($request->filled("title")){
			$title = $request -> input('title');
		}else{
			$title = "None";
		}
		$s2gTitle = "None";
		if($s2gID>0){
			$s2gTitle = DB::table('SubmitJobs')->where('jobID', $s2gID)
							->first()->title;
		}
		if($s2gID==0){
			DB::table('celltype')->insert(
				['title'=>$title, 'email'=>$email, 'created_at'=>$date, 'status'=>'NEW']
			);
		}else{
			DB::table('celltype')->insert(
				['title'=>$title, 'email'=>$email, 'snp2gene'=>$s2gID,
				'snp2geneTitle'=>$s2gTitle, 'created_at'=>$date, 'status'=>'NEW']
			);
		}
		$jobID = DB::table('celltype')->where('email', $email)->where('created_at', $date)->first()->jobID;
		$filedir = config('app.jobdir').'/celltype/'.$jobID;
		File::makeDirectory($filedir);
		if($s2gID==0){
			$request -> file('genes_raw')->move($filedir, "magma.genes.raw");
		}else{
			$s2gfiledir = config('app.jobdir').'/jobs/'.$s2gID.'/';
			File::copy($s2gfiledir.'magma.genes.raw', $filedir.'/magma.genes.raw');
		}

		if($s2gID==0){$s2gID = "NA";}
		$inputfile = "NA";
		if($request -> hasFile('genes_raw')){
			$inputfile = $_FILES["genes_raw"]["name"];
		}
		$app_config = parse_ini_file(scripts_path("app.config"), false, INI_SCANNER_RAW);
		$paramfile = $filedir.'/params.config';
		File::put($paramfile, "[jobinfo]\n");
		File::append($paramfile, "created_at=$date\n");
		File::append($paramfile, "title=$title\n");

		File::append($paramfile, "\n[version]\n");
		File::append($paramfile, "FUMA=".$app_config['FUMA']."\n");
		File::append($paramfile, "MAGMA=".$app_config['MAGMA']."\n");

		File::append($paramfile, "\n[params]\n");
		File::append($paramfile, "snp2geneID=$s2gID\n");
		File::append($paramfile, "inputfile=$inputfile\n");
		File::append($paramfile, "ensg_id=$ensg\n");
		File::append($paramfile, "datasets=$ds\n");
		File::append($paramfile, "adjPmeth=$adjPmeth\n");
		File::append($paramfile, "step2=$step2\n");
		File::append($paramfile, "step3=$step3\n");

		return redirect("/celltype#joblist");
	}

	public function checkFileList(Request $request){
		$id = $request->input('id');
		$filedir = config('app.jobdir').'/celltype/'.$id;
		$params = parse_ini_file($filedir."/params.config", false, INI_SCANNER_RAW);
		if($params['MAGMA']=="v1.06"){
			$step1 = count(glob($filedir."/*.gcov.out"));
			$step1_2 = 0;
			$step2 = 0;
			$step3 = 0;
		}else{
			$step1 = count(glob($filedir."/*.gsa.out"));
			$step1_2 = (int) File::exists($filedir."/step1_2_summary.txt");
			$step2 = (int) File::exists($filedir."/magma_celltype_step2.txt");
			$step3 = (int) File::exists($filedir."/magma_celltype_step3.txt");
		}
		return json_encode([$step1, $step1_2, $step2, $step3]);
	}

	public function getDataList(Request $request){
		$id = $request->input('id');
		$filedir = config('app.jobdir').'/celltype/'.$id;
		$params = parse_ini_file($filedir."/params.config", false, INI_SCANNER_RAW);
		$ds = explode(":", $params['datasets']);
		return json_encode($ds);
	}

	public function filedown(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);

		$checked = $request->input('files');
		$files = [];
		$files[] = "params.config";

		if(in_array("step1", $checked)){
			$ds = explode(":", $params['datasets']);
			if($params['MAGMA']=="v1.06"){
				for($i=0; $i<count($ds); $i++){
					$files[] = "magma_celltype_".$ds[$i].".gcov.out";
					$files[] = "magma_celltype_".$ds[$i].".log";
				}
			}else{
				for($i=0; $i<count($ds); $i++){
					$files[] = "magma_celltype_".$ds[$i].".gsa.out";
					$files[] = "magma_celltype_".$ds[$i].".log";
				}
			}
			$files[] = "magma_celltype_step1.txt";
		}
		if(in_array("step1_2", $checked)){
			$files[] = "step1_2_summary.txt";
		}
		if(in_array("step2", $checked)){
			$files[] = "magma_celltype_step2.txt";
		}
		if(in_array("step3", $checked)){
			$files[] = "magma_celltype_step3.txt";
		}

		$zip = new \ZipArchive();
		$zipfile = $filedir."FUMA_celltype".$id.".zip";
		if(File::exists($zipfile)){
			File::delete($zipfile);
		}
		$zip -> open($zipfile, \ZipArchive::CREATE);
		$zip->addFile(public_path().'/README_cell', "README_cell");
		foreach($files as $f){
			if(FILE::exists($filedir.$f)){
				$zip->addFile($filedir.$f, $f);
			}
		}
		$zip -> close();
		return response() -> download($zipfile);
	}

	public function getPerDatasetData(Request $request){
		$id = $request->input('id');
		$ds = $request->input('ds');
		$filedir = config('app.jobdir').'/celltype/'.$id.'/';
		$script = scripts_path('celltype_perDatasetPlotData.py');
		$json = shell_exec("python $script $filedir $ds");
		return $json;
	}

	public function getStepPlotData(Request $request){
		$id = $request->input('id');
		$ds = $request->input('ds');
		$filedir = config('app.jobdir').'/celltype/'.$id.'/';
		$script = scripts_path('celltype_stepPlotData.py');
		$json = shell_exec("python $script $filedir");
		return $json;
	}
}
