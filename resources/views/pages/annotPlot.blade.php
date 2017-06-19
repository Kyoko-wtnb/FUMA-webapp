<!-- <html> -->
@extends('layouts.simple')
@section('head')
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//d3js.org/d3.v3.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<script type="text/javascript" src="//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="//d3js.org/queue.v1.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}"/>

<script type="text/javascript">
$.ajaxSetup({
	headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
});
var loggedin = "{{ Auth::check() }}";
var id = "{{$id}}";
var prefix = "{{$prefix}}";
var type = "{{$type}}";
var rowI = parseInt("{{$rowI}}");
var GWASplot = parseInt("{{$GWASplot}}");
var CADDplot = parseInt("{{$CADDplot}}");
var RDBplot = parseInt("{{$RDBplot}}");
var eqtlplot = parseInt("{{$eqtlplot}}");
var ciplot = parseInt("{{$ciplot}}");
var Chr15 = parseInt("{{$Chr15}}");
var Chr15cells = "{{$Chr15cells}}";
</script>
<script type="text/javascript" src="{!! URL::asset('js/annotPlot.js') !!}"></script>
@stop
@section('content')
<canvas id="canvas" style="display:none;"></canvas>

<br/><br/>

<div class="container">
	<div class="row">
		<div class="col-md-9 col-xs-9 col-sm-9">
			<div id='title' style="text-align: center;"><h4>Regional plot</h4></div>
			<span class="info"><i class="fa fa-info"></i>
				For SNPs colored grey in the plots of GWAS P-value, CADD, RegulomeDB score and eQTLs, please refer the legend at the bottom of the plot.
			</span><br/>
			<a id="plotclear" style="position: absolute;right: 30px;">Clear</a><br/>
			Download the plot as
			<button class="btn btn-xs ImgDown" onclick='ImgDown("annotPlot","png");'>PNG</button>
			<button class="btn btn-xs ImgDown" onclick='ImgDown("annotPlot","jpeg");'>JPG</button>
			<button class="btn btn-xs ImgDown" onclick='ImgDown("annotPlot","svg");'>SVG</button>
			<button class="btn btn-xs ImgDown" onclick='ImgDown("annotPlot","pdf");'>PDF</button>

			<form method="post" target="_blank" action="imgdown">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dir" id="annotPlotDir" val=""/>
				<input type="hidden" name="id" id="annotPlotID" val=""/>
				<input type="hidden" name="data" id="annotPlotData" val=""/>
				<input type="hidden" name="type" id="annotPlotType" val=""/>
				<input type="hidden" name="fileName" id="annotPlotFileName" val=""/>
				<input type="submit" id="annotPlotSubmit" class="ImgDownSubmit"/>
			</form>    <div id="annotPlot"></div>
			<br/>
			<div id="load" style="text-align:center;"></div>
			<div id="RDBlegend"></div>
			*External link to RegulomeDB from SNP table (when one of the SNPs is clicked) will open a new tab.
			rsID does not always match since RegulomeDB used dbSNP build 141 (the rsID in FUMA is dbSNP build 146).
			Genomic position (bp on hg19) shown in the link of RegulomeDB is the position shown in the SNP table - 1, since RegulomeDB used 0 based corrdinate.
			<br/>
			<div id="EIDlegend"></div>
			<br/>
			<div id="SNPlegend">
			<h4>SNPs colored grey in the plots</h4>
			<strong>GWAS P-value</strong>: SNPs which are not in LD of any of significant independent lead SNPs in the selected region are colored grey.<br/>
			<strong>CADD score</strong>: Only SNPs which are in LD of any of significant independet lead SNPs are displayed in the plot.
			Of those SNPs, SNPs which did not used for mapping (SNPs that were filtered by user defined parameters) are colored grey.
			When both positional and eQTL mappings were performed, only SNPs which were not used either of them are colored grey.<br/>
			<strong>RegulomeDB score</strong>: Same as CADD score.<br/>
			<strong>eQTLs</strong>: When eQTL mapping was performed and eQTLs exist in the selected region, all eQTLs with user defined P-value threshold and tissue types are displayed.
			Of those eQTLs, eQTLs which did not used for eQTL mapping (eQTLs that were filtered by user defined parameters) are colored grey.<br/>
			</div>
		</div>
		<div class="col-md-3 col-xs-3 col-sm-3" style="text-align: center;">
			<h4>SNP annotations</h4>
			<div id="annotTable">
				click any SNP on the plot</br>
			</div>
		</div>
	</div>
</div>

<br/><br/>
@stop
