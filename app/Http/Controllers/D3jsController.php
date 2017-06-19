<?php

namespace IPGAP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use IPGAP\Http\Requests;
use IPGAP\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use View;
use Storage;
use File;
use JavaScript;

class D3jsController extends Controller
{
    public function __construct()
    {
        // Protect this Controller
        $this->middleware('auth');
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
		$xMin = $request->input("xMin");
		$xMax = $request->input("xMax");
		$eqtlgenes = $request->input("eqtlgenes");

		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		$script = storage_path()."/scripts/annotPlot.R";
		$data = shell_exec("Rscript $script $filedir $chrom $xMin $xMax $eqtlgenes");
		$data = explode("\n", $data);
		$data = $data[count($data)-1];
		return $data;
	}

    public function getPrioGenes($jobID){
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $genes = [];
      $f = fopen($filedir.'genes.txt', 'r');
      fgetcsv($f, 0, "\t");
      while($row=fgetcsv($f, 0, "\t")){
        $genes[] = $row[1];
      }
      echo json_encode($genes);
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
        echo json_encode($all_rows);
      }
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
            // $all_rows[] = array_combine($header, $row);
            $all_rows[] = $row;
          }
          echo json_encode($all_rows);
        }
      }else if($file == "magma.genes.out"){
        if(file_exists($f)){
          $file = fopen($f, 'r');
          $header = fgetcsv($file, 0, "\t");
          $all_rows = array();
          while($row = fgetcsv($file, 0, "\t")){
            if($row[1]=="X"){
              $row[1]=23;
            }
            $row[1] = (int)$row[1];
            $row[2] = (int)$row[2];
            $row[3] = (int)$row[3];
            $row[8] = (float)$row[8];
            // $all_rows[] = array_combine($header, $row);
            $all_rows[] = array($row[1], $row[2], $row[3], $row[8], $row[9]);
          }
          echo json_encode($all_rows);
        }
      }

    }

    public function QQplot($prefix, $id, $plot){
       $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

      if(strcmp($plot,"SNP")==0){
      	$file=$filedir."QQSNPs.txt";
      	$f = fopen($file, 'r');
      	$all_row = array();
        $head = fgetcsv($f, 0, "\t");
        while($row = fgetcsv($f, 0, "\t")){
          $all_row[] = array_combine($head, $row);
        }
      	echo json_encode($all_row);

      }else if(strcmp($plot,"Gene")==0){
      	$file=$filedir."magma.genes.out";
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
      	echo json_encode($all_row);
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
}
