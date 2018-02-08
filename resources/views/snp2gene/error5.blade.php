<!-- ERROR:005 -->
<div class="sidePanel container" style="padding-top:50px;" id="error5">
	<h4 style="color: #00004d">ERROR:005 No candidate SNPs were found</h4>
	<div id="error5mes">
		<p>Error because of no significant SNP in the GWAS summary statistics.<br/>
			To obtain annotations; use a less stringent P-value threshold for lead SNPs or provide predefined lead SNPs.<br/>
		</p>
	</div>
	<br/>
	<h4 style="color: #00004d">Top 10 SNPs in the input file</h4>
	<span class="info"><i class="fa fa-info"></i>
		Top 10 significant SNPs of the input file.
		Refer the following P-value to set threshold for lead SNPs in the next submission.<br/>
		Note that deccreasing MAF threshold may lead to more hits (default MAF &ge; 0.01). <br/>
		Note that the MHC region is excluded by default. Check this option to include MHC in the analysis.
	</span>
	<br/>
	<table class="table table-bordered" id="topSNPs"></table>
</div>
