<?php

namespace fuma\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Exception;

abstract class Job
{
    /*
    |--------------------------------------------------------------------------
    | Queueable Jobs
    |--------------------------------------------------------------------------
    |
    | This job base class provides a central location to place any logic that
    | is shared across all of your jobs. The trait included with the class
    | provides access to the "onQueue" and "delay" queue helper methods.
    |
    */

    use Queueable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = null;

    /**
     * Save the start time (unix seconds)
     */
    public $starttime = null;
    /**
     * The command that faulted in timedExec
     */
    public $faulted_command = null;
    /**
     * Set to true if failed was called 
     * the failure is not necessarily a timeout
     */
    public $failed = false;
    /**
     * Set to true if a timeout was detected
     */
    public $timedout = false;
    /**
     * Approximate running time of the job
     */
    public $elapsed = null;
 
    public function setStartTime() {
        $this->starttime = time();
    }

    private function setTimeoutError($command_string) {
        Log::info("Timed out!: ".$command_string);
        $this->faulted_command = $command_string;
        $this->timedout = true;
        $this->elapsed = time() - $this->starttime;
    }

    /**
     * Wrap long running exec sub-jobs and trigger a timeout
     * based on the remaining time if a job timeout is defined.
     */
    public function timedExec($command_string, &$output=null, &$error=null) {
        if (is_null($this->timeout)) {
            exec($command_string, $output, $error);
            return;
        }
        $padding = 10; // Allow 10 seconds to record the timeout before pcntl kicks in
        $remaining_time = ($this->timeout - (time() - $this->starttime)) - $padding;
        if ($remaining_time <= 0) {
            $this->setError($command_string);
            return;
        }
        $timed_command = sprintf("timeout %d %s", $remaining_time, $command_string);
        Log::info("Executing with timeout: ".$timed_command);
        exec($timed_command, $output, $error);
        Log::info(sprintf("Completed with error code: %s", $error));
        if ($error == 124){
            $this->setTimeoutError($command_string);
        }
    }

	/**
     * Handle a job failure.
     * General code to detect and record a timeout
     * and the elapsed time if there was a timeout.
     * 
     * @param  Exception  $exception
     * @return void
     */
	public function failed($exception){
        $this->failed = true;
        Log::info(sprintf("starttime: %d timeout: %d: now: %d", $this->starttime, $this->timeout, time()));
        if(!is_null($this->starttime) && !is_null($this->timeout)) {
            $this->elapsed = time() - $this->starttime;
            Log::info(sprintf("Elapsed time job: %d", $this->elapsed));
            if ($this->elapsed >= $this->timeout) {
                $this->timedout = true;
            }
        }
    }
}
