@extends('layouts.master')
<?php
  header('X-Frame-Options: GOFORIT');
?>
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
<script type="text/javascript" src="https://d3js.org/queue.v1.min.js"></script>

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
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/gene2func.js') !!}"></script>

@section('content')
<div id="wrapper" class="active">
<div id="sidebar-wrapper">
  <ul class="sidebar-nav" id="sidebar-menu">
    <li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
  </ul>
  <ul class="sidebar-nav" id="sidebar">
    <li class="active"><a href="#newquery">New Query<i class="sub_icon fa fa-upload"></i></a></li>
    <div id="resultSide">
      <li><a href="#expPanel">Heatmap<i class="sub_icon fa fa-th"></i></a></li>
      <li><a href="#tsEnrichBarPanel">Tissue sepcificity<i class="sub_icon fa fa-bar-chart"></i></a></li>
      <li><a href="#GeneSetPanel">Gene sets<i class="sub_icon fa fa-bar-chart"></i></a></li>
      <li><a href="#GeneTablePanel">Gene table<i class="sub_icon fa fa-table"></i></a></li>
    </div>
  </ul>
</div>

<div id="page-content-wrapper" class="container">
  <div class="page-content inset">
  <div id="test"></div>
  <div id="newquery" class="sidePanel" style="padding-top:50;">
    {!! Form::open(array('url' => 'gene2func/submit', 'files'=>true)) !!}
    <!-- <h3>Input list of genes</h3> -->
    <div class="row">
      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-heading"><h4>Genes of interest</h4></div>
          <div class="panel-body" style="padding-bottom: 0;">
            <p style="color: #004d99">Please either paste or upload a file of genes to test.
              When both are provided, only genes pasted in the text box will be used.
            </p>
            1. Paste genes<br/>
            <textarea id="genes" name="genes" rows="12" cols="50" placeholder="Please enter each gene per line here." onkeyup="checkInput()" oninput="checkInput()"></textarea><br/>
            <br/>
            2. Upload file
            <tab><input type="file" name="genesfile" id="genesfile" onchange="checkInput()"/>
            <tab>*The first column shoud be the genes without header. Extra columns will be ignored.
            <br/><br/>
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
            <tab>*The first column shoud be the genes without header. Extra columns will be ignored.
            <br/><br/>
            <div id="bkGeneCheck" style="padding-bottom: 0;"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-body" style="padding:10;">
        <!-- <tab><input type="checkbox" id="Xchr" name="Xchr">&nbsp;Execlude genes on X chromosome. <span style="color: #004d99">*Please check to EXCLUDE X chromosome.</span><br/> -->
        <tab><input type="checkbox" id="MHC" name="MHC">&nbsp;Execlude the MHC region. <span style="color: #004d99">*Please check to EXCLUDE genes in MHC region.</span><br/>
        <tab>Multiple test correction method:
          <select id="adjPmeth" name="adjPmeth">
            <option value="bonferroni">Bonferroni</option>
            <option value="sidak">Sidak</option>
            <option value="holm-sidak">Holm-Sidak</option>
            <option value="holm">Holm</option>
            <option value="simes-hochberg">Simes-Hochberg</option>
            <option value="hommel">Hommel</option>
            <option selected value="fdr_bh">Benjamini-Hochberg (FDR)</option>
            <option value="fdr_by">Benjamini-Yekutieli (FDR)</option>
            <option value="fdr_tsbh">two-step Benjamini-Hochberg (FDR)</option>
            <option value="fdr_tsbky">two-step Benjamini-Krieger-Yekuteieli (FDR)</option>
          </select><br/>
        <tab>&nbsp;<span style="color: #004d99">*Options are available from python module <code>statsmodels.sandbox.stats.multicomp.multipletests</code>.</span><br/>
        <tab>Adjusted P-value cutoff (&lt;): <input type="number" id="adjPcut" name="adjPcut" value="0.05"/><br/>
        <tab>Minimum overlapped genes (&ge;): <input type="number" id="minOverlap" name="minOverlap" value="2"/></br>
      </div>
    </div>

    <div id="checkGenes"></div>
    <div id="checkBkGenes"></div>
    <input type="submit" value="Submit" class="btn" id="geneSubmit" name="geneSubmit" style="float: right;"/><br/><br/>
    {!! Form::close() !!}
  </div>

  <div id="loadingGeneQuery" style="text-align: center;"></div>
  <div id="results">
      <!-- <div class="panel panel-default"><div class="panel-body">
        <a href='#expPanel' data-toggle="collapse" style="color: #00004d"><h3>Expression HeatMap</h3></a> -->
      <div id="expPanel" class="sidePanel container" style="padding-top:50;">
        <!-- <div id="expHeat" style='overflow:auto; width:1010px; height:450px;'></div> -->
        <h4>Gene expression heatmap in 53 tissues (GTEx)</h4>
        Expression Value:
      	<select id="expval" class="from-control">
      		<option value="log2RPKM" selected>log2(RPKM+1)</option>
      		<option value="norm">Normalized across samples</option>
      	</select>
      	<tab>
      	Order genes by:
      	<select id="geneSort" class="form-control">
      		<option value="clst">Clusster</option>
      		<option value="alph" selected>Alphabetical order</option>
      	</select>
      	<tab>
      	Order tissues by:
      	<select id="tsSort" class="form-control">
      		<option value="clst">Clusster</option>
      		<option value="alph" selected>Alphabetical order</option>
      	</select>
      	<div id="expHeat"></div>
        <div id="expBox"></div>
        <br/>
      </div>
    <!-- </div></div> -->
    <!-- <div class="panel panel-default"><div class="panel-body">
      <a href='#tsEnrichBarPanel' data-toggle="collapse" style="color: #00004d;"><h3>Tissue specificity</h3></a> -->
      <div id="tsEnrichBarPanel"  class="sidePanel container" style="padding-top:50;">
        <h4>Differentially expressed genes across 53 tissues (GTEx)</h4>
        <!-- <button class="btn" id="DEGdown" name="DEGdown">Download text file</button><br/> -->
        <form action="fileDown" method="post" target="_blank">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="id" value="{{$id}}"/>
          <input type="hidden" name="file" value="DEG.txt"/>
          <input type="submit" class="btn" id="DEGdown" name="DEGdown" value="Download text file"><br/>
        </form>
        <div id="tsEnrichBar"></div>
        <h4>Differrentially expressed genes across 30 general tissue types (GTEx)</h4>
        <!-- <button class="btn" id="DEGgdown" name="DEGgdown">Download text file</button><br/> -->
        <form action="fileDown" method="post" target="_blank">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="id" value="{{$id}}"/>
          <input type="hidden" name="file" value="DEGgeneral.txt"/>
          <input type="submit" class="btn" id="DEGgdown" name="DEGgdown" value="Download text file"><br/>
        </form>
        <div id="tsGeneralEnrichBar"></div>
      </div>
    <!-- </div></div> -->

    <!-- <div class="panel panel-default"><div class="panel-body">
      <a href="#GeneSetPanel" data-toggle="collapse" style="color: #00004d"><h3>Gene Set Enrichment</h3></a> -->
      <div id="GeneSetPanel"  class="sidePanel container" style="padding-top:50;">
        <!-- <button class="btn" id="GSdown" name="GSdown">Download text file</button><br/><br/> -->
        <form action="fileDown" method="post" target="_blank">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="id" value="{{$id}}"/>
          <input type="hidden" name="file" value="GS.txt"/>
          <input type="submit" class="btn" id="GSdown" name="GSdown" value="Download text file"><br/>
        </form>
        <div id="GeneSet">
        </div>
      </div>
    <!-- </div></div> -->

    <div id="GeneTablePanel" class="sidePanel container" style="padding-top:50;">
      <h4>Input genes</h4>
      <table id="GeneTable" class="display dt-body-center compact" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
        <thead>
          <tr>
            <th>ENSG</th><th>entrezID</th><th>symbol</th><th>OMIM</th><th>UniProtID</th><th>DrugBank</th><th>GeneCard</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <p>*Links of OMIM nad DrugBank will open new tab due to the security reason.
      <br/>*Links of GeneCards will be displayed in the frame below.</p>

      <!-- <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#GeneCardsPane" aria-controls="GeneCardsPane" rolw="tab" data-toggle="tab">GeneCards</a></li>
        <li role="presentation"><a href="#OMIMPane" aria-controls="OMIMPane" rolw="tab" data-toggle="tab">OMIM</a></li>
        <li role="presentation"><a href="#DrugBankPane" aria-controls="DrugBankPane" rolw="tab" data-toggle="tab">DrugBank</a></li>
      </ul> -->
      <!-- <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="GeneCardsPane"> -->
          <h4>GeneCards</h4><br/>
          <iframe src="http://www.genecards.org/" name="GeneCards_iframe" width="100%" height="500p"></iframe>
        <!-- </div>
        <div role="tabpanel" class="tab-pane" id="OMIMPane">
          <iframe src="http://www.omim.org/" name="OMIM_iframe" width="100%" height="500p"></iframe>
        </div>
        <div role="tabpanel" class="tab-pane" id="DrugBankPane">
          <iframe src="http://www.drugbank.ca/" name="DrugBank_iframe" width="100%" height="500p"></iframe>
        </div>
      </div> -->
      <br/><br/>
    </div>

  </div>
</div>
</div>
@stop
