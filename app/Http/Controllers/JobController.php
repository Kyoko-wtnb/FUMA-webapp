<?php

namespace IPGAP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use IPGAP\Http\Requests;
use IPGAP\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use View;
use Storage;
use File;
use JavaScript;
use Zipper;

class JobController extends Controller
{
    public function queryJob(Request $request){
      $email = $request -> input('JobQueryEmail');
      $jobtitle = $request -> input('JobQueryTitle');
      $results = DB::select('SELECT * FROM jobs WHERE email=?', [$email]);
      $exists = false;
      $jobID = 0;
      foreach($results as $row){
        if($row->title==$jobtitle){
          $exists = true;
          $jobID = $row->jobID;
          break;
        }
      }
      $filedir = storage_path().'/jobs/'.$jobID.'/'; #local
      #webserver $filedir = '/data/IPGAP/jobs/'.$jobID.'/';
      $params = file($filedir."params.txt");
      $posMap = preg_split("/[\t]/", chop($params[17]))[1];
      $eqtlMap = preg_split("/[\t]/", chop($params[26]))[1];
      // get jobID
      // update last_access date
      JavaScript::put([
        'jobtype'=>'jobquery',
        'email'=>$email,
        'jobID'=>$jobID,
        'filedir'=>$filedir,
        'posMap'=>$posMap,
        'eqtlMap'=>$eqtlMap
      ]);
      return view('pages.snp2geneResults', ['jobID'=>$jobID]); #local
      #webserver return view('pages.snp2geneResults', ['jobID'=>$jobID, 'subdir'=>'/IPGAP']);
    }

    public function newJob(Request $request){
      $email = $request -> input('NewJobEmail');
      $jobtitle = $request -> input('NewJobTitle');

      // obtain jobID and create directory
      $results = DB::select('SELECT * FROM jobs WHERE email=?', [$email]);
      $exists = false;
      $jobID = 0;
      foreach($results as $row){
        if($row->title==$jobtitle){
          $exists = true;
          $jobID = $row->jobID;
          break;
        }
      }
      $filedir;
      $date = date('Y-m-d H:i:s');
      if($exists){
        $filedir = storage_path().'/jobs/'.$jobID; #local
        #webserver $filedir = '/data/IPGAP/jobs/'.$jobID;
        File::cleanDirectory($filedir);
        DB::table('jobs') -> where('jobID', $jobID)
                          -> update(['created_date'=>$date, 'last_access'=>$date]);
      }else{
        $jobID = DB::select('SELECT MAX(jobID) as max FROM jobs')[0];
        $jobID = $jobID->max;
        $jobID++;
        $filedir = storage_path().'/jobs/'.$jobID; #local
        #webserver $filedir = '/data/IPGAP/jobs/'.$jobID;
        File::makeDirectory($filedir);
        DB::table('jobs') -> insert(['jobID'=>$jobID, 'email'=>$email, 'title'=>$jobtitle,
                                      'created_date'=>$date, 'last_access'=>$date]);
      }

      // upload input Filesystem
      $leadSNPs = "input.lead";
      $GWAS = "input.gwas";
      $regions = "input.regions";
      $leadSNPsfileup = 0;
      $GWASfileup = 0;
      $regionsfileup = 0;
      $gwasformat = $request->input('gwasformat');
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
      $Xchr = $request -> input('Xchr');
      if(strcmp($Xchr, "Yes")==0){$Xchr=1;}
      else{$Xchr=0;}
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
      if($leadSNPsfileup==1){File::append($paramfile, "input lead SNPs file\t".$_FILES["leadSNPs"]["name"]."\n");}
      else{File::append($paramfile, "input lead SNPs file\tNot given\n");}
      File::append($paramfile, "Identify additional lead SNPs\t$addleadSNPs\n");
      if($regionsfileup==1){File::append($paramfile, "input genetic regions file\t".$_FILES["regions"]["name"]."\n");}
      else{File::append($paramfile, "input genetic regions file\tNot given\n");}
      File::append($paramfile, "sample size\t$N\n");
      File::append($paramfile, "exclude MHC\t$exMHC\n");
      File::append($paramfile, "extended MHC region\t$extMHC\n");
      File::append($paramfile, "include chromosome X\t$Xchr\n");
      File::append($paramfile, "gene type\t$genetype\n");
      File::append($paramfile, "lead SNP P-value\t$leadP\n");
      File::append($paramfile, "r2\t$r2\n");
      File::append($paramfile, "GWAS tagged SNPs P-value\t$gwasP\n");
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
      File::append($paramfile, "eqtlMap min CADD\t$eqtlMapCADDth\n");
      File::append($paramfile, "eqtlMap min RegulomeDB\t$eqtlMapRDBth\n");
      File::append($paramfile, "eqtlMap chromatin state filterinf tissues\t$eqtlMapChr15\n");
      File::append($paramfile, "eqtlMap max chromatin state\t$eqtlMapChr15Max\n");
      File::append($paramfile, "eqtlMap chromatin state filtering method\t$eqtlMapChr15Meth\n");

      JavaScript::put([
        'jobtype'=>'newjob',
        'email'=>$email,
        'jobtitle'=>$jobtitle,
        'jobID'=>$jobID,
        'filedir'=>$filedir.'/',
        'leadSNPsfileup'=>$leadSNPsfileup,
        'regionsfileup'=>$regionsfileup,
        'gwasformat'=>$gwasformat,
        'addleadSNPs'=>$addleadSNPs,
        'N' => $N,
        'leadP'=>$leadP,
        'r2'=>$r2,
        'gwasP'=>$gwasP,
        'pop'=>$pop,
        'KGSNPs'=>$KGSNPs,
        'maf'=>$maf,
        'mergeDist'=>$mergeDist,
        'Xchr' => $Xchr,
        'exMHC'=>$exMHC,
        'extMHC'=>$extMHC,
        'genetype'=>$genetype,
        'posMap'=>$posMap,
        'posMapWindow'=>$posMapWindow,
        'posMapWindowSize'=>$posMapWindowSize,
        'posMapAnnot'=>$posMapAnnot,
        'posMapCADDth'=>$posMapCADDth,
        'posMapRDBth'=>$posMapRDBth,
        'posMapChr15'=>$posMapChr15,
        'posMapChr15Max'=>$posMapChr15Max,
        'posMapChr15Meth'=>$posMapChr15Meth,
        'eqtlMap'=>$eqtlMap,
        'eqtlMaptss'=>$eqtlMaptss,
        'eqtlMapSigeqtl'=>$sigeqtl,
        'eqtlMapeqtlP'=>$eqtlP,
        'eqtlMapCADDth'=>$eqtlMapCADDth,
        'eqtlMapRDBth'=>$eqtlMapRDBth,
        'eqtlMapChr15'=>$eqtlMapChr15,
        'eqtlMapChr15Max'=>$eqtlMapChr15Max,
        'eqtlMapChr15Meth'=>$eqtlMapChr15Meth
      ]);

      return view('pages.snp2geneResults', ['jobID'=>$jobID]); #local
      #webserver return view('pages.snp2geneResults', ['jobID'=>$jobID, 'subdir'=>'/IPGAP']);
    }

    public function CandidateSelection(Request $request){
      // $args = $request->all();
      // $args = json_encode($args);
      // $script = storage_path().'/scripts/snp2gene.py';
      // exec("python $script $args");
      $email = $request -> input('email');
      $jobtitle = $request -> input('jobtitle');
      $filedir = $request -> input('filedir');
      $gwasformat = $request -> input('gwasformat');
      $leadfile = $request -> input('leadfile');
      $addleadSNPs = $request -> input('addleadSNPs');
      $regionfile = $request -> input('regionfile');
      $N = $request -> input('N');
      $leadP = $request -> input('leadP');
      $r2 = $request -> input('r2');
      $gwasP = $request -> input('gwasP');
      $pop = $request -> input('pop');
      $KGSNPs = $request -> input('KGSNPs');
      $maf = $request -> input('maf');
      $mergeDist = $request -> input('mergeDist');
      $Xchr = $request -> input('Xchr');
      $exMHC = $request -> input('exMHC');
      $extMHC = $request -> input('extMHC');
      $genetype = $request -> input('genetype');
      $posMap = $request -> input('posMap');
      $posMapWindow = $request -> input('posMapWindow');
      $posMapWindowSize = $request -> input('posMapWindowSize');
      $posMapAnnot = $request -> input('posMapAnnot');
      $posMapCADDth = $request -> input('posMapCADDth');
      $posMapRDBth = $request -> input('posMapRDBth');
      $posMapChr15 = $request -> input('posMapChr15');
      $posMapChr15Max = $request -> input('posMapChr15Max');
      $posMapChr15Meth = $request -> input('posMapChr15Meth');
      $eqtlMap = $request -> input('eqtlMap');
      $eqtlMaptss = $request -> input('eqtlMaptss');
      $eqtlMapSigeqtl = $request -> input('eqtlMapSigeqtl');
      $eqtlMapeqtlP = $request -> input('eqtlMapeqtlP');
      $eqtlMapCADDth = $request -> input('eqtlMapCADDth');
      $eqtlMapRDBth = $request -> input('eqtlMapRDBth');
      $eqtlMapChr15 = $request -> input('eqtlMapChr15');
      $eqtlMapChr15Max = $request -> input('eqtlMapChr15Max');
      $eqtlMapChr15Meth = $request -> input('eqtlMapChr15Meth');

      // send job started mail
      $subject = "IPGAP job submitted";
      $message = "
      <html>
      <head><h3>IPGAP job has been submitted</h3></head>
      <body>
      Thank you for submitting a job.<br/>
      We will inform you ones job is done with link to the result page.<br/>
      It usutally takes 30 minuts to 1 hour (depending on file size and parameters).<br/>
      <br/>
      <h4>Job summary</h4>
      your email: ".$email."<br/>
      your job title: ".$jobtitle."<br/>
      you can use those information to qury your results.<br/>
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
      $headers = "MIME-Version: 1.0". "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
      $headers .= "From: <k.watanabe@vu.nl>"."\r\n";

      mail($email, $subject, $message, $headers, "-r $email");


      $script = storage_path().'/scripts/gwas_file.pl';
      exec("perl $script $filedir $gwasformat");
      $script = storage_path().'/scripts/magma.pl';
      exec("perl $script $filedir $N $pop");
      $script = storage_path().'/scripts/manhattan_filt.py';
      exec("python $script $filedir");
      $script = storage_path().'/scripts/QQSNPs_filt.py';
      exec("perl $script $filedir");

      $script = storage_path().'/scripts/getLD.pl';
      // $process = new Proces("/usr/bin/perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC");
      // $process -> start();
      // echo "perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $gwasformat $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC";
      exec("perl $script $filedir $pop $leadP $KGSNPs $gwasP $maf $r2 $leadfile $addleadSNPs $regionfile $mergeDist $exMHC $extMHC $Xchr");
      $script = storage_path().'/scripts/SNPannot.R';
      exec("Rscript $script $filedir");
      $script = storage_path().'/scripts/getGWAScatalog.pl';
      exec("perl $script $filedir");
      $script = storage_path().'/scripts/getExAC.pl';
      #exec("perl $script $filedir");
      if($eqtlMap==1){
        $script = storage_path().'/scripts/geteQTL.pl';
        exec("perl $script $filedir $eqtlMaptss $eqtlMapSigeqtl $eqtlMapeqtlP");
      }

      $script = storage_path().'/scripts/geneMap.R';
      exec("Rscript $script $filedir $genetype $exMHC $extMHC $posMap $posMapWindow $posMapWindowSize $posMapAnnot $posMapCADDth $posMapRDBth $posMapChr15 $posMapChr15Max $posMapChr15Meth $eqtlMap $eqtlMaptss $eqtlMapSigeqtl $eqtlMapeqtlP $eqtlMapCADDth $eqtlMapRDBth $eqtlMapChr15 $eqtlMapChr15Max $eqtlMapChr15Meth");

      // send job completed mail
      $subject = "IPGAP job has been completed";
      $message = "
      <html>
      <head><h3>IPGAP job has been completed!!</h3></head>
      <body>
      Your job has been completed.<br/>
      Pleas follow the link to go to the results page.<br/>
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
      $headers = "MIME-Version: 1.0". "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
      $headers .= "From: <k.watanabe@vu.nl>"."\r\n";

      mail($email, $subject, $message, $headers, "-r $email");
    }

    public function annotPlot(Request $request){
      $jobID = "test";
      $jobID = $request -> input('jobID');
      $filedir = storage_path()."/jobs/".$jobID."/"; #local
      #webserver $filedir = '/data/IPGAP/jobs/'.$jobID.'/';
      $type=null;
      $rowI=null;
      if($request -> has('annotPlotSelect_leadSNP')){
        $type = "leadSNP";
        $rowI = $request -> input('annotPlotSelect_leadSNP');
      }else if($request -> has('annotPlotSelect_interval')){
        $type = "interval";
        $rowI = $request -> input('annotPlotSelect_interval');
      }
      file_put_contents("/media/sf_Documents/VU/Data/WebApp/test.txt", "$type $rowI"); #local

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
      // file_put_contents("/media/sf_Documents/VU/Data/WebApp/test.txt", "Rscript $script $filedir $type $rowI $GWAS $CADD $RDB $eqtl $Chr15 $Chr15cells"); #local
      #webserver file_put_contents("/data/IPGAP/test.txt", "Rscript $script $filedir $type $rowI $GWAS $CADD $RDB $eqtl $Chr15 $Chr15cells");

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
      return view('pages.annotPlot', ['jobID'=>$jobID]); #local
      #webserver return view('pages.annotPlot', ['jobID'=>$jobID, 'subdir'=>'/IPGAP']);
    }

    public function filedown(Request $request){
      $jobID = $request -> input('jobID');
      $filedir = storage_path()."/jobs/".$jobID."/"; #local
      #webserver $filedir = '/data/IPGAP/jobs/'.$jobID.'/';
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
      if($request -> has('exacfile')){$files[] = $filedir."ExAC.txt";}
      $zip_name = $filedir."IPGAP.zip";
      Zipper::make($zip_name)->add($files);
      return response() -> download($zip_name);
    }

    public function gene2funcSubmit(Request $request){
      $filedir = storage_path().'/jobs/gene2func/'; #local
      #webserver $filedir = "/data/IPGAP/jobs/gene2func/";

      if($request -> has('genes')){
        $gtype = "text";
        $gval = $request -> input('genes');
        $gval = preg_split('/[\n\r]+/', $gval);
        $gval = implode(':', $gval);
        // file_put_contents('/media/sf_Documents/VU/Data/WebApp/test.txt', $gval);
      }else{
        $gtype = "file";
        $gval = "genesQuery.txt";
        $request -> file('genefile')->move($filedir, "genesQuery.txt");
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

      if($request -> has('Xchr')){
        $Xchr = 1;
      }else{
        $Xchr = 0;
      }

      if($request -> has('MHC')){
        $MHC = 1;
      }else{
        $MHC = 0;
      }
      // echo "<p>gtype: $gtype<br/>gval: $gval<br/>bkgtype: $bkgtype<br/>bkgval: $bkgval</p>";

      JavaScript::put([
        'filedir' => $filedir,
        'gtype' => $gtype,
        'gval' => $gval,
        'bkgtype' => $bkgtype,
        'bkgval' => $bkgval,
        'Xchr' => $Xchr,
        'MHC' => $MHC
      ]);

      return view('pages.gene2func', ['status'=>'query']); #local
      #webserver return view('pages.gene2func', ['subdir' => '/IPGAP', 'status'=>'query']);
    }

    public function geneQuery(Request $request){
      $filedir = $request -> input('filedir');
      $gtype = $request -> input('gtype');
      $gval = $request -> input('gval');
      $bkgtype = $request -> input('bkgtype');
      $bkgval = $request -> input('bkgval');
      $Xchr = $request -> input('Xchr');
      $MHC = $request -> input('MHC');


      $script = storage_path()."/scripts/gene2func.R";
      #exec("Rscript $script $filedir $gtype $gval $bkgtype $bkgval $Xchr $MHC");
    }

    public function snp2geneGeneQuery(Request $request){
      $jobID = $request -> input('jobID');
      $filedir = storage_path().'/jobs/'.$jobID.'/'; #local
      #webserver $filedir = '/data/IPGAP/jobs/'.$jobID.'/';
      $gtype="text";
      $bkgtype="select";

      $params = file($filedir."params.txt");
      $Xchr = preg_split("/[\t]/", chop($params[9]))[1];
      $MHC = preg_split("/[\t]/", chop($params[7]))[1];
      $bkgval = preg_split("/[\t]/", chop($params[10]))[1];

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
        'filedir' => $filedir,
        'gtype' => $gtype,
        'gval' => $gval,
        'bkgtype' => $bkgtype,
        'bkgval' => $bkgval,
        'Xchr' => $Xchr,
        'MHC' => $MHC
      ]);

      return view('pages.gene2func', ['status'=>'query']); #local
      #webserver return view('pages.gene2func', ['subdir' => '/IPGAP', 'status'=>'query']);
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
      // file_put_contents("/media/sf_Documents/VU/Data/WebApp/test.txt", json_encode($all_row));#local
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

    public function sendMail(Request $request){
      $email = "ainoktmr@gmail.com";
      $subject = "test";
      $message = "Test meail from IPGAP\n";
      $headers = "MIME-Version: 1.0". "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
      $headers .= "From: <k.watanabe@vu.nl>"."\r\n";

      mail($email, $subject, $message, $headers);
    }
}
