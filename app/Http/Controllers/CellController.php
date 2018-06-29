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
		$this->middleware('auth');

		// Store user
		$this->user = Auth::user();
    }

	public function authcheck($jobID){
		$email = $this->user->email;
		$check = DB::table('celltype')->where('jobID', $jobID)->first();
		if($check->email==$email){
			return view('pages.celltype', ['id'=>$jobID, 'status'=>'jobquery', 'page'=>'celltype', 'prefix'=>'celltype']);
		}else{
			return view('pages.celltype', ['id'=>null, 'status'=>null, 'page'=>'celltype', 'prefix'=>'celltype']);
		}
	}

	public function checkJobStatus($jobID){
        $job = DB::table('celltype')->where('jobID', $jobID)
            ->where('email', $this->user->email)->first();
        if(! $job){
            return "Notfound";
        }
        return $job->status;
    }

	public function getS2GIDs(){
		$email = $this->user->email;
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
		$email = $this->user->email;

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
		$user = $this->user;
		$email = $user->email;
		$newJobs = DB::table('celltype')->where('email', $email)->where('status', 'NEW')->get();
		if(count($newJobs)>0){
			foreach($newJobs as $job){
				$jobID = $job->jobID;
				DB::table('celltype') -> where('jobID', $jobID)
					-> update(['status'=>'QUEUED']);
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
		$email = $this->user->email;
		$s2gID = $request->input('s2gID');
		$ensg = 0;
		if($request->has('ensg_id')){$ensg=1;}
		if($s2gID>0){$ensg=0;}
		$ds = implode(":", $request -> input('cellDataSets'));
		if($request->has("title")){
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
		$app_config = parse_ini_file(storage_path()."/scripts/app.config", false, INI_SCANNER_RAW);
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

		return redirect("/celltype#joblist");
	}

	public function getFileList(Request $request){
		$jobID = $request->input('id');
		$filedir = config('app.jobdir').'/celltype/'.$jobID;
		$files = glob($filedir."/*.gcov.out");
		for($i=0; $i<count($files); $i++){
			$files[$i] = preg_replace("/.+magma_celltype_(.+).gcov.out/", "$1", $files[$i]);
		}
		return json_encode($files);
	}

	public function filedown(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$prefix = $request->input('files');
		$files = [];
		for($i=0; $i<count($prefix); $i++){
			$files[] = 'magma_celltype_'.$prefix[$i].'.gcov.out';
			$files[] = 'magma_celltype_'.$prefix[$i].'.log';
		}
		$zip = new \ZipArchive();
		$zipfile = $filedir."FUMA_celltype".$id.".zip";
		if(File::exists($zipfile)){
			File::delete($zipfile);
		}
		$zip -> open($zipfile, \ZipArchive::CREATE);
		// $zip->addFile(storage_path().'/README', "README");
		foreach($files as $f){
			$zip->addFile($filedir.$f, $f);
		}
		$zip -> close();
		return response() -> download($zipfile);
	}

	public function getPlotData(Request $request){
		$id = $request->input('id');
		$filedir = config('app.jobdir').'/celltype/'.$id.'/';
		$script = storage_path().'/scripts/celltypePlotData.py';
		$json = shell_exec("python $script $filedir");
		return $json;
	}
}
