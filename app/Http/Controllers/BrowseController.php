<?php

namespace fuma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use fuma\SubmitJob;
use fuma\Http\Requests;
use fuma\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use View;
use Auth;
use Storage;
use File;
use JavaScript;

class BrowseController extends Controller
{
	public function getGwasList(){
		$results = DB::select('SELECT id, title, author, email, phenotype, publication, sumstats_link, sumstats_ref, notes, created_at, update_at FROM PublicResults ORDER BY ID DESC');
		return response()->json($results);
	}

	public function checkG2F(Request $request){
		$id = $request->input('id');
		$results = collect(DB::select('SELECT g2f_jobID FROM PublicResults WHERE id=?', [$id]))->first();
		return $results->g2f_jobID;
	}

	public function getParams(Request $request){
		$id = $request->input('id');
		$filedir = config('app.jobdir').'/public/'.$id.'/';
        $params = parse_ini_file($filedir."params.config");
        $posMap = $params['posMap'];
        $eqtlMap = $params['eqtlMap'];
        $orcol = $params['orcol'];
        $becol = $params['becol'];
        $secol = $params['secol'];
  	    $ciMap = 0;
  	  if(array_key_exists('ciMap', $params)){
  		  $ciMap = $params['ciMap'];
  	  }
        echo "$posMap:$eqtlMap:$ciMap:$orcol:$becol:$secol";
	}

	public function filedown(Request $request){
      $id = $request->input('id');
	  $prefix = $request->input('prefix');
      $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
      // $zip = new ZipArchive();
      $files = array();
      if($request -> filled('paramfile')){ $files[] = "params.config";}
      if($request -> filled('indSNPfile')){$files[] = "IndSigSNPs.txt";}
      if($request -> filled('leadfile')){$files[] = "leadSNPs.txt";}
      if($request -> filled('locifile')){$files[] = "GenomicRiskLoci.txt";}
      if($request -> filled('snpsfile')){$files[] = "snps.txt"; $files[] = "ld.txt";}
      if($request -> filled('annovfile')){$files[] = "annov.txt";}
      if($request -> filled('annotfile')){$files[] = "annot.txt";}
      if($request -> filled('genefile')){$files[] = "genes.txt";}
      if($request -> filled('eqtlfile')){
		  if(File::exists($filedir."eqtl.txt")){
			  $files[] = "eqtl.txt";
		  }
	  }
	  if($request -> filled('cifile')){
		  if(File::exists($filedir."ci.txt")){
			  $files[] = "ci.txt";
			  $files[] = "ciSNPs.txt";
			  $files[] = "ciProm.txt";
		  }
	  }
      // if($request -> has('exacfile')){$files[] = $filedir."ExAC.txt";}
      if($request -> filled('gwascatfile')){$files[] = "gwascatalog.txt";}
      if($request -> filled('magmafile')){
        $files[] = "magma.genes.out";
        if(File::exists($filedir."magma.sets.out")){
          $files[] = "magma.genes.raw";
          $files[] = "magma.sets.out";
          if(File::exists($filedir."magma.setgenes.out")){
            $files[] = "magma.setgenes.out";
          }
        }
        if(File::exists($filedir."magma_exp.gcov.out")){
          $files[] = "magma_exp.gcov.out";
          $files[] = "magma_exp_general.gcov.out";
        }
      }

      $zip = new \ZipArchive();
	  if($prefix=="public"){
		  $zipfile = $filedir."FUMA_public".$id.".zip";
	  }else{
		  $zipfile = $filedir."FUMA_job".$id.".zip";
	  }

      if(File::exists($zipfile)){
        File::delete($zipfile);
      }
      $zip -> open($zipfile, \ZipArchive::CREATE);
      $zip->addFile(storage_path().'/README', "README");
      foreach($files as $f){
        $zip->addFile($filedir.$f, $f);
      }
      $zip -> close();
      return response() -> download($zipfile);
    }

	public function imgdown(Request $request){
      $svg = $request->input('data');
      $prefix= $request->input('dir');
      $id = $request -> input('id');
      $type = $request->input('type');
      $fileName = $request->input('fileName');
      $svgfile = config('app.jobdir').'/'.$prefix.'/'.$id.'/temp.svg';
      $outfile = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

      $svg = preg_replace("/\),rotate/", ")rotate", $svg);
      $svg = preg_replace("/,skewX\(.+?\),scale\(.+?\)/", "", $svg);
	  if($prefix=="public"){
      	  $fileName .= "_FUMApublic".$id;
	  }else{
	      $fileName .= "_FUMAjob".$id;
	  }
      if($type=="svg"){
        file_put_contents($svgfile, $svg);
        $outfile .= $fileName.'.svg';
        File::move($svgfile, $outfile);
        return response() -> download($outfile);
      }else{
        $outfile .= $fileName.'.'.$type;
        $image = new \Imagick();
        $image->setResolution(300,300);
        $image->readImageBlob('<?xml version="1.0"?>'.$svg);
        $image->setImageFormat($type);
        $image->writeImage($outfile);
        return response() -> download($outfile);
      }
    }

	public function DEGPlot($type, $jobID){
      $filedir = config('app.jobdir').'/public/'.$jobID.'/g2f/';
      $file = "";
      if($type=="general"){
        $file = $filedir."DEGgeneral.txt";
      }else{
        $file = $filedir."DEG.txt";
      }
      if(file_exists($file)){
        $f = fopen($file, 'r');
        fgetcsv($f, 0, "\t");
        $data = [];
        $upp = [];
        $downp = [];
        $twop = [];
        $alph = [];
        $i = 0;
        while($row=fgetcsv($f, 0, "\t")){
          $p[$row[1]] = $row[4];
          $data[] = [$row[0], $row[1], $row[4], $row[5]];
          if($row[0]=="DEG.up"){
            $upp[$row[1]] = $row[4];
            $alph[$row[1]] = $i;
            $i++;
          }else if($row[0]=="DEG.down"){
            $downp[$row[1]] = $row[4];
          }else{
            $twop[$row[1]] = $row[4];
          }
        }
        asort($upp);
        asort($downp);
        asort($twop);
        $order_up = [];
        $order_down = [];
        $order_two = [];
        $i = 0;
        foreach ($upp as $key => $value) {
          $order_up[$key] = $i;
          $i++;
        }
        $i = 0;
        foreach ($downp as $key => $value) {
          $order_down[$key] = $i;
          $i++;
        }
        $i = 0;
        foreach ($twop as $key => $value) {
          $order_two[$key] = $i;
          $i++;
        }

        $r = ["data"=>$data, "order"=>["up"=>$order_up, "down"=>$order_down, "two"=>$order_two, "alph"=>$alph]];
        return json_encode($r);
      }else{
        return;
      }
    }

	public function geneTable(Request $request){
      $jobID = $request->input('id');
      $filedir = config('app.jobdir').'/public/'.$jobID.'/g2f/';
      if(file_exists($filedir."geneTable.txt")){
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
      }else{
        echo '{"data": []}';
      }

    }

	public function g2f_filedown(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/g2f/';
		$files = [];
		if($request->filled('summaryfile')){$files[] = "summary.txt";}
		if($request->filled('paramfile')){$files[] = "params.config";}
		if($request->filled('geneIDfile')){$files[] = "geneIDs.txt";}
		if($request->filled('expfile')){
			$tmp = File::glob($filedir."*_exp.txt");
			for($i=0; $i<count($tmp); $i++){
				$files[] = preg_replace("/.*\/(.*_exp.txt)/", "$1", $tmp[$i]);
			}
		}
		if($request->filled('DEGfile')){
			$tmp = File::glob($filedir."*_DEG.txt");
			for($i=0; $i<count($tmp); $i++){
				$files[] = preg_replace("/.*\/(.*_DEG.txt)/", "$1", $tmp[$i]);
			}
		}
		if($request->filled('gsfile')){$files[] = "GS.txt";}

		$zip = new \ZipArchive();
		if($prefix=="public/g2f"){
			$zipfile = $filedir."FUMA_gene2func_public".$id.".zip";
		}else{
			$zipfile = $filedir."FUMA_gene2func".$id.".zip";
		}
		if(File::exists($zipfile)){
			File::delete($zipfile);
		}
		$zip -> open($zipfile, \ZipArchive::CREATE);
		$zip->addFile(storage_path().'/README_g2f', "README_g2f");
		foreach($files as $f){
			$zip->addFile($filedir.$f, $f);
		}
		$zip -> close();
		return response() -> download($zipfile);
	}

}
