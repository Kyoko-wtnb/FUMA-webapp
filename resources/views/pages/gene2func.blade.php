@extends('layouts.master')
@section('head')
<?php
  header('X-Frame-Options: GOFORIT');
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
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
<script type="text/javascript" src="//d3js.org/queue.v1.min.js"></script>
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
  });
  var public_path = "{{ URL::asset('/image/ajax-loader2.gif') }}";
  var storage_path = "<?php echo storage_path();?>";
  var subdir = "{{ Config::get('app.subdir') }}";
  var jobdir = "{{ Config::get('app.jobdir') }}";
  var status = "{{$status}}";
  var jobID = "{{$id}}";
</script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/gene2func.js') !!}"></script>
@stop
@section('content')
<div id="wrapper" class="active">
  <div id="sidebar-wrapper">
    <ul class="sidebar-nav" id="sidebar-menu">
      <li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
    </ul>
    <ul class="sidebar-nav" id="sidebar">
      <li class="active"><a href="#newquery">New Query<i class="sub_icon fa fa-upload"></i></a></li>
      <li class="active"><a href="#queryhistory">Query History<i class="sub_icon fa fa-history"></i></a></li>
      <div id="resultSide">
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
      <!-- Submit genes -->
      <div id="newquery" class="sidePanel container" style="padding-top:50px;">
        {!! Form::open(array('url' => 'gene2func/submit', 'files'=>true, 'novalidate'=>'novalidate')) !!}
        <!-- <h3>Input list of genes</h3> -->
        <div class="row">
          <div class="col-md-6 col-xs-6 col-sm-6">
            <div class="panel panel-default">
              <div class="panel-body" style="padding-bottom: 0;">
                <h4>Genes of interest</h4>
                <p class="info"><i class="fa fa-info"></i> Paste or upload a file that contains gene-symbols.
                  Priority is given to the text box if both fields are used.
                </p>
                1. Paste genes
                <a class="infoPop" data-toggle="popover" data-content="Please paste one gene per line. ENSG ID, entrez ID or gene symbols are accepted.">
                  <i class="fa fa-question-circle-o fa-lg"></i>
                </a>
                <br/>
                <textarea id="genes" name="genes" rows="12" cols="50" placeholder="Please enter each gene per line here." onkeyup="checkInput()" oninput="checkInput()"></textarea><br/>
                <br/>
                2. Upload file
                <a class="infoPop" data-toggle="popover" data-content="The first column should be the genes without header. Extra columns will be ignored. ENSG ID, entrez ID or gene symbols are accepted.">
                  <i class="fa fa-question-circle-o fa-lg"></i>
                </a>
                <tab><input class="form-control-file" type="file" name="genesfile" id="genesfile" onchange="checkInput()"/>
                <br/>
                <div id="GeneCheck" style="padding-bottom: 0;"></div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-xs-6 col-sm-6">
            <div id="backgroundGenes"></div>
            <div class="panel panel-default">
              <div class="panel-body" style="padding-bottom: 0;">
                <h4>Background genes</h4>
                <p class="info"><i class="fa fa-info"></i>
                  Specify background gene-set. This will be used in the hypergeometric test.
                </p>
                1. Select background genes by gene-type <a id="bkgeneSelectClear">Clear</a><br/>
                <span class="info"><i class="fa fa-info"></i>
                  Multiple gene-types can be selected.
                </span>
                <tab><select class="form-control" multiple size="5" name="genetype[]" id="genetype" onchange="checkInput();">
                  <option value="all">All</option>
                  <option value="protein_coding">Protein coding</option>
                  <option value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA">lncRNA</option>
                  <option value="miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA">ncRNA</option>
                  <option value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA:miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA:processed_transcript">Processed transcripts</option>
                  <option value="pseudogene:processed_pseudogene:unprocessed_pseudogene:polymorphic_pseudogene:IG_C_pseudogene:IG_D_pseudogene:ID_V_pseudogene:IG_J_pseudogene:TR_C_pseudogene:TR_D_pseudogene:TR_V_pseudogene:TR_J_pseudogene">Pseudogene</option>
                  <option value="IG_C_gene:TG_D_gene:TG_V_gene:IG_J_gene">IG genes</option>
                  <option value="TR_C_gene:TR_D_gene:TR_V_gene:TR_J_gene">TR genes</option>
                </select>
                <br/>
                2. Paste custom list of backbround genes
                <a class="infoPop" data-toggle="popover" data-content="Please pasge gene per line. ENSG ID, entrez ID and gene symbol are acceptable.">
                  <i class="fa fa-question-circle-o fa-lg"></i>
                </a><br/>
                <textarea id="bkgenes" name="bkgenes" rows="5" cols="50" placeholder="Please enter each gene per line here." onkeyup="checkInput();" oninput="checkInput()"></textarea><br/>
                <br/>
                3. Upload a file with a custom list of background genes
                <a class="infoPop" data-toggle="popover" data-content="The first column shoud be the genes without header. Extra columns will be ignored. ENSG ID, entrez ID and gene symbol are acceptable.">
                  <i class="fa fa-question-circle-o fa-lg"></i>
                </a>
                <tab><input class="form-control-file" type="file" name="bkgenesfile" id="bkgenesfile" onchange="checkInput()"/>
                <br/>
                <div id="bkGeneCheck" style="padding-bottom: 0;"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body" style="padding:10;">
            <h4>Other optional parameters</h4>
            <!-- <tab><input type="checkbox" id="Xchr" name="Xchr">&nbsp;Execlude genes on X chromosome. <span style="color: #004d99">*Please check to EXCLUDE X chromosome.</span><br/> -->
            <tab><input type="checkbox" id="MHC" name="MHC">&nbsp;Exclude the MHC region.<br/>
            <!-- <span class="info"><i class="fa fa-info"></i> Please check to EXCLUDE genes in MHC region.</span><br/> -->
            <span class="form-inline">
              <tab>Desired multiple test correction method for gene-set enrichment testing:
                <select class="form-control" id="adjPmeth" name="adjPmeth" style="width:auto;">
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
            </span>
            <span class="form-inline">
              <tab>Adjusted P-value for gene set association (&lt;): <input class="form-control" type="number" id="adjPcut" name="adjPcut" value="0.05"/>
              <a class="infoPop" data-toggle="popover" title="Adjusted P-value cutoff" data-content="Only gene sets significantly enriched at given adjusted P-value threshold will be reported.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
              <br/>
            </span>
            <span class="form-inline">
              <tab>Minimum overlapping genes with gene-sets (&ge;): <input class="form-control" type="number" id="minOverlap" name="minOverlap" value="2"/>
              <a class="infoPop" data-toggle="popover" title="Minimum overlapping genes with gene sets" data-content="Only gene sets which overlapping with more than or equal to the given number of genes in the input genes will be reported.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
              </br>
            </span>
            <span class="form-inline">
              <tab>Title: <input type="text" class="form-control" id="title" name="title">
              <span class="info"><i class="fa fa-info"></i> Optional</span>
            </span>
          </div>
        </div>

        <div id="checkGenes"></div>
        <div id="checkBkGenes"></div>
        <input type="submit" value="Submit" class="btn" id="geneSubmit" name="geneSubmit"/><br/><br/>
        {!! Form::close() !!}
      </div>

      <div id="queryhistory" class="sidePanel container" style="padding-top:50px;">
        <div class="panel panel-default">
          <div class="panel-heading">
            Gene query history
          </div>
          <div class="panel-body">
            <button class="btn btn-sm" id="deleteJob" name="deleteJob" style="float:right; margin-right:20px;">Delete selected jobs</button>
            <table class="table">
              <thead>
                <tr>
                  <th>Job ID</th>
                  <th>Title</th>
                  <th>SNP2GENE job ID</th>
                  <th>SNP2GENE title</th>
                  <th>Submit date</th>
                  <th>Link</td>
                  <th>Select</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="7" style="Text-align:center;">Retrieving data</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="results">
        <!-- Expression heatmap -->
        <div id="expPanel" class="sidePanel container" style="padding-top:50px;">
          <!-- <div id="expHeat" style='overflow:auto; width:1010px; height:450px;'></div> -->
          <h4>Gene expression heatmap of 53 specific tissue types (GTEx)</h4>
          <sapn class="form-inline">
            Expression Value:
          	<select id="expval" class="form-control" style="width: auto;">
          		<option value="log2RPKM" selected>Average RPKM per tissue (log2 transformed)</option>
          		<option value="norm">Average of normalized RPKM per tissue (zero mean across tissues)</option>
          	</select>
            <a class="infoPop" data-toggle="popover" title="Expression value" data-html="true" data-content="<b>Average RPKM per tissue</b>:
              Average expression value (after following log2 transformed RPKM per tissue per gene, winsorization at 50).
              Darker red means higher expression of that gene, compared to a darker blue color.<br/>
              <b>Average normalized RPKM per tissue</b>:
              Average value of the <u>relatively</u> expression value (after following RPKM log2 transformation, and zero mean normalization)
              Darker red means higher relative expression of that gene in tissue X, compared to a darker blue color in the same tissue.<br/>
              <b>RPKM = Reads Per Killobase per Million</b>">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </span>
          <br/>
          <span class="form-inline">
          	Order genes by:
          	<select id="geneSort" class="form-control" style="width: auto;">
          		<option value="clst">Cluster</option>
          		<option value="alph" selected>Alphabetical order</option>
          	</select>
          	<tab>
          	Order tissues by:
          	<select id="tsSort" class="form-control" style="width: auto;">
          		<option value="clst">Cluster</option>
          		<option value="alph" selected>Alphabetical order</option>
          	</select>
          </span><br/><br/>
          Download the plot as
          <button class="btn btn-xs ImgDown" onclick='ImgDown("expHeat","png");'>PNG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("expHeat","jpeg");'>JPG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("expHeat","svg");'>SVG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("expHeat","pdf");'>PDF</button>

          <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/gene2func/imgdown">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dir" id="expHeatDir" val=""/>
            <input type="hidden" name="id" id="expHeatJobID" val=""/>
            <input type="hidden" name="data" id="expHeatData" val=""/>
            <input type="hidden" name="type" id="expHeatType" val=""/>
            <input type="hidden" name="fileName" id="expHeatFileName" val=""/>
            <input type="submit" id="expHeatSubmit" class="ImgDownSubmit"/>
          </form>
        	<div id="expHeat"></div>
          <div id="expBox"></div>
          <br/>
        </div>
        <!-- Tissue specificity bar chart -->
        <div id="tsEnrichBarPanel"  class="sidePanel container" style="padding-top:50px;">
          <h4>Differrentially expressed genes across 30 general tissue types (GTEx)
            <a class="infoPop" data-toggle="popover" title="DEG of 30 general tissue types" data-content="Pre-calculated sets of differentially expressed genes (DEG) were created for the 30 general GTEx tissue types.
            DEG sets are defined by a two-sided t-tests per tissue versus all remaining, and Bonferroni correction.
            Genes with a adjusted p-value ≤ 0.05 and absolute log fold change ≥ 0.58 are selected.
            For the signed DEG, the direction of expression was taken intoc account (i.e. a up-regulated DEG set contains all genes that are significantly overexpressed in that tissue compared to other tissues).
            The -log10(P values) in the graph refer to the probability of the hypergeomteric test.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </h4>
          <span class="info"><i class="fa fa-info"></i>
            Significantly enriched DEG sets (FDR at 0.05) are highlighted in red.
          </span><br/><br/>
          Download the plot as
          <button class="btn btn-xs ImgDown" onclick='ImgDown("tsGeneralEnrichBar","png");'>PNG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("tsGeneralEnrichBar","jpeg");'>JPG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("tsGeneralEnrichBar","svg");'>SVG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("tsGeneralEnrichBar","pdf");'>PDF</button>

          <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/gene2func/imgdown">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dir" id="tsGeneralEnrichBarDir" val=""/>
            <input type="hidden" name="id" id="tsGeneralEnrichBarJobID" val=""/>
            <input type="hidden" name="data" id="tsGeneralEnrichBarData" val=""/>
            <input type="hidden" name="type" id="tsGeneralEnrichBarType" val=""/>
            <input type="hidden" name="fileName" id="tsGeneralEnrichBarFileName" val=""/>
            <input type="submit" id="tsGeneralEnrichBarSubmit" class="ImgDownSubmit"/>
          </form>
          <br/>
          <form class="form-inline" action="fileDown" method="post" target="_blank">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" value="{{$id}}"/>
            <input type="hidden" name="file" value="DEGgeneral.txt"/>
            <input type="submit" class="btn btn-xs" id="DEGgdown" name="DEGgdown" value="Download text file">
          </form>
          <div id="tsGeneralEnrichBar"></div>
          <br/><br/>
          <h4>Differentially expressed genes across 53 specific tissue types (GTEx)
            <a class="infoPop" data-toggle="popover" title="DEG of 53 tissue types" data-content="The same method as mentioned above (for 30 general tissue types) was applied, except DEG sets were created for 53 specific tissue types.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </h4>
          <span class="info"><i class="fa fa-info"></i>
            DEG sets with significant enrichment of input genes (FDR at 0.05) are highlighted in red.
          </span><br/><br/>
          Download the plot as
          <button class="btn btn-xs ImgDown" onclick='ImgDown("tsEnrichBar","png");'>PNG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("tsEnrichBar","jpeg");'>JPG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("tsEnrichBar","svg");'>SVG</button>
          <button class="btn btn-xs ImgDown" onclick='ImgDown("tsEnrichBar","pdf");'>PDF</button>

          <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/gene2func/imgdown">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dir" id="tsEnrichBarDir" val=""/>
            <input type="hidden" name="id" id="tsEnrichBarJobID" val=""/>
            <input type="hidden" name="data" id="tsEnrichBarData" val=""/>
            <input type="hidden" name="type" id="tsEnrichBarType" val=""/>
            <input type="hidden" name="fileName" id="tsEnrichBarFileName" val=""/>
            <input type="submit" id="tsEnrichBarSubmit" class="ImgDownSubmit"/>
          </form>
          <br/>
          <form action="fileDown" method="post" target="_blank">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" value="{{$id}}"/>
            <input type="hidden" name="file" value="DEG.txt"/>
            <input type="submit" class="btn btn-xs" id="DEGdown" name="DEGdown" value="Download text file">
          </form>
          <div id="tsEnrichBar"></div>
        </div>
        <!-- GeneSet enrichment -->
        <div id="GeneSetPanel"  class="sidePanel container" style="padding-top:50px;">
          <h4>Enrichment of input genes in Gene Sets</h4>
          <form action="fileDown" method="post" target="_blank">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" value="{{$id}}"/>
            <input type="hidden" name="file" value="GS.txt"/>
            <input type="submit" class="btn btn-xs" id="GSdown" name="GSdown" value="Download text file">
          </form>
          <br/><br/>
          <form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/gene2func/imgdown">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dir" id="GSDir" val=""/>
            <input type="hidden" name="id" id="GSJobID" val=""/>
            <input type="hidden" name="data" id="GSData" val=""/>
            <input type="hidden" name="type" id="GSType" val=""/>
            <input type="hidden" name="fileName" id="GSFileName" val=""/>
            <input type="submit" id="GSSubmit" class="ImgDownSubmit"/>
          </form>
          <div id="GeneSet">
          </div>
        </div>
        <!-- Gene Table -->
        <div id="GeneTablePanel" class="sidePanel container" style="padding-top:50px;">
          <h4>Links to external databases</h4>
          <!-- <p class="info"><i class="fa fa-info"></i> Links to OMIM and DrugBank will open in a new tab.
          <br/><i class="fa fa-info"></i> Links to GeneCards will be displayed in the frame below.</p> -->
          <table id="GeneTable" class="display dt-body-center compact" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
            <thead>
              <tr>
                <th>ENSG</th><th>entrezID</th><th>symbol</th><th>OMIM</th><th>UniProtID</th><th>DrugBank</th><th>GeneCards</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>

          <br/>
          <!-- <h4>GeneCards</h4><br/>
          <iframe src="http://www.genecards.org/" name="GeneCards_iframe" id="GeneCards_iframe" width="100%" height="500p"></iframe>
          <br/><br/> -->
        </div>
      </div>
    </div>
  </div>
</div>
@stop
