<?php

namespace IPGAP\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use IPGAP\Http\Controllers\Controller;
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
          $submittedjobs = DB::table('SubmitJobs')->whereColumn([["created_at", ">", $date], ["created_at", "<", $dateNext]])->count();
          $running = DB::table("SubmitJobs")->where("status", "RUNNING")->get();
          $runTable = "";
          if(count($running)>0){
            $runTable .= "<table><thead><th>jobID</th><th>email<th><th>created_at</th></thead><tbody>";
            foreach($running as $row){
              $runtable .= "<tr>";
              $runTable .= "<td>".$row->jobID."</td>";
              $runTable .= "<td>".$row->email."</td>";
              $runTable .= "<td>".$row->created_at."</td>";
              $runTable .= "</tr>";
            }
            $runTable .= "</tbody></table>";
          }
          $queued = DB::table("SubmitJobs")->where("status", "QUEUED")->get();
          $queTable = "";
          if(count($queued)>0){
            $queTable .= "<table><thead><th>jobID</th><th>email<th><th>created_at</th></thead><tbody>";
            foreach($queued as $row){
              $quetable .= "<tr>";
              $queTable .= "<td>".$row->jobID."</td>";
              $queTable .= "<td>".$row->email."</td>";
              $queTable .= "<td>".$row->created_at."</td>";
              $queTable .= "</tr>";
            }
            $queTable .= "</tbody></table>";
          }
          $all = DB::table("SubmitJobs")->select('created_at')->get();
          $dateStats = [];
          foreach($all as $a){
            $tmp = $a->created_at;
            $tmp = preg_replace("/(.+) \d+:\d+:\d/", '$1', $tmp);
            if(array_key_exists($tmp, $dateStats)){
              $dateStats[$tmp] += 1;
            }else{
              $dateStats[$tmp] = 1;
            }
          }

          $dateavg = 0;
          foreach ($dateStats as $val){
            $dateavg += $val;
          }
          $dateavg = $dateavg/count($dateStats);

          $data = [
            'date'=>$date,
            'totalNjobs'=>$totalNjobs,
            'running'=>count($running),
            'runTable'=>$runTable,
            'queued'=>count($queued),
            'queTable'=>$queTable,
            'dateavg'=>$dateavg
          ];

          Mail::send('emails.JobMonitor', $data, function($m){
            $m->from('noreply@ctglab.nl', "FUMA web application");
            $m->to("k.watanabe@vu.nl", "Kyoko Watanabe")->subject("FUMA Monitor Report");
          });
        })->everyMinute();
        // })->twiceDaily(8, 20);
    }
}
