@extends('layouts.master')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
  });
  var public_path = "{{ URL::asset('/image/ajax-loader2.gif') }}";
  var storage_path = "<?php echo storage_path();?>";
  var status = "{{$status}}";
</script>
<script type="text/javascript" src="{!! URL::asset('js/gene2func.js') !!}"></script>

@section('content')
<div id="test"></div>
{!! Form::open(array('url' => 'gene2func/submit', 'files'=>true)) !!}
<h3>Input list of genes</h3>
<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading"><h4>Genes of interest</h4></div>
      <div class="panel-body" style="padding-bottom: 0;">
        <p style="color: #004d99">Please either paste or upload a file of genes to test.
          When both are provided, only genes pasted in the text box will be used.
          So please make sure the text box is clear when you upload a file.
        </p>
        1. Paste genes<br/>
        <textarea id="genes" name="genes" rows="12" cols="50" placeholder="Please enter each gene per line here." onkeyup="checkInput()" oninput="checkInput()"></textarea><br/>
        <br/>
        2. Upload file
        <tab><input type="file" name="genesfile" id="genesfile" onchange="checkInput()"/>
        <br/>
        <div id="GeneCheck" style="padding-bottom: 0;"></div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div id="backgroundGenes"></div>
    <div class="panel panel-default">
      <div class="panel-heading"><h4>Background genes</h4></div>
      <div class="panel-body" style="padding-bottom: 0;">
        <p style="color: #004d99">
          Please specify background genes for hypergeometric test.
          If more than one options are provided, options are prioritized by the following order; selected genes in the pipeline, pasted genes and uploaded file.
        </p>
        1. Select from genes in the pipeline<br/>
        <tab><select multiple size="5" name="genetype[]" id="genetype" onchange="checkInput();">
          <option value="all">All</option>
          <option value="protein_coding">Protein coding</option>
          <option value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA">lncRNA</option>
          <option value="miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA">ncRNA</option>
          <option value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA:miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA:processed_transcript">Processed transcripts</option>
          <option value="pseudogene:processed_pseudogene:unprocessed_pseudogene:polymorphic_pseudogene:IG_C_pseudogene:IG_D_pseudogene:ID_V_pseudogene:IG_J_pseudogene:TR_C_pseudogene:TR_D_pseudogene:TR_V_pseudogene:TR_J_pseudogene">Pseudogene</option>
          <option value="IG_C_gene:TG_D_gene:TG_V_gene:IG_J_gene">IG genes</option>
          <option value="TR_C_gene:TR_D_gene:TR_V_gene:TR_J_gene">TR genes</option>
        </select>
        <br/><br/>
        2. Paste genes<br/>
        <textarea id="bkgenes" name="bkgenes" rows="5" cols="50" placeholder="Please enter each gene per line here." onkeyup="checkInput();" oninput="checkInput()"></textarea><br/>
        <br/>
        3. Upload file
        <tab><input type="file" name="bkgenesfile" id="bkgenesfile" onchange="checkInput()"/>
        <br/>
        <div id="bkGeneCheck" style="padding-bottom: 0;"></div>
      </div>
    </div>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-body" style="padding:10;">
    <br/>
    <tab><input type="checkbox" id="Xchr" name="Xchr"><tab>Execlude genes on X chromosome. <span style="color: #004d99">Please check to EXCLUDE X chromosome.</span><br/>
    <tab><input type="checkbox" id="MHC" name="MHC"><tab>Execlude the MHC region. <span style="color: #004d99">Please check to EXCLUDE genes in MHC region.</span><br/>
  </div>
</div>
<div id="checkGenes"></div>
<div id="checkBkGenes"></div>
<input type="submit" value="Submit" class="btn" id="geneSubmit" name="geneSubmit" style="float: right;"/><br/><br/>
{!! Form::close() !!}

<br/>
<div id="loadingGeneQuery" style="text-align: center;"></div>
<div id="results">
  <div class="panel panel-default"><div class="panel-body">
    <a href='#expPanel' data-toggle="collapse" style="color: #00004d"><h3>Expression HeatMap</h3></a>
    <div id="expPanel" class="collapse">
      <div id="expHeat" style='overflow:auto; width:1010px; height:450px;'></div>
      <div id="expBox"></div>
      <br/>
    </div>
  </div></div>
  <div class="panel panel-default"><div class="panel-body">
    <a href='#tsEnrichBarPanel' data-toggle="collapse" style="color: #00004d;"><h3>Tissue specificity</h3></a>
    <div id="tsEnrichBarPanel" class="collapse">
      <h4>Differentially expressed genes among 53 tissues (GTEx)</h4>
      <input type="submit" class="btn" id="DEGdown" name="DEGdown" value="Download text file"><br/>
      <div id="tsEnrichBar"></div>
      <h4>Differrentially expressed genes among 30 general tissue types (GTEx)</h4>
      <input type="submit" class="btn" id="DEGgdown" name="DEGgdown" value="Download text file"><br/>
      <div id="tsGeneralEnrichBar"></div>
    </div>
  </div></div>

  <div class="panel panel-default"><div class="panel-body">
    <a href="#GeneSetPanel" data-toggle="collapse" style="color: #00004d"><h3>Gene Set Enrichment</h3></a>
    <div id="GeneSetPanel" class="collapse">
      <input type="submit" class="btn" id="GSdown" name="GSdown" value="Download text file"><br/><br/>
      <div id="GeneSet">
        <!-- <div class="row">
          <div class="col-md-6" id="Canonical_Pathways">
            <h4>Canonical Pathways</h4>
          </div>
          <div class="col-md-6" id="Canonical_PathwaysTable"></div>
        </div>
        <div class="row">
          <div class="col-md-6" id="GO_bp">
            <h4>GO biological process</h4>
          </div>
          <div class="col-md-6" id="GO_bpTable"></div>
        </div>
        <div class="row">
          <div class="col-md-6" id="GO_cc">
            <h4>GO cellular component</h4>
          </div>
          <div class="col-md-6" id="GO_ccTable"></div>
        </div>
        <div class="row">
          <div class="col-md-6" id="GO_mf">
            <h4>GO molecular function</h4>
          </div>
          <div class="col-md-6" id="GO_mfTable"></div>
        </div> -->
      </div>
    </div>
  </div></div>
</div>
@stop
