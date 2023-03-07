@extends('layouts.master')

@section('content')
	<div class="container" style="padding-top:50px;">
		<div id="message" style="text-align: center;">
			<div class="alert alert-danger" style="font-size:24px;">
				FUMA is currently closed for the server maintenance and update of data resources.
			</div>
		</div>
		<div style="text-align: center;">
			<h2>FUMA GWAS</h2>
			<h2>Functional Mapping and Annotation of Genome-Wide Association Studies</h2>
		</div>
		<br/>
		<p>
			FUMA is a platform that can be used to annotate, prioritize, visualize and interpret GWAS results.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a> function takes GWAS summary statistics as an input,
			and provides extensive functional annotation for all SNPs in genomic areas identified by lead SNPs.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/gene2func">GENE2FUNC</a> function takes a list of geneids (as identified by SNP2GENE or as provided manually)
			and annotates genes in biological context
			<br/>
			To submit your own GWAS, logis is required for security reason.
			If you have't registered yet, you can do from <a href="{{ url('/register') }}">here</a>.
			<br/>
			You can browse example results of FUMA for a few GWAS from <a href="{{ Config::get('app.subdir') }}/browse">Browse Examples</a> without registoration or login.
		</p>
		<p>
			Please post any questions, suggestions and bug reports on Google Forum: <a target="_blank" href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a>.
		</p>
		<p>
			<strong>Citation:</strong><br/>
			When using FUMA, please cite the following.<br/>
			K. Watanabe, E. Taskesen, A. van Bochoven and D. Posthuma. Functional mapping and annotation of genetic associations with FUMA. <i>Nat. Commun.</i> <b>8</b>:1826. (2017).<br/>
			<a target="_blank" href="https://www.nature.com/articles/s41467-017-01261-5">https://www.nature.com/articles/s41467-017-01261-5</a>
			<br>
			Depending on which results are reported, please also cite the original study of data sources/tools used in FUMA
			(references are availalbe at <a href="{{ Config::get('app.subdir') }}/links">Links</a>).
		</p>
		<br/>

	</div>
	</br>
@endsection
