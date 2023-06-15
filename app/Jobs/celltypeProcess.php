<?php

namespace fuma\Jobs;

use fuma\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use Illuminate\Support\Facades\DB;
use File;

class celltypeProcess extends Job implements ShouldQueue
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
    public function handle(){
		// Update status
		$email = $this->user->email;
		$jobID = $this->jobID;
		$started_at = date("Y-m-d H:i:s");
		DB::table('celltype') -> where('jobID', $jobID)
			->update(['status'=>'RUNNING']);
		$title = DB::table('celltype') -> where('jobID', $jobID)
			->first()->title;

		$filedir = config('app.jobdir').'/celltype/'.$jobID.'/';
		$logfile = $filedir."job.log";
		$errorfile = $filedir."error.log";

		$script = scripts_path('magma_celltype.R');
		exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
		if($error != 0){
			$this->chmod($filedir);
			DB::table('celltype') -> where('jobID', $jobID)
				-> update(['status'=>'ERROR']);
			if($email!=null){
				$this->sendJobCompMail($email, $title, $jobID, 'error');
				return;
			}
		}else{
			$this->chmod($filedir);
			DB::table('celltype') -> where('jobID', $jobID)
				-> update(['status'=>'OK']);
			if($email!=null){
				$this->sendJobCompMail($email, $title, $jobID, 'OK');
				return;
			}
		}
    }

	public function sendJobCompMail($email, $title, $jobID, $status){
		$user = $this->user;
		if($status=="error"){
			$data = [
				'jobtitle'=>$title,
				'jobID'=>$jobID,
			];
			Mail::send('emails.cellJobError', $data, function($m) use($user){
				$m->from('noreply@ctglab.nl', "FUMA web application");
				$m->to($user->email, $user->name)->subject("FUMA an error occured");
			});
		}else{
			$data = [
				'jobtitle'=>$title,
				'jobID'=>$jobID,
			];
			Mail::send('emails.cellJobComplete', $data, function($m) use($user){
				$m->from('noreply@ctglab.nl', "FUMA web application");
				$m->to($user->email, $user->name)->subject("FUMA your job has been completed");
			});
		}
	}

	public function chmod($filedir){
		exec("find ".$filedir." -type d -exec chmod 775 {} \;");
		exec("find ".$filedir." -type f -exec chmod 664 {} \;");
	}
}
