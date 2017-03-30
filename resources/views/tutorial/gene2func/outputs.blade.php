<h3 id="g2fOutputs">Outputs of GENE2FUNC</h3>
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
    Tissue specificity is tested using the following gene sets based on GTEx gene expression data.<br/>
    <br/>
    <strong>Differentially Expressed Gene (DEG) Sets</strong><br/>
    DEG sets were pre-calculated by performing two-sided t-test for any one of tissues against all others.
    For this, expresstion values were normalized (zero-mean) following to a log 2 transformation of RPKM.
    Genes which with P-value &le; 0.05 after Bonferroni correction and absolute log fold change &ge; 0.58 were defined as differentially expressed genes in a given tissue compared to others.
    On top of DEG, up-regrated DEG and down-regulated DEG were also pre-calculated by taking sign of t-statistics into account.
    This process was performed for 30 general tissue types and 53 specific tissue types, separately.<br/><br/>
    <!-- <strong>2) Tissue Expressed Gene (TEG) Sets (<span style="color:blue;">FUMA v1.1.0</span>)</strong><br/>
    TEG sets were pre-defined by genes which have average RPKM > 1 in each tissue type (30 general tissue types and 53 specific tissue types). -->
  </p>
  <p>
    Input genes were tested against each of the DEG sets using the hypergeometric test.
    The background genes are genes that have average RPKM > 1 in at least one of the 53 tissue types and exist in the user selected background genes.
    Significant enrichment at Bonferroni corrected P-value &le; 0.05 are coloured in red.<br/>
    <span class="info"><i class="fa fa-info"></i>
      Note that for DEG sets, Bonferroni correction is performed for each of up-regulated, down-regulated and both-sided DEG sets separately.
    </span><br/><br/>
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
