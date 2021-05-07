<?php

namespace fuma\Jobs;

use fuma\Jobs\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use fuma\JobMonitor;
use fuma\SubmitJob;
use fuma\User;
use Mail;
use Illuminate\Support\Facades\DB;
use File;
use fuma\Jobs\FumaJobException;
use fuma\Jobs\FumaErrorInfo;

/**
 * Support class for S2G process state enum and error strings
 */
class s2gError implements FumaErrorInfo
{
	protected $code;

	public function __construct($code) {
		$this->code = $code;
	}	

	/**
	 * A series of const ints representing the pocess state
	 * when an error happens
	 */
	const SERVER_ERROR = -2;
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

	/**
	 * An associative array mapping the error ints to 
	 * status strings
	 * 
	 * @var array
	 */
	public static $err2string = array(
		s2gError::SERVER_ERROR => "server error",
		s2gError::FILE_CHECK => "server error",
		s2gError::OK => "OK",
		s2gError::GWAS_FILE => "ERROR:001",
		s2gError::MAGMA => "ERROR:002",
		s2gError::MANHATTAN => "ERROR:003",
		s2gError::QQSNPS => "ERROR:004",
		s2gError::TOPSNPSNC => "ERROR:005",
		s2gError::TOPSNPS => "ERROR:006",
		s2gError::SNPANNOT => "ERROR:007",
		s2gError::GWASCATALOG => "ERROR:008",
		s2gError::EQTL => "ERROR:009",
		s2gError::CI => "ERROR:010",
		s2gError::GENEMAP => "ERROR:011",
		s2gError::CIMAP => "ERROR:012",
	);

	/**
	 * Return the error code
	 * 
	 * @return int
	 * 
	 */
	public function getCode() {
		return $code;
	}

	/**
	 * Return the error string associated with the code
	 * 
	 * @return string
	 *
	 */
	public function getStatus() {
		return s2gError::$err2string[$this->code];
	}

}


class snp2geneProcess extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;
	protected $jobID;
	protected $state;
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
		$this->setStartTime();
		$this->state = s2gError::OK;
	}
	
    /**
     * Execute the job.
     *
     * @return void
     */
	public function handle(){
		// Update status when job is started
		$jobID = $this->jobID;
		$started_at = date("Y-m-d H:i:s");
		SubmitJob::where('jobID', $jobID)
			-> update(['status'=>'RUNNING']);

		$user = $this->user;
		$email = $user->email;
		$job = SubmitJob::find($jobID);
		$jobtitle = $job->title;
		$created_at = $job->created_at;

		$this->state = s2gError::FILE_CHECK;
		// file check
		if(!file_exists(config('app.jobdir').'/jobs/'.$jobID.'/input.gwas')){
			$job->delete();
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

		try {
			$this->state = s2gError::GWAS_FILE;
			//gwas_file.pl
			file_put_contents($logfile, "----- gwas_file.py -----\n");
			file_put_contents($errorfile, "----- gwas_file.py -----\n");
			$script = storage_path().'/scripts/gwas_file.py';
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				$errorout = file_get_contents($errorfile);
				$errorout = explode("\n", $errorout);
				$msg = $errorout[count($errorout)-2];
				throw new FumaJobException(new s2gError(s2gError::GWAS_FILE), $msg);
			}


			$script = storage_path().'/scripts/allSNPs.py';
			exec("python $script $filedir");

			if($params['magma']==1){
				$this->state = s2gError::MAGMA;
				file_put_contents($logfile, "\n----- magma.py -----\n", FILE_APPEND);
				file_put_contents($errorfile, "\n----- magma.py -----\n", FILE_APPEND);
				$script = storage_path().'/scripts/magma.py';
				exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
				if($error != 0){
					Log::error(sprintf("Magma error return: %d", $error));
					$msg = 'server error';
					// check for MMagma specific errors
					$errorout = file_get_contents($errorfile);
					if(preg_match('/MAGMA ERROR/', $errorout)==1){
						$errorout = file_get_contents($logfile);
						$errorout = explode("\n", $errorout);
						$msg = null;
						foreach($errorout as $l) {
							if(preg_match("/ERROR - /", $l)==1){
								$msg = $l;
								break;
							}
						}
					}
					// TODO why is the error not thrown here?
					//throw new FumaJobException(new s2gError(s2gError::MAGMA), $msg);
				}
			}

			file_put_contents($logfile, "\n----- manhattan_filt.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- manhattan_filt.py -----\n", FILE_APPEND);
			$script = storage_path().'/scripts/manhattan_filt.py';
			$this->state = s2gError::MANHATTAN;
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				Log::error(sprintf("Manhattan error return: %d", $error));
				throw new FumaJobException(new s2gError(s2gError::MANHATTAN));
			}

			file_put_contents($logfile, "\n----- QQSNPs_filt.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- QQSNPs_filt.py -----\n", FILE_APPEND);
			$this->state = s2gError::QQSNPS;
			$script = storage_path().'/scripts/QQSNPs_filt.py';
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				Log::error(sprintf("QQSNPs error return: %d", $error));
				throw new FumaJobException(new s2gError(s2gError::QQSNPS));
			}

			file_put_contents($logfile, "\n----- getLD.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- getLD.py -----\n", FILE_APPEND);
			$this->state = s2gError::TOPSNPS;
			$script = storage_path().'/scripts/getLD.py';
			// $process = new Proces("/usr/bin/perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC");
			// $process -> start();
			// echo "perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC";
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				Log::error(sprintf("LD error return: %d", $error));
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
					throw new FumaJobException(new s2gError(s2gError::TOPSNPSNC));
				}else{
					throw new FumaJobException(new s2gError(s2gError::TOPSNPS));
				}
			}

			file_put_contents($logfile, "\n----- SNPannot.R -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- SNPannot.R -----\n", FILE_APPEND);
			$this->state = s2gError::SNPANNOT;
			$script = storage_path().'/scripts/SNPannot.R';
			exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				throw new FumaJobException(new s2gError(s2gError::SNPANNOT));
			}

			file_put_contents($logfile, "\n----- getGWAScatalog.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- getGWAScatalog.py -----\n", FILE_APPEND);
			$this->state = s2gError::GWASCATALOG;
			$script = storage_path().'/scripts/getGWAScatalog.py';
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				throw new FumaJobException(new s2gError(s2gError::GWASCATALOG));
			}

			#$script = storage_path().'/scripts/getExAC.pl';
			#exec("perl $script $filedir");
			if($params['eqtlMap']==1){
				file_put_contents($logfile, "\n----- geteQTL.py -----\n", FILE_APPEND);
				file_put_contents($errorfile, "\n----- geteQTL.py -----\n", FILE_APPEND);
				$this->state = s2gError::EQTL;
				$script = storage_path().'/scripts/geteQTL.py';
				exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
				if($error != 0){
					throw new FumaJobException(new s2gError(s2gError::EQTL));
				}
			}

			if($params['ciMap']==1){
				file_put_contents($logfile, "\n----- getCI.R -----\n", FILE_APPEND);
				file_put_contents($errorfile, "\n----- getCI.R -----\n", FILE_APPEND);
				$this->state = s2gError::CI;
				$script = storage_path().'/scripts/getCI.R';
				exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
				if($error != 0){
					throw new FumaJobException(new s2gError(s2gError::CI));
				}
			}

			file_put_contents($logfile, "\n----- geneMap.R -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- geneMap.R -----\n", FILE_APPEND);
			$this->state = s2gError::GENEMAP;
			$script = storage_path().'/scripts/geneMap.R';
			exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				throw new FumaJobException(new s2gError(s2gError::GENEMAP));
			}

			if($params['ciMap']==1){
				file_put_contents($logfile, "\n----- createCircosPlot.py -----\n", FILE_APPEND);
				file_put_contents($errorfile, "\n----- createCircosPlot.py -----\n", FILE_APPEND);
				$this->state = s2gError::CIMAP;
				$script = storage_path().'/scripts/createCircosPlot.py';
				exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
				if($error != 0){
					$errorout = file_get_contents($errorfile);
					$errorout = explode("\n", $errorout);
					$msg = $errorout[count($errorout)-2];
					throw new FumaJobException(new s2gError(s2gError::CIMAP), $msg);
				}
			}
		} catch (FumaJobException $e) {
			// Cleanup user files
			$this->rmFiles($filedir);
			$this->chmod($filedir);

			$job->status = $e->status;
			$job->save();

			$this->JobMonitorUpdate($jobID, $created_at, $started_at);
			// TODO Elucidate. If there is no $email we don't return ????
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, $e->code, $e->getMessage());
				return;
			}		
		}

		$this->rmFiles($filedir);
		$this->chmod($filedir);

		$job->status = 'OK';
		$job->save();

		$this->JobMonitorUpdate($jobID, $created_at, $started_at);

		if($email != null){
			$this->sendJobCompMail($email, $jobtitle, $jobID, $status, $msg);
		}
		return;
	}

	/**
     * Handle a job failure.
     *
     * @param  Exception  $exception
     * @return void
     */
	public function failed($exception){
		parent::failed($exception);
		$jobID = $this->jobID;
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$logfile = $filedir."job.log";
		$errorfile = $filedir."error.log";
		Log::error(" Job failed: ".$jobID);

/* 		if (!is_null($this->faulted_command)){
			file_put_contents($errorfile, "Failed due to timeout while running: ".$this->faulted_command, FILE_APPEND);
		} */
		file_put_contents($errorfile, "\n----- Mail fail error-----\n", FILE_APPEND);
		
		$user = $this->user;
		$email = $user->email;
		$job = SubmitJob::find($jobID);
		$job->status = 'JOB FAILED';
		$job->save();
		$error = new s2gError($this->state);
		Log::error("Failed with message: ".$exception->getMessage());
		$this->sendJobCompMail($email, $job->title, $jobID, $this->state, $exception->getMessage());
	}

	public function sendJobCompMail($email, $jobtitle, $jobID, $status, $msg){
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$logfile = $filedir."job.log";
		file_put_contents($logfile, "\n----- Mail completion error-----\n".$status."\n".$msg, FILE_APPEND);

		if ($this->timedout) {
			$this->sendJobTimedoutMail($email, $jobtitle, $jobID);	
		} elseif ($this->failed) {
			$this->sendJobFailedMail($email, $jobtitle, $jobID);	
		} elseif ($status==s2gError::OK || $status==s2gError::MAGMA) {
			$user = $this->user;
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
		} else {
			$user = $this->user;
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
			'timeout'=>$this->timeout,
			'elapsed'=>$this->elapsed
		];
		$devemail = config('app.devemail');
		Mail::send('emails.jobTimeout', $data, function($m) use($user, $devemail){
			$m->from('noreply@ctglab.nl', "FUMA web application");
			$m->to($user->email, $user->name)->cc($devemail)->subject("FUMA job timed out");
		});
	}

	public function JobMonitorUpdate($jobID, $created_at, $started_at){
		$completed_at = date("Y-m-d H:i:s");
		$monitor = new JobMonitor;
		$monitor->jobID = $jobID;
		$monitor->created_at = $created_at;
		$monitor->started_at = $started_at;
		$monitor->completed_at = date("Y-m-d H:i:s");
		$monitor->save();
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
