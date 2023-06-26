<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\TimeoutExceededException;
use App\CustomClasses\DockerApi\DockerNamesBuilder;
use Illuminate\Support\Facades\Storage;
use App\Models\SubmitJob;
use JobHelper;

class CelltypeProcess implements ShouldQueue
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

        $filedir = config('app.jobdir') . '/celltype/' . $jobID . '/';
        $ref_data_path_on_host = config('app.ref_data_on_host_path');
        $logfile = $filedir . "job.log";
        $errorfile = $filedir . "error.log";

        // $script = scripts_path('magma_celltype.R');
        Storage::put($logfile, "----- magma_celltype.R -----\n");
        Storage::put($errorfile, "----- magma_celltype.R -----\n");
        
        $container_name = DockerNamesBuilder::containerName($jobID);
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'magma_celltype');
                
        $cmd = "docker run --rm --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_cell_jobs_on_host') . "/$jobID/:/app/job " . $image_name . " /bin/sh -c 'Rscript magma_celltype.R job >>job/job.log 2>>job/error.log'";
        Storage::append($logfile, "Command to be executed:");
        Storage::append($logfile, $cmd . "\n");

        exec($cmd, $output, $error);

        if ($error != 0) {
            JobHelper::rmFiles($filedir);
            JobHelper::JobTerminationHandling($jobID, 17, 'CellType error occured');
            return;
        }

        JobHelper::rmFiles($filedir);
        JobHelper::JobTerminationHandling($jobID, 15);
        return;
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
