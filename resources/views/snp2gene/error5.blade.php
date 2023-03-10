<!-- ERROR:005 -->
<div class="sidePanel container" style="padding-top:50px;" id="error5">
	<h4 style="color: #00004d">ERROR:005 No candidate SNPs were found</h4>
	<div id="error5mes">
		<p>Error because of no significant SNP in the GWAS summary statistics.<br/>
			To obtain annotations; use a less stringent P-value threshold for lead SNPs or provide predefined lead SNPs.<br/>
		</p>
	</div>
	<br/>
	<h4 style="color: #00004d">Top 10 SNPs in the input file</h4>
	<span class="info"><i class="fa fa-info"></i>
		Top 10 significant SNPs of the input file.
		Refer the following P-value to set threshold for lead SNPs in the next submission.<br/>
		Note that deccreasing MAF threshold may lead to more hits (default MAF &ge; 0.01). <br/>
		Note that the MHC region is excluded by default. Check this option to include MHC in the analysis.
	</span>
	<br/>
	<table class="table table-bordered" id="topSNPs"></table>
	<br/>
	<h4 style="color: #00004d">Download files</h4>
	<form action="{{ Config::get('app.subdir') }}/{{$page}}/filedown" method="post" target="_blank">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="id" value="{{$id}}"/>
		<input type="hidden" name="prefix" value="{{$prefix}}"/>
		<div id="downFileCheck">
			<input type="checkbox" name="paramfile" id="paramfile" checked onchange="DownloadFiles();">Parameter settings</br>
			<input type="checkbox" name="magmafile" id="magmafile" checked onchange="DownloadFiles();">MAGMA (full) results<br/>
		</div>
		<span class="form-inline">
			<input class="btn btn-default btn-xs" type="submit" name="download" id="download" value="Download files"/>
			<tab><a class="allfiles"> Select All </a>
			<tab><a class="clearfiles"> Clear</a>
		</span><br/>
	</form>
</div>
