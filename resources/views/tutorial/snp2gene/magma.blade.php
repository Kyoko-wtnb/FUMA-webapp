<h3 id="magma">MAGMA analyses</h3>
FUMA performs MAGMA gene analysis, gene-set analysis and gene-property analysis.
The gene property analysis is performed with multiple gene expression data sets.
In this section, each of the gene expression data sets is described details.

<h4>Gene expression data sets</h4>
<div style="padding-left: 40px;">
	<h4><strong>1. GTEx v6</strong></h4>
	<p><strong>Data source</strong><br/>
		RNAseq data set was downloaded from <a href="http://www.gtexportal.org/home/datasets">http://www.gtexportal.org/home/datasets</a>.
		Gene level RPKM was used (<span style="color: blue;">GTEx_Analysis_v6_RNA-seq_RNA-SeQCv1.1.8_gene_rpkm.gct.gz</span>).
	</p>
	<p><strong>Pre-process</strong><br/>
		Primary gene ID was Ensemble ID.
		In total, 8,555 samples were available.
		From 56,318 annotated genes, genes were filtered on such that average RPKM per tissue is >1 in at least on of the 53 tissues.
		This resulted in 28,577 genes.
		RPKM was winsorized at 50 (replaced RPKM>50 with 50).
		Then average of log transformed RPKM with pseudocount 1 (log2(RPKM+1)) per tissue (for either 53 detail or 30 general tissues)
		was used as the covariates conditioning on the average across all the tissues.
	</p>
	<h4><strong>2. GTEx v7</strong></h4>
	<p><strong>Data source</strong><br/>
		RNAseq data set was downloaded from <a href="http://www.gtexportal.org/home/datasets">http://www.gtexportal.org/home/datasets</a>.
		Gene level TPM was used (<span style="color: blue;">GTEx_Analysis_2016-01-15_v7_RNASeQCv1.1.8_gene_rpm.gct.gz</span>).
	</p>
	<p><strong>Pre-process</strong><br/>
		Primary gene ID was Ensemble ID.
		In total, 11,688 samples were available.
		From 56,203 annotated genes, genes were filtered on such that average TPM per tissue is >1 in at least on of the 53 tissues.
		This resulted in 32,335 genes.
		TPM was winsorized at 50 (replaced TPM>50 with 50).
		Then average of log transformed TPM with pseudocount 1 (log2(TPM+1)) per tissue (for either 53 detail or 30 general tissues)
		was used as the covariates conditioning on the average across all the tissues.
	</p>
	<h4><strong>3. BrainSpan</strong></h4>
	<p><strong>Data source</strong><br/>
		RNAseq data set was downloaded from <a href="http://www.brainspan.org/static/download" target="_blank">http://www.brainspan.org/static/download</a>.
		Gene level RPKM was used (<span style="color: blue;">genes_matrix_csv.zip</span>).
	</p>
	<p><strong>Pre-process</strong><br/>
		Primary gene ID was Ensemble ID.
		In total, 524 samples were available.
		General developmental stages were annotated for each sample based on the age.
		We used 11 developmental stages and 29 ages as the label.
		For the label of age, we excluded age groups with &lt;3 samples (25 pcw and 35 pcw).
		From 52,376 annotated genes, genes were filtered on such that average RPKM per label is >1 in at least one of the either developmental stage or age.
		This resulted in 19,601 and 21,001 genes for developmental stages and age groups, respectively.
		RPKM was winsorized at 50 (replaced RPKM>50 with 50).
		Then average of log transformed RPKM with pseudocount 1 (log2(RPKM+1)) per label (for either 11 developmental stages or 29 age groups)
		was used as the covariates conditioning on the average across all the labels.
	</p>
</div>
