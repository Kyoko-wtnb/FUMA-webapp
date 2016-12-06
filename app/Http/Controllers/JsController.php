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
      $table = '<table class="table table-bordered" style="width:auto;"><tr><td>email</td><td>'.$row->email
        .'</td></tr><tr><td>job title</td><td>'.$row->title.'</td></tr><tr><td>job submitted</td><td>'
        .$row->created_date."</td></tr>";
      $filedir = storage_path().'/jobs/'.$jobID.'/'; #local
      #webserver $filedir = '/data/IPGAP/jobs/'.$jobID.'/';
      $params = file($filedir."params.txt");
      $table .= "<table>";
      echo $table;
    }

    public function paramTable(Request $request){
      $filedir = $request -> input('filedir');
      //  style="display:block; overflow-y:scroll; height: 500px;"
      $table = '<table class="table table-striped" style="width: 100%; margin-left: 10px; margin-right: 10px;ext-align: right;"><tbody>';
      $lines = file($filedir."params.txt");
      foreach($lines as $l){
        $line = preg_split("/[\t]/", chop($l));
        $table .= "<tr><td>".$line[0].'</td><td style="word-break: break-all;">'.$line[1]."</td></tr>";
      }
      $table .= "</tbody></table>";
      echo $table;
    }

    public function sumTable(Request $request){
        $filedir = $request -> input('filedir');
        $table = '<table class="table table-bordered" style="width:auto;margin-right:auto; margin-left:auto; text-align: right;"><tbody>';
        $lines = file($filedir."summary.txt");
        foreach($lines as $l){
          $line = preg_split("/[\t]/", chop($l));
          $table .= "<tr><td>".$line[0]."</td><td>".$line[1]."</td></tr>";
        }
        $table .= "</tbody></table>";

        echo $table;
    }

    public function geneTable(Request $request){
      $jobID = $request->input('id');
      $filedir = storage_path()."/jobs/".$jobID."/"; #local
      #webserver $filedir = "/data/IPGAP/jobs/".$jobID."/";
      $f = fopen($filedir."geneTable.txt", 'r');
      $head = fgetcsv($f, 0, "\t");
      $head[] = "GeneCard";
      $all_rows = [];
      while($row = fgetcsv($f, 0, "\t")){
        if(strcmp($row[3], "NA")!=0){
          $row[3] = '<a href="https://www.omim.org/entry/'.$row[3].'" target="_blank">'.$row[3].'</a>';
        }
        if(strcmp($row[5], "NA")!=0){
          $db = explode(":", $row[5]);
          $row[5] = "";
          foreach ($db as $i){
            if(strlen($row[5])==0){
              $row[5] = '<a href="https://www.drugbank.ca/drugs/'.$i.'" target="_blank">'.$i.'</a>';
            }else{
              $row[5] .= ', <a href="https://www.drugbank.ca/drugs/'.$i.'" target="_blank">'.$i.'</a>';
            }
          }
        }
        $row[] = '<a href="http://www.genecards.org/cgi-bin/carddisp.pl?gene='.$row[2].'" target="GeneCards_iframe">GeneCard</a>';
        $all_rows[] = array_combine($head, $row);
      }

      $json = array('data'=>$all_rows);
      echo json_encode($json);
    }

}
