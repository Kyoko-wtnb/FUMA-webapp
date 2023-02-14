README of output files (GENE2FUNC) from FUMA web application

Author: Kyoko Watanabe (k.watanabe@vu.nl)
Version: 1.3.0 (21 Feb 2018)
Version: 1.3.1 (27 Apr 2018)
Version: 1.3.5 (27 May 2019)

This file contains description of columns for every downloadable file.

######################
# params.config
# Input parameters. The file is INI format.
######################
[jobinfo]
created_at : Date of job created
title : Job title
[params]
gtype : Either "text" (genes were provided in the text box) or "file" (genes were provided by uploading a file).
gval : If gtype is text, input genes are listed separated by ":", otherwise input file name.
ensembl : version of the Ensembl gene annotation
gsFileN : Number of the user provided gene set files
gsFile : Names of the user provided gene set files
bkgtype : Either "text", "file" or "select" (selected from gene type, e.g. protein coding or ncRNA).
	When GENE2FUNC was performed for a SNP2GENE job, this value is same as genetype parameter.
bkgval : If bkgtype is text, input background genes are listed separated by ":", if bkgtype is file, the file name, otherwise selected gene type.
gene_exp : Selected gene expression data sets.
	When GENE2FUNC was performed for a SNP2GENE job, this value is automatically set to same as magma_exp parameter in SNP2GENE.
MHC : 1 to exclude MHC region, 0 otherwise
adjPmeth : The method of P-value correction for gene set enrichment analysis. Benjamini-Hochberg (FDR) by default.
adjPcut : Threshold of corrected P-value for gene set enrichment analysis.
minOverlap : Minimum number of input genes overlapping with a tested gene set to be reported as significant.

######################
# summary.txt
# Summary of input and background genes.
######################
Number of input genes
Number of background genes
Number of input genes with recognised Ensembl ID: This number can be smaller than the number of input genes if they are not Ensembl ID.
Input genes without recognised Ensembl ID
Number of background genes with recognised Ensembl ID : This number can be smaller than the number of provided background genes if they are not Ensembl ID.
Background genes without recognised Ensembl ID
Number of input genes with unique entrez ID : Since Ensembl ID and entrez ID do not always have one-to-one relationship,
	this number can be different from the number of input genes.
	This is the number of input genes for gene set enrichment analysis.
Number of background genes with unique entrez ID : Same as above.
	This is the number of background genes for gene set enrichment analysis.

######################
# geneIDs.txt
# Input genes with assigned IDs (Ensembl ID, entrez ID ad symbol)
# This file does not include background genes.
# User can check which input genes are mapped to which ID (or not) using this file.
######################
ensg : Ensembl gene ID, NA if not available
entrez : NCBI gene ID, NA if not available
symbol : Gene symbol, NA if not available

######################
# XXX_exp.txt
# Input data of expression heatmap.
# There are the same number of files as the number of selected expression data sets.
######################
ensg : Ensembl gene ID
symbol : gene symbol
Rest of the columns are expression value per label

Prefix of file names
	gtex_v6_ts_avg_log2RPKM : average of log2 transformed RPKM after winsolizing at 50 per tissue (53 tissues in total) from GTEx v6
	gtex_v6_ts_avg_normRPKM : average of normalised (zero mean) log2 transformed RPKM after winsolizing at 50 per gene per tissue (53 tissues in total) from GTEx v6
	gtex_v6_ts_general_avg_log2RPKM : average of log2 transformed RPKM after winsolizing at 50 per tissue (30 general tissues in total) from GTEx v6
	gtex_v6_ts_general_avg_normRPKM : average of normalised (zero mean) log2 transformed RPKM after winsolizing at 50 per gene per tissue (30 general tissues in total) from GTEx v6
	gtex_v7_ts_avg_log2TPM : average of log2 transformed TPM after winsolizing at 50 per tissue (53 tissues in total) from GTEx v7
	gtex_v7_ts_avg_normTPM : average of normalised (zero mean) log2 transformed TPM after winsolizing at 50 per gene per tissue (53 tissues in total) from GTEx v7
	gtex_v7_ts_general_avg_log2TPM : average of log2 transformed TPM after winsolizing at 50 per tissue (30 general tissues in total) from GTEx v7
	gtex_v7_ts_general_avg_normTPM : average of normalised (zero mean) log2 transformed TPM after winsolizing at 50 per gene per tissue (30 general tissues in total) from GTEx v7
	bs_age_avg_log2RPKM : average of log2 transformed RPKM after winsolizing at 50 per age (29 different ages in total) from BrainSpan
	bs_age_avg_normRPKM : average of normalised (zero mean)log2 transformed RPKM after winsolizing at 50 per age (29 different ages in total) from BrainSpan
	bs_dev_avg_log2RPKM : average of log2 transformed RPKM after winsolizing at 50 per developmental period (11 developmental periods in total) from BrainSpan
	bs_dev_avg_normRPKM : average of normalised (zero mean)log2 transformed RPKM after winsolizing at 50 per developmental period (11 developmental periods in total) from BrainSpan

######################
# XXX_DEG.txt
# Results of tissue specificity test.
# There are the same number of files as the number of selected expression data sets.
# The test was performed for input genes against DEG genes
# (genes that are significantly up/down expression in a certain label compared to
# all other samples).
######################
Category : Either DEG.up (significantly up regulated genes), DEG.down (significantly down regulated genes) or DEG.twoside.
GeneSet : Label of samples
N_genes : The number of DEG of a given category and label
N_overlap : Number of input genes overlapping with DEG of a given category and label
p : Hypergeometric test (upper tail) P-value
adjP : Bonferroni corrected P-value
genes : Input genes overlapping with DEG of a given category and label

Prefix of file names
	gtex_v6_ts : GTEx v6 53 tissue types
	gtex_v6_ts_general : GTEx v6 30 general tissue types
	gtex_v7_ts : GTEx v7 53 tissue types
	gtex_v7_ts_general : GTEx v7 30 general tissue types
	bs_age : BrainSpan 29 different ages
	bs_dev : BrainSpan 11 developmental periods

######################
# GS.txt
# Results of gene set enrichment analysis.
# Only significant gene sets are included in this file.
######################
Category : One of the category from MsigDB
GeneSet : Name of gene set as provided by MsigDB
N_genes : Number of genes in a gene set
N_overlap : Number of input genes overlapping with the gene set
p : Hypergeometric test (upper tail) P-value
adjP : Adjusted P-value (user defined method)
genes : Genes overlapping with the gene set
link : Link to the MsigDB page if available
