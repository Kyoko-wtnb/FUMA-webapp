@extends('layouts.master')
@section('head')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.0/js/bootstrap-select.min.js"></script>
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}?130">
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
var id = "{{$id}}";
var page = "{{$page}}"
var subdir = "{{ Config::get('app.subdir') }}";
var loggedin = "{{ Auth::check() }}";
</script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}?131"></script>
<script type="text/javascript" src="{!! URL::asset('js/s2g_results.js') !!}?135"></script>
<script type="text/javascript" src="{!! URL::asset('js/g2f_results.js') !!}?135"></script>
<script type="text/javascript" src="{!! URL::asset('js/browse.js') !!}?135"></script>
@stop
@section('content')
<div id="wrapper" class="active">
	<div id="sidebar-wrapper">
		<ul class="sidebar-nav" id="sidebar-menu">
			<li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
		</ul>
		<ul class="sidebar-nav" id="sidebar">
			<li><a href="#GwasList">GWAS list<i class="sub_icon fa fa-search"></i></a></li>
			<span style="padding-left:10px;font-size:14px;"><b>Example input page</b></span>
			<li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
			<div id="resultsSide">
				<span style="padding-left:10px;font-size:14px;"><b>SNP2GENE</b></span>
				<li><a href="#genomePlots">Genome-wide plots<i class="sub_icon fa fa-bar-chart"></i></a></li>
				<li><a href="#summaryTable">Summary of results<i class="sub_icon fa fa-bar-chart"></i></a></li>
				<li><a href="#tables">Results<i class="sub_icon fa fa-table"></i></a></li>
				<li><a href="#downloads">Download<i class="sub_icon fa fa-download"></i></a></li>
			</div>
			<div id="resultsSideG2F">
				<span style="padding-left:10px;font-size:14px;"><b>GENE2FUNC</b></span>
				<li><a href="#g2f_summaryPanel">Summary<i class="sub_icon fa fa-table"></i></a></li>
				<li><a href="#expPanel">Heatmap<i class="sub_icon fa fa-th"></i></a></li>
				<li><a href="#tsEnrichBarPanel">Tissue specificity<i class="sub_icon fa fa-bar-chart"></i></a></li>
				<li><a href="#GeneSetPanel">Gene sets<i class="sub_icon fa fa-bar-chart"></i></a></li>
				<li><a href="#GeneTablePanel">Gene table<i class="sub_icon fa fa-table"></i></a></li>
			</div>
		</ul>
	</div>

	<canvas id="canvas" style="display:none;"></canvas>

	<div id="page-content-wrapper">
		<div class="page-content inset">
			@include('browse.gwaslist')
			@include('browse.newjob')

			<!-- SNP2GENE result page -->
			@include('snp2gene.gwPlot')
			@include('snp2gene.summary')
			@include('snp2gene.result_tables')
			@include('snp2gene.filedown')

			<!-- GENE2FUNC result page -->
			@include('gene2func.summary')
			@include('gene2func.exp_heat')
			@include('gene2func.DEG')
			@include('gene2func.genesets')
			@include('gene2func.geneTable')
		</div>
 	</div>
</div>

@stop
