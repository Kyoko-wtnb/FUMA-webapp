<?php

namespace fuma\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use fuma\SubmitJob;
use fuma\Jobs\snp2geneProcess;
use Auth;

class RestartStaleJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:restartstale
                           {olderthanhours : At least how long ago (in hours) the job was created.}
                           {emails* : The user emails used to filter the jobs or the string all. }
                           {--dry : Perform a dry run, does not actually dispatch the jobs.}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function restartJob($job, $test)
    {
        $this->info('email: ' . $job->email . ' jobID: ' . $job->jobID);
        // Get the user details needed to dispatch the job
        $user_info = DB::table('users')->where('email', "{$job->email}")->first();
        // Is the job already queued, the user id and job id are in the payload of a jobs entry.
        // If sothen we are done
        $queued_jobs = DB::table('jobs')->where('payload', 'LIKE', '%snp2geneProcess%%i:{$user_info->id}%%{$job->jobID}%')->get()->all();
	if (count($queued_jobs) > 0) {
	    $this->info("Job: {$job->jobID} is already queued");
            return;
        }
        // Otherwise log in as the user
        auth()->loginUsingId($user_info->id);
        $user = Auth::user();
        // $this->info("working user {$user}");
        // And dispatch the snp2genejob
	if (!$test) {
            $this->info("Queueing jobID: {$job->jobID}");
            $queueId = dispatch(new snp2geneProcess($user, $job->jobID));
            $this->info("In jobs table at id {$queueId}");
        }
        Auth::logout();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $staledate = Carbon::now()->subHours($this->argument('olderthanhours'));
        $emails = $this->argument('emails');
	$test = $this->option('dry');
	// ask for all users
	$jobs = collect();
	if (count($emails) == 1 && $emails[0] == "all") {
        	$jobs = SubmitJob::where('status', 'QUEUED')->where('created_at', '<', $staledate)->orderBy('created_at', 'asc')->get();
        }
	else {
	// or selected users
		foreach ($emails as $email) {
			$this->info('Searching stale jobs for user ' . $email);
			$tmpres = SubmitJob::where('status', 'QUEUED')
				->where('email', "{$email}")
				->where('created_at', '<', $staledate)->get();
			if (count($tmpres) > 0) {
				$jobs = $jobs->merge($tmpres);
			}
		}
	}
	$this->info('About to attempt to restart ' . count($jobs) . " jobs\n");
        foreach ($jobs as $job) {
            $this->restartJob($job, $test);
        }
    }
}
