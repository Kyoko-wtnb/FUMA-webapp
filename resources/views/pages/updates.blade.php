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
			<td>21 Feb 2018</td>
			<td>v1.3.0</td>
			<td><strong>Major update 1</strong>: The following 4 eQTL data sets are added, GTEx v7, MuTHER, CommonMind Consortium and xQTLServer.
				Each data set has different description for tested allele, P-value and FDR.
				Please check <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">Tutorial</a> for details before start using these new data sets.
				To be able to replicate previous results, GTEx v6 eQTLs are also kept as options.
				Because of this, when "all" is selected for eQTL mapping, both GTEx v6 and GTEx v7 are going to be used.
				To avoid this, please manually check data sets.
				<br/>
				<strong>Major update 2</strong>: Indels are now included in the 1000 genome reference.
				Note that only bi-allelic SNPs and indels are available in FUMA.
				<br/>
				<strong>Major update 3</strong>: GTEx v7 and BrainSpan gene expression data sets were
				added to MAGMA gene expression analysis in SNP2GENE and DEG enrichment analysis in GENE2FUNC.
				<br/>
				<strong>Major update 4</strong>: For existing SNP2GENE jobs, it is possible to re-perform
				gene mapping with different parameters from v1.3.0.
				Please check <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#geneMap">Tutorial</a> for details.
				<br/>
				<strong>Minor update 1</strong>: For SNP2GENE job submission, previous parameter setting
				can be loaded by selecting job ID.
				<br/>
				<strong>Minor update 2</strong>: A summary page is added to the GENE2FUNC results page.
				<br/>
				<strong>Minor update 3</strong>: GWAS catalog is updated to version e91_2018-02-06.
				<br/>
			</td>
		</tr>
		<tr>
			<td>20 Dec 2017</td>
			<td>v1.2.8</td>
			<td>
				Positional mapping based on distance between SNPs and genes has been improved.
				It was purely based on the distance annotated by ANNOVAR until v1.2.7, however,
				ANNOVAR only annotate intergenic SNPs to two closet genes.
				From v1.2.8, distance between SNPs to genes are checked independently from ANNOVAR.
				This change more likely to affect your results when positional mapping was performed
				with distance much larger than 10kb.
				There must be no effect if positional mapping was performed based on the functional consequence of SNPs annotated by ANNOVAR.
			</td>
		</tr>
		<tr>
			<td>11 Dec 2017</td>
			<td>v1.2.7</td>
			<td>
				Filtering of chromatin interactions in circos plot has been updated.
				Only chromatin interactions (orange links) and eQTLs (green links) used for mapping are displayed in circos plot from this version.
				If you wish to update circos plot of existing SNP2GENE job, please contact developer with your jobID.
			</td>
		</tr>
		<tr>
			<td>1 Sep 2017</td>
			<td>v1.2.4</td>
			<td>
				Minor bug in chromatin interaction mapping was fixed.
				Chromatin interaction mapping has been missed some interactions that are overlapping with risk loci.
				<span style="color:red;">If you have any SNP2GENE job with chromatin interaction mapping submitted before 1st of September 2017,
				it's strongly recommended to re-submit jobs or please contact developer to update the results.</span>
			</td>
		</tr>
		<tr>
			<td>22 Aug 2017</td>
			<td>v1.2.3</td>
			<td>
				GWAScatalog has been updated to release e89 2017-08-15.
				Please be aware that jobs submitted to SNP2GENE before 22th August 2017 used previous version (e85 2016-09-27).
				If you wish to update GWAScatalog results for your SNP2GENE jobs, please contact developer with jobID.
			</td>
		</tr>
		<tr>
			<td>25 June 2017</td>
			<td>v1.2.0</td>
			<td><strong>Major update 1</strong>: Chromatin interaction mapping is newly added into SNP2GENE process which utilize 3D genome data such as Hi-C, ChIA-PET and so on.
				Build in Hi-C data is obtained from <a href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE87112" target="_blank">GSE87112</a> and user can also provide custom chromatin interaction data.
				<a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#chromatin-interactions">Tutorial</a> for details.<br/>
				<strong>Major update 2</strong>: "Browse examples" page is newly added which does not require registration/login.
				In the page, pre-computed results can be browsed with full features (e.g. interactive plots and download).<br/>
				<strong>Minor updates</strong>: SNP2GENE process is improved.
				eQTLs are aligned with the risk increasing alleles in the input GWAS file (see <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">Tutorial</a> for details).
				To avoid confusion, allele names in the SNPs table were updated to non_efefct_allele/effect_allele from ref/alt.
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
