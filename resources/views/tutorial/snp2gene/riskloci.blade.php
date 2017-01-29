<h3 id="riskloci">Risk loci and lead SNPs</h3>
In this section, "Genomic risk locai", "lead SNPs" and
"Independent significant SNPs (Ind. sig. SNPs)" in details.
<div style="padding-left: 40px">
  <h4><strong>1. Independent significant SNPs (Ind. sig. SNPs)</strong></h4>
  Ind. sig. SNPs are defined as SNPs that have P-value &le; user define threshold for genome-wide significance (5e-8 by default)
  and independent each other at user defined r<sup>2</sup> (0.6 by default).
  Therefore, ind. sig. SNPs are same as the SNPs that are clumped GWAS tagged SNPs at the same P-value and r2
  (second P-value can be set at "P-value threshold for GWAS tagged SNPs").</br>
  Relaxing genome-wide significant P-value results in increasing number of ind. sig. SNPs.
  When you would like to annotate SNPs in genomic loci which do not reach general genome-wide significance (5e-8),
  less significant P-value should be used.
  Alternatively, by providing pre-defined lead SNPs as separate file, these provided SNPs will be defined as ind. sig. SNPs regardless of their P-value.<br/>
  The higher r<sup>2</sup>, the more SNPs are defined as ind. sig. SNPs.
  At the same time, the number of SNPs in the LD of ind. sig. SNPs (which are the SNPs annotated in FUMA) decreases.
  <h4><strong>2. Lead SNPs</strong></h4>
  Lead SNPs are defined as SNPs which are ind. sig. SNPs and independent from each other at r<sup>2</sup> 0.1 (currentlly not adjustable).
  Therefore, lead SNPs are same as the SNPs clumped ind. sig. SNPs at the same P-value and r<sup>2</sup> 0.1 by plink.<br/>
  When r<sup>2</sup> is set at 0.1, lead SNPs are exactlly same as ind. sig. SNPs.<br/>
  Any parameter has direct effect to lead SNPs since lead SNPs are defined based on ind. sig. SNPs.
  <h4><strong>3. Genomic risk loci</strong></h4>
  On top of lead SNPs, FUMA defines genomic risk loci to represent genomically close signals in GWAS as a locus.
  Genomick risk loci are defined by merging LD blocks of ind. sig. SNPs witch are closer than user defined distance (250 kb by default).
  The distance between two LD blocks of two ind. sig. SNPs is the distance between the closest SNPs
  (which are in LD of the ind. sig. SNPs at user defined r<sup>2</sup>) from each LD block.<br/>
  Each locus is represented by the top lead SNP which has the minimum P-value in tha locus. <br/>
  When distance is set at 0, only phisically overlapped LD blocks will be merged into locus.
  <h4><strong>4. Candidate SNPs (SNPs in LD of ind. sig. SNPs)</strong></h4>
  Candidate SNPs are defnied as such SNPs which are in LD of any of the ind. sig. SNPs at user defined r<sup>2</sup>.
  Most left and morst right SNPs which are in LD of a ind. sig. SNP define a LD block in which those SNPs are used to compute distance between LD blocks.<br/>
  Note that not all SNPs are necessary in LD of lead SNPs.<br/>
  All candidate SNPs are annotated their functions and listed in the "SNPs" table.<br/>
  The higher r<sup>2</sup>, the less candidate SNPs are identified.
  The number of candidate SNPs can be also controlled by the parameter of the maximum P-value for gwas-tagged SNPs (0.05 by default).
  For example, when r<sup>2</sup> is set at less than 0.6, P-value threshold for GWAS tagged SNPs has high impact for candidate SNPs.
  <br/><br/>
  <p>
    <strong>Effect of r<sup>2</sup> parameter</strong><br/>
    <img src="{!! URL::asset('/image/r2-1.png') !!}" style="width:90%"/><br/>

  </p>
</div>
