<?php

namespace fuma\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

use Config;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /**
     * Returns the queue cap value
     * 
     * A null return indicates no timeout
     */
    public function getQueueCap() {
        $queue_cap = config('queue.jobLimits.queue_cap', 10);
        return $queue_cap;
    }
}
