<h3 id="chromatin-interactions">Chromatin interaction data and mapping</h3>
In this section, build in chromatin interaction data, file format of custom chromatin interaction matrices and
details of chromatin interaction mapping are described.
Since chromatin interaction mapping is more complicated than other two mappings (positional and eQTL), please read this section carefully.
<div style="padding-left: 40px;">
	<h4><strong>Terminology</strong></h4>
	<p>
		<strong style="color:red">Region 1</strong><br/>
		One end of a significant interaction which overlap with one of the candidate SNPs (independent significant SNPs and SNPs which are in LD of them).
		This region is always overlap with one of the genomic risk loci identified by FUMA.<br/>
		<strong style="color:red">Region 2</strong><br>
		Another end of the significant interaction.
		This region is used to map to genes.
		Region 2 could also be overlapped with one of the genomic risk loci.<br/>
		<img src="{{ URL::asset('/image/ciMapTerm.png') }}" style="width: 70%; align: middle;">
	</p>
	<h4><strong>Direction of interactions</strong></h4>
	<p>
		Input files of chromatin interaction consist of 7 columns:
		chr1, start1, end1, chr2, start2, end2, FDR (or other score).
		For loops identified by HiC, there is no directionality, i.e. both directions (chr1:start1-end1 <-> chr2:start2-end2)
		were considered regardless of the order in the file.
		For enhancer-promoter (EP) links, only one way (enhancer -> promoter) is considered for the mapping.
		The directionality is specified for each dataset below.
	</p>
	<h4><strong>Build in chromatin interaction data</strong></h4>
	<p><strong>1. Hi-C of 21 tissue/cell types from <a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE87112">GSE87112</a>.</strong><br/>
		Pre-processed significant loops computed by Fit-Hi-C were obtained from GSE87112.
		Loops were filtered at FDR 0.05. For mapping, loops can be further filter by the user defined FDR threshold.
		Both directions are considered.
		Available tissue/cell types are listed below.<br/>
		<ul>
			<li>Adrenal</li>
			<li>Aorta</li>
			<li>Bladder</li>
			<li>Dorsolateral Prefrontal Cortex</li>
			<li>Hippocampus</li>
			<li>Left Ventricle</li>
			<li>Liver</li>
			<li>Lung</li>
			<li>Ovary</li>
			<li>Pancreas</li>
			<li>Psoas</li>
			<li>Right Ventricle</li>
			<li>Small Bowel</li>
			<li>Spleen</li>
			<li>GM12878</li>
			<li>IMR90</li>
			<li>Mesenchymal Stem Cell</li>
			<li>Mesendoderm</li>
			<li>Neural Progenitor Cell</li>
			<li>Trophoblast-like Cell</li>
			<li>hESC</li>
		</ul>
	</p>
	<p>
		<strong>2. Hi-C loops from Giusti-Rodriguez et al. 2019</strong><br/>
		Pre-processed enhancer-promoter and promoter-promoter interactions based on
		HiC data for adult and fetal human brain samples.
		The data was provided by Prof. Patric F. Sullivan.
		Only significant interaction with P < 2.31e-11 (after Bonferroni correction) were included.
		Both directions are considered.
	</p>
	<p>
		<strong>3. Hi-C based data from PsychENCODE</strong><br/>
		3.1 Enhancer-Promoter links based on Hi-C<br/>
		The data was downloaded from <a target="_blank" href="http://resource.psychencode.org/">PsychENCODE resource</a>
		(file: INT-16_HiC_EP_linkages.csv).<br/>
		Promoter regions were defined as 1000 around the provided TSS site.
		Since there is no P-value/FDR/score, all interactions were assigned to 0.
		Only one way (enhancer -> promoter) is considered.<br/>
		3.2 Promoter anchored Hi-C loops<br/>
		The data was downloaded from <a target="_blank" href="http://resource.psychencode.org/">PsychENCODE resource</a>
		(file: Promoter-anchored_chromatin_loops.bed).<br/>
		Since there is no P-value/FDR/score, all interactions were assigned to 0.
		Only one way (region -> promoter) is considered.
	</p>
	<p>
		<strong>4. Enhancer-Promoter correlations from FANTOM5</strong>
		The data was downloaded from <a target="_blank" href="http://slidebase.binf.ku.dk/human_enhancers/presets">FANTOM5 human Enhancer Tracks</a>
		(file: hg19_enhancer_promoter_correlations_distances_cell_type.txt and
		hg19_enhancer_promoter_correlations_distances_organ.txt).
		Only one way (enhancer -> promoter) is considered.
	</p>
	<h4><strong>Custom chromatin interaction matrices file format</strong></h4>
	<p><strong>1. Input file format</strong><br/>
		The chromatin interaction files should have the following 7 columns in the order as listed below.
		Header line is mandatory but the column names do not need to be the same as the below as long as the order is the same.
		Delimiter should be tab or white space(s).
		<span style="color:red;">The input file should be gzipped and named as "(name_of_data).txt.gz"</span> in which "(name_of_data)" will be used in the result table and regional plot.<br/>
		<br/>Columns:
		<ol>
			<li>chromosome of region 1</li>
			<li>start position of region 1</li>
			<li>end position of region 1</li>
			<li>chromosome of region 2</li>
			<li>start position of region 2</li>
			<li>end position of region 2</li>
			<li>FDR</li>
		</ol>
		Example:<br/>
		<span style="font-family:monospace;">
			chr1	start1	end1	chr2	start2	end2	FDR<br/>
			1	2920001	2960000	1	3160001	3200000	0.03186403<br/>
			1	4160001	4200000	1	5880001	5920000	5.3e-8<br/>
			1	4520001	4560000	3	83200001	83240000	0.03920674<br/>
		</span>
		<br/>
		<span class="info"><i class="fa fa-info"></i>
			Chromosome can be coded as string like "chr1" and "chrX" which will be converted into integer.
		</span><br/>
		<span class="info"><i class="fa fa-info"></i>
			Order of regions does not matter, unless a word "oneway" is in the file name (e.g. hic_loops_oneway.txt.gz).
			In that case only one direction (1st region -> 2nd region) is considered.
		</span><br/>
		<span class="info"><i class="fa fa-info"></i>
			Inter-chromosomal interactions can be encoded in the same file by specifying chromosome of region 1 and region 2.
		</span><br/>
		<span class="info"><i class="fa fa-info"></i>
			The column of FDR will be used to filter interaction by the user defined threshold.
 		</span><br/>
		<span class="info"><i class="fa fa-info"></i>
			The maximum size of each file is 600Mb. If the file is larger than this, please filter interactions or split them into multiple files.
		</span><br/>
		<br/>
		<strong>2. Data types</strong><br/>
		When uploading custom chromatin interaction matrices, users can specify the type of data such as Hi-C or ChIA-PET.
		Specifying the data type is not mandatory since it is only used to specify in the result table and regional plot for convenience.
		<br/><br/>
		<strong>3. Filtering of chromatin interactions</strong><br/>
		The 7th column (FDR) will be used to filter interactions.
		To prevent from this filtering, either set filtering threshold to 1 or assign 0 to the FDR column.
		Technically, the 7th column does not have to be FDR but any other scores.
		When one prefers to use different score or nominal P-value, that is also possible by setting proper filtering threshold.
		Note that, interactions will be filtered on which have score less than or equal to the threshold.
		<br/>
	</p>
	<h4><strong>Enhancer and promoter regions</strong></h4>
	<p>
		Enhancer and promoter regions were obtained from Roadmap Epigenomics Projects for 111 epigenomes.
		Those regions were predicted using DNase peaks and core 15-state chromatin state model.
		Please refer <a target="_blank" href="http://egg2.wustl.edu/roadmap/web_portal/DNase_reg.html#delieation">here</a> for details. <br/>
		For selected epigenomes, enhancer regions are annotated to region 1 and promoter regions are annotated to region 2.
		Dyadic enhancer/promoter regions are annotated for both. <br/>
		Annotated enhancer and promoter regions can be used to filter SNPs or mapped genes which is described in the next section.
	</p>
	<h4><strong>Chromatin interaction mapping</strong></h4>
	<p><strong>1. Basic mapping (without filtering)</strong><br/>
		Chromatin interaction mapping is performed with significant chromatin interactions at the user defined threshold.
		Regions 2 is mapped to genes whose promoter regions (250bp up- and 50bp down-stream of the TSS by default) are overlapped with the region 2.
		Those genes were considered as mapped by candidate SNPs which are overlapped with region 1.<br/>
		In the case there is not genes in region 2, those interactions are not mapped to any genes.<br/>
		<img src="{{ URL::asset('/image/ciMap1.png') }}" style="width: 60%; align: middle;">
		<br/><br/>
		<strong>2. Enhancer filtering</strong><br/>
		When enhancers are annotated to region 1, user can select the option to filter candidate SNPs on such that are overlapped with enhancer regions of selected epigenomes.
		Note that, in the result table, all significant interactions are included but not all are necessary used for mapping.<br/>
		<img src="{{ URL::asset('/image/ciMap2.png') }}" style="width: 60%; align: middle;">
		<br/><br/>
		<strong>3. Promoter filtering</strong><br/>
		When promoters are annotated to region 2, user can select the option to limit the chromatin interaction mapping to only genes whose promoter regions are overlapped with annotated promoter regions of selected epigenomes.
		Note that, in the result table, all significant interactions are included but not all are necessary mapped to genes.<br/>
		<img src="{{ URL::asset('/image/ciMap3.png') }}" style="width: 60%; align: middle;">
		<br/><br/>
		<span class="info"><i class="fa fa-info"></i>
			In very rare cares, when the promoter filtering is activated, genes whose promoter regions (250bp up- and 500bp down-stream of TSS) do not overlap with region 2 but do overlap with promoters from Roadmap that are overlapping with region 2 are mapped.
			In this case, these genes are not in "ci.txt" file but in "ciProm.txt" file which can be linked to "ci.txt" by region 2.
		</span>
	</p>
</div>
