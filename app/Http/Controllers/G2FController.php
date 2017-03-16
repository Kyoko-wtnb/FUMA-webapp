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

  public function ExpTsPlot($type, $jobID){
    $filedir = config('app.jobdir').'/gene2func/'.$jobID.'/';
    $file = "";
    if($type=="general"){
      $file = $filedir."ExpTsGeneral.txt";
    }else{
      $file = $filedir."ExpTs.txt";
    }
    if(file_exists($file)){
      $f = fopen($file, 'r');
      fgetcsv($f, 0, "\t");
      $data = [];
      $p = [];
      while($row=fgetcsv($f, 0, "\t")){
        $p[$row[0]] = $row[3];
        $data[] = [$row[0], $row[3], $row[4]];
      }
      asort($p);
      $order_p = [];
      $i = 0;
      foreach ($p as $key => $value) {
        $order_p[$key] = $i;
        $i++;
      }
      ksort($p);
      $order_alph = [];
      $i = 0;
      foreach ($p as $key => $value) {
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
