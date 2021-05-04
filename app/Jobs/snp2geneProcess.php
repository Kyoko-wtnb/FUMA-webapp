<?php

namespace fuma\Jobs;

use fuma\Jobs\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use Illuminate\Support\Facades\DB;
use File;

class s2gProcessError
{
	const FILE_CHECK = -1;
	const OK = 0;
	const GWAS_FILE = 1;
	const MAGMA = 2;
	const MANHATTAN = 3;
	const QQSNPS = 4;
	const TOPSNPSNC = 5;
	const TOPSNPS = 6;
	const SNPANNOT = 7;
	const GWASCATALOG = 8;
	const EQTL = 9;
	const CI = 10;
	const GENEMAP = 11;
	const CIMAP = 12;
}

class snp2geneProcess extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;
    protected $jobID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $jobID, $timeout=null)
    {
        $this->user = $user;
		$this->jobID = $jobID;
		$this->timeout = $timeout;
	}
	
    /**
     * Execute the job.
     *
     * @return void
     */
	public function handle(){
		$this->setStartTime();
		// Update status when job is started
		$jobID = $this->jobID;
		$started_at = date("Y-m-d H:i:s");
		DB::table('SubmitJobs') -> where('jobID', $jobID)
			-> update(['status'=>'RUNNING']);

		$user = $this->user;
		$email = $user->email;
		$jobtitle = DB::table('SubmitJobs') -> where('jobID', $jobID)
			->first() ->title;
		$created_at = DB::table('SubmitJobs') -> where('jobID', $jobID)
			->first() ->created_at;

		//error message
		$msg = "";

		//current error state
		$status = s2gProcessError::OK;

		// file check
		if(!file_exists(config('app.jobdir').'/jobs/'.$jobID.'/input.gwas')){
			DB::table('SubmitJobs') -> where('jobID', $jobID)
				->delete();
			File::deleteDirectory(config('app.jobdir').'/jobs/'.$jobID);
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::FILE_CHECK, $msg);
			return;
			}
		}

		// get parameters
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);

		// log files
		$logfile = $filedir."job.log";
		$errorfile = $filedir."error.log";

		//gwas_file.pl
		file_put_contents($logfile, "----- gwas_file.py -----\n");
		file_put_contents($errorfile, "----- gwas_file.py -----\n");
		$script = storage_path().'/scripts/gwas_file.py';
		exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$this->rmFiles($filedir);
			$this->chmod($filedir);
			DB::table('SubmitJobs') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR:001']);

			$errorout = file_get_contents($errorfile);
			$errorout = explode("\n", $errorout);
			$msg = $errorout[count($errorout)-2];
			$this->JobMonitorUpdate($jobID, $created_at, $started_at);
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::GWAS_FILE, $msg);
				return;
			}
		}

		$script = storage_path().'/scripts/allSNPs.py';
		exec("python $script $filedir");

		if($params['magma']==1){
			file_put_contents($logfile, "\n----- magma.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- magma.py -----\n", FILE_APPEND);
			$script = storage_path().'/scripts/magma.py';
			$this->timedExec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				if(!is_null($this->faulted_command)) {
					$this->rmFiles($filedir);
					$this->chmod($filedir);
					DB::table('SubmitJobs') -> where('jobID', $jobID)
						-> update(['status'=>'ERROR:002']);
					$this->JobMonitorUpdate($jobID, $created_at, $started_at);
					if($email!=null){
						$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::MAGMA, $msg);
						return;
					}
				}
				$errorout = file_get_contents($errorfile);
				if(preg_match('/MAGMA ERROR/', $errorout)==1){
					$errorout = file_get_contents($logfile);
					$errorout = explode("\n", $errorout);
					foreach($errorout as $l){
						if(preg_match("/ERROR - /", $l)==1){
							$msg = $l;
							break;
						}
					}
				}else{
					$msg = "server error";
				}
				$status = s2gProcessError::MAGMA;
			}
		}

		file_put_contents($logfile, "\n----- manhattan_filt.py -----\n", FILE_APPEND);
		file_put_contents($errorfile, "\n----- manhattan_filt.py -----\n", FILE_APPEND);
		$script = storage_path().'/scripts/manhattan_filt.py';
		exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$this->rmFiles($filedir);
			$this->chmod($filedir);
			DB::table('SubmitJobs') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR:003']);
			$this->JobMonitorUpdate($jobID, $created_at, $started_at);
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::MANHATTAN, $msg);
				return;
			}
		}

		file_put_contents($logfile, "\n----- QQSNPs_filt.py -----\n", FILE_APPEND);
		file_put_contents($errorfile, "\n----- QQSNPs_filt.py -----\n", FILE_APPEND);
		$script = storage_path().'/scripts/QQSNPs_filt.py';
		exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$this->rmFiles($filedir);
			$this->chmod($filedir);
			DB::table('SubmitJobs') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR:004']);
			$this->JobMonitorUpdate($jobID, $created_at, $started_at);
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::QQSNPS, $msg);
				return;
			}
		}

		file_put_contents($logfile, "\n----- getLD.py -----\n", FILE_APPEND);
		file_put_contents($errorfile, "\n----- getLD.py -----\n", FILE_APPEND);
		$script = storage_path().'/scripts/getLD.py';
		// $process = new Proces("/usr/bin/perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC");
		// $process -> start();
		// echo "perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC";
		exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$NoCandidates = false;
			$errorout = file_get_contents($errorfile);
			$errorout = explode("\n", $errorout);
			foreach($errorout as $l){
				if(preg_match('/No candidate SNP was identified/', $l)==1){
					$NoCandidates = true;
					break;
				}
			}
			if($NoCandidates){
				$script = storage_path().'/scripts/getTopSNPs.py';
				exec("python $script $filedir >>$logfile 2>>$errorfile");
				$this->rmFiles($filedir);
				$this->chmod($filedir);
				DB::table('SubmitJobs') -> where('jobID', $jobID)
					-> update(['status'=>'ERROR:005']);
				$this->JobMonitorUpdate($jobID, $created_at, $started_at);
				if($email!=null){
					$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::TOPSNPSNC, $msg);
				}
				return;
			}else{
				$this->rmFiles($filedir);
				$this->chmod($filedir);
				DB::table('SubmitJobs') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR:006']);
				$this->JobMonitorUpdate($jobID, $created_at, $started_at);
				if($email!=null){
					$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::TOPSNPS, $msg);
					return;
				}
			}
		}

		file_put_contents($logfile, "\n----- SNPannot.R -----\n", FILE_APPEND);
		file_put_contents($errorfile, "\n----- SNPannot.R -----\n", FILE_APPEND);
		$script = storage_path().'/scripts/SNPannot.R';
		exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$this->rmFiles($filedir);
			$this->chmod($filedir);
			DB::table('SubmitJobs') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR:007']);
			$this->JobMonitorUpdate($jobID, $created_at, $started_at);
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::SNPANNOT, $msg);
				return;
			}
		}

		file_put_contents($logfile, "\n----- getGWAScatalog.py -----\n", FILE_APPEND);
		file_put_contents($errorfile, "\n----- getGWAScatalog.py -----\n", FILE_APPEND);
		$script = storage_path().'/scripts/getGWAScatalog.py';
		exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$this->rmFiles($filedir);
			$this->chmod($filedir);
			DB::table('SubmitJobs') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR:008']);
			$this->JobMonitorUpdate($jobID, $created_at, $started_at);
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::GWASCATALOG, $msg);
				return;
			}
		}

		#$script = storage_path().'/scripts/getExAC.pl';
		#exec("perl $script $filedir");
		if($params['eqtlMap']==1){
			file_put_contents($logfile, "\n----- geteQTL.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- geteQTL.py -----\n", FILE_APPEND);
			$script = storage_path().'/scripts/geteQTL.py';
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				$this->rmFiles($filedir);
				$this->chmod($filedir);
				DB::table('SubmitJobs') -> where('jobID', $jobID)
					-> update(['status'=>'ERROR:009']);
				$this->JobMonitorUpdate($jobID, $created_at, $started_at);
				if($email!=null){
					$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::EQTL, $msg);
					return;
				}
			}
		}

		if($params['ciMap']==1){
			file_put_contents($logfile, "\n----- getCI.R -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- getCI.R -----\n", FILE_APPEND);
			$script = storage_path().'/scripts/getCI.R';
			exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				$this->rmFiles($filedir);
				$this->chmod($filedir);
				DB::table('SubmitJobs') -> where('jobID', $jobID)
					-> update(['status'=>'ERROR:010']);
				$this->JobMonitorUpdate($jobID, $created_at, $started_at);
				$errorout = file_get_contents($errorfile);
				$errorout = explode("\n", $errorout);
				$msg = $errorout[count($errorout)-2];
				if($email!=null){
					$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::CI, $msg);
					return;
				}
			}
		}

		file_put_contents($logfile, "\n----- geneMap.R -----\n", FILE_APPEND);
		file_put_contents($errorfile, "\n----- geneMap.R -----\n", FILE_APPEND);
		$script = storage_path().'/scripts/geneMap.R';
		exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$this->rmFiles($filedir);
			$this->chmod($filedir);
			DB::table('SubmitJobs') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR:011']);
			$this->JobMonitorUpdate($jobID, $created_at, $started_at);
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::GENEMAP, $msg);
				return;
			}
		}

		if($params['ciMap']==1){
			file_put_contents($logfile, "\n----- createCircosPlot.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- createCircosPlot.py -----\n", FILE_APPEND);
			$script = storage_path().'/scripts/createCircosPlot.py';
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				$this->rmFiles($filedir);
				$this->chmod($filedir);
				DB::table('SubmitJobs') -> where('jobID', $jobID)
					-> update(['status'=>'ERROR:012']);
				$this->JobMonitorUpdate($jobID, $created_at, $started_at);
				$errorout = file_get_contents($errorfile);
				$errorout = explode("\n", $errorout);
				$msg = $errorout[count($errorout)-2];
				if($email!=null){
					$this->sendJobCompMail($email, $jobtitle, $jobID, s2gProcessError::CIMAP, $msg);
					return;
				}
			}
		}

		$this->rmFiles($filedir);
		$this->chmod($filedir);

		DB::table('SubmitJobs') -> where('jobID', $jobID)
			-> update(['status'=>'OK']);
		$this->JobMonitorUpdate($jobID, $created_at, $started_at);

		if($email != null){
			$this->sendJobCompMail($email, $jobtitle, $jobID, $status, $msg);
		}
		return;
	}

	public function failed(){
		$jobID = $this->jobID;
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$logfile = $filedir."job.log";
		$errorfile = $filedir."error.log";
		Log::error(" Job failed: ".$jobID);

		if (!is_null($this->faulted_command)){
			file_put_contents($errorfile, "Failed due to timeout while running: ".$this->faulted_command, FILE_APPEND);
		}
		file_put_contents($errorfile, "\n----- Mail fail error-----\n", FILE_APPEND);
		
		$user = $this->user;
        $email = $user->email;
		$jobtitle = DB::table('SubmitJobs') -> where('jobID', $jobID)
            ->first() ->title;
        DB::table('SubmitJobs') -> where('jobID', $jobID)
			-> update(['status'=>'JOB FAILED']);
		if (is_null($this->faulted_command)){
			$this->sendJobFailedMail($email, $jobtitle, $jobID);
		} else {
			$this->sendJobTimedoutMail($email, $jobtitle, $jobID);
		}
	}

	public function sendJobCompMail($email, $jobtitle, $jobID, $status, $msg){
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$logfile = $filedir."job.log";
		file_put_contents($logfile, "\n----- Mail completion error-----\n".$status."\n".$msg, FILE_APPEND);

		if($this->timedout) {
			$this->sendJobTimedoutMail($email, $jobtitle, $jobID);	
		}
		elseif($status==s2gProcessError::OK || $status==s2gProcessError::MAGMA){
			$user = DB::table('users')->where('email', $email)->first();
			$data = [
				'jobID'=>$jobID,
				'jobtitle'=>$jobtitle,
				'status'=>$status,
				'msg'=>$msg
			];
			Mail::send('emails.jobComplete', $data, function($m) use($user){
				$m->from('noreply@ctglab.nl', "FUMA web application");
				$m->to($user->email, $user->name)->subject("FUMA your job has been completed");
			});
		}else{
			$user = DB::table('users')->where('email', $email)->first();
			$data = [
				'status'=>$status,
				'jobtitle'=>$jobtitle,
				'jobID'=>$jobID,
				'msg'=>$msg
			];
			Mail::send('emails.jobError', $data, function($m) use($user){
				$m->from('noreply@ctglab.nl', "FUMA web application");
				$m->to($user->email, $user->name)->subject("FUMA an error occured");
			});
		}
		return;
	}

	public function sendJobFailedMail($email, $jobtitle, $jobID){
		$user = $this->user;
		$data = [
			'jobtitle'=>$jobtitle,
			'jobID'=>$jobID
		];
		$devemail = config('app.devemail');
		Mail::send('emails.jobFailed', $data, function($m) use($user, $devemail){
			$m->from('noreply@ctglab.nl', "FUMA web application");
			$m->to($user->email, $user->name)->cc($devemail)->subject("FUMA job failed");
		});
	}
	
	public function sendJobTimedoutMail($email, $jobtitle, $jobID){
		$user = $this->user;
		$data = [
			'jobtitle'=>$jobtitle,
			'jobID'=>$jobID,
			'timeout'=>$this->timeout
		];
		$devemail = config('app.devemail');
		Mail::send('emails.jobTimeout', $data, function($m) use($user, $devemail){
			$m->from('noreply@ctglab.nl', "FUMA web application");
			$m->to($user->email, $user->name)->cc($devemail)->subject("FUMA job timed out");
		});
	}

	public function JobMonitorUpdate($jobID, $created_at, $started_at){
		$completed_at = date("Y-m-d H:i:s");
		DB::table("JobMonitor")->insert([
			"jobID"=>$jobID,
			"created_at"=>$created_at,
			"started_at"=>$started_at,
			"completed_at"=>$completed_at
		]);
		return;
	}

	public function rmFiles($filedir){
		exec("rm $filedir"."input.gwas");
		exec("rm $filedir"."input.snps");
		if(File::exists($filedir."input.lead")){
			exec("rm $filedir"."input.lead");
		}
		if(File::exists($filedir."input.regions")){
			exec("rm $filedir"."input.regions");
		}
		if(File::exists($filedir."magma.input")){
			exec("rm $filedir"."magma.input");
		}
		return;
	}

	public function chmod($filedir){
		exec("find ".$filedir." -type d -exec chmod 775 {} \;");
		exec("find ".$filedir." -type f -exec chmod 664 {} \;");
	}
}
