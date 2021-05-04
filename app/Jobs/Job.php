<?php

namespace fuma\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;

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
    public $starttime;
    public $faulted_command = null;
    public $timedout = false;


    public function setStartTime() {
        $this->starttime = time();
    }

    private function setTimeoutError($command_string) {
        Log::info("Timed out!: ".$command_string);
        $this->faulted_command = $command_string;
        $this->timedout = true;
    }

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
}
