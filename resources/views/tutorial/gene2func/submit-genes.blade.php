<h3 id="submit-genes">Submit genes</h3>
<div style="padding-left: 40px;">
	<h4><strong>Option 1. Use mapped genes from SNP2GENE</strong></h4>
	<p>If you want to use mapped genes from SNP2GENE, just click a button in Mapped genes panel of the result page.
		It will open a new tab and automatically starts analyses.
		This will take all mapped genes and use background genes with selected gene types for gene mapping (such as "protein-coding" or "ncRNA").
		The method for multiple testing correction (FDR BH), adjusted P-value cutoff (0.05) and minimum number of overlapped genes (2) are set at default values.
		These options can be adjusted by resubmitting your query (click "Submit" button in New Query tab).
	</p>
	<img src="{!! URL::asset('/image/snp2genejump.png') !!}" style="width:70%"/><br/>
	<br/>
	<h4><strong>Option 2. Use a list of genes of interest</strong></h4>
	<p>To analyze a custom list of genes, you have to prepare a list of genes as either ENSG ID, entrez ID or gene symbol.
		Genes can be provided in the text are (one gene per line) or by uploading a file in the left panel. When you upload a file, genes have to be in the first column with a header.
		Header can be anything (even just a new line is fine) but FUMA will start reading your genes from the second row.
	</p>
	<p>To analyze your genes, you need to specify background genes, which are used in the 2x2 enrichment tests.
		You can choose from the provided gene types.
		Alternatively, you can provide a custom list of background genes.
		Please provide this list either in the text area or by uploading a file of the right panel.
		File format should be the same as described for genes of interest.
	</p>
	<img src="{!! URL::asset('/image/gene2funcSubmit.png') !!}" style="width:60%"/>
</div>
