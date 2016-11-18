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

class JsController extends Controller
{
    public function DTfile(Request $request){
      $filedir = $request -> input('filedir');
      $fin = $request -> input('infile');
      $f = $filedir.$fin;
      if(file_exists($f)){
        $file = fopen($f, 'r');
        $all_row = array();
        $header = fgetcsv($file, 0, "\t");
        while($row = fgetcsv($file, 0, "\t")){
          $all_row[] = array_combine($header, $row);
        }
        $json = (array('data'=> $all_row));

        echo json_encode($json);
      }
    }

    public function jobInfo(Request $request){
      $jobID = $request -> input('jobID');
      $row = DB::select('SELECT * FROM jobs WHERE jobID=?', [$jobID]);
      $row = $row[0];
      $table = '<table class="table table-bordered"><tr><td>email</td><td>'.$row->email
        .'</td></tr><tr><td>job title</td><td>'.$row->title.'</td></tr><tr><td>job submitted</td><td>'
        .$row->created_date."</td></tr><table>";
      echo $table;
    }

    public function paramTable(Request $request){
      $filedir = $request -> input('filedir');
      $table = '<table class="table table-striped" style="width: 500px; margin-right:auto; margin-left:auto; text-align: right;"><tbody style="display:block; overflow-y:scroll; height: 500px;">';
      $lines = file($filedir."params.txt");
      foreach($lines as $l){
        $line = preg_split("/[\t]/", chop($l));
        $table .= "<tr><td>".$line[0]."</td><td>".$line[1]."</td></tr>";
      }
      $table .= "</tbody></table>";
      echo $table;
    }

    public function sumTable(Request $request){
        $filedir = $request -> input('filedir');
        $table = '<table class="table table-striped" style="width:auto;margin-right:auto; margin-left:auto; text-align: right;"><tbody>';
        $lines = file($filedir."summary.txt");
        foreach($lines as $l){
          $line = preg_split("/[\t]/", chop($l));
          $table .= "<tr><td>".$line[0]."</td><td>".$line[1]."</td></tr>";
        }
        $table .= "</tbody></table>";

        echo $table;
    }
}
