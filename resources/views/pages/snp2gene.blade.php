@extends('layouts.master')
@section('head')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.0/js/bootstrap-select.min.js"></script>
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="//d3js.org/d3.v3.min.js"></script>
<script src="//labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
<script type="text/javascript" src="//canvg.github.io/canvg/rgbcolor.js"></script>
<script type="text/javascript" src="//canvg.github.io/canvg/StackBlur.js"></script>
<script type="text/javascript" src="//canvg.github.io/canvg/canvg.js"></script>
<script type="text/javascript" src="{!! URL::asset('js/canvas2image.js') !!}"></script>

<meta name="csrf-token" content="{{ csrf_token() }}"/>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
  });
  var status = "{{$status}}";
  var id = "{{$jobID}}";
  var jobid = id;
  var subdir = "{{ Config::get('app.subdir') }}"
  var preurl = "{{ URL::to('snp2gene') }}"
  // console.log(jobID);
</script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/InputParameters.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/snp2geneResults.js') !!}"></script>
@stop
@section('content')
<div id="wrapper" class="active">
  <div id="sidebar-wrapper">
    <ul class="sidebar-nav" id="sidebar-menu">
      <li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
    </ul>
    <ul class="sidebar-nav" id="sidebar">
      <li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
      <li><a href="#joblist-panel">My Jobs<i class="sub_icon fa fa-search"></i></a></li>
      <div id="jobinfoSide">
        <li><a href="#jobInfo">Job Info<i class="sub_icon fa fa-info-circle"></i></a></li>
      </div>
      <div id="resultsSide">
        <li><a href="#genomePlots">Genome-wide plots<i class="sub_icon fa fa-bar-chart"></i></a></li>
        <li><a href="#summaryTable">Summary of results<i class="sub_icon fa fa-bar-chart"></i></a></li>
        <li><a href="#tables">Results<i class="sub_icon fa fa-table"></i></a></li>
        <li><a href="#downloads">Downloads<i class="sub_icon fa fa-download"></i></a></li>
        <!-- <li><a href="#annotPlot">Regional plot<i class="sub_icon fa fa-bar-chart"></i></a></li> -->
      </div>
    </ul>
  </div>

  <canvas id="canvas" style="display:none;"></canvas>

  <div id="page-content-wrapper">
    <div class="page-content inset">
        @include('snp2gene.joblist')
        @include('snp2gene.newjob')

      <!-- results panel -->
      <!-- job info table -->
      <div class="sidePanel container" style="padding-top:50px; height:100vh;" id="jobInfo">
        <h3 style="color: #00004d">Information of your job</h3>
        <div id="jobInfoTable"></div>
        <div id="ErrorMess"></div>
      </div>

      <!-- genome wide plots -->
      <div class="sidePanel" style="padding-top:50px;" id="genomePlots">
        <!-- <h3>Genome Wide Plot</h3> -->
        <!-- <div id="gPlotPanel" class="collapse in"> -->
        <div class="container">
          <h4 style="color: #00004d">Manhattan Plot (GWAS summary statistics)</h4>
          <span class="info"><i class="fa fa-info"></i>
            This is manhattan plot of input GWAS summary statistics with filtering of overlapped data points.
          </span><br/>
          <button class="btn btn-xs CanvDown" id="manhattanCanvasDown" style="float:right; margin-right:200px;">Download PNG</button>
          <div id="manhattanPane">
            <canvas id="manhattanCanvas"></canvas>
            <canvas id="manhattanTmp" style="display: none;"></canvas>
            <div id="manhattan" style="display: none;"></div>
          </div>
          <br/><br/>
          <h4 style="color: #00004d">Mahattan Plot (gene-based test)</h4>
          <span class="info"><i class="fa fa-info"></i>
            This is manhattan plot of gene-based test computed by MAGMA.
          </span><br/>
          <button class="btn btn-xs CanvDown" id="geneManhattanCanvasDown" style="float:right; margin-right:200px;">Download PNG</button>
          <div id="geneManhattanPane">
            <canvas id="geneManhattanCanvas"></canvas>
            <canvas id="geneManhattanTmp" style="display: none;"></canvas>
            <div id="geneManhattan" style="display:none;"></div>
          </div>
          <br/><br/>
          <div id="QQplotPane">
            <!-- <div class="row"> -->
              <div class="col-md-6">
                <h4 style="color: #00004d">QQ plots (GWAS summary statisics)</h4>
                <button class="btn btn-xs CanvDown" id="QQplotCanvasDown" style="float:right; margin-right:100px;">Download PNG</button><br/>
                <div>
                  <canvas id="QQplotCanvas"></canvas>
                  <canvas id="QQplotTmp" style="display: none;"></canvas>
                  <div id="QQplot" style="display:none;"></div>
                </div>
              </div>
              <div class="col-md-6">
                <h4 style="color: #00004d">QQ plots (gene-based test)</h4>
                <button class="btn btn-xs CanvDown" id="geneQQplotCanvasDown" style="float:right; margin-right:100px;">Download PNG</button><br/>
                <div>
                  <canvas id="geneQQplotCanvas"></canvas>
                  <canvas id="geneQQplotTmp" style="display: none;"></canvas>
                  <div id="geneQQplot" style="display:none;"></div>
                </div>
              </div>
            <!-- </div> -->
          </div>
          <br/><br/>
        </div>
      </div>

      <!-- Summary panel -->
      <div class="sidePanel container" style="padding-top:50px;" id="summaryTable">
        <div class="row">
          <div class="col-md-5" id="sumTable" style="text-align:center;">
            <h4 style="color: #00004d">Summary of SNPs and mapped genes</h4>
          </div>

          <div class="col-md-7" style="text-align:center;">
            <h4 style="color: #00004d">Positional annotations of candidate SNPs</h4>
            <button class="btn btn-xs ImgDown" id="snpAnnotPlotImg" style="float:right; margin-right:100px;">Download PNG</button>
            <div id="snpAnnotPlot"></div>
            <canvas id="snpAnnotPlotCanvas" style="display: none;"></canvas>
          </div>
        </div>
        <br/>
        <div style="text-align:center;">
          <h4 style="color: #00004d">Summary per interval</h4>
          <button class="btn btn-xs ImgDown" id="intervalPlotImg" style="margin-left:800px;">Download PNG</button>
          <div id="intervalPlot"></div>
          <canvas id="intervalPlotCanvas" style="display: none;"></canvas>
        </div>
        <br/><br/>
      </div>

      <!-- result tables -->
      <div class="sidePanel container" style="padding-top:50px;" id="tables">
        <div class="panel panel-default"><div class="panel-body">
          <!-- <a href="#tablesPanel" data-toggle="collapse" style="color: #00004d"><h3>Result tables</h3></a> -->
          <h4 style="color: #00004d">Result tables</h4>
            <!-- <div id="tablesPanel" class="collapse in"> -->
            <form action="geneSubmit" method="post" target="_blank">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="jobID" value="<?php echo $jobID;?>"/>
              <input type="submit" class="btn" id="geneQuerySubmit" name="geneQuerySubmit" value="Use results for GENE2FUNC (open new tab)">
            </form>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <!-- <li role="presentation" class="active"><a href="#summaryTable" aria-controls="summaryTable" rolw="tab" data-toggle="tab">Summary</a></li> -->
              <li role="presentation" class="active"><a href="#leadSNPtablePane" aria-controls="leadSNPtablePane" rolw="tab" data-toggle="tab">lead SNPs</a></li>
              <li role="presentation"><a href="#intervalTablePane" aria-controls="intervalTablePane" rolw="tab" data-toggle="tab">Intervals</a></li>
              <li role="presentation"><a href="#SNPtablePane" aria-controls="SNPtablePane" rolw="tab" data-toggle="tab">SNPs (annotations)</a></li>
              <li role="presentation"><a href="#annovTablePane" aria-controls="annovTablePane" rolw="tab" data-toggle="tab">ANNOVAR</a></li>
              <li role="presentation"><a href="#geneTablePane" aria-controls="geneTablePane" rolw="tab" data-toggle="tab">Genes</a></li>
              <li role="presentation" id="eqtlTableTab"><a href="#eqtlTablePane" aria-controls="eqtlTablePane" rolw="tab" data-toggle="tab">eQTL</a></li>
              <li role="presentation" id="gwascatTableTab"><a href="#gwascatTablePane" aria-controls="gwascatTablePane" rolw="tab" data-toggle="tab">GWAScatalog</a></li>
              <!-- <li role="presentation"><a href="#exacTablePane" aria-controls="exacTablePane" rolw="tab" data-toggle="tab">ExAC</a></li> -->
              <li role="presentation"><a href="#paramsPane" aria-controls="paramsPane" rolw="tab" data-toggle="tab">Parameters</a></li>
              <!-- <li role="presentation"><a href="#downloads" aria-controls="downloads" rolw="tab" data-toggle="tab">Downloads</a></li> -->
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="leadSNPtablePane">
              <br/>
              <p class="info">
                <tab><i class="fa fa-info"></i> Click row to display a regional plot of GWAS summary statistics.
              </p>
              <table id="leadSNPtable" class="display compact" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>No</th><th>Interval</th><th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>P-value</th><th>nSNPs</th><th>nGWASSNPs</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="intervalTablePane">
              <br/>
              <p class="info">
                <tab><i class="fa fa-info"></i> Click row to display a regional plot of GWAS summary statistics.
              </p>
              <table id="intervalTable" class="display compact dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>Interval</th><th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>P-value</th><th>nLeadSNPs</th><th>start</th><th>end</th><th>nSNPs</th><th>nGWASSNPs</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="SNPtablePane">
              <br/>
              <table id="SNPtable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>uniqID</th><th>rsID</th><th>chr</th><th>bp</th><th>MAF</th><th>P-value</th><th>Interval</th><th>r2</th><th>leadSNP</th><th>Nearest gene</th>
                    <th>dist</th><th>position</th><th>CADD</th><th>RDB</th><th>minChrState(127)</th><th>commonChrState(127)</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="annovTablePane">
              <table id="annovTable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>uniqID</th><th>chr</th><th>bp</th><th>Gene</th><th>Symbol</th><th>Distance</th><th>Function</th><th>Exonic function</th><th>Exon</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="geneTablePane">
              <br/>
              <table id="geneTable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="eqtlTablePane">
              <br/>
              <table id="eqtlTable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>uniqID</th><th>chr</th><th>bp</th><th>DB</th><th>tissue</th><th>Gene</th><th>Symbol</th><th>P-value</th><th>FDR</th><th>t/z</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="gwascatTablePane">
              <br/>
              <p>Please download a output file (gwascatalog.txt) from "Download" tab to get full information</p>
              <table id="gwascatTable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>Interval</th><th>lead SNP</th><th>chr</th><th>bp</th><th>rsID</th><th>PMID</th><th>Trait</th><th>FirstAuth</th><th>Date</th><th>P-value</th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- <div role="tabpanel" class="tab-pane" id="exacTablePane">
              <br/>
              <table id="exacTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>Interval</th><th>uniqID</th><th>chr</th><th>bp</th><th>ref</th><th>alt</th><th>Annotation</th><th>Gene</th><th>MAF</th>
                    <th>MAF(FIN)</th><th>MAF(NFE)</th><th>MAF(AMR)</th><th>MAF(AFR)</th><th>MAF(EAS)</th><th>MAF(SAS)</th><th>MAF(OTH)<th>
                  </tr>
                </thead>
              </table>
            </div> -->
            <div role="tabpanel" class="tab-pane" id="paramsPane">
              <br/>
              <div id="paramTable"></div>
            </div>

          </div>
          <!-- </div> -->
        </div></div>

        <!-- region plot -->
        <div id="regionalPlot">
          <div class="panel panel-default"><div class="panel-body">
            <!-- <a href="#regionalPlotPanel" data-toggle="collapse" style="color: #00004d"><h3>Regional Plot (GWAS association)</h3></a> -->
            <h4 style="color: #00004d">Regional Plot (GWAS association)</h4>
            <!-- <div class="row collapse in" id="regionalPlotPanel"> -->
              <div class="col-md-9">
                <div id="locusPlot" style="text-align: center;">
                  <a id="plotClear" style="position: absolute;right: 30px;">Clear</a>
                </div>
              </div>
              <div class="col-md-3">
                <div id="selectedLeadSNP"></div>
              </div>
            <!-- </div> -->
          </div></div>
        </div>

        <!-- Annot plot options -->
        <div class="panel panel-default"><div class="panel-body">
          <h4 style="color: #00004d">Regional plot with annotation</h4>
          <div id="annotPlotPanel">
            <!-- {!! Form::open(array('url' => 'snp2gene/annotPlot')) !!} -->
            <!-- {!! Form::token() !!} -->
            <form action="annotPlot" method="post" target="_blank">
            Select region to plot: <span style="color:red">Mandatory</span><br/>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="jobID" value="<?php echo $jobID;?>"/>
            <tab><input type="checkbox" name="annotPlotSelect_leadSNP" id="annotPlotSelect_leadSNP" value="null" onchange="annotSelect('leadSNP')"/>LD block of selected lead SNP<br/>
            <tab><input type="checkbox" name="annotPlotSelect_interval" id="annotPlotSelect_interval" value="null" onchange="annotSelect('interval')"/>Selected interval<br/>
            <!-- <tab><input type="checkbox" name="annotPlotSelect_SNP" id="annotPlotSelect_SNP" value="null" onchange="annotSelect('SNP')"/>LD block which selected SNP belongs<br/> -->
            <!-- <tab><input type="checkbox" id="annotPlotSelect" value="leadSNP"/>Create regional plot for all intervals<br/> -->
            Select annotation(s) to plot:<br/>
            <tab><input type="checkbox" name="annotPlot_GWASp" id="annotPlot_GWASp" checked/>GWAS association statistics<br/>
            <tab><input type="checkbox" name="annotPlot_CADD" id="annotPlot_CADD" checked/>CADD score<br/>
            <tab><input type="checkbox" name="annotPlot_RDB" id="annotPlot_RDB" checked/>RegulomeDB score<br/>
            <tab><input type="checkbox" name="annotPlot_Chrom15" id="annotPlot_Chrom15" onchange="annotSelect();"/>Chromatine 15 state
              <div id="annotPlotChr15Opt">
              <tab><tab><span style="color:red;">Please select at least one tissue type.</span><br/>
              <tab><tab>Individual tissue/cell types: <a id="annotPlotChr15TsClear">clear</a><br/>
              <tab><tab><select multiple size="5" id="annotPlotChr15Ts" name="annotPlotChr15Ts[]" onchange="annotSelect()">
                <option value="all">All</option>
                <option value='E001'>E001 (ESC) ES-I3 Cells</option>
                <option value='E002'>E002 (ESC) ES-WA7 Cells</option>
                <option value='E003'>E003 (ESC) H1 Cells</option>
                <option value='E004'>E004 (ESC Derived) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
                <option value='E005'>E005 (ESC Derived) H1 BMP4 Derived Trophoblast Cultured Cells</option>
                <option value='E006'>E006 (ESC Derived) H1 Derived Mesenchymal Stem Cells</option>
                <option value='E007'>E007 (ESC Derived) H1 Derived Neuronal Progenitor Cultured Cells</option>
                <option value='E008'>E008 (ESC) H9 Cells</option>
                <option value='E009'>E009 (ESC Derived) H9 Derived Neuronal Progenitor Cultured Cells</option>
                <option value='E010'>E010 (ESC Derived) H9 Derived Neuron Cultured Cells</option>
                <option value='E011'>E011 (ESC Derived) hESC Derived CD184+ Endoderm Cultured Cells</option>
                <option value='E012'>E012 (ESC Derived) hESC Derived CD56+ Ectoderm Cultured Cells</option>
                <option value='E013'>E013 (ESC Derived) hESC Derived CD56+ Mesoderm Cultured Cells</option>
                <option value='E014'>E014 (ESC) HUES48 Cells</option>
                <option value='E015'>E015 (ESC) HUES6 Cells</option>
                <option value='E016'>E016 (ESC) HUES64 Cells</option>
                <option value='E017'>E017 (Lung) IMR90 fetal lung fibroblasts Cell Line</option>
                <option value='E018'>E018 (iPSC) iPS-15b Cells</option>
                <option value='E019'>E019 (iPSC) iPS-18 Cells</option>
                <option value='E020'>E020 (iPSC) iPS-20b Cells</option>
                <option value='E021'>E021 (iPSC) iPS DF 6.9 Cells</option>
                <option value='E022'>E022 (iPSC) iPS DF 19.11 Cells</option>
                <option value='E023'>E023 (Fat) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
                <option value='E024'>E024 (ESC) ES-UCSF4  Cells</option>
                <option value='E025'>E025 (Fat) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
                <option value='E026'>E026 (Stromal Connective) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
                <option value='E027'>E027 (Breast) Breast Myoepithelial Primary Cells</option>
                <option value='E028'>E028 (Breast) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
                <option value='E029'>E029 (Blood) Primary monocytes from peripheral blood</option>
                <option value='E030'>E030 (Blood) Primary neutrophils from peripheral blood</option>
                <option value='E031'>E031 (Blood) Primary B cells from cord blood</option>
                <option value='E032'>E032 (Blood) Primary B cells from peripheral blood</option>
                <option value='E033'>E033 (Blood) Primary T cells from cord blood</option>
                <option value='E034'>E034 (Blood) Primary T cells from peripheral blood</option>
                <option value='E035'>E035 (Blood) Primary hematopoietic stem cells</option>
                <option value='E036'>E036 (Blood) Primary hematopoietic stem cells short term culture</option>
                <option value='E037'>E037 (Blood) Primary T helper memory cells from peripheral blood 2</option>
                <option value='E038'>E038 (Blood) Primary T helper naive cells from peripheral blood</option>
                <option value='E039'>E039 (Blood) Primary T helper naive cells from peripheral blood</option>
                <option value='E040'>E040 (Blood) Primary T helper memory cells from peripheral blood 1</option>
                <option value='E041'>E041 (Blood) Primary T helper cells PMA-I stimulated</option>
                <option value='E042'>E042 (Blood) Primary T helper 17 cells PMA-I stimulated</option>
                <option value='E043'>E043 (Blood) Primary T helper cells from peripheral blood</option>
                <option value='E044'>E044 (Blood) Primary T regulatory cells from peripheral blood</option>
                <option value='E045'>E045 (Blood) Primary T cells effector/memory enriched from peripheral blood</option>
                <option value='E046'>E046 (Blood) Primary Natural Killer cells from peripheral blood</option>
                <option value='E047'>E047 (Blood) Primary T CD8+ naive cells from peripheral blood</option>
                <option value='E048'>E048 (Blood) Primary T CD8+ memory cells from peripheral blood</option>
                <option value='E049'>E049 (Stromal Connective) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
                <option value='E050'>E050 (Blood) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
                <option value='E051'>E051 (Blood) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
                <option value='E052'>E052 (Muscle) Muscle Satellite Cultured Cells</option>
                <option value='E053'>E053 (Brain) Cortex derived primary cultured neurospheres</option>
                <option value='E054'>E054 (Brain) Ganglion Eminence derived primary cultured neurospheres</option>
                <option value='E055'>E055 (Skin) Foreskin Fibroblast Primary Cells skin01</option>
                <option value='E056'>E056 (Skin) Foreskin Fibroblast Primary Cells skin02</option>
                <option value='E057'>E057 (Skin) Foreskin Keratinocyte Primary Cells skin02</option>
                <option value='E058'>E058 (Skin) Foreskin Keratinocyte Primary Cells skin03</option>
                <option value='E059'>E059 (Skin) Foreskin Melanocyte Primary Cells skin01</option>
                <option value='E061'>E061 (Skin) Foreskin Melanocyte Primary Cells skin03</option>
                <option value='E062'>E062 (Blood) Primary mononuclear cells from peripheral blood</option>
                <option value='E063'>E063 (Fat) Adipose Nuclei</option>
                <option value='E065'>E065 (Vascular) Aorta</option>
                <option value='E066'>E066 (Liver) Liver</option>
                <option value='E067'>E067 (Brain) Brain Angular Gyrus</option>
                <option value='E068'>E068 (Brain) Brain Anterior Caudate</option>
                <option value='E069'>E069 (Brain) Brain Cingulate Gyrus</option>
                <option value='E070'>E070 (Brain) Brain Germinal Matrix</option>
                <option value='E071'>E071 (Brain) Brain Hippocampus Middle</option>
                <option value='E072'>E072 (Brain) Brain Inferior Temporal Lobe</option>
                <option value='E073'>E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
                <option value='E074'>E074 (Brain) Brain Substantia Nigra</option>
                <option value='E075'>E075 (GI Colon) Colonic Mucosa</option>
                <option value='E076'>E076 (GI Colon) Colon Smooth Muscle</option>
                <option value='E077'>E077 (GI Duodenum) Duodenum Mucosa</option>
                <option value='E078'>E078 (GI Duodenum) Duodenum Smooth Muscle</option>
                <option value='E079'>E079 (GI Esophagus) Esophagus</option>
                <option value='E080'>E080 (Adrenal) Fetal Adrenal Gland</option>
                <option value='E081'>E081 (Brain) Fetal Brain Male</option>
                <option value='E082'>E082 (Brain) Fetal Brain Female</option>
                <option value='E083'>E083 (Heart) Fetal Heart</option>
                <option value='E084'>E084 (GI Intestine) Fetal Intestine Large</option>
                <option value='E085'>E085 (GI Intestine) Fetal Intestine Small</option>
                <option value='E086'>E086 (Kidney) Fetal Kidney</option>
                <option value='E087'>E087 (Pancreas) Pancreatic Islets</option>
                <option value='E088'>E088 (Lung) Fetal Lung</option>
                <option value='E089'>E089 (Muscle) Fetal Muscle Trunk</option>
                <option value='E090'>E090 (Muscle) Fetal Muscle Leg</option>
                <option value='E091'>E091 (Placenta) Placenta</option>
                <option value='E092'>E092 (GI Stomach) Fetal Stomach</option>
                <option value='E093'>E093 (Thymus) Fetal Thymus</option>
                <option value='E094'>E094 (GI Stomach) Gastric</option>
                <option value='E095'>E095 (Heart) Left Ventricle</option>
                <option value='E096'>E096 (Lung) Lung</option>
                <option value='E097'>E097 (Ovary) Ovary</option>
                <option value='E098'>E098 (Pancreas) Pancreas</option>
                <option value='E099'>E099 (Placenta) Placenta Amnion</option>
                <option value='E100'>E100 (Muscle) Psoas Muscle</option>
                <option value='E101'>E101 (GI Rectum) Rectal Mucosa Donor 29</option>
                <option value='E102'>E102 (GI Rectum) Rectal Mucosa Donor 31</option>
                <option value='E103'>E103 (GI Rectum) Rectal Smooth Muscle</option>
                <option value='E104'>E104 (Heart) Right Atrium</option>
                <option value='E105'>E105 (Heart) Right Ventricle</option>
                <option value='E106'>E106 (GI Colon) Sigmoid Colon</option>
                <option value='E107'>E107 (Muscle) Skeletal Muscle Male</option>
                <option value='E108'>E108 (Muscle) Skeletal Muscle Female</option>
                <option value='E109'>E109 (GI Intestine) Small Intestine</option>
                <option value='E110'>E110 (GI Stomach) Stomach Mucosa</option>
                <option value='E111'>E111 (GI Stomach) Stomach Smooth Muscle</option>
                <option value='E112'>E112 (Thymus) Thymus</option>
                <option value='E113'>E113 (Spleen) Spleen</option>
                <option value='E114'>E114 (Lung) A549 EtOH 0.02pct Lung Carcinoma Cell Line</option>
                <option value='E115'>E115 (Blood) Dnd41 TCell Leukemia Cell Line</option>
                <option value='E116'>E116 (Blood) GM12878 Lymphoblastoid Cells</option>
                <option value='E117'>E117 (Cervix) HeLa-S3 Cervical Carcinoma Cell Line</option>
                <option value='E118'>E118 (Liver) HepG2 Hepatocellular Carcinoma Cell Line</option>
                <option value='E119'>E119 (Breast) HMEC Mammary Epithelial Primary Cells</option>
                <option value='E120'>E120 (Muscle) HSMM Skeletal Muscle Myoblasts Cells</option>
                <option value='E121'>E121 (Muscle) HSMM cell derived Skeletal Muscle Myotubes Cells</option>
                <option value='E122'>E122 (Vascular) HUVEC Umbilical Vein Endothelial Primary Cells</option>
                <option value='E123'>E123 (Blood) K562 Leukemia Cells</option>
                <option value='E124'>E124 (Blood) Monocytes-CD14+ RO01746 Primary Cells</option>
                <option value='E125'>E125 (Brain) NH-A Astrocytes Primary Cells</option>
                <option value='E126'>E126 (Skin) NHDF-Ad Adult Dermal Fibroblast Primary Cells</option>
                <option value='E127'>E127 (Skin) NHEK-Epidermal Keratinocyte Primary Cells</option>
                <option value='E128'>E128 (Lung) NHLF Lung Fibroblast Primary Cells</option>
                <option value='E129'>E129 (Bone) Osteoblast Primary Cells</option>
              </select><br/>

              <tab><tab>General tissue/cell types: <a id="annotPlotChr15GtsClear">clear</a><br/>
              <tab><tab><select multiple size="5" id="annotPlotChr15Gts" name="annotPlotChr15Gts[]" onchange="annotSelect()">
                <option value="all">All</option>
                <option value='E080'>Adrenal (1)</option>
                <option value='E062:E034:E045:E033:E044:E043:E039:E041:E042:E040:E037:E048:E038:E047:E029:E031:E035:E051:E050:E036:E032:E046:E030:E115:E116:E123:E124'>Blood (27)</option>
                <option value='E129'>Bone (1)</option>
                <option value='E054:E053:E071:E074:E068:E069:E072:E067:E073:E070:E082:E081:E125'>Brain (13)</option>
                <option value='E028:E027:E119'>Breast (3)</option>
                <option value='E117'>Cervix (1)</option>
                <option value='E002:E008:E001:E015:E014:E016:E003:E024'>ESC (8)</option>
                <option value='E007:E009:E010:E013:E012:E011:E004:E005:E006'>ESC Derived (9)</option>
                <option value='E025:E023:E063'>Fat (3)</option>
                <option value='E076:E106:E075'>GI Colon (3)</option>
                <option value='E078:E077'>GI Duodenum (2)</option>
                <option value='E079'>GI Esophagus (1)</option>
                <option value='E085:E084:E109'>GI Intestine (3)</option>
                <option value='E103:E101:E102'>GI Rectum (3)</option>
                <option value='E111:E092:E110:E094'>GI Stomach (4)</option>
                <option value='E083:E104:E095:E105'>Heart (4)</option>
                <option value='E020:E019:E018:E021:E022'>iPSC (5)</option>
                <option value='E086'>Kidney (1)</option>
                <option value='E066:E118'>Liver (2)</option>
                <option value='E017:E088:E096:E114:E128'>Lung (5)</option>
                <option value='E052:E100:E108:E107:E089:E120:E121:E090'>Muscle (8)</option>
                <option value='E097'>Ovary (1)</option>
                <option value='E087:E098'>Pancreas (2)</option>
                <option value='E099:E091'>Placenta (2)</option>
                <option value='E055:E056:E059:E061:E057:E058:E126:E127'>Skin (8)</option>
                <option value='E113'>Spleen (1)</option>
                <option value='E026:E049'>Stromal Connective (2)</option>
                <option value='E112:E093'>Thymus (2)</option>
                <option value='E065:E122'>Vascular (2)</option>
              </select>
              </div>
              <br/>
            <div id="check_eqtl_annotPlot"><tab><input type="checkbox" name="annotPlot_eqtl" id="annotPlot_eqtl" checked/>eQTL<br/></div>
            <div id="CheckAnnotPlotOpt"></div>
            <input class="btn" type="submit" name="submit" id= "annotPlotSubmit" value="Plot"><br/>
            </form>
          </div>
        </div></div>
      </div>

      <!-- Downloads -->
      <div class="sidePanel container" style="padding-top:50px; height: 100vh;" id="downloads">
        <h4 style="color: #00004d">Download files </h4>
        <form action="filedown" method="post" target="_blank">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="jobID" value="<?php echo $jobID;?>"/>
          <!-- <input type="checkbox" name="allfiles" id="allfiles" checked onchange="DownloadFiles();">All files</br> -->
          <input type="checkbox" name="paramfile" id="paramfile" checked onchange="DownloadFiles();">Parameters</br>
          <input type="checkbox" name="leadfile" id="leadfile" checked onchange="DownloadFiles();">lead SNP table (independent lead SNPs) </br>
          <input type="checkbox" name="intervalfile" id="intervalfile" checked onchange="DownloadFiles();">Interval table <br/>
          <input type="checkbox" name="snpsfile" id="snpsfile" checked onchange="DownloadFiles();"> SNP table (Candidate SNPs with chr, bp, P-value, CADD, RDB, nearest gene, interval and lead SNPs)<br/>
          <input type="checkbox" name="annovfile" id="annovfile" checked onchange="DownloadFiles();">ANNOVAR results (uniqID, annotation, gene and distance, SNP-gene pair per line)<br/>
          <input type="checkbox" name="annotfile" id="annotfile" checked onchange="DownloadFiles();">Annotations (CADD, RDB and Chromatin state of 127 tissue/cell types)<br/>
          <input type="checkbox" name="genefile" id="genefile" checked onchange="DownloadFiles();">Gene table (mapped genes)<br/>
          <div id="eqtlfiledown"><input type="checkbox" name="eqtlfile" id="eqtlfile" checked onchange="DownloadFiles();">eQTL table (eQTL of selected tissue types)<br/></div>
          <!-- <input type="checkbox" name="exacfile" id="exacfile" checked onchange="DownloadFiles();">ExAC variants (rare variants from ExAC within intervals)<br/> -->
          <input type="checkbox" name="gwascatfile" id="gwascatfile" checked onchange="DownloadFiles();">GWAScatalog (full recode from GWAScatalog)<br/>
          <a id="allfiles"> Select All </a><tab><a id="clearfiles"> Clear</a><br/>
          <br/>
          <input class="btn" type="submit" name="download" id="download" value="Download files"/>
        </form>
      </div>


    </div>
  </div>
</div>


@stop
