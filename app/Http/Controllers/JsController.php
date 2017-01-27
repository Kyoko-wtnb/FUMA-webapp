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

    public function __construct()
    {
        // Protect this Controller
        $this->middleware('auth');
    }


    public function DTfile(Request $request){
      $filedir = $request -> input('filedir');
      $fin = $request -> input('infile');
      $cols = $request -> input('header');
      $cols = explode(":", $cols);
      $f = $filedir.$fin;
      if(file_exists($f)){
        $file = fopen($f, 'r');
        $all_rows = array();
        $head = fgetcsv($file, 0, "\t");
        $index = array();
        foreach($cols as $c){
          if(array_search($c, $head)){
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

    public function paramTable(Request $request){
      $filedir = $request -> input('filedir');
      //  style="display:block; overflow-y:scroll; height: 500px;"
      $table = '<table class="table table-striped" style="width: 100%; margin-left: 10px; margin-right: 10px;ext-align: right;"><tbody>';
      $params = parse_ini_file($filedir."params.config");
      // $f = fopen($filedir."params.txt", 'r');

      // while($line = fgetcsv($f, 0, "\t")){
      //   // $line = preg_split("/[\t]/", chop($l));
      //   $table .= "<tr><td>".$line[0].'</td><td style="word-break: break-all;">'.$line[1]."</td></tr>";
      // }

      foreach($params as $key=>$value){
        $table .= "<tr><td>".$key.'</td><td style="word-break: break-all;">'.$value."</td></tr>";
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
      $filedir = config('app.jobdir').'/gene2func/'.$jobID.'/';
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
        $row[] = '<a href="http://www.genecards.org/cgi-bin/carddisp.pl?gene='.$row[2].'" target="_blank">GeneCard</a>';
        $all_rows[] = array_combine($head, $row);
      }

      $json = array('data'=>$all_rows);
      echo json_encode($json);
    }

}
