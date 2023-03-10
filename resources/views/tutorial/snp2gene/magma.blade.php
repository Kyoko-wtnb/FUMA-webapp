<h3 id="magma">MAGMA analyses</h3>
FUMA performs MAGMA gene analysis, gene-set analysis and gene-property analysis.
In this section, reference panels and gene expression data sets are described detils.

<h4>Reference panel</h4>
<div style="padding-left: 40px;">
	To avoid mismatch of rsID, unique ID (chr:pos:allele1:allele2) is used for MAGMA.
	<h4><strong>1. 1000 Genome Phase3</strong></h4>
	Same as described in <a href="{{ Config::get('app.subdir') }}/tutorial#refpanel">Reference panel</a> section.
	<br/>

	<h4><strong>2. UK Biobank release2</strong></h4>
	Same as described in <a href="{{ Config::get('app.subdir') }}/tutorial#refpanel">Reference panel</a> section,
	<span style="color:red;">except further 1,000 individuals were randomly selected</span>
	to reduce runtime of MAGMA (it takes >3 hours with 10,000 individuals).
	<br/>

</div>

<h4>Gene analysis</h4>
<div style="padding-left: 40px;">
	Gene analysis is performed with default parameters (SNP-wide mean model) with the user selected reference panel.<br/>
	The command FUMA uses is the following.<br/>
	<code class="codebox">
		magma --bfile [path to the selected reference panel] \<br/>
		<tab>--pval [magma input file] ncol=3 (or N=[total sample size]) \<br/>
		<tab>--gene-annot [path to the annotation file with use selected window size] \<br/>
		<tab>--out [output file]
	</code>

</div>

<h4>Gene set analyses</h4>
<div style="padding-left: 40px;">
	In FUMA, curated gene sets (c2.all) and go terms (c5.bp, c5.cc and c5.mf) from MsigDB are tested.
	For FUMA &le; v1.3.0, 10894 gene sets (curated gene sets: 4728, GO terms: 6166) from MsigdB v5.2 are used.
	For FUMA &ge; v1.3.1, 10655 gene sets (curated gene sets: 4738, GO terms: 5917) from MsigDB v6.1 are used.
	For FUMA &ge; v1.3.4, 10678 gene sets (curated gene sets: 4761, GO terms: 5917) from MsigDB v6.2 are used.
	Bonferroni correction was performed for the all tested gene sets.
	To customise, you can download the output file and select a specific gene sets.<br/>
	<br/>
	Gene set analysis is performed by the following command.<br/>
	<code class="codebox">
		magma --gene-results [path to]/magma.genes.raw \</br>
		<tab>--set-annot [path to gene set file] \</br>
		<tab>--out [output file]
	</code>
</div>

<h4>Gene property analysis for tissue specificity</h4>
<div style="padding-left: 40px;">
	To identify tissue specificity of the phenotype, FUMA performs MAGMA gene-property analyses to test
	relationships between tissue specific gene expression profiles and disease-gene associations.
	The gene-property analysis is based on the regression model,

	$$Z \sim \beta_0 + E_t\beta_E + A\beta_A + B\beta_B + \epsilon$$

	where \(Z\) is a gene-based Z-score converted from the gene-based P-value,
	\(B\) is a matrix of several technical confounders included by default.

	\(E_t\) is the gene expression value of a testing tissue type c and \(A\) is the
	average expression across tissue types in a data set, defined as follows:

	$$E_t = \sum_{i}^{n} log_2(e_i + 1)/n$$
	$$A = \sum_{j \in T}^{N} E_j/N$$

	where \(n\) is the number of samples in tissue type t, \(e_i\) is the expression value
	of a sample in the tissue type t (e.g. RPKM count or TPM),
	\(N\) is the number of tissue types in a data set
	and \(T = \{tissue\ type\ 1, tissue\ type\ 2, ..., tissue\ type\ N\}\).
	We performed a one-sided test (\(\beta_E>0\)) which is essentially testing
	the positive relationship between tissue specificity and genetic association
	of genes.<br/>
	<br/>
	MAGMA gene-property analysis is run with the following command, <br/>
	<code class="codebox">
		magma --gene-results [input file name].genes.raw \<br/>
		<tab>--gene-covar [file name of selected RNA-seq data set] \ <br/>
		<tab>--model direction-covar=greater condition-hide=Average \<br/>
		<tab>--out [output file name]
	</code>
</div>

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
