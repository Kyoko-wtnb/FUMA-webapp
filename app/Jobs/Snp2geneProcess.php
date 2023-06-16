<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\TimeoutExceededException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\SubmitJob;
use JobHelper;

class Snp2geneProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $jobID;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

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
        SubmitJob::where('jobID', $jobID)
            ->update([
                'status' => 'RUNNING',
                'started_at' => $started_at,
                'uuid' => $this->job->uuid()
            ]);
        $job_type = SubmitJob::find($jobID)->type;

        // file check
        if (!Storage::exists(config('app.jobdir') . '/jobs/' . $jobID . '/input.gwas')) {
            JobHelper::JobTerminationHandling($jobID, 0);
            return;
        }

        // get parameters
        $this->filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';
        $this->ref_data_path_on_host = config('app.ref_data_on_host_path');

        if (!Storage::exists($this->filedir . "params.config")) {
            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 1);
            return;
        }

        $params = parse_ini_string(Storage::get($this->filedir . "params.config"), false, INI_SCANNER_RAW);

        // log files
        $this->logfile = $this->filedir . "job.log";
        $this->errorfile = $this->filedir . "error.log";

        if ($job_type === 'snp2gene') {
            if (!$this->gwas_file()) {
                // error handling
                return;
            }

            if (!$this->allSNPs()) {
                // error handling
                return;
            }

            if ($params['magma'] == 1) {
                if (!$this->magma()) {
                    // error handling
                    return;
                }
            }

            if (!$this->manhattan_filt()) {
                // error handling
                return;
            }

            if (!$this->QQSNPs_filt()) {
                // error handling
                return;
            }

            if (!$this->getLD()) {
                // error handling
                return;
            }

            if (!$this->SNPannot()) {
                // error handling
                return;
            }

            if (!$this->getGWAScatalog()) {
                // error handling
                return;
            }

            if ($params['eqtlMap'] == 1) {
                if (!$this->geteQTL()) {
                    // error handling
                    return;
                }
            }

            if ($params['ciMap'] == 1) {
                if (!$this->getCI()) {
                    // error handling
                    return;
                }
            }

            if (!$this->geneMap()) {
                // error handling
                return;
            }

            if ($params['ciMap'] == 1) {
                if (!$this->createCircosPlot()) {
                    // error handling
                    return;
                }
            }
        } else if ($job_type === 'geneMap') {
            if ($params['eqtlMap'] == 1) {
                if (!$this->geteQTL()) {
                    // error handling
                    return;
                }
            }

            if ($params['ciMap'] == 1) {
                if (!$this->getCI()) {
                    // error handling
                    return;
                }
            }

            if (!$this->geneMap()) {
                // error handling
                return;
            }

            if ($params['ciMap'] == 1) {
                if (!$this->createCircosPlot()) {
                    // error handling
                    return;
                }
            }
        }

        //-------------------------------------------------------------------

        JobHelper::rmFiles($this->filedir);
        JobHelper::JobTerminationHandling($jobID, 15);

        return;
    }

    public function gwas_file()
    {
        $jobID = $this->jobID;
        Storage::put($this->logfile, "----- gwas_file.py -----\n");
        Storage::put($this->errorfile, "----- gwas_file.py -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-gwas_file /bin/sh -c 'python gwas_file.py job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");

        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);

            $msg = "No error log found for SNP2GENE job ID: $jobID";
            if (Storage::exists($this->errorfile)) {
                $errorout = Storage::get($this->errorfile);
                $errorout = explode("\n", $errorout);
                $msg = $errorout[count($errorout) - 2];
            }
            JobHelper::JobTerminationHandling($jobID, 2, $msg);
            return false;
        }
        return true;
    }

    private function allSNPs()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- allSNPs.py -----\n");
        Storage::append($this->errorfile, "----- allSNPs.py -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-allsnps /bin/sh -c 'python allSNPs.py job >>job/job.log 2>>job/error.log'";

        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);

            $msg = "No error log found for SNP2GENE job ID: $jobID";
            if (Storage::exists($this->errorfile)) {
                $errorout = Storage::get($this->errorfile);
                $errorout = explode("\n", $errorout);
                $msg = $errorout[count($errorout) - 2];
            }
            JobHelper::JobTerminationHandling($jobID, 3, $msg);
            return false;
        }
        return true;
    }

    private function magma()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- magma.py -----\n");
        Storage::append($this->errorfile, "----- magma.py -----\n");

        $uuid = Str::uuid();
        $new_cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-magma /bin/sh -c 'python magma.py job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $new_cmd . "\n");
        exec($new_cmd, $output, $error);

        if ($error) {
            $errorout = Storage::get($this->errorfile);
            if (preg_match('/MAGMA ERROR/', $errorout) == 1) {
                $errorout = Storage::get($this->logfile);
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

            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 4, $msg);
            return false;
        }
        return true;
    }

    private function manhattan_filt()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- manhattan_filt.py -----\n");
        Storage::append($this->errorfile, "----- manhattan_filt.py -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-manhattan_filt /bin/sh -c 'python manhattan_filt.py job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);
        if ($error) {
            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 5);
            return false;
        }
        return true;
    }

    private function QQSNPs_filt()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- QQSNPs_filt.py -----\n");
        Storage::append($this->errorfile, "----- QQSNPs_filt.py -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-qqsnps_filt /bin/sh -c 'python QQSNPs_filt.py job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 6);
            return false;
        }
        return true;
    }

    private function getLD()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- getLD.py -----\n");
        Storage::append($this->errorfile, "----- getLD.py -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getld /bin/sh -c 'python getLD.py job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            $NoCandidates = false;
            $errorout = Storage::get($this->errorfile);
            $errorout = explode("\n", $errorout);
            foreach ($errorout as $l) {
                if (preg_match('/No candidate SNP was identified/', $l) == 1) {
                    $NoCandidates = true;
                    break;
                }
            }
            if ($NoCandidates) {
                $uuid = Str::uuid();
                $cmd = "docker run --rm --name job-$jobID-$uuid -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getld /bin/sh -c 'python getTopSNPs.py job >>job/job.log 2>>job/error.log'";
                Storage::append($this->logfile, "Command to be executed:");
                Storage::append($this->logfile, $cmd . "\n");
                exec($cmd, $output, $error);

                JobHelper::rmFiles($this->filedir);
                JobHelper::JobTerminationHandling($jobID, 7);
                return false;
            }
            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 8);
            return false;
        }
        return true;
    }

    private function SNPannot()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- SNPannot.R -----\n");
        Storage::append($this->errorfile, "----- SNPannot.R -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-snpannot /bin/sh -c 'Rscript SNPannot.R job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 9);
            return false;
        }
        return true;
    }

    private function getGWAScatalog()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- getGWAScatalog.py -----\n");
        Storage::append($this->errorfile, "----- getGWAScatalog.py -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getgwascatalog /bin/sh -c 'python getGWAScatalog.py job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 10);
            return false;
        }
        return true;
    }

    private function geteQTL()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- geteQTL.py -----\n");
        Storage::append($this->errorfile, "----- geteQTL.py -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-geteqtl /bin/sh -c 'python geteQTL.py job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 11);
            return false;
        }
        return true;
    }

    private function getCI()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- getCI.R -----\n");
        Storage::append($this->errorfile, "----- getCI.R -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-getci /bin/sh -c 'Rscript getCI.R job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);

            $errorout = Storage::get($this->errorfile);
            $errorout = explode("\n", $errorout);
            $msg = $errorout[count($errorout) - 2];

            JobHelper::JobTerminationHandling($jobID, 12, $msg);
            return false;
        }
        return true;
    }

    private function geneMap()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- geneMap.R -----\n");
        Storage::append($this->errorfile, "----- geneMap.R -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-genemap /bin/sh -c 'Rscript geneMap.R job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);
            JobHelper::JobTerminationHandling($jobID, 13);
            return false;
        }
        return true;
    }

    private function createCircosPlot()
    {
        $jobID = $this->jobID;
        Storage::append($this->logfile, "----- createCircosPlot.py -----\n");
        Storage::append($this->errorfile, "----- createCircosPlot.py -----\n");

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v $this->ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-create_circos_plot /bin/sh -c 'python createCircosPlot.py job >>job/job.log 2>>job/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");
        exec($cmd, $output, $error);

        if ($error) {
            JobHelper::rmFiles($this->filedir);

            $errorout = Storage::get($this->errorfile);
            $errorout = explode("\n", $errorout);
            $msg = $errorout[count($errorout) - 2];

            JobHelper::JobTerminationHandling($jobID, 14, $msg);
            return false;
        }
        return true;
    }

    public function failed($exception): void
    {
        if ($exception instanceof TimeoutExceededException) {
            JobHelper::JobTerminationHandling($this->jobID, 17);
        } else {
            JobHelper::JobTerminationHandling($this->jobID, 16);
        }
    }
}
