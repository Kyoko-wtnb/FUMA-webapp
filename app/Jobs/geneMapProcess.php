<?php

namespace fuma\Jobs;

use fuma\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use Illuminate\Support\Facades\DB;
use File;

class geneMapProcess extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

	protected $user;
    protected $jobID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $jobID)
    {
		$this->user = $user;
		$this->jobID = $jobID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
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
		$status = 0;

		// get parameters
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);

		// log files
		$logfile = $filedir."job.log";
		$errorfile = $filedir."error.log";

		$script = "";

		if($params['eqtlMap']==1){
			file_put_contents($logfile, "\n----- geteQTL.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- geteQTL.py -----\n", FILE_APPEND);
			$script = scripts_path('geteQTL.py');
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				$this->chmod($filedir);
				DB::table('SubmitJobs') -> where('jobID', $jobID)
					-> update(['status'=>'ERROR:009']);
				$this->JobMonitorUpdate($jobID, $created_at, $started_at);
				if($email!=null){
					$this->sendJobCompMail($email, $jobtitle, $jobID, 9, $msg);
					return;
				}
			}
		}

		if($params['ciMap']==1){
			file_put_contents($logfile, "\n----- getCI.R -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- getCI.R -----\n", FILE_APPEND);
			$script = scripts_path('getCI.R');
			exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				$this->chmod($filedir);
				DB::table('SubmitJobs') -> where('jobID', $jobID)
					-> update(['status'=>'ERROR:010']);
				$this->JobMonitorUpdate($jobID, $created_at, $started_at);
				$errorout = file_get_contents($errorfile);
				$errorout = explode("\n", $errorout);
				$msg = $errorout[count($errorout)-2];
				if($email!=null){
					$this->sendJobCompMail($email, $jobtitle, $jobID, 10, $msg);
					return;
				}
			}
		}

		file_put_contents($logfile, "\n----- geneMap.R -----\n", FILE_APPEND);
		file_put_contents($errorfile, "\n----- geneMap.R -----\n", FILE_APPEND);
		$script = scripts_path('geneMap.R');
		exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$this->chmod($filedir);
			DB::table('SubmitJobs') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR:011']);
			$this->JobMonitorUpdate($jobID, $created_at, $started_at);
			if($email!=null){
				$this->sendJobCompMail($email, $jobtitle, $jobID, 11, $msg);
				return;
			}
		}

		if($params['ciMap']==1){
			file_put_contents($logfile, "\n----- createCircosPlot.py -----\n", FILE_APPEND);
			file_put_contents($errorfile, "\n----- createCircosPlot.py -----\n", FILE_APPEND);
			$script = scripts_path('createCircosPlot.py');
			exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
			if($error != 0){
				$this->chmod($filedir);
				DB::table('SubmitJobs') -> where('jobID', $jobID)
					-> update(['status'=>'ERROR:012']);
				$this->JobMonitorUpdate($jobID, $created_at, $started_at);
				$errorout = file_get_contents($errorfile);
				$errorout = explode("\n", $errorout);
				$msg = $errorout[count($errorout)-2];
				if($email!=null){
					$this->sendJobCompMail($email, $jobtitle, $jobID, 12, $msg);
					return;
				}
			}
		}

		DB::table('SubmitJobs') -> where('jobID', $jobID)
			-> update(['status'=>'OK']);
		$this->JobMonitorUpdate($jobID, $created_at, $started_at);

		if($email != null){
			$this->sendJobCompMail($email, $jobtitle, $jobID, $status, $msg);
		}
		$this->chmod($filedir);
		return;
    }

	public function failed($exception){
		$jobID = $this->jobID;
		$user = $this->user;
		$email = $user->email;
		$jobtitle = DB::table('SubmitJobs') -> where('jobID', $jobID)
			->first() ->title;
		DB::table('SubmitJobs') -> where('jobID', $jobID)
			-> update(['status'=>'JOB FAILED']);
		$this->sendJobFailedMail($email, $jobtitle, $jobID);
	}

	public function sendJobCompMail($email, $jobtitle, $jobID, $status, $msg){
		if($status==0 || $status==2){
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

	public function chmod($filedir){
		exec("find ".$filedir." -type d -exec chmod 775 {} \;");
		exec("find ".$filedir." -type f -exec chmod 664 {} \;");
	}
}
