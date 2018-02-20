<!-- Downloads -->
<div class="sidePanel container" style="padding-top:50px; height: 100vh;" id="downloads">
	<h4 style="color: #00004d">Download files</h4>
	<form action="{{ Config::get('app.subdir') }}/{{$page}}/filedown" method="post" target="_blank">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="id" value="{{$id}}"/>
		<input type="hidden" name="prefix" value="{{$prefix}}"/>
		<div id="downFileCheck">
			<input type="checkbox" name="paramfile" id="paramfile" checked onchange="DownloadFiles();">Parameter settings</br>
			<input type="checkbox" name="locifile" id="locifile" checked onchange="DownloadFiles();">Genomic risk loci table <br/>
			<input type="checkbox" name="leadfile" id="leadfile" checked onchange="DownloadFiles();">lead SNP table (independent lead SNPs at r2 0.1) </br>
			<input type="checkbox" name="indSNPfile" id="indSNPfile" checked onchange="DownloadFiles();">Independent Significant SNPs table (independent at user defined r2) </br>
			<input type="checkbox" name="snpsfile" id="snpsfile" checked onchange="DownloadFiles();"> SNP table (Candidate SNPs with chr, bp, P-value, CADD, RDB, nearest gene, genomic risk loci and lead SNPs)<br/>
			<input type="checkbox" name="annovfile" id="annovfile" checked onchange="DownloadFiles();">ANNOVAR results (uniqID, annotation, gene and distance, SNP-gene pair per line)<br/>
			<input type="checkbox" name="annotfile" id="annotfile" checked onchange="DownloadFiles();">Annotations (CADD, RDB and Chromatin state of 127 tissue/cell types)<br/>
			<input type="checkbox" name="genefile" id="genefile" checked onchange="DownloadFiles();">Gene table (mapped genes)<br/>
			<div id="eqtlfiledown"><input type="checkbox" name="eqtlfile" id="eqtlfile" checked onchange="DownloadFiles();">eQTL table (eQTL of selected tissue types)<br/></div>
			<div id="cifiledown"><input type="checkbox" name="cifile" id="cifile" checked onchange="DownloadFiles();">Chromatin interaction tables (chromatin interactions overlap with candidate SNPs and regulatory elements)<br/></div>
			<input type="checkbox" name="gwascatfile" id="gwascatfile" checked onchange="DownloadFiles();">SNPs in GWAS catalog (full features)<br/>
			<input type="checkbox" name="magmafile" id="magmafile" checked onchange="DownloadFiles();">MAGMA (full) results<br/>
		</div>
		<span class="form-inline">
			<input class="btn btn-xs" type="submit" name="download" id="download" value="Download files"/>
			<tab><a class="allfiles"> Select All </a>
			<tab><a class="clearfiles"> Clear</a>
		</span><br/>
	</form>
</div>
