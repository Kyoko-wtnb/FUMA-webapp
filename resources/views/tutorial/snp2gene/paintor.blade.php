<h3 id="paintor">PAINTOR</h3>
PAINTOR is a statistical fina-mapping method that integrates functional genomic
data given genetic associations from GWAS to prioritize SNPs.
The later version of software (PAINTOR V3.0) is implemented in FUMA which can be
optionally performed for genomic risk loci identified by FUMA.
Preparation of input files for PAINTOR is automated in FUMA, therefore users do not need to provide additional files for PAINTOR.
Each process to perform PAINTO is described in this section.<br/>
Please refer <a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/25357204">Kichaev et al. (2014)</a>
for detail methods. The software is available from <a target="_blank", href="https://github.com/gkichaev/PAINTOR_V3.0">https://github.com/gkichaev/PAINTOR_V3.0</a>.
<div style="padding-left: 40px;">
	<h4><strong>1. Locus and SNPs</strong></h4>
	Loci used in PAINTOR are the same as the gnomic risk loci identified by FUMA.
	PAINTOR is only performed for loci with at least two SNPs after filtering (see 3. LD matrix for details).
	Note that only SNPs which exist in the user input file (SNPs with GWAS summary statistics) are used in PAINTOR.
	If there is no locus with more than two SNPs, PAINTOR is not performed (and returns error message in the result page).
	<h4><strong>2. Z scores</strong></h4>
	PAINTOR requires Z score of genetic associaitons to compute posterior probability to be causal.
	When Z score is provided in the user input GWAS summary statistics, FUMA uses it as an input for PAINTOR.
	If not, Z score is computed from the P-value and direction is assigned based on signed summary statistics such as Beta or OR.
	Therefore, unless Z score is provided in the input GWAS summary statistics, P-value and signed summary statistics are necessary to run PAINTOR.
	Otherwise, PAINTOR is not performed (and returns error message in the result page).
	<h4><strong>3. LD matrix</strong></h4>
	LD matrix is pairwise correlation (r) of SNPs in a locus computed based on 1000 genomes project phase 3 of user defined population.
	During this process, SNPs which do not exist in reference panel will be removed from PAINTOR input.
	Since the LD matrix consists of correlation r, it is assumed that effect allele is alternative allele in the reference panel.
	Therefore Z-score is polarized so that non-effect/effect alleles match with major/minor alleles in reference panel.
	SNPs whose one of the allele does not match with reference panel are excluded from PAINTOR.
	Optionally, A/T and C/G SNPs can be excluded due to ambiguous alleles.
	<h4><strong>4. Functional annotations</strong></h4>
	One of the advantages of PAINTOR compared to oather fine-mapping methods is the integration with functional annotations.
	Fairly comprehensive annotations are provided at <a target="_blank", href="https://github.com/gkichaev/PAINTOR_V3.0">https://github.com/gkichaev/PAINTOR_V3.0</a>.
	All of those annotations are available in FUMA and
	user can optionally select multiple of them to include into the model.
	By default, PAINTOR is performed without any functional annotation. <br/>
	The complete list of available annotations can be accessed from <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial/paintor/annotations">here</a>.<br/>
	<br/>
	<div class="panel panel-default">
      <div class="panel-heading">
        <a href="#paintorAnnotTable" data-toggle="collapse">Functional annotations for PAINTOR</a><br/>
      </div>
      <div id="paintorAnnotTable" class="panel-body collapse">
		<table class="table table-bordered">
	  		<thead>
	  			<tr>
	  				<th>﻿Collection</th><th>Description</th><th>Amount</th>
	  			</tr>
	  		</thead>
	  		<tbody>
	  			<tr>
	  				<td>FANTOM5</td><td>enhancer elements from fantom5 consortium. (114 Annotations)</td><td>114 annotations</td>
	  			</tr>
	  			<tr>
	  				<td>GeneElements_Gencode</td><td>Gene Elements from GenCode (8 Annotations)</td><td>8 annotations</td>
	  			</tr>
	  			<tr>
	  				<td>Hnisz_Cell2013_SuperEnhancer</td><td>Super Enhancers (88 annotations/cell types)</td><td>88 annotations/cell types</td>
	  			</tr>
	  			<tr>
	  				<td>Maurano_Science2012_DHS</td><td>DHS (352 annotations, some experimental replicates)</td><td>352, some experimental replicates</td>
	  			</tr>
	  			<tr>
	  				<td>RoadMap_Assayed_NarrowPeak</td><td>CHIP-seq peaks assayed in the RoadMap</td><td>1057 total annotations, 127 cell types, variable number of marks</td>
	  			</tr>
	  			<tr>
	  				<td>Roadmap_ChromeHMM_15state</td><td>15 State CHROME-HMM model</td><td>1905 annotations, 15 states, 127 cell types</td>
	  			</tr>
	  			<tr>
	  				<td>RoadMap_Dyadic:</td><td>Overlap of DHS with BivFlnk</td><td>111 annotations/cell types</td>
	  			</tr>
	  			<tr>
	  				<td>RoadMap_Enhancers:</td><td>Overlap of DHS with EnhG, Enh, and EnhBiv for enhancers</td><td>111 annottions/cell types</td>
	  			</tr>
	  			<tr>
	  				<td>RoadMap_Imputed_NarrowPeak</td><td>All functional marks imputed by Chrome-Impute</td><td>4061 annotations, 127 cell types, 32 functional marks</td>
	  			</tr>
	  			<tr>
	  				<td>RoadMap_Promoter</td><td>Overlap of DHS with TssA, TssAFlnk, and TssBiv</td><td>111 annotations/cell types</td>
	  			</tr>
	  			<tr>
	  				<td>TFBS</td><td>Transcription factor binding sites</td><td>165 annotations</td>
	  			</tr>
	  			<tr>
	  				<td>Thurman_Nature2012_DHS</td><td>DHS</td><td>54 annotations</td>
	  			</tr>
	  		</tbody>
		</table>
	  </div>
  	</div>

	<h4><strong>5. Commandline options</strong></h4>
	Commandline options for PAINTOR can be specified in the text box when a job is submited on SNP2GENE.
	Input of this option should be <span style="color:red;">one line</span> of text with all parameters to be set.
	The following parameters can be provided by users.<br/><br/>

	<code>-enumerate</code> specify this flag if you want to enumerate all possible configurations followed by the max number of causal SNPs (eg. -enumerate 3 considers up to 3 causals at each locus) [Default: not specified]<br/>
	<code>-MI</code> Maximum iterations for algorithm to run [Default: 10]<br/>
	<code>-GAMinital</code> Initialize the enrichment parameters to a pre-specified value (comma separated) [Default: 0,...,0]<br/>
	<code>-variance</code> specify prior variance on the causal effect sizes scaled by sample size [Default: 30]<br/>
	<code>-num_samples</code> specify number of samples to draw for each locus [Default: 1000000]<br/>
	<code>-set_seed</code> specify an integer as a seed for random number generator [default: clock time at execution]<br/>
	<code>-max_causal</code> specify the number of causals to pre-compute enrichments with [default: 2]<br/>

	<span class="info"><i class="fa fa-info"></i>
		If any options other than listed above are provided by users, e.g. <code>-input</code> for input file name or <code>-Zhead</code> for column name, they are removed from the options since input file formats are fixed in FUMA.
	</span>

	<h4><strong>6. Output</strong></h4>
	When PAINTOR is performed, all input and output files are downloadable from SNP2GENE page.
	Please refer <a href="{{ Config::get('app.subdir') }}/tutorial#table-columns">Table columns</a> and/or README file for details.
	The zip file (PAINTOR.zip) contains two folders; "input" and "output".
	There are three input files and one output file for every locus.
	For example, locus 1 has "Locus1", "Locus1.ld" and "Locus1.annotations" in the input folder and "Locus1.results" in the output folder.
	When any annotation is not selected by user, "Locus1.anntation" contains one fake column since annotation file needs to be exist to perform PAINTOR but it is not used.
</div>
