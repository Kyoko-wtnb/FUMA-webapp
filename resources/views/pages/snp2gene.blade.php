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
      <!-- <div id="jobinfoSide">
        <li><a href="#jobInfo">Job Info<i class="sub_icon fa fa-info-circle"></i></a></li>
      </div> -->
      <div id="resultsSide">
        <li><a href="#genomePlots">Genome-wide plots<i class="sub_icon fa fa-bar-chart"></i></a></li>
        <li><a href="#summaryTable">Summary of results<i class="sub_icon fa fa-bar-chart"></i></a></li>
        <li><a href="#tables">Results<i class="sub_icon fa fa-table"></i></a></li>
        <li><a href="#downloads">Downloads<i class="sub_icon fa fa-download"></i></a></li>
      </div>
    </ul>
  </div>

  <canvas id="canvas" style="display:none;"></canvas>

  <div id="page-content-wrapper">
    <div class="page-content inset">
        @include('snp2gene.joblist')
        @include('snp2gene.newjob')

      <!-- genome wide plots -->
      <div class="sidePanel container" style="padding-top:50px;" id="genomePlots">
        <!-- <h3>Genome Wide Plot</h3> -->
        <!-- <div id="gPlotPanel" class="collapse in"> -->
        <div class="container">
          <h4 style="color: #00004d">Manhattan Plot (GWAS summary statistics)</h4>
          <span class="info"><i class="fa fa-info"></i>
            This is a manhattan plot of your input GWAS summary statistics.<br/>
            For plotting purposes, overlapping data points are not drawn (see tutorial for detail of filtering, filtering was performed only for SNPs with P-value &le; 1e-5).
          </span><br/><br/>
          Download the plot as
          <button class="btn btn-xs ImgDown" onclick='ImgDown("manhattan","png");'>PNG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("manhattan","jpeg");'>JPG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("manhattan","svg");'>SVG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("manhattan","pdf");'>PDF</button>

          <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dir" id="manhattanDir" val=""/>
            <input type="hidden" name="id" id="manhattanJobID" val=""/>
            <input type="hidden" name="data" id="manhattanData" val=""/>
            <input type="hidden" name="type" id="manhattanType" val=""/>
            <input type="hidden" name="fileName" id="manhattanFileName" val=""/>
            <input type="submit" id="manhattanSubmit" class="ImgDownSubmit"/>
          </form>
          <div id="manhattanPane">
            <div id="manhattan"></div>
          </div>
          <br/><br/>
          <h4 style="color: #00004d">Mahattan Plot (gene-based test)</h4>
          <span class="info"><i class="fa fa-info"></i>
            This is a manhattan plot of the gene-based test as computed by MAGMA based on your input GWAS summary statistics.<br/>
            The gene-based P-value is downloadable from 'Download' tab from the left side bar.
          </span><br/><br/>
          <span id="geneManhattanDesc"></span><br/><br/>
          Download the plot as
          <button class="btn btn-xs ImgDown" onclick='ImgDown("geneManhattan","png");'>PNG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("geneManhattan","jpeg");'>JPG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("geneManhattan","svg");'>SVG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("geneManhattan","pdf");'>PDF</button>

          <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dir" id="geneManhattanDir" val=""/>
            <input type="hidden" name="id" id="geneManhattanJobID" val=""/>
            <input type="hidden" name="data" id="geneManhattanData" val=""/>
            <input type="hidden" name="type" id="geneManhattanType" val=""/>
            <input type="hidden" name="fileName" id="geneManhattanFileName" val=""/>
            <input type="submit" id="geneManhattanSubmit" class="ImgDownSubmit"/>
          </form>
          <br/>
          <span class="form-inline">
            Label top <input class="form-control" type="number" id="topGenes" style="width: 80px;"> genes.<br/>
          </span>
          <div id="geneManhattanPane">
            <div id="geneManhattan"></div>
          </div>
          <br/><br/>
          <div id="QQplotPane">
            <!-- <div class="row"> -->
              <div class="col-md-6 col-xs-6 col-sm-6">
                <h4 style="color: #00004d">QQ plots (GWAS summary statisics)</h4>
                <span class="info"><i class="fa fa-info"></i>
                  This is a Q-Q plot of GWAS summary statistics. <br/>
                  For plotting purposes, overlapping data points are not drawn (see tutorial for detail of filtering, filtering was performed only for SNPs with P-value &le; 1e-5).
                </span><br/><br/>
                Download the plot as
                <button class="btn btn-xs ImgDown" onclick='ImgDown("QQplot","png");'>PNG</button>
                <button class="btn btn-xs ImgDown" onclick='ImgDown("QQplot","jpeg");'>JPG</button>
                <button class="btn btn-xs ImgDown" onclick='ImgDown("QQplot","svg");'>SVG</button>
                <button class="btn btn-xs ImgDown" onclick='ImgDown("QQplot","pdf");'>PDF</button>

                <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="dir" id="QQplotDir" val=""/>
                  <input type="hidden" name="id" id="QQplotJobID" val=""/>
                  <input type="hidden" name="data" id="QQplotData" val=""/>
                  <input type="hidden" name="type" id="QQplotType" val=""/>
                  <input type="hidden" name="fileName" id="QQplotFileName" val=""/>
                  <input type="submit" id="QQplotSubmit" class="ImgDownSubmit"/>
                </form>
                <div>
                  <div id="QQplot"></div>
                </div>
              </div>
              <div class="col-md-6 col-xs-6 col-sm-6">
                <h4 style="color: #00004d">QQ plots (gene-based test)</h4>
                <span class="info"><i class="fa fa-info"></i>
                  This is a Q-Q plot of the gene-based test computed by MAGMA.<br/>
                </span><br/><br/>
                Download the plot as
                <button class="btn btn-xs ImgDown" onclick='ImgDown("geneQQplot","png");'>PNG</button>
                <button class="btn btn-xs ImgDown" onclick='ImgDown("geneQQplot","jpeg");'>JPG</button>
                <button class="btn btn-xs ImgDown" onclick='ImgDown("geneQQplot","svg");'>SVG</button>
                <button class="btn btn-xs ImgDown" onclick='ImgDown("geneQQplot","pdf");'>PDF</button>

                <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="dir" id="geneQQplotDir" val=""/>
                  <input type="hidden" name="id" id="geneQQplotJobID" val=""/>
                  <input type="hidden" name="data" id="geneQQplotData" val=""/>
                  <input type="hidden" name="type" id="geneQQplotType" val=""/>
                  <input type="hidden" name="fileName" id="geneQQplotFileName" val=""/>
                  <input type="submit" id="geneQQplotSubmit" class="ImgDownSubmit"/>
                </form>
                <div>
                  <div id="geneQQplot"></div>
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
            <h4><span style="color: #00004d">Functional consequences of SNPs on genes</span>
              <a class="infoPop" data-toggle="popover" data-content="The histogram displays the number of SNPs (all SNPs in LD of lead SNPs) which have corresponding functional annotaiton assigned by ANNOVAR.
                SNPs which have more than one dirrent annotations are counted for each annotation.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </h4>
            Download the plot as
            <button class="btn btn-xs ImgDown" onclick='ImgDown("snpAnnotPlot","png");'>PNG</button>
            <button class="btn btn-xs ImgDown" onclick='ImgDown("snpAnnotPlot","jpeg");'>JPG</button>
            <button class="btn btn-xs ImgDown" onclick='ImgDown("snpAnnotPlot","svg");'>SVG</button>
            <button class="btn btn-xs ImgDown" onclick='ImgDown("snpAnnotPlot","pdf");'>PDF</button>

            <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="dir" id="snpAnnotPlotDir" val=""/>
              <input type="hidden" name="id" id="snpAnnotPlotJobID" val=""/>
              <input type="hidden" name="data" id="snpAnnotPlotData" val=""/>
              <input type="hidden" name="type" id="snpAnnotPlotType" val=""/>
              <input type="hidden" name="fileName" id="snpAnnotPlotFileName" val=""/>
              <input type="submit" id="snpAnnotPlotSubmit" class="ImgDownSubmit"/>
            </form>
            <div id="snpAnnotPlot"></div>
            <!-- <canvas id="snpAnnotPlotCanvas" style="display: none;"></canvas> -->
          </div>
        </div>
        <br/>
        <div style="text-align:center;">
          <h4><span style="color: #00004d">Summary per genomic risk loci</span>
            <a class="infoPop" data-toggle="popover" data-content="The histgrams dispaly summary results per genomic loci. Note that genomic loci could contain more than one independent lead SNPs.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </h4>
          Download the plot as
          <button class="btn btn-xs ImgDown" onclick='ImgDown("intervalPlot","png");'>PNG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("intervalPlot","jpeg");'>JPG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("intervalPlot","svg");'>SVG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("intervalPlot","pdf");'>PDF</button>

          <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dir" id="intervalPlotDir" val=""/>
            <input type="hidden" name="id" id="intervalPlotJobID" val=""/>
            <input type="hidden" name="data" id="intervalPlotData" val=""/>
            <input type="hidden" name="type" id="intervalPlotType" val=""/>
            <input type="hidden" name="fileName" id="intervalPlotFileName" val=""/>
            <input type="submit" id="intervalPlotSubmit" class="ImgDownSubmit"/>
          </form>
          <div id="intervalPlot"></div>
        </div>
        <br/><br/>
      </div>

      <!-- result tables -->
      <div class="sidePanel container" style="padding-top:50px;" id="tables">
        <div class="panel panel-default"><div class="panel-body">
          <!-- <a href="#tablesPanel" data-toggle="collapse" style="color: #00004d"><h3>Result tables</h3></a> -->
          <h4 style="color: #00004d">Result tables</h4>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <!-- <li role="presentation" class="active"><a href="#summaryTable" aria-controls="summaryTable" rolw="tab" data-toggle="tab">Summary</a></li> -->
              <li role="presentation" class="active"><a href="#leadSNPtablePane" aria-controls="leadSNPtablePane" rolw="tab" data-toggle="tab">lead SNPs</a></li>
              <li role="presentation"><a href="#intervalTablePane" aria-controls="intervalTablePane" rolw="tab" data-toggle="tab">Genomic risk loci</a></li>
              <li role="presentation"><a href="#SNPtablePane" aria-controls="SNPtablePane" rolw="tab" data-toggle="tab">SNPs (annotations)</a></li>
              <li role="presentation"><a href="#annovTablePane" aria-controls="annovTablePane" rolw="tab" data-toggle="tab">ANNOVAR</a></li>
              <li role="presentation"><a href="#geneTablePane" aria-controls="geneTablePane" rolw="tab" data-toggle="tab">Mapped Genes</a></li>
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
                <i class="fa fa-info"></i> Click row to display a regional plot of GWAS summary statistics.
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
                <i class="fa fa-info"></i> Click row to display a regional plot of GWAS summary statistics.
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
              <span class="info"><i class="fa fa-info"></i> This table contain all SNPs in LD of identified lead SNPs even if functional filtering is performed for gene mapping.</span>
              <br/>
              <table id="SNPtable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="annovTablePane">
              <br/>
              <span class="info"><i class="fa fa-info"></i> This is result of annotation by ANNOVAR. SNPs can be appear multiple times in this table if they are annotated to more than one genes.</span>
              <br/>
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
              <span class="info"><i class="fa fa-info"></i>
                This table contains prioritized genes based on user defined mapping criteria. Note that these genes do no necessary contain all genes which are locating within genomic loci (depending on mapping paramters).
              </span>
              <!-- Jump to GENE2FUNC -->
              <form action="{{ Config::get('app.subdir') }}/gene2func/geneSubmit" method="post" target="_blank">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="jobID" value="<?php echo $jobID;?>"/>
                <span class="form-inline">
                  <input type="submit" class="btn" id="geneQuerySubmit" name="geneQuerySubmit" value="Use mapped genes for GENE2FUNC (open new tab)">
                  <a class="infoPop" data-toggle="popover" data-content="This is linked to GENE2FUNC process. All genes in the table below will be used. You can manually submit selected genes later on. This will open new tab.">
                    <i class="fa fa-question-circle-o fa-lg"></i>
                  </a>
                </span>
              </form>
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
              <p class="info"><i class="fa fa-info"></i>
                This table only shows subset of information from GWAS catalog. <br/>
                Please download a output file (gwascatalog.txt) from "Download" tab to get full information
              </p>
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
            <span class="info"><i class="fa fa-info"></i>
              Please click one of the row of lead SNPs or Genomic risk loci tables to display regional plot.<br/>
              This plot only displays SNPs that are in LD with one of the lead SNPs.
              You can zoom in/out by mouse scroll. <br/>
              Lead SNPs are colored in purple.
              When independe lead SNPs are selected instead of genomic loci, SNPs which are in the same loci but not in LD with the selected lead SNPs are colored in grey.<br/>
              SNPs that are in LD with a lead SNP but do not have a P-value because they were not available in the summary statistics, are displayed at the top of the plot (1000G SNPs).
            </span>
            <div class="row">
              <div class="col-md-9">
                <div id="locusPlot" style="text-align: center;">
                  <a id="plotClear" style="position: absolute;right: 30px;">Clear</a>
                </div>
              </div>
              <div class="col-md-3">
                <div id="selectedLeadSNP"></div>
              </div>
            </div>
            <!-- </div> -->
          <!-- </div></div>
        </div> -->

        <!-- Annot plot options -->
        <!-- <div class="panel panel-default"><div class="panel-body"> -->
          <div id="annotPlotPanel">
            <h4><span style="color: #00004d">Regional plot with annotation</span>
              <a class="infoPop" data-toggle="popover" data-content="To create regional plot with genes and annotations, select the following options and click 'Plot'.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </h4>
            <div style="margin-left: 40px;">
              <form action="annotPlot" method="post" target="_blank">
                <!-- Select region to plot: <span style="color:red">Mandatory</span><br/> -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="jobID" value="<?php echo $jobID;?>"/>
                <input type="hidden" name="annotPlotSelect" id="annotPlotSelect" value="null"/>
                <input type="hidden" name="annotPlotRow" id="annotPlotRow" value="null"/>
                Select annotation(s) to plot:<br/>
                <tab><input type="checkbox" name="annotPlot_GWASp" id="annotPlot_GWASp" checked/>GWAS association statistics<br/>
                <tab><input type="checkbox" name="annotPlot_CADD" id="annotPlot_CADD" checked/>CADD score<br/>
                <tab><input type="checkbox" name="annotPlot_RDB" id="annotPlot_RDB" checked/>RegulomeDB score<br/>
                <tab><input type="checkbox" name="annotPlot_Chrom15" id="annotPlot_Chrom15" onchange="Chr15Select();"/>Chromatine 15 state
                  <div id="annotPlotChr15Opt">
                  <tab><tab><span style="color:red;">Please select at least one tissue type.</span><br/>
                  <tab><tab>Individual tissue/cell types: <a id="annotPlotChr15TsClear">clear</a><br/>
                  <tab><tab><select multiple size="5" id="annotPlotChr15Ts" name="annotPlotChr15Ts[]" onchange="Chr15Select()">
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
                  <tab><tab><select multiple size="5" id="annotPlotChr15Gts" name="annotPlotChr15Gts[]" onchange="Chr15Select()">
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
                <br/>
                <span class="form-inline">
                  <input class="btn" type="submit" name="submit" id= "annotPlotSubmit" value="Plot">
                  <span id="CheckAnnotPlotOpt"></span>
                </span>
              </form>
            </div>
          </div>
        </div></div>
      </div>
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
          <input type="checkbox" name="magmafile" id="magmafile" checked onchange="DownloadFiles();">MAGMA results<br/>
          <a id="allfiles"> Select All </a><tab><a id="clearfiles"> Clear</a><br/>
          <br/>
          <input class="btn" type="submit" name="download" id="download" value="Download files"/>
        </form>
      </div>


    </div>
  </div>
</div>


@stop
