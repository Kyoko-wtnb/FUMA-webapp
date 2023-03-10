<h3 id="parameters">Parameters</h3>
<p>Annotation and prioritization depends on several settings, which can be adjusted if desired.
	The default settings will result in performing naive positional mapping which maps all independent lead SNPs and SNPs in LD to genes up to 10kb apart.
	It does not include eQTL mapping by default, and it also does not filter on specific functional consequences of SNPs.
	If for example you are interested in prioritizing genes only when they are indicated by an eQTL that is in LD with a significant lead SNP, or by exonic SNPs, then you need to adjust the parameter settings.
</p>
<p>Each of user inputs and parameters have status as described below.
	Please make sure all input has non-red status, otherwise the submit button will not be activated.<br/><br/>
	<span class="alert alert-info" style="padding: 5px;">
		This is for optional inputs/parameters.
	</span><br/><br/>
	<span class="alert alert-success" style="padding: 5px;">
		This is the message if everything is fine.
	</span><br/><br/>
	<span class="alert alert-danger" style="padding: 5px;">
		This is the message if the input/parameter is mandatory and not given or invalid input is given.
	</span><br/><br/>
	<span class="alert alert-warning" style="padding: 5px;">
		This is the warning message for the input/parameter. Please check your input settings.
	</span><br/><br/>
</p>
<p>In this section, every parameter that can be adjusted will be described in detail.
</p>

<div style="margin-left: 40px;">
	<h4 id="input-files"><strong>1. Input files</strong></h4>
	<table class="table table-bordered">
		<thead>
			<tr>
			<th style="width: 20%">Parameter</th>
			<th>Mandatory</th>
			<th>Description</th>
			<th>Type</th>
			<th>Default</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>GWAS summary statistics</td>
				<td>Mandatory</td>
				<td>Input file of GWAS summary statistics.
					Plain text file or zipped or gzipped files are acceptable.
					The maximum file size which can be uploaded is 600Mb.
					As well as full results of GWAS summary statistics, subset of results can also be used.
					e.g. If you would like to look up specific SNPs, you can filter out other SNPs.
					Please refer to the <a class="inpage" href="{{ Config::get('app. subdir') }}/tutorial#prepare-input-files">Input files</a> section for specific file format.
				</td>
				<td>File upload</td>
				<td>none</td>
			</tr>
			<tr>
				<td>Pre-defined lead SNPs</td>
				<td>Optional</td>
				<td>Optional pre-defined lead SNPs. The file should have 3 columns, rsID, chromosome and position.</td>
				<td>File upload</td>
				<td>none</td>
			</tr>
			<tr>
				<td>Identify additional lead SNPs</td>
				<td>Optional only when predefined lead SNPs are provided</td>
				<td>If this option is CHECKED, FUMA will identify additional independent lead SNPs after defining the LD block for pre-defined lead SNPs.
					Otherwise, only given lead SNPs and SNPs in LD of them will be used for further annotations.
				</td>
				<td>Check</td>
				<td>Checked</td>
			</tr>
			<tr>
				<td>Pre-defined genetic region</td>
				<td>Optional</td>
				<td>Optional pre-defined genomic regions.<br/>
					FUMA only looks at provided regions to identify lead SNPs and SNPs in LD of them.
					If you are only interested in specific regions, this option will increase the speed of process.
				</td>
				<td>File upload</td>
				<td>none</td>
			</tr>
		</tbody>
	</table>
</div>
<br/>
<div style="margin-left: 40px;">
	<h4><strong>2. Parameters for lead SNPs and candidate SNPs identification</strong></h4>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Mandatory</th>
				<th style="width: 40%;">Description</th>
				<th>Type</th>
				<th>Default</th>
				<th style="width: 20%;">Direction</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Sample size (N)</td>
				<td>Mandatory</td>
				<td>The total number of individuals in the GWAS or the number of individuals per SNP.
					This is only used for MAGMA to compute the gene-based P-values.
					For total sample size, input should be an integer.
					When the input file of GWAS summary statistics contains a column of sample size per SNP, the column name can be provided in the second text box.<br/>
					<span class="info"><i class="fa fa-info"></i> When column name is provided, please make sure that the column only contains integers (no float or scientific notation).
						If there are any float values, they will be rounded up by FUMA.
					</span>
				</td>
				<td>Integer or text</td>
				<td>none</td>
				<td>Does not affect any candidates</td>
			</tr>
			<tr>
				<td>Maximum lead SNP P-value (&lt;)</td>
				<td>Mandatory</td>
				<td>FUMA identifies lead SNPs with P-value less than or equal to this threshold and independent from each other.
				</td>
				<td>numeric</td>
				<td>5e-8</td>
				<td><span style="color: blue;">lower</span>: decrease #lead SNPs. <br/>
					<span style="color:red;">higher</span>: increase #lead SNPs.
				</td>
			</tr>
			<tr>
				<td>Maximum GWAS P-value (&lt;)</td>
				<td>Mandatory</td>
				<td>This is the P-value threshold for candidate SNPs in LD of independent significant SNPs.
					This will be applied only for GWAS-tagged SNPs as SNPs which do not exist in the GWAS input but are extracted from 1000 genomes reference do not have P-value.
				</td>
				<td>numeric</td>
				<td>0.05</td>
				<td><span style="color:red;">higher</span>: decrease #candidate SNPs.<br/>
					<span style="color: blue;">lower</span>: increase #candidate SNPs.
				</td>
			</tr>
			<tr>
				<td>r<sup>2</sup> threshold for independent significant SNPs (&ge;)</td>
				<td>Mandatory</td>
				<td>The minimum r<sup>2</sup> for defining independent significant SNPs, which is used to determine the borders of the genomic risk loci.
					SNPs with r<sup>2</sup> &ge; user defined threshold with any of the detected independent significant SNPs will be included for further annotations and are used fro gene prioritisation.
				</td>
				<td>numeric</td>
				<td>0.6</td>
				<td><span style="color:red;">higher</span>: decrease #candidate SNPs and increase #independent significant SNPs.<br/>
					<span style="color: blue;">lower</span>: increase #candidate SNPs and decrease #independent significant SNPs.
				</td>
			</tr>
			<tr>
				<td>2nd r<sup>2</sup> threshold for lead SNPs (&ge;)</td>
				<td>Mandatory</td>
				<td>The minimum r<sup>2</sup> for defining lead SNPs, which is used for the second clumping (clumping of the independent significant SNPs).
					Note that when this threshold is same as the first r<sup>2</sup> threshold, lead SNPs are identical to independent significant SNPs.
				</td>
				<td>numeric</td>
				<td>0.1</td>
				<td><span style="color:red;">higher</span>: increase #lead SNPs.<br/>
					<span style="color: blue;">lower</span>: decrease #lead SNPs.
				</td>
			</tr>
			<tr>
				<td>Reference panel</td>
				<td>Mandatory</td>
				<td>The reference panel to compute r<sup>2</sup> and MAF.
					Five populations from 1000 genomes Phase 3 and
					3 versions of UK Biobank are available.
					See <a href="{{ Config::get('app.subdir') }}/tutorial#refpanel">here</a> for details.
				</td>
				<td>Select</td>
				<td>1000G Phase EUR</td>
				<td>-</td>
			</tr>
			<tr>
				<td>Include variants from reference panel</td>
				<td>Mandatory</td>
				<td>If Yes, all SNPs in strong LD with any of independent significant SNPs
					including non-GWAS-tagged SNPs will be included and used for
					gene mapping.
				</td>
				<td>Yes/No</td>
				<td>Yes</td>
				<td>-</td>
			</tr>
			<tr>
				<td>Minimum MAF (&ge;)</td>
				<td>Mandatory</td>
				<td>The minimum Minor Allele Frequency to be included in annotation and prioritisation.
					MAF is based the user selected reference panel.
					This filter also applies to lead SNPs.
					If there is any pre-defined lead SNPs with MAF less than this threshold, those SNPs will be skipped.
					When this value is 0 (by default), SNPs with MAF>0 are considered.
				</td>
				<td>numeric</td>
				<td>0</td>
				<td><span style="color:red;">higher</span>: decrease #candidate SNPs.<br/>
					<span style="color: blue;">lower</span>: increase #candidate SNPs.
				</td>
			</tr>
			<tr>
				<td>Maximum distance of LD blocks to merge (&le;)</td>
				<td>Mandatory</td>
				<td>This is the maximum distance between LD blocks of independent significant SNPs to merge into a single genomic locus.
					When this is set at 0, only physically overlapping LD blocks are merged.
					Defining genomic loci does not affect identifying which SNPs fulfil selection criteria to be used for annotation and prioritization.
					It will only result in a different number of reported risk loci, which can be desired when certain loci are partly overlapping or physically very close.
				</td>
				<td>numeric</td>
				<td>250kb</td>
				<td><span style="color:red;">higher</span>: decrease #genomic loci.<br/>
					<span style="color: blue;">lower</span>: increase #genomic loci.
				</td>
			</tr>
		</tbody>
	</table>
</div>
<br/>
<div style="margin-left: 40px;">
	<h4><strong>3. Parameters for gene mapping</strong></h4>
	<p>There are two options for gene mapping; positional and eQTL mappings. By default, positional mapping with maximum distance 10kb is performed.
		Since parameters in this section largely affect the result of mapped genes, please set carefully.
	</p>
	<h4><strong>3.1 Positional mapping</strong></h4>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Mandatory</th>
				<th style="width:40%;">Description</th>
				<th>Type</th>
				<th>Default</th>
				<th style="width:20%;">Direction</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Positional mapping</td>
				<td>Optional</td>
				<td>Check this option to perform positional mapping.
					Positional mapping is based on ANNOVAR annotations by specifying the maximum distance between SNPs and genes or based on functional consequences of SNPs on genes.
					These parameters can be specified in the option below.
				</td>
				<td>Check</td>
				<td>Checked</td>
				<td>-</td>
			</tr>
			<tr>
				<td>Distance to genes or functional consequences of SNPs on genes to map</td>
				<td>Mandatory if positional mapping is activated.</td>
				<td>Positional mapping criterion either map SNPs to genes based on physical distances or functional consequences of SNPs on genes. <br/>
					When maximum distance is provided SNPs are mapped to genes based on the distance given the user defined maximum distance.
					Alternatively, specific functional consequences of SNPs on genes can be selected which filtered SNPs to map to genes.
					Note that when functional consequences are selected, all SNPs are locating on the gene body (distance 0) except upstream and downstream SNPs which are up to 1kb apart from TSS or TSE. <br/>
					<span class="info"><i class="fa fa-info"></i>
						When the maximum distance is set at > 0kb and < 1kb all upstream and downstream SNPs are included since the actual distance is not provided by ANNOVAR.
						Therefore, the maximum distance > 0kb and < 1kb is same as the maximum distance 1 kb.
					</span>
					<span class="info"><i class="fa fa-info"></i>
						For SNPs which are locating on a genomic region where multiple genes are overlapped, ANNOVAR has its own prioritization criteria to report the most deleterious function.
						For those SNPs, only prioritized annotations are used.
					</span>
				</td>
				<td>Integer / Multiple selection</td>
				<td>Maximum distance 10 kb</td>
				<td>-</td>
			</tr>
		</tbody>
	</table>

	<h4><strong>3.2 eQTL mapping</strong></h4>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Mandatory</th>
				<th style="width:40%;">Description</th>
				<th>Type</th>
				<th>Default</th>
				<th style="width:20%;">Direction</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>eQTL mapping</td>
				<td>Optional</td>
				<td>Check this option to perform eQTL mapping.
					eQTL mapping will map SNPs to genes which likely affect expression of those genes up to 1 Mb (cis-eQTL).
					eQTLs are highly tissue specific and tissue types can be selected in the following option.
					eQTL mapping can be used together with positional mapping.
				</td>
				<td>Check</td>
				<td>Unchecked</td>
				<td>-</td>
			</tr>
			<tr>
				<td>Tissue types</td>
				<td>Mandatory if <code>eQTL mapping</code> is CHECKED</td>
				<td>All available tissue types with data sources are shown in the select boxes.
					From FUMA v1.3.0, GTEx v7 became available but GTEx v6 are kept available.
					Therefore, when "all" is selected, both GTEx v6 and v7 are used for mapping.
					For detail of eQTL data resources, please refer to the <a href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">eQTL</a> section in this tutorial.
				</td>
				<td>Multiple selection</td>
				<td>none</td>
				<td>-</td>
			</tr>
			<tr>
				<td>eQTL maximum P-value (&le;)</td>
				<td>Optional</td>
				<td>The P-value threshold of eQTLs.
					Two options are available, <code>Use only significant snp-gene pairs</code> or nominal P-value threshold.
					When <code>Use only significant snp-gene pairs</code> is checked, only eQTLs with FDR &le; 0.05 will be used.
					Otherwise, defined nominal P-value is used to filter eQTLs.<br/>
					<span class="info"><i class="fa fa-info"></i>
						Some of eQTL data source only contained eQTLs with a certain FDR threshold.
						Please refer to the <a href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">eQTLs</a> section for details of each data sources.
					</span>
				</td>
				<td>Check / Numeric</td>
				<td>Checked / 1e-3</td>
				<td><span style="color: blue;">lower</span>: increase #eQTLs and #mapped genes.<br/>
					<span style="color: red;">higher</span>: decrease #eQTLs and #mapped genes.
				</td>
			</tr>
		</tbody>
	</table>

	<h4><strong>3.3 Chromatin interaction mapping</strong></h4>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Mandatory</th>
				<th style="width:40%;">Description</th>
				<th>Type</th>
				<th>Default</th>
				<th style="width:20%;">Direction</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>chromatin interaction mapping</td>
				<td>Optional</td>
				<td>Check this option to perform chromatin interaction mapping.
				</td>
				<td>Check</td>
				<td>Unchecked</td>
				<td>-</td>
			</tr>
			<tr>
				<td>Builtin chromatin interaction data</td>
				<td>Optional</td>
				<td>Build in chromatin interaction data can be selected in this option.
					Details of available build in data are available in the <a href="{{ Config::get('app.subdir') }}/tutorial#chromatin-interactions">Chromatin interactions</a> section in this tutorial.
				</td>
				<td>Multiple selection</td>
				<td>none</td>
				<td>-</td>
			</tr>
			<tr>
				<td>Custom chromatin interaction matrices</td>
				<td>Optional</td>
				<td>In addition to build in chromatin interaction data, user can upload custom data.
					The data should be pre-computed chromatin loops with significance (ideally FDR but another score can be used, see the Chromatin interactions section for details).
					The file should be gzipped and named as "(name-of-data).txt.gz". Multiple files can be uploaded.
					For each data, user can also provide data type, such as Hi-C, ChIA-PET or C5 which is not mandatory but will be used in the result table and regional plot.
					The file format is described in the <a href="{{ Config::get('app.subdir') }}/tutorial#chromatin-interactions">Chromatin interactions</a> section in this tutorial.<br/>
					<span class="info"><i class="fa fa-info"></i>
						Please avoid uploading more than one file with identical file names. In that case, the files are over-written by the last uploaded one.
					</span>
				</td>
				<td>File upload (multiple)</td>
				<td>none</td>
				<td>-</td>
			</tr>
			<tr>
				<td>FDR threshold (&le;)</td>
				<td>Mandatory if <code>chromatin interaction mapping</code> is CHECKED</td>
				<td>FDR threshold for significant loops.
					The default value is set at 1e-6 which is suggested by <a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27851967">Schmitt et al. (2016)</a><br/>
					<span class="info"><i class="fa fa-info"></i>
						This threshold will be applied both build in and user uploaded chromatin loops.
					</span>
				</td>
				<td>Numeric</td>
				<td>1e-6</td>
				<td><span style="color: blue;">lower</span>: increase #chromatin interactions and #mapped genes.<br/>
					<span style="color: red;">higher</span>: decrease #chromatin interactions and #mapped genes.
				</td>
			</tr>
			<tr>
				<td>Promoter region window</td>
				<td>Mandatory if <code>chromatin interaction mapping</code> is CHECKED</td>
				<td>Promoter regions of genes to map in significantly interacting regions.
					The input format should be "(upstream bp)-(donwstream bp)" from transcription start site (TSS).
					For example, the default "250-500" means that promoter regions are defined as 250bp upstream and 500bp downstream of the TSS.
					By the chromatin interaction mapping, genes whose user defined promoter regions are overlapped with the significantly interacting regions will be mapped.
					Please refer the <a href="{{ Config::get('app.subdir') }}/tutorial#chromatin-interactions">Chromatin interactions</a> section in this tutorial for details.
				</td>
				<td>text</td>
				<td>250-500</td>
				<td><span style="color: blue;">lower</span>: increase #mapped genes.<br/>
					<span style="color: red;">smaller</span>: decrease #mapped genes.
				</td>
			</tr>
			<tr>
				<td>Annotate enhancer/promoter regions (Roadmap 111 epigenomes)</td>
				<td>Optional</td>
				<td>Predicted enhancer and promoter regions from Roadmap epigenomics project for 111 epigenomes can be annotated to significantly interaction regions.
					If any epigenome is not selected, enhancer and promoter regions are not annotated.
					Annotated enhancer/promoter regions can be used to filter SNPs and mapped genes in the next two options.
				</td>
				<td>Multiple selection</td>
				<td>none</td>
				<td>-</td>
			</tr>
			<tr>
				<td>Filter SNPs by enhancers</td>
				<td>Optional</td>
				<td>This option is only available when at least one epigenome is selected in the previous option to annotate enhancer/promoter regions.
					When this option is checked, SNPs are filtered on such that overlap with one of the annotated enhancer regions for chromatin interaction mapping.
					Please refer the <a href="{{ Config::get('app.subdir') }}/tutorial#chromatin-interactions">Chromatin interactions</a> section in this tutorial for details.
				</td>
				<td>Check</td>
				<td>Unchecked</td>
				<td>-</td>
			</tr>
			<tr>
				<td>Filter genes by promoters</td>
				<td>Optional</td>
				<td>This option is only available when at least one epigenome is selected in the previous option to annotate enhancer/promoter regions.
					When this option is checked, chromatin interaction mapping is only performed for genes whose promoter regions are overlap with one of the annotated promoter regions.
					Please refer the <a href="{{ Config::get('app.subdir') }}/tutorial#chromatin-interactions">Chromatin interactions</a> section in this tutorial for details.
				</td>
				<td>Check</td>
				<td>Unchecked</td>
				<td>-</td>
			</tr>
		</tbody>
	</table>

	<h4><strong>3.4 Functional annotation filtering</strong></h4>
	<p>Positional, eQTL and chromatin interaction mappings have the following options separately, for the filtering of SNPs based on functional annotation.
		All filters below apply to selected SNPs in LD with independent significant SNPs that are used to prioritize genes and influence the number of SNPs that are mapped to genes, and consequently influence the number of prioritized genes.
	</p>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Mandatory</th>
				<th style="width:40%;">Description</th>
				<th>Type</th>
				<th>Default</th>
				<th style="width:20%;">Direction</th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<td>CADD score</td>
			<td>Optional</td>
			<td>Check this if you want to perform filtering of SNPs by CADD score.
				This applies to selected SNPs in LD with independent significant SNPs that are used to prioritize genes.
				CADD score is the score of deleteriousness of SNPs predicted by 63 functional annotations.
				12.37 is the threshold to be deleterious suggested by Kicher et al (2014).
				Please refer to the original publication for details from <a href="{{ Config::get('app.subdir') }}/links">links</a>.
			</td>
			<td>Check</td>
			<td>Unchecked</td>
			<td>-</td>
		</tr>
		<tr>
			<td>Minimum CADD score (&ge;)</td>
			<td>Mandatory if <code>CADD score</code> is checked</td>
			<td>The higher the CADD score, the more deleterious.</td>
			<td>numeric</td>
			<td>12.37</td>
			<td><span style="color:red;">higher</span>: less SNPs will be mapped to genes.<br/>
				<span style="color: blue;">lower</span>: more SNPs will be mapped to genes.</td>
			</td>
		</tr>
		<tr>
			<td>RegulomeDB score</td>
			<td>Optional</td>
			<td>Check if you want to perform filtering of SNPs by RegulomeDB score.
				This applies to selected SNPs in LD with independent significant SNPs that are used to prioritize genes.
				RegulomeDB score is a categorical score representing regulatory functionality of SNPs based on eQTLs and chromatin marks.
				Please refer to the original publication for details from <a href="{{ Config::get('app.subdir') }}/links">links</a>.
			</td>
			<td>Check</td>
			<td>Unchecked</td>
			<td>-</td>
		</tr>
		<tr>
			<td>Minimum RegulomeDB score (&ge;)</td>
			<td>Mandatory if <code>RegulomeDB score</code> is checked</td>
			<td>RegulomeDB score is a categorical score from 1a to 7)
				Score 1a means that those SNPs are most likely affecting regulatory elements and 7 means that those SNPs do not have any annotations.
				SNPs are recorded as NA if they are not present in the database.
				SNPs with NA will not be included for filtering on RegulomeDB score.
			</td>
			<td>string</td>
			<td>7</td>
			<td><span style="color:red;">higher</span>: more SNPs will be mapped to genes.<br/>
				<span style="color: blue;">lower</span>: less SNPs will be mapped to genes.</td>
			</td>
		</tr>
		<tr>
			<td>15-core chromatin state</td>
			<td>Optional</td>
			<td>Check if you want to perform filtering of SNPs by chromatin state.
				This applies to selected SNPs in LD with independent significant SNPs that are used to prioritize genes.
				The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states predicted by ChromHMM based on 5 chromatin marks for 127 epigenomes.
			</td>
			<td>Check</td>
			<td>Unchecked</td>
			<td>-</td>
		</tr>
		<tr>
			<td>15-core chromatin state tissue/cell types</td>
			<td>Mandatory if <code>15-core chromatin state</code> is checked</td>
			<td>Multiple tissue/cell types can be selected from the list.</td>
			<td>Multiple selection</td>
			<td>none</td>
			<td>-</td>
		</tr>
		<tr>
			<td>Maximum state of chromatin(&le;)</td>
			<td>Mandatory if <code>15-core chromatin state</code> is checked</td>
			<td>The maximum state to filter SNPs. Between 1 and 15.
				Generally, between 1 and 7 is open state.
			</td>
			<td>numeric</td>
			<td>7</td>
			<td><span style="color:red;">higher</span>: more SNPs will be mapped to genes.<br/>
				<span style="color: blue;">lower</span>: less SNPs will be mapped to genes.</td>
			</td>
		</tr>
		<tr>
			<td>Method for 15-core chromatin state filtering</td>
			<td>Mandatory if <code>15-core chromatin state</code> is checked</td>
			<td>When multiple tissue/cell types are selected, either
				<code>any</code> (filtered on SNPs which have state above than threshold in any of selected tissue/cell types),
				<code>majority</code> (filtered on SNPs which have state above than threshold in majority (&ge;50%) of selected tissue/cell type), or
				<code>all</code> (filtered on SNPs which have state above than threshold in all of selected tissue/cell type).
			</td>
			<td>Selection</td>
			<td>any</td>
			<td>-</td>
		</tr>
		<tr>
			<td>Annotation datasets</td>
			<td>Optional</td>
			<td>
				Additional functional annotations can be annotated to candidate SNPs.
				All available data are regional based annotation (bed file format).
			</td>
			<td>Multiple selection</td>
			<td>none</td>
			<td>-</td>
		</tr>
		<tr>
			<td>Annotation filtering method</td>
			<td>Mandatory if any of <code>Annotation datasets</code> is selected.</td>
			<td>
				By default, SNPs are not filtered by the annotations selected in <code>Annotation datasets</code>.
				To filter SNPs based on the selected annotation, select this options from
				<code>any</code> (filtered on SNPs which are overlapping with any selected annotations),
				<code>majority</code> (filtered on SNPs which are overlapping with majority (&ge;50%) of selected annotations), or
				<code>all</code> (filtered on SNPs which are overlapping with all of selected annotations).
			</td>
			<td>Selection</td>
			<td>No filtering</td>
			<td>-</td>
		</tr>
		</tbody>
	</table>
	<br/>
</div>

<div style="margin-left: 40px;">
	<h4><strong>4. Gene types</strong></h4>
	<p>Biotype of genes to map can be selected. Please refer to Ensembl for details of biotypes.</p>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Mandatory</th>
				<th>Description</th>
				<th>Type</th>
				<th>Default</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Gene type</td>
				<td>Mandatory</td>
				<td>Gene type to map.
				This is based on gene_biotype obtained from BioMart of Ensembl build 85.
				Please see <a href="http://vega.sanger.ac.uk/info/about/gene_and_transcript_types.html">here</a> for details
				</td>
				<td>Multiple selection.</td>
				<td>Protein coding genes.</td>
			</tr>
		</tbody>
	</table>
	<br/>
</div>

<div style="margin-left: 40px;">
	<h4><strong>5. MHC region</strong></h4>
	<p>The MHC region is often excluded due to its complicated LD structure.
		Therefore, this option is checked by default.
		Please uncheck to include MHC region.
		Note that it doesn't change any results if there is no significant hit in the MHC region.
	</p>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Mandatory</th>
				<th>Description</th>
				<th>Type</th>
				<th>Default</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Exclude MHC region</td>
				<td>Optional</td>
				<td>Check if you want to exclude the MHC region. The default region is defined as between "MOG" and "COL11A2" genes.</td>
				<td>Check</td>
				<td>Checked</td>
			</tr>
			<tr>
				<td>Options for excluding MHC region</td>
				<td>Optional</td>
				<td>MHC region can be excluded only from either annotations or MAGMA gene analysis, or from both by selecting this option.</td>
				<td>Select</td>
				<td>Only from annotations</td>
			</tr>
			<tr>
				<td>Extended MHC region</td>
				<td>Optional</td>
				<td>User specified MHC region to exclude (for extended or shorter region).
					The input format should be like "25000000-34000000" on hg19.
				</td>
				<td>Text</td>
				<td>Null</td>
			</tr>
		</tbody>
	</table>
	<br/>
</div>

<div style="margin-left: 40px;">
	<h4><strong>6. MAGMA analysis</strong></h4>
	<p>
		Starting from FUMA version 1.5.1, user needs to check the magma checkbox to perform MAGMA. 
		MAGMA gene and gene-set analyses are performed for the input summary statistics.
		Gene expression data sets for MAGMA gene expression analysis can be also selected from here.
	</p>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Mandatory</th>
				<th>Description</th>
				<th>Type</th>
				<th>Default</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Perform MAGMA</td>
				<td>Optional</td>
				<td>UNCHECK to SKIP MAGMA analyses.</td>
				<td>Check</td>
				<td>Checked</td>
			</tr>
			<tr>
				<td>MAGMA gene annotation window</td>
				<td>Mandatory when <code>MAGMA</code> is active.</td>
				<td>The window of the genes to assign SNPs (symmetric).
					e.g. when 5kb is selected, SNPs within 5kb window of a gene (both side)
					will be assigned to that gene.
					The option is available from 0, 5, 10, 15, 20kb window.
				</td>
				<td>Select</td>
				<td>0kb from both side of the genes</td>
			</tr>
			<tr>
				<td>MAGMA gene expression analysis</td>
				<td>Mandatory when <code>MAGMA</code> is active.</td>
				<td>Gene expression data sets used for MAGMA gene-property analysis to test
					positive association between genetic associations and gene expression in a given label.
				</td>
				<td>Select</td>
				<td>GTEx v6</td>
			</tr>
		</tbody>
	</table>
	<br/>
</div>

<div style="margin-left: 40px;">
	<h4><strong>7. Title of job submission</strong></h4>
	<p>
		Title of job submission can be provided at above the "Submit Job" button.
		This is not mandatory but this would be useful to keep track your jobs.
	</p>
</div>
