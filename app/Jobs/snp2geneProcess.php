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

      // get parameters
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $params = file($filedir."params.txt");
      // $gwasformat = preg_split("/[\t]/", chop($params[3]))[1];
      $leadfile = preg_split("/[\t]/", chop($params[4]))[1];
      if(strcmp($leadfile, "Not given")==0){$leadfile=0;}
      else{$leadfile=1;}
      $addleadSNPs = preg_split("/[\t]/", chop($params[5]))[1];
      $regionfile = preg_split("/[\t]/", chop($params[6]))[1];
      if(strcmp($regionfile, "Not given")==0){$regionfile=0;}
      else{$regionfile=1;}
      $N = preg_split("/[\t]/", chop($params[7]))[1];
      $leadP = preg_split("/[\t]/", chop($params[11]))[1];
      $r2 = preg_split("/[\t]/", chop($params[12]))[1];
      $gwasP = preg_split("/[\t]/", chop($params[13]))[1];
      $pop = preg_split("/[\t]/", chop($params[14]))[1];
      $maf = preg_split("/[\t]/", chop($params[15]))[1];
      $KGSNPs = preg_split("/[\t]/", chop($params[16]))[1];
      $mergeDist = preg_split("/[\t]/", chop($params[17]))[1];
      // $Xchr = $request -> input('Xchr');
      $exMHC = preg_split("/[\t]/", chop($params[8]))[1];
      $extMHC = preg_split("/[\t]/", chop($params[9]))[1];
      $genetype = preg_split("/[\t]/", chop($params[10]))[1];
      $posMap = preg_split("/[\t]/", chop($params[18]))[1];
      $posMapWindow = preg_split("/[\t]/", chop($params[19]))[1];
      $posMapWindowSize = preg_split("/[\t]/", chop($params[20]))[1];
      $posMapAnnot = preg_split("/[\t]/", chop($params[21]))[1];
      $posMapCADDth = preg_split("/[\t]/", chop($params[22]))[1];
      $posMapRDBth = preg_split("/[\t]/", chop($params[23]))[1];
      $posMapChr15 = preg_split("/[\t]/", chop($params[24]))[1];
      $posMapChr15Max = preg_split("/[\t]/", chop($params[25]))[1];
      $posMapChr15Meth = preg_split("/[\t]/", chop($params[26]))[1];
      $eqtlMap = preg_split("/[\t]/", chop($params[27]))[1];
      $eqtlMaptss = preg_split("/[\t]/", chop($params[28]))[1];
      $eqtlMapSigeqtl = preg_split("/[\t]/", chop($params[29]))[1];
      $eqtlMapeqtlP = preg_split("/[\t]/", chop($params[30]))[1];
      $eqtlMapCADDth = preg_split("/[\t]/", chop($params[31]))[1];
      $eqtlMapRDBth = preg_split("/[\t]/", chop($params[32]))[1];
      $eqtlMapChr15 = preg_split("/[\t]/", chop($params[33]))[1];
      $eqtlMapChr15Max = preg_split("/[\t]/", chop($params[34]))[1];
      $eqtlMapChr15Meth = preg_split("/[\t]/", chop($params[35]))[1];


      $logfile = $filedir."job.log";

      $script = storage_path().'/scripts/gwas_file.pl';
      exec("perl $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:001']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 1);
          return;
        }
      }
      $script = storage_path().'/scripts/magma.pl';
      exec("perl $script $filedir $N $pop >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:002']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 2);
          return;
        }
      }

      $script = storage_path().'/scripts/manhattan_filt.py';
      exec("python $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:003']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 3);
          return;
        }
      }

      $script = storage_path().'/scripts/QQSNPs_filt.py';
      exec("python $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:004']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 4);
          return;
        }
      }

      $script = storage_path().'/scripts/getLD.pl';
      // $process = new Proces("/usr/bin/perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC");
      // $process -> start();
      // echo "perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC";
      exec("perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC >>$logfile", $output, $error);
      if($error != 0){
        $NoCandidates = false;
        foreach($outputs as $l){
          if(preg_match("No candidate SNP was identified", $l)==1){
            $NoCandidates = true;
            break;
          }
        }
        if($NoCandidates){
          DB::table('SubmitJobs') -> where('jobID', $jobID)
                            -> update(['status'=>'ERROR:005']);
          if($email!=null){
            $this->sendJobCompMail($email, $jobtitle, $jobID, 5);
            return;
          }
        }else{
          DB::table('SubmitJobs') -> where('jobID', $jobID)
                            -> update(['status'=>'ERROR:006']);
          if($email!=null){
            $this->sendJobCompMail($email, $jobtitle, $jobID, 6);
            return;
          }
        }

      }
      $script = storage_path().'/scripts/SNPannot.R';
      exec("Rscript $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:007']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 7);
          return;
        }
      }
      $script = storage_path().'/scripts/getGWAScatalog.pl';
      exec("perl $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:008']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 8);
          return;
        }
      }

      #$script = storage_path().'/scripts/getExAC.pl';
      #exec("perl $script $filedir");
      if($eqtlMap==1){
        $script = storage_path().'/scripts/geteQTL.pl';
        exec("perl $script $filedir $eqtlMaptss $eqtlMapSigeqtl $eqtlMapeqtlP >>$logfile", $output, $error);
        if($error != 0){
          DB::table('SubmitJobs') -> where('jobID', $jobID)
                            -> update(['status'=>'ERROR:009']);
          if($email!=null){
            $this->sendJobCompMail($email, $jobtitle, $jobID, 9);
            return;
          }
        }
      }

      $script = storage_path().'/scripts/geneMap.R';
      exec("Rscript $script $filedir $genetype $exMHC $extMHC $posMap $posMapWindow $posMapWindowSize $posMapAnnot $posMapCADDth $posMapRDBth $posMapChr15 $posMapChr15Max $posMapChr15Meth $eqtlMap $eqtlMaptss $eqtlMapSigeqtl $eqtlMapeqtlP $eqtlMapCADDth $eqtlMapRDBth $eqtlMapChr15 $eqtlMapChr15Max $eqtlMapChr15Meth >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:010']);
        if($email!=null){
          $this->sendJobCompMail($email, $jobtitle, $jobID, 10);
          return;
        }
      }

      DB::table('SubmitJobs') -> where('jobID', $jobID)
                        -> update(['status'=>'OK']);

      if($email != null){
        $this->sendJobCompMail($email, $jobtitle, $jobID, 0);
      }

    }

    public function sendJobCompMail($email, $jobtitle, $jobID, $status){
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
          'jobtitle'=>$jobtitle
        ];
        Mail::send('emails.jobError', $data, function($m) use($user){
          $m->from('noreply@ctglab.nl', "FUMA web application");
          $m->to($user->email, $user->name)->subject("FUMA an error occured");
        });

    }
  }
}
