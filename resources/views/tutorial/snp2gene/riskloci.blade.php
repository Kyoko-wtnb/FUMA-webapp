<h3 id="riskloci">Risk loci and lead SNPs</h3>
In this section, "Genomic risk loci", "lead SNPs" and
"Independent significant SNPs (Ind. sig. SNPs)" are explained in more detail.
<br/>
<span class="info"><i class="fa fa-info"></i>
	From FUMA v1.3.5, r<sup>2</sup> threshold for the second clumping can be provided by users.
</span>
<div style="padding-left: 40px">
	<h4><strong>1. Independent significant SNPs (Ind. sig. SNPs)</strong></h4>
	Ind. sig. SNPs are defined as SNPs that have a P-value &le; the user define threshold for genome-wide significance (5e-8 by default)
	and are independent from each other at the user defined r<sup>2</sup> (0.6 by default).
	Therefore, ind. sig. SNPs are essentially the same as SNPs that are contained after clumping GWAS tagged SNPs at the same P-value and r<sup>2</sup>.
	Ind. sig. SNPs are used to select candidate SNPs that are in LD with the ind. sig. SNPs.</br>
	The candidate SNPs (and ind. sig. SNPs) are used for gene prioritization.<br/>
	Relaxing the threshold for the genome-wide significant P-value results in an increased number of ind. sig. SNPs.
	When you would like to identify ind. sig. SNPs in genomic loci which do not reach the commonly adopted genome-wide significance level of 5e-8,
	less significant P-value can be used.
	Alternatively, by providing pre-defined lead SNPs in a separate file, these provided SNPs will be defined as ind. sig. SNPs regardless of their P-value.<br/>
	The higher the threshold for r<sup>2</sup>, the more SNPs are defined as ind. sig. SNPs.
	At the same time, the number of SNPs in the LD with the ind. sig. SNPs (the candidate SNPs; which are the SNPs annotated in FUMA and used for gene prioritization) decreases.
	<h4><strong>2. Lead SNPs</strong></h4>
	Lead SNPs are defined as SNPs which are ind. sig. SNPs and are independent from each other at r<sup>2</sup> &lt; 0.1 (from v1.3.5, this value can be specified by users).
	Therefore, lead SNPs are same as the SNPs clumped ind. sig. SNPs at the user defined P-value and r<sup>2</sup> = 0.1 by plink.<br/>
	When r<sup>2</sup> is set at 0.1, lead SNPs are exactly the same as ind. sig. SNPs.
	However, this will also result in selecting candidate SNPs that have r<sup>2</sup> above 0.1 with any of ind. sig. SNPs.
	We thus advise to set r<sup>2</sup> at 0.6 or higher.
	<h4><strong>3. Genomic risk loci</strong></h4>
	On top of lead SNPs, FUMA defines genomic risk loci, including all independent signals that are physically close or overlapping in a single locus.
	First, ind. sig. SNPs which are dependent each other at r2 &ge; 0.1 are assigned to the same genomic risk locus.
	Then, ind. sig. SNPs which are closer than the user defined distance (250 kb by default) are merged into one genomic risk locus.
	The distance between two LD blocks of two ind. sig. SNPs is the distance between the closest SNPs
	(which are in LD of the ind. sig. SNPs at user defined r<sup>2</sup>) from each LD block.<br/>
	Each locus is represented by the top lead SNP which has the minimum P-value in the locus.
	<h4><strong>4. Candidate SNPs (SNPs in LD of ind. sig. SNPs)</strong></h4>
	Candidate SNPs are SNPs that are in LD with any of the ind. sig. SNPs at the user defined r<sup>2</sup>.
	Candidate SNPs, together with the ind. sig. SNPs, are the SNPs that are used to prioritize genes.
	The most left and most right SNPs which are in LD of a ind. sig. SNP define a LD block in which those SNPs are used to compute distance between LD blocks.<br/>
	Note that not all SNPs are necessary in LD with lead SNPs, although they must be in LD with ind. sig. SNPs at the user defined r<sup>2</sup>.<br/>
	All candidate SNPs are annotated and their functions and listed in the "SNPs" table.<br/>
	The higher the threshold r<sup>2</sup>, the less candidate SNPs are identified.
	The number of candidate SNPs can also be controlled by the parameter of the maximum P-value for gwas-tagged SNPs (0.05 by default).
	For example, when r<sup>2</sup> is set at less than 0.6, a parameter of P-value threshold for GWAS tagged SNPs might need to be set at more significant since SNPs with r<sup>2</sup> often have very high P-value.
	<br/><br/>
	<p>
		<strong>Effect of r<sup>2</sup> parameter</strong><br/>
		<img src="{!! URL::asset('/image/r2-1.png') !!}" style="width:90%"/><br/>
	</p>
</div>
