<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        DB::table('celltype')->where('jobID', $jobID)
            ->update(['status' => 'RUNNING']);
        $title = DB::table('celltype')->where('jobID', $jobID)
            ->first()->title;

        $filedir = config('app.jobdir') . '/celltype/' . $jobID . '/';
        $ref_data_path_on_host = config('app.ref_data_on_host_path');
        $logfile = $filedir . "job.log";
        $errorfile = $filedir . "error.log";

        // $script = scripts_path('magma_celltype.R');
        Storage::put($logfile, "----- magma_celltype.R -----\n");
        Storage::put($errorfile, "----- magma_celltype.R -----\n");
        
        $uuid = Str::uuid();
        $new_cmd = "docker run --rm --name job-cell-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_cell_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-magma_celltype /bin/sh -c 'Rscript magma_celltype.R job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");

        exec($new_cmd, $output, $error);


        if ($error != 0) {
            // $this->chmod($filedir);
            DB::table('celltype')->where('jobID', $jobID)
                ->update(['status' => 'ERROR']);
            if ($email != null) {
                $this->sendJobCompMail($email, $title, $jobID, 'error');
                return;
            }
        } else {
            // $this->chmod($filedir);
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
        // if ($status == "error") {
        //     $data = [
        //         'jobtitle' => $title,
        //         'jobID' => $jobID,
        //     ];
        //     Mail::send('emails.cellJobError', $data, function ($m) use ($user) {
        //         $m->from('noreply@ctglab.nl', "FUMA web application");
        //         $m->to($user->email, $user->name)->subject("FUMA an error occured");
        //     });
        // } else {
        //     $data = [
        //         'jobtitle' => $title,
        //         'jobID' => $jobID,
        //     ];
        //     Mail::send('emails.cellJobComplete', $data, function ($m) use ($user) {
        //         $m->from('noreply@ctglab.nl', "FUMA web application");
        //         $m->to($user->email, $user->name)->subject("FUMA your job has been completed");
        //     });
        // }
    }

    public function chmod($filedir)
    {
        exec("find " . $filedir . " -type d -exec chmod 775 {} \;");
        exec("find " . $filedir . " -type f -exec chmod 664 {} \;");
    }
}
