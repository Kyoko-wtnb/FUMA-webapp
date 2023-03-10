<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// in the old one the CelltypeProcess class extends Job. Check what is the different
class CelltypeProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    // use InteractsWithQueue, SerializesModels; // this was the old one. Check if the can be used interchangeably

    protected $user;
    protected $jobID;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $jobID)
    {
        $this->user = $user;
        $this->jobID = $jobID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Update status
        $email = $this->user->email;
        $jobID = $this->jobID;
        $started_at = date("Y-m-d H:i:s");
        DB::table('celltype')->where('jobID', $jobID)
            ->update(['status' => 'RUNNING']);
        $title = DB::table('celltype')->where('jobID', $jobID)
            ->first()->title;

        $filedir = config('app.jobdir') . '/celltype/' . $jobID . '/';
        $logfile = $filedir . "job.log";
        $errorfile = $filedir . "error.log";

        $script = scripts_path('magma_celltype.R');
        exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
        if ($error != 0) {
            $this->chmod($filedir);
            DB::table('celltype')->where('jobID', $jobID)
                ->update(['status' => 'ERROR']);
            if ($email != null) {
                $this->sendJobCompMail($email, $title, $jobID, 'error');
                return;
            }
        } else {
            $this->chmod($filedir);
            DB::table('celltype')->where('jobID', $jobID)
                ->update(['status' => 'OK']);
            if ($email != null) {
                $this->sendJobCompMail($email, $title, $jobID, 'OK');
                return;
            }
        }
    }

    public function sendJobCompMail($email, $title, $jobID, $status)
    {
        $user = $this->user;
        if ($status == "error") {
            $data = [
                'jobtitle' => $title,
                'jobID' => $jobID,
            ];
            Mail::send('emails.cellJobError', $data, function ($m) use ($user) {
                $m->from('noreply@ctglab.nl', "FUMA web application");
                $m->to($user->email, $user->name)->subject("FUMA an error occured");
            });
        } else {
            $data = [
                'jobtitle' => $title,
                'jobID' => $jobID,
            ];
            Mail::send('emails.cellJobComplete', $data, function ($m) use ($user) {
                $m->from('noreply@ctglab.nl', "FUMA web application");
                $m->to($user->email, $user->name)->subject("FUMA your job has been completed");
            });
        }
    }

    public function chmod($filedir)
    {
        exec("find " . $filedir . " -type d -exec chmod 775 {} \;");
        exec("find " . $filedir . " -type f -exec chmod 664 {} \;");
    }
}
