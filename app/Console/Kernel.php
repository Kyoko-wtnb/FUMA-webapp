<?php

namespace fuma\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use fuma\Http\Controllers\Controller;
use DB;
use Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
	      Commands\ListStaleJobs::class,
        Commands\RestartStaleJobs::class,
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

        $schedule->call(function(){
          $date = date("Y-m-d");
          $dateNext = $date;
          $time = date("H");
          $report = "today";
          if ($time < 12){
            $report = "yesterday";
            $date = strtotime($date)-60*60*24;
            $date = date("Y-m-d", $date);
          } else {
            $dateNext = strtotime($date)+60*60*24;
            $dateNext = date("Y-m-d", $dateNext);
          }
          $totalNjobs = DB::table('SubmitJobs')->count();
          $totalUsers = DB::table('users')->count();
          $submittedjobs = DB::table('SubmitJobs')->whereColumn([["created_at", ">", $date], ["created_at", "<", $dateNext]])->count();
          $running = DB::table("SubmitJobs")->where("status", "RUNNING")->get()->all();
          $runTable = "";
          if(count($running)>0){
            $runTable .= "<table class='table table-bordered'><thead><th>jobID</th><th>email<th><th>created_at</th></thead><tbody>";
            foreach($running as $row){
              $runtable .= "<tr>";
              $runTable .= "<td>".$row->jobID."</td>";
              $runTable .= "<td>".$row->email."</td>";
              $runTable .= "<td>".$row->created_at."</td>";
              $runTable .= "</tr>";
            }
            $runTable .= "</tbody></table>";
          }
          $queued = DB::table("SubmitJobs")->where("status", "QUEUED")->get()->all();
          $queTable = "";
          if(count($queued)>0){
            $queTable .= "<table class='table table-bordered'><thead><th>jobID</th><th>email<th><th>created_at</th></thead><tbody>";
            foreach($queued as $row){
              $quetable .= "<tr>";
              $queTable .= "<td>".$row->jobID."</td>";
              $queTable .= "<td>".$row->email."</td>";
              $queTable .= "<td>".$row->created_at."</td>";
              $queTable .= "</tr>";
            }
            $queTable .= "</tbody></table>";
          }
          $all = DB::table("JobMonitor")->get()->all();
          $dateStats = [];
          $ProcessStartTimeAvg = [];
          $ProcessTimeAvg = [];
          foreach($all as $a){
            $created_at = $a->created_at;
            $started_at = $a->started_at;
            $completed_at = $a->completed_at;

            $datetmp = preg_replace("/(.+) \d+:\d+:\d+/", '$1', $created_at);
            $created_at = strtotime($created_at);
            $started_at = strtotime($started_at);
            $completed_at = strtotime($completed_at);

            if(array_key_exists($datetmp, $dateStats)){
              $dateStats[$datetmp] += 1;
              $ProcessStartTimeAvg[$datetmp] += $started_at - $created_at;
              $ProcessTimeAvg[$datetmp] += $completed_at - $started_at;
            }else{
              $dateStats[$datetmp] = 1;
              $ProcessStartTimeAvg[$datetmp] = $started_at - $created_at;
              $ProcessTimeAvg[$datetmp] = $completed_at - $started_at;
            }
          }

          $dateavg = 0;
          foreach ($dateStats as $key => $val){
            $dateavg += $val;
            $ProcessStartTimeAvg[$key] = $ProcessStartTimeAvg[$key]/$val;
            $ProcessTimeAvg[$key] = $ProcessTimeAvg[$key]/$val;
          }
          $dateavg = $dateavg/count($dateStats);

          $ProcessTable = "<table class='table table-bordered'><thead><th>Date</th><th>ProcessStartTimeAvg</th><th>ProcessTimeAvg</th></thead><tbody>";
          foreach ($ProcessTimeAvg as $key=>$val){
            $ProcessTable .= "<tr>";
            $ProcessTable .= "<td>".$key."</td>";
            $ProcessTable .= "<td>".$ProcessStartTimeAvg[$key]."</td>";
            $ProcessTable .= "<td>".$ProcessTimeAvg[$key]."</td>";
            $ProcessTable .= "</tr>";
          }
          $ProcessTable .= "</tbody></table>";

          $data = [
            'date'=>$date,
            'totalNjobs'=>$totalNjobs,
            'totalUsers'=>$totalUsers,
            'running'=>count($running),
            'queued'=>count($queued),
            'dateavg'=>$dateavg
          ];

          $file = storage_path().'/JobReport.html';
          $html = "<html>
            <head>
              <h3>FUMA Monitor Report for $date</h3>
              <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
            </head>
            <body>
            <p><h4>Process aberage time</h4>
              $ProcessTable
            </p>

            <p><h4>Running jobs</h4>
              $runTable
            </p>

            <p><h4>Queued jobs</h4>
              $queTable
            </p>
            </body>
            </html>";

          file_put_contents($file, $html);

          Mail::send('emails.JobMonitor', $data, function($m){
            $m->from('noreply@ctglab.nl', "FUMA web application");
            $m->to("k.watanabe@vu.nl", "Kyoko Watanabe")->subject("FUMA Monitor Report");
            $m->attach(storage_path()."/JobReport.html");
          });
        })->everyMinute();
        // })->twiceDaily(8, 20);
    }

}
