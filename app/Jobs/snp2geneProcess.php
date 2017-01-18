<?php

namespace IPGAP\Jobs;

use IPGAP\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use Illuminate\Support\Facades\DB;

class snp2geneProcess extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;
    protected $jobID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $jobID)
    {
        $this->user = $user;
        $this->jobID = $jobID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      // Update status when job is started
      $jobID = $this->jobID;
      DB::table('SubmitJobs') -> where('jobID', $jobID)
                      -> update(['status'=>'RUNNING']);

      $user = $this->user;
      $email = $user->email;
      $jobtitle = DB::table('SubmitJobs') -> where('jobID', $jobID)
          ->first() ->title;

      // file check
      if(!file_exists(config('app.jobdir').'/jobs/'.$jobID.'/input.gwas')){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          ->delete();
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, -1);
          return;
        }
      }

      // get parameters
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $params = parse_ini_file($filedir."params.config");

      // log files
      $logfile = $filedir."job.log";
      $errorfile = $filedir."error.log";

      //error message
      $msg = "";

      //gwas_file.pl
      file_put_contents($logfile, "----- gwas_file.pl -----\n");
      file_put_contents($errorfile, "----- gwas_file.pl -----\n");
      $script = storage_path().'/scripts/gwas_file.pl';
      exec("perl $script $filedir >>$logfile 2>>$errorfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:001']);

        $errorout = file_get_contents($errorfile);
        $errorout = explode("\n", $errorout);
        $msg = $errorout[count($errorout)-2];
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 1, $msg);
          return;
        }
      }

      file_put_contents($logfile, "\n----- magma.pl -----\n", FILE_APPEND);
      file_put_contents($errorfile, "\n----- magma.pl -----\n", FILE_APPEND);
      $script = storage_path().'/scripts/magma.pl';
      exec("perl $script $filedir >>$logfile 2>>$errorfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:002']);
        $errorout = file_get_contents($errorfile);
        if(preg_match('/MAGMA ERROR/', $errorout)==1){
          $errorout = file_get_contents($logfile);
          $errorout = explode("\n", $errorout);
          foreach($errorout as $l){
            if(preg_match("ERROR - ", $l)==1){
              $msg = $l;
              break;
            }
          }
        }else{
          $msg = "server error";
        }
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 2, $msg);
          return;
        }
      }

      file_put_contents($logfile, "\n----- manhattan_filt.py -----\n", FILE_APPEND);
      file_put_contents($errorfile, "\n----- manhattan_filt.py -----\n", FILE_APPEND);
      $script = storage_path().'/scripts/manhattan_filt.py';
      exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:003']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 3, $msg);
          return;
        }
      }

      file_put_contents($logfile, "\n----- QQSNPs_filt.py -----\n", FILE_APPEND);
      file_put_contents($errorfile, "\n----- QQSNPs_filt.py -----\n", FILE_APPEND);
      $script = storage_path().'/scripts/QQSNPs_filt.py';
      exec("python $script $filedir >>$logfile 2>>$errorfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:004']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 4, $msg);
          return;
        }
      }

      file_put_contents($logfile, "\n----- getLD.pl -----\n", FILE_APPEND);
      file_put_contents($errorfile, "\n----- getLD.pl -----\n", FILE_APPEND);
      $script = storage_path().'/scripts/getLD.pl';
      // $process = new Proces("/usr/bin/perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC");
      // $process -> start();
      // echo "perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC";
      exec("perl $script $filedir >>$logfile 2>>$errorfile", $output, $error);
      if($error != 0){
        $NoCandidates = false;
        $errorout = file_get_contents($errorfile);
        $errorout = explode("\n", $errorout);
        foreach($errorout as $l){
          if(preg_match('/No candidate SNP was identified/', $l)==1){
            $NoCandidates = true;
            break;
          }
        }
        if($NoCandidates){
          DB::table('SubmitJobs') -> where('jobID', $jobID)
                            -> update(['status'=>'ERROR:005']);
          $script = storage_path().'/scripts/getTopSNPs.py';
          exec("python $script $filedir >>$logfile 2>>$errorfile");
          if($email!=null){
            $this->sendJobCompMail($email, $jobtitle, $jobID, 5, $msg);
          }
          return;
        }else{
          DB::table('SubmitJobs') -> where('jobID', $jobID)
                            -> update(['status'=>'ERROR:006']);
          if($email!=null){
            $this->sendJobCompMail($email, $jobtitle, $jobID, 6, $msg);
            return;
          }
        }

      }

      file_put_contents($logfile, "\n----- SNPannot.R -----\n", FILE_APPEND);
      file_put_contents($errorfile, "\n----- SNPannot.R -----\n", FILE_APPEND);
      $script = storage_path().'/scripts/SNPannot.R';
      exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:007']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 7, $msg);
          return;
        }
      }

      file_put_contents($logfile, "\n----- getGWAScatalog.pl -----\n", FILE_APPEND);
      file_put_contents($errorfile, "\n----- getGWAScatalog.pl -----\n", FILE_APPEND);
      $script = storage_path().'/scripts/getGWAScatalog.pl';
      exec("perl $script $filedir >>$logfile 2>>$errorfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:008']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 8, $msg);
          return;
        }
      }

      #$script = storage_path().'/scripts/getExAC.pl';
      #exec("perl $script $filedir");
      if($params['eqtlMap']==1){
        file_put_contents($logfile, "\n----- geteQTL.pl -----\n", FILE_APPEND);
        file_put_contents($errorfile, "\n----- geteQTL.pl -----\n", FILE_APPEND);
        $script = storage_path().'/scripts/geteQTL.pl';
        exec("perl $script $filedir >>$logfile 2>>$errorfile", $output, $error);
        if($error != 0){
          DB::table('SubmitJobs') -> where('jobID', $jobID)
                            -> update(['status'=>'ERROR:009']);
          if($email!=null){
            $this->sendJobCompMail($email, $jobtitle, $jobID, 9, $msg);
            return;
          }
        }
      }

      file_put_contents($logfile, "\n----- geneMap.R -----\n", FILE_APPEND);
      file_put_contents($errorfile, "\n----- geneMap.R -----\n", FILE_APPEND);
      $script = storage_path().'/scripts/geneMap.R';
      exec("Rscript $script $filedir >>$logfile 2>>$errorfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:010']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 10, $msg);
          return;
        }
      }

      DB::table('SubmitJobs') -> where('jobID', $jobID)
                        -> update(['status'=>'OK']);

      if($email != null){
        $this->sendJobCompMail($email, $jobtitle, $jobID, 0, $msg);
      }

    }

    public function sendJobCompMail($email, $jobtitle, $jobID, $status, $msg){
      if($status==0){
        $user = DB::table('users')->where('email', $email)->first();
        $data = [
          'jobID'=>$jobID,
          'jobtitle'=>$jobtitle
        ];
        Mail::send('emails.jobComplete', $data, function($m) use($user){
          $m->from('noreply@ctglab.nl', "FUMA web application");
          $m->to($user->email, $user->name)->subject("FUMA your job has been completed");
        });
      }else{
        $user = DB::table('users')->where('email', $email)->first();
        $data = [
          'status'=>$status,
          'jobtitle'=>$jobtitle,
          'jobID'=>$jobID,
          'msg'=>$msg
        ];
        Mail::send('emails.jobError', $data, function($m) use($user){
          $m->from('noreply@ctglab.nl', "FUMA web application");
          $m->to($user->email, $user->name)->subject("FUMA an error occured");
        });

    }
  }
}
