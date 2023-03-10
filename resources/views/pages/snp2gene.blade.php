@extends('layouts.master')

@section('stylesheets')	
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endsection

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

		<!-- <canvas id="canvas" style="display:none;"></canvas> -->

		<div id="page-content-wrapper">
			<div class="page-content inset">
				@include('snp2gene.newjob')
				@include('snp2gene.geneMap')
				@include('snp2gene.joblist')

				@include('snp2gene.gwPlot')
				@include('snp2gene.error5')
				@include('snp2gene.summary')
				@include('snp2gene.result_tables')
				@include('snp2gene.filedown')
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	{{-- Imports from the web --}}
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.0/js/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
	<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
	<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.16/sorting/scientific.js"></script>
	<script type="text/javascript" src="//d3js.org/d3.v3.min.js"></script>
	<script src="//labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
	<script type="text/javascript" src="//d3js.org/queue.v1.min.js"></script>

	{{-- Hand written ones --}}
	<script type="text/javascript">
		$.ajaxSetup({
			headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
		});
		var status = "{{$status}}";
		var id = "{{$id}}";
		var page = "{{$page}}";
		var subdir = "{{ Config::get('app.subdir') }}";
		var loggedin = "{{ Auth::check() }}";
	</script>

	{{-- Imports from the project --}}
		<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}?131"></script>
		<script type="text/javascript" src="{!! URL::asset('js/NewJobParameters.js') !!}?136"></script>
		<script type="text/javascript" src="{!! URL::asset('js/geneMapParameters.js') !!}?135"></script>
		<script type="text/javascript" src="{!! URL::asset('js/s2g_results.js') !!}?135"></script>
		<script type="text/javascript" src="{!! URL::asset('js/snp2gene.js') !!}?135a"></script>
	
@endsection
