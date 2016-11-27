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
    public function locusPlot($ldI, $type, $jobID){
      $script = storage_path()."/scripts/locusPlot.R";
      $filedir = storage_path()."/jobs/".$jobID."/"; #local
      #webserver $filedir = '/data/IPGAP/jobs/'.$jobID.'/';
      exec("Rscript $script $filedir $ldI $type");

      $f = $filedir."locusPlot.txt";
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

    public function d3js_textfile($jobID, $file){
      // $filedir = $request -> input('filedir');
      // $file = $request -> input('file');
      $filedir = storage_path()."/jobs/".$jobID."/"; #local
      #webserver $filedir = '/data/IPGAP/jobs/'.$jobID.'/';
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

    public function manhattan($type, $jobID, $file){
      if($type=="jobs"){ #local
        $filedir = storage_path().'/jobs/'.$jobID.'/'; #local
      }else{ #local
        $filedir = "/media/sf_Documents/VU/Data/gwasDB/".$jobID."/"; #local
      } #local

      #webserver $filedir = "/data/IPGAP/".$type."/".$jobID."/";

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
            $row[1] = (int)$row[1];
            $row[2] = (int)$row[2];
            $row[3] = (int)$row[3];
            $row[8] = (float)$row[8];
            // $all_rows[] = array_combine($header, $row);
            $all_rows[] = array($row[1], $row[2], $row[3], $row[8]);
          }
          echo json_encode($all_rows);
        }
      }

    }

    public function QQplot($type, $jobID, $plot){
      if($type=="jobs"){ #local
        $filedir = storage_path().'/jobs/'.$jobID.'/'; #local
      }else{ #local
        $filedir = "/media/sf_Documents/VU/Data/gwasDB/".$jobID."/"; #local
      } #local

      #webserver $filedir = "/data/IPGAP/".$type."/".$jobID."/";

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

    public function d3js_GWAS_textfile($dbName, $file){
      $filedir = "/media/sf_Documents/VU/Data/gwasDB/".$dbName."/"; #local
      #webserver $filedir = '/data/IPGAP/gwasDB/'.$dbName.'/';
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

    public function d3js_GWAS_QQ($dbName, $type){
      $filedir = "/media/sf_Documents/VU/Data/gwasDB/".$dbName."/"; #local
      #webserver $filedir = '/data/IPGAP/gwasDB/'.$dbName.'/';
      if(strcmp($type,"SNP")==0){
      	$file=$filedir."QQSNPs.txt";
      	$f = fopen($file, 'r');
      	$all_row = array();
        $head = fgetcsv($f, 0, "\t");
        while($row = fgetcsv($f, 0, "\t")){
          $all_row[] = array_combine($head, $row);
        }
      	echo json_encode($all_row);

      }else if(strcmp($type,"Gene")==0){
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
}
