<?php

namespace fuma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use fuma\SubmitJob;
use fuma\Http\Requests;
use fuma\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Log;
use View;
use Auth;
use Storage;
use File;
use JavaScript;
use Mail;
// use fuma\User;

class FumaController extends Controller
{
	public function appinfo(){
		$app_config = parse_ini_file(scripts_path("app.config"), false, INI_SCANNER_RAW);
		$out["ver"] = $app_config['FUMA'];
		$out["user"] = DB::table('users')->count();
		$out["s2g"] = collect(DB::select("SELECT MAX(jobID) as max from SubmitJobs"))->first()->max;
		$out["g2f"] = collect(DB::select("SELECT MAX(jobID) as max from gene2func"))->first()->max;
		$out["run"] = collect(DB::select("SELECT COUNT(jobID) as count from SubmitJobs WHERE status='RUNNING'"))->first()->count;
		$out["que"] = collect(DB::select("SELECT COUNT(jobID) as count from SubmitJobs WHERE status='QUEUED'"))->first()->count;
		return json_encode($out);
	}

	public function DTfile(Request $request){
		$id = $request -> input('id');
		$prefix = $request -> input('prefix');
		$fin = $request -> input('infile');
		$cols = $request -> input('header');
		$cols = explode(":", $cols);
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$f = $filedir.$fin;
		if(file_exists($f)){
			$file = fopen($f, 'r');
			$all_rows = array();
			$head = fgetcsv($file, 0, "\t");
			$index = array();
			foreach($cols as $c){
				if(in_array($c, $head)){
					$index[] = array_search($c, $head);
				}else{
					$index[] = -1;
				}
			}
			while($row = fgetcsv($file, 0, "\t")){
				$temp = [];
				foreach($index as $i){
					if($i==-1){
						$temp[] = "NA";
					}else{
						$temp[] = $row[$i];
					}
				}
				$all_rows[] = $temp;
			}
			$json = (array('data'=> $all_rows));

			echo json_encode($json);
		}else{
			echo '{"data":[]}';
		}
    }

	public function DTfileServerSide(Request $request){
		$id = $request->input('id');
		$prefix = $request -> input('prefix');
		$fin = $request -> input('infile');
		$cols = $request -> input('header');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		$draw = $request -> input('draw');
		$order = $request -> input('order');
		$order_column = $order[0]["column"];
		$order_dir = $order[0]["dir"];
		$start = $request -> input('start');
		$length = $request -> input('length');
		$search = $request -> input('search');
		$search = $search['value'];

		$script = scripts_path('dt.py');
		$out = shell_exec("python $script $filedir $fin $draw $cols $order_column $order_dir $start $length $search");
		echo $out;
	}

	public function paramTable(Request $request){
		$id = $request -> input('id');
		$prefix = $request -> input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);
		$out = [];
		foreach($params as $key=>$value){
			$out[] = [$key, $value];
		}
		return json_encode($out);
    }

	public function sumTable(Request $request){
		$id = $request -> input('id');
		$prefix = $request -> input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$table = '<table class="table table-bordered" style="width:auto;margin-right:auto; margin-left:auto; text-align: right;"><tbody>';
		$lines = file($filedir."summary.txt");
		foreach($lines as $l){
			$line = preg_split("/[\t]/", chop($l));
			$table .= "<tr><td>".$line[0]."</td><td>".$line[1]."</td></tr>";
		}
		$table .= "</tbody></table>";

		return $table;
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

	public function MAGMA_expPlot($prefix, $jobID){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$jobID.'/';
		$script = scripts_path('magma_expPlot.py');
	    $data = shell_exec("python $script $filedir");
		return $data;
	}

	public function locusPlot(Request $request){
      $id = $request->input('id');
	  $prefix = $request->input('prefix');
      $type = $request->input('type');
      $rowI = $request->input('rowI');
      $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

      $script = scripts_path('locusPlot.py');
      $out = shell_exec("python $script $filedir $rowI $type");
      return $out;
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
		if($request -> filled('annotPlot_GWASp')){$GWAS=1;}
		if($request -> filled('annotPlot_CADD')){$CADD=1;}
		if($request -> filled('annotPlot_RDB')){$RDB=1;}
		if($request -> filled('annotPlot_Chrom15')){
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
		if($request -> filled('annotPlot_eqtl')){$eqtl=1;}
		if($request -> filled('annotPlot_ci')){$ci=1;}

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

		$script = scripts_path('annotPlot.py');
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
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);
		$ensembl = $params['ensembl'];

		$script = scripts_path('annotPlot.R');
		$data = shell_exec("Rscript $script $filedir $chrom $xMin $xMax $eqtlgenes $eqtlplot $ciplot $ensembl");
		$data = explode("\n", $data);
		$data = $data[count($data)-1];
		return $data;
	}

	public function legendText($file){
		$f = scripts_path('legends/'.$file);
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
		if($prefix=="public"){
			$zipfile = $filedir."FUMA_public".$id."_circos_".$type.".zip";
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

	public function imgdown(Request $request){
		$svg = $request->input('data');
		$prefix= $request->input('dir');
		$id = $request -> input('id');
		$type = $request->input('type');
		$fileName = $request->input('fileName');
		$svgfile = config('app.jobdir').'/'.$prefix.'/'.$id.'/temp.svg';
		$outfile = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		$svg = preg_replace("/\),rotate/", ")rotate", $svg);
		$svg = preg_replace("/,skewX\(.+?\)/", "", $svg);
		$svg = preg_replace("/,scale\(.+?\)/", "", $svg);
		$fileName .= "_FUMA_".$prefix.$id;
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

	public function d3text($prefix, $id, $file){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		$f = $filedir.$file;
		if(file_exists($f)){
			$file = fopen($f, 'r');
			$header = fgetcsv($file, 0, "\t");
			$all_rows = array();
			while($row = fgetcsv($file, 0, "\t")){
				$all_rows[] = array_combine($header, $row);
			}
			echo json_encode($all_rows);
		}
    }

	public function g2f_filedown(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		if($prefix=="public"){
			$filedir .= 'g2f/';
		}
		$files = [];
		if($request->filled('summaryfile')){$files[] = "summary.txt";}
		if($request->filled('paramfile')){$files[] = "params.config";}
		if($request->filled('geneIDfile')){$files[] = "geneIDs.txt";}
		if($request->filled('expfile')){
			$tmp = File::glob($filedir."*_exp.txt");
			for($i=0; $i<count($tmp); $i++){
				$files[] = preg_replace("/.*\/(.*_exp.txt)/", "$1", $tmp[$i]);
			}
		}
		if($request->filled('DEGfile')){
			$tmp = File::glob($filedir."*_DEG.txt");
			for($i=0; $i<count($tmp); $i++){
				$files[] = preg_replace("/.*\/(.*_DEG.txt)/", "$1", $tmp[$i]);
			}
		}
		if($request->filled('gsfile')){$files[] = "GS.txt";}
		if($request->filled('gtfile')){$files[] = "geneTable.txt";}

		$zip = new \ZipArchive();
		if($prefix=="public"){
			$zipfile = $filedir."FUMA_gene2func_public".$id.".zip";
		}else{
			$zipfile = $filedir."FUMA_gene2func".$id.".zip";
		}
		if(File::exists($zipfile)){
			File::delete($zipfile);
		}
		$zip -> open($zipfile, \ZipArchive::CREATE);
		$zip->addFile(public_path().'/README_g2f', "README_g2f");
		foreach($files as $f){
			$zip->addFile($filedir.$f, $f);
		}
		$zip -> close();
		return response() -> download($zipfile);
	}

	public function download_variants(Request $request) {
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
		$path = config("app.downloadsDir")."/$name";
		# Log::error("Variant path $path");
		$headers = array('Content-Type: application/gzip');
		return response()->download($path, $name, $headers);
	}

	public function g2f_d3text($prefix, $id, $file){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		if($prefix=="public"){
			$filedir .= 'g2f/';
		}
		$f = $filedir.$file;
		if(file_exists($f)){
			$file = fopen($f, 'r');
			$header = fgetcsv($file, 0, "\t");
			$all_rows = array();
			while($row = fgetcsv($file, 0, "\t")){
				$all_rows[] = array_combine($header, $row);
			}
			echo json_encode($all_rows);
		}
    }

	public function g2f_paramTable(Request $request){
		$id = $request -> input('id');
		$prefix = $request -> input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		if($prefix=="public"){
			$filedir .= 'g2f/';
		}
		$params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);
		$out = [];
		foreach($params as $key=>$value){
			$out[] = [$key, $value];
		}
		return json_encode($out);
    }

	public function g2f_sumTable(Request $request){
		$id = $request -> input('id');
		$prefix = $request -> input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		if($prefix=="public"){
			$filedir .= 'g2f/';
		}

		$out = [["No sumary table found.","GENE2FUNC Job ID:".$id]];
		if (file_exists($filedir."summary.txt")) {
			$lines = file($filedir."summary.txt");
			$out = [];
			foreach($lines as $l){
				$l = preg_split("/\t/", chop($l));
				$out[] = [$l[0], $l[1]];
			}
		}
		return json_encode($out);
	}

	public function expDataOption(Request $request){
		$id = $request -> input('id');
		$prefix = $request -> input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		if($prefix=="public"){
			$filedir .= 'g2f/';
		}
		$params = parse_ini_file($filedir.'params.config', false, INI_SCANNER_RAW);
		return $params['gene_exp'];
	}

	public function expPlot($prefix, $id, $dataset){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		if($prefix=="public"){
			$filedir .= 'g2f/';
		}
		$script = scripts_path('g2f_expPlot.py');
	    $data = shell_exec("python $script $filedir $dataset");
		return $data;
	}

	public function DEGPlot($prefix, $id){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		if($prefix=="public"){
			$filedir .= 'g2f/';
		}
		$script = scripts_path('g2f_DEGPlot.py');
	    $data = shell_exec("python $script $filedir");
		return $data;
	}

	public function geneTable(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
		if($prefix=="public"){
			$filedir .= 'g2f/';
		}
		if(file_exists($filedir."geneTable.txt")){
			$f = fopen($filedir."geneTable.txt", 'r');
			$head = fgetcsv($f, 0, "\t");
			$head[] = "GeneCard";
			$all_rows = [];
			while($row = fgetcsv($f, 0, "\t")){
				if(strcmp($row[3], "NA")!=0){
					$row[3] = '<a href="https://www.omim.org/entry/'.$row[3].'" target="_blank">'.$row[3].'</a>';
				}
				if(strcmp($row[5], "NA")!=0){
					$db = explode(":", $row[5]);
					$row[5] = "";
					foreach ($db as $i){
						if(strlen($row[5])==0){
							$row[5] = '<a href="https://www.drugbank.ca/drugs/'.$i.'" target="_blank">'.$i.'</a>';
						}else{
							$row[5] .= ', <a href="https://www.drugbank.ca/drugs/'.$i.'" target="_blank">'.$i.'</a>';
						}
					}
				}
				$row[] = '<a href="http://www.genecards.org/cgi-bin/carddisp.pl?gene='.$row[2].'" target="_blank">GeneCard</a>';
				$all_rows[] = array_combine($head, $row);
			}

			$json = array('data'=>$all_rows);
			return json_encode($json);
		}else{
			return '{"data": []}';
		}
	}

}
