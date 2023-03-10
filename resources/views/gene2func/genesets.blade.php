<!-- GeneSet enrichment -->
<div id="GeneSetPanel"  class="sidePanel container" style="padding-top:50px;">
	<h4>Enrichment of input genes in Gene Sets</h4>
	<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="dir" id="GSDir" val=""/>
		<input type="hidden" name="id" id="GSJobID" val=""/>
		<input type="hidden" name="data" id="GSData" val=""/>
		<input type="hidden" name="type" id="GSType" val=""/>
		<input type="hidden" name="fileName" id="GSFileName" val=""/>
		<input type="submit" id="GSSubmit" class="ImgDownSubmit"/>
	</form>
	<span class="info"><i class="fa fa-info"></i>
		Plots and tables only display gene sets with adjusted P-value < 0.05.
		When adjusted P-value threshold is set to > 0.05, all results passed threshold are included in the GS.txt field
		downloadable from "Summary" tab.
	</span>
	<br/>
	<span class="info"><i class="fa fa-info"></i>
		If there is no significant gene sets (adjusted P-value < 0.05) in user provided custom gene sets,
		they are not displayed in this page, but all results passed threshold are included in the GS.txt field
		downloadable from "Summary" tab.
	</span>
	<br/>
	<div id="GeneSet"></div>
</div>
