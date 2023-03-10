<div id="newJob" class="sidePanel container" style="padding-top:50px;">
	<h4 style="color: #00004d">This is an example page of SNP2GENE job submission.
		All input options are disabled in this page.
		Please register to submit your own job.
	</h4>
	<br/>
	<!-- Input files upload -->
	<div class="panel panel-danger" style="padding-top: 0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>1. Upload input files <a href="#NewJobFilesPanel" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
		</div>
		<div class="panel-body collapse in" id="NewJobFilesPanel">
			<table class="table table-bordered inputTable" id="NewJobFiles" style="width: auto;">
				<tr>
					<td>GWAS summary statistics
						<a class="infoPop" data-toggle="popover" title="GWAS summary statistics input file" data-content="Every row should have information on one SNP.
							The minimum required columns are ‘chromosome, position and P-value’ or ‘rsID and P-value’.
							If you provide position, please make sure the position is on hg19.
							The file could be complete results of GWAS or a subset of SNPs can be used as an input.
							The input file should be plain text, zip or gzip files.
							If you would like to test FUMA, please check 'Use example input', this will load an example file automatically.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="file" class="form-control-file" name="GWASsummary" id="GWASsummary"/>
						Or <input type="checkbox" class="form-check-input" name="egGWAS" id="egGWAS"/> : Use example input (Crohn's disease, Franke et al. 2010).
					</td>
					<td>
						<div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-ban"></i> Mandatory<br/>The maximum file size is 600Mb. Please gzip if your file is bigger than 600Mb.</div>
					</td>
				</tr>
				<tr>
					<td>GWAS summary statistics file columns
						<a class="infoPop" data-toggle="popover" title="GWAS summary statistics input file columns" data-content="This is optional parameter to define column names.
							Unless defined, FUMA will automatically detect columns from the list of acceptable column names (see tutorial for detail).
							However, to avoid error, please provide column names.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="info"><i class="fa fa-info"></i> case insensitive</span><br/>
						<span class="form-inline">Chromosome: <input type="text" class="form-control" id="chrcol" name="chrcol"></span><br/>
						<span class="form-inline">Position: <input type="text" class="form-control" id="poscol" name="poscol"></span><br/>
						<span class="form-inline">rsID: <input type="text" class="form-control" id="rsIDcol" name="rsIDcol"></span><br/>
						<span class="form-inline">P-value: <input type="text" class="form-control" id="pcol" name="pcol"></span><br/>
						<span class="form-inline">Risk allele: <input type="text" class="form-control" id="altcol" name="altcol"></span><br/>
						<span class="form-inline">Other allele: <input type="text" class="form-control" id="refcol" name="refcol"></span><br/>
						<span class="form-inline">OR: <input type="text" class="form-control" id="orcol" name="orcol"></span><br/>
						<span class="form-inline">Beta: <input type="text" class="form-control" id="becol" name="becol"></span><br/>
						<span class="form-inline">SE: <input type="text" class="form-control" id="secol" name="secol"></span><br/>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional. Please fill as much as you can. It is not necessary to fill all column names.
						</div>
					</td>
				</tr>
				<tr>
					<td>Pre-defined lead SNPs
						<a class="infoPop" data-toggle="popover" title="Pre-defined lead SNPs" data-content="This option can be used when you already have determined lead SNPs and do not want FUMA to do this for you. This option can be also used when you want to include specific SNPs as lead SNPs which do no reach significant P-value threshold. The input file should have 3 columns, rsID, chromosome and position with header (header could be anything but the order of columns have to match).">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="file" class="form-control-file" name="leadSNPs" id="leadSNPs"/></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr>
					<td>Identify additional independent lead SNPs
						<a class="infoPop" data-toggle="popover" title="Additional identification of lead SNPs" data-content="This option is only vallid when pre-defined lead SNPs are provided. Please uncheck this to NOT IDENTIFY additional lead SNPs than the provided ones. When this option is checked, FUMA will identify all independent lead SNPs after taking all SNPs in LD of pre-defined lead SNPs if there is any.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" class="form-check-input" name="addleadSNPs" id="addleadSNPs" value="1" checked></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional. <br/>This is only valid when predefined lead SNPs are provided.</div>
					</td>
				</tr>
				<tr>
					<td>Predefined genomic region
						<a class="infoPop" data-toggle="popover" title="Pre-defined genomic regions" data-content="This option can be used when you already have defined specific genomic regions of interest and only require annotations of significant SNPs and their proxi SNPs in these regions. The input file should have 3 columns, chromosome, start and end position (on hg19) with header (header could be anything but the order of columns have to match).">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="file" class="form-control-file" name="regions" id="regions"/></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Parameters for lead SNPs and candidate SNPs -->
	<div class="panel panel-danger" style="padding-top: 0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>2. Parameters for lead SNPs and candidate SNPs identification<a href="#NewJobParamsPanel" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
		</div>
		<div class="panel-body collapse in" id="NewJobParamsPanel">
			<table class="table table-bordered inputTable" id="NewJobParams" style="width: auto;">
				<tr>
					<td>Sample size (N)
						<a class="infoPop" data-toggle="popover" title="Sample size" data-content="The total number of individuals (cases + controls, or total N) used in GWAS.
							This is only used for MAGMA. When total sample size is defined, the same number will be used for all SNPs.
							If you have column 'N' in yout input GWAS summary statistics file, specified column will be used for N per SNP.
							It does not affect functional annotations and prioritizations.
							If you don't know the sample size, the random number should be fine (> 50), yet that does not render the gene-based tests from MAGMA invalid.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						Total sample size (integer): <input type="number" class="form-control" id="N" name="N">
						OR<br/>
						Column name for N per SNP (text): <input type="text" class="form-control" id="Ncol" name="Ncol">
					</td>
					<td>
						<div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-ban"></i> Mandatory input. <br/>Please provide either total sample size of GWAS study or column name of N in input file.</div>
					</td>
				</tr>
				<tr>
					<td>Maximum P-value of lead SNPs (&lt;)</td>
					<td><input type="number" class="form-control" id="leadP" name="leadP" value="5e-8"/></td>
					<td>
						<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-check"></i> OK.</div>
					</td>
				</tr>
				<tr>
					<td>r<sup>2</sup> threshold to define LD structure of lead SNPs (&ge;)</td>
					<td><input type="number" class="form-control" id="r2" name="r2" value="0.6"></td>
					<td>
						<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-check"></i> OK.</div>
					</td>
				</tr>
				<tr>
					<td>Maximum P-value cutoff (&lt;)
						<a class="infoPop" data-toggle="popover" title="GWAS P-value cutoff" data-content="This threshold defines the maximum P-values of SNPs to be included in the annotation. Setting it at 1 means that all SNPs that are in LD with the lead SNP will be included in the annotation and prioritization even though they may not show a significant association with the phenotype. We advise to set this threshold at least at 0.05.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="number" class="form-control" id="gwasP" name="gwasP" value="0.05"/></td>
					<td>
						<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-check"></i> OK.</div>
					</td>
				</tr>
				<tr>
					<td>Population</td>
					<td>
						<select class="form-control" id="pop" name="pop">
							<option selected>EUR</option>
							<option>AMR</option>
							<option>AFR</option>
							<option>SAS</option>
							<option>EAS</option>
						</select>
					</td>
					<td>
						<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-check"></i> OK.
						</div>
					</td>
				</tr>
				<tr>
					<td>Include 1000 genome variant (non-GWAS tagged SNPs in LD)
						<a class="infoPop" data-toggle="popover" title="1000G SNPs" data-content="Select ‘yes’ if you want to include SNPs that are not available in the GWAS output but are available in 1000G. Including these SNPs may provide information on functional variants in LD with the lead SNP.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<select class="form-control" id="KGSNPs" name="KGSNPs">
							<option selected>Yes</option>
							<option>No</option>
						</select>
					</td>
					<td>
						<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-check"></i> OK.
						</div>
					</td>
				</tr>
				<tr>
					<td>Minimum Minor Allele Frequency (&ge;)
						<a class="infoPop" data-toggle="popover" title="Minimu Minor Allele Frequency" data-content="This threshold defines the minimum MAF of the SNPs to be included in the annotation. MAFs are based on the selected reference population (1000G).">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="number" class="form-control" id="maf" name="maf" value="0.01"/></td>
					<td>
						<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-check"></i> OK.</div>
					</td>
				</tr>
				<tr>
					<td>Maximum distance between LD blocks to merge into a locus (&lt; kb)
						<a class="infoPop" data-toggle="popover" title="Maximum distance between LD blocks to merge" data-content="LD blocks clorser than the distance will be merged into a genomic locus. If this is set at 0, only phesically overlapped LD blocks will be merged. This is only for representation of GWAS risk loci which does not affect any annotation and prioritization results.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><span class="form-inline"><input type="number" class="form-control" id="mergeDist" name="mergeDist" value="250"/> kb</span></td>
					<td>
						<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-check"></i> OK.</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Parameters for gene mapping -->
	<!-- positional mapping -->
	<div class="panel panel-default" style="padding:0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>3-1. Gene Mapping (positional mapping) <a href="#NewJobPosMapPanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="NewJobPosMapPanel">
			<h4>Positional mapping</h4>
			<table class="table table-bordered inputTable" id="NewJobPosMap" style="width: auto;">
				<tr>
					<td>Perform positional mapping
						<a class="infoPop" data-toggle="popover" title="Positional maping" data-content="When checked, positional mapping will be carried out and includes functional consequences of SNPs on gene functions (such as exonic, intronic and splicing).">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" class="form-check-input" name="posMap" id="posMap" checked></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="posMapOptions">
					<td>Distance to genes or <br>functional consequences of SNPs on genes to map
						<a class="infoPop" data-toggle="popover" title="Positional mapping" data-content="
							Positional mapping can be performed purly based on the phisical distance between SNPs and genes by providing the maximum distance.
							Optionally, functional consequences of SNPs on genes can be selected to map only specific SNPs such as SNPs locating on exonic regions.
							Note that when functional consequnces are selected, only SNPs locationg on the gene body (distance 0) are mapped to genes except upstream and downstream SNPs which are up to 1kb apart from TSS or TES.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="form-inline">Maximum distance: <input type="number" class="form-control" id="posMapWindow" name="posMapWindow" value="10" min="0" max="1000"> kb</span><br/>
						OR<br/>
						Functional consequences of SNPs on genes:<br/>
						<span class="multiSelect">
							<a>clear</a><br/>
							<select multiple class="form-control" id="posMapAnnot" name="posMapAnnot[]">
								<option value="exonic">exonic</option>
								<option value="splicing">splicing</option>
								<option value="intronic">intronic</option>
								<option value="UTR3">3UTR</option>
								<option value="UTR5">5UTR</option>
								<option value="upstream">upstream</option>
								<option value="downstream">downstream</option>
							</select>
						</span>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
			</table>

			<div id="posMapOptFilt">
				Optional SNP filtering by functional annotations for positional mapping<br/>
				<span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by positional mapping criterion. When eQTL mapping is also performed, this filtering can be specified separately.<br/>
					All these annotations will be available for all SNPs within LD of identified lead SNPs in the result tables, but this filtering affect gene prioritization.
				</span>
				<table class="table table-bordered inputTable" id="posMapOptFiltTable" style="width: auto;">
					<tr>
						<td rowspan="2">CADD</td>
						<td>Perform SNPs filtering based on CADD score.
							<a class="infoPop" data-toggle="popover" title="CADD score filtering" data-content="Please check this option to filter SNPs based on CADD score and spacify minimum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="posMapCADDcheck" id="posMapCADDcheck"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>Minimum CADD score (&ge;)
							<a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The heigher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="posMapCADDth" name="posMapCADDth" value="12.37"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td rowspan="2">RegulomeDB</td>
						<td>Perform SNPs filtering based on RegulomeDB score
							<a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="posMapRDBcheck" id="posMapRDBcheck"></td>
						<td></td>
					</tr>
					<tr>
						<td>Maximum RegulomeDB score (categorical)
							<a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least liekly. Some SNPs have 'NA' which are not assigned any score.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select class="form-control" id="posMapRDBth" name="posMapRDBth">
								<option>1a</option>
								<option>1b</option>
								<option>1c</option>
								<option>1d</option>
								<option>1e</option>
								<option>1f</option>
								<option>2a</option>
								<option>2b</option>
								<option>2c</option>
								<option>3a</option>
								<option>3b</option>
								<option>4</option>
								<option>5</option>
								<option>6</option>
								<option selected>7</option>
							</select>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
						<tr>
						<td rowspan="4">15-core chromatin state</td>
						<td>Perform SNPs filtering based on chromatin state
							<a class="infoPop" data-toggle="popover" title="15-core chromatin state filtering" data-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="posMapChr15check" id="posMapChr15check"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>Tissue/cell types for 15-core chromatin state<br/>
							<span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
						</td>
						<td>
							<span class="multiSelect">
								<a style="float:right; padding-right:20px;">clear</a><br/>
								<select multiple class="form-control" size="10" id="posMapChr15Ts" name="posMapChr15Ts[]">
									<option value="all">All</option>
									<option class="level1" value="null">Adrenal (1)</option>
									<option class="level2" value="E080">E080 (Other) Fetal Adrenal Gland</option>
									<option class="level1" value="null">Blood (27)</option>
									<option class="level2" value="E029">E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
									<option class="level2" value="E030">E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
									<option class="level2" value="E031">E031 (HSC & B-cell) Primary B cells from cord blood</option>
									<option class="level2" value="E032">E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
									<option class="level2" value="E033">E033 (Blood & T-cell) Primary T cells from cord blood</option>
									<option class="level2" value="E034">E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
									<option class="level2" value="E035">E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
									<option class="level2" value="E036">E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
									<option class="level2" value="E037">E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
									<option class="level2" value="E038">E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
									<option class="level2" value="E039">E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
									<option class="level2" value="E040">E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
									<option class="level2" value="E041">E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
									<option class="level2" value="E042">E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
									<option class="level2" value="E043">E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
									<option class="level2" value="E044">E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
									<option class="level2" value="E045">E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
									<option class="level2" value="E046">E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
									<option class="level2" value="E047">E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
									<option class="level2" value="E048">E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
									<option class="level2" value="E050">E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
									<option class="level2" value="E051">E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
									<option class="level2" value="E062">E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
									<option class="level2" value="E115">E115 (ENCODE2012) Dnd41 TCell Leukemia Cell Line</option>
									<option class="level2" value="E116">E116 (ENCODE2012) GM12878 Lymphoblastoid Cells</option>
									<option class="level2" value="E123">E123 (ENCODE2012) K562 Leukemia Cells</option>
									<option class="level2" value="E124">E124 (ENCODE2012) Monocytes-CD14+ RO01746 Primary Cells</option>
									<option class="level1" value="null">Bone (1)</option>
									<option class="level2" value="E129">E129 (ENCODE2012) Osteoblast Primary Cells</option>
									<option class="level1" value="null">Brain (13)</option>
									<option class="level2" value="E053">E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
									<option class="level2" value="E054">E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
									<option class="level2" value="E067">E067 (Brain) Brain Angular Gyrus</option>
									<option class="level2" value="E068">E068 (Brain) Brain Anterior Caudate</option>
									<option class="level2" value="E069">E069 (Brain) Brain Cingulate Gyrus</option>
									<option class="level2" value="E070">E070 (Brain) Brain Germinal Matrix</option>
									<option class="level2" value="E071">E071 (Brain) Brain Hippocampus Middle</option>
									<option class="level2" value="E072">E072 (Brain) Brain Inferior Temporal Lobe</option>
									<option class="level2" value="E073">E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
									<option class="level2" value="E074">E074 (Brain) Brain Substantia Nigra</option>
									<option class="level2" value="E081">E081 (Brain) Fetal Brain Male</option>
									<option class="level2" value="E082">E082 (Brain) Fetal Brain Female</option>
									<option class="level2" value="E125">E125 (ENCODE2012) NH-A Astrocytes Primary Cells</option>
									<option class="level1" value="null">Breast (3)</option>
									<option class="level2" value="E027">E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
									<option class="level2" value="E028">E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
									<option class="level2" value="E119">E119 (ENCODE2012) HMEC Mammary Epithelial Primary Cells</option>
									<option class="level1" value="null">Cervix (1)</option>
									<option class="level2" value="E117">E117 (ENCODE2012) HeLa-S3 Cervical Carcinoma Cell Line</option>
									<option class="level1" value="null">ESC (8)</option>
									<option class="level2" value="E001">E001 (ESC) ES-I3 Cells</option>
									<option class="level2" value="E002">E002 (ESC) ES-WA7 Cells</option>
									<option class="level2" value="E003">E003 (ESC) H1 Cells</option>
									<option class="level2" value="E008">E008 (ESC) H9 Cells</option>
									<option class="level2" value="E014">E014 (ESC) HUES48 Cells</option>
									<option class="level2" value="E015">E015 (ESC) HUES6 Cells</option>
									<option class="level2" value="E016">E016 (ESC) HUES64 Cells</option>
									<option class="level2" value="E024">E024 (ESC) ES-UCSF4  Cells</option>
									<option class="level1" value="null">ESC Derived (9)</option>
									<option class="level2" value="E004">E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
									<option class="level2" value="E005">E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
									<option class="level2" value="E006">E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
									<option class="level2" value="E007">E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
									<option class="level2" value="E009">E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
									<option class="level2" value="E010">E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
									<option class="level2" value="E011">E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
									<option class="level2" value="E012">E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
									<option class="level2" value="E013">E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
									<option class="level1" value="null">Fat (3)</option>
									<option class="level2" value="E023">E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
									<option class="level2" value="E025">E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
									<option class="level2" value="E063">E063 (Adipose) Adipose Nuclei</option>
									<option class="level1" value="null">GI Colon (3)</option>
									<option class="level2" value="E075">E075 (Digestive) Colonic Mucosa</option>
									<option class="level2" value="E076">E076 (Sm. Muscle) Colon Smooth Muscle</option>
									<option class="level2" value="E106">E106 (Digestive) Sigmoid Colon</option>
									<option class="level1" value="null">GI Duodenum (2)</option>
									<option class="level2" value="E077">E077 (Digestive) Duodenum Mucosa</option>
									<option class="level2" value="E078">E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
									<option class="level1" value="null">GI Esophagus (1)</option>
									<option class="level2" value="E079">E079 (Digestive) Esophagus</option>
									<option class="level1" value="null">GI Intestine (3)</option>
									<option class="level2" value="E084">E084 (Digestive) Fetal Intestine Large</option>
									<option class="level2" value="E085">E085 (Digestive) Fetal Intestine Small</option>
									<option class="level2" value="E109">E109 (Digestive) Small Intestine</option>
									<option class="level1" value="null">GI Rectum (3)</option>
									<option class="level2" value="E101">E101 (Digestive) Rectal Mucosa Donor 29</option>
									<option class="level2" value="E102">E102 (Digestive) Rectal Mucosa Donor 31</option>
									<option class="level2" value="E103">E103 (Sm. Muscle) Rectal Smooth Muscle</option>
									<option class="level1" value="null">GI Stomach (4)</option>
									<option class="level2" value="E092">E092 (Digestive) Fetal Stomach</option>
									<option class="level2" value="E094">E094 (Digestive) Gastric</option>
									<option class="level2" value="E110">E110 (Digestive) Stomach Mucosa</option>
									<option class="level2" value="E111">E111 (Sm. Muscle) Stomach Smooth Muscle</option>
									<option class="level1" value="null">Heart (4)</option>
									<option class="level2" value="E083">E083 (Heart) Fetal Heart</option>
									<option class="level2" value="E095">E095 (Heart) Left Ventricle</option>
									<option class="level2" value="E104">E104 (Heart) Right Atrium</option>
									<option class="level2" value="E105">E105 (Heart) Right Ventricle</option>
									<option class="level1" value="null">Kidney (1)</option>
									<option class="level2" value="E086">E086 (Other) Fetal Kidney</option>
									<option class="level1" value="null">Liver (2)</option>
									<option class="level2" value="E066">E066 (Other) Liver</option>
									<option class="level2" value="E118">E118 (ENCODE2012) HepG2 Hepatocellular Carcinoma Cell Line</option>
									<option class="level1" value="null">Lung (5)</option>
									<option class="level2" value="E017">E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
									<option class="level2" value="E088">E088 (Other) Fetal Lung</option>
									<option class="level2" value="E096">E096 (Other) Lung</option>
									<option class="level2" value="E114">E114 (ENCODE2012) A549 EtOH 0.02pct Lung Carcinoma Cell Line</option>
									<option class="level2" value="E128">E128 (ENCODE2012) NHLF Lung Fibroblast Primary Cells</option>
									<option class="level1" value="null">Muscle (7)</option>
									<option class="level2" value="E052">E052 (Myosat) Muscle Satellite Cultured Cells</option>
									<option class="level2" value="E089">E089 (Muscle) Fetal Muscle Trunk</option>
									<option class="level2" value="E100">E100 (Muscle) Psoas Muscle</option>
									<option class="level2" value="E107">E107 (Muscle) Skeletal Muscle Male</option>
									<option class="level2" value="E108">E108 (Muscle) Skeletal Muscle Female</option>
									<option class="level2" value="E120">E120 (ENCODE2012) HSMM Skeletal Muscle Myoblasts Cells</option>
									<option class="level2" value="E121">E121 (ENCODE2012) HSMM cell derived Skeletal Muscle Myotubes Cells</option>
									<option class="level1" value="null">Muscle Leg (1)</option>
									<option class="level2" value="E090">E090 (Muscle) Fetal Muscle Leg</option>
									<option class="level1" value="null">Ovary (1)</option>
									<option class="level2" value="E097">E097 (Other) Ovary</option>
									<option class="level1" value="null">Pancreas (2)</option>
									<option class="level2" value="E087">E087 (Other) Pancreatic Islets</option>
									<option class="level2" value="E098">E098 (Other) Pancreas</option>
									<option class="level1" value="null">Placenta (2)</option>
									<option class="level2" value="E091">E091 (Other) Placenta</option>
									<option class="level2" value="E099">E099 (Other) Placenta Amnion</option>
									<option class="level1" value="null">Skin (8)</option>
									<option class="level2" value="E055">E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
									<option class="level2" value="E056">E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
									<option class="level2" value="E057">E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
									<option class="level2" value="E058">E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
									<option class="level2" value="E059">E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
									<option class="level2" value="E061">E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
									<option class="level2" value="E126">E126 (ENCODE2012) NHDF-Ad Adult Dermal Fibroblast Primary Cells</option>
									<option class="level2" value="E127">E127 (ENCODE2012) NHEK-Epidermal Keratinocyte Primary Cells</option>
									<option class="level1" value="null">Spleen (1)</option>
									<option class="level2" value="E113">E113 (Other) Spleen</option>
									<option class="level1" value="null">Stromal Connective (2)</option>
									<option class="level2" value="E026">E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
									<option class="level2" value="E049">E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
									<option class="level1" value="null">Thymus (2)</option>
									<option class="level2" value="E093">E093 (Thymus) Fetal Thymus</option>
									<option class="level2" value="E112">E112 (Thymus) Thymus</option>
									<option class="level1" value="null">Vascular (2)</option>
									<option class="level2" value="E065">E065 (Heart) Aorta</option>
									<option class="level2" value="E122">E122 (ENCODE2012) HUVEC Umbilical Vein Endothelial Primary Cells</option>
									<option class="level1" value="null">iPSC (5)</option>
									<option class="level2" value="E018">E018 (iPSC) iPS-15b Cells</option>
									<option class="level2" value="E019">E019 (iPSC) iPS-18 Cells</option>
									<option class="level2" value="E020">E020 (iPSC) iPS-20b Cells</option>
									<option class="level2" value="E021">E021 (iPSC) iPS DF 6.9 Cells</option>
									<option class="level2" value="E022">E022 (iPSC) iPS DF 19.11 Cells</option>
								</select>
							</span>
							<br/>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>15-core chromatin state maximum state
							<a class="infoPop" data-toggle="popover" title="The maximum chromatin state" data-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="posMapChr15Max" name="posMapChr15Max" value="7"/></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>15-core chromatin state filtering method
							<a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissye/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select  class="form-control" id="posMapChr15Meth" name="posMapChr15Meth">
								<option selected value="any">any</option>
								<option value="majority">majority</option>
								<option value="all">all</option>
							</select>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<!-- eqtl mapping -->
	<div class="panel panel-default" style="padding: 0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>3-2. Gene Mapping (eQTL mapping)<a href="#NewJobEqtlMapPanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="NewJobEqtlMapPanel">
			<h4>eQTL mapping</h4>
			<table class="table table-bordered inputTable" id="NewJobEqtlMap" style="width: auto;">
				<tr>
					<td>Perform eQTL mapping
						<a class="infoPop" data-toggle="popover" title="eQTL mapping" data-content="eQTL mapping maps SNPs to genes based on eQTL information. This maps SNPs to genes up to 1 Mb part (cis-eQTL). Please check this option to perform eQTL mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" calss="form-control" name="eqtlMap", id="eqtlMap"></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="eqtlMapOptions">
					<td>Tissue types
						<a class="infoPop" data-toggle="popover" title="Tissue types of eQTLs" data-content="This is mandatory parameter for eQTL mapping. Currentlly 44 tissue types from GTEx and two large scale eQTL study of blood cell are available.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="multiSelect">
							<a style="float:right; padding-right:20px;">clear</a><br/>
							<select multiple class="form-control" id="eqtlMapTs" name="eqtlMapTs[]" size="10">
								<option value="all">All</option>
								<option class="level1" value="null">Blood eQTLs</option>
								<option class="level2" value='BloodeQTL_BloodeQTL'>Westra et al. (2013) Blood eQTL Browser</option>
								<option class="level2" value='BIOSQTL_BIOS_eQTL_geneLevel'>Zhernakova et al. (2017) BIOS QTL Browser</option>
								<option class="level1" value="null">GTEx Adipose Tissue (2)</option>
								<option class="level2" value="GTEx_Adipose_Subcutaneous">GTEx Adipose Subcutaneous</option>
								<option class="level2" value="GTEx_Adipose_Visceral_Omentum">GTEx Adipose Visceral Omentum</option>
								<option class="level1" value="null">GTEx Adrenal Gland (1)</option>
								<option class="level2" value="GTEx_Adrenal_Gland">GTEx Adrenal Gland</option>
								<option class="level1" value="null">GTEx Blood (2)</option>
								<option class="level2" value="GTEx_Cells_EBV-transformed_lymphocytes">GTEx Cells EBV-transformed lymphocytes</option>
								<option class="level2" value="GTEx_Whole_Blood">GTEx Whole Blood</option>
								<option class="level1" value="null">GTEx Blood Vessel (3)</option>
								<option class="level2" value="GTEx_Artery_Aorta">GTEx Artery Aorta</option>
								<option class="level2" value="GTEx_Artery_Coronary">GTEx Artery Coronary</option>
								<option class="level2" value="GTEx_Artery_Tibial">GTEx Artery Tibial</option>
								<option class="level1" value="null">GTEx Brain (10)</option>
								<option class="level2" value="GTEx_Brain_Anterior_cingulate_cortex_BA24">GTEx Brain Anterior cingulate cortex BA24</option>
								<option class="level2" value="GTEx_Brain_Caudate_basal_ganglia">GTEx Brain Caudate basal ganglia</option>
								<option class="level2" value="GTEx_Brain_Cerebellar_Hemisphere">GTEx Brain Cerebellar Hemisphere</option>
								<option class="level2" value="GTEx_Brain_Cerebellum">GTEx Brain Cerebellum</option>
								<option class="level2" value="GTEx_Brain_Cortex">GTEx Brain Cortex</option>
								<option class="level2" value="GTEx_Brain_Frontal_Cortex_BA9">GTEx Brain Frontal Cortex BA9</option>
								<option class="level2" value="GTEx_Brain_Hippocampus">GTEx Brain Hippocampus</option>
								<option class="level2" value="GTEx_Brain_Hypothalamus">GTEx Brain Hypothalamus</option>
								<option class="level2" value="GTEx_Brain_Nucleus_accumbens_basal_ganglia">GTEx Brain Nucleus accumbens basal ganglia</option>
								<option class="level2" value="GTEx_Brain_Putamen_basal_ganglia">GTEx Brain Putamen basal ganglia</option>
								<option class="level1" value="null">GTEx Breast (1)</option>
								<option class="level2" value="GTEx_Breast_Mammary_Tissue">GTEx Breast Mammary Tissue</option>
								<option class="level1" value="null">GTEx Colon (2)</option>
								<option class="level2" value="GTEx_Colon_Sigmoid">GTEx Colon Sigmoid</option>
								<option class="level2" value="GTEx_Colon_Transverse">GTEx Colon Transverse</option>
								<option class="level1" value="null">GTEx Esophagus (3)</option>
								<option class="level2" value="GTEx_Esophagus_Gastroesophageal_Junction">GTEx Esophagus Gastroesophageal Junction</option>
								<option class="level2" value="GTEx_Esophagus_Mucosa">GTEx Esophagus Mucosa</option>
								<option class="level2" value="GTEx_Esophagus_Muscularis">GTEx Esophagus Muscularis</option>
								<option class="level1" value="null">GTEx Heart (2)</option>
								<option class="level2" value="GTEx_Heart_Atrial_Appendage">GTEx Heart Atrial Appendage</option>
								<option class="level2" value="GTEx_Heart_Left_Ventricle">GTEx Heart Left Ventricle</option>
								<option class="level1" value="null">GTEx Liver (1)</option>
								<option class="level2" value="GTEx_Liver">GTEx Liver</option>
								<option class="level1" value="null">GTEx Lung (1)</option>
								<option class="level2" value="GTEx_Lung">GTEx Lung</option>
								<option class="level1" value="null">GTEx Muscle (1)</option>
								<option class="level2" value="GTEx_Muscle_Skeletal">GTEx Muscle Skeletal</option>
								<option class="level1" value="null">GTEx Nerve (1)</option>
								<option class="level2" value="GTEx_Nerve_Tibial">GTEx Nerve Tibial</option>
								<option class="level1" value="null">GTEx Ovary (1)</option>
								<option class="level2" value="GTEx_Ovary">GTEx Ovary</option>
								<option class="level1" value="null">GTEx Pancreas (1)</option>
								<option class="level2" value="GTEx_Pancreas">GTEx Pancreas</option>
								<option class="level1" value="null">GTEx Pituitary (1)</option>
								<option class="level2" value="GTEx_Pituitary">GTEx Pituitary</option>
								<option class="level1" value="null">GTEx Prostate (1)</option>
								<option class="level2" value="GTEx_Prostate">GTEx Prostate</option>
								<option class="level1" value="null">GTEx Skin (3)</option>
								<option class="level2" value="GTEx_Cells_Transformed_fibroblasts">GTEx Cells Transformed fibroblasts</option>
								<option class="level2" value="GTEx_Skin_Not_Sun_Exposed_Suprapubic">GTEx Skin Not Sun Exposed Suprapubic</option>
								<option class="level2" value="GTEx_Skin_Sun_Exposed_Lower_leg">GTEx Skin Sun Exposed Lower leg</option>
								<option class="level1" value="null">GTEx Small Intestine (1)</option>
								<option class="level2" value="GTEx_Small_Intestine_Terminal_Ileum">GTEx Small Intestine Terminal Ileum</option>
								<option class="level1" value="null">GTEx Spleen (1)</option>
								<option class="level2" value="GTEx_Spleen">GTEx Spleen</option>
								<option class="level1" value="null">GTEx Stomach (1)</option>
								<option class="level2" value="GTEx_Stomach">GTEx Stomach</option>
								<option class="level1" value="null">GTEx Testis (1)</option>
								<option class="level2" value="GTEx_Testis">GTEx Testis</option>
								<option class="level1" value="null">GTEx Thyroid (1)</option>
								<option class="level2" value="GTEx_Thyroid">GTEx Thyroid</option>
								<option class="level1" value="null">GTEx Uterus (1)</option>
								<option class="level2" value="GTEx_Uterus">GTEx Uterus</option>
								<option class="level1" value="null">GTEx Vagina (1)</option>
								<option class="level2" value="GTEx_Vagina">GTEx Vagina</option>
								<option class="level1" value="null">BRAINEAC (11)</option>
								<option class="level2" value="BRAINEAC_CRBL">BRAINEAC Cerebellar cortex</option>
								<option class="level2" value="BRAINEAC_FCTX">BRAINEAC Frontal cortex</option>
								<option class="level2" value="BRAINEAC_HIPP">BRAINEAC Hippocampus</option>
								<option class="level2" value="BRAINEAC_MEDU">BRAINEAC Inferior olivary nucleus (sub-dissected from the medulla)</option>
								<option class="level2" value="BRAINEAC_OCTX">BRAINEAC Occipital cortex</option>
								<option class="level2" value="BRAINEAC_PUTM">BRAINEAC Putamen (at the level of the anterior commissure)</option>
								<option class="level2" value="BRAINEAC_SNIG">BRAINEAC Substantia nigra</option>
								<option class="level2" value="BRAINEAC_TCTX">BRAINEAC Temporal cortex</option>
								<option class="level2" value="BRAINEAC_THAL">BRAINEAC Thalamus (at the level of the lateral geniculate nucleus)</option>
								<option class="level2" value="BRAINEAC_WHMT">BRAINEAC Intralobular white matter</option>
								<option class="level2" value="BRAINEAC_aveALL">BRAINEAC Averaged expression of 10 brain regions</option>
							</select>
						</span>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="eqtlMapOptions">
					<td>eQTL P-value threshold
						<a class="infoPop" data-toggle="popover" title="eQTL P-value threshold" data-content="By default, only significant eQTLs are used (FDR &lt; 0.05). Please UNCHECK 'Use only significant snp-gene pair' to filter eQTLs based on raw P-value.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="form-inline">Use only significant snp-gene pairs: <input type="checkbox" class="form-control" name="sigeqtlCheck" id="sigeqtlCheck" checked> (FDR&lt;0.05)</span><br/>
						OR<br/>
						<span class="form-inline">(nominal) P-value cutoff (&lt;): <input type="number" class="form-control" name="eqtlP" id="eqtlP" value="1e-3"></span>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
			</table>

			<div id="eqtlMapOptFilt">
				Optional SNP filtering by functional annotation for eQTL mapping<br/>
				<span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by eQTL mapping criterion.<br/>
					All these annotations will be available for all SNPs within LD of identified lead SNPs in the result tables, but this filtering affect gene prioritization.
				</span>
				<table class="table table-bordered inputTable" id="eqtlMapOptFiltTable">
					<tr>
						<td rowspan="2">CADD</td>
						<td>Perform SNPs filtering based on CADD score.
							<a class="infoPop" data-toggle="popover" title="CADD score filtering" data-content="Please check this option to filter SNPs based on CADD score and spacify minimum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="eqtlMapCADDcheck" id="eqtlMapCADDcheck"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>Minimum CADD score (&ge;)
							<a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The heigher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="eqtlMapCADDth" name="eqtlMapCADDth" value="12.37"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td rowspan="2">RegulomeDB</td>
						<td>Perform SNPs filtering based on RegulomeDB score
							<a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="eqtlMapRDBcheck" id="eqtlMapRDBcheck"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>Maximum RegulomeDB score (categorical)
							<a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least liekly. Some SNPs have 'NA' which are not assigned any score.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select class="form-control" id="eqtlMapRDBth" name="eqtlMapRDBth">
								<option>1a</option>
								<option>1b</option>
								<option>1c</option>
								<option>1d</option>
								<option>1e</option>
								<option>1f</option>
								<option>2a</option>
								<option>2b</option>
								<option>2c</option>
								<option>3a</option>
								<option>3b</option>
								<option>4</option>
								<option>5</option>
								<option>6</option>
								<option selected>7</option>
							</select>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td rowspan="4">15-core chromatin state</td>
						<td>Perform SNPs filtering based on chromatin state
							<a class="infoPop" data-toggle="popover" title="15-core chromatin state filtering" data-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="eqtlMapChr15check" id="eqtlMapChr15check"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>Tissue/cell types for 15-core chromatin state<br/>
							<span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
						</td>
						<td>
							<span class="multiSelect">
								<a style="float:right; padding-right:20px;">clear</a><br/>
								<select multiple class="form-control" size="10" id="eqtlMapChr15Ts" name="eqtlMapChr15Ts[]">
							<option value="all">All</option>
							<option class="level1" value="null">Adrenal (1)</option>
							<option class="level2" value="E080">E080 (Other) Fetal Adrenal Gland</option>
							<option class="level1" value="null">Blood (27)</option>
							<option class="level2" value="E029">E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
							<option class="level2" value="E030">E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
							<option class="level2" value="E031">E031 (HSC & B-cell) Primary B cells from cord blood</option>
							<option class="level2" value="E032">E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
							<option class="level2" value="E033">E033 (Blood & T-cell) Primary T cells from cord blood</option>
							<option class="level2" value="E034">E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
							<option class="level2" value="E035">E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
							<option class="level2" value="E036">E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
							<option class="level2" value="E037">E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
							<option class="level2" value="E038">E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
							<option class="level2" value="E039">E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
							<option class="level2" value="E040">E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
							<option class="level2" value="E041">E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
							<option class="level2" value="E042">E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
							<option class="level2" value="E043">E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
							<option class="level2" value="E044">E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
							<option class="level2" value="E045">E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
							<option class="level2" value="E046">E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
							<option class="level2" value="E047">E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
							<option class="level2" value="E048">E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
							<option class="level2" value="E050">E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
							<option class="level2" value="E051">E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
							<option class="level2" value="E062">E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
							<option class="level2" value="E115">E115 (ENCODE2012) Dnd41 TCell Leukemia Cell Line</option>
							<option class="level2" value="E116">E116 (ENCODE2012) GM12878 Lymphoblastoid Cells</option>
							<option class="level2" value="E123">E123 (ENCODE2012) K562 Leukemia Cells</option>
							<option class="level2" value="E124">E124 (ENCODE2012) Monocytes-CD14+ RO01746 Primary Cells</option>
							<option class="level1" value="null">Bone (1)</option>
							<option class="level2" value="E129">E129 (ENCODE2012) Osteoblast Primary Cells</option>
							<option class="level1" value="null">Brain (13)</option>
							<option class="level2" value="E053">E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
							<option class="level2" value="E054">E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
							<option class="level2" value="E067">E067 (Brain) Brain Angular Gyrus</option>
							<option class="level2" value="E068">E068 (Brain) Brain Anterior Caudate</option>
							<option class="level2" value="E069">E069 (Brain) Brain Cingulate Gyrus</option>
							<option class="level2" value="E070">E070 (Brain) Brain Germinal Matrix</option>
							<option class="level2" value="E071">E071 (Brain) Brain Hippocampus Middle</option>
							<option class="level2" value="E072">E072 (Brain) Brain Inferior Temporal Lobe</option>
							<option class="level2" value="E073">E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
							<option class="level2" value="E074">E074 (Brain) Brain Substantia Nigra</option>
							<option class="level2" value="E081">E081 (Brain) Fetal Brain Male</option>
							<option class="level2" value="E082">E082 (Brain) Fetal Brain Female</option>
							<option class="level2" value="E125">E125 (ENCODE2012) NH-A Astrocytes Primary Cells</option>
							<option class="level1" value="null">Breast (3)</option>
							<option class="level2" value="E027">E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
							<option class="level2" value="E028">E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
							<option class="level2" value="E119">E119 (ENCODE2012) HMEC Mammary Epithelial Primary Cells</option>
							<option class="level1" value="null">Cervix (1)</option>
							<option class="level2" value="E117">E117 (ENCODE2012) HeLa-S3 Cervical Carcinoma Cell Line</option>
							<option class="level1" value="null">ESC (8)</option>
							<option class="level2" value="E001">E001 (ESC) ES-I3 Cells</option>
							<option class="level2" value="E002">E002 (ESC) ES-WA7 Cells</option>
							<option class="level2" value="E003">E003 (ESC) H1 Cells</option>
							<option class="level2" value="E008">E008 (ESC) H9 Cells</option>
							<option class="level2" value="E014">E014 (ESC) HUES48 Cells</option>
							<option class="level2" value="E015">E015 (ESC) HUES6 Cells</option>
							<option class="level2" value="E016">E016 (ESC) HUES64 Cells</option>
							<option class="level2" value="E024">E024 (ESC) ES-UCSF4  Cells</option>
							<option class="level1" value="null">ESC Derived (9)</option>
							<option class="level2" value="E004">E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
							<option class="level2" value="E005">E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
							<option class="level2" value="E006">E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
							<option class="level2" value="E007">E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
							<option class="level2" value="E009">E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
							<option class="level2" value="E010">E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
							<option class="level2" value="E011">E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
							<option class="level2" value="E012">E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
							<option class="level2" value="E013">E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
							<option class="level1" value="null">Fat (3)</option>
							<option class="level2" value="E023">E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
							<option class="level2" value="E025">E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
							<option class="level2" value="E063">E063 (Adipose) Adipose Nuclei</option>
							<option class="level1" value="null">GI Colon (3)</option>
							<option class="level2" value="E075">E075 (Digestive) Colonic Mucosa</option>
							<option class="level2" value="E076">E076 (Sm. Muscle) Colon Smooth Muscle</option>
							<option class="level2" value="E106">E106 (Digestive) Sigmoid Colon</option>
							<option class="level1" value="null">GI Duodenum (2)</option>
							<option class="level2" value="E077">E077 (Digestive) Duodenum Mucosa</option>
							<option class="level2" value="E078">E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
							<option class="level1" value="null">GI Esophagus (1)</option>
							<option class="level2" value="E079">E079 (Digestive) Esophagus</option>
							<option class="level1" value="null">GI Intestine (3)</option>
							<option class="level2" value="E084">E084 (Digestive) Fetal Intestine Large</option>
							<option class="level2" value="E085">E085 (Digestive) Fetal Intestine Small</option>
							<option class="level2" value="E109">E109 (Digestive) Small Intestine</option>
							<option class="level1" value="null">GI Rectum (3)</option>
							<option class="level2" value="E101">E101 (Digestive) Rectal Mucosa Donor 29</option>
							<option class="level2" value="E102">E102 (Digestive) Rectal Mucosa Donor 31</option>
							<option class="level2" value="E103">E103 (Sm. Muscle) Rectal Smooth Muscle</option>
							<option class="level1" value="null">GI Stomach (4)</option>
							<option class="level2" value="E092">E092 (Digestive) Fetal Stomach</option>
							<option class="level2" value="E094">E094 (Digestive) Gastric</option>
							<option class="level2" value="E110">E110 (Digestive) Stomach Mucosa</option>
							<option class="level2" value="E111">E111 (Sm. Muscle) Stomach Smooth Muscle</option>
							<option class="level1" value="null">Heart (4)</option>
							<option class="level2" value="E083">E083 (Heart) Fetal Heart</option>
							<option class="level2" value="E095">E095 (Heart) Left Ventricle</option>
							<option class="level2" value="E104">E104 (Heart) Right Atrium</option>
							<option class="level2" value="E105">E105 (Heart) Right Ventricle</option>
							<option class="level1" value="null">Kidney (1)</option>
							<option class="level2" value="E086">E086 (Other) Fetal Kidney</option>
							<option class="level1" value="null">Liver (2)</option>
							<option class="level2" value="E066">E066 (Other) Liver</option>
							<option class="level2" value="E118">E118 (ENCODE2012) HepG2 Hepatocellular Carcinoma Cell Line</option>
							<option class="level1" value="null">Lung (5)</option>
							<option class="level2" value="E017">E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
							<option class="level2" value="E088">E088 (Other) Fetal Lung</option>
							<option class="level2" value="E096">E096 (Other) Lung</option>
							<option class="level2" value="E114">E114 (ENCODE2012) A549 EtOH 0.02pct Lung Carcinoma Cell Line</option>
							<option class="level2" value="E128">E128 (ENCODE2012) NHLF Lung Fibroblast Primary Cells</option>
							<option class="level1" value="null">Muscle (7)</option>
							<option class="level2" value="E052">E052 (Myosat) Muscle Satellite Cultured Cells</option>
							<option class="level2" value="E089">E089 (Muscle) Fetal Muscle Trunk</option>
							<option class="level2" value="E100">E100 (Muscle) Psoas Muscle</option>
							<option class="level2" value="E107">E107 (Muscle) Skeletal Muscle Male</option>
							<option class="level2" value="E108">E108 (Muscle) Skeletal Muscle Female</option>
							<option class="level2" value="E120">E120 (ENCODE2012) HSMM Skeletal Muscle Myoblasts Cells</option>
							<option class="level2" value="E121">E121 (ENCODE2012) HSMM cell derived Skeletal Muscle Myotubes Cells</option>
							<option class="level1" value="null">Muscle Leg (1)</option>
							<option class="level2" value="E090">E090 (Muscle) Fetal Muscle Leg</option>
							<option class="level1" value="null">Ovary (1)</option>
							<option class="level2" value="E097">E097 (Other) Ovary</option>
							<option class="level1" value="null">Pancreas (2)</option>
							<option class="level2" value="E087">E087 (Other) Pancreatic Islets</option>
							<option class="level2" value="E098">E098 (Other) Pancreas</option>
							<option class="level1" value="null">Placenta (2)</option>
							<option class="level2" value="E091">E091 (Other) Placenta</option>
							<option class="level2" value="E099">E099 (Other) Placenta Amnion</option>
							<option class="level1" value="null">Skin (8)</option>
							<option class="level2" value="E055">E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
							<option class="level2" value="E056">E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
							<option class="level2" value="E057">E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
							<option class="level2" value="E058">E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
							<option class="level2" value="E059">E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
							<option class="level2" value="E061">E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
							<option class="level2" value="E126">E126 (ENCODE2012) NHDF-Ad Adult Dermal Fibroblast Primary Cells</option>
							<option class="level2" value="E127">E127 (ENCODE2012) NHEK-Epidermal Keratinocyte Primary Cells</option>
							<option class="level1" value="null">Spleen (1)</option>
							<option class="level2" value="E113">E113 (Other) Spleen</option>
							<option class="level1" value="null">Stromal Connective (2)</option>
							<option class="level2" value="E026">E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
							<option class="level2" value="E049">E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
							<option class="level1" value="null">Thymus (2)</option>
							<option class="level2" value="E093">E093 (Thymus) Fetal Thymus</option>
							<option class="level2" value="E112">E112 (Thymus) Thymus</option>
							<option class="level1" value="null">Vascular (2)</option>
							<option class="level2" value="E065">E065 (Heart) Aorta</option>
							<option class="level2" value="E122">E122 (ENCODE2012) HUVEC Umbilical Vein Endothelial Primary Cells</option>
							<option class="level1" value="null">iPSC (5)</option>
							<option class="level2" value="E018">E018 (iPSC) iPS-15b Cells</option>
							<option class="level2" value="E019">E019 (iPSC) iPS-18 Cells</option>
							<option class="level2" value="E020">E020 (iPSC) iPS-20b Cells</option>
							<option class="level2" value="E021">E021 (iPSC) iPS DF 6.9 Cells</option>
							<option class="level2" value="E022">E022 (iPSC) iPS DF 19.11 Cells</option>
							</select>
							</span>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>15-core chromatin state maximum state
							<a class="infoPop" data-toggle="popover" title="The maximum chromatin state" data-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="eqtlMapChr15Max" name="eqtlMapChr15Max" value="7"/></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>15-core chromatin state filtering method
							<a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissye/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select  class="form-control" id="eqtlMapChr15Meth" name="eqtlMapChr15Meth">
								<option selected value="any">any</option>
								<option value="majority">majority</option>
								<option value="all">all</option>
							</select>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<!-- chromatin interaction mapping -->
	<div class="panel panel-default" style="padding: 0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>3-3. Gene Mapping (3D Chromatin Interaction mapping)<a href="#NewJobCiMapPanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="NewJobCiMapPanel">
			<h4>chromatin interaction mapping</h4>
			<table class="table table-bordered inputTable" id="NewJobCiMap" style="width: auto;">
				<tr>
					<td>Perform chromatin interaction mapping
						<a class="infoPop" data-toggle="popover" title="3D chromatin interaction mapping" data-content="3D chromatin interaction mapping maps SNPs to genes based on chromatin interactions such as Hi-C and ChIA-PET. Please check to perform this mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" calss="form-control" name="ciMap", id="ciMap"></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="ciMapOptions">
					<td>Buildin chromatin interaction data
						<a class="infoPop" data-toggle="popover" title="Buildin Hi-C data" data-content="Hi-C datasets of 21 tissue and cell types from GSE87112 are selectabe as buildin data. Multiple tissue and cell types can be selected.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="multiSelect">
							<a style="float:right; padding-right:20px;">clear</a><br/>
							<select multiple class="form-control" id="ciMapBuildin" name="ciMapBuildin[]" size="10">
								<option value="all">All</option>
								<option value="HiC/GSE87112/Adrenal.txt.gz">HiC(GSE87112) Adrenal</option>
								<option value="HiC/GSE87112/Aorta.txt.gz">HiC(GSE87112) Aorta</option>
								<option value="HiC/GSE87112/Bladder.txt.gz">HiC(GSE87112) Bladder</option>
								<option value="HiC/GSE87112/Dorsolateral_Prefrontal_Cortex.txt.gz">HiC(GSE87112) Dorsolateral_Prefrontal_Cortex</option>
								<option value="HiC/GSE87112/Hippocampus.txt.gz">HiC(GSE87112) Hippocampus</option>
								<option value="HiC/GSE87112/Left_Ventricle.txt.gz">HiC(GSE87112) Left_Ventricle</option>
								<option value="HiC/GSE87112/Liver.txt.gz">HiC(GSE87112) Liver</option>
								<option value="HiC/GSE87112/Lung.txt.gz">HiC(GSE87112) Lung</option>
								<option value="HiC/GSE87112/Ovary.txt.gz">HiC(GSE87112) Ovary</option>
								<option value="HiC/GSE87112/Pancreas.txt.gz">HiC(GSE87112) Pancreas</option>
								<option value="HiC/GSE87112/Psoas.txt.gz">HiC(GSE87112) Psoas</option>
								<option value="HiC/GSE87112/Right_Ventricle.txt.gz">HiC(GSE87112) Right_Ventricle</option>
								<option value="HiC/GSE87112/Small_Bowel.txt.gz">HiC(GSE87112) Small_Bowel</option>
								<option value="HiC/GSE87112/Spleen.txt.gz">HiC(GSE87112) Spleen</option>
								<option value="HiC/GSE87112/GM12878.txt.gz">HiC(GSE87112) GM12878</option>
								<option value="HiC/GSE87112/IMR90.txt.gz">HiC(GSE87112) IMR90</option>
								<option value="HiC/GSE87112/Mesenchymal_Stem_Cell.txt.gz">HiC(GSE87112) Mesenchymal_Stem_Cell</option>
								<option value="HiC/GSE87112/Mesendoderm.txt.gz">HiC(GSE87112) Mesendoderm</option>
								<option value="HiC/GSE87112/Neural_Progenitor_Cell.txt.gz">HiC(GSE87112) Neural_Progenitor_Cell</option>
								<option value="HiC/GSE87112/Trophoblast-like_Cell.txt.gz">HiC(GSE87112) Trophoblast-like_Cell</option>
								<option value="HiC/GSE87112/hESC.txt.gz">HiC(GSE87112) hESC</option>
							</select>
						</span>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="ciMapOptions">
					<td>Custom chromatin interaction matrices
						<a class="infoPop" data-toggle="popover" title="Custome chromatin interaction matrices"
							data-content="Please upload files of custome chromatin interaction matrices (significant loops). The input files have to follow the specific format. Please refer the tutorial for details. THe file name should be '(Name_of_the_data).txt.gz' in which (Name_of_the_data) will be used in the results table.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span id="ciFiles"></span><br/>
						<button type="button" class="btn btn-default btn-xs" id="ciFileAdd">add file</button>
						<input type="hidden" value="0" id="ciFileN" name="ciFileN">
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="ciMapOptions">
					<td>FDR threshold
						<a class="infoPop" data-toggle="popover" title="FDR threshold for significant interaction" data-content="Significane of interaction for buildin Hi-C datasets are computed by Fit-Hi-C (see tutorial for details). The default threshold is FDR &lt; 1e-6 as suggested by Schmit et al. (2016).">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="form-inline">FDR cutoff (&lt;): <input type="number" class="form-control" name="ciMapFDR" id="ciMapFDR" value="1e-6"></span>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="ciMapOptions">
					<td>Promoter region window
						<a class="infoPop" data-toggle="popover" title="Promoter region window" data-content="The window of promoter regions are used to overlap TSS of genes with significantly interacted regions with risk loci.
							By default, promoter region is defined as 250bp upstream and 500bp downsteram of TSS. Genes whoes promoter regions are overlapped with the interacted region are used for gene mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="text" class="form-control" name="ciMapPromWindow" id="ciMapPromWindow" value="250-500">
						<span class="info"><i class="fa fa-info"></i>
							Please specify both upstream and downstream from TSS. For example, "250-500" means 250bp upstream and 500bp downstream from TSS.
						</span>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="ciMapOptions">
					<td>Annotate enhancer/promoter regions (Roadmap 111 epigenomes)
						<a class="infoPop" data-toggle="popover" title="Enhancer/promoter regions" data-content="Enhancers are annotated to overlapped candidate SNPs which are also overlapped with significant chromatin interactions (region 1).
							Promoters are annotated to regions which are significantly ineracted with risk loci (region 2). Dyadic enhancer/promoter regions are annotated for both. Please refer the tutorial for detils.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="multiSelect">
							<a style="float:right; padding-right:20px;">clear</a><br/>
							<select multiple class="form-control" id="ciMapRoadmap" name="ciMapRoadmap[]" size="10">
								<option value="all">All</option>
								<option class="level1" value="null">Adrenal (1)</option>
								<option class="level2" value="E080">E080 (Other) Fetal Adrenal Gland</option>
								<option class="level1" value="null">Blood (27)</option>
								<option class="level2" value="E029">E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
								<option class="level2" value="E030">E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
								<option class="level2" value="E031">E031 (HSC & B-cell) Primary B cells from cord blood</option>
								<option class="level2" value="E032">E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
								<option class="level2" value="E033">E033 (Blood & T-cell) Primary T cells from cord blood</option>
								<option class="level2" value="E034">E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
								<option class="level2" value="E035">E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
								<option class="level2" value="E036">E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
								<option class="level2" value="E037">E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
								<option class="level2" value="E038">E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
								<option class="level2" value="E039">E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
								<option class="level2" value="E040">E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
								<option class="level2" value="E041">E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
								<option class="level2" value="E042">E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
								<option class="level2" value="E043">E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
								<option class="level2" value="E044">E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
								<option class="level2" value="E045">E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
								<option class="level2" value="E046">E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
								<option class="level2" value="E047">E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
								<option class="level2" value="E048">E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
								<option class="level2" value="E050">E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
								<option class="level2" value="E051">E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
								<option class="level2" value="E062">E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
								<option class="level1" value="null">Bone (1)</option>
								<option class="level1" value="null">Brain (13)</option>
								<option class="level2" value="E053">E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
								<option class="level2" value="E054">E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
								<option class="level2" value="E067">E067 (Brain) Brain Angular Gyrus</option>
								<option class="level2" value="E068">E068 (Brain) Brain Anterior Caudate</option>
								<option class="level2" value="E069">E069 (Brain) Brain Cingulate Gyrus</option>
								<option class="level2" value="E070">E070 (Brain) Brain Germinal Matrix</option>
								<option class="level2" value="E071">E071 (Brain) Brain Hippocampus Middle</option>
								<option class="level2" value="E072">E072 (Brain) Brain Inferior Temporal Lobe</option>
								<option class="level2" value="E073">E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
								<option class="level2" value="E074">E074 (Brain) Brain Substantia Nigra</option>
								<option class="level2" value="E081">E081 (Brain) Fetal Brain Male</option>
								<option class="level2" value="E082">E082 (Brain) Fetal Brain Female</option>
								<option class="level1" value="null">Breast (3)</option>
								<option class="level2" value="E027">E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
								<option class="level2" value="E028">E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
								<option class="level1" value="null">Cervix (1)</option>
								<option class="level1" value="null">ESC (8)</option>
								<option class="level2" value="E001">E001 (ESC) ES-I3 Cells</option>
								<option class="level2" value="E002">E002 (ESC) ES-WA7 Cells</option>
								<option class="level2" value="E003">E003 (ESC) H1 Cells</option>
								<option class="level2" value="E008">E008 (ESC) H9 Cells</option>
								<option class="level2" value="E014">E014 (ESC) HUES48 Cells</option>
								<option class="level2" value="E015">E015 (ESC) HUES6 Cells</option>
								<option class="level2" value="E016">E016 (ESC) HUES64 Cells</option>
								<option class="level2" value="E024">E024 (ESC) ES-UCSF4  Cells</option>
								<option class="level1" value="null">ESC Derived (9)</option>
								<option class="level2" value="E004">E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
								<option class="level2" value="E005">E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
								<option class="level2" value="E006">E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
								<option class="level2" value="E007">E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
								<option class="level2" value="E009">E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
								<option class="level2" value="E010">E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
								<option class="level2" value="E011">E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
								<option class="level2" value="E012">E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
								<option class="level2" value="E013">E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
								<option class="level1" value="null">Fat (3)</option>
								<option class="level2" value="E023">E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
								<option class="level2" value="E025">E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
								<option class="level2" value="E063">E063 (Adipose) Adipose Nuclei</option>
								<option class="level1" value="null">GI Colon (3)</option>
								<option class="level2" value="E075">E075 (Digestive) Colonic Mucosa</option>
								<option class="level2" value="E076">E076 (Sm. Muscle) Colon Smooth Muscle</option>
								<option class="level2" value="E106">E106 (Digestive) Sigmoid Colon</option>
								<option class="level1" value="null">GI Duodenum (2)</option>
								<option class="level2" value="E077">E077 (Digestive) Duodenum Mucosa</option>
								<option class="level2" value="E078">E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
								<option class="level1" value="null">GI Esophagus (1)</option>
								<option class="level2" value="E079">E079 (Digestive) Esophagus</option>
								<option class="level1" value="null">GI Intestine (3)</option>
								<option class="level2" value="E084">E084 (Digestive) Fetal Intestine Large</option>
								<option class="level2" value="E085">E085 (Digestive) Fetal Intestine Small</option>
								<option class="level2" value="E109">E109 (Digestive) Small Intestine</option>
								<option class="level1" value="null">GI Rectum (3)</option>
								<option class="level2" value="E101">E101 (Digestive) Rectal Mucosa Donor 29</option>
								<option class="level2" value="E102">E102 (Digestive) Rectal Mucosa Donor 31</option>
								<option class="level2" value="E103">E103 (Sm. Muscle) Rectal Smooth Muscle</option>
								<option class="level1" value="null">GI Stomach (4)</option>
								<option class="level2" value="E092">E092 (Digestive) Fetal Stomach</option>
								<option class="level2" value="E094">E094 (Digestive) Gastric</option>
								<option class="level2" value="E110">E110 (Digestive) Stomach Mucosa</option>
								<option class="level2" value="E111">E111 (Sm. Muscle) Stomach Smooth Muscle</option>
								<option class="level1" value="null">Heart (4)</option>
								<option class="level2" value="E083">E083 (Heart) Fetal Heart</option>
								<option class="level2" value="E095">E095 (Heart) Left Ventricle</option>
								<option class="level2" value="E104">E104 (Heart) Right Atrium</option>
								<option class="level2" value="E105">E105 (Heart) Right Ventricle</option>
								<option class="level1" value="null">Kidney (1)</option>
								<option class="level2" value="E086">E086 (Other) Fetal Kidney</option>
								<option class="level1" value="null">Liver (2)</option>
								<option class="level2" value="E066">E066 (Other) Liver</option>
								<option class="level1" value="null">Lung (5)</option>
								<option class="level2" value="E017">E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
								<option class="level2" value="E088">E088 (Other) Fetal Lung</option>
								<option class="level2" value="E096">E096 (Other) Lung</option>
								<option class="level1" value="null">Muscle (7)</option>
								<option class="level2" value="E052">E052 (Myosat) Muscle Satellite Cultured Cells</option>
								<option class="level2" value="E089">E089 (Muscle) Fetal Muscle Trunk</option>
								<option class="level2" value="E100">E100 (Muscle) Psoas Muscle</option>
								<option class="level2" value="E107">E107 (Muscle) Skeletal Muscle Male</option>
								<option class="level2" value="E108">E108 (Muscle) Skeletal Muscle Female</option>
								<option class="level1" value="null">Muscle Leg (1)</option>
								<option class="level2" value="E090">E090 (Muscle) Fetal Muscle Leg</option>
								<option class="level1" value="null">Ovary (1)</option>
								<option class="level2" value="E097">E097 (Other) Ovary</option>
								<option class="level1" value="null">Pancreas (2)</option>
								<option class="level2" value="E087">E087 (Other) Pancreatic Islets</option>
								<option class="level2" value="E098">E098 (Other) Pancreas</option>
								<option class="level1" value="null">Placenta (2)</option>
								<option class="level2" value="E091">E091 (Other) Placenta</option>
								<option class="level2" value="E099">E099 (Other) Placenta Amnion</option>
								<option class="level1" value="null">Skin (8)</option>
								<option class="level2" value="E055">E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
								<option class="level2" value="E056">E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
								<option class="level2" value="E057">E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
								<option class="level2" value="E058">E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
								<option class="level2" value="E059">E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
								<option class="level2" value="E061">E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
								<option class="level1" value="null">Spleen (1)</option>
								<option class="level2" value="E113">E113 (Other) Spleen</option>
								<option class="level1" value="null">Stromal Connective (2)</option>
								<option class="level2" value="E026">E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
								<option class="level2" value="E049">E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
								<option class="level1" value="null">Thymus (2)</option>
								<option class="level2" value="E093">E093 (Thymus) Fetal Thymus</option>
								<option class="level2" value="E112">E112 (Thymus) Thymus</option>
								<option class="level1" value="null">Vascular (2)</option>
								<option class="level2" value="E065">E065 (Heart) Aorta</option>
								<option class="level1" value="null">iPSC (5)</option>
								<option class="level2" value="E018">E018 (iPSC) iPS-15b Cells</option>
								<option class="level2" value="E019">E019 (iPSC) iPS-18 Cells</option>
								<option class="level2" value="E020">E020 (iPSC) iPS-20b Cells</option>
								<option class="level2" value="E021">E021 (iPSC) iPS DF 6.9 Cells</option>
								<option class="level2" value="E022">E022 (iPSC) iPS DF 19.11 Cells</option>
							</select>
						</span>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="ciMapOptions">
					<td>Filter SNPs by enhancers
						<a class="infoPop" data-toggle="popover" title="Filter SNPs by enhancers" data-content="Only map SNPs which are overlapped with enhancers of selected epigenomes. Please select at least one epigenome to enable this option.
							If this option is not checked, all SNPs overlapped with chromatin interaction are used for mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" calss="form-control" name="ciMapEnhFilt", id="ciMapEnhFilt"></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr class="ciMapOptions">
					<td>Filter genes by promoters
						<a class="infoPop" data-toggle="popover" title="Filter genes by promoters" data-content="Only map to genes whoes promoter regions are overlap with promoters of selected epigenomes. Please select at least one epigenome to enable this option.
							If this option is not checked, all genes whose promoter reions are overlapped with the interacted regions are mapped.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" calss="form-control" name="ciMapPromFilt", id="ciMapPromFilt"></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<!-- </div> -->
			</table>

			<div id="ciMapOptFilt">
				Optional SNP filtering by functional annotation for chromatin interaction mapping<br/>
				<span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by chromatin interaction mapping criterion.<br/>
				All these annotations will be available for all SNPs within LD of identified lead SNPs in the result tables, but this filtering affect gene prioritization.
				</span>
				<table class="table table-bordered inputTable" id="ciMapOptFiltTable">
					<tr>
						<td rowspan="2">CADD</td>
						<td>Perform SNPs filtering based on CADD score.
							<a class="infoPop" data-toggle="popover" title="CADD score filtering" data-content="Please check this option to filter SNPs based on CADD score and spacify minimum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="ciMapCADDcheck" id="ciMapCADDcheck"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>Minimum CADD score (&ge;)
							<a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The heigher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="ciMapCADDth" name="ciMapCADDth" value="12.37"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td rowspan="2">RegulomeDB</td>
						<td>Perform SNPs filtering based on RegulomeDB score
							<a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="ciMapRDBcheck" id="ciMapRDBcheck"></td>
						<td></td>
					</tr>
					<tr>
						<td>Maximum RegulomeDB score (categorical)
							<a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least liekly. Some SNPs have 'NA' which are not assigned any score.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select class="form-control" id="ciMapRDBth" name="ciMapRDBth">
								<option>1a</option>
								<option>1b</option>
								<option>1c</option>
								<option>1d</option>
								<option>1e</option>
								<option>1f</option>
								<option>2a</option>
								<option>2b</option>
								<option>2c</option>
								<option>3a</option>
								<option>3b</option>
								<option>4</option>
								<option>5</option>
								<option>6</option>
								<option selected>7</option>
							</select>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td rowspan="4">15-core chromatin state</td>
						<td>Perform SNPs filtering based on chromatin state
							<a class="infoPop" data-toggle="popover" title="15-core chromatin state filtering" data-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="ciMapChr15check" id="ciMapChr15check"></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>Tissue/cell types for 15-core chromatin state<br/>
							<span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
						</td>
						<td>
							<span class="multiSelect">
								<a style="float:right; padding-right:20px;">clear</a><br/>
								<select multiple class="form-control" size="10" id="ciMapChr15Ts" name="ciMapChr15Ts[]">
									<option value="all">All</option>
									<option class="level1" value="null">Adrenal (1)</option>
									<option class="level2" value="E080">E080 (Other) Fetal Adrenal Gland</option>
									<option class="level1" value="null">Blood (23)</option>
									<option class="level2" value="E029">E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
									<option class="level2" value="E030">E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
									<option class="level2" value="E031">E031 (HSC & B-cell) Primary B cells from cord blood</option>
									<option class="level2" value="E032">E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
									<option class="level2" value="E033">E033 (Blood & T-cell) Primary T cells from cord blood</option>
									<option class="level2" value="E034">E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
									<option class="level2" value="E035">E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
									<option class="level2" value="E036">E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
									<option class="level2" value="E037">E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
									<option class="level2" value="E038">E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
									<option class="level2" value="E039">E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
									<option class="level2" value="E040">E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
									<option class="level2" value="E041">E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
									<option class="level2" value="E042">E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
									<option class="level2" value="E043">E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
									<option class="level2" value="E044">E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
									<option class="level2" value="E045">E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
									<option class="level2" value="E046">E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
									<option class="level2" value="E047">E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
									<option class="level2" value="E048">E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
									<option class="level2" value="E050">E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
									<option class="level2" value="E051">E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
									<option class="level2" value="E062">E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
									<option class="level1" value="null">Brain (12)</option>
									<option class="level2" value="E053">E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
									<option class="level2" value="E054">E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
									<option class="level2" value="E067">E067 (Brain) Brain Angular Gyrus</option>
									<option class="level2" value="E068">E068 (Brain) Brain Anterior Caudate</option>
									<option class="level2" value="E069">E069 (Brain) Brain Cingulate Gyrus</option>
									<option class="level2" value="E070">E070 (Brain) Brain Germinal Matrix</option>
									<option class="level2" value="E071">E071 (Brain) Brain Hippocampus Middle</option>
									<option class="level2" value="E072">E072 (Brain) Brain Inferior Temporal Lobe</option>
									<option class="level2" value="E073">E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
									<option class="level2" value="E074">E074 (Brain) Brain Substantia Nigra</option>
									<option class="level2" value="E081">E081 (Brain) Fetal Brain Male</option>
									<option class="level2" value="E082">E082 (Brain) Fetal Brain Female</option>
									<option class="level1" value="null">Breast (2)</option>
									<option class="level2" value="E027">E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
									<option class="level2" value="E028">E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
									<option class="level1" value="null">ESC (8)</option>
									<option class="level2" value="E001">E001 (ESC) ES-I3 Cells</option>
									<option class="level2" value="E002">E002 (ESC) ES-WA7 Cells</option>
									<option class="level2" value="E003">E003 (ESC) H1 Cells</option>
									<option class="level2" value="E008">E008 (ESC) H9 Cells</option>
									<option class="level2" value="E014">E014 (ESC) HUES48 Cells</option>
									<option class="level2" value="E015">E015 (ESC) HUES6 Cells</option>
									<option class="level2" value="E016">E016 (ESC) HUES64 Cells</option>
									<option class="level2" value="E024">E024 (ESC) ES-UCSF4  Cells</option>
									<option class="level1" value="null">ESC Derived (9)</option>
									<option class="level2" value="E004">E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
									<option class="level2" value="E005">E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
									<option class="level2" value="E006">E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
									<option class="level2" value="E007">E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
									<option class="level2" value="E009">E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
									<option class="level2" value="E010">E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
									<option class="level2" value="E011">E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
									<option class="level2" value="E012">E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
									<option class="level2" value="E013">E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
									<option class="level1" value="null">Fat (3)</option>
									<option class="level2" value="E023">E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
									<option class="level2" value="E025">E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
									<option class="level2" value="E063">E063 (Adipose) Adipose Nuclei</option>
									<option class="level1" value="null">GI Colon (3)</option>
									<option class="level2" value="E075">E075 (Digestive) Colonic Mucosa</option>
									<option class="level2" value="E076">E076 (Sm. Muscle) Colon Smooth Muscle</option>
									<option class="level2" value="E106">E106 (Digestive) Sigmoid Colon</option>
									<option class="level1" value="null">GI Duodenum (2)</option>
									<option class="level2" value="E077">E077 (Digestive) Duodenum Mucosa</option>
									<option class="level2" value="E078">E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
									<option class="level1" value="null">GI Esophagus (1)</option>
									<option class="level2" value="E079">E079 (Digestive) Esophagus</option>
									<option class="level1" value="null">GI Intestine (3)</option>
									<option class="level2" value="E084">E084 (Digestive) Fetal Intestine Large</option>
									<option class="level2" value="E085">E085 (Digestive) Fetal Intestine Small</option>
									<option class="level2" value="E109">E109 (Digestive) Small Intestine</option>
									<option class="level1" value="null">GI Rectum (3)</option>
									<option class="level2" value="E101">E101 (Digestive) Rectal Mucosa Donor 29</option>
									<option class="level2" value="E102">E102 (Digestive) Rectal Mucosa Donor 31</option>
									<option class="level2" value="E103">E103 (Sm. Muscle) Rectal Smooth Muscle</option>
									<option class="level1" value="null">GI Stomach (4)</option>
									<option class="level2" value="E092">E092 (Digestive) Fetal Stomach</option>
									<option class="level2" value="E094">E094 (Digestive) Gastric</option>
									<option class="level2" value="E110">E110 (Digestive) Stomach Mucosa</option>
									<option class="level2" value="E111">E111 (Sm. Muscle) Stomach Smooth Muscle</option>
									<option class="level1" value="null">Heart (4)</option>
									<option class="level2" value="E083">E083 (Heart) Fetal Heart</option>
									<option class="level2" value="E095">E095 (Heart) Left Ventricle</option>
									<option class="level2" value="E104">E104 (Heart) Right Atrium</option>
									<option class="level2" value="E105">E105 (Heart) Right Ventricle</option>
									<option class="level1" value="null">Kidney (1)</option>
									<option class="level2" value="E086">E086 (Other) Fetal Kidney</option>
									<option class="level1" value="null">Liver (1)</option>
									<option class="level2" value="E066">E066 (Other) Liver</option>
									<option class="level1" value="null">Lung (3)</option>
									<option class="level2" value="E017">E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
									<option class="level2" value="E088">E088 (Other) Fetal Lung</option>
									<option class="level2" value="E096">E096 (Other) Lung</option>
									<option class="level1" value="null">Muscle (5)</option>
									<option class="level2" value="E052">E052 (Myosat) Muscle Satellite Cultured Cells</option>
									<option class="level2" value="E089">E089 (Muscle) Fetal Muscle Trunk</option>
									<option class="level2" value="E100">E100 (Muscle) Psoas Muscle</option>
									<option class="level2" value="E107">E107 (Muscle) Skeletal Muscle Male</option>
									<option class="level2" value="E108">E108 (Muscle) Skeletal Muscle Female</option>
									<option class="level1" value="null">Muscle Leg (1)</option>
									<option class="level2" value="E090">E090 (Muscle) Fetal Muscle Leg</option>
									<option class="level1" value="null">Ovary (1)</option>
									<option class="level2" value="E097">E097 (Other) Ovary</option>
									<option class="level1" value="null">Pancreas (2)</option>
									<option class="level2" value="E087">E087 (Other) Pancreatic Islets</option>
									<option class="level2" value="E098">E098 (Other) Pancreas</option>
									<option class="level1" value="null">Placenta (2)</option>
									<option class="level2" value="E091">E091 (Other) Placenta</option>
									<option class="level2" value="E099">E099 (Other) Placenta Amnion</option>
									<option class="level1" value="null">Skin (6)</option>
									<option class="level2" value="E055">E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
									<option class="level2" value="E056">E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
									<option class="level2" value="E057">E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
									<option class="level2" value="E058">E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
									<option class="level2" value="E059">E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
									<option class="level2" value="E061">E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
									<option class="level1" value="null">Spleen (1)</option>
									<option class="level2" value="E113">E113 (Other) Spleen</option>
									<option class="level1" value="null">Stromal Connective (2)</option>
									<option class="level2" value="E026">E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
									<option class="level2" value="E049">E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
									<option class="level1" value="null">Thymus (2)</option>
									<option class="level2" value="E093">E093 (Thymus) Fetal Thymus</option>
									<option class="level2" value="E112">E112 (Thymus) Thymus</option>
									<option class="level1" value="null">Vascular (1)</option>
									<option class="level2" value="E065">E065 (Heart) Aorta</option>
									<option class="level1" value="null">iPSC (5)</option>
									<option class="level2" value="E018">E018 (iPSC) iPS-15b Cells</option>
									<option class="level2" value="E019">E019 (iPSC) iPS-18 Cells</option>
									<option class="level2" value="E020">E020 (iPSC) iPS-20b Cells</option>
									<option class="level2" value="E021">E021 (iPSC) iPS DF 6.9 Cells</option>
									<option class="level2" value="E022">E022 (iPSC) iPS DF 19.11 Cells</option>
								</select>
							</span>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>15-core chromatin state maximum state
							<a class="infoPop" data-toggle="popover" title="The maximum chromatin state" data-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="ciMapChr15Max" name="ciMapChr15Max" value="7"/></td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
					<tr>
						<td>15-core chromatin state filtering method
							<a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissye/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select  class="form-control" id="ciMapChr15Meth" name="ciMapChr15Meth">
								<option selected value="any">any</option>
								<option value="majority">majority</option>
								<option value="all">all</option>
							</select>
						</td>
						<td>
							<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-exclamation-circle"></i> Optional.</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>


	<!-- Gene type multiple selection -->
	<div class="panel panel-default" style="padding:0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>4. Gene types<a href="#NewJobGenePanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="NewJobGenePanel">
			<table class="table table-bordered inputTable" id="NewJobGene" style="width: auto;">
				<tr>
					<td>Gene type
						<a class="infoPop" data-toggle="popover" title="Gene Type" data-content="Setting gene type defines what kind of genes should be included in the gene prioritization. Gene type is based on gene biotype obtained from BioMart (Ensembl 85). By default, only protein-coding genes are used for mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a><br/>
						<span class="info"><i class="fa fa-info"></i> Multiple gene type can be selected.</span>
					</td>
					<td>
						<select multiple class="form-control" name="genetype[]" id="genetype">
					<option value="all">All</option>
					<option selected value="protein_coding">Protein coding</option>
					<option value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA">lncRNA</option>
					<option value="miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA">ncRNA</option>
					<option value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA:miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA:processed_transcript">Processed transcripts</option>
					<option value="pseudogene:processed_pseudogene:unprocessed_pseudogene:polymorphic_pseudogene:IG_C_pseudogene:IG_D_pseudogene:ID_V_pseudogene:IG_J_pseudogene:TR_C_pseudogene:TR_D_pseudogene:TR_V_pseudogene:TR_J_pseudogene">Pseudogene</option>
					<option value="IG_C_gene:TG_D_gene:TG_V_gene:IG_J_gene">IG genes</option>
					<option value="TR_C_gene:TR_D_gene:TR_V_gene:TR_J_gene">TR genes</option>
					</select>
					</td>
					<td>
						<div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">
							<i class="fa fa-check"></i> OK.
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- MHC regions -->
	<div class="panel panel-default" style="padding:0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>5. MHC region<a href="#NewJobMHCPanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="NewJobMHCPanel">
			<table class="table table-bordered inputTable" id="NewJobMHC" style="width: auto;">
				<tr>
					<td>Exclude MHC region
						<a class="infoPop" data-toggle="popover" title="Exclude MHC region" data-content="Please cehck to EXCLUDE MHC region; default MHC region is the genomic region between MOG and COL11A2 genes.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="form-inline">
							<input type="checkbox" class="form-check-input" name="MHCregion" id="MHCregion" value="exMHC" checked>
							<select class="form-control" id="MHCopt" name="MHCopt">
								<option value="all">from all (annotations and MAGMA)</option>
								<option selected value="annot">from only annotations</option>
								<option value="magma">from only MAGMA</option>
							</select>
						</span>
					</td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
				<tr>
					<td>Extended MHC region
						<a class="infoPop" data-toggle="popover" title="Extended MHC region" data-content="User defined MHC region. When this option is not given, the default MHC region will be used.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a><br/>
						<span class="info"><i class="fa fa-info"></i>e.g. 25000000-33000000<br/>
					</td>
					<td><input type="text" class="form-control" name="extMHCregion" id="extMHCregion"/></td>
					<td>
						<div class="alert alert-info" style="display: table-cell; padding-top:0; padding-bottom:0;">
						<i class="fa fa-exclamation-circle"></i> Optional.</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<span class="form-inline">
		<span style="font-size:18px;">Title of job submission</span>:
		<input type="text" class="form-control" name="NewJobTitle" id="NewJobTitle"/><br/>
		<span class="info"><i class="fa fa-info"></i>
			This is not mandatory, but job title might help you to track your jobs.
		</span>
	</span><br/><br/>

	<input class="btn btn-primary" type="submit" value="Submit Job" name="SubmitNewJob" id="SubmitNewJob"/>
	<span style="color: red; font-size:18px;">
		<i class="fa fa-exclamation-triangle"></i> After submitting, please wait a couple of seconds until the file is uploaded, and do not move away from the submission page.
	</span>
</div>
