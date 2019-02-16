<h3 id="basemodel">MAGMA gene-property analysis with scRNA-seq</h3>
The gene-property analysis aims to test relationships between
cell specific gene expression profiles and disease-gene associations.
The gene-property analysis is based on the regression model,

$$Z = \beta_0 + E_c\beta_E + A\beta_A + B\beta_B + \epsilon$$

where \(Z\) is a gene-based Z-score converted from the gene-based P-value,
\(B\) is a matrix of several technical confounders included by default.

\(E_c\) is the gene expression value of a testing cell type c and \(A\) is the
average expression across cell types in a data set, defined as follows:

$$E_c = \sum_{i}^{n} log_2(e_i + 1)/n$$
$$A = \sum_{j \in C}^{N} E_j/N$$

where \(n\) is the number of cells in the cell type c, \(e_i\) is the expression value
of a cell in the cell type c (e.g. UMI count or CPM),
\(N\) is the number of cell types in a data set
and \(C = \{cell\ type\ 1, cell\ type\ 2, ..., cell\ type\ N\}\).
Note that log transformation was omitted when available data was already log transformed.<br/>
We performed a one-sided test (\(\beta_E>0\)) which is essentially testing
the positive relationship between cell specificity and genetic association
of genes.<br/>
In principle, this model is same as tissue specificity analyses with MAGMA on SNP2GENE process
where tissue specific expression was used instead of cell specific expression.<br/>
<br/>
The file format of scRNA-seq data set is, Ensembl gene ID in the first column with column name "GENE",
N columns for per cell type average expression and average expression across
cell types with column name "Average".
MAGMA gene-property analysis is run with the following command.<br/>
<br/>
From FUMA v1.3.4 (MAGMA v1.07)<br/>
<code class="codebox">
	magma --gene-results [input file name].genes.raw \<br/>
	<tab>--gene-covar [file name of selected scRNA-seq data set] \<br/>
	<tab> --model condition-hide=Average direction=greater \<br/>
	<tab>--out [output file name]
</code>
<br/>
Until FUMA v1.3.3d (MAGMA v1.06)<br/>
<code class="codebox">
	magma --gene-results [input file name].genes.raw \<br/>
	<tab>--gene-covar [file name of selected scRNA-seq data set] condition=Average  onesided=greater \<br/>
	<tab>--out [output file name]
</code>
<br/>
<span class="info"><i class="fa fa-info"></i>
	The extension of output files from gene-property analysis using MAGMA v1.06 and v1.07 are different.
	Please refer to <a href="#cell_outputs">Outputs</a> for details.
</span>
<br/>
