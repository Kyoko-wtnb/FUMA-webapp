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

class FumaController extends Controller
{
	protected $user;

    public function __construct(){
        // Protect this Controller
        $this->middleware('auth');

        // Store user
        $this->user = Auth::user();
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

		$script = storage_path().'/scripts/dt.py';
		$out = shell_exec("python $script $filedir $fin $draw $cols $order_column $order_dir $start $length $search");
		echo $out;
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
		$svg = preg_replace("/,skewX\(.+?\),scale\(.+?\)/", "", $svg);
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

	public function d3js_textfile($prefix, $id, $file){
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
}
