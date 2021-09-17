@extends('layouts.master')
@section('head')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
        <td>18 September 2021</td>
        <td>v1.3.6d</td>
        <td>
          Update GWAScatalog to e104_2021-09-15.
        </td>
      </tr>
      <tr>
        <td>10 September 2021</td>
        <td>v1.3.6c</td>
        <td>
          Fixed a minor bug to allow for transparent regional plot backgrounds. Fixed a minor bug to allow for analysis of chr23 using UKB 10k reference panel.
        </td>
      </tr>
      <tr>
        <td>04 July 2021</td>
        <td>v1.3.6b</td>
        <td>
          Fixed a minor bug in the calculation of annotation enrichment p-values. We re-analyzed results from existing jobs, and the 99th percentile absolute difference between old and new p-values was 0.00000458. However, jobs in which more than 100,000 variants were selected for further analysis may encounter larger differences, and these analyses should be re-run.
        </td>
      </tr>
     <tr>
        <td>09 September 2020</td>
        <td>v1.3.6a</td>
        <td>
          Update MAGMA to v1.08. Bug fix for gene2func (DrugBank annotation).
        </td>
      </tr>
      <tr>
  			<td>23 March 2020</td>
  			<td>v1.3.6</td>
  			<td>
  				Datasets in the eQTL Catalogue which were not already present in FUMA were added as options for eQTL mapping.
  			</td>
  		</tr>
		<tr>
			<td>4 Nov 2019</td>
			<td>v1.3.5e</td>
			<td>
				There was an error during mapping hg38 to hg19 for eQTLs of GTEx v8, which caused some missing significant eQTLs.
				It has been fixed in the current version.
				If you have any SNP2GENE jobs with eQTL mapping using GTEx v8, it is strongly recommended to re-do gene-mapping.
			</td>
		</tr>
		<tr>
			<td>14 Oct 2019</td>
			<td>v1.3.5d</td>
			<td>
				<strong>Update 1</strong>: GTEx v8 eQTLs and gene expression data were added to SNP2GENE and GENE2FUNC.
				Due to the limited storage space on the server, GTEx eQTLs for all versions (v6/v7/v8) are now limited to
				SNP-gene pairs with nominal P-value < 0.05 (before all tested SNP-gene pairs were available).
				This does not affect your results if you used only significant SNP-gene pairs (with gene Q-value < 0.05)<br/>
				<strong>Update 2</strong>: MsigDB was updated to v7.0.<br/>
				<strong>Update 3</strong>: GWAS catalog was updated to e96_2019-09-24.<br/>
				<strong>Update 4</strong>: Wikipathway was updated to v20191010.<br/>
				<strong>Update 5</strong>: DrugBank was updated to v5.1.4.<br/>
				<strong>Update 6</strong>: There was a bug in the script to process PsychENCODE eQTL data.
				This bug caused to filter all PsychENCODE eQTLs during gene mapping.
				If you used this data, it is strongly advised to re-run gene mapping (you might get more eQTLs but not less).<br/>
			</td>
		</tr>
		<tr>
			<td>5 Aug 2019</td>
			<td>v1.3.5b</td>
			<td>
				Minor bugs in the main scripts are fixed.
			</td>
		</tr>
		<tr>
			<td>27 May 2019</td>
			<td>v1.3.5</td>
			<td>
				<strong>Major update 1</strong>: Reference panels were updated to include as much as variants possible.
				For 1000 Genome, new reference panel "ALL" was added.
				UK Biobank reference panel was updated to release 2b (based on genotype data released in May 2018).
				UK Biobank release 1 and release 2 reference panels are no longer available.
				Please check <a a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#refpanel">Tutorial</a> for more details.
				<br/>
				<strong>Major update 2</strong>: New options for additional SNP annotations (genome based annotations provided as bed files) were added.
				Annotations can be selected for each mapping separately.
				Options can be selected to either only annotate SNPs or also filter based on the overlap of selected annotations.
				<br/>
				<strong>Major update 3</strong>: For GENE2FUNC, users can now provide custom gene sets in GMT format.
				<br/>
				<strong>Minor update 1</strong>: A threshold of r<sup>2</sup> for the second clumping to define lead SNPs was added as input parameter.
				<br/>
				<strong>Minor update 2</strong>: Additional data sets were added to eQTLs and chromatin interaction mappings.
				<br/>
				<strong>Minor update 3</strong>: Additional data sets were added to cell type analyses.
				<br/>
				<strong>Minor update 4</strong>: Enrichments of functional consequence of candidate SNPs are now tested against SNPs in the user selected reference panel.
				<br/>
				<strong>Minor update 5</strong>: GWAS catalog was updated to e96 2019-05-03.
				<br/>
				<strong>Minor update 6</strong>: Gene window size for MAGMA gene analysis can be specified for up- and downstream separately.
			</td>
		</tr>
		<tr>
			<td>26 Apr 2019</td>
			<td>v1.3.4d</td>
			<td>
				Script for cell type specificity analysis has been optimised and minor bugs are fixed.
			</td>
		</tr>
		<tr>
			<td>26 Mar 2019</td>
			<td>v1.3.4c</td>
			<td>
				Minor bug update on GENE2FUNC gene set enrichment test.
				It has been testing P(X>x), but now it tests P(X&ge;x).
				This change will affect the results of "Tissue specificity" and "Gene sets" in GENE2FUNC process.
				If you wish to update the existing job, please contact developer.
			</td>
		</tr>
		<tr>
			<td>28 Feb 2019</td>
			<td>v1.3.4b</td>
			<td>
				MAGMA reference panels were modified. There were mil-filtering of some SNPs in the pre-process (only for UK biobank references).
				This might cause different results of MAGMA gene, gene-set and gene-property analyses.
			</td>
		</tr>
		<tr>
			<td>17 Feb 2019</td>
			<td>v1.3.4</td>
			<td>
				<strong>Major update 1</strong>: Cell type specificity analysis was updated to 3 step workflow.
				The workflow consist of 1) per dataset cell type analysis, 2) within dataset conditional analysis and
				3) across datasets conditional analysis.
				Additional datasets were curated in addition to ones available from v1.3.3.
				Please check <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#workflow">Tutorial</a> for details.
				<br/>
				<strong>Minor update 1</strong>: MAGMA was updated to v1.07 to facilitate conditional analyses
				in cell type specificity analysis.
				This does not affect results of SNP2GENE MAGMA analyses, however the extension of gene-set/gene-property analysis
				has been changed to XXX.gsa.out.
				<br/>
				<strong>Minor update 2</strong>: GWAS catalog was updated to e93 2019-01-11.
				<br/>
				<strong>Minor update 3</strong>: MsigDB was updated to v6.2.
				<br/>
				<strong>Minor update 4</strong>: WikiPathways was updated to v20190110.
				<br/>
				<strong>Minor update 5</strong>: Additional HiC datasets (adult and fetal cortex samples) were added to the chromatin interaction mapping.
			</td>
		</tr>
		<tr>
			<td>19 Dec 2018</td>
			<td>v1.3.3d</td>
			<td>
				Minor bugs have been fixed.
				Bugs include mis-alignment of alleles in ANNOVAR and mis-count of SNPs in LD per independent significant SNPs.
			</td>
		</tr>
		<tr>
			<td>22 Oct 2018</td>
			<td>v1.3.3c</td>
			<td>
				Cis- and trans-eQTLs from eQTLGen are now available for eQTL mapping.
				The eQTLGen is a meta-analysis of 37 datasets with in total of 31,684 individuals.
				To annotate new eQTLs for your existing SNP2GENE jobs, you can use "re-do gene mapping" option.
			</td>
		</tr>
		<tr>
			<td>11 Sep 2018</td>
			<td>v1.3.3b</td>
			<td>
				As requested, addition options for window size of MAGMA gene analysis have been added.
				In the new version, 0, 1, 5, 10, 15, 20, 25, 30, 40 and 50kb (both sides) are available.
			</td>
		</tr>
		<tr>
			<td>22 July 2018</td>
			<td>v1.3.3</td>
			<td>
				Cell type specificity analysis based on scRNA-seq datasets using MAGMA is now available.
				This is currently a beta version, further improvement of plots and additional info
				will be available soon.
			</td>
		</tr>
		<tr>
			<td>29 May 2018</td>
			<td>v1.3.2</td>
			<td>
				Some scripts have been optimized. Chromatin interaction mapping with large number of SNPs is now much faster than before.
			</td>
		</tr>
		<tr>
			<td>2 May 2018</td>
			<td>v1.3.1b</td>
			<td>
				Minor bug in chromatin interaction mapping has been fixed.
				This missed some promoter annotations in region2 which might has caused over filtering of chromatin interactions.
				This affects the mapped genes only when you activate the filtering based on promoter in chromatin interaction mapping.
			</td>
		</tr>
		<tr>
			<td>27 Apr 2018</td>
			<td>v1.3.1</td>
			<td>
				<strong>Major update 1</strong>: UK Biobank reference panel has been added.
				There are three types of reference panels are avilable for UKB;
				release 1 white british, release 2 white british and release 2 European.
				Each reference panel consists of randomly selected 10,000 subjects.
				MAGMA reference was created for each of these population by further randomly selected 1,000 subject
				since the run time is very long by using 10K subjects.
				Please check <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#refpanel">Tutorial</a> for details.
				<br/>
				<strong>Major update 2</strong>: You can now publish your FUMA results to public to allow other users can browse your results.
				The browse page does not require users to login which makes it possible to share your results to larger population in an easy way!!
				Please check <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#publish">Tutorial</a> for details.
				<br/>
				<strong>Minor update 1</strong>: Emsemble v92 genes are now available for both SNP2GENE and GENE2FUNC.
				Ensembl v85 can be selected from the option, but the default is updated to v92.
				<br/>
				<strong>Minor update 2</strong>: MsigDB is updated to v6.1 and WikiPathways is updated to 20180410.
				<br/>
				<strong>Minor update 3</strong>: An info tab is added to the header (<i class="fa fa-info-circle"></i>).
				You can check the current version of FUMA and how many jobs are currently running/queued.
				<br/>
			</td>
		</tr>
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
