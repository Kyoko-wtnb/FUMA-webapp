@extends('layouts.master')
@section('head')
<script type="text/javascript">
  var loggedin = "{{ Auth::check() }}";
</script>
@stop

@section('content')
<div class="container" style="padding-top: 50px;">
	<table class="table table-bordered">
	<thead>
		<tr>
			<th style="width: 15%;">Date</th>
			<th style="width: 15%;">Version</th>
			<th style="width: 70%;">Description</th>
		</tr>
	</thead>
    <tbody>
		<tr>
			<td>25 June 2017</td>
			<td>v1.2.0</td>
			<td><strong>Major update 1</strong>: Chromatin interaction mapping is newly added into SNP2GENE process which utilize 3D genome data such as Hi-C, ChIA-PET and so on.
				Build in Hi-C data is obtained from <a href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE87112" target="_blank">GSE87112</a> and user can also provide custom chromatin interaction data.
				<a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#chromatin-interactions">Tutorial</a> for details.<br/>
				<strong>Major update 2</strong>: "Browse examples" page is newly added which does not require registration/login.
				In the page, pre-computed results can be borwsed with full features (e.g. interactive plots and download).<br/>
				<strong>Minor updates</strong>: SNP2GENE process is improved.
				eQTLs are aligned with the risk increasing alleles in the input GWAS file (see <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">Tutorial</a> for details).
			</td>
		</tr>
		<tr>
			<td>27 Apr 2017</td>
			<td>v1.1.2</td>
			<td>Two gene scores (pLI and ncRVIS) are added to the gene table. See <a href="{{ Config::get('app.subdir') }}/links">links</a> for detail information of each score.
			</td>
		</tr>
		<tr>
			<td>27 Apr 2017</td>
			<td>v1.1.2</td>
			<td>The speed of SNP2GENE process is improved.
			</td>
		</tr>
		<tr>
			<td>24 Mar 2017</td>
			<td>v1.1.1</td>
			<td>SNPs filtering with functional annotation for gene mapping is now reflected in the regional plot with annotations.
				Details are described at the bottom of the page of regional plot with annotations.
			</td>
		</tr>
		<tr>
			<td>17 Mar 2017</td>
			<td>v1.1.0</td>
			<td>In SNP2GENE, MAGMA tissue expression analyses was added to "Genome wide plot".
				Details are in the <a href="{{ Config::get('app.subdir') }}/tutorial#outputs">SNP2GENE Outputs</a> section of the tutorial.
			</td>
		</tr>
		<tr>
			<td>21 Feb 2017</td>
			<td>v1.0.0</td>
			<td>The first version was freezed.</td>
		</tr>
    </tbody>
  </table>
</div>
@stop
