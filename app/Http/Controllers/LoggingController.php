<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use LogicException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LoggingController extends Controller
{
    /**
     * Log a client side error under a job directory
     * @param Request $request 
     * @return void 
     * @throws LogicException 
     * @throws BindingResolutionException 
     */
    public function logClientError(Request $request)
    {
        $jobID = $request->input('id', NULL);
        if (intval($jobID) == 0) {
            return;
        }
        $msg = $request->input('msg', '');
        $stack = $request->input('stack', NULL);
        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';
        $clientlogfile = $filedir . 'client_error.log';
        Storage::append($clientlogfile, '[' . Carbon::now() . ']: Client Error:' . PHP_EOL . $msg . PHP_EOL);
        if (!is_null($stack)) {
            Storage::append($clientlogfile, '*** Call stack ***:' . PHP_EOL . $stack . PHP_EOL);
        }
        return;
    }
}
