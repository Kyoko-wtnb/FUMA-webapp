<?php

namespace IPGAP\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use IPGAP\Http\Controllers\Controller;
use DB;
use IPGAP\Jobs\snp2geneProcess;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        // $schedule->call(function(){
        //   $newjobs = DB::table('SubmitJobs')->where('status', 'NEW')->get();
        //   foreach($newjobs as $job){
        //     $email = $job->email;
        //     $user = DB::table('users')->where('email', $email)->first();
        //     $jobID = $job->jobID;
        //     DB::table('SubmitJobs') -> where('jobID', $jobID)
        //       -> update(['status'=>'QUEUED']);
        //     \IPGAP\Http\Controllers\Controller::dispatch(new snp2geneProcess($user, $jobID));
        //   }
        // })->everyMinute();
    }
}
