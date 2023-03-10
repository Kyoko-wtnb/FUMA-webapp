<div id="result" class="sidePanel container" style="padding-top:50px;">
	<div class="panel panel-default">
	    <div class="panel-heading">
	        <div class="panel-title">Download MAGMA results</div>
	    </div>
	    <div class="panel-body">
			<form action="{{ Config::get('app.subdir') }}/{{$page}}/filedown" method="post" target="_blank">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="id" value="{{$id}}"/>
				<input type="hidden" name="prefix" value="{{$prefix}}"/>
				<div id="downFileCheck">
					<input checked class="form-check-input" type="checkbox" value="step1" name="files[]" id="step1_file" onchange="DownloadFiles()"> Per dataset MAGMA output (Step 1)<br/>
					<input checked class="form-check-input" type="checkbox" value="step2" name="files[]" id="step2_file" onchange="DownloadFiles()"> Full results of per dataset conditional analyses (Step 2)<br/>
					<input checked class="form-check-input" type="checkbox" value="step1_2" name="files[]" id="step1_2_file" onchange="DownloadFiles()"> Summary of step 1 and 2<br/>
					<input checked class="form-check-input" type="checkbox" value="step3" name="files[]" id="step3_file" onchange="DownloadFiles()"> Full results of cross-datasets conditional analyses (Step 3)<br/>
				</div>
				<br/>
				<span class="form-inline">
					<input class="btn btn-default btn-xs" type="submit" name="download" id="download" value="Download files"/>
					<tab><a class="allfiles"> Select All </a>
					<tab><a class="clearfiles"> Clear</a>
				</span><br/>
			</form>
		</div>
	</div>
	<div id="cellPlotPanel">
		<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="dir" id="celltypeDir" val=""/>
			<input type="hidden" name="id" id="celltypeID" val="{{$id}}"/>
			<input type="hidden" name="data" id="celltypeData" val=""/>
			<input type="hidden" name="type" id="celltypeType" val=""/>
			<input type="hidden" name="fileName" id="celltypeFileName" val=""/>
			<input type="submit" id="celltypeSubmit" class="ImgDownSubmit"/>
		</form>

		<div class="panel panel-default">
		    <div class="panel-heading">
		        <div class="panel-title">Per-dataset cell type specificity</div>
		    </div>
		    <div class="panel-body">
				Download the plot as
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDownDS("perDatasetPlot","png");'>PNG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDownDS("perDatasetPlot","jpeg");'>JPG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDownDS("perDatasetPlot","svg");'>SVG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDownDS("perDatasetPlot","pdf");'>PDF</button>
				<br/><br/>
				<span class="form-inline">
					Dataset :
					<select id="dataset_select" class="form-control" style="width: auto;" onchange="updatePerDatasetPlot();">
					</select>
				</span>
				<br/>
				<span class="form-inline">
					Multiple testing correction :
					<select id="test_correct_panel1" class="form-control" style="width: auto;">
						<option value="pd" selected>Per dataset</option>
						<option value="ad">Across datasets</option>
						<option value="both">Both</option>
					</select>
				</span>
				<br/>
				<span class="form-inline">
					Order cell type by :
					<select id="celltype_order_panel1" class="form-control" style="width: auto;">
						<option value="alph">Alphabetical</option>
						<option value="p" selected>P-value</option>
					</select>
				</span>
				<div id="perDatasetPlot" style="overflow-x: auto;"></div>
			</div>
		</div>

		<div class="panel panel-default">
		    <div class="panel-heading">
		        <div class="panel-title">Significant cell types across datasets (Step 1)</div>
		    </div>
		    <div class="panel-body">
				<span class="info"><i class="fa fa-info"></i>
					The plot is only displayed when there is at least one significant cell type after
					multiple testing correction across datasets.
					Bars are colored by dataset.
				</span>
				<br/>
				Download the plot as
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step1Plot", "step1", "png");'>PNG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step1Plot", "step1","jpeg");'>JPG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step1Plot", "step1","svg");'>SVG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step1Plot", "step1","pdf");'>PDF</button>
				<br/><br/>
				<span class="form-inline">
					Order cell type by :
					<select id="celltype_order_panel2" class="form-control" style="width: auto;">
						<option value="dp" selected>P-value per dataset</option>
						<option value="p">P-value across dataset</option>
					</select>
				</span>
				<div id="step1Plot" style="overflow-x: auto;"></div>
			</div>
		</div>

		<div class="panel panel-default">
		    <div class="panel-heading">
		        <div class="panel-title">Independent cell type associations based on within-dataset conditional analyses (Step 2)</div>
		    </div>
		    <div class="panel-body">
				<span class="info"><i class="fa fa-info"></i>
					The plot is only displayed when there is at least one significant cell type after
					multiple testing correction across datasets.
					Bars are colored by dataset.
				</span>
				<br/>
				Download the plot as
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step2Plot", "step2","png");'>PNG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step2Plot", "step2","jpeg");'>JPG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step2Plot", "step2","svg");'>SVG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step2Plot", "step2","pdf");'>PDF</button>
				<br/><br/>
				<span class="form-inline">
					Order cell type by :
					<select id="celltype_order_panel3" class="form-control" style="width: auto;">
						<option value="dp" selected>P-value per dataset</option>
						<option value="p">P-value across dataset</option>
					</select>
				</span>
				<div id="step2Plot" style="overflow-x: auto;"></div>
			</div>
		</div>

		<div class="panel panel-default">
		    <div class="panel-heading">
		        <div class="panel-title">Pair-wise cross-datasets conditional analyses (Step 3)</div>
		    </div>
		    <div class="panel-body">
				<span class="info"><i class="fa fa-info"></i>
					The plot is only displayed when there is at least one significant cell type after
					multiple testing correction across datasets.
					The heatmap is asymmetric; a cell on row i and column j is cross-datasets (CD) proportional significance (PS)
					of cell type j conditioning on cell type i.
					Each cell is colored by PS with upper limit 1, where PS>1 is represented by double stars.
					Star represents pair of cell types which are collinear.
					Top bar plot represent marginal P-value of the cell type on X-axis.
				</span>
				<br/>
				Download the plot as
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step3Plot", "step3","png");'>PNG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step3Plot", "step3","jpeg");'>JPG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step3Plot", "step3","svg");'>SVG</button>
				<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("step3Plot", "step3","pdf");'>PDF</button>
				<br/><br/>
				<span class="form-inline">
					Order cell type by :
					<select id="celltype_order_panel4" class="form-control" style="width: auto;">
						<option value="dp" selected>P-value per dataset</option>
						<option value="p">P-value across dataset</option>
					</select>
				</span>
				<div id="step3Plot" style="overflow-x: auto;"></div>
			</div>
		</div>

	</div>
</div>
