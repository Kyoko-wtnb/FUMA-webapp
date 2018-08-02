<!-- Expression heatmap -->
<div id="expPanel" class="sidePanel container" style="padding-top:50px;">
	<!-- <div id="expHeat" style='overflow:auto; width:1010px; height:450px;'></div> -->
	<h4>Gene expression heatmap</h4>
	<span class="form-inline">
		Data set:
		<select id="gene_exp_data" class="form-control" style="width: auto;">
		</select>
	</span><br/><br/>
	<span class="form-inline">
		Expression Value:
		<select id="expval" class="form-control" style="width: auto;">
			<option value="log2" selected>Average expression per label (log2 transformed)</option>
			<option value="norm">Average of normalized expression per label (zero mean across samples)</option>
		</select>
		<a class="infoPop" data-toggle="popover" title="Expression value" data-html="true" data-content="
			<b>Average expression per label</b>:
			This is an average of log2 transformed expression value (e.g. RPKM and TPM) per label (e.g. tissue type or developmental stage).
			RPKM and TPM were wisolized at 50.
			Darker red means higher expression of that gene, compared to a darker blue color.<br/>
			<b>Average of normalized expression per label</b>:
			Average value of the <u>relative</u> expression value (zero mean normalization of log2 transformed expression).
			Darker red means higher relative expression of that gene in label X, compared to a darker blue color in the same label.<br/>
			">
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
	<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("expHeat","png");'>PNG</button>
	<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("expHeat","jpeg");'>JPG</button>
	<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("expHeat","svg");'>SVG</button>
	<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("expHeat","pdf");'>PDF</button>

	<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="dir" id="expHeatDir" val=""/>
		<input type="hidden" name="id" id="expHeatID" val=""/>
		<input type="hidden" name="data" id="expHeatData" val=""/>
		<input type="hidden" name="type" id="expHeatType" val=""/>
		<input type="hidden" name="fileName" id="expHeatFileName" val=""/>
		<input type="submit" id="expHeatSubmit" class="ImgDownSubmit"/>
	</form>
	<div id="expHeat"></div>
	<div id="expBox"></div>
	<br/>
</div>
