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
      $jobID = $request->input('jobID');
      $type = $request->input('type');
      $rowI = $request->input('rowI');
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';

      $script = storage_path()."/scripts/locusPlot.py";
      exec("python $script $filedir $rowI $type");
      return;
    }

    public function d3js_textfile($jobID, $file){
      // $filedir = $request -> input('filedir');
      // $file = $request -> input('file');
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
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

    public function manhattan($type, $jobID, $file){
      if($type=="jobs"){
        $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      }else{
        $filedir = config('app.gwasDBdir')."/gwasDB/".$jobID."/";
      }

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

    public function QQplot($type, $jobID, $plot){
       if($type=="jobs"){
         $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
       }else{
         $filedir = config('app.gwasDBdir')."/gwasDB/".$jobID."/";
       }

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

    public function MAGMAtsplot($type, $jobID){
      $filedir = config('app.jobdir').'/jobs/'.$jobID.'/';
      $file = "";
      if($type=="general"){
        $file = $filedir."magma_exp_general.gcov.out";
      }else{
        $file = $filedir."magma_exp.gcov.out";
      }
      if(file_exists($file)){
        $f = fopen($file, 'r');
        $rows = [];
        while($row=fgetcsv($f)){
          $row = preg_split('/\s+/', $row[0]);
          if($row[0]=="#" || $row[0]=="COVAR"){
            continue;
          }else{
            $rows[$row[0]] =$row[5];
          }
        }
        asort($rows);
        $all_rows = [];
        foreach ($rows as $key => $value) {
          $all_rows[] = [$key, $value];
        }
        return json_encode($all_rows);
      }else{
        return;
      }
    }
}
