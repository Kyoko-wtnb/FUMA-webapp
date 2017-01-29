<h3 id="results">Result page</h3>
<p>One process is done, you will receive an email.
  Unless an error occured during the process, the email includes the link to results page (this again requires login).
  You can also access to the results page from My Job list.
  The result page display 4 additional side bars.
</p><br/>
<img src="{!! URL::asset('/image/result.png') !!}" style="width:55%"/><br/><br/>

<h4><strong>1. Genome-wide plots</strong></h4>
<p>This panel displays manhattan plots and Q-Q plots for both GWAS summary statistics (input file) and gene-based association test.<br/>
  Images are downloadable as PNG files.
</p>
<p><strong>Plots for GWAS summary statistics</strong><br/>
  To minimize overlapped data points in the plot, they are filtered based on the following criteria.
  Please be aware that, since majority of overlapped data points are not displayed in the plot, those plots are approximated plots.<br/>
  <ul>
    <li>Manhattan plot: Overlapped data points (SNPs) were filtered to make the plot one data point per pixel only when average data points per pixel (x-axis) across y-axis is above 1.
      For each pixel, data point was randomly selected.
      This filtering was only performed SNPs with P-value &ge; 1e-5 to avoid over filtering.</li>
    <li>Q-Q plot: Overlapped data points (SNPs) were filtered such that one data point per pixel.
      For each pixel, data point was randomly selected.
      This filtering was only performed SNPs with P-value &ge; 1e-5 to avoid over filtering.</li>
  </ul>
</p>
<p><strong>Plots for gene-based test (MAGMA)</strong><br/>
  Gene analysis was performed by using MAGMA (v1.6) with default setting.
  SNPs were assigned to the genes obtained from Ensembl build 85 (only protein-coding genes).<br/>
  Genome-wide significance (red dashed line) was set at 0.05 / (the number of tested genes).
  Genes whose P-value yealed the genome-wide significance will be labeled in the manhattan plot.
  The number of genes to label can be controled by typing the number at the left uppser side of the plot.
  MAGMA results are available from the download panel.
</p>
<p><strong>MAGMA gene-set analysis</strong><br/>
  Using the result of gene anlysis (gene level p-value), (sompetitive) gene-set analysis is performed with defalt parameters with MAGMA v1.6.
  Gene sets were obtained from Msigdb v5.2 for "Curated gene sets" and "GO terms".
</p>
<br/>
<img src="{!! URL::asset('/image/snp2geneGWplot.png') !!}" style="width:55%"/><br/><br/>
<br/>

<h4><strong>2. Summary of results</strong></h4>
<p>This panel shows summary of your GWAS input. Images are downloadable as PNG files.</p>
<ul>
  <li>Summary of SNPs and mapped genes<ul>
    <li><strong>#Genomic risk loci</strong>: Number of the genomic risk loci defined from ind. sig. SNPs by merging LD blocks if they are less apart than user defined distance.
      A genomic risk loci could contain multiple lead SNPs and/or ind. sig. SNPs.
    </li>
    <li><strong>#lead SNPs</strong>: Nuumber of the lead SNPs identified from ind. sig SNPs which are indepedent each other at r<sup>2</sup> 0.2.</li>
    <li><strong>#Ind. Sig. SNPs</strong>: Number of independent significant SNPs which reached user defined P-value threshold and independent each other at user defined r<sup>2</sup></li>
    <li><strong>#candidate SNPs</strong>: The number of candidate SNPs which are in LD (given r<sup>2</sup>) of one of the ind. sig. SNPs.
      This includes non-GWAS tagged SNPs which is extracted from 1000 genomes reference panel.
      When SNPs were filtered based on functional annotation for gene mapping, this number is before the functional filtering.</li>
    <li><strong>#candidate GWAS tagged SNPs</strong>: The number of candidate SNPs (described above) which are tagged in GWAS (exists in your input file).</li>
    <li><strong>#mapped genes</strong>: The number of genes mapped by user-defined parameters.</li>
  </ul></li>
  <li>Positional annotation of candidate SNPs<br/>
    This is a histogram of the number of SNPs per functional consequences on genes.
    When SNPs have more than one (different) annotations, those are counted for each annotation.
    SNPs assigned NA might be because alleles do not matche with fasta file in ANNOVAR Ensembl genes.
  </li>

  <li>Summary per genomic locus<br/>
    This histogram display, the size of loci,  the number of candidate SNPs, the number of mapped genes and number of genes phisically locating within define locus per genomic locus.
  </li>
</ul>
<br/>
<img src="{!! URL::asset('/image/snp2geneSummary.png') !!}" style="width:55%"/><br/><br/>
<br/>

<h4><strong>3. Result tables</strong></h4>
<p>This panel contain multiple tables of your results.
  Here are descriptions for columns in each tables. Each columns will be described in the following section.
  Downloadable text files have the same column as shown in the interface unless methioned.
</p>
<p>By clicking one of the rows of tables of genomic risk loci, lead SNPs or ind. sig. SNPs, it will create regional plots of candidate SNPs (GWAS P-value).
  Optionally, regional plot with genes and functional annotations can be created from the panel at the bottom of the page.
</p>
Regional plots can be created with the following annotations optionally.<br/>
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
  Columns are described in README file.<br/>
  When SNP table is downloaded, <strong>ld.txt</strong> will be also downloaded at the same time.
  This file contains r2 computed from 1000G reference panel for all pairs of one of the independent lead SNPs and all other SNPs within the LD.
</p>
