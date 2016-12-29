<?php

namespace IPGAP\Http\Controllers;

use Illuminate\Http\Request;

use IPGAP\Http\Requests;

class G2FController extends Controller
{
  public function __construct()
  {
      // Protect this Controller
      $this->middleware('auth');
  }

  public function d3js_textfile($jobID, $file){
    // $filedir = $request -> input('filedir');
    // $file = $request -> input('file');
    $filedir = config('app.jobdir').'/gene2func/'.$jobID.'/';
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
