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
		$results = DB::table('BrowseGwas')->get();
		return response()->json($results);
	}

	public function getParams(Request $request){
		$gwasID = $request->input('gwasID');
		$filedir = config('app.jobdir').'/gwas/'.$gwasID.'/';
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
        echo "$filedir:$posMap:$eqtlMap:$ciMap:$orcol:$becol:$secol";
	}

	public function manhattan($prefix, $id, $file){
	  $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

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

    public function QQplot($prefix, $id, $plot){
       $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

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

	public function MAGMAtsplot($type, $prefix, $jobID){
      $filedir = config('app.jobdir').'/'.$prefix.'/'.$jobID.'/';
      $file = "";
      if($type=="general"){
        $file = $filedir."magma_exp_general.gcov.out";
      }else{
        $file = $filedir."magma_exp.gcov.out";
      }
      if(file_exists($file)){
        $f = fopen($file, 'r');
        $data = [];
        $p = [];
        while($row=fgetcsv($f)){
          $row = preg_split('/\s+/', $row[0]);
          if($row[0]=="#" || $row[0]=="COVAR"){
            continue;
          }else{
            $data[] = [$row[0], $row[5]];
            $p[$row[0]] =$row[5];
          }
        }
        asort($p);
        $order_p = [];
        $i = 0;
        foreach($p as $key => $val){
          $order_p[$key] = [$i];
          $i++;
        }
        ksort($p);
        $order_alph = [];
        $i = 0;
        foreach($p as $key => $val){
          $order_alph[$key] = $i;
          $i++;
        }
        $r = ["data"=>$data, "order"=>["p"=>$order_p, "alph"=>$order_alph]];
        return json_encode($r);
      }else{
        return;
      }
    }

	public function DTfile(Request $request){
      $id = $request -> input('id');
	  $prefix = $request -> input('prefix');
      $fin = $request -> input('infile');
      $cols = $request -> input('header');
      $cols = explode(":", $cols);
	  $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
      $f = $filedir.$fin;
      if(file_exists($f)){
        $file = fopen($f, 'r');
        $all_rows = array();
        $head = fgetcsv($file, 0, "\t");
        $index = array();
        foreach($cols as $c){
          if(in_array($c, $head)){
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

    public function DTfileServerSide(Request $request){
	  $id = $request->input('id');
	  $prefix = $request -> input('prefix');
      $fin = $request -> input('infile');
      $cols = $request -> input('header');
	  $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

      $draw = $request -> input('draw');
      $order = $request -> input('order');
      $order_column = $order[0]["column"];
      $order_dir = $order[0]["dir"];
      $start = $request -> input('start');
      $length = $request -> input('length');
      $search = $request -> input('search');
      $search = $search['value'];

      $script = storage_path().'/scripts/dt.py';
      $out = shell_exec("python $script $filedir $fin $draw $cols $order_column $order_dir $start $length $search");
      echo $out;
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

	public function d3js_textfile($prefix, $id, $file){
	  $file = preg_replace("/:/", "/", $file);
      $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
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

	public function locusPlot(Request $request){
      $id = $request->input('id');
	  $prefix = $request->input('prefix');
      $type = $request->input('type');
      $rowI = $request->input('rowI');
      $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

      $script = storage_path()."/scripts/locusPlot.py";
      $out = shell_exec("python $script $filedir $rowI $type");
      return $out;
    }

	public function circos_chr(Request $request){
		$id = $request->input("id");
		$prefix = $request->input("prefix");
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/circos/';
		$files = File::glob($filedir."circos_chr*.png");
		for($i=0; $i<count($files); $i++){
			$files[$i] = preg_replace('/.+\/circos_chr(\d+)\.png/', '$1', $files[$i]);
		}
		$files = implode(":", $files);
		return $files;
	}

	public function circos_image($prefix, $id, $file){
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/circos/';
		$f = File::get($filedir.$file);
		$type = File::mimeType($filedir.$file);

		return response($f)->header("Content-Type", $type);
	}

	public function circosDown(Request $request){
		$id = $request->input('id');
		$prefix = $request->input('prefix');
		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/circos/';
		$type = $request->input('type');
		$zip = new \ZipArchive();
		if($prefix=="gwas"){
			$zipfile = $filedir."FUMA_gwas".$id."_circos_".$type.".zip";
		}else{
			$zipfile = $filedir."FUMA_job".$id."_circos_".$type.".zip";
		}

		$files = File::glob($filedir."*.".$type);
		for($i=0; $i<count($files); $i++){
			$files[$i] = preg_replace("/.+\/(\w+\.$type)/", '$1', $files[$i]);
		}

		$zip -> open($zipfile, \ZipArchive::CREATE);
        foreach($files as $f){
          $zip->addFile($filedir.$f, $f);
        }
        $zip -> close();
        return response() -> download($zipfile);
	}

	public function filedown(Request $request){
      $id = $request->input('id');
	  $prefix = $request->input('prefix');
      $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
      // $zip = new ZipArchive();
      $files = array();
      if($request -> has('paramfile')){ $files[] = "params.config";}
      if($request -> has('indSNPfile')){$files[] = "IndSigSNPs.txt";}
      if($request -> has('leadfile')){$files[] = "leadSNPs.txt";}
      if($request -> has('locifile')){$files[] = "GenomicRiskLoci.txt";}
      if($request -> has('snpsfile')){$files[] = "snps.txt"; $files[] = "ld.txt";}
      if($request -> has('annovfile')){$files[] = "annov.txt";}
      if($request -> has('annotfile')){$files[] = "annot.txt";}
      if($request -> has('genefile')){$files[] = "genes.txt";}
      if($request -> has('eqtlfile')){
		  if(File::exists($filedir."eqtl.txt")){
			  $files[] = "eqtl.txt";
		  }
	  }
	  if($request -> has('cifile')){
		  if(File::exists($filedir."ci.txt")){
			  $files[] = "ci.txt";
			  $files[] = "ciSNPs.txt";
			  $files[] = "ciGenes.txt";
		  }
	  }
      // if($request -> has('exacfile')){$files[] = $filedir."ExAC.txt";}
      if($request -> has('gwascatfile')){$files[] = "gwascatalog.txt";}
      if($request -> has('magmafile')){
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
	  if($prefix=="gwas"){
		  $zipfile = $filedir."FUMA_gwas".$id.".zip";
	  }else{
		  $zipfile = $filedir."FUMA_job".$id.".zip";
	  }

      if(File::exists($zipfile)){
        File::delete($zipfile);
      }
      // Zipper::make($zipfile)->add($files);
      // sleep(5);
      $zip -> open($zipfile, \ZipArchive::CREATE);
      $zip->addFile(storage_path().'/README', "README");
      foreach($files as $f){
        $zip->addFile($filedir.$f, $f);
      }
      $zip -> close();
      return response() -> download($zipfile);
    }

	public function g2fFileDown(Request $request){
      $file = $request -> input('file');
      $id = $request -> input('id');
      $filedir = config('app.jobdir').'/gwas/'.$id.'/g2f/'.$file;
      return response() -> download($filedir);
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
	  if($prefix=="gwas"){
      	  $fileName .= "_FUMAgwas".$id;
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

	public function annotPlot(Request $request){
      $id = $request->input('id');
	  $prefix = $request->input('prefix');
      $filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';
      $type = $request -> input('annotPlotSelect');
      $rowI = $request -> input('annotPlotRow');

      $GWAS=0;
      $CADD=0;
      $RDB=0;
      $Chr15=0;
      $eqtl=0;
	  $ci=0;
	  $Chr15cells="NA";
      if($request -> has('annotPlot_GWASp')){$GWAS=1;}
      if($request -> has('annotPlot_CADD')){$CADD=1;}
      if($request -> has('annotPlot_RDB')){$RDB=1;}
      if($request -> has('annotPlot_Chrom15')){
        $Chr15=1;
        $temp = $request -> input('annotPlotChr15Ts');
        $Chr15cells = [];
        foreach($temp as $ts){
          if($ts != "null"){
            $Chr15cells[] = $ts;
          }
        }
        $Chr15cells = implode(":", $Chr15cells);
      }
      if($request -> has('annotPlot_eqtl')){$eqtl=1;}
	  if($request -> has('annotPlot_ci')){$ci=1;}

      return view('pages.annotPlot', ['id'=>$id, 'prefix'=>$prefix, 'type'=>$type, 'rowI'=>$rowI,
	  	'GWASplot'=>$GWAS, 'CADDplot'=>$CADD, 'RDBplot'=>$RDB, 'eqtlplot'=>$eqtl,
		'ciplot'=>$ci, 'Chr15'=>$Chr15, 'Chr15cells'=>$Chr15cells]);
    }

	public function annotPlotGetData(Request $request){
		$id = $request->input("id");
		$prefix = $request->input("prefix");
		$type = $request->input("type");
		$rowI = $request->input("rowI");
		$GWASplot = $request->input("GWASplot");
		$CADDplot = $request->input("CADDplot");
		$RDBplot = $request->input("RDBplot");
		$eqtlplot = $request->input("eqtlplot");
		$ciplot = $request->input("ciplot");
		$Chr15 = $request->input("Chr15");
		$Chr15cells = $request->input("Chr15cells");

		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		$script = storage_path()."/scripts/annotPlot.py";
	    $data = shell_exec("python $script $filedir $type $rowI $GWASplot $CADDplot $RDBplot $eqtlplot $ciplot $Chr15 $Chr15cells");
		return $data;
	}

	public function annotPlotGetGenes(Request $request){
		$id = $request->input("id");
		$prefix = $request->input("prefix");
		$chrom = $request->input("chrom");
		$eqtlplot = $request->input("eqtlplot");
		$ciplot = $request->input("ciplot");
		$xMin = $request->input("xMin");
		$xMax = $request->input("xMax");
		$eqtlgenes = $request->input("eqtlgenes");

		$filedir = config('app.jobdir').'/'.$prefix.'/'.$id.'/';

		$script = storage_path()."/scripts/annotPlot.R";
		$data = shell_exec("Rscript $script $filedir $chrom $xMin $xMax $eqtlgenes $eqtlplot $ciplot");
		$data = explode("\n", $data);
		$data = $data[count($data)-1];
		return $data;
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

	public function DEGPlot($type, $jobID){
      $filedir = config('app.jobdir').'/gwas/'.$jobID.'/g2f/';
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
      $filedir = config('app.jobdir').'/gwas/'.$jobID.'/g2f/';
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

}
