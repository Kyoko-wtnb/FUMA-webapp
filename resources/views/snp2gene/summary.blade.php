<!-- Summary panel -->
<div class="sidePanel container" style="padding-top:50px;" id="summaryTable">
	<div class="row">
		<div class="col-md-5 col-xs-5 col-sm-5" id="sumTable" style="text-align:center;">
			<h4 style="color: #00004d">Summary of SNPs and mapped genes</h4>
		</div>

		<div class="col-md-7 col-xs-7 col-sm-7" style="text-align:center;">
			<h4><span style="color: #00004d">Functional consequences of SNPs on genes</span>
			<a class="infoPop" data-toggle="popover" data-content="The histogram displays the proportion of SNPs (all SNPs in LD of Ind. sig. SNPs)
				which have corresponding functional annotation assigned by ANNOVAR.
				Bars are colored by log2(enrichment) relative to all SNPs in the selected reference panel.
				See tutorial for more details.">
				<i class="fa fa-question-circle-o fa-lg"></i>
			</a>
			<span class="into"><i class="fa fa-info"></i>
				Statistics are available in "annov.stats.txt".
				The file is downloadable from the "Download" tab.
			</span>
			</h4>
			Download the plot as
			<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("snpAnnotPlot","png");'>PNG</button>
			<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("snpAnnotPlot","jpeg");'>JPG</button>
			<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("snpAnnotPlot","svg");'>SVG</button>
			<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("snpAnnotPlot","pdf");'>PDF</button>

			<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dir" id="snpAnnotPlotDir" val=""/>
				<input type="hidden" name="id" id="snpAnnotPlotID" val=""/>
				<input type="hidden" name="data" id="snpAnnotPlotData" val=""/>
				<input type="hidden" name="type" id="snpAnnotPlotType" val=""/>
				<input type="hidden" name="fileName" id="snpAnnotPlotFileName" val=""/>
				<input type="submit" id="snpAnnotPlotSubmit" class="ImgDownSubmit"/>
			</form>
			<div id="snpAnnotPlot"></div>
			<!-- <canvas id="snpAnnotPlotCanvas" style="display: none;"></canvas> -->
		</div>
	</div>
	<br/>
	<div style="text-align:center;">
		<h4><span style="color: #00004d">Summary per genomic risk locus</span>
		<a class="infoPop" data-toggle="popover" data-content="The histograms display summary results per genomic locus. Note that genomic loci could contain more than one independent lead SNPs.">
			<i class="fa fa-question-circle-o fa-lg"></i>
		</a>
		</h4>
		Download the plot as
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("lociPlot","png");'>PNG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("lociPlot","jpeg");'>JPG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("lociPlot","svg");'>SVG</button>
		<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("lociPlot","pdf");'>PDF</button>

		<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="dir" id="lociPlotDir" val=""/>
			<input type="hidden" name="id" id="lociPlotID" val=""/>
			<input type="hidden" name="data" id="lociPlotData" val=""/>
			<input type="hidden" name="type" id="lociPlotType" val=""/>
			<input type="hidden" name="fileName" id="lociPlotFileName" val=""/>
			<input type="submit" id="lociPlotSubmit" class="ImgDownSubmit"/>
		</form>
		<div id="lociPlot"></div>
	</div>
	<br/><br/>
</div>
