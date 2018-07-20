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
				<div id="downFileCheck"></div>
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
		<span class="form-inline">
			Order tissue by :
			<select id="celltype_order" class="form-control" style="width: auto;">
				<option value="alph">Alphabetical</option>
				<option value="p" selected>P-value</option>
			</select>
		</span>
	</div>
</div>
