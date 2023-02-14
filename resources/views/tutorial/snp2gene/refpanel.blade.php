<h3 id="refpanel">Reference panel</h3>
To define independent significant SNPs, lead SNPs and genomic risk loci,
FUMA uses reference panels.
In this section, each reference panel is described details.
<br>
<span class="info"><i class="fa fa-info"></i>
	From FUMA v1.3.5, multi allelic SNPs are all included.
</span>

<div style="padding-left: 40px;">
	<h4><strong>1. 1000 Genome Phase3</strong></h4>
	Genotype data for chromosome 1-22 and X was downloaded from
	<a target="_blank" href="ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/">ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/</a>.<br/>
	Multi allelic SNPs were first split into separate columns using vcfmulti2oneallele.jar from JVARKIT
	(<a target="_blank" href="http://lindenb.github.io/jvarkit/">http://lindenb.github.io/jvarkit/</a>).
	VCF files were then converted to PLINK bfile (PLINK v1.9).
	Any CNVs were removed, while any indels were kept.
	Unique ID (consists of chr:position:allele1:allele2 where alleles were alphabetically ordered) was assigned to each SNP and
	duplicated SNPs (with identical unique ID) were excluded.
	Genotype data were split into 5 (super) populations based on panel file
	(<a target="_blank" href="ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/integrated_call_samples_v3.20130502.ALL.panel">ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/integrated_call_samples_v3.20130502.ALL.panel</a>)
	using PLINK.<br/>
	MAF and pairwise LD were computed by PLINK (--r2 --ld-window 99999 --ld-window-r2 0.05) for each population and all samples together (ALL),
	and SNPs with MAF=0 were excluded for each population.
	<br/>
	<span class="info"><i class="fa fa-info"></i>
		Reference panel ALL covers most number of SNPs.
		To avoid missing SNPs from FUMA annotations, reference panel ALL might be preferred.
		However, the LD is not population specific and need caution for the definition of independent significant SNPs and lead SNPs.
	</span>
	<br/><br/>
	Number of samples and SNPs in the reference panels (click on a row to download the corresponding variant file):
	<table class="table table-bordered table-hover" style="width:auto">
		<thead>
			<th>Population</th>
			<th>Sample size</th>
			<th>Number of SNPs</th>
			<th>Download size</th>
		</thead>
		<tbody>
			<tr class="clickable" onclick='tutorialDownloadVariant("ALL")'>
				<td>ALL</td>
				<td>2,504</td>
				<td>84,853,668</td>
				<td><img class="fontsvg" src="{{URL::asset('/image/download.svg')}}"/> 870M</td>
			</tr>
			<tr class="clickable" onclick='tutorialDownloadVariant("AFR")'>
				<td>AFR</td>
				<td>661</td>
				<td>43,676,209</td>
				<td><img class="fontsvg" src="{{URL::asset('/image/download.svg')}}"/> 461M</td>
			</tr>

			<tr class="clickable" onclick='tutorialDownloadVariant("AMR")'>
				<td>AMR</td>
				<td>347</td>
				<td>29,501,504</td>
				<td><img class="fontsvg" src="{{URL::asset('/image/download.svg')}}"/> 305M</td>
			</tr>
			<tr class="clickable" onclick='tutorialDownloadVariant("EAS")'>
				<td>EAS</td>
				<td>504</td>
				<td>24,507,348</td>
				<td><img class="fontsvg" src="{{URL::asset('/image/download.svg')}}"/> 254M</td>
			</tr>
			<tr class="clickable" onclick='tutorialDownloadVariant("EUR")'>
				<td>EUR</td>
				<td>503</td>
				<td>25,063,419</td>
				<td><img class="fontsvg" src="{{URL::asset('/image/download.svg')}}"/> 260M</td>
			</tr>
			<tr class="clickable" onclick='tutorialDownloadVariant("SAS")'>
				<td>SAS</td>
				<td>489</td>
				<td>27,691,316</td>
				<td><img class="fontsvg" src="{{URL::asset('/image/download.svg')}}"/> 287M</td>
			</tr>
		</tbody>
	</table>
	<form method="post" target="_blank" action="/tutorial/download_variants">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="variant_code" id="tutorialDownloadVariantCode" value=""/>
		<input type="submit" id="tutorialDownloadVariantSubmit" class="ImgDownSubmit" style="display: none;"/>
	</form>

	<h4><strong>2. UK Biobank release 2b</strong></h4>
	Genotype data was obtained under application ID 16406.
	The reference panel is based on genotype data released in May 2018 (including SNPs imputed UK10K/1000G).
	Two reference panels were created;
	white British and European subjects.
	For white British, 10,000 unrelated individuals were randomly selected.
	For European, each individuals were first assigned to one of the 5 1000G populations
	based on the minimum Mahalanobis distance.
	Then randomly selected 10,000 unrelated EUR individuals were used.<br/>
	SNPs were filtered on INFO score > 0.9.
	MAF and pairwise LD were computed by PLINK (--r2 --ld-window 99999 --ld-window-r2 0.05)
	and SNPs with MAF=0 were excluded.
	<br/>
	In both reference panels, 16,972,700 SNPs are available.
	<br/>
</div>
