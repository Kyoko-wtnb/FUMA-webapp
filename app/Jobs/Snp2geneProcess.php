<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Mail;
use Helper;


class Snp2geneProcess implements ShouldQueue
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

        // file check
        if (!Storage::exists(config('app.jobdir') . '/jobs/' . $jobID . '/input.gwas')) {
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->delete();
            Storage::deleteDirectory(config('app.jobdir') . '/jobs/' . $jobID);
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, -1, $msg);
                return;
            }
            return;
            // There should also be a return here, in case email == null stil it should return
        }

        // get parameters
        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';
        $ref_data_path_on_host = config('app.ref_data_on_host_path');

        if (!Storage::exists($filedir . "params.config")) {
            $msg = "Job parameters not found.";
            $this->rmFiles($filedir);
            // $this->chmod($filedir);
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->update(['status' => 'ERROR:100']);
            $this->JobMonitorUpdate($jobID, $created_at, $started_at);
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, 100, $msg);
                return;
            }
            return;
        }

        $params = parse_ini_string(Storage::get($filedir . "params.config"), false, INI_SCANNER_RAW);

        // log files
        $logfile = $filedir . "job.log";
        $errorfile = $filedir . "error.log";

        //gwas_file.pl
        Storage::put($logfile, "----- gwas_file.py -----\n");
        Storage::put($errorfile, "----- gwas_file.py -----\n");

        $new_cmd = "docker run --rm --name job-$jobID -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-gwas_file /bin/sh -c 'python gwas_file.py job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");

        exec($new_cmd, $output, $error);

        if ($error != 0) {
            $this->rmFiles($filedir);
            // $this->chmod($filedir);
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->update(['status' => 'ERROR:001']);

            $msg = "No error log found for SNP2GENE job ID: $jobID";
            if (Storage::exists($errorfile)) {
                $errorout = Storage::get($errorfile);
                $errorout = explode("\n", $errorout);
                $msg = $errorout[count($errorout) - 2];
            }
            $this->JobMonitorUpdate($jobID, $created_at, $started_at);
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, 1, $msg);
                // return; //TODO: uncomment before flight
            }
        }

        Storage::append($logfile, "----- allSNPs.py -----\n");
        Storage::append($errorfile, "----- allSNPs.py -----\n");

        $new_cmd = "docker run --rm --name job-$jobID -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-allsnps /bin/sh -c 'python allSNPs.py job >>job/job.log 2>>job/error.log'";

        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);
        if ($params['magma'] == 1) {
            Storage::append($logfile, "----- magma.py -----\n");
            Storage::append($errorfile, "----- magma.py -----\n");

            $new_cmd = "docker run --rm --name job-$jobID -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-magma /bin/sh -c 'python magma.py job >>job/job.log 2>>job/error.log'";
            Storage::append($logfile, "Command to be executed:");
            Storage::append($logfile, $new_cmd . "\n");
            exec($new_cmd, $output, $error);

            if ($error != 0) {
                $errorout = Storage::get($errorfile);
                if (preg_match('/MAGMA ERROR/', $errorout) == 1) {
                    $errorout = Storage::get($logfile);
                    $errorout = explode("\n", $errorout);
                    foreach ($errorout as $l) {
                        if (preg_match("/ERROR - /", $l) == 1) {
                            $msg = $l;
                            break;
                        }
                    }
                } else {
                    $msg = "server error";
                }
                $status = 2;
            }
        }

        Storage::append($logfile, "----- manhattan_filt.py -----\n");
        Storage::append($errorfile, "----- manhattan_filt.py -----\n");

        $new_cmd = "docker run --rm --name job-$jobID -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-manhattan_filt /bin/sh -c 'python manhattan_filt.py job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);
        if ($error != 0) {
            $this->rmFiles($filedir);
            // $this->chmod($filedir);
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->update(['status' => 'ERROR:003']);
            // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, 3, $msg);
                // return; //TODO: uncomment before flight
            }
        }

        Storage::append($logfile, "----- QQSNPs_filt.py -----\n");
        Storage::append($errorfile, "----- QQSNPs_filt.py -----\n");

        $new_cmd = "docker run --rm --name job-$jobID -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-qqsnps_filt /bin/sh -c 'python QQSNPs_filt.py job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);

        if ($error != 0) {
            $this->rmFiles($filedir);
            // $this->chmod($filedir);
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->update(['status' => 'ERROR:004']);
            // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, 4, $msg);
                // return; //TODO: uncomment before flight
            }
        }

        Storage::append($logfile, "----- getLD.py -----\n");
        Storage::append($errorfile, "----- getLD.py -----\n");

        $new_cmd = "docker run --rm --name job-$jobID -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getld /bin/sh -c 'python getLD.py job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);

        if ($error != 0) {
            $NoCandidates = false;
            $errorout = Storage::get($errorfile);
            $errorout = explode("\n", $errorout);
            foreach ($errorout as $l) {
                if (preg_match('/No candidate SNP was identified/', $l) == 1) {
                    $NoCandidates = true;
                    break;
                }
            }
            if ($NoCandidates) {
                $new_cmd = "docker run --rm --name job-$jobID -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getld /bin/sh -c 'python getTopSNPs.py job >>job/job.log 2>>job/error.log'";
                Storage::append($logfile, "Command to be executed:");
                Storage::append($logfile, $new_cmd . "\n");
                exec($new_cmd, $output, $error);

                $this->rmFiles($filedir);
                // $this->chmod($filedir);
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'ERROR:005']);
                // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
                if ($email != null) {
                    $this->sendJobCompMail($email, $jobtitle, $jobID, 5, $msg);
                }
                // return; //TODO: uncomment before flight
            } else {
                $this->rmFiles($filedir);
                // $this->chmod($filedir);
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'ERROR:006']);
                // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
                if ($email != null) {
                    $this->sendJobCompMail($email, $jobtitle, $jobID, 6, $msg);
                    // return; //TODO: uncomment before flight
                }
            }
        }

        Storage::append($logfile, "----- SNPannot.R -----\n");
        Storage::append($errorfile, "----- SNPannot.R -----\n");

        $new_cmd = "docker run --rm --name job-$jobID -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-snpannot /bin/sh -c 'Rscript SNPannot.R job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);

        if ($error != 0) {
            $this->rmFiles($filedir);
            // $this->chmod($filedir);
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->update(['status' => 'ERROR:007']);
            // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, 7, $msg);
                // return; //TODO: replace before flight
            }
        }

        Storage::append($logfile, "----- getGWAScatalog.py -----\n");
        Storage::append($errorfile, "----- getGWAScatalog.py -----\n");

        $new_cmd = "docker run --rm --name job-$jobID -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getgwascatalog /bin/sh -c 'python getGWAScatalog.py job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);

        if ($error != 0) {
            $this->rmFiles($filedir);
            // $this->chmod($filedir);
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->update(['status' => 'ERROR:008']);
            // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, 8, $msg);
                // return; //TODO: uncomment before flight
            }
        }

        #$script = Helper::scripts_path('getExAC.pl');
        #exec("perl $script $filedir");
        if ($params['eqtlMap'] == 1) {
            Storage::append($logfile, "----- geteQTL.py -----\n");
            Storage::append($errorfile, "----- geteQTL.py -----\n");

            $new_cmd = "docker run --rm --name job-$jobID -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-geteqtl /bin/sh -c 'python geteQTL.py job >>job/job.log 2>>job/error.log'";
            Storage::append($logfile, "Command to be executed:");
            Storage::append($logfile, $new_cmd . "\n");
            exec($new_cmd, $output, $error);

            if ($error != 0) {
                $this->rmFiles($filedir);
                // $this->chmod($filedir);
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'ERROR:009']);
                // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
                if ($email != null) {
                    $this->sendJobCompMail($email, $jobtitle, $jobID, 9, $msg);
                    // return; //TODO: uncomment before flight
                }
            }
        }

        if ($params['ciMap'] == 1) {
            Storage::append($logfile, "----- getCI.R -----\n");
            Storage::append($errorfile, "----- getCI.R -----\n");

            $new_cmd = "docker run --rm --name job-$jobID -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getci /bin/sh -c 'Rscript getCI.R job >>job/job.log 2>>job/error.log'";
            Storage::append($logfile, "Command to be executed:");
            Storage::append($logfile, $new_cmd . "\n");
            exec($new_cmd, $output, $error);

            if ($error != 0) {
                $this->rmFiles($filedir);
                // $this->chmod($filedir);
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'ERROR:010']);
                // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
                $errorout = Storage::get($errorfile);
                $errorout = explode("\n", $errorout);
                $msg = $errorout[count($errorout) - 2];
                if ($email != null) {
                    $this->sendJobCompMail($email, $jobtitle, $jobID, 10, $msg);
                    // return; //TODO: uncomment before flight
                }
            }
        }

        Storage::append($logfile, "----- geneMap.R -----\n");
        Storage::append($errorfile, "----- geneMap.R -----\n");

        $new_cmd = "docker run --rm --name job-$jobID -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-genemap /bin/sh -c 'Rscript geneMap.R job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);

        if ($error != 0) {
            $this->rmFiles($filedir);
            // $this->chmod($filedir);
            DB::table('SubmitJobs')->where('jobID', $jobID)
                ->update(['status' => 'ERROR:011']);
            // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
            if ($email != null) {
                $this->sendJobCompMail($email, $jobtitle, $jobID, 11, $msg);
                // return; //TODO: uncomment before flight
            }
        }
        if ($params['ciMap'] == 1) {
            Storage::append($logfile, "----- createCircosPlot.py -----\n");
            Storage::append($errorfile, "----- createCircosPlot.py -----\n");
            
            $new_cmd = "docker run --rm --name job-$jobID -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-create_circos_plot /bin/sh -c 'python createCircosPlot.py job >>job/job.log 2>>job/error.log'";
            Storage::append($logfile, "Command to be executed:");
            Storage::append($logfile, $new_cmd . "\n");
            exec($new_cmd, $output, $error);

            if ($error != 0) {
                $this->rmFiles($filedir);
                // $this->chmod($filedir);
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'ERROR:012']);
                // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)
                $errorout = Storage::get($errorfile);
                $errorout = explode("\n", $errorout);
                $msg = $errorout[count($errorout) - 2];
                if ($email != null) {
                    $this->sendJobCompMail($email, $jobtitle, $jobID, 12, $msg);
                    // return; //TODO: uncomment before flight
                }
            }
        }
        // $this->rmFiles($filedir);
        // $this->chmod($filedir);

        DB::table('SubmitJobs')->where('jobID', $jobID)
            ->update(['status' => 'OK']);
        // $this->JobMonitorUpdate($jobID, $created_at, $started_at); //TODO: to be replaced (it crashes if it's called twice or more because it tries to insert the same id)

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
        if ($status == 0 || $status == 2) {
            $user = DB::table('users')->where('email', $email)->first();
            $data = [
                'jobID' => $jobID,
                'jobtitle' => $jobtitle,
                'status' => $status,
                'msg' => $msg
            ];
            try {
                // Mail::send('emails.jobComplete', $data, function ($m) use ($user) {
                //     $m->from('noreply@ctglab.nl', "FUMA web application");
                //     $m->to($user->email, $user->name)->subject("FUMA your job has been completed");
                // });
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
                // Mail::send('emails.jobError', $data, function ($m) use ($user) {
                //     $m->from('noreply@ctglab.nl', "FUMA web application");
                //     $m->to($user->email, $user->name)->subject("FUMA an error occured");
                // });
            } catch (Throwable $e) {
            }
        }
        return;
    }

    public function sendJobFailedMail($email, $jobtitle, $jobID)
    {
        $user = $this->user;
        $data = [
            'jobtitle' => $jobtitle,
            'jobID' => $jobID
        ];
        $devemail = config('app.devemail');
        try {
            // Mail::send('emails.jobFailed', $data, function ($m) use ($user, $devemail) {
            //     $m->from('noreply@ctglab.nl', "FUMA web application");
            //     $m->to($user->email, $user->name)->cc($devemail)->subject("FUMA job failed");
            // });
        } catch (Throwable $e) {
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

    public function rmFiles($filedir)
    {
        if (Storage::exists($filedir . "input.gwas")) {
            Storage::delete($filedir . "input.gwas");
        }
        if (Storage::exists($filedir . "input.snps")) {
            Storage::delete($filedir . "input.snps");
        }
        if (Storage::exists($filedir . "input.lead")) {
            Storage::delete($filedir . "input.lead");
        }
        if (Storage::exists($filedir . "input.regions")) {
            Storage::delete($filedir . "input.regions");
        }
        if (Storage::exists($filedir . "magma.input")) {
            Storage::delete($filedir . "magma.input");
        }
        return;
    }

    public function chmod($filedir)
    {
        exec("find " . $filedir . " -type d -exec chmod 775 {} \;");
        exec("find " . $filedir . " -type f -exec chmod 664 {} \;");
    }
}
