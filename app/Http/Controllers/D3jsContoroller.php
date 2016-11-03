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

class D3jsContoroller extends Controller
{
    public function locusPlot($ldI, $type, $jobID){
      $script = storage_path()."/scripts/locusPlot.R";
      $filedir = storage_path()."/jobs/".$jobID."/";
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
      $filedir = storage_path()."/jobs/".$jobID."/";
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
