<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Helper;
use Auth;
use App\CustomClasses\DockerApi\DockerNamesBuilder;
use App\Jobs\CelltypeProcess;
use App\Models\SubmitJob;


class CellController extends Controller
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
        return view('pages.celltype', ['id' => $id, 'status' => null, 'page' => 'celltype', 'prefix' => 'celltype']);
    }

    public function authcheck($jobID)
    {
        $check = SubmitJob::find($jobID);

        if ($check->jobID == $jobID) {
            return view('pages.celltype', ['id' => $jobID, 'status' => 'jobquery', 'page' => 'celltype', 'prefix' => 'celltype']);
        } else {
            return view('pages.celltype', ['id' => null, 'status' => null, 'page' => 'celltype', 'prefix' => 'celltype']);
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

    public function getS2GIDs()
    {
        $user_id = Auth::user()->id;
        $results = SubmitJob::where('user_id', $user_id)
            ->where('type', 'snp2gene')
            ->where('status', 'OK')
            ->whereNull('removed_at')
            ->get(['jobID', 'title']);
        return $results;
    }

    public function checkMagmaFile(Request $request)
    {
        $id = $request->input('id');
        if (Storage::exists(config('app.jobdir') . '/jobs/' . $id . "/magma.genes.raw")) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getJobList()
    {
        $user_id = Auth::user()->id;

        if ($user_id) {
            $queries = SubmitJob::with('parent:jobID,title,removed_at')
                ->where('user_id', $user_id)
                ->where('type', 'celltype')
                ->whereNull('removed_at')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $queries = array();
        }

        $this->queueNewJobs();

        return response()->json($queries);
    }

    public function queueNewJobs()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $newJobs = (new SubmitJob)->getNewJobs_celltype_only($user->id);

        if (count($newJobs) > 0) {
            foreach ($newJobs as $job) {
                (new SubmitJob)->updateStatus($job->jobID, 'QUEUED');
                CelltypeProcess::dispatch($user, $job->jobID)->afterCommit();
            }
        }
        return;
    }

    public function deleteJob(Request $request)
    {
        $jobID = $request->input('jobID');
        return Helper::deleteJob(config('app.jobdir') . '/celltype/', $jobID);
    }

    public function newJob(Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;

        $s2gID = $request->input('s2gID');
        $ensg = 0;
        if ($request->filled('ensg_id')) {
            $ensg = 1;
        }
        if ($s2gID > 0) {
            $ensg = 1;
        }
        $ds = implode(":", $request->input('cellDataSets'));
        $adjPmeth = $request->input('adjPmeth');
        $step2 = 0;
        if ($request->filled('step2')) {
            $step2 = 1;
        }
        $step3 = 0;
        if ($request->filled('step3')) {
            $step3 = 1;
        }
        if ($request->filled("title")) {
            $title = $request->input('title');
        } else {
            $title = "None";
        }

        // Create new job in database
        $submitJob = new SubmitJob;
        $submitJob->email = $email;
        $submitJob->user_id = $user_id;
        $submitJob->type = 'celltype';
        $submitJob->parent_id = $s2gID;
        $submitJob->title = $title;
        $submitJob->status = 'NEW';
        $submitJob->save();
        $jobID = $submitJob->jobID;

        $filedir = config('app.jobdir') . '/celltype/' . $jobID;
        Storage::makeDirectory($filedir);
        if ($s2gID == 0) {
            Storage::putFileAs($filedir, $request->file('genes_raw'), 'magma.genes.raw');
        } else {
            $s2gfiledir = config('app.jobdir') . '/jobs/' . $s2gID . '/';
            Storage::copy($s2gfiledir . 'magma.genes.raw', $filedir . '/magma.genes.raw');
        }

        if ($s2gID == 0) {
            $s2gID = "NA";
        }
        $inputfile = "NA";
        if ($request->hasFile('genes_raw')) {
            $inputfile = $_FILES["genes_raw"]["name"];
        }
        $app_config = parse_ini_file(Helper::scripts_path('app.config'), false, INI_SCANNER_RAW);
        $paramfile = $filedir . '/params.config';
        Storage::put($paramfile, "[jobinfo]");
        Storage::append($paramfile, "created_at=$date");
        Storage::append($paramfile, "title=$title");

        Storage::append($paramfile, "\n[version]");
        Storage::append($paramfile, "FUMA=" . $app_config['FUMA']);
        Storage::append($paramfile, "MAGMA=" . $app_config['MAGMA']);

        Storage::append($paramfile, "\n[params]");
        Storage::append($paramfile, "snp2geneID=$s2gID");
        Storage::append($paramfile, "inputfile=$inputfile");
        Storage::append($paramfile, "ensg_id=$ensg");
        Storage::append($paramfile, "datasets=$ds");
        Storage::append($paramfile, "adjPmeth=$adjPmeth");
        Storage::append($paramfile, "step2=$step2");
        Storage::append($paramfile, "step3=$step3");

        return redirect("/celltype#joblist");
    }

    public function checkFileList(Request $request)
    {
        $id = $request->input('id');
        $filedir = config('app.jobdir') . '/celltype/' . $id;
        $params = parse_ini_string(Storage::get($filedir . '/params.config'), false, INI_SCANNER_RAW);
        if ($params['MAGMA'] == "v1.06") {
            $step1 = count(glob($filedir . "/*.gcov.out"));
            $step1_2 = 0;
            $step2 = 0;
            $step3 = 0;
        } else {
            // $step1 = count(glob($filedir . "/*.gsa.out"));
            $step1 = count(Helper::my_glob($filedir, "/.*\.gsa\.out/"));
            $step1_2 = (int) Storage::exists($filedir . "/step1_2_summary.txt");
            $step2 = (int) Storage::exists($filedir . "/magma_celltype_step2.txt");
            $step3 = (int) Storage::exists($filedir . "/magma_celltype_step3.txt");
        }
        return json_encode([$step1, $step1_2, $step2, $step3]);
    }

    public function getDataList(Request $request)
    {
        $id = $request->input('id');
        $filedir = config('app.jobdir') . '/celltype/' . $id;
        $params = parse_ini_string(Storage::get($filedir . '/params.config'), false, INI_SCANNER_RAW);
        $ds = explode(":", $params['datasets']);
        return json_encode($ds);
    }

    public function filedown(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        $params = parse_ini_string(Storage::get($filedir . 'params.config'), false, INI_SCANNER_RAW);

        $checked = $request->input('files');
        $files = [];
        $files[] = "params.config";

        if (in_array("step1", $checked)) {
            $ds = explode(":", $params['datasets']);
            if ($params['MAGMA'] == "v1.06") {
                for ($i = 0; $i < count($ds); $i++) {
                    $files[] = "magma_celltype_" . $ds[$i] . ".gcov.out";
                    $files[] = "magma_celltype_" . $ds[$i] . ".log";
                }
            } else {
                for ($i = 0; $i < count($ds); $i++) {
                    $files[] = "magma_celltype_" . $ds[$i] . ".gsa.out";
                    $files[] = "magma_celltype_" . $ds[$i] . ".log";
                }
            }
            $files[] = "magma_celltype_step1.txt";
        }
        if (in_array("step1_2", $checked)) {
            $files[] = "step1_2_summary.txt";
        }
        if (in_array("step2", $checked)) {
            $files[] = "magma_celltype_step2.txt";
        }
        if (in_array("step3", $checked)) {
            $files[] = "magma_celltype_step3.txt";
        }

        # check if zip file exists, if yes, delete it
        $zipfile = $filedir . "FUMA_celltype" . $id . ".zip";
        if (Storage::exists($zipfile)) {
            Storage::delete($zipfile);
        }

        # create zip file and open it
        $zip = new \ZipArchive();
        $zip->open(Storage::path($zipfile), \ZipArchive::CREATE);

        # add README file if exists in the public storage
        if (Storage::disk('public')->exists('README_cell.txt')) {
            $zip->addFile(Storage::disk('public')->path('README_cell.txt'), "README_cell");
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

    public function getPerDatasetData(Request $request)
    {
        $jobID = $request->input('id');
        $ds = $request->input('ds');

        $container_name = DockerNamesBuilder::containerName($jobID);
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'celltype_plot_data');
        
        $cmd = "docker run --rm --name " . $container_name . " -v " . config('app.abs_path_to_cell_jobs_on_host') . "/$jobID/:/app/job -w /app " . $image_name . " /bin/sh -c 'python celltype_perDatasetPlotData.py job/ $ds'";
        $json = shell_exec($cmd);
        return $json;
    }

    public function getStepPlotData(Request $request)
    {
        $jobID = $request->input('id');

        $container_name = DockerNamesBuilder::containerName($jobID);
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'celltype_plot_data');

        $cmd = "docker run --rm --name " . $container_name . " -v " . config('app.abs_path_to_cell_jobs_on_host') . "/$jobID/:/app/job -w /app " . $image_name . " /bin/sh -c 'python celltype_stepPlotData.py job/'";
        $json = shell_exec($cmd);
        return $json;
    }
}
