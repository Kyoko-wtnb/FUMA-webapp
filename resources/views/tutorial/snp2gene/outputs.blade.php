<h3 id="outputs">Outputs of SNP2GENE</h3>
<p>Once your job is completed, you will receive an email.
  Unless an error occured during the process, the email includes the link to results page (this again requires login).
  You can also access to the results page from My Job list.
  The result page displays 4 results panels.
</p><br/>
<img src="{!! URL::asset('/image/result.png') !!}" style="width:55%"/><br/><br/>

<h4><strong>1. Genome-wide plots</strong></h4>
<p>This panel displays manhattan plots and Q-Q plots for both GWAS summary statistics (input file) and gene-based association test.<br/>
  In addition MAGMA based gene-set P-values are provided.
  Note that MAGMA gene-set analyses uses the full distribution of SNP p-values and is different from pathway enrichment test that only test for enrichment of low P-values, or enrichment of prioritized genes. <br/>
  Images are downloadable in several formats, and underlying results can be downloaded in Table format from the download tab.
</p>
<p><strong>Plots for GWAS summary statistics</strong><br/>
  For plotting purposes, overlapping data points are filtered out based on the following criteria. <br/>
  <ul>
    <li>Manhattan plot: Overlapping data points (SNPs) were filtered out such that there is only one data point per pixel, but only when the average data points per pixel (x-axis) across y-axis is above 1.
      For each pixel, the plotted data point was randomly selected.
      This filtering was only performed for SNPs with P-value ≥ 1e-5.
    </li>
    <li>Overlapping data points (SNPs) were filtered such that only one data point per pixel was kept.
      For each pixel, the plotted data point was randomly selected.
      This filtering was only performed for SNPs with P-value ≥ 1e-5.
    </li>
  </ul>
</p>
<p><strong>Plots for gene-based test (MAGMA)</strong><br/>
  Gene analysis was performed by using MAGMA (v1.6) with default setting.
  SNPs were assigned to the genes obtained from Ensembl build 85 (only protein-coding genes).<br/>
  Genome-wide significance (red dashed line) was set at 0.05 / (the number of tested genes).
  Genes whose P-value reached the genome-wide significance can be labeled in the manhattan plot.
  The number of genes to label can be controled by typing the number at the left uppser side of the plot.
  MAGMA results are available from the download panel.
</p>
<p><strong>MAGMA Gene-Set Analysis</strong><br/>
  Using the result of gene analysis (gene level p-value), (competitive) gene-set analysis is performed with default parameters with MAGMA v1.6.
  Gene sets were obtained from Msigdb v5.2 for "Curated gene sets" and "GO terms".
</p>
<p><strong>MAGMA Tissue Expression Analysis (<span style="color: blue;">FUMA v1.1.0</span>)</strong><br/>
  To test the (positive) relationship between highly expresesd genes in a specific tissue and genetic associations, gene-property analysis is performed using average expression of genes per tissue type as a gene covariate.
  Gene expression values are log2 transformed average RPKM per tissue type after winsorized at 50 based on GTEx RNA-seq data. Tissue expression analysis is performed for 30 general tissue types and 53 specific tissue types separatly.
  MAGMA was performed using the result of gene analysis (gene-based P-value) and tested for oneside (greater) with conditioning on average expression across all tissue types.
</p>
<br/>
<img src="{!! URL::asset('/image/snp2geneGWplot.png') !!}" style="width:55%"/><br/><br/>
<br/>

<h4><strong>2. Summary of results</strong></h4>
<p>This panel shows a general summary of the results based on your GWAS input. Images are downloadable in several foramts.</p>
<ul>
  <li>Summary of SNPs and mapped genes<ul>
    <li><strong>#Genomic risk loci</strong>: The number of genomic risk loci defined from independent significant SNPs by merging LD blocks if they are less apart than the user defined distance.
      A genomic risk locus can contain multiple lead SNPs and/or independent significant SNPs.
    </li>
    <li><strong>#lead SNPs</strong>: The number of lead SNPs identified from independent significant SNPs which are indepedent each other at r<sup>2</sup> 0.1.</li>
    <li><strong>#independent significant SNPs</strong>: The number of independent significant SNPs which reached the user defined  genome-wide significant P-value and are independent each other at the user defined r<sup>2</sup></li>
    <li><strong>#candidate SNPs</strong>: The number of candidate SNPs which are in LD (given r<sup>2</sup>) of one of the independent significant SNPs.
      This includes non-GWAS tagged SNPs which are extracted from the 1000 genomes reference panel.
      When SNPs are filtered based on functional annotation for gene mapping, this number refers to the number of SNPs before the functional filtering.</li>
    <li><strong>#candidate GWAS tagged SNPs</strong>: The number of candidate SNPs (described above) which are tagged in GWAS (exists in your input file).</li>
    <li><strong>#mapped genes</strong>: The number of genes mapped based on the user-defined parameters.</li>
  </ul></li>
  <li>Positional annotation of candidate SNPs<br/>
    This is a histogram of the number of SNPs per functional consequences on genes.
    When SNPs have more than one (different) annotations, they are counted for each annotation.
    SNPs assigned NA; this may be because alleles do not matche with the fasta files of ANNOVAR Ensembl genes.
  </li>

  <li>Summary per genomic locus<br/>
    This histogram displays the size of genomic risk loci, the number of candidate SNPs, the number of prioritized genes and the number of genes phisically locating within the genomic locus.
  </li>
</ul>
<br/>
<img src="{!! URL::asset('/image/snp2geneSummary.png') !!}" style="width:55%"/><br/><br/>
<br/>

<h4><strong>3. Result tables</strong></h4>
<p>This panel contains multiple tables of your results.
  Each column is described in <a href="{{ Config::get('app.subdir') }}/tutorial#table-columns">Table columns</a>.
</p>
<p>By clicking one of the rows of tables of genomic risk loci, lead SNPs or independent significant SNPs, FUMA will create regional plots of candidate SNPs (GWAS P-value).
  Optionally, regional plots with genes and functional annotations can be created from the panel at the bottom of the page.
</p>
Regional plots can be created with the following optional annotations:<br/>
<ul>
  <li>GWAS association statistics: input P-value</li>
  <li>CADD score</li>
  <li>RegulomeDB score</li>
  <li>15-core chromatin state: tissue/cell types have to be selected.</li>
  <li>eQTLs: This is only available when eQTL mapping is performed. eQTLs are plotted per gene and colored per tissue types.</li>
</ul>
<br/>
<img src="{!! URL::asset('/image/snp2geneResults.png') !!}" style="width:60%"/><br/>
<img src="{!! URL::asset('/image/snp2geneAnnotPlot.png') !!}" style="width:50%"/><br/><br/>
<br/>
<h4><strong>4. Downloads</strong></h4>
<p>All results are downloadable as text file.
  Columns are described in <a href="{{ Config::get('app.subdir') }}/tutorial#table-columns">Table columns</a>.
  README file is also included in a zip file of the selected files to download.<br/>
  When the SNP table is downloaded, <strong>ld.txt</strong> will be also downloaded at the same time.
  This file contains the r2 values computed from 1000G reference panel for all pairs of one of the independent lead SNPs and all other SNPs within the LD.
</p>
