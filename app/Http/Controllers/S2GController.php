<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\SubmitJob;
use App\Jobs\Snp2geneProcess;
use App\CustomClasses\myFile;

use Auth;
use Helper;
use File;

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
        $check = SubmitJob::find($jobID);

        if ($check->jobID == $jobID) {
            return view('pages.snp2gene', ['id' => $jobID, 'status' => 'jobquery', 'page' => 'snp2gene', 'prefix' => 'jobs']);
        } else {
            return view('pages.snp2gene', ['id' => null, 'status' => null, 'page' => 'snp2gene', 'prefix' => 'jobs']);
        }
    }

    public function getJobList()
    {
        $user_id = Auth::user()->id;
        $results = (new SubmitJob)->getJobList_snp2gene_and_geneMap_only($user_id);
        $this->queueNewJobs(); // TODO: move this to a cron job
        return response()->json($results);
    }

    /**
     * Return the number of scheduled (snp2gene and geneMap jobs only) jobs for the current user
     * including all QUEUED RUNNING and NEW jobs.
     * @param int $user_id The user id
     * @return int
     */
    private function getNumberScheduledJobs($user_id): int
    {
        $results = (new SubmitJob)->getScheduledJobs_snp2gene_and_geneMap_only($user_id);
        return count($results);
    }

    public function getjobIDs()
    {
        $user_id = Auth::user()->id;
        $results = (new SubmitJob)->getJob_ids_and_titles_snp2gene_and_geneMap_only($user_id);
        return $results;
    }

    public function getFinishedjobsIDs()
    {
        $user_id = Auth::user()->id;
        $results = (new SubmitJob)->getOkJob_ids_and_titles_snp2gene_and_geneMap_only($user_id);
        return $results;
    }

    public function loadParams(Request $request)
    {
        $id = $request->input("id");
        $filedir = config('app.jobdir') . '/jobs/' . $id . '/';
        $params = parse_ini_string(Storage::get($filedir . "params.config"), false, INI_SCANNER_RAW);
        return json_encode($params);
    }

    private function queueNewJobs()
    {
        $user = Auth::user();
        $newJobs = (new SubmitJob)->getNewJobs_snp2gene_and_geneMap_only($user->id);

        if ($newJobs->count() > 0) {
            foreach ($newJobs as $job) {
                (new SubmitJob)->updateStatus($job->jobID, 'QUEUED');
                Snp2geneProcess::dispatch($user, $job->jobID)->afterCommit();
            }
        }
    }

    public function checkJobStatus($jobID)
    {
        $job = SubmitJob::find($jobID);
        if (!$job) {
            return "Notfound";
        }
        return $job->status;
    }

    public function getParams(Request $request)
    {
        $jobID = $request->input('jobID');

        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';
        $params = parse_ini_string(Storage::get($filedir . "params.config"), false, INI_SCANNER_RAW);

        $res = [];

        if (array_key_exists('posMap', $params)) {$res['posMap'] = $params['posMap'];}
        if (array_key_exists('eqtlMap', $params)) {$res['eqtlMap'] = $params['eqtlMap'];}
        if (array_key_exists('orcol', $params)) {$res['orcol'] = $params['orcol'];}
        if (array_key_exists('becol', $params)) {$res['becol'] = $params['becol'];}
        if (array_key_exists('secol', $params)) {$res['secol'] = $params['secol'];}
        if (array_key_exists('ciMap', $params)) {$res['ciMap'] = $params['ciMap'];}
        if (array_key_exists('magma', $params)) {$res['magma'] = $params['magma'];}

        return response()->json($res);
    }

    public function getFilesContents(Request $request)
    {
        $jobID = $request->input('jobID');
        $fileNames = $request->input('fileNames');
        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';

        $result = Helper::getFilesContents($filedir, $fileNames);

        // Convert the array to a JSON string.
        return response()->json($result);
    }

    /*
    *Function name: MAGMA_expPlot
    *Input parameter: $request - an instance of the Request class
    *Return value: a JSON-encoded string
    *Steps:
    **Extract the job ID from the input request.
    **Construct a directory path based on the job ID.
    **Search for files with names that match the pattern "magma_exp*.gsa.out" or "magma_exp*.gcov.out".
    **Read and parse the contents of the file(s) to extract data.
    **Sort the data by ascending p-value and add two columns to the array.
    **Sort the data by the first column and add a new column to the array.
    **Add each line of the array to an output array as a new JSON object.
    **Convert the output array to a JSON-encoded string and return it as a HTTP response.
    */
    public function MAGMA_expPlot(Request $request)
    {
        $jobID = $request->input('jobID');
        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';


        // Find all files with names that match the pattern "magma_exp*.gsa.out" or "magma_exp*.gcov.out"
        $files = Helper::my_glob($filedir, "/magma_exp.*\.gsa\.out/");
        $suffix = "gsa";
        if (count($files) === 0) {
            $files = Helper::my_glob($filedir, "/magma_exp.*\.gcov\.out/");
            $suffix = "gcov";
        }

        $out = [];
        foreach ($files as $f) {
            // Read the header of the current file to determine the names of the columns in the data
            if ($suffix === "gsa") {
                $contents = Storage::get($f);
                $lines = explode("\n", $contents);
                foreach ($lines as $line) {
                    if (substr($line, 0, 1) !== "#") {
                        $header = preg_split('/\s+/', $line);
                        if (in_array("FULL_NAME", $header)) {
                            $header = array("P", "FULL_NAME");
                        } else {
                            $header = array("VARIABLE", "P");
                        }
                        break;
                    }
                }
            } else {
                $header = array("COVAR", "P");
            }

            // Read the data from the file and store it in an array of associative arrays
            $contents = Storage::get($f);
            $lines = explode(PHP_EOL, $contents);
            // Initialize the array to store the extracted data
            $data = array();
            $entrire_header = array();
            // Loop through the file lines
            $i = 0;
            foreach ($lines as $line) {
                // Skip lines that start with #
                if (strpos($line, '#') === 0) {
                    continue;
                } elseif (empty(trim($line))) {
                    continue;
                }
                $i++;
                if ($i == 1) {
                    $entrire_header = preg_split('/\s+/', $line);
                } else {
                    // Split the line into an array of values
                    $values = array_combine($entrire_header, preg_split('/\s+/', trim($line)));
                    // Extract only the desired columns
                    $extracted_data = array_intersect_key($values, array_flip($header));
                    // Add the extracted data to the output array
                    // If the header includes "FULL_NAME", reverse the order of the columns
                    if (in_array("FULL_NAME", $header)) {
                        $extracted_data = array_reverse($extracted_data);
                    }
                    $data[] = $extracted_data;
                }
            }

            // Sort the data by ascending p-value
            usort($data, function ($a, $b) {
                return floatval($a["P"]) <=> floatval($b["P"]);
            });

            // Add two columns to the data array: one with the original row index and one with a new row index
            foreach ($data as $i => $line) {
                $line['ascending_P_idx'] = $i;
                $data[$i] = $line;
            }

            // Sort the data by the first column (either "VARIABLE" or "COVAR") and add a new column with a new row index
            usort($data, function ($a, $b) {
                return reset($a) <=> reset($b);
            });

            foreach ($data as $i => $line) {
                $line['ascending_var_or_covar_idx'] = $i;
                $data[$i] = $line;
            }

            // Extract the prefix of the file name (i.e., the part between "magma_exp_" and ".$suffix.out")
            preg_match('/.*magma_exp_(.*)\.' . $suffix . '.out/', $f, $matches);
            $c = $matches[1];

            // Add each line of the data array to the output array as a new JSON object
            foreach ($data as $line) {
                $out[] = array(
                    'f_name'                        => $c,
                    'var_name'                      => reset($line),
                    'p'                             => floatval($line['P']),
                    'ascending_P_idx'               => $line['ascending_P_idx'],
                    'ascending_var_or_covar_idx'    => $line['ascending_var_or_covar_idx']
                );
            }
        }

        // Convert the output array to a JSON-encoded string and return it
        return response()->json($out);
    }

    /**
     * Returns the queue cap value
     * 
     * A null return indicates no timeout
     */
    private function getQueueCap()
    {
        return config('queue.jobLimits.queue_cap', 10);
    }

    public function newJob(Request $request)
    {
        $acceptable_mime_types = array(
            "text/plain",
            "application/zip",
            "application/x-zip",
            "application/x-gzip",
            "application/gzip",
            "application/x-zip-compressed"
        );
        $date = date('Y-m-d H:i:s');
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;
        $exfile = config('app.jobdir') . '/example/CD.gwas';
        
        // Implement the cap on max jobs in queue
        $numSchedJobs = $this->getNumberScheduledJobs($user_id);
        $queueCap = $this->getQueueCap();

        if ($request->has('egGWAS')) {
            if (!Storage::exists($exfile)) {
                $message = <<<MSG
                Example file is missing from the server. Please contact the administrator.
                MSG;
                $request->session()->flash("alert-warning", $message);
                return redirect()->back();
            }
        }

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
            if (!in_array($type, $acceptable_mime_types)) {
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
        $submitJob->user_id = $user_id;
        $submitJob->type = 'snp2gene';
        $submitJob->title = $jobtitle;
        $submitJob->status = 'NEW';
        $submitJob->save();

        // Get jobID (automatically generated)
        $jobID = $submitJob->jobID;

        // create job directory
        $filedir = config('app.jobdir') . '/jobs/' . $jobID;
        Storage::makeDirectory($filedir);

        // upload input Filesystem
        $leadSNPs = "input.lead";
        $GWAS = "input.gwas";
        $regions = "input.regions";
        $leadSNPsfileup = 0;
        $regionsfileup = 0;

        // GWAS smmary stats file
        if ($request->hasFile('GWASsummary')) {
            myFile::fileValidationAndStore($request->file('GWASsummary'), $GWAS, $filedir);
        } else if ($request->has('egGWAS')) {
            Storage::copy($exfile, $filedir . '/input.gwas');
        }

        // pre-defined lead SNPS file
        if ($request->hasFile('leadSNPs')) {
            myFile::fileValidationAndStore($request->file('leadSNPs'), $leadSNPs, $filedir);
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
            myFile::fileValidationAndStore($request->file('regions'), $regions, $filedir);
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
        Storage::put($paramfile, "[jobinfo]");
        Storage::append($paramfile, "created_at=$date");
        Storage::append($paramfile, "title=$jobtitle");

        Storage::append($paramfile, "\n[version]");
        Storage::append($paramfile, "FUMA=" . $app_config['FUMA']);
        Storage::append($paramfile, "MAGMA=" . $app_config['MAGMA']);
        Storage::append($paramfile, "GWAScatalog=" . $app_config['GWAScatalog']);
        Storage::append($paramfile, "ANNOVAR=" . $app_config['ANNOVAR']);

        Storage::append($paramfile, "\n[inputfiles]");
        if ($request->hasFile('GWASsummary')) {
            Storage::append($paramfile, "gwasfile=" . $_FILES["GWASsummary"]["name"]);
        } else {
            Storage::append($paramfile, "gwasfile=fuma.example.CD.gwas");
        }
        Storage::append($paramfile, "chrcol=$chrcol");
        Storage::append($paramfile, "poscol=$poscol");
        Storage::append($paramfile, "rsIDcol=$rsIDcol");
        Storage::append($paramfile, "pcol=$pcol");
        Storage::append($paramfile, "eacol=$eacol");
        Storage::append($paramfile, "neacol=$neacol");
        Storage::append($paramfile, "orcol=$orcol");
        Storage::append($paramfile, "becol=$becol");
        Storage::append($paramfile, "secol=$secol");
        // Storage::append($paramfile, "mafcol=$mafcol");

        if ($leadSNPsfileup == 1) {
            Storage::append($paramfile, "leadSNPsfile=" . $_FILES["leadSNPs"]["name"]);
        } else {
            Storage::append($paramfile, "leadSNPsfile=NA");
        }
        Storage::append($paramfile, "addleadSNPs=$addleadSNPs");
        if ($regionsfileup == 1) {
            Storage::append($paramfile, "regionsfile=" . $_FILES["regions"]["name"]);
        } else {
            Storage::append($paramfile, "regionsfile=NA");
        }

        Storage::append($paramfile, "\n[params]");
        Storage::append($paramfile, "N=$N");
        Storage::append($paramfile, "Ncol=$Ncol");
        Storage::append($paramfile, "exMHC=$exMHC");
        Storage::append($paramfile, "MHCopt=$MHCopt");
        Storage::append($paramfile, "extMHC=$extMHC");
        Storage::append($paramfile, "ensembl=$ensembl");
        Storage::append($paramfile, "genetype=$genetype");
        Storage::append($paramfile, "leadP=$leadP");
        Storage::append($paramfile, "gwasP=$gwasP");
        Storage::append($paramfile, "r2=$r2");
        Storage::append($paramfile, "r2_2=$r2_2");
        Storage::append($paramfile, "refpanel=$refpanel");
        Storage::append($paramfile, "pop=$pop");
        Storage::append($paramfile, "MAF=$maf");
        Storage::append($paramfile, "refSNPs=$refSNPs");
        Storage::append($paramfile, "mergeDist=$mergeDist");

        Storage::append($paramfile, "\n[magma]");
        Storage::append($paramfile, "magma=$magma");
        Storage::append($paramfile, "magma_window=$magma_window");
        Storage::append($paramfile, "magma_exp=$magma_exp");

        Storage::append($paramfile, "\n[posMap]");
        Storage::append($paramfile, "posMap=$posMap");
        // Storage::append($paramfile, "posMapWindow=$posMapWindow\n");
        Storage::append($paramfile, "posMapWindowSize=$posMapWindowSize");
        Storage::append($paramfile, "posMapAnnot=$posMapAnnot");
        Storage::append($paramfile, "posMapCADDth=$posMapCADDth");
        Storage::append($paramfile, "posMapRDBth=$posMapRDBth");
        Storage::append($paramfile, "posMapChr15=$posMapChr15");
        Storage::append($paramfile, "posMapChr15Max=$posMapChr15Max");
        Storage::append($paramfile, "posMapChr15Meth=$posMapChr15Meth");
        Storage::append($paramfile, "posMapAnnoDs=$posMapAnnoDs");
        Storage::append($paramfile, "posMapAnnoMeth=$posMapAnnoMeth");

        Storage::append($paramfile, "\n[eqtlMap]");
        Storage::append($paramfile, "eqtlMap=$eqtlMap");
        Storage::append($paramfile, "eqtlMaptss=$eqtlMaptss");
        Storage::append($paramfile, "eqtlMapSig=$sigeqtl");
        Storage::append($paramfile, "eqtlMapP=$eqtlP");
        Storage::append($paramfile, "eqtlMapCADDth=$eqtlMapCADDth");
        Storage::append($paramfile, "eqtlMapRDBth=$eqtlMapRDBth");
        Storage::append($paramfile, "eqtlMapChr15=$eqtlMapChr15");
        Storage::append($paramfile, "eqtlMapChr15Max=$eqtlMapChr15Max");
        Storage::append($paramfile, "eqtlMapChr15Meth=$eqtlMapChr15Meth");
        Storage::append($paramfile, "eqtlMapAnnoDs=$eqtlMapAnnoDs");
        Storage::append($paramfile, "eqtlMapAnnoMeth=$eqtlMapAnnoMeth");

        Storage::append($paramfile, "\n[ciMap]");
        Storage::append($paramfile, "ciMap=$ciMap");
        Storage::append($paramfile, "ciMapBuiltin=$ciMapBuiltin");
        Storage::append($paramfile, "ciMapFileN=$ciMapFileN");
        Storage::append($paramfile, "ciMapFiles=$ciMapFiles");
        Storage::append($paramfile, "ciMapFDR=$ciMapFDR");
        Storage::append($paramfile, "ciMapPromWindow=$ciMapPromWindow");
        Storage::append($paramfile, "ciMapRoadmap=$ciMapRoadmap");
        Storage::append($paramfile, "ciMapEnhFilt=$ciMapEnhFilt");
        Storage::append($paramfile, "ciMapPromFilt=$ciMapPromFilt");
        Storage::append($paramfile, "ciMapCADDth=$ciMapCADDth");
        Storage::append($paramfile, "ciMapRDBth=$ciMapRDBth");
        Storage::append($paramfile, "ciMapChr15=$ciMapChr15");
        Storage::append($paramfile, "ciMapChr15Max=$ciMapChr15Max");
        Storage::append($paramfile, "ciMapChr15Meth=$ciMapChr15Meth");
        Storage::append($paramfile, "ciMapAnnoDs=$ciMapAnnoDs");
        Storage::append($paramfile, "ciMapAnnoMeth=$ciMapAnnoMeth");

        $this->queueNewJobs();

        return redirect("/snp2gene#joblist-panel");
    }

    public function geneMap(Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $oldID = $request->input("geneMapID");
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;

        $jobtitle = "";
        if ($request->filled("geneMapTitle")) {
            $jobtitle = $request->input('geneMapTitle');
        }
        $jobtitle .= "_copied_" . $oldID;

        // Create new job in database
        $submitJob = new SubmitJob;
        $submitJob->email = $email;
        $submitJob->user_id = $user_id;
        $submitJob->type = 'geneMap';
        $submitJob->parent_id = $oldID;
        $submitJob->title = $jobtitle;
        $submitJob->status = 'NEW';
        $submitJob->save();

        // Get jobID (automatically generated)
        $jobID = $submitJob->jobID;

        // copie old job to new ID
        $filedir = config('app.jobdir') . '/jobs/' . $jobID;
        $oldfiledir = config('app.jobdir') . '/jobs/' . $oldID;
        File::copyDirectory(Storage::path($oldfiledir), Storage::path($filedir));

        // remove old files
        Storage::delete(Helper::my_glob($filedir, "/.*\.svg/"));
        Storage::delete(Helper::my_glob($filedir, "/.*\.png/"));
        Storage::delete(Helper::my_glob($filedir, "/.*\.pdf/"));
        Storage::delete(Helper::my_glob($filedir, "/.*\.jpg/"));
        Storage::deleteDirectory($filedir . '/circos');

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
        $oldparam = fopen(Storage::path($oldfiledir . '/params.config'), 'r');
        while ($line = fgets($oldparam)) {
            if (preg_match('/^\n/', $line)) {
                continue;
            } else if (preg_match('/\[jobinfo\]/', $line)) {
                Storage::append($paramfile, $line, null);
            } else if (preg_match('/posMap|eqtlMap|ciMap/', $line)) {
                continue;
            } else if (preg_match('/^title/', $line)) {
                Storage::append($paramfile, "title=" . $jobtitle . "\n");
            } else if (preg_match('/^created_at/', $line)) {
                Storage::append($paramfile, "created_at=" . $date, null);
            } else if (preg_match('/^\[/', $line)) {
                Storage::append($paramfile, $line);
            } else {
                Storage::append($paramfile, $line, null);
            }
        }

        Storage::append($paramfile, "[posMap]");
        Storage::append($paramfile, "posMap=$posMap");
        // Storage::append($paramfile, "posMapWindow=$posMapWindow");
        Storage::append($paramfile, "posMapWindowSize=$posMapWindowSize");
        Storage::append($paramfile, "posMapAnnot=$posMapAnnot");
        Storage::append($paramfile, "posMapCADDth=$posMapCADDth");
        Storage::append($paramfile, "posMapRDBth=$posMapRDBth");
        Storage::append($paramfile, "posMapChr15=$posMapChr15");
        Storage::append($paramfile, "posMapChr15Max=$posMapChr15Max");
        Storage::append($paramfile, "posMapChr15Meth=$posMapChr15Meth");
        Storage::append($paramfile, "posMapAnnoDs=$posMapAnnoDs");
        Storage::append($paramfile, "posMapAnnoMeth=$posMapAnnoMeth");

        Storage::append($paramfile, "\n[eqtlMap]");
        Storage::append($paramfile, "eqtlMap=$eqtlMap");
        Storage::append($paramfile, "eqtlMaptss=$eqtlMaptss");
        Storage::append($paramfile, "eqtlMapSig=$sigeqtl");
        Storage::append($paramfile, "eqtlMapP=$eqtlP");
        Storage::append($paramfile, "eqtlMapCADDth=$eqtlMapCADDth");
        Storage::append($paramfile, "eqtlMapRDBth=$eqtlMapRDBth");
        Storage::append($paramfile, "eqtlMapChr15=$eqtlMapChr15");
        Storage::append($paramfile, "eqtlMapChr15Max=$eqtlMapChr15Max");
        Storage::append($paramfile, "eqtlMapChr15Meth=$eqtlMapChr15Meth");
        Storage::append($paramfile, "eqtlMapAnnoDs=$eqtlMapAnnoDs");
        Storage::append($paramfile, "eqtlMapAnnoMeth=$eqtlMapAnnoMeth");

        Storage::append($paramfile, "\n[ciMap]");
        Storage::append($paramfile, "ciMap=$ciMap");
        Storage::append($paramfile, "ciMapBuiltin=$ciMapBuiltin");
        Storage::append($paramfile, "ciMapFileN=$ciMapFileN");
        Storage::append($paramfile, "ciMapFiles=$ciMapFiles");
        Storage::append($paramfile, "ciMapFDR=$ciMapFDR");
        Storage::append($paramfile, "ciMapPromWindow=$ciMapPromWindow");
        Storage::append($paramfile, "ciMapRoadmap=$ciMapRoadmap");
        Storage::append($paramfile, "ciMapEnhFilt=$ciMapEnhFilt");
        Storage::append($paramfile, "ciMapPromFilt=$ciMapPromFilt");
        Storage::append($paramfile, "ciMapCADDth=$ciMapCADDth");
        Storage::append($paramfile, "ciMapRDBth=$ciMapRDBth");
        Storage::append($paramfile, "ciMapChr15=$ciMapChr15");
        Storage::append($paramfile, "ciMapChr15Max=$ciMapChr15Max");
        Storage::append($paramfile, "ciMapChr15Meth=$ciMapChr15Meth");
        Storage::append($paramfile, "ciMapAnnoDs=$ciMapAnnoDs");
        Storage::append($paramfile, "ciMapAnnoMeth=$ciMapAnnoMeth");

        $this->queueNewJobs();

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
        SubmitJob::find($jobID)->delete();
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
            $tmp = Helper::my_glob($filedir, "/magma_exp_.*\.gcov\.out/");
            for ($i = 0; $i < count($tmp); $i++) {
                $files[] = preg_replace("/.+\/(magma_exp_*)/", '$1', $tmp[$i]);
            }
            $tmp = Helper::my_glob($filedir, "/magma_exp_.*\.gsa\.out/");
            for ($i = 0; $i < count($tmp); $i++) {
                $files[] = preg_replace("/.+\/(magma_exp_*)/", '$1', $tmp[$i]);
            }
        }

        if ($prefix == "gwas") {
            $zipfile = $filedir . "FUMA_gwas" . $id . ".zip";
        } else {
            $zipfile = $filedir . "FUMA_job" . $id . ".zip";
        }

        if (Storage::exists($zipfile)) {
            Storage::delete($zipfile);
        }

        # create zip file and open it
        $zip = new \ZipArchive();
        $zip->open(Storage::path($zipfile), \ZipArchive::CREATE);

        # add README file if exists in the public storage
        if (Storage::disk('public')->exists('README')) {
            $zip->addFile(Storage::disk('public')->path('README'), "README");
        }

        # for each file, check if exists in the storage and add to zip file
        foreach ($files as $f) {
            if (Storage::exists($filedir . $f)) {
                $abs_path = Storage::path($filedir . $f);
                $zip->addFile($abs_path, $f);
            }
        }

        # close zip file
        $zip->close();

        # download zip file and delete it after download
        return response()->download(Storage::path($zipfile))->deleteFileAfterSend(true);
    }

    public function checkPublish(Request $request)
    {
        $job_id = $request->input('id');
        $job = SubmitJob::find($job_id);
        $out = [];

        $out['publish'] = $job->is_public;
        $out['title'] = $job->title;

        $out['g2f'] = $job->childs->where('type', 'gene2func')->first();
        $out['g2f'] = is_null($out['g2f']) ? NULL : $out['g2f']->jobID;

        $out['author'] = is_null($job->author) ? $job->user->name : $job->author;
        $out['email'] = is_null($job->publication_email) ? $job->user->email : $job->publication_email;
        
        $out['phenotype'] = is_null($job->phenotype) ? "" : $job->phenotype;
        $out['publication'] = is_null($job->publication) ? "" : $job->publication;
        $out['publication_link'] = is_null($job->sumstats_link) ? "" : $job->sumstats_link;
        $out['sumstats_ref'] = is_null($job->sumstats_ref) ? "" : $job->sumstats_ref;
        $out['notes'] = is_null($job->notes) ? "" : $job->notes;

        return json_encode($out);
    }

    public function publish(Request $request)
    {
        $jobID = $request->input('jobID');
        SubmitJob::where('jobID', $jobID)->update(
            [
                'is_public'             => 1,
                'title'                 => $request->input('title'),
                'author'                => $request->input('author'),
                'publication_email'     => $request->input('email'),
                'phenotype'             => $request->input('phenotype'),
                'publication'           => $request->input('publication'),
                'sumstats_link'         => $request->input('sumstats_link'),
                'sumstats_ref'          => $request->input('sumstats_ref'),
                'notes'                 => $request->input('notes'),
                'published_at'          => date('Y-m-d H:i:s')
            ]
        );
        return;
    }

    public function deletePublicRes(Request $request)
    {
        $jobID = $request->input('jobID');
        $job = SubmitJob::find($jobID);
        $job->is_public = 0;
        $job->save();
        return;
    }

    private function filePreprocessAndStore($file, $file_name, $filedir)
    {
        $acceptable_zip_mime_types = array(
            "application/zip",
            "application/x-zip",
            "application/x-zip-compressed"
        );

        $acceptable_gzip_mime_types = array(
            "application/x-gzip",
            "application/gzip"
        );

        $type = $file->getClientMimeType();
        if ($type == "text/plain" || $type == "application/octet-stream") {
            Storage::put($filedir . '/' . $file_name, file_get_contents($file));
        } else if (in_array($type, $acceptable_zip_mime_types)) {
            Storage::put($filedir . '/' . 'temp', file_get_contents($file));

            $zip = new \ZipArchive;
            $zip->open(Storage::path($filedir . '/temp'));


            $zf = $zip->getNameIndex(0);
            $zip->extractTo(Storage::path($filedir));
            $zip->close();

            Storage::move($filedir . '/' . $zf, $filedir . '/' . $file_name);
            Storage::delete($filedir . '/temp');
        } else if (in_array($type, $acceptable_gzip_mime_types)) {
            Storage::put($filedir . '/' . 'temp', file_get_contents($file));

            $buffer_size = 4096; // The number of bytes that needs to be read at a specific time, 4KB here
            $file = gzopen(Storage::path($filedir . '/temp'), 'rb'); //Opening the file in binary mode
            $out_file = fopen(Storage::path($filedir . '/' . $file_name), 'wb');
            // Keep repeating until the end of the input file
            while (!gzeof($file)) {
                fwrite($out_file, gzread($file, $buffer_size)); //Read buffer-size bytes.
            }
            fclose($out_file); //Close the files once they are done with
            gzclose($file);

            Storage::delete($filedir . '/temp');
        }
        return;
    }
}
