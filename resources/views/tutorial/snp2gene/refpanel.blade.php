<h3 id="refpanel">Reference panel</h3>
To define independent significnat SNPs, lead SNPs and genomic risk loci,
FUMA uses reference pnales.
In this section, each reference panel is described details.

<div style="padding-left: 40px;">
	<h4><strong>1. 1000 Genome Phase3</strong></h4>
	Genotype data for chromosom 1-22 and X was downloaded from
	<a target="_blank" href="ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/">ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/</a>.<br/>
	Downloaded vcf files were split into 5 (super) populations based on panel file
	(<a target="_blank" href="ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/integrated_call_samples_v3.20130502.ALL.panel">ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/integrated_call_samples_v3.20130502.ALL.panel</a>)
	using vcftools (vcf-subset).
	Number of individuals per poplation is AFR: 661, AMR: 347, EAS: 504, EUR: 503 and SAS: 489.<br/>
	Only bi-allelic SNPs with "PASS" were extracted per population using vcftools
	(--remove-filtered-all, --min-allele 2, --max-allele 2) and manually excluding SNPs
	with duplicated position.
	SNPs with MAF=0 were excluded.<br/>
	MAF and pairwise LD were compued by PLINK (--r2 --ld-window 99999 --ld-window-r2 0.05).
	<br/>

	<h4><strong>2. UK Biobank release1</strong></h4>
	Genotype data was obtained under application ID 1640.
	10,000 white british individuals were randomly selected to create reference pnale.<br/>
	<span class="info"><i class="fa fa-info"></i> Chromosome X is not available for this reference panel</span>.<br/>
	Only bi-allelic SNPs with imputation INFO score > 0.9 were extracted by PLINK (--biallelic-only) and manually excluding
	SNPs with duplicated position.
	SNPs with MAF=0 were excluded.<br/>
	MAF and pairwise LD were compued by PLINK (--r2 --ld-window 99999 --ld-window-r2 0.05).
	<br/>

	<h4><strong>3. UK Biobank release2</strong></h4>
	Genotype data was obtained under application ID 1640.
	SNPs imputed basaed on UK10K/1000G were excluded (only SNPs imputed based on HRC were included).
	For release 2, two reference panels were created;
	white british and european individuals projected onto 1000G population.
	For white british, 10,000 unrelated individuals were randomly selected.
	For european, each individuals were first assigned to one of the 5 1000G populations
	based on the minimum Mahalanobis distance.
	Then randomly selected 10,000 unrelated EUR individuals were used.<br/>
	<span class="info"><i class="fa fa-info"></i> Chromosome X is not available for this reference panel</span>.<br/>
	Only bi-allelic SNPs with imputation INFO score > 0.9 were extracted by PLINK (--biallelic-only) and manually excluding
	SNPs with duplicated position.
	SNPs with MAF=0 were excluded.<br/>
	MAF and pairwise LD were compued by PLINK (--r2 --ld-window 99999 --ld-window-r2 0.05).
	<br/>

</div>
