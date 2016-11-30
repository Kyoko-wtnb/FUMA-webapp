@extends('layouts.master')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.0/js/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
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
</script>
<script type="text/javascript" src="{!! URL::asset('js/GWASresult.js') !!}"></script>
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">
<script type="text/javascript">
  var public_path = "{{ URL::asset('/image/ajax-loader.gif') }}";
</script>
@section('content')
<div class="container" style="padding-top: 50px;">
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="panel-title">Select GWASs from database</div>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-3">
        Domain:<br/>
        <select class="selectpicker" name="Domain" id="Domain" onchange='Selection("Domain");'>
          <option value=null>-- Please select Domain of interest --</option>
        </select>
      </div>
      <div class="col-md-3">
        Chapter level:<br/>
        <select class="selectpicker" name="Chapter" id="Chapter" onchange='Selection("Chapter");'>
          <option value=null>-- Please select Chapter of interest --</option>
        </select>
      </div>
      <div class="col-md-3">
        Subchapter level:<br/>
        <select class="selectpicker" name="Subchapter" id="Subchapter" onchange='Selection("Subchapter");'>
          <option value=null>-- Please select Subchapter of interest --</option>
        </select>
      </div>
      <div class="col-md-3">
        Trait:<br/>
        <select class="selectpicker" name="Trait" id="Trait" onchange='Selection("Trait");'>
          <option value=null>-- Please select Trait of interest --</option>
        </select>
      </div>
    </div>
    <br/>
    <!-- <div id="kewqordSerch">
      Keyword:<br/>
      <input type="text" id="keyword" name="keyword" style="height: 30; width: 250;"/>
    </div>
    <br/> -->
    <div>
      <table id="selectTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
        <thead>
          <th>ID</th>
          <th>PMID</th>
          <th>Year</th>
          <th>Domain</th>
          <th>Chapter level</th>
          <th>Subchapter level</th>
          <th>Trait</th>
          <th>Case</th>
          <th>Control</th>
          <th>N</th>
          <th>Population</th>
          <th>SNP h2</th>
          <th>Web site</th>
        </thead>
      </table>
    </div>
    <!-- <select name="GWAstudy" id="GWAstudy">
      <option value="BMI2015" selected>Body Mass Index adult (2015)</option>
      <option value="SWB2016">Subject Well Being (2016)</option>
      <option value="EDU2016">Educational attainment Disc+Rep (2016)</option>
      <option value="IQ_child2014">Highlevel Cognitive Functions (IQ) (2014)</option>
      <option value="ALZ2013">Alzheimer's disease Stage 1 (2013)</option>
      <option value="HC_child2012">Structure of Head and Neck Regions (2012)</option>
    </select>
    <br/><br/>
    <input class="btn" type="submit" name="GWAstudySelect" id="GWAstudySelect" value="Select"/> -->
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="panel-title">Selected GWAS studies</div>
  </div>
  <div class="panel-body">
    <table id="selectedTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
      <thead>
        <th>Delete</th>
        <th>Plot</th>
        <th>ID</th>
        <th>PMID</th>
        <th>Year</th>
        <th>Domain</th>
        <th>Chapter level</th>
        <th>Subchapter level</th>
        <th>Trait</th>
        <th>Case</th>
        <th>Control</th>
        <th>N</th>
        <th>Population</th>
        <th>SNP h2</th>
        <th>Web site</th>
      </thead>
    </table>
  </div>
</div>
<div id="test"></div>
<div id="Plots">
  <!-- <h3>Selected GWA studies</h3>
  <table id="gwasTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
    <thead>
      <tr>
        <th>ID</th>
        <th>PMID</th>
        <th>Year</th>
        <th>Domain</th>
        <th>Chapter level</th>
        <th>Subchapter level</th>
        <th>Trait</th>
        <th>Case</th>
        <th>Control</th>
        <th>N</th>
        <th>Population</th>
        <th>SNP h2</th>
        <th>Web site</th>
      </tr>
    </thead>
  </table>
  <br/>
  <br/> -->
  <h3>Manhattan plot (GWAS summary statistics)</h3>
  <div id="ManhattanPanel" style="position: relative;">
    <!-- <div id="manhattan" class="canvasarea"></div> -->
  </div>
  <br/>
  <h3>Manhattan plot (gene based test by MAGMA)</h3>
  <div id="GenemanPlot" style="position: relative;">
    <div id="GenesManhattan" class="canvasarea"></div>
  </div>
  <br/>
  <h3>Q-Q plot</h3>
  <div class="row" style="position: relative;" id="QQPlot">
    <div class="col-md-6">
      <div id="QQSNPs" class="canvasarea"></div>
    </div>
    <div class="col-md-6">
      <div id="QQGenes" class="canvasarea"></div>
    </div>
  </div>
  <br/>
</div>
</div>
@stop
