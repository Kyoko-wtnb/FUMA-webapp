<!-- Summary -->
<div id="g2f_summaryPanel" class="sidePanel container" style="padding-top:50px;">
	<h4>Summary of input genes</h4>
	<div id="g2f_summaryTable">
	</div>
	<br/>

	<h4>Download files</h4>
	<div id="downloads">
		<form action="{{ Config::get('app.subdir') }}/{{$page}}/g2f_filedown" method="post" target="_blank">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="id" value="<?php echo $id;?>"/>
			<input type="hidden" name="prefix" value="{{$prefix}}"/>
			<div id="downFileCheck">
				<input type="checkbox" name="paramfile" id="paramfile" checked onchange="DownloadFiles();">Parameter settings</br>
				<input type="checkbox" name="summaryfile" id="summaryfile" checked onchange="DownloadFiles();">Summary of input genes</br>
				<input type="checkbox" name="geneIDfile" id="geneIDfile" checked onchange="DownloadFiles();">IDs of input genes (including Ensembl ID, entrez ID and gene symbol)<br/>
				<input type="checkbox" name="expfile" id="expfile" checked onchange="DownloadFiles();">Data for expression heatmap of user selected gene expression data sets</br>
				<input type="checkbox" name="DEGfile" id="DEGfile" checked onchange="DownloadFiles();">Tissue specificity restuls (enrichment test results of DEG sets for user selected expression data sets)</br>
				<input type="checkbox" name="gsfile" id="gsfile" checked onchange="DownloadFiles();">Gene set analysis results (only include significnat gene sets) </br>
			</div>
			<span class="form-inline">
				<input class="btn btn-xs" type="submit" name="download" id="download" value="Download files"/>
				<tab><a class="allfiles"> Select All </a>
				<tab><a class="clearfiles"> Clear</a>
			</span>
			<br/>
		</form>
	</div>

	<br/>
	<h4>Parameters</h4>
	<div id="g2f_paramTable">
	</div>
</div>
