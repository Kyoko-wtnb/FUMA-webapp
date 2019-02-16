README of output files (Cell Type) from FUMA web application

Author: Kyoko Watanabe (k.watanabe@vu.nl)
Version: 1.3.4 (5 Feb 2019)

This file contains description of columns for every downloadable file.

######################
# params.config
# Input parameters. The file is INI format.
######################
[jobinfo]
created_at : Date of job created
title : Job title
[params]
snp2geneID: job ID of SNP2GENE entry if existing job was used as input.
inputfile: input file name if user uploaded xxx.genes.raw file instead of using existing SNP2GENE job.
ensg_id: 1 if input gene id needs to be mapped to ENSG ID, 0 otherwise.
datasets: Selected scRNA-seq datasets, separated by ":".
adjPmeth: Mutiple testing correction methods.
step2: 1 to perform step 2 for datasets with more than one significant cell types (after correcting P-value across datasets), 0 otherwise.
step3: 1 to perform step 3 if there significant cell types from more than one dataset, 0 otherwise.

######################
# XXX.gsa.out (for MAGMA v1.07)
# XXX.gcov.out (for MAGMA v1.06)
# MAGMA output for per dataset analyses
######################
These files are original output of MAGMA gene-property analysis for each dataset.
Version in the parentheses indication version specific column name.
VARIABLE (v1.07)/COVAR (v1.06): Cell type name
TYPE (v1.07): Always "COVAR" (this can be "SET" when MAGMA gene-set analysis was performed with MAGMA v1.07)
NGENES (v1.07)/OBS_GENES (v1.06): Number of genes used in the analysis.
BETA: Effect size
BETA_STD: Standardised effect size
SE: Standard error
P: P-value
FULL_NAME (v1.07): Full cell type name if omitted in VARIABLE

######################
# magma_celltype_step1.txt
# Results of step 1
######################
The file contains all tested cell types by merging all xxx.gsa.out (or xxx.gcov.out) files.
Dataset: Dataset name
Cell_type: Cell type name
NGENES (v1.07)/OBS_GENES (v1.06): Number of genes used in the analysis.
BETA: Effect size
BETA_STD: Standardised effect size
SE: Standard error
P: P-value
P.adj.pds: Adjusted P-value per dataset
P.adj: Adjusted P-value across dataset

######################
# magma_celltype_step2.txt
# Results of step 2 (only available from FUMA v1.3.4)
######################
This file is only available when step 2 is performed.
The file contains all possible pairs of cell types within datasets which are significant after correcting P-value across datasets.
Dataset: Dataset name
Cell_type: Cell type name
MODEL: Index of the MODEL. Cell types with the same MODEL index are conditioned each other.
NGENES: Number of genes used in the analysis.
BETA: Effect size
BETA_STD: Standardised effect size
SE: Standard error
P: P-value conditioned on the other cell type with the same MODEL index
Marginal.P: P-value without conditioning on the other cell type.
	This P-value is same as in "magma_celltype_step1.txt".
PS: Proportional significance of the conditional P-value relative to the marginal P-value in log 10 scale.
	PS = -log10(P)/-log10(Marginal.P)
*Pair of cell types have values "NA" when they are collinear.

######################
# step1_2_summary.txt
# Summary of step 1 and 2 (only available from FUMA v1.3.4)
######################
The file contains all significant cell types after correcting P-value across datasets.
First 9 columns are same as in "magma_celltype_step1.txt".
cond_state: Resulting state of forward selection within dataset.
	The state of the per dataset conditional analysis of per dataset conditioned on the cell type in 'cond_celltype' column.
cond_celltype: Conditioned cell type corresponding "cond_state" column.
	 Items in both "cond_state" and "cond_celltype" columns separated by ";" are in corresponding order.
	 For example, if cell type A has "main;join" in "cond_state" column and "cell type B;cell type C" in "cond_celltype" column,
	 this means cell type A has state main for cell type B and join for cell type C.
step3: 1 if cell type is retained for step 3, 0 otherwise.

######################
# magma_celltype_step3.txt
# Results of step 3
######################
This file is only available when step 3 is performed
The file contains all possible pairs of cell types retained from step 2.
Dataset: Dataset name
Cell_type: Cell type name
MODEL: Index of the MODEL. Cell types with the same MODEL index are conditioned each other.
NGENES: Number of genes used in the analysis.
BETA: Effect size
BETA_STD: Standardised effect size
SE: Standard error
P: Cross-datasets conditional P-value conditioned on the other cell type with the same MODEL index
	(and the average expression of the corresponding dataset)
CDM.BETA: Cross-datasets marginal effect size
CDM.BETA_STD: Cross-datasets marginal standardised effect size
CDM.SE: Cross-datasets marginal standard error
CDM.P: Cross-datasets P-value conditioned on the average of the dataset of other cell type with the same MODEL index
Marginal.P: P-value without conditioning on the other cell type (nor the average of the dataset).
	This P-value is same as in "magma_celltype_step1.txt".
PS: Proportional significance of the CD conditional P-value relative to the CD marginal P-value in log 10 scale.
	PS = -log10(P)/-log10(CDM.P)
	Note that when CDM is NA due to collinearlity, PS=-log10(P)/-log10(Marginal.P)
*Pair of cell types have values "NA" when they are collinear.
