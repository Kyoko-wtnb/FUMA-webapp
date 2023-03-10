<h3 id="cell_submit">How to perform cell type specificity analysis on FUMA</h3>
We use MAGMA gene-property analysis to test cell type specificity of phenotype with GWAS summary statistics.
As an input, it requires XXX.genes.raw file from MAGMA gene analysis.
You can either select your existing SNP2GENE job or upload MAGMA output file you run by yourself.
<br/><br/>
FUMA uses Ensembl gene ID for all scRNA-seq data.
If the input file contains different gene ID or gene symbols, FUMA will map to Ensembl gene ID.
To do so, please <strong>UNCHECK</strong> the option right below the file selection, "Ensembl gene ID is used in the provided file".
Otherwise MAGMA will result in an error due to mismatch of gene ID.
<br/><br/>
From FUMA v1.3.4, a 3-step workflow is implemented for the cell type analysis.
This workflow consists of <br/>
<ul>
	<li><strong>Step 1</strong>: per dataset analysis (same as implemented in v1.3.4)<br/>
		After multiple testing correction across selected datasets, significant cell types were retained for Step 2.
	</li>
	<li><strong>Step 2</strong>: within dataset conditional analysis<br/>
		Identify independent signals per dataset by performing forward-selection.
	</li>
	<li><strong>Step 3</strong>: cross datasets conditional analysis<br/>
		Cell types retained from Step 2 are further conditioned each other across datasets to disentangle
		relationship between association of cell types from different datasets.
	</li>
</ul>
Please refer to <a href="#workflow">3-step workflow</a> for details.<br/>
<span class="info"><i class="fa fa-info"></i>
	Note that step 2 and step 3 are not activated by default.
	To perform entire workflow, please <strong>CHECK</strong> the options.
</span>
<br/>
