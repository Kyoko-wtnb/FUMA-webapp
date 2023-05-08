<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Helper;
use DB;

class FumaController extends Controller
{
    public function appinfo()
    {
        // $app_config = parse_ini_file(Helper::scripts_path('app.config'), false, INI_SCANNER_RAW);
        // $out["ver"] = $app_config['FUMA'];
        $out["ver"] = 'To be removed';
        $out["user"] = DB::table('users')->count();
        $out["s2g"] = collect(DB::select("SELECT MAX(jobID) as max from SubmitJobs"))->first()->max;
        $out["g2f"] = collect(DB::select("SELECT MAX(jobID) as max from gene2func"))->first()->max;
        $out["run"] = collect(DB::select("SELECT COUNT(jobID) as count from SubmitJobs WHERE status='RUNNING'"))->first()->count;
        $out["que"] = collect(DB::select("SELECT COUNT(jobID) as count from SubmitJobs WHERE status='QUEUED'"))->first()->count;
        return json_encode($out);
    }

    public function DTfile(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $fin = $request->input('infile');
        $cols = $request->input('header');
        $cols = explode(":", $cols);
        $filedir = config('app.temp_abs_path_to_jobs') . '/' . $prefix . '/' . $id . '/';
        $f = $filedir . $fin;
        if (file_exists($f)) {
            $file = fopen($f, 'r');
            $all_rows = array();
            $head = fgetcsv($file, 0, "\t");
            $index = array();
            foreach ($cols as $c) {
                if (in_array($c, $head)) {
                    $index[] = array_search($c, $head);
                } else {
                    $index[] = -1;
                }
            }
            while ($row = fgetcsv($file, 0, "\t")) {
                $temp = [];
                foreach ($index as $i) {
                    if ($i == -1) {
                        $temp[] = "NA";
                    } else {
                        $temp[] = $row[$i];
                    }
                }
                $all_rows[] = $temp;
            }
            $json = (array('data' => $all_rows));

            echo json_encode($json);
        } else {
            echo '{"data":[]}';
        }
    }

    public function DTfileServerSide(Request $request)
    {
        $jobID = $request->input('id');
        $fin = $request->input('infile');
        $cols = $request->input('header');

        $draw = $request->input('draw');
        $order = $request->input('order');
        $order_column = $order[0]["column"];
        $order_dir = $order[0]["dir"];
        $start = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search');
        $search = $search['value'];

        $uuid = Str::uuid();
        $new_cmd = "docker run --rm --name job-$jobID-$uuid -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job -w /app laradock-fuma-dt /bin/sh -c 'python dt.py job/ $fin $draw $cols $order_column $order_dir $start $length $search'";
        $out = shell_exec($new_cmd);
        echo $out;


        // TODO: The following code (result of chatgpt) with some modifications could be used as a drop in replacement
        // for the dt.py code and thus to replace the docker container.

        // function read_csv($filedir, $filename) {
        //     $data = array();
        //     $file = fopen($filedir.$filename, "r");
        //     $header = fgetcsv($file, 0, "\t");
        
        //     while ($row = fgetcsv($file, 0, "\t")) {
        //         array_push($data, $row);
        //     }
        
        //     fclose($file);
        //     return array("header" => $header, "data" => $data);
        // }
        
        // function dt($filedir, $filename, $draw, $headers, $sort_col, $sort_dir, $start, $length, $search = null) {
        //     if ($search) {
        //         $search = strtolower($search);
        //     }
        
        //     $cols = explode(":", $headers);
        //     $fin = read_csv($filedir, $filename)["data"];
        //     $header = read_csv($filedir, $filename)["header"];
        //     $hind = array_map(function($x) use ($header) { return array_search($x, $header); }, $cols);
        //     $fin = array_map(function($row) use ($hind) { return array_intersect_key($row, array_flip($hind)); }, $fin);
        
        //     $total = count($fin);
        //     $filt = $total;
        
        //     if ($search) {
        //         $n = array();
        //         foreach ($hind as $i) {
        //             $tmp = array_filter($fin, function($row) use ($i, $search) {
        //                 return (strpos(strtolower($row[$i]), $search) !== false);
        //             });
        //             $n = array_merge($n, array_keys($tmp));
        //         }
        //         $n = array_unique($n);
        //         $fin = array_intersect_key($fin, array_flip($n));
        //         $filt = count($fin);
        //     }
        
        //     if ($filt > 0) {
        //         usort($fin, function($a, $b) use ($sort_col, $sort_dir) {
        //             $cmp = strcmp($a[$sort_col], $b[$sort_col]);
        //             return ($sort_dir == 'asc') ? $cmp : -$cmp;
        //         });
        //         $fin = array_slice($fin, $start, $length);
        //     } else {
        //         $fin = array();
        //     }
        
        //     $out = array(
        //         "draw" => $draw,
        //         "recordsTotal" => $total,
        //         "recordsFiltered" => $filt,
        //         "data" => $fin
        //     );
        //     return json_encode($out);
        // }
    }

    public function paramTable(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        $params = parse_ini_string(Storage::get($filedir . 'params.config'), false, INI_SCANNER_RAW);
        $out = [];
        foreach ($params as $key => $value) {
            $out[] = [$key, $value];
        }
        return json_encode($out);
    }

    public function sumTable(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.temp_abs_path_to_jobs') . '/' . $prefix . '/' . $id . '/';
        $table = '<table class="table table-bordered" style="width:auto;margin-right:auto; margin-left:auto; text-align: right;"><tbody>';
        $lines = file($filedir . "summary.txt");
        foreach ($lines as $l) {
            $line = preg_split("/[\t]/", chop($l));
            $table .= "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td></tr>";
        }
        $table .= "</tbody></table>";

        return $table;
    }

    public function manhattan($prefix, $id, $file)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';

        $f = $filedir . $file;
        if ($file == "manhattan.txt") {
            if (file_exists($f)) {
                $file = fopen($f, 'r');
                $header = fgetcsv($file, 0, "\t");
                $all_rows = [];
                while ($row = fgetcsv($file, 0, "\t")) {
                    $row[0] = (int)$row[0];
                    $row[1] = (int)$row[1];
                    $row[2] = (float)$row[2];
                    $all_rows[] = $row;
                }
                return json_encode($all_rows);
            }
        } else if ($file == "magma.genes.out") {
            if (file_exists($f)) {
                $file = fopen($f, 'r');
                $header = fgetcsv($file, 0, "\t");
                $all_rows = array();
                while ($row = fgetcsv($file, 0, "\t")) {
                    if ($row[1] == "X" | $row[1] == "x") {
                        $row[1] = 23;
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

    // deprecated to be removed
    // public function QQplot(Request $request)
    // {
    //     $jobID = $request->input('jobID');
    //     $fileNames = $request->input('fileNames');
    //     $plot = $request->input('plot');
    //     $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';

    //     if (strcmp($plot, "Gene") == 0) {
    //         $result = Helper::getFilesContents($filedir, $fileNames);
    //         $data = $result[$fileNames[0]];

    //         $obs = array();
    //         $exp = array();
    //         $c = 0;
    //         foreach ($data as $row) {
    //             $c++;
    //             $obs[] = -log10($row["P"]);
    //         }

    //         sort($obs);
    //         $step = (1 - 1 / $c) / $c;
    //         $head = ["obs", "exp", "n"];
    //         $all_row = array();
    //         for ($i = 0; $i < $c; $i++) {
    //             $all_row[] = array_combine($head, [$obs[$i], -log10(1 - $i * $step), $i + 1]);
    //         }
    //         return response()->json([$fileNames[0] => $all_row]);

    //         // $file = $filedir . "magma.genes.out";
    //         // if (Storage::exists($file)) {
    //         //     $f = Storage::get($file);
    //         //     $obs = array();
    //         //     $exp = array();
    //         //     $c = 0;
    //         //     str_getcsv($f, "\t");
    //         //     while ($row = str_getcsv($f, "\t")) {
    //         //         $c++;
    //         //         $obs[] = -log10($row[8]);
    //         //     }
    //         //     sort($obs);
    //         //     $step = (1 - 1 / $c) / $c;
    //         //     $head = ["obs", "exp", "n"];
    //         //     $all_row = array();
    //         //     for ($i = 0; $i < $c; $i++) {
    //         //         $all_row[] = array_combine($head, [$obs[$i], -log10(1 - $i * $step), $i + 1]);
    //         //     }
    //         //     return response()->json($all_row);
    //         // }
    //     }
    // }

    // deprecated to be removed
    // public function MAGMA_expPlot($prefix, $jobID)
    // {
    //     $filedir = config('app.jobdir') . '/' . $prefix . '/' . $jobID . '/';
    //     $script = scripts_path('magma_expPlot.py');
    //     $data = shell_exec("python $script $filedir");
    //     return $data;
    // }

    public function locusPlot(Request $request)
    {
        $jobID = $request->input('id');
        $prefix = $request->input('prefix');
        $type = $request->input('type');
        $rowI = $request->input('rowI');
        // $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';

        $uuid = Str::uuid();
        $cmd = "docker run --rm --name job-$jobID-$uuid -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job -w /app laradock-fuma-locus_plot /bin/sh -c 'python locusPlot.py job/ $rowI $type'";
        $out = shell_exec($cmd);
        return $out;
    }

    public function annotPlot(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        $type = $request->input('annotPlotSelect');
        $rowI = $request->input('annotPlotRow');

        $GWAS = 0;
        $CADD = 0;
        $RDB = 0;
        $Chr15 = 0;
        $eqtl = 0;
        $ci = 0;
        if ($request->filled('annotPlot_GWASp')) {
            $GWAS = 1;
        }
        if ($request->filled('annotPlot_CADD')) {
            $CADD = 1;
        }
        if ($request->filled('annotPlot_RDB')) {
            $RDB = 1;
        }
        if ($request->filled('annotPlot_Chrom15')) {
            $Chr15 = 1;
            $temp = $request->input('annotPlotChr15Ts');
            $Chr15cells = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $Chr15cells[] = $ts;
                }
            }
            $Chr15cells = implode(":", $Chr15cells);
        } else {
            $Chr15cells = "NA";
        }
        if ($request->filled('annotPlot_eqtl')) {
            $eqtl = 1;
        }
        if ($request->filled('annotPlot_ci')) {
            $ci = 1;
        }

        return view('pages.annotPlot', [
            'id' => $id,
            'prefix' => $prefix,
            'type' => $type,
            'rowI' => $rowI,
            'GWASplot' => $GWAS,
            'CADDplot' => $CADD,
            'RDBplot' => $RDB,
            'eqtlplot' => $eqtl,
            'ciplot' => $ci,
            'Chr15' => $Chr15,
            'Chr15cells' => $Chr15cells,
            'page' => 'snp2gene/annotPlot'
        ]);
    }

    public function annotPlotGetData(Request $request)
    {
        $jobID = $request->input("id");
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

        // $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';

        $uuid = Str::uuid();
        $ref_data_path_on_host = config('app.ref_data_on_host_path');

        $cmd = "docker run --rm --name job-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job -w /app laradock-fuma-annot_plot /bin/sh -c 'python annotPlot.py job/ $type $rowI $GWASplot $CADDplot $RDBplot $eqtlplot $ciplot $Chr15 $Chr15cells'";

        $data = shell_exec($cmd);
        return $data;
    }

    public function annotPlotGetGenes(Request $request)
    {
        $jobID = $request->input("id");
        $prefix = $request->input("prefix");
        $chrom = $request->input("chrom");
        $eqtlplot = $request->input("eqtlplot");
        $ciplot = $request->input("ciplot");
        $xMin = $request->input("xMin");
        $xMax = $request->input("xMax");
        $eqtlgenes = $request->input("eqtlgenes");

        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $jobID . '/';
        $params = parse_ini_string(Storage::get($filedir . 'params.config'), false, INI_SCANNER_RAW);
        $ensembl = $params['ensembl'];

        $uuid = Str::uuid();
        $ref_data_path_on_host = config('app.ref_data_on_host_path');

        $cmd = "docker run --rm --name job-$jobID-$uuid -v $ref_data_path_on_host:/data -v " . config('app.abs_path_of_jobs_on_host') . "/$jobID/:/app/job -w /app laradock-fuma-annot_plot /bin/sh -c 'Rscript annotPlot.R job/ $chrom $xMin $xMax $eqtlgenes $eqtlplot $ciplot $ensembl'";

        $data = shell_exec($cmd);
        $data = explode("\n", $data);
        $data = $data[count($data) - 1];
        return $data;
    }

    public function legendText(Request $request)
    {
        $fileNames = $request->input('fileNames');
        $filedir = config('app.jobdir') . '/legends/';

        $result = Helper::getFilesContents($filedir, $fileNames);

        // Convert the array to a JSON string.
        return response()->json($result);
    }

    public function circos_chr(Request $request)
    {
        $id = $request->input("id");
        $filedir = config('app.jobdir') . '/jobs/' . $id . '/circos/';

        $file_paths = Helper::my_glob($filedir, "/circos_chr.*\.png/");
        $data = array();
        foreach ($file_paths as $path) {
            $name = preg_replace('/.+\/circos_chr(\d+)\.png/', '$1', $path);
            $data[$name] = base64_encode(Storage::get($path));
        }
        return response()->json(array($data));
    }

    public function circos_image($prefix, $id, $file)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/circos/';
        $f = File::get($filedir . $file);
        $type = File::mimeType($filedir . $file);

        return response($f)->header("Content-Type", $type);
    }

    public function circosDown(Request $request)
    {
        $jobID = $request->input('id');
        $type = $request->input('type');
        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/circos/';
        $zip = new \ZipArchive();
        $zipfile = "job" . $jobID . "_circos_" . $type . ".zip";

        if ($type == "conf") {
            $file_paths = Helper::my_glob($filedir, "/.*\.txt/");
            foreach ($file_paths as $path) {
                $files[] = preg_replace("/.+\/(\w+\.txt)/", '$1', $path);
            }
        } else {
            $file_paths = Helper::my_glob($filedir, "/.*\." . $type . "/");
            foreach ($file_paths as $path) {
                $files[] = preg_replace("/.+\/(\w+\.$type)/", '$1', $path);
            }
        }

        $zip->open(Storage::path($filedir . $zipfile), \ZipArchive::CREATE);
        foreach ($files as $f) {
            $abs_path = Storage::path($filedir . $f);
            $zip->addFile($abs_path, $f);
        }
        $zip->close();
        
        return response()->download(Storage::path($filedir . $zipfile))->deleteFileAfterSend(true);
    }

    public function imgdown(Request $request)
    {
        $svg = $request->input('data');
        $jobID = $request->input('id');
        $type = $request->input('type');
        $fileName = $request->input('fileName') . "_FUMA_" . "jobs" . $jobID;

        $svg = preg_replace("/\),rotate/", ")rotate", $svg);
        $svg = preg_replace("/,skewX\(.+?\)/", "", $svg);
        $svg = preg_replace("/,scale\(.+?\)/", "", $svg);

        if ($type == "svg") {
            $filename = $fileName . '.svg';
            return response()->streamDownload(function () use ($svg) {
                echo $svg;
            }, $filename);
        } else {
            $fileName = $fileName . '.' . $type;
            $image = new \Imagick();
            $image->setResolution(300, 300);
            $image->readImageBlob('<?xml version="1.0"?>' . $svg);
            $image->setImageFormat($type);
            return response()->streamDownload(function () use ($image) {
                echo $image;
            }, $fileName);
        }
    }

    public function d3text($prefix, $id, $file)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        $f = $filedir . $file;
        if (file_exists($f)) {
            $file = fopen($f, 'r');
            $header = fgetcsv($file, 0, "\t");
            $all_rows = array();
            while ($row = fgetcsv($file, 0, "\t")) {
                $all_rows[] = array_combine($header, $row);
            }
            echo json_encode($all_rows);
        }
    }

    public function g2f_filedown(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }
        $files = [];
        if ($request->filled('summaryfile')) {
            $files[] = "summary.txt";
        }
        if ($request->filled('paramfile')) {
            $files[] = "params.config";
        }
        if ($request->filled('geneIDfile')) {
            $files[] = "geneIDs.txt";
        }
        if ($request->filled('expfile')) {
            $tmp = File::glob($filedir . "*_exp.txt");
            for ($i = 0; $i < count($tmp); $i++) {
                $files[] = preg_replace("/.*\/(.*_exp.txt)/", "$1", $tmp[$i]);
            }
        }
        if ($request->filled('DEGfile')) {
            $tmp = File::glob($filedir . "*_DEG.txt");
            for ($i = 0; $i < count($tmp); $i++) {
                $files[] = preg_replace("/.*\/(.*_DEG.txt)/", "$1", $tmp[$i]);
            }
        }
        if ($request->filled('gsfile')) {
            $files[] = "GS.txt";
        }
        if ($request->filled('gtfile')) {
            $files[] = "geneTable.txt";
        }

        $zip = new \ZipArchive();
        if ($prefix == "public") {
            $zipfile = $filedir . "FUMA_gene2func_public" . $id . ".zip";
        } else {
            $zipfile = $filedir . "FUMA_gene2func" . $id . ".zip";
        }
        if (File::exists($zipfile)) {
            File::delete($zipfile);
        }
        $zip->open($zipfile, \ZipArchive::CREATE);
        $zip->addFile(public_path() . '/README_g2f', "README_g2f");
        foreach ($files as $f) {
            $zip->addFile($filedir . $f, $f);
        }
        $zip->close();
        return response()->download($zipfile);
    }

    public function download_variants(Request $request)
    {
        $code = $request->input('variant_code');
        # Log::error("Variant code $code");
        $path = null;
        $name = null;
        switch ($code) {
            case "ALL":
                $name = "1KGphase3ALLvariants.txt.gz";
                break;
            case "AFR":
                $name = "1KGphase3AFRvariants.txt.gz";
                break;
            case "AMR":
                $name = "1KGphase3AMRvariants.txt.gz";
                break;
            case "EAS":
                $name = "1KGphase3EASvariants.txt.gz";
                break;
            case "EUR":
                $name = "1KGphase3EURvariants.txt.gz";
                break;
            case "SAS":
                $name = "1KGphase3SASvariants.txt.gz";
                break;
            default:
                return redirect()->back();
        }
        $path = config("app.downloadsDir") . "/$name";
        # Log::error("Variant path $path");
        $headers = array('Content-Type: application/gzip');
        return response()->download($path, $name, $headers);
    }

    public function g2f_d3text($prefix, $id, $file)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }
        $f = $filedir . $file;
        if (file_exists($f)) {
            $file = fopen($f, 'r');
            $header = fgetcsv($file, 0, "\t");
            $all_rows = array();
            while ($row = fgetcsv($file, 0, "\t")) {
                $all_rows[] = array_combine($header, $row);
            }
            echo json_encode($all_rows);
        }
    }

    public function g2f_paramTable(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }
        $params = parse_ini_string(Storage::get($filedir . 'params.config'), false, INI_SCANNER_RAW);
        $out = [];
        foreach ($params as $key => $value) {
            $out[] = [$key, $value];
        }
        return json_encode($out);
    }

    public function g2f_sumTable(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }

        $out = [["No sumary table found.", "GENE2FUNC Job ID:" . $id]];
        if (file_exists($filedir . "summary.txt")) {
            $lines = file($filedir . "summary.txt");
            $out = [];
            foreach ($lines as $l) {
                $l = preg_split("/\t/", chop($l));
                $out[] = [$l[0], $l[1]];
            }
        }
        return json_encode($out);
    }

    public function expDataOption(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }
        $params = parse_ini_string(Storage::get($filedir . 'params.config'), false, INI_SCANNER_RAW);
        return $params['gene_exp'];
    }

    public function expPlot($prefix, $id, $dataset)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }
        $script = scripts_path('g2f_expPlot.py');
        $data = shell_exec("python $script $filedir $dataset");
        return $data;
    }

    public function DEGPlot($prefix, $id)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }
        $script = scripts_path('g2f_DEGPlot.py');
        $data = shell_exec("python $script $filedir");
        return $data;
    }

    public function geneTable(Request $request)
    {
        $id = $request->input('id');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }
        if (file_exists($filedir . "geneTable.txt")) {
            $f = fopen($filedir . "geneTable.txt", 'r');
            $head = fgetcsv($f, 0, "\t");
            $head[] = "GeneCard";
            $all_rows = [];
            while ($row = fgetcsv($f, 0, "\t")) {
                if (strcmp($row[3], "NA") != 0) {
                    $row[3] = '<a href="https://www.omim.org/entry/' . $row[3] . '" target="_blank">' . $row[3] . '</a>';
                }
                if (strcmp($row[5], "NA") != 0) {
                    $db = explode(":", $row[5]);
                    $row[5] = "";
                    foreach ($db as $i) {
                        if (strlen($row[5]) == 0) {
                            $row[5] = '<a href="https://www.drugbank.ca/drugs/' . $i . '" target="_blank">' . $i . '</a>';
                        } else {
                            $row[5] .= ', <a href="https://www.drugbank.ca/drugs/' . $i . '" target="_blank">' . $i . '</a>';
                        }
                    }
                }
                $row[] = '<a href="http://www.genecards.org/cgi-bin/carddisp.pl?gene=' . $row[2] . '" target="_blank">GeneCard</a>';
                $all_rows[] = array_combine($head, $row);
            }

            $json = array('data' => $all_rows);
            return json_encode($json);
        } else {
            return '{"data": []}';
        }
    }
}
