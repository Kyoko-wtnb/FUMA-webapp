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
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}?135">
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
var id = "{{$id}}";
var page = "{{$page}}";
var loggedin = "{{ Auth::check() }}";
</script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}?131"></script>
<script type="text/javascript" src="{!! URL::asset('js/g2f_results.js') !!}?135e"></script>
<script type="text/javascript" src="{!! URL::asset('js/gene2func.js') !!}?135"></script>
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
								<a class="infoPop" data-toggle="popover" data-content="Please paste gene per line. ENSG ID, entrez ID and gene symbol are acceptable.">
									<i class="fa fa-question-circle-o fa-lg"></i>
								</a><br/>
								<textarea id="bkgenes" name="bkgenes" rows="5" cols="50" placeholder="Please enter each gene per line here." onkeyup="checkInput();" oninput="checkInput()"></textarea><br/>
								<br/>
								3. Upload a file with a custom list of background genes
								<a class="infoPop" data-toggle="popover" data-content="The first column should be the genes without header. Extra columns will be ignored. ENSG ID, entrez ID and gene symbol are acceptable.">
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
						<tab>
						<span class="form-inline">
							Ensembl version:
							<select class="form-control" id="ensembl" name="ensembl">
								<option selected value="v92">v92</option>
								<!-- REMOVED: no longer supported by biomart option value="v85">v85</option-->
							</select>
						</span><br/>
						<tab>
						Custom gene set files:
						<button type="button" class="btn btn-default btn-xs" id="gsFileAdd">add file</button>
						<input type="hidden" value="0" id="gsFileN" name="gsFileN">
						<span class="info"><i class="fa fa-info"></i>File is required to have GMT format with an extension ".gmt".</span>
						<br/>
						<span id="gsFiles"></span><br/>
						<tab>
						<span class="form-inline">
							Gene expression data sets:
							<select multiple class="form-control" name="gene_exp[]" id="gene_exp">
								<option selected value="GTEx/v8/gtex_v8_ts_avg_log2TPM">GTEx v8: 54 tissue types</option>
								<option selected value="GTEx/v8/gtex_v8_ts_general_avg_log2TPM">GTEx v8: 30 general tissue types</option>
								<option value="GTEx/v7/gtex_v7_ts_avg_log2TPM">GTEx v7: 53 tissue types</option>
								<option value="GTEx/v7/gtex_v7_ts_general_avg_log2TPM">GTEx v7: 30 general tissue types</option>
								<option value="GTEx/v6/gtex_v6_ts_avg_log2RPKM">GTEx v6: 53 tissue types</option>
								<option value="GTEx/v6/gtex_v6_ts_general_avg_log2RPKM">GTEx v6: 30 general tissue types</option>
								<option value="BrainSpan/bs_age_avg_log2RPKM">BrainSpan: 29 different ages of brain samples</option>
								<option value="BrainSpan/bs_dev_avg_log2RPKM">BrainSpan: 11 general developmental stages of brain samples</option>
							</select>
						</span><br/>
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
							<tab>Maximum adjusted P-value for gene set association (&lt;): <input class="form-control" type="number" id="adjPcut" name="adjPcut" value="0.05"/>
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
				<input type="submit" value="Submit" class="btn btn-primary" id="geneSubmit" name="geneSubmit"/><br/><br/>
				{!! Form::close() !!}
			</div>

			<!-- job list -->
			<div id="queryhistory" class="sidePanel container" style="padding-top:50px;">
				<div class="panel panel-default">
					<div class="panel-heading">
						Gene query history
					</div>
					<div class="panel-body">
						<button class="btn btn-default btn-sm" id="deleteJob" name="deleteJob" style="float:right; margin-right:20px;">Delete selected jobs</button>
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

			<!-- results panel -->
			<div id="results">
				@include('gene2func.summary')
				@include('gene2func.exp_heat')
				@include('gene2func.DEG')
				@include('gene2func.genesets')
				@include('gene2func.geneTable')
			</div>
		</div>
	</div>
</div>
@stop
