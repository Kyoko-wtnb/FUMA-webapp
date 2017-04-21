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

class PaintorController extends Controller
{
	public function PaintorCheck(Request $request){
		$jobID = $request -> input("jobID");
		$errorMsg = "";
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$files = glob($filedir.'PAINTOR/output/*.results');
		if(count($files)==0){
			$err = fopen($filedir."error.log", 'r');
			$paintor = 0;
			while($line = fgets($err)){
				if(preg_match("/paintor.py/", $line)){
					$paintor = 1;
				}
				if($paintor == 0){
					continue;
				}
				if(preg_match("/ERROR|Error/", $line)){
					$errorMsg = $line;
					break;
				}else if(preg_match("/Segmentation fault/", $line)){
					$errorMsg = $line;
					break;
				}
			}
			fclose($err);
			return $errorMsg;
		}else{
			return 1;
		}
	}

	public function PaintorLocus(Request $request){
		$jobID = $request -> input("jobID");
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$loci = [];
		foreach(glob($filedir.'PAINTOR/output/*.results') as $f){
			$f = str_replace(".results", "", $f);
			$f = preg_replace("/.*\//", "", $f);
			$loci[] = $f;
		}
		$loci = implode(":", $loci);
		return $loci;
	}

	public function PaintorTableHeader(Request $request){
		$jobID = $request -> input('jobID');
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
        $params = parse_ini_file($filedir."params.config", false, INI_SCANNER_RAW);
		$annot = $params['annot'];
		$annot = explode(":", $annot);
		for($i=0; $i<count($annot); $i++){
			$annot[$i] = preg_replace("/.*\/(.*)/", '$1', $annot[$i]);
		}
		return json_encode($annot);
	}

	public function PaintorTable(Request $request){
		$jobID = $request -> input("jobID");
		$locus = $request -> input("locus");
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		if(file_exists($filedir.'PAINTOR/output/'.$locus.'.results')){
			$file = fopen($filedir.'PAINTOR/output/'.$locus.'.results', 'r');
			$annot = fopen($filedir.'PAINTOR/input/'.$locus.'.annotations', 'r');
			fgetcsv($file, 0, ' ');
			fgetcsv($annot, 0, ' ');
			$all_rows = [];
			while($row = fgetcsv($file, 0, ' ')){
				$row2 = fgetcsv($annot, 0, ' ');
				$all_rows[] = array_merge($row, $row2);
			}
			return json_encode(array('data'=>$all_rows));
		}else{
        	return '{"data":[]}';
        }
	}

	public function PaintorPlot(Request $request){
		$jobID = $request -> input("jobID");
		$locus = $request -> input("locus");
		$script = storage_path().'/scripts/paintor_plot.py';
		$filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
		$out = shell_exec("python ".$script." ".$filedir." ".$locus);
		// if(file_exists($filedir.'PAINTOR/plots/'.$locus.'.svg')){
		// 	$file = $filedir.'PAINTOR/plots/'.$locus.'.svg';
		// 	$svg = file_get_contents($file);
		// 	return $svg;
		// }else{
		// 	return "";
		// }
		return $out;
	}

}
