<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SubmitJob;
// use fuma\Http\Requests;
// use Symfony\Component\Process\Process;
// use View;
use Auth;
// use Storage;
use File;
use Helper;
// use JavaScript;
// use Session;
// use Mail;
// use fuma\User;
use App\Jobs\Snp2geneProcess;
use App\Jobs\GeneMapProcess;

class S2GController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($id = null)
    {
        return view('pages.snp2gene', ['id' => $id, 'status' => null, 'page' => 'snp2gene', 'prefix' => 'jobs']);
    }

    public function authcheck($jobID)
    {
        $email = Auth::user()->email;
        $check = DB::table('SubmitJobs')
            ->where('jobID', $jobID)
            ->first();
        if ($check->email == $email) {
            return view('pages.snp2gene', ['id' => $jobID, 'status' => 'jobquery', 'page' => 'snp2gene', 'prefix' => 'jobs']);
        } else {
            return view('pages.snp2gene', ['id' => null, 'status' => null, 'page' => 'snp2gene', 'prefix' => 'jobs']);
        }
    }

    public function getJobList()
    {
        $email = Auth::user()->email;

        if ($email) {
            $results = SubmitJob::where('email', $email)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $results = array();
        }

        $this->queueNewJobs();
        $this->queueGeneMap();

        return response()->json($results);
    }


    /**
     * Return the number of scheduled jobs for the current user
     * including all QUEUED RUNNING and NEW jobs.
     */
    public function getNumberScheduledJobs()
    {
        $email = Auth::user()->email;
        $results = array();
        if ($email) {
            $results = SubmitJob::where('email', $email)
                ->whereIn('status', ['QUEUED', 'RUNNING', 'NEW'])
                ->get();
        }

        return count($results);
    }

    public function getPublicIDs()
    {
        $email = Auth::user()->email;
        $results = array();

        if ($email) {
            $rows = DB::select('SELECT jobID from PublicResults WHERE email=?', [$email]);

            // TODO: make a new model PublicResults -> uncomment the following -> delete the DB::select above
            // $rows = PublicResults::where('email', $email)
            //     ->get(['jobID']);

            foreach ($rows as $r) {
                $results[] = (int) $r->jobID;
            }
        }

        return response()->json($results);
    }

    public function getjobIDs()
    {
        $email = Auth::user()->email;
        // $results = DB::select('SELECT jobID, title FROM SubmitJobs WHERE email=?', [$email]);
        $results = SubmitJob::where('email', $email)
            ->get(['jobID', 'title']);
        return $results;
    }

    public function getGeneMapIDs()
    {   // TODO: the name of this func should be getFinishedjobsIDs since retrieves the jobes with ok status
        // so to snp2gene and then to the tab redo gene mapping, the result of this func is shown is the 
        // dropdown jobID 
        $email = Auth::user()->email;
        // $results = DB::select('SELECT jobID, title FROM SubmitJobs WHERE email=? AND status="OK"', [$email]);
        $results = SubmitJob::where('email', $email)
            ->where('status', 'OK')
            ->get(['jobID', 'title']);
        return $results;
    }

    public function loadParams(Request $request)
    {
        $id = $request->input("id");
        $filedir = config('app.jobdir') . '/jobs/' . $id . '/';
        $params = parse_ini_file($filedir . "params.config", false, INI_SCANNER_RAW);
        return json_encode($params);
    }

    public function queueNewJobs()
    {
        $user = Auth::user();
        $email = $user->email;
        $newJobs = DB::table('SubmitJobs')->where('email', $email)->where('status', 'NEW')->get()->all();
        if (count($newJobs) > 0) {
            foreach ($newJobs as $job) {
                $jobID = $job->jobID;
                DB::table('SubmitJobs')
                    ->where('jobID', $jobID)
                    ->update(['status' => 'QUEUED']);
                Snp2geneProcess::dispatch($user, $jobID);
            }
        }
        return;
    }

    public function queueGeneMap()
    {
        $user = Auth::user();
        $email = $user->email;
        $newJobs = DB::table('SubmitJobs')
            ->where('email', $email)
            ->where('status', 'NEW_geneMap')
            ->get()
            ->all();
        if (count($newJobs) > 0) {
            foreach ($newJobs as $job) {
                $jobID = $job->jobID;
                DB::table('SubmitJobs')->where('jobID', $jobID)
                    ->update(['status' => 'QUEUED']);
                GeneMapProcess::dispatch($user, $jobID);
            }
        }
        return;
    }

    public function checkJobStatus($jobID)
    {
        $email = Auth::user()->email;

        $job = SubmitJob::where('jobID', $jobID)
            ->where('email', $email)
            ->first();
        if (!$job) {
            return "Notfound";
        }
        return $job->status;
    }

    public function getParams(Request $request)
    {
        $jobID = $request->input('jobID');
        $date = date('Y-m-d H:i:s');
        DB::table('SubmitJobs')
            ->where('jobID', $jobID)
            ->update(['updated_at' => $date]);

        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';
        $params = parse_ini_file($filedir . "params.config", false, INI_SCANNER_RAW);
        $posMap = $params['posMap'];
        $eqtlMap = $params['eqtlMap'];
        $orcol = $params['orcol'];
        $becol = $params['becol'];
        $secol = $params['secol'];
        $ciMap = 0;
        if (array_key_exists('ciMap', $params)) {
            $ciMap = $params['ciMap'];
        }
        $magma = $params['magma'];
        return "$posMap:$eqtlMap:$ciMap:$orcol:$becol:$secol:$magma";
    }

    /**
     * Returns the queue cap value
     * 
     * A null return indicates no timeout
     */
    public function getQueueCap()
    {
        $queue_cap = config('queue.jobLimits.queue_cap', 10);
        return $queue_cap;
    }

    public function newJob(Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $email = Auth::user()->email;
        // Implement the cap on max jobs in queue
        $numSchedJobs = $this->getNumberScheduledJobs();
        $queueCap = $this->getQueueCap();
        if (!is_null($queueCap) && ($numSchedJobs >= $queueCap)) {
            // flash a warning to the user about the queue cap
            $name = Auth::user()->name;
            $message = <<<MSG
                Job submission temporarily blocked for user: $name!<br>
                The maximum number of jobs: $queueCap, has been reached. <br>
                Wait for some jobs to complete or delete stalled jobs.
                MSG;
            $request->session()->flash("alert-warning", $message);
            return redirect()->back();
            //return view('pages.snp2gene', ['id' => null, 'status'=> null, 'page'=>'snp2gene', 'prefix'=>'jobs']);
        }

        if ($request->hasFile('GWASsummary')) {
            $type = mime_content_type($_FILES["GWASsummary"]["tmp_name"]);
            if ($type != "text/plain" && $type != "application/zip" && $type != "application/x-gzip") {
                $jobID = null;
                return view('pages.snp2gene', ['id' => null, 'status' => 'fileFormatGWAS', 'page' => 'snp2gene', 'prefix' => 'jobs']);
            }
        }
        if ($request->hasFile('leadSNPs')) {
            if (mime_content_type($_FILES["leadSNPs"]["tmp_name"]) != "text/plain") {
                $jobID = null;
                return redirect('pages.snp2gene')->with(['id' => null, 'status' => 'fileFormatLead', 'page' => 'snp2gene', 'prefix' => 'jobs']);
            }
        }
        if ($request->hasFile('regions')) {
            if (mime_content_type($_FILES["regions"]["tmp_name"]) != "text/plain") {
                $jobID = null;
                return view('pages.snp2gene', ['id' => null, 'status' => 'fileFormatRegions', 'page' => 'snp2gene', 'prefix' => 'jobs']);
            }
        }

        if ($request->filled("NewJobTitle")) {
            $jobtitle = $request->input('NewJobTitle');
        } else {
            $jobtitle = "None";
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
        $filedir = config('app.jobdir') . '/jobs/' . $jobID;
        Storage::makeDirectory($filedir, 0775, true, true);

        // upload input Filesystem
        $leadSNPs = "input.lead";
        $GWAS = "input.gwas";
        $regions = "input.regions";
        $leadSNPsfileup = 0;
        $GWASfileup = 0;
        $regionsfileup = 0;

        // GWAS smmary stats file
        if ($request->hasFile('GWASsummary')) {
            $type = mime_content_type($_FILES["GWASsummary"]["tmp_name"]);
            if ($type == "text/plain") {
                $request->file('GWASsummary')->move($filedir, $GWAS);
            } else if ($type == "application/zip") {
                $request->file('GWASsummary')->move($filedir, "temp.zip");
                $zip = new \ZipArchive;
                $zip->open($filedir . '/temp.zip');
                $zf = $zip->getNameIndex(0);
                $zip->extractTo($filedir);
                Storage::move($filedir . '/' . $zf, $filedir . '/' . $GWAS);
                system("rm $filedir/temp.zip");
            } else {
                $f = $_FILES["GWASsummary"]["name"];
                $request->file('GWASsummary')->move($filedir, $f);
                system("gzip -cd $filedir/$f > $filedir/$GWAS");
                system("rm $filedir/$f");
            }
            $GWASfileup = 1;
        } else if ($request->has('egGWAS')) {
            $exfile = config('app.jobdir') . '/example/CD.gwas';
            Storage::copy($exfile, $filedir . '/input.gwas');
            $GWASfileup = 1;
        }

        // pre-defined lead SNPS file
        if ($request->hasFile('leadSNPs')) {
            $type = mime_content_type($_FILES["leadSNPs"]["tmp_name"]);
            if ($type == "text/plain") {
                $request->file('leadSNPs')->move($filedir, $leadSNPs);
            } else if ($type == "application/zip") {
                $request->file('leadSNPs')->move($filedir, "temp.zip");
                $zip = new \ZipArchive;
                $zip->open($filedir . '/temp.zip');
                $zf = $zip->getNameIndex(0);
                $zip->extractTo($filedir);
                Storage::move($filedir . '/' . $zf, $filedir . '/' . $leadSNPs);
            } else {
                $f = $_FILES["leadSNPs"]["name"];
                $request->file('leadSNPs')->move($filedir, $f);
                system("gzip -cd $filedir/$f > $filedir/$leadSNPs");
            }
            $leadSNPsfileup = 1;
        }

        if ($leadSNPsfileup == 1 && $request->has('addleadSNPs')) {
            $addleadSNPs = 1;
        } else if ($leadSNPsfileup == 0) {
            $addleadSNPs = 1;
        } else {
            $addleadSNPs = 0;
        }

        // pre-defined genomic region file
        if ($request->hasFile('regions')) {
            $type = mime_content_type($_FILES["regions"]["tmp_name"]);
            if ($type == "text/plain") {
                $request->file('regions')->move($filedir, $regions);
            } else if ($type == "application/zip") {
                $request->file('regions')->move($filedir, "temp.zip");
                $zip = new \ZipArchive;
                $zip->open($filedir . '/temp.zip');
                $zf = $zip->getNameIndex(0);
                $zip->extractTo($filedir);
                Storage::move($filedir . '/' . $zf, $filedir . '/' . $regions);
            } else {
                $f = $_FILES["regions"]["name"];
                $request->file('regions')->move($filedir, $f);
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

        if ($request->filled('chrcol')) {
            $chrcol = $request->input('chrcol');
        }
        if ($request->filled('poscol')) {
            $poscol = $request->input('poscol');
        }
        if ($request->filled('rsIDcol')) {
            $rsIDcol = $request->input('rsIDcol');
        }
        if ($request->filled('pcol')) {
            $pcol = $request->input('pcol');
        }
        if ($request->filled('eacol')) {
            $eacol = $request->input('eacol');
        }
        if ($request->filled('neacol')) {
            $neacol = $request->input('neacol');
        }
        if ($request->filled('orcol')) {
            $orcol = $request->input('orcol');
        }
        if ($request->filled('becol')) {
            $becol = $request->input('becol');
        }
        if ($request->filled('secol')) {
            $secol = $request->input('secol');
        }

        // MHC region
        if ($request->filled('MHCregion')) {
            $exMHC = 1;
            $MHCopt = $request->input('MHCopt');
        } else {
            $exMHC = 0;
            $MHCopt = "NA";
        }
        $extMHC = $request->input('extMHCregion');
        if ($extMHC == null) {
            $extMHC = "NA";
        }

        // gene type
        $ensembl = $request->input('ensembl');
        $genetype = implode(":", $request->input('genetype'));

        // others
        $N = "NA";
        $Ncol = "NA";
        if ($request->filled('N')) {
            $N = $request->input('N');
        } else if ($request->filled('Ncol')) {
            $Ncol = $request->input('Ncol');
        }
        $leadP = $request->input('leadP');
        $gwasP = $request->input('gwasP');
        $r2 = $request->input('r2');
        $r2_2 = $request->input('r2_2');
        $refpanel = $request->input('refpanel');
        $pop = preg_replace('/.+\/.+\/(.+)/', '$1', $refpanel);
        $refpanel = preg_replace('/(.+\/.+)\/.+/', '$1', $refpanel);
        $refSNPs = $request->input('refSNPs');
        if (strcmp($refSNPs, "Yes") == 0) {
            $refSNPs = 1;
        } else {
            $refSNPs = 0;
        }
        $maf = $request->input('maf');
        $mergeDist = $request->input('mergeDist');

        // positional mapping
        $posMapAnnot = "NA";
        $posMapWindowSize = "NA";
        if ($request->filled('posMap')) {
            $posMap = 1;
            if ($request->filled('posMapWindow')) {
                $posMapWindowSize = $request->input('posMapWindow');
                $posMapAnnot = "NA";
            } else {
                $posMapWindowSize = "NA";
                $posMapAnnot = implode(":", $request->input('posMapAnnot'));
            }
        } else {
            $posMap = 0;
        }

        if ($request->filled('posMapCADDcheck')) {
            $posMapCADDth = $request->input('posMapCADDth');
        } else {
            $posMapCADDth = 0;
        }
        if ($request->filled('posMapRDBcheck')) {
            $posMapRDBth = $request->input('posMapRDBth');
        } else {
            $posMapRDBth = "NA";
        }

        if ($request->filled('posMapChr15check')) {
            $temp = $request->input('posMapChr15Ts');
            $posMapChr15 = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $posMapChr15[] = $ts;
                }
            }
            $posMapChr15 = implode(":", $posMapChr15);
            $posMapChr15Max = $request->input('posMapChr15Max');
            $posMapChr15Meth = $request->input('posMapChr15Meth');
        } else {
            $posMapChr15 = "NA";
            $posMapChr15Max = "NA";
            $posMapChr15Meth = "NA";
        }
        $posMapAnnoDs = $request->input('posMapAnnoDs', []);
        if (count($posMapAnnoDs) == 0) {
            $posMapAnnoDs = "NA";
        } else {
            $temp = [];
            foreach ($posMapAnnoDs as $ds) {
                if ($ds != "null") {
                    $temp[] = $ds;
                }
            }
            $posMapAnnoDs = implode(":", $temp);
        }
        $posMapAnnoMeth = $request->input('posMapAnnoMeth');

        // eqtl mapping
        if ($request->filled('eqtlMap')) {
            $eqtlMap = 1;
            $temp = $request->input('eqtlMapTs');
            // $eqtlMapGts = $request -> input('eqtlMapGts');
            $eqtlMapTs = [];
            $eqtlMapGts = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $eqtlMapTs[] = $ts;
                }
            }
            if (!empty($eqtlMapTs) && !empty($eqtlMapGts)) {
                $eqtlMapTs = implode(":", $eqtlMapTs);
                $eqtlMapGts = implode(":", $eqtlMapGts);
                $eqtlMaptss = implode(":", array($eqtlMapTs, $eqtlMapGts));
            } else if (!empty($eqtlMapTs)) {
                $eqtlMaptss = implode(":", $eqtlMapTs);
            } else {
                $eqtlMaptss = implode(":", $eqtlMapGts);
            }
        } else {
            $eqtlMap = 0;
            $eqtlMaptss = "NA";
        }
        if ($request->filled('sigeqtlCheck')) {
            $sigeqtl = 1;
            $eqtlP = 1;
        } else {
            $sigeqtl = 0;
            $eqtlP = $request->input('eqtlP');
        }
        if ($request->filled('eqtlMapCADDcheck')) {
            $eqtlMapCADDth = $request->input('eqtlMapCADDth');
        } else {
            $eqtlMapCADDth = 0;
        }
        if ($request->filled('eqtlMapRDBcheck')) {
            $eqtlMapRDBth = $request->input('eqtlMapRDBth');
        } else {
            $eqtlMapRDBth = "NA";
        }
        if ($request->filled('eqtlMapChr15check')) {
            $temp = $request->input('eqtlMapChr15Ts');
            $eqtlMapChr15 = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $eqtlMapChr15[] = $ts;
                }
            }
            $eqtlMapChr15 = implode(":", $eqtlMapChr15);
            $eqtlMapChr15Max = $request->input('eqtlMapChr15Max');
            $eqtlMapChr15Meth = $request->input('eqtlMapChr15Meth');
        } else {
            $eqtlMapChr15 = "NA";
            $eqtlMapChr15Max = "NA";
            $eqtlMapChr15Meth = "NA";
        }
        $eqtlMapAnnoDs = $request->input('eqtlMapAnnoDs', []);
        if (count($eqtlMapAnnoDs) == 0) {
            $eqtlMapAnnoDs = "NA";
        } else {
            $temp = [];
            foreach ($eqtlMapAnnoDs as $ds) {
                if ($ds != "null") {
                    $temp[] = $ds;
                }
            }
            $eqtlMapAnnoDs = implode(":", $temp);
        }
        $eqtlMapAnnoMeth = $request->input('eqtlMapAnnoMeth');

        // chromatin interaction mapping
        $ciMap = 0;
        $ciMapFileN = 0;
        $ciMapFiles = "NA";
        if ($request->filled('ciMap')) {
            $ciMap = 1;
            if ($request->filled('ciMapBuiltin')) {
                $temp = $request->input('ciMapBuiltin');
                $ciMapBuiltin = [];
                foreach ($temp as $dat) {
                    if ($dat != "null") {
                        $ciMapBuiltin[] = $dat;
                    }
                }
                $ciMapBuiltin = implode(":", $ciMapBuiltin);
            } else {
                $ciMapBuiltin = "NA";
            }

            $ciMapFileN = (int)$request->input("ciFileN");
            if ($ciMapFileN > 0) {
                $ciMapFiles = [];
                $n = 1;
                while (count($ciMapFiles) < $ciMapFileN) {
                    $id = (string) $n;
                    if ($request->hasFile("ciMapFile" . $id)) {
                        $tmp_filename = $_FILES["ciMapFile" . $id]["name"];
                        $request->file("ciMapFile" . $id)->move($filedir, $tmp_filename);
                        $tmp_datatype = "undefined";
                        if ($request->filled("ciMapType" . $id)) {
                            $tmp_datatype = $request->input("ciMapType" . $id);
                        }
                        $ciMapFiles[] = $tmp_datatype . "/user_upload/" . $tmp_filename;
                    }
                    $n++;
                }
                $ciMapFiles = implode(":", $ciMapFiles);
            }

            $ciMapFDR = $request->input('ciMapFDR');
            if ($request->filled('ciMapPromWindow')) {
                $ciMapPromWindow = $request->input('ciMapPromWindow');
            } else {
                $ciMapPromWindow = "250-500";
            }
            if ($request->filled('ciMapRoadmap')) {
                $temp = $request->input('ciMapRoadmap');
                $ciMapRoadmap = [];
                foreach ($temp as $dat) {
                    if ($dat != "null") {
                        $ciMapRoadmap[] = $dat;
                    }
                }
                $ciMapRoadmap = implode(":", $ciMapRoadmap);
            } else {
                $ciMapRoadmap = "NA";
            }
            if ($request->filled('ciMapEnhFilt')) {
                $ciMapEnhFilt = 1;
            } else {
                $ciMapEnhFilt = 0;
            }
            if ($request->filled('ciMapPromFilt')) {
                $ciMapPromFilt = 1;
            } else {
                $ciMapPromFilt = 0;
            }
        } else {
            $ciMapBuiltin = "NA";
            $ciMapFDR = "NA";
            $ciMapPromWindow = "NA";
            $ciMapRoadmap = "NA";
            $ciMapEnhFilt = 0;
            $ciMapPromFilt = 0;
        }


        if ($request->filled('ciMapCADDcheck')) {
            $ciMapCADDth = $request->input('ciMapCADDth');
        } else {
            $ciMapCADDth = 0;
        }
        if ($request->filled('ciMapRDBcheck')) {
            $ciMapRDBth = $request->input('ciMapRDBth');
        } else {
            $ciMapRDBth = "NA";
        }
        if ($request->filled('ciMapChr15check')) {
            $temp = $request->input('ciMapChr15Ts');
            $ciMapChr15 = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $ciMapChr15[] = $ts;
                }
            }
            $ciMapChr15 = implode(":", $ciMapChr15);
            $ciMapChr15Max = $request->input('ciMapChr15Max');
            $ciMapChr15Meth = $request->input('ciMapChr15Meth');
        } else {
            $ciMapChr15 = "NA";
            $ciMapChr15Max = "NA";
            $ciMapChr15Meth = "NA";
        }
        $ciMapAnnoDs = $request->input('ciMapAnnoDs', []);
        if (count($ciMapAnnoDs) == 0) {
            $ciMapAnnoDs = "NA";
        } else {
            $temp = [];
            foreach ($ciMapAnnoDs as $ds) {
                if ($ds != "null") {
                    $temp[] = $ds;
                }
            }
            $ciMapAnnoDs = implode(":", $temp);
        }
        $ciMapAnnoMeth = $request->input('ciMapAnnoMeth');

        // MAGMA option
        $magma = 0;
        $magma_window = "NA";
        $magma_exp = "NA";
        if ($request->filled('magma')) {
            $magma = 1;
            $magma_window = $request->input("magma_window");
            $magma_exp = implode(":", $request->input('magma_exp'));
        }

        $app_config = parse_ini_file(Helper::scripts_path('app.config'), false, INI_SCANNER_RAW);

        // write parameter into a file
        $paramfile = $filedir . '/params.config';
        Storage::put($paramfile, "[jobinfo]\n");
        Storage::append($paramfile, "created_at=$date\n");
        Storage::append($paramfile, "title=$jobtitle\n");

        Storage::append($paramfile, "\n[version]\n");
        Storage::append($paramfile, "FUMA=" . $app_config['FUMA'] . "\n");
        Storage::append($paramfile, "MAGMA=" . $app_config['MAGMA'] . "\n");
        Storage::append($paramfile, "GWAScatalog=" . $app_config['GWAScatalog'] . "\n");
        Storage::append($paramfile, "ANNOVAR=" . $app_config['ANNOVAR'] . "\n");

        Storage::append($paramfile, "\n[inputfiles]\n");
        if ($request->hasFile('GWASsummary')) {
            Storage::append($paramfile, "gwasfile=" . $_FILES["GWASsummary"]["name"] . "\n");
        } else {
            Storage::append($paramfile, "gwasfile=fuma.example.CD.gwas\n");
        }
        Storage::append($paramfile, "chrcol=$chrcol\n");
        Storage::append($paramfile, "poscol=$poscol\n");
        Storage::append($paramfile, "rsIDcol=$rsIDcol\n");
        Storage::append($paramfile, "pcol=$pcol\n");
        Storage::append($paramfile, "eacol=$eacol\n");
        Storage::append($paramfile, "neacol=$neacol\n");
        Storage::append($paramfile, "orcol=$orcol\n");
        Storage::append($paramfile, "becol=$becol\n");
        Storage::append($paramfile, "secol=$secol\n");
        // Storage::append($paramfile, "mafcol=$mafcol\n");

        if ($leadSNPsfileup == 1) {
            Storage::append($paramfile, "leadSNPsfile=" . $_FILES["leadSNPs"]["name"] . "\n");
        } else {
            Storage::append($paramfile, "leadSNPsfile=NA\n");
        }
        Storage::append($paramfile, "addleadSNPs=$addleadSNPs\n");
        if ($regionsfileup == 1) {
            Storage::append($paramfile, "regionsfile=" . $_FILES["regions"]["name"] . "\n");
        } else {
            Storage::append($paramfile, "regionsfile=NA\n");
        }

        Storage::append($paramfile, "\n[params]\n");
        Storage::append($paramfile, "N=$N\n");
        Storage::append($paramfile, "Ncol=$Ncol\n");
        Storage::append($paramfile, "exMHC=$exMHC\n");
        Storage::append($paramfile, "MHCopt=$MHCopt\n");
        Storage::append($paramfile, "extMHC=$extMHC\n");
        Storage::append($paramfile, "ensembl=$ensembl\n");
        Storage::append($paramfile, "genetype=$genetype\n");
        Storage::append($paramfile, "leadP=$leadP\n");
        Storage::append($paramfile, "gwasP=$gwasP\n");
        Storage::append($paramfile, "r2=$r2\n");
        Storage::append($paramfile, "r2_2=$r2_2\n");
        Storage::append($paramfile, "refpanel=$refpanel\n");
        Storage::append($paramfile, "pop=$pop\n");
        Storage::append($paramfile, "MAF=$maf\n");
        Storage::append($paramfile, "refSNPs=$refSNPs\n");
        Storage::append($paramfile, "mergeDist=$mergeDist\n");

        Storage::append($paramfile, "\n[magma]\n");
        Storage::append($paramfile, "magma=$magma\n");
        Storage::append($paramfile, "magma_window=$magma_window\n");
        Storage::append($paramfile, "magma_exp=$magma_exp\n");

        Storage::append($paramfile, "\n[posMap]\n");
        Storage::append($paramfile, "posMap=$posMap\n");
        // Storage::append($paramfile, "posMapWindow=$posMapWindow\n");
        Storage::append($paramfile, "posMapWindowSize=$posMapWindowSize\n");
        Storage::append($paramfile, "posMapAnnot=$posMapAnnot\n");
        Storage::append($paramfile, "posMapCADDth=$posMapCADDth\n");
        Storage::append($paramfile, "posMapRDBth=$posMapRDBth\n");
        Storage::append($paramfile, "posMapChr15=$posMapChr15\n");
        Storage::append($paramfile, "posMapChr15Max=$posMapChr15Max\n");
        Storage::append($paramfile, "posMapChr15Meth=$posMapChr15Meth\n");
        Storage::append($paramfile, "posMapAnnoDs=$posMapAnnoDs\n");
        Storage::append($paramfile, "posMapAnnoMeth=$posMapAnnoMeth\n");

        Storage::append($paramfile, "\n[eqtlMap]\n");
        Storage::append($paramfile, "eqtlMap=$eqtlMap\n");
        Storage::append($paramfile, "eqtlMaptss=$eqtlMaptss\n");
        Storage::append($paramfile, "eqtlMapSig=$sigeqtl\n");
        Storage::append($paramfile, "eqtlMapP=$eqtlP\n");
        Storage::append($paramfile, "eqtlMapCADDth=$eqtlMapCADDth\n");
        Storage::append($paramfile, "eqtlMapRDBth=$eqtlMapRDBth\n");
        Storage::append($paramfile, "eqtlMapChr15=$eqtlMapChr15\n");
        Storage::append($paramfile, "eqtlMapChr15Max=$eqtlMapChr15Max\n");
        Storage::append($paramfile, "eqtlMapChr15Meth=$eqtlMapChr15Meth\n");
        Storage::append($paramfile, "eqtlMapAnnoDs=$eqtlMapAnnoDs\n");
        Storage::append($paramfile, "eqtlMapAnnoMeth=$eqtlMapAnnoMeth\n");

        Storage::append($paramfile, "\n[ciMap]\n");
        Storage::append($paramfile, "ciMap=$ciMap\n");
        Storage::append($paramfile, "ciMapBuiltin=$ciMapBuiltin\n");
        Storage::append($paramfile, "ciMapFileN=$ciMapFileN\n");
        Storage::append($paramfile, "ciMapFiles=$ciMapFiles\n");
        Storage::append($paramfile, "ciMapFDR=$ciMapFDR\n");
        Storage::append($paramfile, "ciMapPromWindow=$ciMapPromWindow\n");
        Storage::append($paramfile, "ciMapRoadmap=$ciMapRoadmap\n");
        Storage::append($paramfile, "ciMapEnhFilt=$ciMapEnhFilt\n");
        Storage::append($paramfile, "ciMapPromFilt=$ciMapPromFilt\n");
        Storage::append($paramfile, "ciMapCADDth=$ciMapCADDth\n");
        Storage::append($paramfile, "ciMapRDBth=$ciMapRDBth\n");
        Storage::append($paramfile, "ciMapChr15=$ciMapChr15\n");
        Storage::append($paramfile, "ciMapChr15Max=$ciMapChr15Max\n");
        Storage::append($paramfile, "ciMapChr15Meth=$ciMapChr15Meth\n");
        Storage::append($paramfile, "ciMapAnnoDs=$ciMapAnnoDs\n");
        Storage::append($paramfile, "ciMapAnnoMeth=$ciMapAnnoMeth\n");
        return redirect("/snp2gene#joblist-panel");
    }

    public function geneMap(Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $oldID = $request->input("geneMapID");
        $email = Auth::user()->email;

        $jobtitle = "";
        if ($request->filled("geneMapTitle")) {
            $jobtitle = $request->input('geneMapTitle');
        }
        $jobtitle .= "_copied_" . $oldID;

        // Create new job in database
        $submitJob = new SubmitJob;
        $submitJob->email = $email;
        $submitJob->title = $jobtitle;
        $submitJob->status = 'NEW_geneMap';
        $submitJob->save();

        // Get jobID (automatically generated)
        $jobID = $submitJob->jobID;

        // copie old job to new ID
        $filedir = config('app.jobdir') . '/jobs/' . $jobID;
        // $oldfiledir = config('app.jobdir').'/jobs/'.$oldID;
        // File::makeDirectory($filedir, $mode = 0755, $recursive = true);
        File::copyDirectory(config('app.jobdir') . '/jobs/' . $oldID, $filedir);
        system("rm $filedir/*.svg $filedir/*.png $filedir/*.pdf $filedir/*.jpg");
        system("rm -r $filedir/circos");

        // positional mapping
        $posMap = 0;
        $posMapWindowSize = "NA";
        $posMapAnnot = "NA";
        if ($request->filled('geneMap_posMap')) {
            $posMap = 1;
            if ($request->filled('geneMap_posMapWindow')) {
                $posMapWindowSize = $request->input('geneMap_posMapWindow');
                $posMapAnnot = "NA";
            } else {
                $posMapWindowSize = "NA";
                $posMapAnnot = implode(":", $request->input('geneMap_posMapAnnot'));
            }
        }
        if ($request->filled('geneMap_posMapCADDcheck')) {
            $posMapCADDth = $request->input('geneMap_posMapCADDth');
        } else {
            $posMapCADDth = 0;
        }
        if ($request->filled('geneMap_posMapRDBcheck')) {
            $posMapRDBth = $request->input('geneMap_posMapRDBth');
        } else {
            $posMapRDBth = "NA";
        }

        if ($request->filled('geneMap_posMapChr15check')) {
            $temp = $request->input('geneMap_posMapChr15Ts');
            $posMapChr15 = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $posMapChr15[] = $ts;
                }
            }
            $posMapChr15 = implode(":", $posMapChr15);
            $posMapChr15Max = $request->input('geneMap_posMapChr15Max');
            $posMapChr15Meth = $request->input('geneMap_posMapChr15Meth');
        } else {
            $posMapChr15 = "NA";
            $posMapChr15Max = "NA";
            $posMapChr15Meth = "NA";
        }
        $posMapAnnoDs = $request->input('geneMap_posMapAnnoDs', []);
        if (count($posMapAnnoDs) == 0) {
            $posMapAnnoDs = "NA";
        } else {
            $temp = [];
            foreach ($posMapAnnoDs as $ds) {
                if ($ds != "null") {
                    $temp[] = $ds;
                }
            }
            $posMapAnnoDs = implode(":", $temp);
        }
        $posMapAnnoMeth = $request->input('geneMap_posMapAnnoMeth');

        // eqtl mapping
        if ($request->filled('geneMap_eqtlMap')) {
            $eqtlMap = 1;
            $temp = $request->input('geneMap_eqtlMapTs');
            // $eqtlMapGts = $request -> input('geneMap_eqtlMapGts');
            $eqtlMapTs = [];
            $eqtlMapGts = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $eqtlMapTs[] = $ts;
                }
            }
            if (!empty($eqtlMapTs) && !empty($eqtlMapGts)) {
                $eqtlMapTs = implode(":", $eqtlMapTs);
                $eqtlMapGts = implode(":", $eqtlMapGts);
                $eqtlMaptss = implode(":", array($eqtlMapTs, $eqtlMapGts));
            } else if (!empty($eqtlMapTs)) {
                $eqtlMaptss = implode(":", $eqtlMapTs);
            } else {
                $eqtlMaptss = implode(":", $eqtlMapGts);
            }
        } else {
            $eqtlMap = 0;
            $eqtlMaptss = "NA";
        }
        if ($request->filled('sigeqtlCheck')) {
            $sigeqtl = 1;
            $eqtlP = 1;
        } else {
            $sigeqtl = 0;
            $eqtlP = $request->input('eqtlP');
        }
        if ($request->filled('geneMap_eqtlMapCADDcheck')) {
            $eqtlMapCADDth = $request->input('geneMap_eqtlMapCADDth');
        } else {
            $eqtlMapCADDth = 0;
        }
        if ($request->filled('geneMap_eqtlMapRDBcheck')) {
            $eqtlMapRDBth = $request->input('geneMap_eqtlMapRDBth');
        } else {
            $eqtlMapRDBth = "NA";
        }
        if ($request->filled('geneMap_eqtlMapChr15check')) {
            $temp = $request->input('geneMap_eqtlMapChr15Ts');
            $eqtlMapChr15 = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $eqtlMapChr15[] = $ts;
                }
            }
            $eqtlMapChr15 = implode(":", $eqtlMapChr15);
            $eqtlMapChr15Max = $request->input('geneMap_eqtlMapChr15Max');
            $eqtlMapChr15Meth = $request->input('geneMap_eqtlMapChr15Meth');
        } else {
            $eqtlMapChr15 = "NA";
            $eqtlMapChr15Max = "NA";
            $eqtlMapChr15Meth = "NA";
        }
        $eqtlMapAnnoDs = $request->input('geneMap_eqtlMapAnnoDs', []);
        if (count($eqtlMapAnnoDs) == 0) {
            $eqtlMapAnnoDs = "NA";
        } else {
            $temp = [];
            foreach ($eqtlMapAnnoDs as $ds) {
                if ($ds != "null") {
                    $temp[] = $ds;
                }
            }
            $eqtlMapAnnoDs = implode(":", $temp);
        }
        $eqtlMapAnnoMeth = $request->input('geneMap_eqtlMapAnnoMeth');

        // chromatin interaction mapping
        $ciMap = 0;
        $ciMapFileN = 0;
        $ciMapFiles = "NA";
        if ($request->filled('geneMap_ciMap')) {
            $ciMap = 1;
            if ($request->filled('geneMap_ciMapBuiltin')) {
                $temp = $request->input('geneMap_ciMapBuiltin');
                $ciMapBuiltin = [];
                foreach ($temp as $dat) {
                    if ($dat != "null") {
                        $ciMapBuiltin[] = $dat;
                    }
                }
                $ciMapBuiltin = implode(":", $ciMapBuiltin);
            } else {
                $ciMapBuiltin = "NA";
            }

            $ciMapFileN = (int)$request->input("ciFileN");
            if ($ciMapFileN > 0) {
                $ciMapFiles = [];
                $n = 1;
                while (count($ciMapFiles) < $ciMapFileN) {
                    $id = (string) $n;
                    if ($request->hasFile("ciMapFile" . $id)) {
                        $tmp_filename = $_FILES["ciMapFile" . $id]["name"];
                        $request->file("ciMapFile" . $id)->move($filedir, $tmp_filename);
                        $tmp_datatype = "undefined";
                        if ($request->filled("ciMapType" . $id)) {
                            $tmp_datatype = $request->input("ciMapType" . $id);
                        }
                        $ciMapFiles[] = $tmp_datatype . "/user_upload/" . $tmp_filename;
                    }
                    $n++;
                }
                $ciMapFiles = implode(":", $ciMapFiles);
            }

            $ciMapFDR = $request->input('geneMap_ciMapFDR');
            if ($request->filled('geneMap_ciMapPromWindow')) {
                $ciMapPromWindow = $request->input('geneMap_ciMapPromWindow');
            } else {
                $ciMapPromWindow = "250-500";
            }
            if ($request->filled('geneMap_ciMapRoadmap')) {
                $temp = $request->input('geneMap_ciMapRoadmap');
                $ciMapRoadmap = [];
                foreach ($temp as $dat) {
                    if ($dat != "null") {
                        $ciMapRoadmap[] = $dat;
                    }
                }
                $ciMapRoadmap = implode(":", $ciMapRoadmap);
            } else {
                $ciMapRoadmap = "NA";
            }
            if ($request->filled('geneMap_ciMapEnhFilt')) {
                $ciMapEnhFilt = 1;
            } else {
                $ciMapEnhFilt = 0;
            }
            if ($request->filled('geneMap_ciMapPromFilt')) {
                $ciMapPromFilt = 1;
            } else {
                $ciMapPromFilt = 0;
            }
        } else {
            $ciMapBuiltin = "NA";
            $ciMapFDR = "NA";
            $ciMapPromWindow = "NA";
            $ciMapRoadmap = "NA";
            $ciMapEnhFilt = 0;
            $ciMapPromFilt = 0;
        }


        if ($request->filled('geneMap_ciMapCADDcheck')) {
            $ciMapCADDth = $request->input('geneMap_ciMapCADDth');
        } else {
            $ciMapCADDth = 0;
        }
        if ($request->filled('geneMap_ciMapRDBcheck')) {
            $ciMapRDBth = $request->input('geneMap_ciMapRDBth');
        } else {
            $ciMapRDBth = "NA";
        }
        if ($request->filled('geneMap_ciMapChr15check')) {
            $temp = $request->input('geneMap_ciMapChr15Ts');
            $ciMapChr15 = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $ciMapChr15[] = $ts;
                }
            }
            $ciMapChr15 = implode(":", $ciMapChr15);
            $ciMapChr15Max = $request->input('geneMap_ciMapChr15Max');
            $ciMapChr15Meth = $request->input('geneMap_ciMapChr15Meth');
        } else {
            $ciMapChr15 = "NA";
            $ciMapChr15Max = "NA";
            $ciMapChr15Meth = "NA";
        }
        $ciMapAnnoDs = $request->input('geneMap_ciMapAnnoDs', []);
        if (count($ciMapAnnoDs) == 0) {
            $ciMapAnnoDs = "NA";
        } else {
            $temp = [];
            foreach ($ciMapAnnoDs as $ds) {
                if ($ds != "null") {
                    $temp[] = $ds;
                }
            }
            $ciMapAnnoDs = implode(":", $temp);
        }
        $ciMapAnnoMeth = $request->input('geneMap_ciMapAnnoMeth');

        // write parameter into a file
        $paramfile = $filedir . '/params.config';
        Storage::put($paramfile, "");
        $oldparam = fopen(config('app.jobdir') . '/jobs/' . $oldID . '/params.config', 'r');
        while ($line = fgets($oldparam)) {
            if (preg_match('/^\n/', $line)) {
                continue;
            } else if (preg_match('/\[jobinfo\]/', $line)) {
                Storage::append($paramfile, $line);
            } else if (preg_match('/posMap|eqtlMap|ciMap/', $line)) {
                continue;
            } else if (preg_match('/^title/', $line)) {
                Storage::append($paramfile, "title=" . $jobtitle . "\n");
            } else if (preg_match('/^created_at/', $line)) {
                Storage::append($paramfile, "created_at=" . $date . "\n");
            } else if (preg_match('/^\[/', $line)) {
                Storage::append($paramfile, "\n" . $line);
            } else {
                Storage::append($paramfile, $line);
            }
        }

        Storage::append($paramfile, "\n[posMap]\n");
        Storage::append($paramfile, "posMap=$posMap\n");
        // Storage::append($paramfile, "posMapWindow=$posMapWindow\n");
        Storage::append($paramfile, "posMapWindowSize=$posMapWindowSize\n");
        Storage::append($paramfile, "posMapAnnot=$posMapAnnot\n");
        Storage::append($paramfile, "posMapCADDth=$posMapCADDth\n");
        Storage::append($paramfile, "posMapRDBth=$posMapRDBth\n");
        Storage::append($paramfile, "posMapChr15=$posMapChr15\n");
        Storage::append($paramfile, "posMapChr15Max=$posMapChr15Max\n");
        Storage::append($paramfile, "posMapChr15Meth=$posMapChr15Meth\n");
        Storage::append($paramfile, "posMapAnnoDs=$posMapAnnoDs\n");
        Storage::append($paramfile, "posMapAnnoMeth=$posMapAnnoMeth\n");

        Storage::append($paramfile, "\n[eqtlMap]\n");
        Storage::append($paramfile, "eqtlMap=$eqtlMap\n");
        Storage::append($paramfile, "eqtlMaptss=$eqtlMaptss\n");
        Storage::append($paramfile, "eqtlMapSig=$sigeqtl\n");
        Storage::append($paramfile, "eqtlMapP=$eqtlP\n");
        Storage::append($paramfile, "eqtlMapCADDth=$eqtlMapCADDth\n");
        Storage::append($paramfile, "eqtlMapRDBth=$eqtlMapRDBth\n");
        Storage::append($paramfile, "eqtlMapChr15=$eqtlMapChr15\n");
        Storage::append($paramfile, "eqtlMapChr15Max=$eqtlMapChr15Max\n");
        Storage::append($paramfile, "eqtlMapChr15Meth=$eqtlMapChr15Meth\n");
        Storage::append($paramfile, "eqtlMapAnnoDs=$eqtlMapAnnoDs\n");
        Storage::append($paramfile, "eqtlMapAnnoMeth=$eqtlMapAnnoMeth\n");

        Storage::append($paramfile, "\n[ciMap]\n");
        Storage::append($paramfile, "ciMap=$ciMap\n");
        Storage::append($paramfile, "ciMapBuiltin=$ciMapBuiltin\n");
        Storage::append($paramfile, "ciMapFileN=$ciMapFileN\n");
        Storage::append($paramfile, "ciMapFiles=$ciMapFiles\n");
        Storage::append($paramfile, "ciMapFDR=$ciMapFDR\n");
        Storage::append($paramfile, "ciMapPromWindow=$ciMapPromWindow\n");
        Storage::append($paramfile, "ciMapRoadmap=$ciMapRoadmap\n");
        Storage::append($paramfile, "ciMapEnhFilt=$ciMapEnhFilt\n");
        Storage::append($paramfile, "ciMapPromFilt=$ciMapPromFilt\n");
        Storage::append($paramfile, "ciMapCADDth=$ciMapCADDth\n");
        Storage::append($paramfile, "ciMapRDBth=$ciMapRDBth\n");
        Storage::append($paramfile, "ciMapChr15=$ciMapChr15\n");
        Storage::append($paramfile, "ciMapChr15Max=$ciMapChr15Max\n");
        Storage::append($paramfile, "ciMapChr15Meth=$ciMapChr15Meth\n");
        Storage::append($paramfile, "ciMapAnnoDs=$ciMapAnnoDs\n");
        Storage::append($paramfile, "ciMapAnnoMeth=$ciMapAnnoMeth\n");
        return redirect("/snp2gene#joblist-panel");
    }

    public function Error5(Request $request)
    {
        $jobID = $request->input('jobID');

        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';
        $f = fopen($filedir . 'topSNPs.txt', 'r');
        $rows = [];
        while ($row = fgetcsv($f, 0, "\t")) {
            $rows[] = $row;
        }

        return json_encode($rows);
    }

    public function deleteJob(Request $request)
    {
        $jobID = $request->input('jobID');
        Storage::deleteDirectory(config('app.jobdir') . '/jobs/' . $jobID);
        DB::table('SubmitJobs')->where('jobID', $jobID)->delete();
        return;
    }

    public function filedown(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        // $zip = new ZipArchive();
        $files = [];
        if ($request->filled('paramfile')) {
            $files[] = "params.config";
        }
        if ($request->filled('indSNPfile')) {
            $files[] = "IndSigSNPs.txt";
        }
        if ($request->filled('leadfile')) {
            $files[] = "leadSNPs.txt";
        }
        if ($request->filled('locifile')) {
            $files[] = "GenomicRiskLoci.txt";
        }
        if ($request->filled('snpsfile')) {
            $files[] = "snps.txt";
            $files[] = "ld.txt";
        }
        if ($request->filled('annovfile')) {
            $files[] = "annov.txt";
            $files[] = "annov.stats.txt";
        }
        if ($request->filled('annotfile')) {
            $files[] = "annot.txt";
            if (Storage::exists($filedir . "annot.bed")) {
                $files[] = "annot.bed";
            }
        }
        if ($request->filled('genefile')) {
            $files[] = "genes.txt";
        }
        if ($request->filled('eqtlfile')) {
            if (Storage::exists($filedir . "eqtl.txt")) {
                $files[] = "eqtl.txt";
            }
        }
        if ($request->filled('cifile')) {
            if (Storage::exists($filedir . "ci.txt")) {
                $files[] = "ci.txt";
                $files[] = "ciSNPs.txt";
                $files[] = "ciProm.txt";
            }
        }
        // if($request -> has('exacfile')){$files[] = $filedir."ExAC.txt";}
        if ($request->filled('gwascatfile')) {
            $files[] = "gwascatalog.txt";
        }
        if ($request->filled('magmafile')) {
            $files[] = "magma.genes.out";
            $files[] = "magma.genes.raw";
            if (Storage::exists($filedir . "magma.sets.out")) {
                $files[] = "magma.sets.out";
                if (Storage::exists($filedir . "magma.setgenes.out")) {
                    $files[] = "magma.setgenes.out";
                }
            }
            if (Storage::exists($filedir . "magma.gsa.out")) {
                $files[] = "magma.gsa.out";
                if (Storage::exists($filedir . "magma.gsa.sets.genes.out")) {
                    $files[] = "magma.gsa.sets.genes.out";
                }
            }
            if (Storage::exists($filedir . "magma_exp.gcov.out")) {
                $files[] = "magma_exp.gcov.out";
                $files[] = "magma_exp_general.gcov.out";
            }
            $tmp = File::glob($filedir . "magma_exp_*.gcov.out");
            for ($i = 0; $i < count($tmp); $i++) {
                $files[] = preg_replace("/.+\/(magma_exp_*)/", '$1', $tmp[$i]);
            }
            $tmp = File::glob($filedir . "magma_exp_*.gsa.out");
            for ($i = 0; $i < count($tmp); $i++) {
                $files[] = preg_replace("/.+\/(magma_exp_*)/", '$1', $tmp[$i]);
            }
        }


        $zip = new \ZipArchive();
        if ($prefix == "gwas") {
            $zipfile = $filedir . "FUMA_gwas" . $id . ".zip";
        } else {
            $zipfile = $filedir . "FUMA_job" . $id . ".zip";
        }

        if (Storage::exists($zipfile)) {
            File::delete($zipfile);
        }
        $zip->open($zipfile, \ZipArchive::CREATE);
        $zip->addFile(public_path() . '/README', "README");
        foreach ($files as $f) {
            $zip->addFile($filedir . $f, $f);
        }
        $zip->close();
        return response()->download($zipfile);
    }

    public function checkPublish(Request $request)
    {
        $id = $request->input('id');
        $out = [];
        $out['publish'] = collect(DB::select('SELECT jobID FROM PublicResults WHERE jobID=?', [$id]))->count();
        if ($out['publish'] == 0) {
            $check = collect(DB::select('SELECT jobID FROM gene2func WHERE snp2gene=?', [$id]))->count();
            if ($check > 0) {
                $out['g2f'] = collect(DB::select('SELECT jobID FROM gene2func WHERE snp2gene=?', [$id]))->first()->jobID;
            }
            $out['author'] = Auth::user()->name;
            $out['email'] = Auth::user()->email;
            $out['title'] = collect(DB::select('SELECT title FROM SubmitJobs WHERE jobID=?', [$id]))->first()->title;
            return json_encode($out);
        } else {
            $out['entry'] = collect(DB::select('SELECT * FROM PublicResults WHERE jobID=?', [$id]))->first();
            return json_encode($out);
        }
    }

    public function publish(Request $request)
    {
        $date = date('Y-m-d');
        $jobID = $request->input('jobID');
        $g2f_jobID = $request->input('g2f_jobID');
        $title = $request->input('title');
        $author = $request->input('author');
        $email = $request->input('email');
        $pheno = $request->input('phenotype');
        $publication = $request->input('publication');
        $sumstats_link = $request->input('sumstats_link');
        $sumstats_ref = $request->input('sumstats_ref');
        $notes = $request->input('notes');

        if (strlen($pheno) == 0) {
            $pheno = 'NA';
        }
        if (strlen($publication) == 0) {
            $publication = 'NA';
        }
        if (strlen($sumstats_link) == 0) {
            $sumstats_link = 'NA';
        }
        if (strlen($sumstats_ref) == 0) {
            $sumstats_ref = 'NA';
        }
        if (strlen($notes) == 0) {
            $notes = 'NA';
        }

        DB::table('PublicResults')->insert(
            [
                'jobID' => $jobID, 'g2f_jobID' => $g2f_jobID, 'title' => $title,
                'author' => $author, 'email' => $email, 'phenotype' => $pheno,
                'publication' => $publication, 'sumstats_link' => $sumstats_link,
                'sumstats_ref' => $sumstats_ref, 'notes' => $notes,
                'created_at' => $date, 'update_at' => $date
            ]
        );

        $id = collect(DB::select('SELECT id FROM PublicResults WHERE jobID=?', [$jobID]))->first()->id;
        $filedir = config('app.jobdir') . '/public/' . $id;
        File::makeDirectory($filedir);
        exec('cp -r ' . config('app.jobdir') . '/jobs/' . $jobID . '/* ' . $filedir . '/');
        exec('rm ' . $filedir . '/*.zip');
        if (strlen($g2f_jobID) > 0) {
            File::makeDirectory($filedir . '/g2f');
            exec('cp -r ' . config('app.jobdir') . '/gene2func/' . $g2f_jobID . '/* ' . $filedir . '/g2f/');
            exec('rm ' . $filedir . '/g2f/*.zip');
        }
        return;
    }

    public function updatePublicRes(Request $request)
    {
        $date = date('Y-m-d');
        $jobID = $request->input('jobID');
        $update = array();
        $update['g2f_jobID'] = $request->input('g2f_jobID');
        $update['title'] = $request->input('title');
        $update['author'] = $request->input('author');
        $update['email'] = $request->input('email');
        $update['phenotype'] = $request->input('phenotype');
        $update['publication'] = $request->input('publication');
        $update['sumstats_link'] = $request->input('sumstats_link');
        $update['sumstats_ref'] = $request->input('sumstats_ref');
        $update['notes'] = $request->input('notes');

        if (strlen($update['phenotype']) == 0) {
            $pheno = 'NA';
        }
        if (strlen($update['publication']) == 0) {
            $publication = 'NA';
        }
        if (strlen($update['sumstats_link']) == 0) {
            $sumstats_link = 'NA';
        }
        if (strlen($update['sumstats_ref']) == 0) {
            $sumstats_ref = 'NA';
        }
        if (strlen($update['notes']) == 0) {
            $notes = 'NA';
        }

        $current = collect(DB::select('SELECT g2f_jobID,title,author,email,phenotype,publication,sumstats_link,sumstats_ref,notes FROM PublicResults WHERE jobID=?', [$jobID]))->first();
        $updated = false;
        foreach ($current as $k => $v) {
            if ($v != $update[$k]) {
                DB::table('PublicResults')->where('jobID', $jobID)->update([$k => $update[$k]]);
                $updated = true;
            }
        }
        if ($updated) {
            DB::table('PublicResults')->where('jobID', $jobID)->update(['update_at' => $date]);
        }
        return;
    }

    public function deletePublicRes(Request $request)
    {
        $jobID = $request->input('jobID');
        $id = collect(DB::select('SELECT id FROM PublicResults WHERE jobID=?', [$jobID]))->first()->id;
        $filedir = config('app.jobdir') . '/public/' . $id;
        DB::table('PublicResults')->where('jobID', $jobID)->delete();
        File::deletedirectory($filedir);
        return;
    }
}
