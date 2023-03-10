@extends('layouts.master')

@section('content')
	<div id="wrapper" class="active">
		<div id="sidebar-wrapper">
			<ul class="sidebar-nav" id="sidebar-menu">
				<li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
			</ul>
			<ul class="sidebar-nav" id="sidebar">
				<li class="active"><a href="#overview">Overview<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
				<li><a href="#quick-start">Quick Start<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
				<div class="subside" id="quick-startsub">
					<a href="#generalInfo">General Info</a>
					<a href="#getCandidate">Prioritize genes</a>
					<a href="#geneQuery">Gene functions</a>
				</div>
				<li><a href="#snp2gene">SNP2GENE<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
				<div class="subside" id="snp2genesub">
					<a href="#prepare-input-files">Input files</a>
					<a href="#parameters">Parameters</a>
					<a href="#outputs">Outputs</a>
					<a href="#table-columns">Table columns</a>
					<a href="#geneMap">Redo gene mapping</a>
					<a href="#refpanel">Reference panel</a>
					<a href="#annov">ANNOVAR enrichment</a>
					<a href="#magma">MAGMA</a>
					<a href="#riskloci">Risk loci and lead SNPs</a>
					<a href="#eQTLs">eQTLs</a>
					<a href="#chromatin-interactions">Chromatin interactions</a>
					<!-- <a href="#examples">Example senarios</a> -->
				</div>
				<li><a href="#gene2func">GENE2FUNC<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
				<div class="subside" id="gene2funcsub">
					<a href="#submit-genes">Submit genes</a>
					<a href="#g2fOutputs">Outputs</a>
				</div>
				<li><a href="#celltype">Cell type<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
				<div class="subside" id="celltypesub">
					<a href="#cell_submit">Submit jobs</a>
					<a href="#basemodel">Base model</a>
					<a href="#workflow">3-step workflow</a>
					<!-- <a href="#cell_outputs">Outputs</a> -->
					<a href="#datasets">scRNA data sets</a>
				</div>
				<li class="active"><a href="#publish">Publish results<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
				<li class="active"><a href="#data-security">Data security<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
			</ul>
		</div>

		<div id="page-content-wrapper">
			<div class="page-content inset">
				<div id="overview" class="sidePanel container" style="padding-top:50px;">
					<h3>General overview of <strong>FUMA<span style="color:#3498DB">GWAS</span></strong></h3>
					<div style="margin-left: 40px;">
						<p>The main purpose of FUMA is to use functional, biological information to prioritize genes based on GWAS outcomes.</p>
						<p>FUMA consists of two separate process; SNP2GENE and GENE2FUNC.</p>
						<p>To annotate and prioritize SNPs and genes from your GWAS summary statistics, go to <a href="{{ Config::get('app.subdir') }}/snp2gene"><strong>SNP2GENE</strong></a> which compute LD structure,
						annotates functions to SNPs, and prioritize candidate genes.</p>
						<p>You can then use the prioritized genes as input to <a href="{{ Config::get('app.subdir') }}/gene2func"><strong>GENE2FUNC</strong></a> to check expression patterns and shared molecular functions between genes.
							<strong>GENE2FUNC</strong> can also be used for any list of pre-selected genes (i.e. created outside of SNP2GENE).
						<p/>
						<br/>
						<img src="{{ URL::asset('/image/pipeline.png') }}" style="width: 80%;">
					</div>
				</div>

				@include('tutorial.quickstart')

				<div id="snp2gene" class="sidePanel container" style="padding-top:50;">
					<h2>SNP2GENE</h2>
					<div style="margin-left: 40px;">
						@include('tutorial.snp2gene.inputfiles')
						<br/>
						@include('tutorial.snp2gene.parameters')
						<br/>
						@include('tutorial.snp2gene.outputs')
						<br/>
						@include('tutorial.snp2gene.tables')
						<br/>
						@include('tutorial.snp2gene.geneMap')
						<br/>
						@include('tutorial.snp2gene.refpanel')
						<br/>
						@include('tutorial.snp2gene.annov')
						<br/>
						@include('tutorial.snp2gene.magma')
						<br/>
						@include('tutorial.snp2gene.riskloci')
						<br/>
						@include('tutorial.snp2gene.eqtl')
						<br/>
						@include('tutorial.snp2gene.ci')
					</div>
				</div>

				<div id="gene2func" class="sidePanel container" style="padding-top:50;">
					<h2>GENE2FUNC</h2>
					<p>The main goal of GENE2FUNC is to provide information on expression of prioritized genes and test for enrichment of the set of genes in pre-defined pathways.
						You can use the genes prioritized with SNP2GENE or use a separate list of genes.
					</p>
					<div style="padding-left: 40px;">
						@include('tutorial.gene2func.submit-genes')
						<br/>
						@include('tutorial.gene2func.outputs')
					</div>
				</div>
				<div id="celltype" class="sidePanel container" style="padding-top:50px;">
					<h2>Cell type specificity analyses with scRNA-seq</h2>
					<div style="margin-left: 40px;">
						@include('tutorial.celltype.submit')
						<br/>
						@include('tutorial.celltype.basemodel')
						<br/>
						@include('tutorial.celltype.workflow')
						<br/>
						<!-- @include('tutorial.celltype.output') -->
						<br/>
						@include('tutorial.celltype.datasets')
					</div>
				</div>

				@include('tutorial.publish')
				@include('tutorial.data-security')
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	{{-- Imports from the web --}}
	<script src='//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/latest.js?config=TeX-MML-AM_CHTML' async></script>
	
	{{-- Imports from the project --}}
	<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}"></script>
	<script type="text/javascript" src="{!! URL::asset('js/tutorial_utils.js') !!}"></script>

	{{-- Hand written ones --}}
	<script type="text/javascript">
		var page = "tutorial";
		var loggedin = "{{ Auth::check() }}";
		$(document).ready(function(){
			var hashid = window.location.hash;
			var side = [];
			$('.sidebar-nav li a').each(function(){
				side.push($(this).attr("href"));
			})
			if(hashid==""){
				$('a[href*="#overview"]').trigger('click');
			}else{
				if(side.indexOf(hashid)>=0){
					// $(hashid).show();
					$('a[href*="'+hashid+'"]').trigger('click');
				}else{
					$('.subside a').each(function(){
						if($(this).attr("href")==hashid){
							var parent = '#'+$(this).parent().attr("id").replace("sub", "");
							// $(parent).show();
							$('a[href*="'+parent+'"]').trigger('click');
							$(this).trigger('click');
						}
					});
				}
			}
		
			$('.inpage').on('click', function(){
				var hashid = $(this).attr('href');
				hashid = hashid.replace(/\/\w+#/, "#");
				var side = [];
				$('.sidebar-nav li a').each(function(){
					side.push($(this).attr("href"));
				})
				if(hashid==""){
					$('a[href*="#overview"]').trigger('click');
				}else{
					if(side.indexOf(hashid)>=0){
						// $(hashid).show();
						$('a[href*="'+hashid+'"]').trigger('click');
					}else{
						$('.subside a').each(function(){
							if($(this).attr("href")==hashid){
								var parent = '#'+$(this).parent().attr("id").replace("sub", "");
								// $(parent).show();
								$('a[href*="'+parent+'"]').trigger('click');
								$(this).trigger('click');
							}
						});
					}
				}
			})
		});
	</script>
	
@endsection