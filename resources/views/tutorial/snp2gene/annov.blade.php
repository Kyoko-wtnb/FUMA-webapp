<h3 id="annov">ANNOVAR enrichment test</h3>
Enrichment of functional consequences of SNPs are tested agains the user selected reference panel.
All SNPs that are in LD with one of the independent significant SNPs are annotated by ANNOVAR.
SNPs can be annotated to multiple annotations, and those SNPs are counted twice.
If SNPs have same annotations assigned to more than one gene,
those SNPs are counted once
(i.e. only unique combinations of SNP-annotation are counted).
There might SNPs that are not annotated by ANNOVAR which are not included
in the enrichment test.
Thus, sum of counts across annotation ("count" column in "annov.stats.txt" file) is not
necessary the same as the number of SNPs in "snps.txt" file.
Same applies to the counts for reference panel (sum of "ref.count" is not
necessary the same as the number of SNPs mentioned in the "Reference panel" section of this tutorial).
<br/>
Enrichment value is computed as (proportion of SNPs with an annotation)
/(proportion of SNPs with an annotation relative to all available SNPs in the reference panel).
Fisher's exact test (two side) is performed for each annotation as below.
<br/>
<code class="codebox">
	# count: a vector of the number of SNPs for each annotation<br/>
	# ref.count: a vector of the number of SNPs for each annotation for all SNPs in the reference panel<br/>
	N = sum(ref.count)<br/>
	n = sum(count)<br/>
	# to compute P-value of the first annotation with R, for example<br/>
	fisher.test(matrix(c(count[1], n-count[1], ref.count[1]-count[1], N-n-ref.count[1]), ncol=2))<br/>
</code>
