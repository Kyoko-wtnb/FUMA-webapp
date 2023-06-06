<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\G2FQuery;

use Helper;
use Auth;

class G2FController extends Controller
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
        return view('pages.gene2func', ['status' => 'new', 'id' => 'none', 'page' => 'gene2func', 'prefix' => 'gene2func']);
    }

    public function authcheck($jobID)
    {
        $email = Auth::user()->email;
        $check = G2FQuery::where('jobID', $jobID)->first();
        if ($check->email == $email) {
            return view('pages.gene2func', ['status' => 'getJob', 'id' => $jobID, 'page' => 'gene2func', 'prefix' => 'gene2func']);
        } else {
            return view('pages.gene2func', ['status' => null, 'id' => $jobID, 'page' => 'gene2func', 'prefix' => 'gene2func']);
        }
    }

    public function getJobList()
    {
        $email = Auth::user()->email;

        if ($email) {
            $queries = G2FQuery::where('email', $email)
                        ->orderBy('created_at', 'desc')
                        ->get();
        } else {
            $queries = array();
        }

        return response()->json($queries);
    }

    public function deleteJob(Request $request)
    {
        $jobID = $request->input('jobID');
        Storage::deleteDirectory(config('app.jobdir') . '/gene2func/' . $jobID);
        G2FQuery::where('jobID', $jobID)->delete();
        return;
    }

    public function gene2funcSubmit(Request $request)
    {
        $G2FQuery = new G2FQuery();
        $date = date('Y-m-d');
        $email = Auth::user()->email;

        if ($request->filled('title')) {
            $title = $request->input('title');
        } else {
            $title = "None";
        }

        $G2FQuery->created_at = $date;
        $G2FQuery->email = $email;
        $G2FQuery->title = $title;

        $G2FQuery->save();
        $jobID = $G2FQuery->jobID;
        
        $filedir = config('app.jobdir') . '/gene2func/' . $jobID;
        Storage::makeDirectory($filedir);
        $filedir = $filedir . '/';

        if ($request->filled('genes')) {
            $gtype = "text";
            $gval = $request->input('genes');
            $gval = preg_split('/[\n\r]+/', $gval);
            $gval = implode(':', $gval);
        } else {
            $gtype = "file";
            $gval = $_FILES["genesfile"]["name"];
            $request->file('genesfile')->move($filedir, $_FILES["genesfile"]["name"]);
        }

        if ($request->filled('genetype')) {
            $bkgtype = "select";
            $bkgval = $request->input('genetype');
            $bkgval = implode(':', $bkgval);
        } else if ($request->filled('bkgenes')) {
            $bkgtype = "text";
            $bkgval = $request->input('bkgenes');
            $bkgval = preg_split('/[\n\r]+/', $bkgval);
            $bkgval = implode(':', $bkgval);
        } else {
            $bkgtype = "file";
            $bkgval = $_FILES["bkgenesfile"]["name"];
            $request->file('bkgenesfile')->move($filedir, $_FILES["bkgenesfile"]["name"]);
        }

        $ensembl = $request->input('ensembl');

        $gsFileN = (int)$request->input('gsFileN');
        $gsFiles = "NA";
        if ($gsFileN > 0) {
            $gsFiles = [];
            $n = 1;
            while (count($gsFiles) < $gsFileN) {
                $id = (string) $n;
                if ($request->hasFile("gsFile" . $id)) {
                    $tmp_filename = $_FILES["gsFile" . $id]["name"];
                    $request->file("gsFile" . $id)->move($filedir, $tmp_filename);
                    $gsFiles[] = $tmp_filename;
                }
                $n++;
            }
            $gsFiles = implode(":", $gsFiles);
        }

        $gene_exp = implode(":", $request->input("gene_exp"));

        if ($request->filled('MHC')) {
            $MHC = 1;
        } else {
            $MHC = 0;
        }

        $adjPmeth = $request->input('adjPmeth');
        $adjPcut = $request->input('adjPcut');
        $minOverlap = $request->input('minOverlap');

        $app_config = parse_ini_file(Helper::scripts_path('app.config'), false, INI_SCANNER_RAW);
        // write parameters to config file
        $paramfile = $filedir . 'params.config';
        Storage::put($paramfile, "[jobinfo]");
        Storage::append($paramfile, "created_at=$date");
        Storage::append($paramfile, "title=$title");

        Storage::append($paramfile, "\n[version]");
        Storage::append($paramfile, "FUMA=" . $app_config['FUMA']);
        Storage::append($paramfile, "MsigDB=" . $app_config['MsigDB']);
        Storage::append($paramfile, "WikiPathways=" . $app_config['WikiPathways']);
        Storage::append($paramfile, "GWAScatalog=" . $app_config['GWAScatalog']);

        Storage::append($paramfile, "\n[params]");
        Storage::append($paramfile, "gtype=$gtype");
        Storage::append($paramfile, "gval=$gval");
        Storage::append($paramfile, "bkgtype=$bkgtype");
        Storage::append($paramfile, "bkgval=$bkgval");
        Storage::append($paramfile, "ensembl=$ensembl");
        Storage::append($paramfile, "gsFileN=$gsFileN");
        Storage::append($paramfile, "gsFiles=$gsFiles");
        Storage::append($paramfile, "gene_exp=$gene_exp");
        Storage::append($paramfile, "MHC=$MHC");
        Storage::append($paramfile, "adjPmeth=$adjPmeth");
        Storage::append($paramfile, "adjPcut=$adjPcut");
        Storage::append($paramfile, "minOverlap=$minOverlap");

        $data = [
            'id' => $jobID,
            'filedir' => $filedir,
            'gtype' => $gtype,
            'gval' => $gval,
            'bkgtype' => $bkgtype,
            'bkgval' => $bkgval,
            'ensembl' => $ensembl,
            'gene_exp' => $gene_exp,
            'MHC' => $MHC,
            'adjPmeth' => $adjPmeth,
            'adjPcut' => $adjPcut,
            'minOverlap' => $minOverlap
        ];

        return view('pages.gene2func', ['status' => 'query', 'id' => $jobID, 'page' => 'gene2func', 'prefix' => 'gene2func', 'data' => $data]);
    }

    public function geneQuery(Request $request)
    {
        $ref_data_path_on_host = config('app.ref_data_on_host_path');
        $jobID = $request->input('id');

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-g2f-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_g2f_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-g2f /bin/sh -c 'Rscript gene2func.R job/'";
        exec($cmd, $output, $error);

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-g2f-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_g2f_jobs_on_host') . "/$jobID/:/app/job laradock-fuma-g2f /bin/sh -c 'python GeneSet.py job/'";
        exec($cmd, $output, $error);
    }

    public function snp2geneGeneQuery(Request $request)
    {
        $s2gID = $request->input('jobID');

        $checkExists = DB::table('gene2func')->where('snp2gene', $s2gID)->first();
        if ($checkExists == null) {
            $date = date('Y-m-d H:i:s');
            // $jobID;
            // $filedir;
            $email = Auth::user()->email;

            if ($request->filled('title')) {
                $title = $request->input('title');
            } else {
                $title = "None";
            }

            $s2gTitle = DB::table('SubmitJobs')->where('jobID', $s2gID)->first()->title;

            DB::table('gene2func')->insert(
                ['title' => $title, 'email' => $email, 'snp2gene' => $s2gID, 'snp2geneTitle' => $s2gTitle, 'created_at' => $date]
            );

            // Get jobID (automatically generated)
            $jobID = DB::table('gene2func')->where('snp2gene', $s2gID)->first()->jobID;
            $filedir = config('app.jobdir') . '/gene2func/' . $jobID;
            Storage::makeDirectory($filedir);
            $filedir = $filedir . '/';

            $s2gfiledir = config('app.jobdir') . '/jobs/' . $s2gID . '/';
            $gtype = "text";
            $bkgtype = "select";
            $params = parse_ini_string(Storage::get($s2gfiledir . 'params.config'), false, INI_SCANNER_RAW);
            $ensembl = $params['ensembl'];
            $gene_exp = $params['magma_exp'];
            $MHC = $params['exMHC'];
            $bkgval = $params['genetype'];
            $adjPmeth = "fdr_bh";
            $adjPcut = 0.05;
            $minOverlap = 2;

            $gval = null;
            $f = fopen(Storage::path($s2gfiledir . 'genes.txt'), 'r');
            fgetcsv($f, 0, "\t");
            while ($row = fgetcsv($f, 0, "\t")) {
                if ($gval == null) {
                    $gval = $row[0];
                } else {
                    $gval = $gval . ":" . $row[0];
                }
            }

            $paramfile = $filedir . 'params.config';
            Storage::put($paramfile, "[jobinfo]");
            Storage::append($paramfile, "created_at=$date");
            Storage::append($paramfile, "title=$title");
            Storage::append($paramfile, "snp2geneID=$s2gID");
            Storage::append($paramfile, "snp2geneTitle=$s2gTitle");
            Storage::append($paramfile, "\n[params]");
            Storage::append($paramfile, "gtype=$gtype");
            Storage::append($paramfile, "gval=$gval");
            Storage::append($paramfile, "bkgtype=$bkgtype");
            Storage::append($paramfile, "bkgval=$bkgval");
            Storage::append($paramfile, "MHC=$MHC");
            Storage::append($paramfile, "ensembl=$ensembl");
            Storage::append($paramfile, "gsFileN=0");
            Storage::append($paramfile, "gsFiles=NA");
            Storage::append($paramfile, "gene_exp=$gene_exp");
            Storage::append($paramfile, "adjPmeth=$adjPmeth");
            Storage::append($paramfile, "adjPcut=$adjPcut");
            Storage::append($paramfile, "minOverlap=$minOverlap");

            $data = [
                'id' => $jobID,
                'filedir' => $filedir,
                'gtype' => $gtype,
                'gval' => $gval,
                'bkgtype' => $bkgtype,
                'bkgval' => $bkgval,
                'ensembl' => $ensembl,
                'gene_exp' => $gene_exp,
                'MHC' => $MHC,
                'adjPmeth' => $adjPmeth,
                'adjPcut' => $adjPcut,
                'minOverlap' => $minOverlap
            ];

            return view('pages.gene2func', ['status' => 'query', 'id' => $jobID, 'page' => 'gene2func', 'prefix' => 'gene2func', 'data' => $data]);
        } else {
            $jobID = $checkExists->jobID;
            return redirect("gene2func/" . $jobID);
        }
    }

    public function ExpTsPlot($type, $jobID)
    {
        $filedir = config('app.jobdir') . '/gene2func/' . $jobID . '/';
        $file = "";
        if ($type == "general") {
            $file = $filedir . "ExpTsGeneral.txt";
        } else {
            $file = $filedir . "ExpTs.txt";
        }
        if (file_exists($file)) {
            $f = fopen($file, 'r');
            fgetcsv($f, 0, "\t");
            $data = [];
            $p = [];
            while ($row = fgetcsv($f, 0, "\t")) {
                $p[$row[0]] = $row[3];
                $data[] = [$row[0], $row[3], $row[4]];
            }
            asort($p);
            $order_p = [];
            $i = 0;
            foreach ($p as $key => $value) {
                $order_p[$key] = $i;
                $i++;
            }
            ksort($p);
            $order_alph = [];
            $i = 0;
            foreach ($p as $key => $value) {
                $order_alph[$key] = $i;
                $i++;
            }
            $r = ["data" => $data, "order" => ["p" => $order_p, "alph" => $order_alph]];
            return json_encode($r);
        } else {
            return;
        }
    }
}
