<?php

namespace fuma\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Auth;
use Config;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Returns the job timeout for the authorized user
     * based on thier Roles. Returns 0 if no user is authorized
     */
    public function getJobTimeoutForAuthUser() {
        if (is_null(Auth::user())) {
            return 0;
        }
        $timeouts = Config::get('queue.jobLimits.timeouts');
        // Go through all assigned user roles and get the maximum timeout
        // jobs info is indexed on role names
        $roles = Auth::user()->roles;
        return $roles->reduce(function($carry, $item) use (&$timeouts) {
            $roleTimeout = $timeouts[$item["name"]] ?? 0;
            if (($carry == -1) || ($roleTimeout == -1)) {
                $carry = -1;
            } elseif ($roleTimeout > $carry) {
                $carry = $roleTimeout;
            }
            return $carry;
        }, 0);
    }

    /**
     * Returns the maximum number of jobs for the authorized user
     * based on thier Roles. Returns 0 if no user is authorized
     */
    public function getMaxJobsForAuthUser() {
        if (is_null(Auth::user())) {
            return 0;
        }
        // Go through all assigned user roles and get the maximum number of jobs
        // jobs info is indexed on role names
        $maxJobs = Config::get('queue.jobLimits.maxJobs');
        $roles = Auth::user()->roles;
        return $roles->reduce(function($carry, $item) use (&$maxJobs) {
            $roleMax = $maxJobs[$item["name"]] ?? 0;
            if (($carry == -1) || ($roleMax == -1)) {
                $carry = -1;
            } elseif ($roleMax > $carry) {
                $carry = $roleMax;
            }
            return $carry;
        }, 0);
    }

}
