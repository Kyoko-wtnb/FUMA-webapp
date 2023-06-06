<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class GeneMapProcess implements ShouldQueue
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
        // Update status when job is started
        $jobID = $this->jobID;
        $started_at = date("Y-m-d H:i:s");
        DB::table('SubmitJobs')->where('jobID', $jobID)
            ->update(['status' => 'RUNNING']);

        $user = $this->user;
        $email = $user->email;
        $jobtitle = DB::table('SubmitJobs')->where('jobID', $jobID)
            ->first()->title;
        $created_at = DB::table('SubmitJobs')->where('jobID', $jobID)
            ->first()->created_at;
        //error message
        $msg = "";

        //current error state
        $status = 0;

        // get parameters
        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';
        $ref_data_path_on_host = config('app.ref_data_on_host_path');
        $params = parse_ini_string(Storage::get($filedir . "params.config"), false, INI_SCANNER_RAW);

        // log files
        $logfile = $filedir . "job.log";
        $errorfile = $filedir . "error.log";

        if ($params['eqtlMap'] == 1) {
            Storage::append($logfile, "----- geteQTL.py -----\n");
            Storage::append($errorfile, "----- geteQTL.py -----\n");

            $uuid = Str::uuid();
            $new_cmd = "docker run --rm --name job-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-geteqtl /bin/sh -c 'python geteQTL.py job >>job/job.log 2>>job/error.log'";
            Storage::append($logfile, "Command to be executed:");
            Storage::append($logfile, $new_cmd . "\n");
            exec($new_cmd, $output, $error);

            if ($error != 0) {
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'ERROR:009']);
                // $this->JobMonitorUpdate($jobID, $created_at, $started_at);
                if ($email != null) {
                    $this->sendJobCompMail($email, $jobtitle, $jobID, 9, $msg);
                    return;
                }
            }
        }

        if ($params['ciMap'] == 1) {
            Storage::append($logfile, "----- getCI.R -----\n");
            Storage::append($errorfile, "----- getCI.R -----\n");

            $uuid = Str::uuid();
            $new_cmd = "docker run --rm --name job-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getci /bin/sh -c 'Rscript getCI.R job >>job/job.log 2>>job/error.log'";
            Storage::append($logfile, "Command to be executed:");
            Storage::append($logfile, $new_cmd . "\n");
            exec($new_cmd, $output, $error);

            if ($error != 0) {
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'ERROR:010']);
                // $this->JobMonitorUpdate($jobID, $created_at, $started_at);
                $errorout = Storage::get($errorfile);
                $errorout = explode("\n", $errorout);
                $msg = $errorout[count($errorout) - 2];
                if ($email != null) {
                    $this->sendJobCompMail($email, $jobtitle, $jobID, 10, $msg);
                    return;
                }
            }
        }

        Storage::append($logfile, "----- geneMap.R -----\n");
        Storage::append($errorfile, "----- geneMap.R -----\n");

        $uuid = Str::uuid();
        $new_cmd = "docker run --rm --name job-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-genemap /bin/sh -c 'Rscript geneMap.R job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);

        if ($error != 0) {
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->update(['status' => 'ERROR:011']);
            // $this->JobMonitorUpdate($jobID, $created_at, $started_at);
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, 11, $msg);
                return;
            }
        }

        if ($params['ciMap'] == 1) {
            Storage::append($logfile, "----- createCircosPlot.py -----\n");
            Storage::append($errorfile, "----- createCircosPlot.py -----\n");

            $uuid = Str::uuid();
            $new_cmd = "docker run --rm --name job-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-create_circos_plot /bin/sh -c 'python createCircosPlot.py job >>job/job.log 2>>job/error.log'";
            Storage::append($logfile, "Command to be executed:");
            Storage::append($logfile, $new_cmd . "\n");
            exec($new_cmd, $output, $error);

            if ($error != 0) {
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'ERROR:012']);
                // $this->JobMonitorUpdate($jobID, $created_at, $started_at);
                $errorout = Storage::get($errorfile);
                $errorout = explode("\n", $errorout);
                $msg = $errorout[count($errorout) - 2];
                if ($email != null) {
                    $this->sendJobCompMail($email, $jobtitle, $jobID, 12, $msg);
                    return;
                }
            }
        }

        DB::table('SubmitJobs')->where('jobID', $jobID)
            ->update(['status' => 'OK']);
        // $this->JobMonitorUpdate($jobID, $created_at, $started_at);

        if ($email != null) {
            $this->sendJobCompMail($email, $jobtitle, $jobID, $status, $msg);
        }
        return;
    }

    public function failed()
    {
        $jobID = $this->jobID;
        $user = $this->user;
        $email = $user->email;
        $jobtitle = DB::table('SubmitJobs')->where('jobID', $jobID)
            ->first()->title;
        DB::table('SubmitJobs')->where('jobID', $jobID)
            ->update(['status' => 'JOB FAILED']);
        $this->sendJobFailedMail($email, $jobtitle, $jobID);
    }

    public function sendJobCompMail($email, $jobtitle, $jobID, $status, $msg)
    {
        if (App::isProduction()) {
            if ($status == 0 || $status == 2) {
                $user = DB::table('users')->where('email', $email)->first();
                $data = [
                    'jobID' => $jobID,
                    'jobtitle' => $jobtitle,
                    'status' => $status,
                    'msg' => $msg
                ];
                try {
                    Mail::send('emails.jobComplete', $data, function ($m) use ($user) {
                        $m->from('noreply@ctglab.nl', "FUMA web application");
                        $m->to($user->email, $user->name)->subject("FUMA your job has been completed");
                    });
                } catch (Throwable $e) {
                }
            } else {
                $user = DB::table('users')->where('email', $email)->first();
                $data = [
                    'status' => $status,
                    'jobtitle' => $jobtitle,
                    'jobID' => $jobID,
                    'msg' => $msg
                ];
                try {
                    Mail::send('emails.jobError', $data, function ($m) use ($user) {
                        $m->from('noreply@ctglab.nl', "FUMA web application");
                        $m->to($user->email, $user->name)->subject("FUMA an error occured");
                    });
                } catch (Throwable $e) {
                }
            }
        }
        return;
    }

    public function sendJobFailedMail($email, $jobtitle, $jobID)
    {
        if (App::isProduction()) {
            $user = $this->user;
            $data = [
                'jobtitle' => $jobtitle,
                'jobID' => $jobID
            ];
            $devemail = config('app.devemail');
            try {
                Mail::send('emails.jobFailed', $data, function ($m) use ($user, $devemail) {
                    $m->from('noreply@ctglab.nl', "FUMA web application");
                    $m->to($user->email, $user->name)->cc($devemail)->subject("FUMA job failed");
                });
            } catch (Throwable $e) {
            }
        }
        return;
    }

    public function JobMonitorUpdate($jobID, $created_at, $started_at)
    {
        $completed_at = date("Y-m-d H:i:s");
        DB::table("JobMonitor")->insert([
            "jobID" => $jobID,
            "created_at" => $created_at,
            "started_at" => $started_at,
            "completed_at" => $completed_at
        ]);
        return;
    }
}
