<?php

namespace IPGAP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use IPGAP\SubmitJob;
use IPGAP\Http\Requests;
use IPGAP\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use View;
use Storage;
use File;
use JavaScript;
// use Zipper;

class JobController extends Controller
{
    
    public function getJobList($email = '', $limit = 10)
    {
        if( $email){
            $results = SubmitJob::where('email', $email)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }
        else{
            $results = SubmitJob::orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }
        
        return response()->json($results);

    }
    
    public function JobCheck(Request $request){
      $email = $request -> input('Email');
      $jobtitle = $request -> input('jobtitle');

      if(!filter_var($email, FILTER_VALIDATE_EMAIL)===false){
        $results = DB::select('SELECT * FROM SubmitJobs WHERE email=?', [$email]);
        $exists = false;
        foreach($results as $row){
          if($row->title==$jobtitle){
            $exists = true;
            break;
          }
        }
        if($exists){return "2";}
        else{return "1";}
      }else{
        return "3";
      }
    }

    public function getJobID(Request $request){
      if($request -> has('JobQueryEmail')){
        $email = $request -> input('JobQueryEmail');
      }else{
        $email = null;
      }

      if($request -> has('JobQueryTitle')){
        $jobtitle = $request -> input('JobQueryTitle');
      }else{
        $jobtitle = " None";
      }

      $results = DB::select('SELECT * FROM SubmitJobs WHERE email=? AND title=?', [$email, $jobtitle]);
      // $exists = false;
      $jobID = 0;
      foreach($results as $row){
        // if($row->title==$jobtitle){
          // $exists = true;
          $jobID = $row->jobID;
          // break;
        // }
      }

      return redirect("/snp2gene/$jobID");
    }

    public function checkJobStatus(Request $request){
      $jobID = $request->input('jobID');
      $results = DB::select('SELECT * FROM SubmitJobs WHERE jobID=?', [$jobID]);
      if(count($results)==0){
        return "Notfound";
      }else{
        foreach($results as $row){
          $status = $row->status;
          return $status;
        }
      }
    }

    public function getParams(Request $request){
      $jobID = $request->input('jobID');
      $date = date('Y-m-d H:i:s');
      DB::table('SubmitJobs') -> where('jobID', $jobID)
                        -> update(['updated_at'=>$date]);

      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $params = file($filedir."params.txt");
      $posMap = preg_split("/[\t]/", chop($params[18]))[1];
      $eqtlMap = preg_split("/[\t]/", chop($params[27]))[1];
      // get jobID
      // update updated_at date
      // JavaScript::put([
      //   'jobtype'=>'jobquery',
      //   // 'email'=>$email,
      //   'jobID'=>$jobID,
      //   'filedir'=>$filedir,
      //   'posMap'=>$posMap,
      //   'eqtlMap'=>$eqtlMap
      // ]);
#local       #return view('pages.snp2gene', ['jobID'=>$jobID, 'status'=>'jobquery']);
      // return view('pages.snp2gene', ['jobID'=>$jobID,'status'=>'jobquery']);
      // return redirect("/snp2gene/$jobID");
      echo "$filedir:$posMap:$eqtlMap";
    }

    public function newJob(Request $request){
      // check file type
      if(mime_content_type($_FILES["GWASsummary"]["tmp_name"])!="text/plain"){
        $jobID = null;
        return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>'fileFormatGWAS']);
        // return back()->withInput(['status'=> 'fileFormat']); // parameter is not working
      }
      if($request -> hasFile('leadSNPs')){
        if(mime_content_type($_FILES["leadSNPs"]["tmp_name"])!="text/plain"){
          $jobID = null;
          return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>'fileFormatLead']);
        }
      }
      if($request -> hasFile('regions')){
        if(mime_content_type($_FILES["regions"]["tmp_name"])!="text/plain"){
          $jobID = null;
          return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>'fileFormatRegions']);
        }
      }
      session_start();

      $date = date('Y-m-d H:i:s');
      $jobID;
      $filedir;
      if($request->has("NewJobEmail")){
        $email = $request -> input('NewJobEmail');
      }else{
        $email=null;
      }
      if($request->has("NewJobTitle")){
        $jobtitle = $request -> input('NewJobTitle');
      }else{
        $jobtitle="None";
      }

      if($email==null){
        $jobID = DB::select('SELECT COUNT(jobID) as njob FROM SubmitJobs')[0];
        $jobID = $jobID->njob;
        $jobID++;
        DB::table('SubmitJobs') -> insert(['jobID'=>$jobID, 'email'=>'Not Given', 'title'=>$jobtitle,
                                      'created_at'=>$date, 'updated_at'=>$date, 'status'=>"NEW"]);
        $filedir = config('app.jobdir').'/jobs/'.$jobID;

        File::makeDirectory($filedir, 0775, true);
      }else{
        $results = DB::select('SELECT * FROM SubmitJobs WHERE email=?', [$email]);
        $exists = false;
        foreach($results as $row){
          if($row->title==$jobtitle){
            $exists = true;
            $jobID = $row->jobID;
            break;
          }
        }

        if($exists){
          $filedir = config('app.jobdir').'/jobs/'.$jobID;
          File::cleanDirectory($filedir);
          DB::table('SubmitJobs') -> where('jobID', $jobID)
                            -> update(['created_at'=>$date, 'updated_at'=>$date, 'status'=>'NEW']);
        }else{
          $jobID = DB::select('SELECT COUNT(jobID) as njob FROM SubmitJobs')[0];
          $jobID = $jobID->njob;
          $jobID++;
          $filedir = config('app.jobdir').'/jobs/'.$jobID;
          File::makeDirectory($filedir);
          DB::table('SubmitJobs') -> insert(['jobID'=>$jobID, 'email'=>$email, 'title'=>$jobtitle,
                                        'created_at'=>$date, 'updated_at'=>$date, 'status'=>'NEW']);
        }
      }
      $_SESSION['snp2gene'] = $jobID;
      $_SESSION['gene2func'] = $jobID;
      print_r ($_SESSION);
      // $sessionID = $request->session()->get('key');

      // upload input Filesystem
      $leadSNPs = "input.lead";
      $GWAS = "input.gwas";
      $regions = "input.regions";
      $leadSNPsfileup = 0;
      $GWASfileup = 0;
      $regionsfileup = 0;
      // $gwasformat = $request->input('gwasformat'); //removed this option
      $gwasformat = "Plain";

      if($request -> has('addleadSNPs')){$addleadSNPs=1;}
      else{$addleadSNPs=0;}
      if($request -> hasFile('GWASsummary')){
        $request -> file('GWASsummary')->move($filedir, $GWAS);
        $GWASfileup = 1;
      }
      if($request -> hasFile('leadSNPs')){
        $request -> file('leadSNPs')->move($filedir, $leadSNPs);
        $leadSNPsfileup = 1;
      }
      if($request -> hasFile('regions')){
        $request -> file('regions')->move($filedir, $regions);
        $regionsfileup = 1;
      }

      // get parameters
      // MHC region
      if($request -> has('MHCregion')){$exMHC=1;}
      else{$exMHC=0;}
      $extMHC = $request -> input('extMHCregion');
      if($extMHC==null){$extMHC="NA";}
      // gene type
      $genetype = implode(":", $request -> input('genetype'));
      // others
      $N = $request -> input('N');
      $leadP = $request -> input('leadP');
      $r2 = $request -> input('r2');
      $gwasP = $request -> input('gwasP');
      $pop = $request -> input('pop');
      $KGSNPs = $request -> input('KGSNPs');
      if(strcmp($KGSNPs, "Yes")==0){$KGSNPs=1;}
      else{$KGSNPs=0;}
      $maf = $request -> input('maf');
      $mergeDist = $request -> input('mergeDist');
      // $Xchr = $request -> input('Xchr');
      // if(strcmp($Xchr, "Yes")==0){$Xchr=1;}
      // else{$Xchr=0;}
      if($request -> has('posMap')){$posMap=1;}
      else{$posMap=0;}
      if($request -> has('windowCheck')){
        $posMapWindow=1;
        $posMapAnnot="NA";
      }else{
        $posMapWindow=0;
        $posMapAnnot=implode(":",$request -> input('posMapAnnot'));
      }
      $posMapWindowSize = $request -> input('posMapWindow');
      if($request -> has('posMapCADDcheck')){
        $posMapCADDth = $request -> input('posMapCADDth');
      }else{
        $posMapCADDth = 0;
      }
      if($request -> has('posMapRDBcheck')){
        $posMapRDBth = $request -> input('posMapRDBth');
      }else{
        $posMapRDBth = "NA";
      }

      if($request -> has('posMapChr15check')){
        $posMapChr15Ts = $request -> input('posMapChr15Ts');
        $posMapChr15Gts = $request -> input('posMapChr15Gts');
        if(!empty($posMapChr15Ts) && !empty($posMapChr15Gts)){
          $posMapChr15Ts = implode(":", $posMapChr15Ts);
          $posMapChr15Gts = implode(":", $posMapChr15Gts);
          $posMapChr15 = implode(":", array($posMapChr15Ts, $posMapChr15Gts));
        }else if(!empty($posMapChr15Ts)){
          $posMapChr15 = implode(":", $posMapChr15Ts);
        }else{
          $posMapChr15 = implode(":", $posMapChr15Gts);
        }
        $posMapChr15Max = $request -> input('posMapChr15Max');
        $posMapChr15Meth = $request -> input('posMapChr15Meth');
      }else{
        $posMapChr15 = "NA";
        $posMapChr15Max = "NA";
        $posMapChr15Meth = "NA";
      }

      if($request -> has('eqtlMap')){
        $eqtlMap=1;
        $eqtlMapTs = $request -> input('eqtlMapTs');
        $eqtlMapGts = $request -> input('eqtlMapGts');
        if(!empty($eqtlMapTs) && !empty($eqtlMapGts)){
          $eqtlMapTs = implode(":", $eqtlMapTs);
          $eqtlMapGts = implode(":", $eqtlMapGts);
          $eqtlMaptss = implode(":", array($eqtlMapTs, $eqtlMapGts));
        }else if(!empty($eqtlMapTs)){
          $eqtlMaptss = implode(":", $eqtlMapTs);
        }else{
          $eqtlMaptss = implode(":", $eqtlMapGts);
        }
      }else{
        $eqtlMap=0;
        $eqtlMaptss = "NA";
      }
      if($request -> has('sigeqtlCheck')){
        $sigeqtl = 1;
        $eqtlP = 1;
      }else{
        $sigeqtl = 0;
        $eqtlP = $request -> input('eqtlP');
      }

      if($request -> has('eqtlMapCADDcheck')){
        $eqtlMapCADDth = $request -> input('eqtlMapCADDth');
      }else{
        $eqtlMapCADDth = 0;
      }
      if($request -> has('eqtlMapRDBcheck')){
        $eqtlMapRDBth = $request -> input('eqtlMapRDBth');
      }else{
        $eqtlMapRDBth = "NA";
      }
      if($request -> has('eqtlMapChr15check')){
        $eqtlMapChr15Ts = $request -> input('eqtlMapChr15Ts');
        $eqtlMapChr15Gts = $request -> input('eqtlMapChr15Gts');
        if(!empty($eqtlMapChr15Ts) && !empty($eqtlMapChr15Gts)){
          $eqtlMapChr15Ts = implode(":", $eqtlMapChr15Ts);
          $eqtlMapChr15Gts = implode(":", $eqtlMapChr15Gts);
          $eqtlMapChr15 = implode(":", array($eqtlMapChr15Ts, $eqtlMapChr15Gts));
        }else if(!empty($eqtlMapChr15Ts)){
          $eqtlMapChr15 = implode(":", $eqtlMapChr15Ts);
        }else{
          $eqtlMapChr15 = implode(":", $eqtlMapChr15Gts);
        }
        $eqtlMapChr15Max = $request -> input('eqtlMapChr15Max');
        $eqtlMapChr15Meth = $request -> input('eqtlMapChr15Meth');
      }else{
        $eqtlMapChr15 = "NA";
        $eqtlMapChr15Max = "NA";
        $eqtlMapChr15Meth = "NA";
      }
      // write parameter into a file
      $paramfile = $filedir.'/params.txt';
      File::put($paramfile, "Job created\t$date\n");
      File::append($paramfile, "Job title\t$jobtitle\n");
      if($GWASfileup==1){File::append($paramfile, "input GWAS summary statistics file\t".$_FILES["GWASsummary"]["name"]."\n");}
      else{File::append($paramfile, "input GWAS summary statistics file\tNot given\n");}
      File::append($paramfile, "GWAS summary statistics file format\t$gwasformat\n");
      if($leadSNPsfileup==1){File::append($paramfile, "input lead SNPs file\t".$_FILES["leadSNPs"]["name"]."\n");}
      else{File::append($paramfile, "input lead SNPs file\tNot given\n");}
      File::append($paramfile, "Identify additional lead SNPs\t$addleadSNPs\n");
      if($regionsfileup==1){File::append($paramfile, "input genetic regions file\t".$_FILES["regions"]["name"]."\n");}
      else{File::append($paramfile, "input genetic regions file\tNot given\n");}
      File::append($paramfile, "sample size\t$N\n");
      File::append($paramfile, "exclude MHC\t$exMHC\n");
      File::append($paramfile, "extended MHC region\t$extMHC\n");
      // File::append($paramfile, "include chromosome X\t$Xchr\n");
      File::append($paramfile, "gene type\t$genetype\n");
      File::append($paramfile, "lead SNP P-value\t$leadP\n");
      File::append($paramfile, "r2\t$r2\n");
      File::append($paramfile, "GWAS tagged SNPs P-value\t$gwasP\n");
      File::append($paramfile, "Population\t$pop\n");
      File::append($paramfile, "MAF\t$maf\n");
      File::append($paramfile, "Include 1000G SNPs\t$KGSNPs\n");
      File::append($paramfile, "Interval merge max distance\t$mergeDist\n");
      File::append($paramfile, "Positional mapping\t$posMap\n");
      File::append($paramfile, "posMap Window based\t$posMapWindow\n");
      File::append($paramfile, "posMap Window size\t$posMapWindowSize\n");
      File::append($paramfile, "posMap Annotation based\t$posMapAnnot\n");
      File::append($paramfile, "posMap min CADD\t$posMapCADDth\n");
      File::append($paramfile, "posMap min RegulomeDB\t$posMapRDBth\n");
      File::append($paramfile, "posMap chromatin state filterinf tissues\t$posMapChr15\n");
      File::append($paramfile, "posMap max chromatin state\t$posMapChr15Max\n");
      File::append($paramfile, "posMap chromatin state filtering method\t$posMapChr15Meth\n");
      File::append($paramfile, "eQTL mapping\t$eqtlMap\n");
      File::append($paramfile, "eqtlMap tissues\t$eqtlMaptss\n");
      File::append($paramfile, "eqtlMap significant only\t$sigeqtl\n");
      File::append($paramfile, "eqtlMap P-value\t$eqtlP\n");
      File::append($paramfile, "eqtlMap min CADD\t$eqtlMapCADDth\n");
      File::append($paramfile, "eqtlMap min RegulomeDB\t$eqtlMapRDBth\n");
      File::append($paramfile, "eqtlMap chromatin state filterinf tissues\t$eqtlMapChr15\n");
      File::append($paramfile, "eqtlMap max chromatin state\t$eqtlMapChr15Max\n");
      File::append($paramfile, "eqtlMap chromatin state filtering method\t$eqtlMapChr15Meth\n");

      // JavaScript::put([
      //   'jobtype'=>'newjob',
      //   'email'=>$email,
      //   'jobtitle'=>$jobtitle,
      //   'jobID'=>$jobID,
      //   'filedir'=>$filedir.'/',
      //   'leadSNPsfileup'=>$leadSNPsfileup,
      //   'regionsfileup'=>$regionsfileup,
      //   'gwasformat'=>$gwasformat,
      //   'addleadSNPs'=>$addleadSNPs,
      //   'N' => $N,
      //   'leadP'=>$leadP,
      //   'r2'=>$r2,
      //   'gwasP'=>$gwasP,
      //   'pop'=>$pop,
      //   'KGSNPs'=>$KGSNPs,
      //   'maf'=>$maf,
      //   'mergeDist'=>$mergeDist,
      //   // 'Xchr' => $Xchr,
      //   'exMHC'=>$exMHC,
      //   'extMHC'=>$extMHC,
      //   'genetype'=>$genetype,
      //   'posMap'=>$posMap,
      //   'posMapWindow'=>$posMapWindow,
      //   'posMapWindowSize'=>$posMapWindowSize,
      //   'posMapAnnot'=>$posMapAnnot,
      //   'posMapCADDth'=>$posMapCADDth,
      //   'posMapRDBth'=>$posMapRDBth,
      //   'posMapChr15'=>$posMapChr15,
      //   'posMapChr15Max'=>$posMapChr15Max,
      //   'posMapChr15Meth'=>$posMapChr15Meth,
      //   'eqtlMap'=>$eqtlMap,
      //   'eqtlMaptss'=>$eqtlMaptss,
      //   'eqtlMapSigeqtl'=>$sigeqtl,
      //   'eqtlMapeqtlP'=>$eqtlP,
      //   'eqtlMapCADDth'=>$eqtlMapCADDth,
      //   'eqtlMapRDBth'=>$eqtlMapRDBth,
      //   'eqtlMapChr15'=>$eqtlMapChr15,
      //   'eqtlMapChr15Max'=>$eqtlMapChr15Max,
      //   'eqtlMapChr15Meth'=>$eqtlMapChr15Meth
      // ]);

#local       // return view('pages.snp2gene', ['jobID'=>$jobID, 'status'=>'newjob']);
      # return view('pages.snp2gene', ['jobID'=>$jobID,'status'=>'newjob']);
      return redirect("/snp2gene/$jobID");
    }

    public function CandidateSelection(Request $request){
      // $args = $request->all();
      // $args = json_encode($args);
      // $script = storage_path().'/scripts/snp2gene.py';
      // exec("python $script $args");
      $jobID = $request->input('jobID');
      // $email = $request -> input('email');
      // $jobtitle = $request -> input('jobtitle');
      // $filedir = $request -> input('filedir');

      $results = DB::select('SELECT * FROM SubmitJobs WHERE jobID=?', [$jobID]);
      $email = $results[0]->email;
      if(strcmp($email, "Not Given")==0){$email==null;}
      $jobtitle = $results[0]->title;

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
      DB::table('SubmitJobs') -> where('jobID', $jobID)
                        -> update(['status'=>'RUNNING']);


      $script = storage_path().'/scripts/gwas_file.pl';
      exec("perl $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:001']);
        if($email!=null){
          $this->sendJobCommpMail($email, $jobtitle, $jobID, 1);
          return;
        }
      }
      $script = storage_path().'/scripts/magma.pl';
      exec("perl $script $filedir $N $pop >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:002']);
        if($email!=null){
          $this->sendJobCommpMail($email, $jobtitle, $jobID, 2);
          return;
        }
      }

      $script = storage_path().'/scripts/manhattan_filt.py';
      exec("python $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:003']);
        if($email!=null){
          $this->sendJobCommpMail($email, $jobtitle, $jobID, 3);
          return;
        }
      }

      $script = storage_path().'/scripts/QQSNPs_filt.py';
      exec("python $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:004']);
        if($email!=null){
          $this->sendJobCommpMail($email, $jobtitle, $jobID, 4);
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
            $this->sendJobCommpMail($email, $jobtitle, $jobID, 5);
            return;
          }
        }else{
          DB::table('SubmitJobs') -> where('jobID', $jobID)
                            -> update(['status'=>'ERROR:006']);
          if($email!=null){
            $this->sendJobCommpMail($email, $jobtitle, $jobID, 6);
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
          $this->sendJobCommpMail($email, $jobtitle, $jobID, 7);
          return;
        }
      }
      $script = storage_path().'/scripts/getGWAScatalog.pl';
      exec("perl $script $filedir >>$logfile", $output, $error);
      if($error != 0){
        DB::table('SubmitJobs') -> where('jobID', $jobID)
                          -> update(['status'=>'ERROR:008']);
        if($email!=null){
          $this->sendJobCommpMail($email, $jobtitle, $jobID, 8);
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
            $this->sendJobCommpMail($email, $jobtitle, $jobID, 9);
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
          $this->sendJobCommpMail($email, $jobtitle, $jobID, 10);
          return;
        }
      }

      DB::table('SubmitJobs') -> where('jobID', $jobID)
                        -> update(['status'=>'OK']);

      if($email != null){
        $this->sendJobCompMail($email, $jobtitle, $jobID, 0);
      }
      return;
    }

    public function annotPlot(Request $request){
      $jobID = "test";
      $jobID = $request -> input('jobID');
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $type=null;
      $rowI=null;
      if($request -> has('annotPlotSelect_leadSNP')){
        $type = "leadSNP";
        $rowI = $request -> input('annotPlotSelect_leadSNP');
      }else if($request -> has('annotPlotSelect_interval')){
        $type = "interval";
        $rowI = $request -> input('annotPlotSelect_interval');
      }
#local       file_put_contents("/media/sf_Documents/VU/Data/WebApp/test.txt", "$type $rowI");

      $GWAS=0;
      $CADD=0;
      $RDB=0;
      $Chr15=0;
      $eqtl=0;
      if($request -> has('annotPlot_GWASp')){$GWAS=1;}
      if($request -> has('annotPlot_CADD')){$CADD=1;}
      if($request -> has('annotPlot_RDB')){$RDB=1;}
      if($request -> has('annotPlot_Chrom15')){
        $Chr15=1;
        $Chr15Ts = $request -> input('annotPlotChr15Ts');
        $Chr15Gts = $request -> input('annotPlotChr15Gts');
        if(!empty($Chr15Ts) && !empty($Chr15Gts)){
          $Chr15Ts = implode(":", $Chr15Ts);
          $Chr15Gts = implode(":", $Chr15Gts);
          $Chr15cells = implode(":", array($Chr15Ts, $Chr15Gts));
        }else if(!empty($Chr15Ts)){
          $Chr15cells = implode(":", $Chr15Ts);
        }else{
          $Chr15cells = implode(":", $Chr15Gts);
        }
      }else{
        $Chr15cells="NA";
      }
      if($request -> has('annotPlot_eqtl')){$eqtl=1;}

      $script = storage_path()."/scripts/annotPlot.R";
      exec("Rscript $script $filedir $type $rowI $GWAS $CADD $RDB $eqtl $Chr15 $Chr15cells");

      if($Chr15==1){
        $script = storage_path()."/scripts/getChr15.pl";
        exec("$script $filedir $Chr15cells");
      }

      $eqtlplot = 0;
      $eqtlNgenes = 0;
      if($eqtl==1){
        $eqtlfile = fopen($filedir."eqtlplot.txt", 'r');
        $eqtlcheck = 0;
        fgetcsv($eqtlfile, 0, "\t");
        $eqtlgenes = array();
        while($row = fgetcsv($eqtlfile, 0, "\t")){
          $eqtlgenes[] = $row[0];
        }
        if(empty($eqtlgenes)){$eqtplot=0;}
        else{$eqtlplot=1;}
        $eqtlgenes = array_unique($eqtlgenes);
        $eqtlNgenes = count($eqtlgenes);
      }

      $xmin=null;
      $xmax=null;
      $chr=null;
      $f = $filedir."annotPlot.txt";
      $file = fopen($f, 'r');
      fgetcsv($file, 0, "\t");
      while($row=fgetcsv($file, 0, "\t")){
        if($chr==null){$chr=$row[1];}
        if($xmin==null){
          $xmin=$row[2];
          $xmax=$row[2];
        }
        if($row[2]>$xmax){$xmax=$row[2];}
      }
      fclose($file);
      $xmin_init=$xmin;
      $xmax_init=$xmax;

      $f = $filedir."genesplot.txt";
      $file = fopen($f, 'r');
      fgetcsv($file, 0, "\t");
      while($row=fgetcsv($file, 0, "\t")){
        if($xmin>$row[2]){
          $xmin=$row[2];
        }
        if($row[3]>$xmax){$xmax=$row[3];}
      }
      fclose($file);

      JavaScript::put([
        'jobID'=>$jobID,
        'filedir'=>$filedir,
        'type'=>$type,
        'rowI'=>$rowI,
        'chr'=>$chr,
        'GWASplot'=>$GWAS,
        'CADDplot'=>$CADD,
        'RDBplot'=>$RDB,
        'Chr15'=>$Chr15,
        'Chr15cells'=>$Chr15cells,
        'eqtl'=>$eqtl,
        'eqtlplot'=>$eqtlplot,
        'eqtlNgenes'=>$eqtlNgenes,
        'xMin'=>$xmin,
        'xMax'=>$xmax,
        'xMin_init'=>$xmin_init,
        'xMax_init'=>$xmax_init
      ]);
      return view('pages.annotPlot', ['jobID'=>$jobID]);
    }

    public function filedown(Request $request){
      $jobID = $request -> input('jobID');
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      // $zip = new ZipArchive();
      $files = array();
      if($request -> has('paramfile')){ $files[] = $filedir."params.txt";}
      if($request -> has('leadfile')){$files[] = $filedir."leadSNPs.txt";}
      if($request -> has('intervalfile')){$files[] = $filedir."intervals.txt";}
      if($request -> has('snpsfile')){$files[] = $filedir."snps.txt"; $files[] = $filedir."ld.txt";}
      if($request -> has('annovfile')){$files[] = $filedir."annov.txt";}
      if($request -> has('annotfile')){$files[] = $filedir."annot.txt";}
      if($request -> has('genefile')){$files[] = $filedir."genes.txt";}
      if($request -> has('eqtlfile')){$files[] = $filedir."eqtl.txt";}
      // if($request -> has('exacfile')){$files[] = $filedir."ExAC.txt";}
      if($request -> has('gwascatfile')){$files[] = $filedir."gwascatalog.txt";}
      $zip = new \ZipArchive();
      $zipfile = $filedir."IPGAP.zip";

      if(File::exists($zipfile)){
        File::delete($zipfile);
      }
      // Zipper::make($zipfile)->add($files);
      // sleep(5);
      $zip -> open($zipfile, \ZipArchive::CREATE);
      foreach($files as $f){
        $zip->addFile($f);
      }
      $zip -> close();
      return response() -> download($zipfile);
    }

    public function gene2funcSubmit(Request $request){
      $id = uniqid();
      $filedir = config('app.jobdir').'/jobs/'.$id.'/';
      File::makeDirectory($filedir);
      $filedir = $filedir.'/';
      #$id = "gene2func";
      #$filedir = storage_path().'/jobs/gene2func/';

      if($request -> has('genes')){
        $gtype = "text";
        $gval = $request -> input('genes');
        $gval = preg_split('/[\n\r]+/', $gval);
        $gval = implode(':', $gval);
        // file_put_contents('/media/sf_Documents/VU/Data/WebApp/test.txt', $gval);
      }else{
        $gtype = "file";
        $gval = "genesQuery.txt";
        $request -> file('genesfile')->move($filedir, "genesQuery.txt");
      }

      if($request -> has('genetype')){
        $bkgtype = "select";
        $bkgval = $request -> input('genetype');
        $bkgval = implode(':', $bkgval);
      }else if($request -> has('bkgenes')){
        $bkgtype = "text";
        $bkgval = $request -> input('bkgenes');
        $bkgval = preg_split('/[\n\r]+/', $bkgval);
        $bkgval = implode(':', $bkgval);
      }else{
        $bkgtype ="file";
        $bkgval = "bkgenes.txt";
        $request -> file('bkgenesfile') -> move($filedir, "bkgenes.txt");
      }

      // if($request -> has('Xchr')){
      //   $Xchr = 1;
      // }else{
      //   $Xchr = 0;
      // }

      if($request -> has('MHC')){
        $MHC = 1;
      }else{
        $MHC = 0;
      }
      // echo "<p>gtype: $gtype<br/>gval: $gval<br/>bkgtype: $bkgtype<br/>bkgval: $bkgval</p>";

      $adjPmeth = $request -> input('adjPmeth');
      $adjPcut = $request -> input('adjPcut');
      $minOverlap = $request -> input('minOverlap');

      JavaScript::put([
        'id' => $id,
        'filedir' => $filedir,
        'gtype' => $gtype,
        'gval' => $gval,
        'bkgtype' => $bkgtype,
        'bkgval' => $bkgval,
        // 'Xchr' => $Xchr,
        'MHC' => $MHC,
        'adjPmeth' => $adjPmeth,
        'adjPcut' => $adjPcut,
        'minOverlap' => $minOverlap
      ]);

      return view('pages.gene2func', ['status'=>'query', 'id'=>'gene2func']);
    }

    public function geneQuery(Request $request){
      $filedir = $request -> input('filedir');
      $gtype = $request -> input('gtype');
      $gval = $request -> input('gval');
      $bkgtype = $request -> input('bkgtype');
      $bkgval = $request -> input('bkgval');
      // $Xchr = $request -> input('Xchr');
      $MHC = $request -> input('MHC');
      $adjPmeth = $request -> input('adjPmeth');
      $adjPcut = $request -> input('adjPcut');
      $minOverlap = $request -> input('minOverlap');

      $script = storage_path()."/scripts/gene2func.R";
      exec("Rscript $script $filedir $gtype $gval $bkgtype $bkgval $MHC");
      $script = storage_path()."/scripts/GeneSet.py";
      exec("$script $filedir $gtype $gval $bkgtype $bkgval $MHC $adjPmeth $adjPcut $minOverlap");
    }

    public function snp2geneGeneQuery(Request $request){
      $jobID = $request -> input('jobID');
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $gtype="text";
      $bkgtype="select";

      $params = file($filedir."params.txt");
      // $Xchr = preg_split("/[\t]/", chop($params[9]))[1];
      $MHC = preg_split("/[\t]/", chop($params[8]))[1];
      $bkgval = preg_split("/[\t]/", chop($params[10]))[1];
      $adjPmeth = "fdr_bh";
      $adjPcut = 0.05;
      $minOverlap = 2;

      $gval = null;
      $f = fopen($filedir."genes.txt", 'r');
      fgetcsv($f, 0, "\t");
      while($row = fgetcsv($f, 0, "\t")){
        if($gval==null){
          $gval = $row[0];
        }else{
          $gval = $gval.":".$row[0];
        }
      }

      JavaScript::put([
        'id' => $jobID,
        'filedir' => $filedir,
        'gtype' => $gtype,
        'gval' => $gval,
        'bkgtype' => $bkgtype,
        'bkgval' => $bkgval,
        // 'Xchr' => $Xchr,
        'MHC' => $MHC,
        'adjPmeth' => $adjPmeth,
        'adjPcut' => $adjPcut,
        'minOverlap' => $minOverlap
      ]);

       return view('pages.gene2func', ['status'=>'query', 'id'=>$jobID]);

    }

    public function SelectOption(Request $request){
      $type = $request -> input('type');
      $domain = $request -> input('domain');
      $chapter = $request -> input('chapter');
      $subchapter = $request -> input('subchapter');

      if(strcmp($type, 'Domain')==0){
        if($domain==null){
          $Domain = [];
          $results = DB::select('SELECT * FROM gwasDB');
          foreach($results as $row){
            $d = $row->Domain;
            if(array_key_exists($d, $Domain)){
              $Domain[$d] += 1;
            }else{
              $Domain[$d] = 1;
            }
          }
          // $keys = array_keys($Domain);
          // $json = "";
          // foreach($keys as $k){
          //   if(strcmp($json, "")==0){
          //     $json = '{0:"'.$k.'", 1:'.$Domain[$k].'}';
          //   }else{
          //     $json .= ',{0:"'.$k.'", 1:'.$Domain[$k].'}';
          //   }
          // }
          // $json = '{Domain:['.$json.']}';
          $json = array("Domain" => $Domain);
          // $json = json_encode($json);
          echo json_encode($json);
        }else{
          $results = DB::select('SELECT * FROM gwasDB WHERE Domain=?', [$domain]);
          $Chapter = [];
          $Subchapter = [];
          $Trait = [];
          foreach($results as $row){
            $c = $row->ChapterLevel;
            $s = $row->SubchapterLevel;
            $t = $row->Trait;
            if(array_key_exists($c, $Chapter)){
              $Chapter[$c] += 1;
            }else{
              $Chapter[$c] = 1;
            }
            if(array_key_exists($s, $Subchapter)){
              $Subchapter[$s] += 1;
            }else{
              $Subchapter[$s] = 1;
            }
            if(array_key_exists($t, $Trait)){
              $Trait[$t] += 1;
            }else{
              $Trait[$t] = 1;
            }
          }
          $json = array("Chapter"=>$Chapter, "Subchapter"=>$Subchapter, "Trait"=>$Trait);
          echo json_encode($json);
        }

      }else if(strcmp($type, 'Chapter')==0){
        if($chapter==null){
          $results = DB::select('SELECT * FROM gwasDB WHERE Domain=?', [$domain]);
          $Chapter = [];
          $Subchapter = [];
          $Trait = [];
          foreach($results as $row){
            $c = $row->ChapterLevel;
            $s = $row->SubchapterLevel;
            $t = $row->Trait;
            if(array_key_exists($c, $Chapter)){
              $Chapter[$c] += 1;
            }else{
              $Chapter[$c] = 1;
            }
            if(array_key_exists($s, $Subchapter)){
              $Subchapter[$s] += 1;
            }else{
              $Subchapter[$s] = 1;
            }
            if(array_key_exists($t, $Trait)){
              $Trait[$t] += 1;
            }else{
              $Trait[$t] = 1;
            }
          }
          $json = array("Chapter"=>$Chapter, "Subchapter"=>$Subchapter, "Trait"=>$Trait);
          echo json_encode($json);
        }else{
          $results = DB::select('SELECT * FROM gwasDB WHERE Domain=? AND ChapterLevel=?', [$domain, $chapter]);
          $Subchapter = [];
          $Trait = [];
          foreach($results as $row){
            $s = $row->SubchapterLevel;
            $t = $row->Trait;
            if(array_key_exists($s, $Subchapter)){
              $Subchapter[$s] += 1;
            }else{
              $Subchapter[$s] = 1;
            }
            if(array_key_exists($t, $Trait)){
              $Trait[$t] += 1;
            }else{
              $Trait[$t] = 1;
            }
          }
          $json = array("Subchapter"=>$Subchapter, "Trait"=>$Trait);
          echo json_encode($json);
        }
      }else if(strcmp($type, 'Subchapter')==0){
        if($chapter==null && $subchapter==null){
          $results = DB::select('SELECT * FROM gwasDB WHERE Domain=?', [$domain]);
          $Chapter = [];
          $Subchapter = [];
          $Trait = [];
          foreach($results as $row){
            $c = $row->ChapterLevel;
            $s = $row->SubchapterLevel;
            $t = $row->Trait;
            if(array_key_exists($c, $Chapter)){
              $Chapter[$c] += 1;
            }else{
              $Chapter[$c] = 1;
            }
            if(array_key_exists($s, $Subchapter)){
              $Subchapter[$s] += 1;
            }else{
              $Subchapter[$s] = 1;
            }
            if(array_key_exists($t, $Trait)){
              $Trait[$t] += 1;
            }else{
              $Trait[$t] = 1;
            }
          }
          $json = array("Chapter"=>$Chapter, "Subchapter"=>$Subchapter, "Trait"=>$Trait);
          echo json_encode($json);
        }
        else if($subchapter==null){
          $results = DB::select('SELECT * FROM gwasDB WHERE Domain=? AND ChapterLevel=?', [$domain, $chapter]);
          $Subchapter = [];
          $Trait = [];
          foreach($results as $row){
            $s = $row->SubchapterLevel;
            $t = $row->Trait;
            if(array_key_exists($s, $Subchapter)){
              $Subchapter[$s] += 1;
            }else{
              $Subchapter[$s] = 1;
            }
            if(array_key_exists($t, $Trait)){
              $Trait[$t] += 1;
            }else{
              $Trait[$t] = 1;
            }
          }
          $json = array("Subchapter"=>$Subchapter, "Trait"=>$Trait);
          echo json_encode($json);
        }else{
          $results = DB::select('SELECT * FROM gwasDB WHERE Domain=? AND SubchapterLevel=?', [$domain, $subchapter]);
          $Trait = [];
          foreach($results as $row){
            $t = $row->Trait;
            if(array_key_exists($t, $Trait)){
              $Trait[$t] += 1;
            }else{
              $Trait[$t] = 1;
            }
          }
          $json = array("Trait"=>$Trait);
          echo json_encode($json);
        }
      }else{

      }

    }

    public function selectTable(Request $request){
      $type = $request -> input('type');
      $domain = $request -> input('domain');
      $chapter = $request -> input('chapter');
      $subchapter = $request -> input('subchapter');
      $trait = $request -> input('trait');

      if(strcmp($domain, "null")==0 || (strcmp($domain, "null")==0 && strcmp($chapter, "null")==0 && strcmp($subchapter, "null")==0 && strcmp($trait, "null")==0)){
        echo '{"data":[]}';
      }else{
        $query = 'SELECT ID,PMID,Year,Domain,ChapterLevel,SubchapterLevel,Trait,Ncase,Ncontrol,N,Population,SNPh2,website FROM gwasDB WHERE';
        $head = ['ID','PMID','Year','Domain','ChapterLevel','SubchapterLevel','Trait','Ncase','Ncontrol','N','Population','SNPh2','website'];
        $val = [];
        if(strcmp($domain, "null")!=0){
          $query .= ' Domain=?';
          $val[] = $domain;
        }
        if(strcmp($chapter, "null")!=0 && strcmp($type, "Domain")!=0){
          if(count($val)!=0){$query .= " AND";}
          $query .= ' ChapterLevel=?';
          $val[] = $chapter;
        }
        if(strcmp($subchapter, "null")!=0 && strcmp($type, "Domain")!=0 && strcmp($type, "Chapter")!=0){
          if(count($val)!=0){$query .= " AND";}
          $query .= ' SubchapterLevel=?';
          $val[] = $subchapter;
        }
        if(strcmp($trait, "null")!=0 && strcmp($type, "Domain")!=0 && strcmp($type, "Chapter")!=0 && strcmp($type, "Subchapter")!=0){
          if(count($val)!=0){$query .= " AND";}
          $query .= ' Trait=?';
          $val[] = $trait;
        }
        $results = DB::select($query, $val);
        $results = json_decode(json_encode($results), true);
        $all_row = array();
        foreach($results as $row){
          $all_row[] = array_combine($head, $row);
        }
        $json = array('data'=>$all_row);
        echo json_encode($json);
      }
    }

    public function gwasDBtable(Request $request){
      $dbName = $request -> input('dbName');
      $head = DB::getSchemaBuilder()->getColumnListing('gwasDB');
      $head[8] = "ChapterLevel";
      $head[9] = "SubchapterLevel";
      $head[11] = "Nsample";
      $head[15] = "SNPh2";
      $results = DB::select('SELECT * FROM gwasDB WHERE dbName=?', [$dbName]);
      $rows = json_decode(json_encode($results), true);
      #$results = $results->toArray();
      // $f = fopen()
      // file_put_contents("/media/sf_Documents/VU/Data/WebApp/test.txt", implode(":",$rows[0]));
      $all_row = array();
      $all_row[] = array_combine($head, $rows[0]);
      $json = array('data'=>$all_row);
#local       // file_put_contents("/media/sf_Documents/VU/Data/WebApp/test.txt", json_encode($all_row));#local
      // foreach($results as $row){
      //   if($row->title==$jobtitle){
      //     $exists = true;
      //     $jobID = $row->jobID;
      //     break;
      //   }
      // }
      // file_put_contents("/media/sf_Documents/VU/Data/WebApp/test.txt", json_encode($all_row));

      // $file = fopen("/media/sf_Documents/VU/Data/gwasDB/old.txt", 'r');
      // $head = fgetcsv($file, 0, "\t");
      // $all_row = array();
      // while($row = fgetcsv($file, 0, "\t")){
      //   $all_row[] = array_combine($head, $row);
      // }
      // $json = array('data'=>$all_row);
      echo json_encode($json);
    }

    public function gene2funcFileDown(Request $request){
      $file = $request -> input('file');
      $id = $request -> input('id');
      $filedir = config('app.jobdir').'/jobs/'.$if.'/'.$file;
      return response() -> download($filedir);
    }

    public function sendJobCompMail($email, $jobtitle, $jobID, $status){
      if($status==0){
        $subject = "GWAS ATLAS job has been completed";
        $message = "
        <html>
        <head><h3>GWAS ATLAS job has been completed!!</h3></head>
        <body>
        Your job has been completed.<br/>
        Pleas follow the link to go to the results page.<br/>
        <a href=".'"http://ctg.labs.vu.nl/IPGAP/snp2gene/'.$jobID.'"'.">SNP2GENE job query</a><br/>
        <br/>
        <h4>Job summary</h4>
        your email: ".$email."<br/>
        your job title: ".$jobtitle."<br/>
        You can also use those information to qury your results.<br/>
        <br/>
        Please do not hesitate to contact us for any questions/suggestions.<br/><br/>
        Kyoko Watanabe<br/>
        VU University Amsterdam<br/>
        Dept. Complex Trait Genetics<br/>
        De Boelelaan 1085 WN-B628 1018HV Amsterdam The Netherlands<be/>
        k.watanabe@vu.nl<br/>
        </body>
        </html>
        ";
      }else{
        $subject = "GWAS ATLAS job, ERROR";
        $message = "
        <html>
        <head><h3>An error occured fruing GWAS ATLAS job</h3></head>
        <body>
        There was a error occured during the process.<br/>
        ERROR: ".$status;
        if($status==1){
          $message .= ' (Not enough columns are provided in GWAS summary statistics file)<br/>
            Please make sure your input file have sufficient column names.
            Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
        }else if($status==2){
          $message .= ' (Error from MAGMA)<br/>
            This error might be because of the rsID and/or p-value columns are wrongly labeled.
            Please make sure your input file have sufficient column names.
            Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
        }else if($status==3 | $status==4){
          $message .= ' (Error during SNPs filtering for manhattan plot)<br/>
            This error might be because of the p-value column is wrongly labeled.
            Please make sure your input file have sufficient column names.
            Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
        }else if($status==5){
          $message .= ' (Error from lead SNPs and candidate SNPs identification)<br/>
            This error occures when no candidate SNPs were identified.
            It might be becaseu there is no significant hit at your defined P-value cutoff for lead SNPs and GWAS tagged SNPs.
            In that case, you can relax threshold or provide predefined lead SNPs.
            Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#snp2gene">Tutorial<a/> for detilas.<br/>';
        }else if($status==6){
          $message .= ' (Error from lead SNPs and candidate SNPs identification)<br/>
            This error might be because of either invalid input parameters or columns which are wrongly labeled.
            Please make sure your input file have sufficient column names.
            Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
        }else if($status==7){
          $message .= ' (Error during SNPs annotation extraction)<br/>
            This error might be because of either invalid input parameters or columns which are wrongly labeled.
            Please make sure your input file have sufficient column names.
            Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
        }else if($status==8 || $status==9){
          $message .= ' (Error during extracting external data sources)<br/>
            This error might be because of either invalid input parameters or columns which are wrongly labeled.
            Please make sure your input file have sufficient column names.
            Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
        }else if($status==10){
          $message .= ' (Error during gene mapping)<br/>
            This error might be because of either invalid input parameters or columns which are wrongly labeled.
            Please make sure your input file have sufficient column names.
            Please refer <a href="http://ctg.labs.vu.nl/IPGAP/tutorial#prepare-input-files">Tutorial<a/> for detilas.<br/>';
        }

      }

      $message .= "
      Please do not hesitate to contact us for any questions/suggestions.<br/><br/>
      Kyoko Watanabe<br/>
      VU University Amsterdam<br/>
      Dept. Complex Trait Genetics<br/>
      De Boelelaan 1085 WN-B628 1018HV Amsterdam The Netherlands<be/>
      k.watanabe@vu.nl<br/>
      </body>
      </html>
      ";

      $headers = "MIME-Version: 1.0". "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
      $headers .= "From: <k.watanabe@vu.nl>"."\r\n";

      mail($email, $subject, $message, $headers, "-r $email");
    }
}
