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
use Auth;
use Storage;
use File;
use JavaScript;
// use Zipper;
use Mail;
use IPGAP\User;
use IPGAP\Jobs\snp2geneProcess;

class JobController extends Controller
{
    protected $user;

    public function __construct(){
        // Protect this Controller
        $this->middleware('auth');

        // Store user
        $this->user = Auth::user();
    }

    public function getJobList(){
        $email = $this->user->email;

        if($email){
            $results = SubmitJob::where('email', $email)
                ->orderBy('created_at', 'desc')
                ->get();
        }else{
            $results = array();
        }

        $this->queueNewJobs();

        return response()->json($results);

    }

    public function queueNewJobs(){
      $user = $this->user;
      $email = $user->email;
      $newJobs = DB::table('SubmitJobs')->where('email', $email)->where('status', 'NEW')->get();
      if(count($newJobs)>0){
        foreach($newJobs as $job){
          // if($job->status != "NEW"){continue;}
          $jobID = $job->jobID;
          DB::table('SubmitJobs') -> where('jobID', $jobID)
            -> update(['status'=>'QUEUED']);
          $this->dispatch(new snp2geneProcess($user, $jobID));
        }
      }
      return;
    }

    public function checkJobStatus($jobID){

        $job = SubmitJob::where('jobID', $jobID)
            ->where('email', $this->user->email)->first();

        if( ! $job ){
            return "Notfound";
        }

        return $job->status;
    }

    public function getParams(Request $request){
      $jobID = $request->input('jobID');
      $date = date('Y-m-d H:i:s');
      DB::table('SubmitJobs') -> where('jobID', $jobID)
                        -> update(['updated_at'=>$date]);

      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $params = parse_ini_file($filedir."params.config");
      $posMap = $params['posMap'];
      $eqtlMap = $params['eqtlMap'];
      $orcol = $params['orcol'];
      $becol = $params['becol'];
      $secol = $params['secol'];
      echo "$filedir:$posMap:$eqtlMap:$orcol:$becol:$secol";
    }

    public function Error5(Request $request){
      $jobID = $request->input('jobID');

      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $f = fopen($filedir.'topSNPs.txt', 'r');
      $rows = [];
      while($row = fgetcsv($f, 0, "\t")){
        $rows[] = $row;
      }

      return json_encode($rows);
    }

    public function newJob(Request $request){
      // check file type
      if($request -> hasFile('GWASsummary')){
        $type = mime_content_type($_FILES["GWASsummary"]["tmp_name"]);
        if($type != "text/plain" && $type != "application/zip" && $type != "application/x-gzip"){
        // if(mime_content_type($request->input("GWASsummary"))!="text/plain"){
          $jobID = null;
          return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>'fileFormatGWAS']);
          // return back()->withInput(['status'=> 'fileFormat']); // parameter is not working
        }
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

      $date = date('Y-m-d H:i:s');
      $jobID;
      $filedir;
      $email = $this->user->email;

      if($request->has("NewJobTitle")){
        $jobtitle = $request -> input('NewJobTitle');
      }else{
        $jobtitle="None";
      }

      // Create new job in database
      $submitJob = new SubmitJob;
      $submitJob->email = $email;
      $submitJob->title = $jobtitle;
      $submitJob->status = 'NEW';
      $submitJob->save();

      // Get jobID (automatically generated)
      $jobID = $submitJob->jobID;

      // create job directory
      $filedir = config('app.jobdir').'/jobs/'.$jobID;
      File::makeDirectory($filedir, $mode = 0755, $recursive = true);

      // upload input Filesystem
      $leadSNPs = "input.lead";
      $GWAS = "input.gwas";
      $regions = "input.regions";
      $leadSNPsfileup = 0;
      $GWASfileup = 0;
      $regionsfileup = 0;
      // $gwasformat = $request->input('gwasformat'); //removed this option
      // $gwasformat = "Plain";

      if($request -> has('addleadSNPs')){$addleadSNPs=1;}
      else{$addleadSNPs=0;}

      // GWAS smmary stats file
      if($request -> hasFile('GWASsummary')){
        $type = mime_content_type($_FILES["GWASsummary"]["tmp_name"]);
        if($type=="text/plain"){
          $request -> file('GWASsummary')->move($filedir, $GWAS);
        }else if($type=="application/zip"){
          $request -> file('GWASsummary')->move($filedir, "temp.zip");
          $zip = new \ZipArchive;
          $zip -> open($filedir.'/temp.zip');
          $zf = $zip->getNameIndex(0);
          $zip->extractTo($filedir);
          File::move($filedir.'/'.$zf, $filedir.'/'.$GWAS);
        }else{
          $f = $_FILES["GWASsummary"]["name"];
          $request -> file('GWASsummary')->move($filedir, $f);
          system("gzip -cd $filedir/$f > $filedir/$GWAS");
        }
        $GWASfileup = 1;
      }else if($request -> has('egGWAS')){
        $exfile = config('app.jobdir').'/example/CD.gwas';
        File::copy($exfile, $filedir.'/input.gwas');
        $GWASfileup = 1;
      }

      // pre-defined lead SNPS file
      if($request -> hasFile('leadSNPs')){
        $type = mime_content_type($_FILES["leadSNPs"]["tmp_name"]);
        if($type=="text/plain"){
          $request -> file('leadSNPs')->move($filedir, $leadSNPs);
        }else if($type=="application/zip"){
          $request -> file('leadSNPs')->move($filedir, "temp.zip");
          $zip = new \ZipArchive;
          $zip -> open($filedir.'/temp.zip');
          $zf = $zip->getNameIndex(0);
          $zip->extractTo($filedir);
          File::move($filedir.'/'.$zf, $filedir.'/'.$leadSNPs);
        }else{
          $f = $_FILES["leadSNPs"]["name"];
          $request -> file('leadSNPs')->move($filedir, $f);
          system("gzip -cd $filedir/$f > $filedir/$leadSNPs");
        }
        $leadSNPsfileup = 1;
      }

      // pre-defined genomic region file
      if($request -> hasFile('regions')){
        $type = mime_content_type($_FILES["regions"]["tmp_name"]);
        if($type=="text/plain"){
          $request -> file('regions')->move($filedir, $regions);
        }else if($type=="application/zip"){
          $request -> file('regions')->move($filedir, "temp.zip");
          $zip = new \ZipArchive;
          $zip -> open($filedir.'/temp.zip');
          $zf = $zip->getNameIndex(0);
          $zip->extractTo($filedir);
          File::move($filedir.'/'.$zf, $filedir.'/'.$regions);
        }else{
          $f = $_FILES["regions"]["name"];
          $request -> file('regions')->move($filedir, $f);
          system("gzip -cd $filedir/$f > $filedir/$regions");
        }
        $regionsfileup = 1;
      }

      // get parameters
      // column names
      $chrcol = "NA";
      $poscol = "NA";
      $rsIDcol = "NA";
      $pcol = "NA";
      $altcol = "NA";
      $refcol = "NA";
      $orcol = "NA";
      $becol = "NA";
      $secol = "NA";
      // $mafcol = "NA";
      if($request -> has('chrcol')){$chrcol = $request->input('chrcol');}
      if($request -> has('poscol')){$poscol = $request->input('poscol');}
      if($request -> has('rsIDcol')){$rsIDcol = $request->input('rsIDcol');}
      if($request -> has('pcol')){$pcol = $request->input('pcol');}
      if($request -> has('altcol')){$altcol = $request->input('altcol');}
      if($request -> has('refcol')){$refcol = $request->input('refcol');}
      if($request -> has('orcol')){$orcol = $request->input('orcol');}
      if($request -> has('becol')){$orcol = $request->input('becol');}
      if($request -> has('secol')){$secol = $request->input('secol');}
      // if($request -> has('mafcol')){$chrcol = $request->input('mafcol');}
      // MHC region
      if($request -> has('MHCregion')){$exMHC=1;}
      else{$exMHC=0;}
      $extMHC = $request -> input('extMHCregion');
      if($extMHC==null){$extMHC="NA";}
      // gene type
      $genetype = implode(":", $request -> input('genetype'));
      // others
      $N="NA";
      $Ncol="NA";
      if($request->has('N')){
        $N = $request->input('N');
      }else if($request->has('Ncol')){
        $Ncol = $request->input('Ncol');
      }
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

      if($request -> has('posMapWindow')){
        $posMapWindowSize=$request -> input('posMapWindow');
        $posMapAnnot="NA";
      }else{
        $posMapWindow="NA";
        $posMapAnnot=implode(":",$request -> input('posMapAnnot'));
      }
      // $posMapWindowSize = $request -> input('posMapWindow');
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
        $temp = $request -> input('posMapChr15Ts');
        $posMapChr15 = [];
        foreach($temp as $ts){
          if($ts != "null"){
            $posMapChr15[] = $ts;
          }
        }
        $posMapChr15 = implode(":", $posMapChr15);
        $posMapChr15Max = $request -> input('posMapChr15Max');
        $posMapChr15Meth = $request -> input('posMapChr15Meth');
      }else{
        $posMapChr15 = "NA";
        $posMapChr15Max = "NA";
        $posMapChr15Meth = "NA";
      }

      if($request -> has('eqtlMap')){
        $eqtlMap=1;
        $temp = $request -> input('eqtlMapTs');
        // $eqtlMapGts = $request -> input('eqtlMapGts');
        $eqtlMapTs = [];
        $eqtlMapGts = [];
        foreach($temp as $ts){
          if($ts != "null"){
            $eqtlMapTs[] = $ts;
          }
        }
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
        $temp = $request -> input('eqtlMapChr15Ts');
        $eqtlMapChr15 = [];
        foreach($temp as $ts){
          if($ts != "null"){
            $eqtlMapChr15[] = $ts;
          }
        }
        $eqtlMapChr15 = implode(":", $eqtlMapChr15);
        $eqtlMapChr15Max = $request -> input('eqtlMapChr15Max');
        $eqtlMapChr15Meth = $request -> input('eqtlMapChr15Meth');
      }else{
        $eqtlMapChr15 = "NA";
        $eqtlMapChr15Max = "NA";
        $eqtlMapChr15Meth = "NA";
      }
      // write parameter into a file
      $paramfile = $filedir.'/params.config';
      File::put($paramfile, "[jobinfo]\n");
      File::append($paramfile, "created_at=$date\n");
      File::append($paramfile, "title=$jobtitle\n");

      File::append($paramfile, "\n[inputfiles]\n");
      if($request -> hasFile('GWASsummary')){
        File::append($paramfile, "gwasfile=".$_FILES["GWASsummary"]["name"]."\n");
      }else{
        File::append($paramfile, "gwasfile=fuma.example.CD.gwas\n");
      }
      File::append($paramfile, "chrcol=$chrcol\n");
      File::append($paramfile, "poscol=$poscol\n");
      File::append($paramfile, "rsIDcol=$rsIDcol\n");
      File::append($paramfile, "pcol=$pcol\n");
      File::append($paramfile, "altcol=$altcol\n");
      File::append($paramfile, "refcol=$refcol\n");
      File::append($paramfile, "orcol=$orcol\n");
      File::append($paramfile, "becol=$becol\n");
      File::append($paramfile, "secol=$secol\n");
      // File::append($paramfile, "mafcol=$mafcol\n");

      if($leadSNPsfileup==1){File::append($paramfile, "leadSNPsfile=".$_FILES["leadSNPs"]["name"]."\n");}
      else{File::append($paramfile, "leadSNPsfile=NA\n");}
      File::append($paramfile, "addleadSNPs=$addleadSNPs\n");
      if($regionsfileup==1){File::append($paramfile, "regionsfile=".$_FILES["regions"]["name"]."\n");}
      else{File::append($paramfile, "regionsfile=NA\n");}

      File::append($paramfile, "\n[params]\n");
      File::append($paramfile, "N=$N\n");
      File::append($paramfile, "Ncol=$Ncol\n");
      File::append($paramfile, "exMHC=$exMHC\n");
      File::append($paramfile, "extMHC=$extMHC\n");
      // File::append($paramfile, "include chromosome X\t$Xchr\n");
      File::append($paramfile, "genetype=$genetype\n");
      File::append($paramfile, "leadP=$leadP\n");
      File::append($paramfile, "r2=$r2\n");
      File::append($paramfile, "gwasP=$gwasP\n");
      File::append($paramfile, "pop=$pop\n");
      File::append($paramfile, "MAF=$maf\n");
      File::append($paramfile, "Incl1KGSNPs=$KGSNPs\n");
      File::append($paramfile, "mergeDist=$mergeDist\n");

      File::append($paramfile, "\n[posMap]\n");
      File::append($paramfile, "posMap=$posMap\n");
      // File::append($paramfile, "posMapWindow=$posMapWindow\n");
      File::append($paramfile, "posMapWindowSize=$posMapWindowSize\n");
      File::append($paramfile, "posMapAnnot=$posMapAnnot\n");
      File::append($paramfile, "posMapCADDth=$posMapCADDth\n");
      File::append($paramfile, "posMapRDBth=$posMapRDBth\n");
      File::append($paramfile, "posMapChr15=$posMapChr15\n");
      File::append($paramfile, "posMapChr15Max=$posMapChr15Max\n");
      File::append($paramfile, "posMapChr15Meth=$posMapChr15Meth\n");

      File::append($paramfile, "\n[eqtlMap]\n");
      File::append($paramfile, "eqtlMap=$eqtlMap\n");
      File::append($paramfile, "eqtlMaptss=$eqtlMaptss\n");
      File::append($paramfile, "eqtlMapSig=$sigeqtl\n");
      File::append($paramfile, "eqtlMapP=$eqtlP\n");
      File::append($paramfile, "eqtlMapCADDth=$eqtlMapCADDth\n");
      File::append($paramfile, "eqtlMapRDBth=$eqtlMapRDBth\n");
      File::append($paramfile, "eqtlMapChr15=$eqtlMapChr15\n");
      File::append($paramfile, "eqtlMapChr15Max=$eqtlMapChr15Max\n");
      File::append($paramfile, "eqtlMapChr15Meth=$eqtlMapChr15Meth\n");

      // $user = DB::table('users')->where('email', $email)->first();
      // $this->dispatch(new snp2geneProcess($user, $jobID));
      return redirect("/snp2gene#joblist-panel");
    }

    public function deleteJob(Request $request){
      $jobID = $request->input('jobID');
      File::deleteDirectory(config('app.jobdir').'/jobs/'.$jobID);
      DB::table('SubmitJobs')->where('jobID', $jobID)->delete();
      return;
    }

    public function G2FdeleteJob(Request $request){
      $jobID = $request->input('jobID');
      File::deleteDirectory(config('app.jobdir').'/gene2func/'.$jobID);
      DB::table('gene2func')->where('jobID', $jobID)->delete();
      return;
    }

    public function annotPlot(Request $request){
      $jobID = $request -> input('jobID');
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $type = $request->input('annotPlotSelect');
      $rowI = $request->input('annotPlotRow');

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
        $temp = $request -> input('annotPlotChr15Ts');
        $Chr15cells = [];
        foreach($temp as $ts){
          if($ts != "null"){
            $Chr15cells[] = $ts;
          }
        }
        $Chr15cells = implode(":", $Chr15cells);
      }else{
        $Chr15cells="NA";
      }
      if($request -> has('annotPlot_eqtl')){$eqtl=1;}

      $script = storage_path()."/scripts/annotPlot.py";
      exec("python $script $filedir $type $rowI $GWAS $CADD $RDB $eqtl $Chr15 $Chr15cells");
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
          $eqtlgenes[] = $row[3];
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
      if($request -> has('paramfile')){ $files[] = "params.config";}
      if($request -> has('indSNPfile')){$files[] = "IndSigSNPs.txt";}
      if($request -> has('leadfile')){$files[] = "leadSNPs.txt";}
      if($request -> has('locifile')){$files[] = "GenomicRiskLoci.txt";}
      if($request -> has('snpsfile')){$files[] = "snps.txt"; $files[] = "ld.txt";}
      if($request -> has('annovfile')){$files[] = "annov.txt";}
      if($request -> has('annotfile')){$files[] = "annot.txt";}
      if($request -> has('genefile')){$files[] = "genes.txt";}
      if($request -> has('eqtlfile')){$files[] = "eqtl.txt";}
      // if($request -> has('exacfile')){$files[] = $filedir."ExAC.txt";}
      if($request -> has('gwascatfile')){$files[] = "gwascatalog.txt";}
      if($request -> has('magmafile')){
        $files[] = "magma.genes.out";
        if(File::exists($filedir."magma.sets.out")){
          $files[] = "magma.genes.raw";
          $files[] = "magma.sets.out";
          $files[] = "magma.setgenes.out";
        }
      }

      $zip = new \ZipArchive();
      $zipfile = $filedir."FUMA_job".$jobID.".zip";

      if(File::exists($zipfile)){
        File::delete($zipfile);
      }
      // Zipper::make($zipfile)->add($files);
      // sleep(5);
      $zip -> open($zipfile, \ZipArchive::CREATE);
      $zip->addFile(storage_path().'/README', "README");
      foreach($files as $f){
        $zip->addFile($filedir.$f, $f);
      }
      $zip -> close();
      return response() -> download($zipfile);
    }

    public function getG2FJobList(){
        $email = $this->user->email;

        if($email){
            $results = DB::table('gene2func')->where('email', $email)
                ->orderBy('created_at', 'desc')
                ->get();
        }else{
            $results = array();
        }

        return response()->json($results);
    }

    public function gene2funcSubmit(Request $request){
      // $id = uniqid();
      $date = date('Y-m-d H:i:s');
      $jobID;
      $filedir;
      $email = $this->user->email;

      if($request->has('title')){
        $title = $request->input('title');
      }else{
        $title = "None";
      }

      DB::table('gene2func')->insert(
        ['title'=>$title, 'email'=>$email, 'created_at'=>$date]
      );

      // Get jobID (automatically generated)
      $jobID = DB::table('gene2func')->where('email', $email)->where('created_at', $date)->first()->jobID;
      $filedir = config('app.jobdir').'/gene2func/'.$jobID;
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

      $paramfile = $filedir.'params.config';
      File::put($paramfile, "[jobinfo]\n");
      File::append($paramfile, "created_at=$date\n");
      File::append($paramfile, "title=$title\n");
      File::append($paramfile, "\n[params]\n");
      File::append($paramfile, "gtype=$gtype\n");
      File::append($paramfile, "gval=$gval\n");
      File::append($paramfile, "bkgtype=$bkgtype\n");
      File::append($paramfile, "bkgval=$bkgval\n");
      File::append($paramfile, "MHC=$MHC\n");
      File::append($paramfile, "adjPmeth=$adjPmeth\n");
      File::append($paramfile, "adjPcut=$adjPcut\n");
      File::append($paramfile, "minOverlap=$minOverlap\n");

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
      exec("Rscript $script $filedir");
      $script = storage_path()."/scripts/GeneSet.py";
      exec("$script $filedir");
    }

    public function snp2geneGeneQuery(Request $request){
      $s2gID = $request -> input('jobID');


      $checkExists = DB::table('gene2func')->where('snp2gene', $s2gID)->first();
      if($checkExists==null){
        $date = date('Y-m-d H:i:s');
        $jobID;
        $filedir;
        $email = $this->user->email;

        if($request->has('title')){
          $title = $request->input('title');
        }else{
          $title = "None";
        }

        $s2gTitle = DB::table('SubmitJobs')->where('jobID', $s2gID)->first()->title;

        DB::table('gene2func')->insert(
          ['title'=>$title,'email'=>$email, 'snp2gene'=>$s2gID, 'snp2geneTitle'=>$s2gTitle, 'created_at'=>$date]
        );

        // Get jobID (automatically generated)
        $jobID = DB::table('gene2func')->where('snp2gene', $s2gID)->first()->jobID;
        $filedir = config('app.jobdir').'/gene2func/'.$jobID;
        File::makeDirectory($filedir);
        $filedir = $filedir.'/';

        $s2gfiledir = config('app.jobdir').'/jobs/'.$s2gID.'/';
        $gtype="text";
        $bkgtype="select";
        $params = parse_ini_file($s2gfiledir.'params.config');
        // $Xchr = preg_split("/[\t]/", chop($params[9]))[1];
        $MHC = $params['exMHC'];
        $bkgval = $params['genetype'];
        $adjPmeth = "fdr_bh";
        $adjPcut = 0.05;
        $minOverlap = 2;

        $gval = null;
        $f = fopen($s2gfiledir."genes.txt", 'r');
        fgetcsv($f, 0, "\t");
        while($row = fgetcsv($f, 0, "\t")){
          if($gval==null){
            $gval = $row[0];
          }else{
            $gval = $gval.":".$row[0];
          }
        }

        $paramfile = $filedir.'params.config';
        File::put($paramfile, "[jobinfo]\n");
        File::append($paramfile, "created_at=$date\n");
        File::append($paramfile, "title=$title\n");
        File::append($paramfile, "snp2geneID=$s2gID\n");
        File::append($paramfile, "snp2geneTitle=$s2gTitle\n");
        File::append($paramfile, "\n[params]\n");
        File::append($paramfile, "gtype=$gtype\n");
        File::append($paramfile, "gval=$gval\n");
        File::append($paramfile, "bkgtype=$bkgtype\n");
        File::append($paramfile, "bkgval=$bkgval\n");
        File::append($paramfile, "MHC=$MHC\n");
        File::append($paramfile, "adjPmeth=$adjPmeth\n");
        File::append($paramfile, "adjPcut=$adjPcut\n");
        File::append($paramfile, "minOverlap=$minOverlap\n");

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

      }else{
        $jobID = $checkExists->jobID;
        return redirect("gene2func/".$jobID);
      }
    }

    public function imgdown(Request $request){
      $svg = $request->input('data');
      $dir = $request->input('dir');
      $jobID = $request -> input('id');
      $type = $request->input('type');
      $fileName = $request->input('fileName');
      $svgfile = config('app.jobdir').'/'.$dir.'/'.$jobID.'/temp.svg';
      $outfile = config('app.jobdir').'/'.$dir.'/'.$jobID.'/';
      if($fileName=="expHeat"){
        $svg = preg_replace("/\),rotate/", ")rotate", $svg);
      }
      $fileName .= "_FUMAjob".$jobID;
      if($type=="svg"){
        file_put_contents($svgfile, $svg);
        $outfile .= $fileName.'.svg';
        File::move($svgfile, $outfile);
        return response() -> download($outfile);
      }else{
        $outfile .= $fileName.'.'.$type;
        $image = new \Imagick();
        $image->setResolution(300,300);
        $image->readImageBlob('<?xml version="1.0"?>'.$svg);
        $image->setImageFormat($type);
        $image->writeImage($outfile);
        return response() -> download($outfile);
      }
    }

    public function gene2funcFileDown(Request $request){
      $file = $request -> input('file');
      $id = $request -> input('id');
      $filedir = config('app.jobdir').'/gene2func/'.$id.'/'.$file;
      return response() -> download($filedir);
    }

}
