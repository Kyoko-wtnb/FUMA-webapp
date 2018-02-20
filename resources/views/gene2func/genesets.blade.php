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
	<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="dir" id="GSDir" val=""/>
		<input type="hidden" name="id" id="GSJobID" val=""/>
		<input type="hidden" name="data" id="GSData" val=""/>
		<input type="hidden" name="type" id="GSType" val=""/>
		<input type="hidden" name="fileName" id="GSFileName" val=""/>
		<input type="submit" id="GSSubmit" class="ImgDownSubmit"/>
	</form>
	<div id="GeneSet"></div>
</div>
