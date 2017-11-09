<?php

namespace fuma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use fuma\SubmitJob;
use fuma\Http\Requests;
use fuma\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use View;
use Auth;
use Storage;
use File;
use JavaScript;
use Mail;
use fuma\User;
use fuma\Jobs\snp2geneProcess;

class S2GController extends Controller
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
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);
		$posMap = $params['posMap'];
		$eqtlMap = $params['eqtlMap'];
		$orcol = $params['orcol'];
		$becol = $params['becol'];
		$secol = $params['secol'];
		$ciMap = 0;
		if(array_key_exists('ciMap', $params)){
			$ciMap = $params['ciMap'];
		}
		return "$filedir:$posMap:$eqtlMap:$ciMap:$orcol:$becol:$secol";
    }

	public function newJob(Request $request){
		// check file type
		if($request -> hasFile('GWASsummary')){
			$type = mime_content_type($_FILES["GWASsummary"]["tmp_name"]);
			if($type != "text/plain" && $type != "application/zip" && $type != "application/x-gzip"){
				$jobID = null;
				return view('pages.snp2gene', ['jobID' => $jobID, 'status'=>'fileFormatGWAS']);
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
				system("rm $filedir/temp.zip");
			}else{
				$f = $_FILES["GWASsummary"]["name"];
				$request -> file('GWASsummary')->move($filedir, $f);
				system("gzip -cd $filedir/$f > $filedir/$GWAS");
				system("rm $filedir/$f");
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

		if($leadSNPsfileup==1 && $request -> has('addleadSNPs')){$addleadSNPs=1;}
		else if($leadSNPsfileup==0){$addleadSNPs=1;}
		else{$addleadSNPs=0;}

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
		$eacol = "NA";
		$neacol = "NA";
		$orcol = "NA";
		$becol = "NA";
		$secol = "NA";

		if($request -> has('chrcol')){$chrcol = $request->input('chrcol');}
		if($request -> has('poscol')){$poscol = $request->input('poscol');}
		if($request -> has('rsIDcol')){$rsIDcol = $request->input('rsIDcol');}
		if($request -> has('pcol')){$pcol = $request->input('pcol');}
		if($request -> has('eacol')){$eacol = $request->input('eacol');}
		if($request -> has('neacol')){$neacol = $request->input('neacol');}
		if($request -> has('orcol')){$orcol = $request->input('orcol');}
		if($request -> has('becol')){$orcol = $request->input('becol');}
		if($request -> has('secol')){$secol = $request->input('secol');}

		// MHC region
		if($request -> has('MHCregion')){
			$exMHC=1;
			$MHCopt = $request->input('MHCopt');
		}else{
			$exMHC=0;
			$MHCopt = "NA";
		}
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
		$refpanel = $request -> input('refpanel');
		$pop = preg_replace('/.+\/.+\/(.+)/', '$1', $refpanel);
		$refpanel = preg_replace('/(.+\/.+)\/.+/', '$1', $refpanel);
		$KGSNPs = $request -> input('KGSNPs');
		if(strcmp($KGSNPs, "Yes")==0){$KGSNPs=1;}
		else{$KGSNPs=0;}
		$maf = $request -> input('maf');
		$mergeDist = $request -> input('mergeDist');

		// positional mapping
		if($request -> has('posMap')){$posMap=1;}
		else{$posMap=0;}
		if($request -> has('posMapWindow')){
		$posMapWindowSize=$request -> input('posMapWindow');
		$posMapAnnot="NA";
		}else{
		$posMapWindowSize="NA";
		$posMapAnnot=implode(":",$request -> input('posMapAnnot'));
		}
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

		// eqtl mapping
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

		// chromatin interaction mapping
		$ciMap = 0;
		$ciMapFileN = 0;
		$ciMapFiles = "NA";
		if($request->has('ciMap')){
			$ciMap = 1;
			if($request->has('ciMapBuildin')){
				$temp = $request->input('ciMapBuildin');
				$ciMapBuildin = [];
				foreach($temp as $dat){
					if($dat != "null"){
						$ciMapBuildin[] = $dat;
					}
				}
				$ciMapBuildin = implode(":", $ciMapBuildin);
			}else{
				$ciMapBuildin = "NA";
			}

			$ciMapFileN = (int)$request->input("ciFileN");
			if($ciMapFileN>0){
				$ciMapFiles = [];
				$n = 1;
				while(count($ciMapFiles)<$ciMapFileN){
					$id = (string) $n;
					if($request->hasFile("ciMapFile".$id)){
						$tmp_filename = $_FILES["ciMapFile".$id]["name"];
						$request -> file("ciMapFile".$id)->move($filedir, $tmp_filename);
						$tmp_datatype="undefined";
						if($request->has("ciMapType".$id)){
							$tmp_datatype = $request->input("ciMapType".$id);
						}
						$ciMapFiles[] = $tmp_datatype."/user_upload/".$tmp_filename;
					}
					$n++;
				}
				$ciMapFiles = implode(":", $ciMapFiles);
			}

			$ciMapFDR = $request->input('ciMapFDR');
			if($request->has('ciMapPromWindow')){
				$ciMapPromWindow = $request->input('ciMapPromWindow');
			}else{
				$ciMapPromWindow = "250-500";
			}
			if($request->has('ciMapRoadmap')){
				$temp = $request->input('ciMapRoadmap');
				$ciMapRoadmap = [];
				foreach($temp as $dat){
					if($dat != "null"){
						$ciMapRoadmap[] = $dat;
					}
				}
				$ciMapRoadmap = implode(":", $ciMapRoadmap);
			}else{
				$ciMapRoadmap="NA";
			}
			if($request->has('ciMapEnhFilt')){$ciMapEnhFilt = 1;}
			else{$ciMapEnhFilt=0;}
			if($request->has('ciMapPromFilt')){$ciMapPromFilt = 1;}
			else{$ciMapPromFilt=0;}
		}else{
			$ciMapBuildin = "NA";
			$ciMapFDR = "NA";
			$ciMapPromWindow="NA";
			$ciMapRoadmap="NA";
			$ciMapEnhFilt=0;
			$ciMapPromFilt=0;
		}


		if($request -> has('ciMapCADDcheck')){
			$ciMapCADDth = $request -> input('ciMapCADDth');
		}else{
			$ciMapCADDth = 0;
		}
		if($request -> has('ciMapRDBcheck')){
			$ciMapRDBth = $request -> input('ciMapRDBth');
		}else{
			$ciMapRDBth = "NA";
		}
		if($request -> has('ciMapChr15check')){
			$temp = $request -> input('ciMapChr15Ts');
			$ciMapChr15 = [];
			foreach($temp as $ts){
				if($ts != "null"){
					$ciMapChr15[] = $ts;
				}
			}
			$ciMapChr15 = implode(":", $ciMapChr15);
			$ciMapChr15Max = $request -> input('ciMapChr15Max');
			$ciMapChr15Meth = $request -> input('ciMapChr15Meth');
		}else{
			$ciMapChr15 = "NA";
			$ciMapChr15Max = "NA";
			$ciMapChr15Meth = "NA";
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
		File::append($paramfile, "eacol=$eacol\n");
		File::append($paramfile, "neacol=$neacol\n");
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
		File::append($paramfile, "MHCopt=$MHCopt\n");
		File::append($paramfile, "extMHC=$extMHC\n");
		// File::append($paramfile, "include chromosome X\t$Xchr\n");
		File::append($paramfile, "genetype=$genetype\n");
		File::append($paramfile, "leadP=$leadP\n");
		File::append($paramfile, "r2=$r2\n");
		File::append($paramfile, "gwasP=$gwasP\n");
		File::append($paramfile, "refpanel=$refpanel\n");
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

		File::append($paramfile, "\n[ciMap]\n");
		File::append($paramfile, "ciMap=$ciMap\n");
		File::append($paramfile, "ciMapBuildin=$ciMapBuildin\n");
		File::append($paramfile, "ciMapFileN=$ciMapFileN\n");
		File::append($paramfile, "ciMapFiles=$ciMapFiles\n");
		File::append($paramfile, "ciMapFDR=$ciMapFDR\n");
		File::append($paramfile, "ciMapPromWindow=$ciMapPromWindow\n");
		File::append($paramfile, "ciMapRoadmap=$ciMapRoadmap\n");
		File::append($paramfile, "ciMapEnhFilt=$ciMapEnhFilt\n");
		File::append($paramfile, "ciMapPromFilt=$ciMapPromFilt\n");
		File::append($paramfile, "ciMapCADDth=$ciMapCADDth\n");
		File::append($paramfile, "ciMapRDBth=$ciMapRDBth\n");
		File::append($paramfile, "ciMapChr15=$ciMapChr15\n");
		File::append($paramfile, "ciMapChr15Max=$ciMapChr15Max\n");
		File::append($paramfile, "ciMapChr15Meth=$ciMapChr15Meth\n");
		return redirect("/snp2gene#joblist-panel");
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

	public function circos_chr(Request $request){
		$id = $request->input("id");
		$prefix = $request->input("prefix");
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/circos/';
		$files = File::glob($filedir."circos_chr*.png");
		for($i=0; $i<count($files); $i++){
			$files[$i] = preg_replace('/.+\/circos_chr(\d+)\.png/', '$1', $files[$i]);
		}
		$files = implode(":", $files);
		return $files;
	}

	public function circos_image($prefix, $id, $file){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/circos/';
		$f = File::get($filedir.$file);
		$type = File::mimeType($filedir.$file);

		return response($f)->header("Content-Type", $type);
	}

	public function circosDown(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/circos/';
		$type = $request->input('type');
		$zip = new \ZipArchive();
		if($prefix=="gwas"){
			$zipfile = $filedir."FUMA_gwas".$id."_circos_".$type.".zip";
		}else{
			$zipfile = $filedir."FUMA_job".$id."_circos_".$type.".zip";
		}

		$files = File::glob($filedir."*.".$type);
		for($i=0; $i<count($files); $i++){
			$files[$i] = preg_replace("/.+\/(\w+\.$type)/", '$1', $files[$i]);
		}

		if($type=="conf"){
			$tmp = File::glob($filedir."*.txt");
			foreach($tmp as $f){
				$f = preg_replace("/.+\/(\w+\.txt)/", '$1', $f);
				$files[] = $f;
			}
		}

		$zip -> open($zipfile, \ZipArchive::CREATE);
        foreach($files as $f){
          $zip->addFile($filedir.$f, $f);
        }
        $zip -> close();
        return response() -> download($zipfile);
	}

    public function deleteJob(Request $request){
		$jobID = $request->input('jobID');
		File::deleteDirectory(config('app.jobdir').'/jobs/'.$jobID);
		DB::table('SubmitJobs')->where('jobID', $jobID)->delete();
		return;
    }

	public function paramTable(Request $request){
		$filedir = $request -> input('filedir');

		$table = '<table class="table table-striped" style="width: 100%; margin-left: 10px; margin-right: 10px;ext-align: right;"><tbody>';
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);

		foreach($params as $key=>$value){
			$table .= "<tr><td>".$key.'</td><td style="word-break: break-all;">'.$value."</td></tr>";
		}

		$table .= "</tbody></table>";
		return $table;
    }

    public function sumTable(Request $request){
		$filedir = $request -> input('filedir');
		$table = '<table class="table table-bordered" style="width:auto;margin-right:auto; margin-left:auto; text-align: right;"><tbody>';
		$lines = file($filedir."summary.txt");
		foreach($lines as $l){
			$line = preg_split("/[\t]/", chop($l));
			$table .= "<tr><td>".$line[0]."</td><td>".$line[1]."</td></tr>";
		}
		$table .= "</tbody></table>";

		return $table;
    }

	public function locusPlot(Request $request){
      $id = $request->input('id');
	  $prefix = $request->input('prefix');
      $type = $request->input('type');
      $rowI = $request->input('rowI');
      $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

      $script = storage_path()."/scripts/locusPlot.py";
      $out = shell_exec("python $script $filedir $rowI $type");
      return $out;
    }

	public function manhattan($prefix, $id, $file){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		$f = $filedir.$file;
		if($file == "manhattan.txt"){
			if(file_exists($f)){
				$file = fopen($f, 'r');
				$header = fgetcsv($file, 0, "\t");
				$all_rows = [];
				while($row = fgetcsv($file, 0, "\t")){
					$row[0] = (int)$row[0];
					$row[1] = (int)$row[1];
					$row[2] = (float)$row[2];
					$all_rows[] = $row;
				}
				return json_encode($all_rows);
			}
		}else if($file == "magma.genes.out"){
			if(file_exists($f)){
				$file = fopen($f, 'r');
				$header = fgetcsv($file, 0, "\t");
				$all_rows = array();
				while($row = fgetcsv($file, 0, "\t")){
					if($row[1]=="X" | $row[1]=="x"){
						$row[1]=23;
					}
					$row[1] = (int)$row[1];
					$row[2] = (int)$row[2];
					$row[3] = (int)$row[3];
					$row[8] = (float)$row[8];
					$all_rows[] = array($row[1], $row[2], $row[3], $row[8], $row[9]);
				}
				return json_encode($all_rows);
			}
		}
    }

    public function QQplot($prefix, $id, $plot){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		if(strcmp($plot,"SNP")==0){
			$file=$filedir."QQSNPs.txt";
			if(file_exists($file)){
				$f = fopen($file, 'r');
				$all_row = array();
				$head = fgetcsv($f, 0, "\t");
				while($row = fgetcsv($f, 0, "\t")){
					$all_row[] = array_combine($head, $row);
				}
				return json_encode($all_row);
			}
		}else if(strcmp($plot,"Gene")==0){
			$file=$filedir."magma.genes.out";
			if(file_exists($file)){
				$f = fopen($file, 'r');
				$obs = array();
				$exp = array();
				$c = 0;
				fgetcsv($f, 0, "\t");
				while($row = fgetcsv($f, 0, "\t")){
					$c++;
					$obs[] = -log10($row[8]);
				}
				sort($obs);
				$step = (1-1/$c)/$c;
				$head = ["obs", "exp", "n"];
				$all_row = array();
				for($i=0; $i<$c; $i++){
					$all_row[] = array_combine($head, [$obs[$i], -log10(1-$i*$step), $i+1]);
				}
				return json_encode($all_row);
			}
		}
    }

    public function MAGMAtsplot($type, $prefix, $jobID){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$jobID.'/';
		$file = "";
		if($type=="general"){
			$file = $filedir."magma_exp_general.gcov.out";
		}else{
			$file = $filedir."magma_exp.gcov.out";
		}
		if(file_exists($file)){
			$f = fopen($file, 'r');
			$data = [];
			$p = [];
			while($row=fgetcsv($f)){
				$row = preg_split('/\s+/', $row[0]);
				if($row[0]=="#" || $row[0]=="COVAR"){
					continue;
				}else{
					$data[] = [$row[0], $row[5]];
					$p[$row[0]] =$row[5];
				}
			}
			asort($p);
			$order_p = [];
			$i = 0;
			foreach($p as $key => $val){
				$order_p[$key] = [$i];
				$i++;
			}
			ksort($p);
			$order_alph = [];
			$i = 0;
			foreach($p as $key => $val){
				$order_alph[$key] = $i;
				$i++;
			}
			$r = ["data"=>$data, "order"=>["p"=>$order_p, "alph"=>$order_alph]];
			return json_encode($r);
		}else{
			return;
		}
    }

    public function annotPlot(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$type = $request -> input('annotPlotSelect');
		$rowI = $request -> input('annotPlotRow');

		$GWAS=0;
		$CADD=0;
		$RDB=0;
		$Chr15=0;
		$eqtl=0;
		$ci=0;
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
		if($request -> has('annotPlot_ci')){$ci=1;}

		return view('pages.annotPlot', ['id'=>$id, 'prefix'=>$prefix, 'type'=>$type, 'rowI'=>$rowI,
			'GWASplot'=>$GWAS, 'CADDplot'=>$CADD, 'RDBplot'=>$RDB, 'eqtlplot'=>$eqtl,
			'ciplot'=>$ci, 'Chr15'=>$Chr15, 'Chr15cells'=>$Chr15cells]);
    }

	public function annotPlotGetData(Request $request){
		$id = $request->input("id");
		$prefix = $request->input("prefix");
		$type = $request->input("type");
		$rowI = $request->input("rowI");
		$GWASplot = $request->input("GWASplot");
		$CADDplot = $request->input("CADDplot");
		$RDBplot = $request->input("RDBplot");
		$eqtlplot = $request->input("eqtlplot");
		$ciplot = $request->input("ciplot");
		$Chr15 = $request->input("Chr15");
		$Chr15cells = $request->input("Chr15cells");

		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		$script = storage_path()."/scripts/annotPlot.py";
	    $data = shell_exec("python $script $filedir $type $rowI $GWASplot $CADDplot $RDBplot $eqtlplot $ciplot $Chr15 $Chr15cells");
		return $data;
	}

	public function annotPlotGetGenes(Request $request){
		$id = $request->input("id");
		$prefix = $request->input("prefix");
		$chrom = $request->input("chrom");
		$eqtlplot = $request->input("eqtlplot");
		$ciplot = $request->input("ciplot");
		$xMin = $request->input("xMin");
		$xMax = $request->input("xMax");
		$eqtlgenes = $request->input("eqtlgenes");

		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		$script = storage_path()."/scripts/annotPlot.R";
		$data = shell_exec("Rscript $script $filedir $chrom $xMin $xMax $eqtlgenes $eqtlplot $ciplot");
		$data = explode("\n", $data);
		$data = $data[count($data)-1];
		return $data;
	}

	public function legendText($file){
		$f = storage_path().'/legends/'.$file;
		if(file_exists($f)){
			$file = fopen($f, 'r');
			$header = fgetcsv($file, 0, "\t");
			$all_rows = array();
			while($row = fgetcsv($file, 0, "\t")){
				$all_rows[] = array_combine($header, $row);
			}
			return json_encode($all_rows);
		}
    }

	public function filedown(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
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
		if($request -> has('eqtlfile')){
			if(File::exists($filedir."eqtl.txt")){
				$files[] = "eqtl.txt";
			}
		}
		if($request -> has('cifile')){
			if(File::exists($filedir."ci.txt")){
				$files[] = "ci.txt";
				$files[] = "ciSNPs.txt";
				$files[] = "ciProm.txt";
			}
		}
		// if($request -> has('exacfile')){$files[] = $filedir."ExAC.txt";}
		if($request -> has('gwascatfile')){$files[] = "gwascatalog.txt";}
		if($request -> has('magmafile')){
			$files[] = "magma.genes.out";
			if(File::exists($filedir."magma.sets.out")){
				$files[] = "magma.genes.raw";
				$files[] = "magma.sets.out";
				if(File::exists($filedir."magma.setgenes.out")){
					$files[] = "magma.setgenes.out";
				}
			}
			if(File::exists($filedir."magma_exp.gcov.out")){
				$files[] = "magma_exp.gcov.out";
				$files[] = "magma_exp_general.gcov.out";
			}
		}

		$zip = new \ZipArchive();
		if($prefix=="gwas"){
			$zipfile = $filedir."FUMA_gwas".$id.".zip";
		}else{
			$zipfile = $filedir."FUMA_job".$id.".zip";
		}

		if(File::exists($zipfile)){
			File::delete($zipfile);
		}
		$zip -> open($zipfile, \ZipArchive::CREATE);
		$zip->addFile(storage_path().'/README', "README");
		foreach($files as $f){
			$zip->addFile($filedir.$f, $f);
		}
		$zip -> close();
		return response() -> download($zipfile);
    }

}
