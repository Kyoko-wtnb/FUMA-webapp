<h3 id="gene2funcOutputs">Results and Outputs</h3>
<div style="padding-left: 40px;">
  <h4><strong>1. Gene Expression Heatmap</strong></h4>
  <p>
    The heatmap displays two expression values.<br/>
    1) <b>Average RPKM per tissue</b> : This is the averaged RPKM (Read Per Kilo base per Million) per tissue per gene following to winsorization at 50 and log 2 transformation with pseudocount 1.
    This allows for comparison across tissues and genes.
    Hence, cells filled in red represent higher expression compared to cells filled in blue across genes and tissue types.<br/>
    2) <b>Average of normarized RPKM per tissue</b> : This is the average of normalized expression (zero mean across samples) following to a log 2 transformation of the RPKM with pseudocount 1.
    This allows comparison across tissues (horizontal comparison) within a gene.
    Thus expression values of different genes within a tissue (vertical comparison) are not comparable.
    Hence, cells filled in red represents higher expression of the genes in a corresponding tissue compared to other tissues, but it DOES NOT represent higher expression compared to other genes.
  </p>
  <p>Tissues (columns) and genes (rows) can be ordered by alphabetically or cluster (hierarchial clustering). <br/>
    The heatmap is downloadable in several file formats. Note that the image will be downloaded as displayed.
  </p>
  <img src="{!! URL::asset('/image/gene2funcHeatmap.png') !!}" style="width:60%"/>
  <br/><br/>

  <h4><strong>2. Tissue specificity</strong></h4>
  <p>
     Differentially expressed gene (DEG) sets for 53 tissue types from GTEx were pre-calculated by performing two-sided t-test for any one of tissues against all others.
     For this, expresstion values were normalized (zero-mean) following to a log 2 transformation of RPKM.
     Genes which with P-value &le; 0.05 after Bonferroni correction and absolute log fold change &ge; 0.58 were defined as differentially expressed genes in a given tissue compared to others.
     On top of DEG, up-regrated DEG and down-regulated DEG were also pre-calculated by taking sign of t-statistics into account.
     The same process was performed for 30 general tissue types.<br/>
  </p>
  <p>Input genes were tested against each of the DEG sets.
    Significant enrichment at FDR &le; 0.05 are coloured in red.<br/>
    Results and images are downloadable as text files and in several image file formats.
  </p>
  <img src="{!! URL::asset('/image/gene2funcTs.png') !!}" style="width:60%"/>
  <br/><br/>

  <h4><strong>3. Gene Sets</strong></h4>
  <p>
    Hypergeometric tests are performed to test if genes of interest are overrepresented in any of  the pre-defined gene sets.
    Multiple test correction is performed per category, (i.e. canonical pathways, GO biological processes and so on, separately).
    Gene sets were obtained from MsigDB, WikiPathways and reported genes from the GWAS-catalog.
  </p>
  <p>
    The full results are downloadable as a text file at the top of the page. <br/>
    In each category, plot view and table view are selectable.
    In the plot view, images are downloadable in several file formats.
  </p>
  <img src="{!! URL::asset('/image/gene2funcGS.png') !!}" style="width:70%"/>
  <br/><br/>

  <h4><strong>4. Gene Table</strong></h4>
  <p>
    Input genes are mapped to OMIM ID, UniProt ID, Drug ID of DrugBank and links to GeneCards.
    Drug IDs are assigned if the UniProt ID of the gene is one of the targets of the drug.<br/>
    Each link to OMIM, Drugbank and GeneCards will open in a new tab.
  </p>
  <img src="{!! URL::asset('/image/gene2funcGT.png') !!}" style="width:70%"/>

</div>
