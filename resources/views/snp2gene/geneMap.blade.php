<div id="geneMap" class="sidePanel container" style="padding-top:50px;">
	{!! Form::open(array('url' => 'snp2gene/geneMap', 'files' => true, 'novalidate'=>'novalidate')) !!}
	<h4 style="color: #00004d">Select jobID of your existing jobs to re-perform gene mapping with different settings.</h4>
	Re-dogin gene mapping does not require to upload any of input file, instead the selected job is duplicated
	with new jobID and perform gene mapping can be performed with different parameter settings.
	Only parameters of gene mappings can be modified, other parameters such as P-value and r2 for
	defining independent significant SNPs are fixed.
	<br/>
	<span class="info"><i class="fa fa-info"></i>
		User own files for chromatin interactions need to be uploaded again.
	</span>
	<br/><br/>

	<!-- load existing job -->
	<span class="form-inline" style="font-size:18px;">
		jobID:
		<select class="form-control" id="geneMapID" name="geneMapID" onchange="loadGeneMap();">
			<option value=0>None</option>
		</select>
		<a class="infoPop" data-toggle="popover" data-content="By selecting jobID of your existing SNP2GENE jobs,
		you can re-perform gene mapping (only if there is any existing job in your account).
		This load parameter settings that you used before, please change parameters before submission, otherwise results will be the same.
		">
			<i class="fa fa-question-circle-o fa-lg"></i>
		</a>
	</span>
	<br/><br/>

	<!-- Parameters for gene mapping -->
	<!-- positional mapping -->
	<div class="panel panel-default" style="padding:0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>3-1. Gene Mapping (positional mapping) <a href="#geneMapPosMapPanel" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
		</div>
		<div class="panel-body collapse in" id="geneMapPosMapPanel">
			<h4>Positional mapping</h4>
			<table class="table table-bordered inputTable" id="geneMapPosMap" style="width: auto;">
				<tr>
					<td>Perform positional mapping
						<a class="infoPop" data-toggle="popover" title="Positional maping" data-content="When checked, positional mapping will be carried out and includes functional consequences of SNPs on gene functions (such as exonic, intronic and splicing).">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" class="form-check-input" name="geneMap_posMap" id="geneMap_posMap" checked onchange="geneMapCheckAll();"></td>
					<td></td>
				</tr>
				<tr class="posMapOptions">
					<td>Distance to genes or <br>functional consequences of SNPs on genes to map
						<a class="infoPop" data-toggle="popover" title="Positional mapping" data-content="
							Positional mapping can be performed purly based on the physical distance between SNPs and genes by providing the maximum distance.
							Optionally, functional consequences of SNPs on genes can be selected to map only specific SNPs such as SNPs locating on exonic regions.
							Note that when functional consequences are selected, only SNPs location on the gene body (distance 0) are mapped to genes except upstream and downstream SNPs which are up to 1kb apart from TSS or TES.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="form-inline">Maximum distance: <input type="number" class="form-control" id="geneMap_posMapWindow" name="geneMap_posMapWindow" value="10" min="0" max="1000" onkeyup="geneMapCheckAll();" onpaste="geneMapCheckAll();" oninput="geneMapCheckAll();"> kb</span><br/>
						OR<br/>
						Functional consequences of SNPs on genes:<br/>
						<span class="geneMapMultiSelect">
							<a>clear</a><br/>
							<select multiple class="form-control" id="geneMap_posMapAnnot" name="geneMap_posMapAnnot[]" onchange="geneMapCheckAll();">
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
					<td></td>
				</tr>
			</table>

			<div id="geneMapposMapOptFilt">
				Optional SNP filtering by functional annotations for positional mapping<br/>
				<span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by positional mapping criterion. When eQTL mapping is also performed, this filtering can be specified separately.<br/>
					All these annotations will be available for all SNPs within LD of identified lead SNPs in the result tables, but this filtering affect gene prioritization.
				</span>
				<table class="table table-bordered inputTable" id="geneMap_posMapOptFiltTable" style="width: auto;">
					<tr>
						<td rowspan="2">CADD</td>
						<td>Perform SNPs filtering based on CADD score.
							<a class="infoPop" data-toggle="popover" title="CADD score filtering" data-content="Please check this option to filter SNPs based on CADD score and specify minimum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_posMapCADDcheck" id="geneMap_posMapCADDcheck" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Minimum CADD score (&ge;)
							<a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The higher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="geneMap_posMapCADDth" name="geneMap_posMapCADDth" value="12.37" onkeyup="geneMapCheckAll();" onpaste="geneMapCheckAll();" oninput="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td rowspan="2">RegulomeDB</td>
						<td>Perform SNPs filtering baed on ReguomeDB score
							<a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_posMapRDBcheck" id="geneMap_posMapRDBcheck" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Maximum RegulomeDB score (categorical)
							<a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least likely. Some SNPs have 'NA' which are not assigned any score.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<!-- <input type="text" class="form-control" id="geneMap_posMapRDBth" name="geneMap_posMapRDBth" value="7" style="width: 80px;"> -->
							<select class="form-control" id="geneMap_posMapRDBth" name="geneMap_posMapRDBth" onchange="geneMapCheckAll();">
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
						<td></td>
					</tr>
					<tr>
						<td rowspan="4">15-core chromatin state</td>
						<td>Perform SNPs filtering based on chromatin state
							<a class="infoPop" data-toggle="popover" title="15-core chromatin state filtering" data-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_posMapChr15check" id="geneMap_posMapChr15check" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Tissue/cell types for 15-core chromatin state<br/>
							<span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
						</td>
						<td>
							<span class="geneMapMultiSelect">
								<a style="float:right; padding-right:20px;">clear</a><br/>
								<select multiple class="form-control" size="10" id="geneMap_posMapChr15Ts" name="geneMap_posMapChr15Ts[]" onchange="geneMapCheckAll();">
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
						<td></td>
					</tr>
					<tr>
						<td>15-core chromatin state maximum state
							<a class="infoPop" data-toggle="popover" title="The maximum chromatin state" data-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="geneMap_posMapChr15Max" name="geneMap_posMapChr15Max" value="7" onkeyup="geneMapCheckAll();" onpaste="geneMapCheckAll();" oninput="geneMapCheckAll();"/></td>
						<td></td>
					</tr>
					<tr>
						<td>15-core chromatin state filtering method
							<a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissue/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select  class="form-control" id="geneMap_posMapChr15Meth" name="geneMap_posMapChr15Meth" onchange="geneMapCheckAll();">
								<option selected value="any">any</option>
								<option value="majority">majority</option>
								<option value="all">all</option>
							</select>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<!-- eqtl mapping -->
	<div class="panel panel-default" style="padding: 0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>3-2. Gene Mapping (eQTL mapping)<a href="#geneMapEqtlMapPanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="geneMapEqtlMapPanel">
			<h4>eQTL mapping</h4>
			<table class="table table-bordered inputTable" id="geneMapEqtlMap" style="width: auto;">
				<tr>
					<td>Perform eQTL mapping
						<a class="infoPop" data-toggle="popover" title="eQTL mapping" data-content="eQTL mapping maps SNPs to genes based on eQTL information. This maps SNPs to genes up to 1 Mb part (cis-eQTL). Please check this option to perform eQTL mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" calss="form-control" name="geneMap_eqtlMap", id="geneMap_eqtlMap" onchange="geneMapCheckAll();"></td>
					<td></td>
				</tr>
				<tr class="eqtlMapOptions">
					<td>Tissue types
						<a class="infoPop" data-toggle="popover" title="Tissue types of eQTLs" data-content="This is mandatory parameter for eQTL mapping. Currently 44 tissue types from GTEx and two large scale eQTL study of blood cell are available.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="geneMapMultiSelect">
							<a class="clear" style="float:right; padding-right:20px;">Clear</a>
							<a class="all" style="float:right; padding-right:20px;">Select all</a></br>
							<select multiple class="form-control" id="geneMap_eqtlMapTs" name="geneMap_eqtlMapTs[]" size="10" onchange="geneMapCheckAll();">
								@include('snp2gene.eqtl_options')
							</select>
						</span>
						<span class="info"><i class="fa fa-info"></i>
							From FUMA v1.3.0, a data set of GTEx v7 has been added.<br/>
							When the "all" option is selected, both GTEx v6 and v7 will be used.<br/>
							To avoid this, please manually select either GTEx v6 or v7.
							GTEx v6 is located at the bottom of the options.
						</span>
					</td>
					<td></td>
				</tr>
				<tr class="eqtlMapOptions">
					<td>eQTL P-value threshold
						<a class="infoPop" data-toggle="popover" title="eQTL P-value threshold" data-content="By default, only significant eQTLs are used (FDR &lt; 0.05). Please UNCHECK 'Use only significant snp-gene pair' to filter eQTLs based on raw P-value.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="form-inline">Use only significant snp-gene pairs: <input type="checkbox" class="form-control" name="sigeqtlCheck" id="sigeqtlCheck" checked onchange="geneMapCheckAll();"> (FDR&lt;0.05)</span><br/>
						OR<br/>
						<span class="form-inline">(nominal) P-value cutoff (&lt;): <input type="number" class="form-control" name="eqtlP" id="eqtlP" value="1e-3" onchange="geneMapCheckAll();"></span>
					</td>
					<td></td>
				</tr>
			</table>

			<div id="geneMap_eqtlMapOptFilt">
				Optional SNP filtering by functional annotation for eQTL mapping<br/>
				<span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by eQTL mapping criterion.<br/>
					All these annotations will be available for all SNPs within LD of identified lead SNPs in the result tables, but this filtering affect gene prioritization.
				</span>
				<table class="table table-bordered inputTable" id="geneMap_eqtlMapOptFiltTable">
					<tr>
						<td rowspan="2">CADD</td>
						<td>Perform SNPs filtering based on CADD score.
							<a class="infoPop" data-toggle="popover" title="CADD score filtering" data-content="Please check this option to filter SNPs based on CADD score and specify minimum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_eqtlMapCADDcheck" id="geneMap_eqtlMapCADDcheck" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Minimum CADD score (&ge;)
							<a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The higher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="geneMap_eqtlMapCADDth" name="geneMap_eqtlMapCADDth" value="12.37" onkeyup="geneMapCheckAll();" onpaste="geneMapCheckAll();" oninput="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td rowspan="2">RegulomeDB</td>
						<td>Perform SNPs filtering baed on ReguomeDB score
							<a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_eqtlMapRDBcheck" id="geneMap_eqtlMapRDBcheck" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Maximum RegulomeDB score (categorical)
							<a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least likely. Some SNPs have 'NA' which are not assigned any score.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<!-- <input type="text" class="form-control" id="geneMap_eqtlMapRDBth" name="geneMap_eqtlMapRDBth" value="7"> -->
							<select class="form-control" id="geneMap_eqtlMapRDBth" name="geneMap_eqtlMapRDBth" onchange="geneMapCheckAll();">
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
						<td></td>
					</tr>
					<tr>
						<td rowspan="4">15-core chromatin state</td>
						<td>Perform SNPs filtering based on chromatin state
							<a class="infoPop" data-toggle="popover" title="15-core chromatin state filtering" data-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_eqtlMapChr15check" id="geneMap_eqtlMapChr15check" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Tissue/cell types for 15-core chromatin state<br/>
							<span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
						</td>
						<td>
							<span class="geneMapMultiSelect">
								<a style="float:right; padding-right:20px;">clear</a><br/>
								<select multiple class="form-control" size="10" id="geneMap_eqtlMapChr15Ts" name="geneMap_eqtlMapChr15Ts[]" onchange="geneMapCheckAll();">
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
						<td></td>
					</tr>
					<tr>
						<td>15-core chromatin state maximum state
							<a class="infoPop" data-toggle="popover" title="The maximum chromatin state" data-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="geneMap_eqtlMapChr15Max" name="geneMap_eqtlMapChr15Max" value="7" onkeyup="geneMapCheckAll();" onpaste="geneMapCheckAll();" oninput="geneMapCheckAll();"/></td>
						<td></td>
					</tr>
					<tr>
						<td>15-core chromatin state filtering method
							<a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissue/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select  class="form-control" id="geneMap_eqtlMapChr15Meth" name="geneMap_eqtlMapChr15Meth" onchange="geneMapCheckAll();">
								<option selected value="any">any</option>
								<option value="majority">majority</option>
								<option value="all">all</option>
							</select>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<!-- chromatin interaction mapping -->
	<div class="panel panel-default" style="padding: 0px;">
		<div class="panel-heading input" style="padding:5px;">
			<h4>3-3. Gene Mapping (3D Chromatin Interaction mapping)<a href="#geneMapCiMapPanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
		</div>
		<div class="panel-body collapse" id="geneMapCiMapPanel">
			<h4>chromatin interaction mapping</h4>
			<table class="table table-bordered inputTable" id="geneMapCiMap" style="width: auto;">
				<tr>
					<td>Perform chromatin interaction mapping
						<a class="infoPop" data-toggle="popover" title="3D chromatin interaction mapping" data-content="3D chromatin interaction mapping maps SNPs to genes based on chromatin interactions such as Hi-C and ChIA-PET. Please check to perform this mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" calss="form-control" name="geneMap_ciMap", id="geneMap_ciMap" onchange="geneMapCheckAll();"></td>
					<td></td>
				</tr>
				<tr class="ciMapOptions">
					<td>Builtin chromatin interaction data
						<a class="infoPop" data-toggle="popover" title="Buildin Hi-C data" data-content="Hi-C datasets of 21 tissue and cell types from GSE87112 are selectable as build-in data. Multiple tissue and cell types can be selected.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="geneMapMultiSelect">
							<a style="float:right; padding-right:20px;">clear</a><br/>
							<select multiple class="form-control" id="geneMap_ciMapBuiltin" name="geneMap_ciMapBuiltin[]" size="10" onchange="geneMapCheckAll();">
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
								<option value="HiC/Giusti-Rodriguez_et_al_2019/Adult_Cortex.txt.gz">HiC(Giusti-Rodriguez et al. 2019) Adult cortex</option>
								<option value="HiC/Giusti-Rodriguez_et_al_2019/Fetal_Cortex.txt.gz">HiC(Giusti-Rodriguez et al. 2019) Fetal cortex</option>
							</select>
						</span>
					</td>
					<td></td>
				</tr>
				<tr class="ciMapOptions">
					<td>Custom chromatin interaction matrices
						<a class="infoPop" data-toggle="popover" title="Custom chromatin interaction matrices"
							data-content="Please upload files of custom chromatin interaction matrices (significant loops). The input files have to follow the specific format. Please refer the tutorial for details. The file name should be '(Name_of_the_data).txt.gz' in which (Name_of_the_data) will be used in the results table.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span id="ciFiles"></span><br/>
						<button type="button" class="btn btn-default btn-xs" id="ciFileAdd">add file</button>
						<input type="hidden" value="0" id="ciFileN" name="ciFileN">
					</td>
					<td></td>
				</tr>
				<tr class="ciMapOptions">
					<td>FDR threshold
						<a class="infoPop" data-toggle="popover" title="FDR threshold for significant interaction" data-content="Significance of interaction for build-in Hi-C datasets are computed by Fit-Hi-C (see tutorial for details). The default threshold is FDR &le; 1e-6 as suggested by Schmit et al. (2016).">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="form-inline">FDR cutoff (&lt;): <input type="number" class="form-control" name="geneMap_ciMapFDR" id="geneMap_ciMapFDR" value="1e-6" onchange="geneMapCheckAll();"></span>
					</td>
					<td></td>
				</tr>
				<tr class="ciMapOptions">
					<td>Promoter region window
						<a class="infoPop" data-toggle="popover" title="Promoter region window" data-content="The window of promoter regions are used to overlap TSS of genes with significantly interacted regions with risk loci.
							By default, promoter region is defined as 250bp upstream and 500bp downstream of TSS. Genes whose promoter regions are overlapped with the interacted region are used for gene mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="text" class="form-control" name="geneMap_ciMapPromWindow" id="geneMap_ciMapPromWindow" value="250-500" onchange="geneMapCheckAll();">
						<span class="info"><i class="fa fa-info"></i>
							Please specify both upstream and downstream from TSS. For example, "250-500" means 250bp upstream and 500bp downstream from TSS.
						</span>
					</td>
					<td></td>
				</tr>
				<tr class="ciMapOptions">
					<td>Annotate enhancer/promoter regions (Roadmap 111 epigenomes)
						<a class="infoPop" data-toggle="popover" title="Enhancer/promoter regions" data-content="Enhancers are annotated to overlapped candidate SNPs which are also overlapped with significant chromatin interactions (region 1).
							Promoters are annotated to regions which are significantly interacted with risk loci (region 2). Dyadic enhancer/promoter regions are annotated for both. Please refer the tutorial for details.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td>
						<span class="geneMapMultiSelect">
							<a style="float:right; padding-right:20px;">clear</a><br/>
							<select multiple class="form-control" id="geneMap_ciMapRoadmap" name="geneMap_ciMapRoadmap[]" size="10" onchange="geneMapCheckAll();">
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
					<td></td>
				</tr>
				<tr class="ciMapOptions">
					<td>Filter SNPs by enhancers
						<a class="infoPop" data-toggle="popover" title="Filter SNPs by enhancers" data-content="Only map SNPs which are overlapped with enhancers of selected epigenomes. Please select at least one epigenome to enable this option.
							If this option is not checked, all SNPs overlapped with chromatin interaction are used for mapping.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" calss="form-control" name="geneMap_ciMapEnhFilt", id="geneMap_ciMapEnhFilt" onchange="geneMapCheckAll();"></td>
					<td></td>
				</tr>
				<tr class="ciMapOptions">
					<td>Filter genes by promoters
						<a class="infoPop" data-toggle="popover" title="Filter genes by promoters" data-content="Only map to genes whose promoter regions are overlap with promoters of selected epigenomes. Please select at least one epigenome to enable this option.
							If this option is not checked, all genes whose promoter regions are overlapped with the interacted regions are mapped.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
					</td>
					<td><input type="checkbox" calss="form-control" name="geneMap_ciMapPromFilt", id="geneMap_ciMapPromFilt" onchange="geneMapCheckAll();"></td>
					<td></td>
				</tr>
				<!-- </div> -->
			</table>

			<div id="geneMap_ciMapOptFilt">
				Optional SNP filtering by functional annotation for chromatin interaction mapping<br/>
				<span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by chromatin interaction mapping criterion.<br/>
					All these annotations will be available for all SNPs within LD of identified lead SNPs in the result tables, but this filtering affect gene prioritization.
				</span>
				<table class="table table-bordered inputTable" id="geneMap_ciMapOptFiltTable">
					<tr>
						<td rowspan="2">CADD</td>
						<td>Perform SNPs filtering based on CADD score.
							<a class="infoPop" data-toggle="popover" title="CADD score filtering" data-content="Please check this option to filter SNPs based on CADD score and specify minimum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_ciMapCADDcheck" id="geneMap_ciMapCADDcheck" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Minimum CADD score (&ge;)
							<a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The higher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="geneMap_ciMapCADDth" name="geneMap_ciMapCADDth" value="12.37" onkeyup="geneMapCheckAll();" onpaste="geneMapCheckAll();" oninput="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td rowspan="2">RegulomeDB</td>
						<td>Perform SNPs filtering baed on ReguomeDB score
							<a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_ciMapRDBcheck" id="geneMap_ciMapRDBcheck" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Maximum RegulomeDB score (categorical)
							<a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least likely. Some SNPs have 'NA' which are not assigned any score.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select class="form-control" id="geneMap_ciMapRDBth" name="geneMap_ciMapRDBth" onchange="geneMapCheckAll();">
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
					<td></td>
					</tr>
					<tr>
						<td rowspan="4">15-core chromatin state</td>
						<td>Perform SNPs filtering based on chromatin state
							<a class="infoPop" data-toggle="popover" title="15-core chromatin state filtering" data-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="checkbox" class="form-check-input" name="geneMap_ciMapChr15check" id="geneMap_ciMapChr15check" onchange="geneMapCheckAll();"></td>
						<td></td>
					</tr>
					<tr>
						<td>Tissue/cell types for 15-core chromatin state<br/>
							<span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
						</td>
						<td>
							<span class="geneMapMultiSelect">
								<a style="float:right; padding-right:20px;">clear</a><br/>
								<select multiple class="form-control" size="10" id="geneMap_ciMapChr15Ts" name="geneMap_ciMapChr15Ts[]" onchange="geneMapCheckAll();">
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
						<td></td>
					</tr>
					<tr>
						<td>15-core chromatin state maximum state
							<a class="infoPop" data-toggle="popover" title="The maximum chromatin state" data-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td><input type="number" class="form-control" id="geneMap_ciMapChr15Max" name="geneMap_ciMapChr15Max" value="7" onkeyup="geneMapCheckAll();" onpaste="geneMapCheckAll();" oninput="geneMapCheckAll();"/></td>
						<td></td>
					</tr>
					<tr>
						<td>15-core chromatin state filtering method
							<a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissue/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</td>
						<td>
							<select  class="form-control" id="geneMap_ciMapChr15Meth" name="geneMap_ciMapChr15Meth" onchange="geneMapCheckAll();">
								<option selected value="any">any</option>
								<option value="majority">majority</option>
								<option value="all">all</option>
							</select>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<!-- Title -->
	<span class="form-inline">
		<span style="font-size:18px;">Title of job submission</span>:
		<input type="text" class="form-control" name="geneMapTitle" id="geneMapTitle"/><br/>
		<span class="info"><i class="fa fa-info"></i>
			Suffix (e.g. "_copied_100" when jobID 100 is selected) will be automatically added to the title.
		</span>
	</span><br/><br/>

	<input class="btn btn-default" type="submit" value="Submit Job" name="SubmitGeneMap" id="SubmitGeneMap"/>
	<span style="color: red; font-size:18px;">
		<i class="fa fa-exclamation-triangle"></i> After submitting, please wait until the file is uploaded, and do not move away from the submission page.
	</span>
	{!! Form::close() !!}
</div>
