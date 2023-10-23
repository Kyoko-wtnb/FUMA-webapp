<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use App\Models\SubmitJob;
use App\Mail\JobCompletedSuccessfully;
use App\Mail\JobFailedWithErrorCode;
use Mail;


class JobHelper
{
    public static function sendJobMail($job, $mailer)
    {
        if (App::isProduction()) {
            try {
                Mail::to($job->user->email, $job->user->name)
                    ->send($mailer);
                return true;
            } catch (Throwable $e) {
                return false;
            }
        }
        return;
    }

    public static function JobTerminationHandling($jobID, $report_code, $msg = null)
    {
        $job = SubmitJob::find($jobID);

        $job->status = config('snp2gene_status_codes.' . $report_code . '.short_name');
        $job->completed_at = date("Y-m-d H:i:s");
        $job->save();

        if ($msg == null) {
            $msg = config('snp2gene_status_codes.' . $report_code . '.email_message');
        }

        if (config('snp2gene_status_codes.' . $report_code . '.type') == 'err') {
            // error occured
            JobHelper::sendJobMail($job, new JobFailedWithErrorCode($job, $msg));
        } elseif (config('snp2gene_status_codes.' . $report_code . '.type') == 'success') {
            // success
            JobHelper::sendJobMail($job, new JobCompletedSuccessfully($job, $msg));
        }
        return;
    }

    public static function rmFiles($filedir)
    {
        // Clean up some files if nessesary
        return;
    }
}
