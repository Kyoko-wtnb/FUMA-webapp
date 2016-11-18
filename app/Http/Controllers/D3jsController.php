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
