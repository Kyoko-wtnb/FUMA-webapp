<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use App\Models\SubmitJob;
use Mail;


class JobHelper
{
    public static function sendJobMail($user, $job, $subject, $msg)
    {
        if (App::isProduction()) {
            $data = [
                'jobID' => $job->jobID,
                'jobtitle' => $job->title,
                'status' => $$job->status,
                'msg' => $msg
            ];
            try {
                Mail::send('emails.jobComplete', $data, function ($m) use ($user, $subject) {
                    $m->from('noreply@ctglab.nl', "FUMA web application");
                    $m->to($user->email, $user->name)->subject($subject);
                });
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

            // Comment out if you wanna delete the db entry and/or directory
            // $job->delete();
            // Storage::deleteDirectory(config('app.jobdir') . '/jobs/' . $jobID);
            JobHelper::sendJobMail($job->user, $job, 'FUMA job failed', $msg);
        } elseif (config('snp2gene_status_codes.' . $report_code . '.type') == 'success') {
            // success
            JobHelper::sendJobMail($job->user, $job, 'FUMA your job has been completed', $msg);
        }
        return;
    }

    public static function rmFiles($filedir)
    {
        // Clean up some files if nessesary
        return;
    }
}
