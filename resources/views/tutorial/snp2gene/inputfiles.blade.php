<h3 id="prepare-input-files">Prepare Input Files</h3>
<div style="margin-left: 40px;">
	<h4><strong>1. GWAS summary statistics</strong></h4>
	<p>GWAS summary statistics is a mandatory input of <strong>SNP2GENE</strong> process.
		FUMA accept various types of format. For example, PLINK, SNPTEST and METAL output formats can be used as it is.
		For other formats, column names can be provided.
		Input files should be prepared in asci txt or (preferably) gzipped or zipped.
		Every row should contain information on one SNP.
		An input GWAS summary statistics file could contain only subset of SNPs (e.g. SNPs of interest for your study to annotate them),
		but in this case, results of MAGMA will not be relevant anymore.
		Please note that variants which do not exists in the selected reference panel will not be included in any analyses.<br/>
		<span class="info"><i class="fa fa-info"></i>
			For indels, both alleles need to be matched exactly with reference panel to be included in the ananlysis.
			For example, an indel rs144029872 needs to be encoded with AG/A (the order of alleles does not matter),
			anything else such as G/- or I2/D will not match wich the selected reference panel.
		</span>
	</p>
	<p><strong>Mandatory columns</strong><br/>
		The input file must include a P-value and either an rsID or chromosome index + genetic position on hg19 reference genome.
		Whenevr rsID is provided, it is updated to dbSNP build 146.
		When either chromosome or position is missing, they are extracted from dbSNP build 146 based on rsID.
		When rsID is missing, it is extracted from dbSNP build 146 based on chromosome and position.
		When all of them (rsID, chromosome and position) are provided, they are kept as input except rsID which is updated to dbSNP build 146.<br/>
		The column of chromosome can be a string like "chr1" or just an integer like 1.
		When "chr" is attached, this will be removed in output files.
		When the input file contains chromosome X, this will be encoded as chromosome 23, however, the input file can contain "X".
	</p>
	<p><strong>Allele columns</strong><br/>
		Alleles are not mandatory but if only one allele is provided, that is considered to be the effect allele.
		When two alleles are provided, the effect allele will be defined depending on column name.
		If alleles are not provided, they will be extracted from the dbSNP build 146 and minor alleles will be assumed to be the effect alleles.
		Effect and non-effect alleles are not distinguished during annotations, but used for alignment with eQTLs.
		Whenever alleles are provided, they are matched with dbSNP build 146 if extraction of rsID, chromosome or position is necessary.<br/>
		Alleles are case insensitive.
	</p>
	<p><strong>Headers</strong><br/>
		Column names are automatically detected based on the following headers (case insensitive).</p>
		<ul>
			<li><strong>SNP | snpid | markername | rsID</strong>: rsID</li>
			<li><strong>CHR | chromosome | chrom</strong>: chromosome</li>
			<li><strong>BP | pos | position</strong>: genomic position (hg19)</li>
			<li><strong>A1 | effect_allele | allele1 | alleleB</strong>: affected allele</li>
			<li><strong>A2 | non_effect_allele | allele2 | alleleA</strong>: another allele</li>
			<li><strong>P | pvalue | p-value | p_value | frequentist_add_pvalue | pval</strong>: P-value (Mandatory)</li>
			<li><strong>OR</strong>: Odds Ratio</li>
			<li><strong>Beta | be</strong>: Beta</li>
			<li><strong>SE</strong>: Standard error</li>
		</ul>
		If your input file has alternative names, these can be entered in the respective input boxes when specifying the input file.
		Note that any columns with the name listed above but with different element need to be avoided.
		For example, when the column name is "SNP" but the actual element is an id such as "chr:position" rather than rsID will cause an error.<br/>
		Extra columns will be ignored.<br/>
		Rows that start with "#" will be ignored.<br/>
		<span class="info"><i class="fa fa-info"></i> Column  "N" is described in the <a href="{{ Config::get('app.subdir') }}/tutorial#parameters">Parameters</a> section.</span><br/>
		<span class="info"><i class="fa fa-info"></i> Be carefull with the alleles header in which A1 is defined as effect allele by default. Please specify both effect and non-effect allele column to avoid mislabeling.<br/>
			If wrong labels are proveded for alleles, it does not affect any annotation and prioritization results. It does however affect eQTLs results (alignment of risk increasing allele of GWAS and tested allele of eQTLs).
			Be aware of that when you interpret results.
		</span><br/>
	</p>

	<p><strong>Delimiter</strong><br/>
		Delimiter can be any of white space including single space, multiple space and tab.
		Because of this, each element including column names must not include any space.
	</p>

	<hr>
	<h4>Note and Tips</h4>
	<p>
		When the input file has all of the following columns; rsID, chromosome, position, allele1 and allele2, the process will be much quicker than extracting information.
	</p>
	<p>The pipeline currently supports human genome <span style="color: red;">hg19</span>.
		If your input file is not based on hg19, please update the genomic position using liftOver from UCSC.
		However, there is an option for you!! When you provide only rsID without chromosome and genomic position, FUMA will extract them from dbSNP build 146 based on hg19.
		To do this, remove columns of chromosome and genomic position or rename headers to ignore those columns.
		Note that extracting chromosome and genomic position will take extra time.
	</p>
	<hr>
</div>

<div style="margin-left: 40px;">
	<h4><strong>2. Pre-defined lead SNPs</strong></h4>
	<p>This is an optional input file.<br/>
		This option would be useful when<br/>
		1. You have lead SNPs of interest but they do not reach significant P-value threshold.<br/>
		2. You are only interested in specific lead SNPs and do not want to identify additional lead SNPs which are independent.
		In this case, you also have to UNCHECK option of <code>Identify additional independent lead SNPs</code>.<br/>
		If you want to specify lead SNPs, input file should have the following 3 columns:<br/>
	</p>

	<ul>
		<li><strong>rsID</strong> : rsID of the lead SNPs</li>
		<li><strong>chr</strong> : chromosome</li>
		<li><strong>pos</strong> : genomic position (hg19)</li>
	</ul>
	<p style="color: #000099;"><i class="fa fa-info"></i>
		The order of columns has to be exactly the same as shown above but header could be anything (the first row is ignored).
		Extra columns will be ignored.
	</p>
</div>

<div style="margin-left: 40px;">
	<h4><strong>3. Pre-defined genomic region</strong></h4>
	<p>This is an optional input file.
		This option would be useful when you have already done some followup analyses of your GWAS and are interested in specific genomic regions.
		When pre-defined genomic region is provided, regardless of parameters, only lead SNPs and SNPs in LD with them within provided regions will be reported in outputs.<br/>
		If you want to analyse only specific genomic regions, the input file should have the following 3 columns:<br/>
	</p>
	<ul>
		<li><strong>chr</strong> : chromosome</li>
		<li><strong>start</strong> : start position of the genomic region of interest (hg19)</li>
		<li><strong>end</strong> : end position of the genomic region of interest (hg19)</li>
	</ul>
	<p style="color: #000099;"><i class="fa fa-info"></i>
		The order of columns has to be exactly the same as shown above but header could be anything (the first row is ignored).
		Extra columns will be ignored.
	</p>
</div>
