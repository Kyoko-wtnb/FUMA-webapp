<!-- genome wide plots -->
<div class="sidePanel container" style="padding-top:50px;" id="genomePlots">
	<!-- <h3>Genome Wide Plot</h3> -->
	<!-- <div id="gPlotPanel" class="collapse in"> -->
	<div class="container">
		<h4 style="color: #00004d">Manhattan Plot (GWAS summary statistics)</h4>
		<span class="info"><i class="fa fa-info"></i>
			Manhattan plot of the input GWAS summary statistics.<br/>
			For plotting, overlapping data points are not drawn (only SNPs with P-value &le; 1e-5 are kept, see tutorial for details).
		</span><br/><br/>
		Download the plot as
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("manhattan","png");'>PNG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("manhattan","jpeg");'>JPG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("manhattan","svg");'>SVG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("manhattan","pdf");'>PDF</button>

		<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
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
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("geneManhattan","png");'>PNG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("geneManhattan","jpeg");'>JPG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("geneManhattan","svg");'>SVG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("geneManhattan","pdf");'>PDF</button>

		<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
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
					For plotting purposes, overlapping data points are not drawn (Only SNPs with P-value &le; 1e-5 are kept, see tutorial for details).
				</span><br/><br/>
				Download the plot as
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("QQplot","png");'>PNG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("QQplot","jpeg");'>JPG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("QQplot","svg");'>SVG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("QQplot","pdf");'>PDF</button>

				<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
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
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("geneQQplot","png");'>PNG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("geneQQplot","jpeg");'>JPG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("geneQQplot","svg");'>SVG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("geneQQplot","pdf");'>PDF</button>

				<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
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
			MAGMA gene-set analysis is performed for curated gene sets and GO terms obtained from MsigDB (see <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#magma">here</a> for details).<br/>
			The table displays either significant gene sets with P<sub>bon</sub> < 0.05 or top 10 gene sets when there are less than 10 significant gene sets.
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
			MAGMA gene-property analysis is performed for gene expression of user selected data sets.<br/>
			Details of gene expression data sets are available at
			<a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#magma">Tutorial</a>.
			Full results are downloadable from "Download" tab. <br/>
			Note that MAGMA gene-property analyses uses the full distribution of SNP p-values
			and is different from a enrichment test of DEG (differentially expressed genes)
			as implemented in GENE2FUNC that only tests for enrichment of prioritised genes.
		</span><br/><br/>
		<div id="magmaPlot">
			<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dir" id="expDir" val=""/>
				<input type="hidden" name="id" id="expJobID" val=""/>
				<input type="hidden" name="data" id="expData" val=""/>
				<input type="hidden" name="type" id="expType" val=""/>
				<input type="hidden" name="fileName" id="expFileName" val=""/>
				<input type="submit" id="expSubmit" class="ImgDownSubmit"/>
			</form>
			<span class="form-inline">
				Order tissue by :
				<select id="magma_exp_order" class="form-control" style="width: auto;">
					<option value="alph">Alphabetical</option>
					<option value="p" selected>P-value</option>
				</select>
			</span>
		</div>
	</div>
</div>
