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
<script type="text/javascript" src="//d3js.org/queue.v1.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}"/>
<script type="text/javascript">
$.ajaxSetup({
	headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
});
var status = "{{$status}}";
var id = "{{$jobID}}";
var jobid = id;
var subdir = "{{ Config::get('app.subdir') }}";
var loggedin = "{{ Auth::check() }}";
</script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/NewJobParameters.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/geneMapParameters.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/snp2gene.js') !!}"></script>
@stop
@section('content')
<div id="wrapper" class="active">
	<div id="sidebar-wrapper">
		<ul class="sidebar-nav" id="sidebar-menu">
			<li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
		</ul>
		<ul class="sidebar-nav" id="sidebar">
			<li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
			<li><a href="#geneMap">Redo gene mapping<i class="sub_icon fa fa-repeat"></i></a></li>
			<li><a href="#joblist-panel">My Jobs<i class="sub_icon fa fa-search"></i></a></li>
			<div id="GWplotSide">
				<li><a href="#genomePlots">Genome-wide plots<i class="sub_icon fa fa-bar-chart"></i></a></li>
			</div>
			<div id="Error5Side">
				<li><a href="#error5">ERROR:005<i class="sub_icon fa fa-exclamation-triangle"></i></a></li>
			</div>
			<div id="resultsSide">
				<li><a href="#summaryTable">Summary of results<i class="sub_icon fa fa-bar-chart"></i></a></li>
				<li><a href="#tables">Results<i class="sub_icon fa fa-table"></i></a></li>
				<li><a href="#downloads">Download<i class="sub_icon fa fa-download"></i></a></li>
			</div>
		</ul>
  </div>

	<canvas id="canvas" style="display:none;"></canvas>

	<div id="page-content-wrapper">
		<div class="page-content inset">
	        @include('snp2gene.newjob')
			@include('snp2gene.geneMap')
	        @include('snp2gene.joblist')

			<!-- genome wide plots -->
			<div class="sidePanel container" style="padding-top:50px;" id="genomePlots">
		        <!-- <h3>Genome Wide Plot</h3> -->
		        <!-- <div id="gPlotPanel" class="collapse in"> -->
		        <div class="container">
					<h4 style="color: #00004d">Manhattan Plot (GWAS summary statistics)</h4>
					<span class="info"><i class="fa fa-info"></i>
						Manhattan plot of the input GWAS summary statistics.<br/>
						For plotting, overlapping data points are not drawn (filtering was performed only for SNPs with P-value &ge; 1e-5, see tutorial for details).
					</span><br/><br/>
					Download the plot as
					<button class="btn btn-xs ImgDown" onclick='ImgDown("manhattan","png");'>PNG</button>
					<button class="btn btn-xs ImgDown" onclick='ImgDown("manhattan","jpeg");'>JPG</button>
					<button class="btn btn-xs ImgDown" onclick='ImgDown("manhattan","svg");'>SVG</button>
					<button class="btn btn-xs ImgDown" onclick='ImgDown("manhattan","pdf");'>PDF</button>

					<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="dir" id="manhattanDir" val=""/>
						<input type="hidden" name="id" id="manhattanID" val=""/>
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
						<input type="hidden" name="id" id="geneManhattanID" val=""/>
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
					<div id="QQplotPane" class="row">
						<!-- <div class="row"> -->
						<div class="col-md-6 col-xs-6 col-sm-6">
							<h4 style="color: #00004d">QQ plot (GWAS summary statisics)</h4>
							<span class="info"><i class="fa fa-info"></i>
								This is a Q-Q plot of GWAS summary statistics. <br/>
								For plotting purposes, overlapping data points are not drawn (filtering was performed only for SNPs with P-value &ge; 1e-5, see tutorial for details).
							</span><br/><br/>
							Download the plot as
							<button class="btn btn-xs ImgDown" onclick='ImgDown("QQplot","png");'>PNG</button>
							<button class="btn btn-xs ImgDown" onclick='ImgDown("QQplot","jpeg");'>JPG</button>
							<button class="btn btn-xs ImgDown" onclick='ImgDown("QQplot","svg");'>SVG</button>
							<button class="btn btn-xs ImgDown" onclick='ImgDown("QQplot","pdf");'>PDF</button>

							<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="dir" id="QQplotDir" val=""/>
								<input type="hidden" name="id" id="QQplotID" val=""/>
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
							<h4 style="color: #00004d">QQ plot (gene-based test)</h4>
							<span class="info"><i class="fa fa-info"></i>
								This is a Q-Q plot of the gene-based test computed by MAGMA.<br/>
								<br/>
							</span><br/><br/>
							Download the plot as
							<button class="btn btn-xs ImgDown" onclick='ImgDown("geneQQplot","png");'>PNG</button>
							<button class="btn btn-xs ImgDown" onclick='ImgDown("geneQQplot","jpeg");'>JPG</button>
							<button class="btn btn-xs ImgDown" onclick='ImgDown("geneQQplot","svg");'>SVG</button>
							<button class="btn btn-xs ImgDown" onclick='ImgDown("geneQQplot","pdf");'>PDF</button>

							<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="dir" id="geneQQplotDir" val=""/>
								<input type="hidden" name="id" id="geneQQplotID" val=""/>
								<input type="hidden" name="data" id="geneQQplotData" val=""/>
								<input type="hidden" name="type" id="geneQQplotType" val=""/>
								<input type="hidden" name="fileName" id="geneQQplotFileName" val=""/>
								<input type="submit" id="geneQQplotSubmit" class="ImgDownSubmit"/>
							</form>
							<div>
								<div id="geneQQplot"></div>
							</div>
						</div>
					</div>
					<br/><br/>
					<h4 style="color: #00004d">MAGMA Gene-Set Analysis</h4>
					<span class="info"><i class="fa fa-info"></i>
						MAGMA gene-set analysis is performed for curated gene sets and GO terms obtained from MsigDB (total of 10894 gene sets).<br/>
						The table displays the top the 10 significant gene sets with a maximum of P<sub>bon</sub> < 0.05.
						Full results are downloadable from "Download" tab. <br/>
						Note that MAGMA gene-set analyses uses the full distribution of SNP p-values and is different from a pathway enrichment test as implemented in GENE2FUNC that only tests for enrichment of prioritized genes.
					</span><br/><br/>
					<table id="MAGMAtable" class="display compact" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
						<thead>
							<th>Gene Set</th><th>N genes</th><th>Beta</th><th>Beta STD</th><th>SE</th><th>P</th><th>P<sub>bon</sub></th>
						</thead>
					</table>
					<br/><br/>
					<h4 style="color: #00004d">MAGMA Tissue Expression Analysis</h4>
					<span class="info"><i class="fa fa-info"></i>
						MAGMA gene-property analysis is performed for gene expression per tissue baed on GTEx RNA-seq data.<br/>
						Expression values (RPKM) were log2 transformed with pseudocount 1 after winsorization at 50 and average was taked per tissue.
						MAGMA was performed for average expression of 30 general tissue types and 53 specific tissue types separately.
						Full results are downloadable from "Download" tab. <br/>
						Note that MAGMA gene-property analyses uses the full distribution of SNP p-values and is different from a enrichment test of DEG (differentially expressed genes) and tissue expressed genes as implemented in GENE2FUNC that only tests for enrichment of prioritized genes.
					</span><br/><br/>
					<div id="magmaPlot">
						General 30 tissue types<br/>
						Download the plot as
						<button class="btn btn-xs ImgDown" onclick='ImgDown("magma_exp_general","png");'>PNG</button>
						<button class="btn btn-xs ImgDown" onclick='ImgDown("magma_exp_general","jpeg");'>JPG</button>
						<button class="btn btn-xs ImgDown" onclick='ImgDown("magma_exp_general","svg");'>SVG</button>
						<button class="btn btn-xs ImgDown" onclick='ImgDown("magma_exp_general","pdf");'>PDF</button>

						<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="dir" id="magma_exp_generalDir" val=""/>
							<input type="hidden" name="id" id="magma_exp_generalID" val=""/>
							<input type="hidden" name="data" id="magma_exp_generalData" val=""/>
							<input type="hidden" name="type" id="magma_exp_generalType" val=""/>
							<input type="hidden" name="fileName" id="magma_exp_generalFileName" val=""/>
							<input type="submit" id="magma_exp_generalSubmit" class="ImgDownSubmit"/>
						</form>
						<br/>
						<span class="form-inline">
							Order tissue by :
							<select id="magmaTsGorder" class="form-control" style="width: auto;">
								<option value="alph">Alphabetical</option>
								<option value="p" selected>P-value</option>
							</select>
						</span>
						<div id="magma_exp_general"></div>

						Specific 53 tissue types<br/>
						Download the plot as
						<button class="btn btn-xs ImgDown" onclick='ImgDown("magma_exp","png");'>PNG</button>
						<button class="btn btn-xs ImgDown" onclick='ImgDown("magma_exp","jpeg");'>JPG</button>
						<button class="btn btn-xs ImgDown" onclick='ImgDown("magma_exp","svg");'>SVG</button>
						<button class="btn btn-xs ImgDown" onclick='ImgDown("magma_exp","pdf");'>PDF</button>

						<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="dir" id="magma_expDir" val=""/>
							<input type="hidden" name="id" id="magma_expID" val=""/>
							<input type="hidden" name="data" id="magma_expData" val=""/>
							<input type="hidden" name="type" id="magma_expType" val=""/>
							<input type="hidden" name="fileName" id="magma_expFileName" val=""/>
							<input type="submit" id="magma_expSubmit" class="ImgDownSubmit"/>
						</form>
						<br/>
						<span class="form-inline">
							Order tissue by :
							<select id="magmaTsorder" class="form-control" style="width: auto;">
								<option value="alph">Alphabetical</option>
								<option value="p" selected>P-value</option>
							</select>
						</span>
						<div id="magma_exp"></div>
					</div>
				</div>
      		</div>

			<!-- ERROR:005 -->
			<div class="sidePanel container" style="padding-top:50px;" id="error5">
				<h4 style="color: #00004d">ERROR:005 No candidate SNPs were found</h4>
				<div id="error5mes">
					<p>Error because of no significant SNP in the GWAS summary statistics.<br/>
						To obtain annotations; use a less stringent P-value threshold for lead SNPs or provide predefined lead SNPs.<br/>
					</p>
				</div>
				<br/>
				<h4 style="color: #00004d">Top 10 SNPs in the input file</h4>
				<span class="info"><i class="fa fa-info"></i>
					Top 10 significant SNPs of the input file.
					Refer the following P-value to set threshold for lead SNPs in the next submission.<br/>
					Note that deccreasing MAF threshold may lead to more hits (default MAF &ge; 0.01). <br/>
					Note that the MHC region is excluded by default. Check this option to include MHC in the analysis.
				</span>
				<br/>
				<table class="table table-bordered" id="topSNPs"></table>
			</div>

			<!-- Summary panel -->
			<div class="sidePanel container" style="padding-top:50px;" id="summaryTable">
				<div class="row">
					<div class="col-md-5 col-xs-5 col-sm-5" id="sumTable" style="text-align:center;">
						<h4 style="color: #00004d">Summary of SNPs and mapped genes</h4>
					</div>

					<div class="col-md-7 col-xs-7 col-sm-7" style="text-align:center;">
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
							<input type="hidden" name="id" id="snpAnnotPlotID" val=""/>
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
					<h4><span style="color: #00004d">Summary per genomic risk locus</span>
					<a class="infoPop" data-toggle="popover" data-content="The histgrams dispaly summary results per genomic locus. Note that genomic loci could contain more than one independent lead SNPs.">
						<i class="fa fa-question-circle-o fa-lg"></i>
					</a>
					</h4>
					Download the plot as
					<button class="btn btn-xs ImgDown" onclick='ImgDown("lociPlot","png");'>PNG</button>
					<button class="btn btn-xs ImgDown" onclick='ImgDown("lociPlot","jpeg");'>JPG</button>
					<button class="btn btn-xs ImgDown" onclick='ImgDown("lociPlot","svg");'>SVG</button>
					<button class="btn btn-xs ImgDown" onclick='ImgDown("lociPlot","pdf");'>PDF</button>

					<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/imgdown">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="dir" id="lociPlotDir" val=""/>
						<input type="hidden" name="id" id="lociPlotID" val=""/>
						<input type="hidden" name="data" id="lociPlotData" val=""/>
						<input type="hidden" name="type" id="lociPlotType" val=""/>
						<input type="hidden" name="fileName" id="lociPlotFileName" val=""/>
						<input type="submit" id="lociPlotSubmit" class="ImgDownSubmit"/>
					</form>
					<div id="lociPlot"></div>
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
						<li role="presentation" class="active"><a class="RegionalPlotOn" href="#lociTablePane" aria-controls="lociTablePane" rolw="tab" data-toggle="tab">Genomic risk loci</a></li>
						<li role="presentation"><a class="RegionalPlotOn" href="#leadSNPtablePane" aria-controls="leadSNPtablePane" rolw="tab" data-toggle="tab">lead SNPs</a></li>
						<li role="presentation"><a class="RegionalPlotOn" href="#sigSNPtablePane" aria-controls="sigSNPtablePane" rolw="tab" data-toggle="tab">Ind. Sig. SNPs</a></li>
						<li role="presentation"><a class="RegionalPlotOff" href="#SNPtablePane" aria-controls="SNPtablePane" rolw="tab" data-toggle="tab">SNPs (annotations)</a></li>
						<li role="presentation"><a class="RegionalPlotOff" href="#annovTablePane" aria-controls="annovTablePane" rolw="tab" data-toggle="tab">ANNOVAR</a></li>
						<li role="presentation"><a class="RegionalPlotOff" href="#geneTablePane" aria-controls="geneTablePane" rolw="tab" data-toggle="tab">Mapped Genes</a></li>
						<li role="presentation" id="eqtlTableTab"><a class="RegionalPlotOff" href="#eqtlTablePane" aria-controls="eqtlTablePane" rolw="tab" data-toggle="tab">eQTL</a></li>
						<li role="presentation" id="ciTableTab"><a class="RegionalPlotOff" href="#ciTablePane" aria-controls="ciTablePane" rolw="tab" data-toggle="tab">Chromatin interactions</a></li>
						<li role="presentation" id="gwascatTableTab"><a class="RegionalPlotOff" href="#gwascatTablePane" aria-controls="gwascatTablePane" rolw="tab" data-toggle="tab">GWAScatalog</a></li>
						<!-- <li role="presentation"><a href="#exacTablePane" aria-controls="exacTablePane" rolw="tab" data-toggle="tab">ExAC</a></li> -->
						<li role="presentation"><a class="RegionalPlotOff" href="#paramsPane" aria-controls="paramsPane" rolw="tab" data-toggle="tab">Parameters</a></li>
						<!-- <li role="presentation"><a href="#downloads" aria-controls="downloads" rolw="tab" data-toggle="tab">Downloads</a></li> -->
					</ul>
					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="lociTablePane">
							<br/>
							<p class="info">
								<i class="fa fa-info"></i> Click row to display a regional plot of GWAS summary statistics.
							</p>
							<table id="lociTable" class="display compact dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
								<thead>
									<tr>
										<th>Genomic Locus</th><th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>P-value</th><th>start</th><th>end</th><th>nSNPs</th><th>nGWASSNPs</th><th>nIndSigSNPs</th><th>IndSigSNPs</th><th>nLeadSNPs</th><th>LeadSNPs</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>

						<div role="tabpanel" class="tab-pane" id="leadSNPtablePane">
							<br/>
							<p class="info">
								<i class="fa fa-info"></i> Click row to display a regional plot of GWAS summary statistics.
							</p>
							<table id="leadSNPtable" class="display compact" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
								<thead>
									<tr>
										<th>No</th><th>Genomic Locus</th><th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>P-value</th><th>nIndSigSNPs</th><th>IndSigSNPs</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>

						<div role="tabpanel" class="tab-pane" id="sigSNPtablePane">
							<br/>
							<p class="info">
								<i class="fa fa-info"></i> Click row to display a regional plot of GWAS summary statistics.
							</p>
							<table id="sigSNPtable" class="display compact" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
								<thead>
									<tr>
										<th>No</th><th>Genomic Locus</th><th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>P-value</th><th>nSNPs</th><th>nGWASSNPs</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>

						<div role="tabpanel" class="tab-pane" id="SNPtablePane">
							<br/>
							<span class="info"><i class="fa fa-info"></i> This table contain all SNPs in LD of identified lead SNPs (functional filtering for gene mapping is not applied in this table).</span>
							<br/>
							<span class="info"><i class="fa fa-info"></i> In this table "alt" is the risk allele if provided in the input GWAS file. See <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#table-columns">tutorial</a> for details.</span>
							<br/>
							<table id="SNPtable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
							</table>
						</div>

						<div role="tabpanel" class="tab-pane" id="annovTablePane">
							<br/>
							<span class="info"><i class="fa fa-info"></i> This is the result of annotation by ANNOVAR. SNPs can appear multiple times in this table if they are annotated to more than one genes.</span>
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
							<span class="info"><i class="fa fa-info"></i>
								When signed effect size (beta or OR) is provided in the input GWAS file, risk increasing alleles are aligned to the tested alleles of eQTLs.
								See <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">tutorial</a> for details.
								<br/>
								When P-value or FDR are not available in the original data source, they are replaced with -9.
							</span>
							<br/>
							<table id="eqtlTable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
								<thead>
									<tr>
										<th>uniqID</th><th>chr</th><th>pos</th><th>testedAllele</th><th>DB</th><th>tissue</th><th>Gene</th><th>Symbol</th><th>P-value</th><th>FDR</th><th>signed_stats</th><th>RiskincAllele</th><th>alignedDirection</th>
									</tr>
								</thead>
							</table>
						</div>

						<div role="tabpanel" class="tab-pane" id="ciTablePane">
							<br/>
							<h4>Chromatin interaction</h4>
							<table id="ciTable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
								<thead>
									<tr>
										<th>GenomicLocus</th><th>region1</th><th>region2</th><th>FDR</th><th>type</th><th>DB</th><th>tissue/cell</th><th>inter/intra</th><th>SNPs</th><th>Genes</th>
									</tr>
								</thead>
							</table>
							<br/>
							<h4>SNPs and overlapped regulatory elements in region 1</h4>
							<table id="ciSNPsTable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
								<thead>
									<tr>
										<th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>regulatory region</th><th>type</th><th>tissue/cell</th>
									</tr>
								</thead>
							</table>
							<br/>
							<h4>Regulatory elements and genes in regions 2</h4>
							<table id="ciGenesTable" class="display compact dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
								<thead>
									<tr>
										<th>region2</th><th>regulatory region</th><th>type</th><th>tissue/cell</th><th>genes</th>
									</tr>
								</thead>
							</table>
							<br/>
							<h4>Circos plots of chromatin interactions and eQTLs</h4>
							Download circos plots (all displayed chromosomes) as
							<button class="btn btn-xs circosDown" onclick='circosDown("png");'>PNG</button>
							<button class="btn btn-xs circosDown" onclick='circosDown("svg");'>SVG</button>
							<button class="btn btn-xs circosDown" onclick='circosDown("conf");'>Circos config files</button>

							<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/snp2gene/circosDown">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="id" id="circosPlotID" val=""/>
								<input type="hidden" name="prefix" id="circosPlotDir" val=""/>
								<input type="hidden" name="type" id="circosPlotType" val=""/>
								<input type="submit" id="circosPlotSubmit" class="ImgDownSubmit"/>
							</form>
							<br/><br/>
							<p>
								The specific layers and color-coding of the circos plot is described below.
								See <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#outputs">tutorial</a> for details.<br/>
								<ul>
									<li>Manhattan plot: The most outer layer. Only SNPs with P < 0.05 are displayed. SNPs in genomic risk loci are color-coded as a function of their maximum r<sup>2</sup> to the one of the independent significant SNPs in the locus, as follows:
										red (r<sup>2</sup> > 0.8), orange (r<sup>2</sup> > 0.6), green (r<sup>2</sup> > 0.4) and blue (r<sup>2</sup> > 0.2). SNPs that are not in LD with any of the independent significat SNPs (with r<sup>2</sup> &le; 0.2) are grey.<br/>
										The rsID of the top SNPs in each risk locus are displayed in the most outer layer.
										Y-axis are raned between 0 to the maximum -log10(P-value) of the SNPs.
									</li>
									<li>Chromosome ring: The second layer. Genomic risk loci are highlighted in blue.</li>
									<li>Mapped genes by chromatin interactions or eQTLs: Only mapped genes by either chroamtin interaction and/or eQTLs (conditional on user defined parameters) are displayed.
										If the gene is mapped only by chromatin interactions or only by eQTLs, it is colored orange or green, respectively. When the gene is mapped by both, it is colored red.</li>
									<li>Chromosome ring: The third layer. This is the same as second layer but without coordinates to make it easy to align position of genes with genomic coordinate.</li>
									<li>Chromatin interaction links: Links colored orange are chromatin interactions. Since v1.2.7, only the interactions used for mapping based on user defined parameters are displayed.</li>
									<li>eQTL lilnks: Links colored green are eQTLs.  Since v1.2.7, only the eQTLs used for mapping based on user defined parameters are displayed.</li>
								</ul>
								<span class="info"><i class="fa fa-info"></i>
									Since creating a circos plot might take long time with a large number of points and links, the maximum number of points and links are limited to 50,000 and 10,000 per plot (chromosome), respectively, in the default plot.
									Therefore, if there are more than 50,000 SNPs with P-value < 0.05 in a chromosome, top 50,000 SNPs (sorted by P-value) are displayed in the plot.
									This is same for eQTLs and chromatin interactions, e.g. if there are more than 10,000 eQTLs in a chromosome, top 10,000 eQTLs (sorted by P-value for eQTLs, FDR for chromatin interactions) are displayed in the plot.
									These can be optimized by downloading config file and re-creating input text files for SNPs and links.
									Please refer github repository <a href="https://github.com/Kyoko-wtnb/FUAM-circos-plot" target="_blank">FUMA circos plot</a> for details.
								</span>
							</p>
							<br/><br/>
							<div id="ciMapCircosPlot" style="text-align:center;"></div>
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
										<th>Genomic Locus</th><th>IndSigSNP</th><th>chr</th><th>bp</th><th>rsID</th><th>PMID</th><th>Trait</th><th>FirstAuth</th><th>Date</th><th>P-value</th>
									</tr>
								</thead>
							</table>
						</div>
						<!-- <div role="tabpanel" class="tab-pane" id="exacTablePane">
							<br/>
							<table id="exacTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
								<thead>
									<tr>
										<th>Genomic Locus</th><th>uniqID</th><th>chr</th><th>bp</th><th>ref</th><th>alt</th><th>Annotation</th><th>Gene</th><th>MAF</th>
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
							Please click one of the row of 'Genomic risk loci', 'lead SNPs' or 'ind. sig. SNPs' tables to display a regional plot.<br/>
							You can zoom in/out by mouse scroll. <br/>
							Each SNP is color-coded based on the highest r<sup>2</sup> to one of the ind. sig. SNPs, if that is greater or equal to the user defined threshold.
							Other SNPs (i.e. below the user-defined r<sup>2</sup>) are colored in grey.
							The top lead SNPs in genomic risk loci, lead SNPs and ind. sig. SNPs are circled in black and colored in dark-purple, purple and red, respectively.
						</span>
						<div class="row">
							<div class="col-md-9 col-xs-9 col-sm-9">
								<div id="locusPlot" style="text-align: center;">
									<a id="plotClear" style="position: absolute;right: 30px;">Clear</a>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 col-sm-3">
								<div id="selectedLeadSNP"></div>
							</div>
						</div>

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
									<input type="hidden" name="id" value="<?php echo $jobID;?>"/>
									<input type="hidden" name="prefix" value="jobs"/>
									<input type="hidden" name="annotPlotSelect" id="annotPlotSelect" value="null"/>
									<input type="hidden" name="annotPlotRow" id="annotPlotRow" value="null"/>
									Select annotation(s) to plot:<br/>
									<tab><input type="checkbox" name="annotPlot_GWASp" id="annotPlot_GWASp" checked/>GWAS association statistics<br/>
									<tab><input type="checkbox" name="annotPlot_CADD" id="annotPlot_CADD" checked/>CADD score<br/>
									<tab><input type="checkbox" name="annotPlot_RDB" id="annotPlot_RDB" checked/>RegulomeDB score<br/>
									<tab><input type="checkbox" name="annotPlot_Chrom15" id="annotPlot_Chrom15" onchange="Chr15Select();"/>Chromatine 15 state
									<div id="annotPlotChr15Opt">
										<tab><tab><span style="color:red;">Please select at least one tissue type.</span><br/>
										<tab><tab>Tissue/Cell types: <a id="annotPlotChr15TsClear">clear</a><br/>
										<tab><tab><select multiple size="10" id="annotPlotChr15Ts" name="annotPlotChr15Ts[]" onchange="Chr15Select()">
											<option value="all">All</option>
											<option class="level1" value="null">Adrenal (1)</option>
											<option class="level2" value="E080">E080 (Other) Fetal Adrenal Gland</option>
											<option class="level1" value="null">Blood (27)</option>
											<option class="level2" value="E029">E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
											<option class="level2" value="E030">E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
											<option class="level2" value="E031">E031 (HSC & B-cell) Primary B cells from cord blood</option>
											<option class="level2" value="E032">E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
											<option class="level2" value="E033">E033 (Blood & T-cell) Primary T cells from cord blood</option>
											<option class="level2" value="E034">E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
											<option class="level2" value="E035">E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
											<option class="level2" value="E036">E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
											<option class="level2" value="E037">E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
											<option class="level2" value="E038">E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
											<option class="level2" value="E039">E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
											<option class="level2" value="E040">E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
											<option class="level2" value="E041">E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
											<option class="level2" value="E042">E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
											<option class="level2" value="E043">E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
											<option class="level2" value="E044">E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
											<option class="level2" value="E045">E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
											<option class="level2" value="E046">E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
											<option class="level2" value="E047">E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
											<option class="level2" value="E048">E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
											<option class="level2" value="E050">E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
											<option class="level2" value="E051">E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
											<option class="level2" value="E062">E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
											<option class="level2" value="E115">E115 (ENCODE2012) Dnd41 TCell Leukemia Cell Line</option>
											<option class="level2" value="E116">E116 (ENCODE2012) GM12878 Lymphoblastoid Cells</option>
											<option class="level2" value="E123">E123 (ENCODE2012) K562 Leukemia Cells</option>
											<option class="level2" value="E124">E124 (ENCODE2012) Monocytes-CD14+ RO01746 Primary Cells</option>
											<option class="level1" value="null">Bone (1)</option>
											<option class="level2" value="E129">E129 (ENCODE2012) Osteoblast Primary Cells</option>
											<option class="level1" value="null">Brain (13)</option>
											<option class="level2" value="E053">E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
											<option class="level2" value="E054">E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
											<option class="level2" value="E067">E067 (Brain) Brain Angular Gyrus</option>
											<option class="level2" value="E068">E068 (Brain) Brain Anterior Caudate</option>
											<option class="level2" value="E069">E069 (Brain) Brain Cingulate Gyrus</option>
											<option class="level2" value="E070">E070 (Brain) Brain Germinal Matrix</option>
											<option class="level2" value="E071">E071 (Brain) Brain Hippocampus Middle</option>
											<option class="level2" value="E072">E072 (Brain) Brain Inferior Temporal Lobe</option>
											<option class="level2" value="E073">E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
											<option class="level2" value="E074">E074 (Brain) Brain Substantia Nigra</option>
											<option class="level2" value="E081">E081 (Brain) Fetal Brain Male</option>
											<option class="level2" value="E082">E082 (Brain) Fetal Brain Female</option>
											<option class="level2" value="E125">E125 (ENCODE2012) NH-A Astrocytes Primary Cells</option>
											<option class="level1" value="null">Breast (3)</option>
											<option class="level2" value="E027">E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
											<option class="level2" value="E028">E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
											<option class="level2" value="E119">E119 (ENCODE2012) HMEC Mammary Epithelial Primary Cells</option>
											<option class="level1" value="null">Cervix (1)</option>
											<option class="level2" value="E117">E117 (ENCODE2012) HeLa-S3 Cervical Carcinoma Cell Line</option>
											<option class="level1" value="null">ESC (8)</option>
											<option class="level2" value="E001">E001 (ESC) ES-I3 Cells</option>
											<option class="level2" value="E002">E002 (ESC) ES-WA7 Cells</option>
											<option class="level2" value="E003">E003 (ESC) H1 Cells</option>
											<option class="level2" value="E008">E008 (ESC) H9 Cells</option>
											<option class="level2" value="E014">E014 (ESC) HUES48 Cells</option>
											<option class="level2" value="E015">E015 (ESC) HUES6 Cells</option>
											<option class="level2" value="E016">E016 (ESC) HUES64 Cells</option>
											<option class="level2" value="E024">E024 (ESC) ES-UCSF4  Cells</option>
											<option class="level1" value="null">ESC Derived (9)</option>
											<option class="level2" value="E004">E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
											<option class="level2" value="E005">E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
											<option class="level2" value="E006">E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
											<option class="level2" value="E007">E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
											<option class="level2" value="E009">E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
											<option class="level2" value="E010">E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
											<option class="level2" value="E011">E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
											<option class="level2" value="E012">E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
											<option class="level2" value="E013">E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
											<option class="level1" value="null">Fat (3)</option>
											<option class="level2" value="E023">E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
											<option class="level2" value="E025">E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
											<option class="level2" value="E063">E063 (Adipose) Adipose Nuclei</option>
											<option class="level1" value="null">GI Colon (3)</option>
											<option class="level2" value="E075">E075 (Digestive) Colonic Mucosa</option>
											<option class="level2" value="E076">E076 (Sm. Muscle) Colon Smooth Muscle</option>
											<option class="level2" value="E106">E106 (Digestive) Sigmoid Colon</option>
											<option class="level1" value="null">GI Duodenum (2)</option>
											<option class="level2" value="E077">E077 (Digestive) Duodenum Mucosa</option>
											<option class="level2" value="E078">E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
											<option class="level1" value="null">GI Esophagus (1)</option>
											<option class="level2" value="E079">E079 (Digestive) Esophagus</option>
											<option class="level1" value="null">GI Intestine (3)</option>
											<option class="level2" value="E084">E084 (Digestive) Fetal Intestine Large</option>
											<option class="level2" value="E085">E085 (Digestive) Fetal Intestine Small</option>
											<option class="level2" value="E109">E109 (Digestive) Small Intestine</option>
											<option class="level1" value="null">GI Rectum (3)</option>
											<option class="level2" value="E101">E101 (Digestive) Rectal Mucosa Donor 29</option>
											<option class="level2" value="E102">E102 (Digestive) Rectal Mucosa Donor 31</option>
											<option class="level2" value="E103">E103 (Sm. Muscle) Rectal Smooth Muscle</option>
											<option class="level1" value="null">GI Stomach (4)</option>
											<option class="level2" value="E092">E092 (Digestive) Fetal Stomach</option>
											<option class="level2" value="E094">E094 (Digestive) Gastric</option>
											<option class="level2" value="E110">E110 (Digestive) Stomach Mucosa</option>
											<option class="level2" value="E111">E111 (Sm. Muscle) Stomach Smooth Muscle</option>
											<option class="level1" value="null">Heart (4)</option>
											<option class="level2" value="E083">E083 (Heart) Fetal Heart</option>
											<option class="level2" value="E095">E095 (Heart) Left Ventricle</option>
											<option class="level2" value="E104">E104 (Heart) Right Atrium</option>
											<option class="level2" value="E105">E105 (Heart) Right Ventricle</option>
											<option class="level1" value="null">Kidney (1)</option>
											<option class="level2" value="E086">E086 (Other) Fetal Kidney</option>
											<option class="level1" value="null">Liver (2)</option>
											<option class="level2" value="E066">E066 (Other) Liver</option>
											<option class="level2" value="E118">E118 (ENCODE2012) HepG2 Hepatocellular Carcinoma Cell Line</option>
											<option class="level1" value="null">Lung (5)</option>
											<option class="level2" value="E017">E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
											<option class="level2" value="E088">E088 (Other) Fetal Lung</option>
											<option class="level2" value="E096">E096 (Other) Lung</option>
											<option class="level2" value="E114">E114 (ENCODE2012) A549 EtOH 0.02pct Lung Carcinoma Cell Line</option>
											<option class="level2" value="E128">E128 (ENCODE2012) NHLF Lung Fibroblast Primary Cells</option>
											<option class="level1" value="null">Muscle (7)</option>
											<option class="level2" value="E052">E052 (Myosat) Muscle Satellite Cultured Cells</option>
											<option class="level2" value="E089">E089 (Muscle) Fetal Muscle Trunk</option>
											<option class="level2" value="E100">E100 (Muscle) Psoas Muscle</option>
											<option class="level2" value="E107">E107 (Muscle) Skeletal Muscle Male</option>
											<option class="level2" value="E108">E108 (Muscle) Skeletal Muscle Female</option>
											<option class="level2" value="E120">E120 (ENCODE2012) HSMM Skeletal Muscle Myoblasts Cells</option>
											<option class="level2" value="E121">E121 (ENCODE2012) HSMM cell derived Skeletal Muscle Myotubes Cells</option>
											<option class="level1" value="null">Muscle Leg (1)</option>
											<option class="level2" value="E090">E090 (Muscle) Fetal Muscle Leg</option>
											<option class="level1" value="null">Ovary (1)</option>
											<option class="level2" value="E097">E097 (Other) Ovary</option>
											<option class="level1" value="null">Pancreas (2)</option>
											<option class="level2" value="E087">E087 (Other) Pancreatic Islets</option>
											<option class="level2" value="E098">E098 (Other) Pancreas</option>
											<option class="level1" value="null">Placenta (2)</option>
											<option class="level2" value="E091">E091 (Other) Placenta</option>
											<option class="level2" value="E099">E099 (Other) Placenta Amnion</option>
											<option class="level1" value="null">Skin (8)</option>
											<option class="level2" value="E055">E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
											<option class="level2" value="E056">E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
											<option class="level2" value="E057">E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
											<option class="level2" value="E058">E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
											<option class="level2" value="E059">E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
											<option class="level2" value="E061">E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
											<option class="level2" value="E126">E126 (ENCODE2012) NHDF-Ad Adult Dermal Fibroblast Primary Cells</option>
											<option class="level2" value="E127">E127 (ENCODE2012) NHEK-Epidermal Keratinocyte Primary Cells</option>
											<option class="level1" value="null">Spleen (1)</option>
											<option class="level2" value="E113">E113 (Other) Spleen</option>
											<option class="level1" value="null">Stromal Connective (2)</option>
											<option class="level2" value="E026">E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
											<option class="level2" value="E049">E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
											<option class="level1" value="null">Thymus (2)</option>
											<option class="level2" value="E093">E093 (Thymus) Fetal Thymus</option>
											<option class="level2" value="E112">E112 (Thymus) Thymus</option>
											<option class="level1" value="null">Vascular (2)</option>
											<option class="level2" value="E065">E065 (Heart) Aorta</option>
											<option class="level2" value="E122">E122 (ENCODE2012) HUVEC Umbilical Vein Endothelial Primary Cells</option>
											<option class="level1" value="null">iPSC (5)</option>
											<option class="level2" value="E018">E018 (iPSC) iPS-15b Cells</option>
											<option class="level2" value="E019">E019 (iPSC) iPS-18 Cells</option>
											<option class="level2" value="E020">E020 (iPSC) iPS-20b Cells</option>
											<option class="level2" value="E021">E021 (iPSC) iPS DF 6.9 Cells</option>
											<option class="level2" value="E022">E022 (iPSC) iPS DF 19.11 Cells</option>
										</select><br/>
									</div>
									<br/>
									<div id="check_eqtl_annotPlot"><tab><input type="checkbox" name="annotPlot_eqtl" id="annotPlot_eqtl" checked/>eQTL<br/></div>
									<div id="check_ci_annotPlot"><tab><input type="checkbox" name="annotPlot_ci" id="annotPlot_ci" checked/>Chromatin interaction<br/></div>
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
				<h4 style="color: #00004d">Download files</h4>
				<form action="filedown" method="post" target="_blank">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="id" value="<?php echo $jobID;?>"/>
					<input type="hidden" name="prefix" value="jobs"/>
					<input type="checkbox" name="paramfile" id="paramfile" checked onchange="DownloadFiles();">Parameter settings</br>
					<input type="checkbox" name="locifile" id="locifile" checked onchange="DownloadFiles();">Genomic risk loci table <br/>
					<input type="checkbox" name="leadfile" id="leadfile" checked onchange="DownloadFiles();">lead SNP table (independent lead SNPs at r2 0.1) </br>
					<input type="checkbox" name="indSNPfile" id="indSNPfile" checked onchange="DownloadFiles();">Independent Significant SNPs table (independent at user defined r2) </br>
					<input type="checkbox" name="snpsfile" id="snpsfile" checked onchange="DownloadFiles();"> SNP table (Candidate SNPs with chr, bp, P-value, CADD, RDB, nearest gene, genomic risk loci and lead SNPs)<br/>
					<input type="checkbox" name="annovfile" id="annovfile" checked onchange="DownloadFiles();">ANNOVAR results (uniqID, annotation, gene and distance, SNP-gene pair per line)<br/>
					<input type="checkbox" name="annotfile" id="annotfile" checked onchange="DownloadFiles();">Annotations (CADD, RDB and Chromatin state of 127 tissue/cell types)<br/>
					<input type="checkbox" name="genefile" id="genefile" checked onchange="DownloadFiles();">Gene table (mapped genes)<br/>
					<div id="eqtlfiledown"><input type="checkbox" name="eqtlfile" id="eqtlfile" checked onchange="DownloadFiles();">eQTL table (eQTL of selected tissue types)<br/></div>
					<div id="cifiledown"><input type="checkbox" name="cifile" id="cifile" checked onchange="DownloadFiles();">Chromatin interaction tables (chromatin interactions overlap with candidate SNPs and regulatory elements)<br/></div>
					<!-- <input type="checkbox" name="exacfile" id="exacfile" checked onchange="DownloadFiles();">ExAC variants (rare variants from ExAC within genomic risk locis)<br/> -->
					<input type="checkbox" name="gwascatfile" id="gwascatfile" checked onchange="DownloadFiles();">SNPs in GWAS catalog (full features)<br/>
					<input type="checkbox" name="magmafile" id="magmafile" checked onchange="DownloadFiles();">MAGMA (full) results<br/>
					<a id="allfiles"> Select All </a><tab><a id="clearfiles"> Clear</a><br/>
					<br/>
					<input class="btn" type="submit" name="download" id="download" value="Download files"/>
				</form>
			</div>
		</div>
	</div>
</div>

@stop
