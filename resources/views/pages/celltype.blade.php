@extends('layouts.master')
@section('head')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.0/js/bootstrap-select.min.js"></script>
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}?130">
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<script type="text/javascript" src="//d3js.org/d3.v3.min.js"></script>
<script src="//labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
<script type="text/javascript" src="//d3js.org/queue.v1.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}"/>
<script type="text/javascript">
$.ajaxSetup({
	headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
});
var id = "{{$id}}";
var status = "{{$status}}";
var page = "{{$page}}";
var prefix = "{{$prefix}}";
var subdir = "{{ Config::get('app.subdir') }}";
var loggedin = "{{ Auth::check() }}";
</script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}?131"></script>
<script type="text/javascript" src="{!! URL::asset('js/cell_results.js') !!}?131"></script>
<script type="text/javascript" src="{!! URL::asset('js/celltype.js') !!}?134"></script>
@stop
@section('content')
<div id="wrapper" class="active">
	<div id="sidebar-wrapper">
		<ul class="sidebar-nav" id="sidebar-menu">
			<li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
		</ul>
		<ul class="sidebar-nav" id="sidebar">
			<li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
			<li class="active"><a href="#joblist">My Jobs<i class="sub_icon fa fa-history"></i></a></li>
			<!-- <li class="active"><a href="#DIY">Do It Yourself<i class="sub_icon fa fa-wrench"></i></a></li> -->
			<div id="resultSide">
				<!-- <li><a href="#Summary">Summary<i class="sub_icon fa fa-table"></i></a></li> -->
				<li><a href="#result">Results<i class="sub_icon fa fa-bar-chart"></i></a></li>
			</div>
		</ul>
	</div>

	<div id="page-content-wrapper">
		<div class="page-content inset">
			<div id="newJob" class="sidePanel container" style="padding-top:50px;">
				{!! Form::open(array('url' => 'celltype/submit', 'files'=>true, 'novalidate'=>'novalidate')) !!}
				<div class="panel panel-default">
					<div class="panel-body" style="padding-bottom: 10;">
						<h4>MAGMA gene analysis result</h4>
						1. Select from existing SNP2GENE job<br/>
						<span class="info"><i class="fa fa-info"></i>
							You can only select one of the succeeded SNP2GENE jobs in your account.<br/>
							When you select a job ID, FUMA will automatically check if MAGMA was performed in the selected job.
						</span>
						<select class="form-control" id="s2gID" name="s2gID" onchange="CheckInput();">
						</select>
						<br/>
						2. Upload your own genes.raw file<br/>
						<span class="info"><i class="fa fa-info"></i>
							You can only upload a file with extension "genes.raw"
							which is an output of MAGMA gene analysis.
						</span>
						<input type="file" class="form-control-file" name="genes_raw" id="genes_raw" onchange="CheckInput();"/>
						<span class="form-inline">
							<input type="checkbox" checked class="form-check-input" name="ensg_id" i="ensg_id"/>
							: Ensembl gene ID is used in the provided file.
							<a class="infoPop" data-toggle="popover" data-content="Please UNCHECK this option if you used different gene ID than Ensembl gene ID
							in your uploaded MAGMA output. In that case, provided genes will be mapped to Ensembl gene ID.">
								<i class="fa fa-question-circle-o fa-lg"></i>
							</a>
						</span>
						<br/>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-body" style="padding-bottom: 10;">
						<h4>Single-cell expression data sets</h4>
						Select single-cell expression data sets to perform MAGMA gene-property analysis<br/>
						<span class="info"><i class="fa fa-info"></i>
							You should not select all datasets if you want to perform step 2 and 3 of the workflow
							due to the duplicated cell types in multiple datasets from the same data resource.
							For example, Tabula Muris FACS data have one dataset with all cell types from all tissues and
							other datasets for each tissue separately.
							Therefore, "endothelial cell" in Lung sample in the dataset with all tissues is
							exactly the same as "endothelial cell" in Lung dataset.
							This applies to data resource with multiple levels, where level 1 cell types include level 2 cell types.
							In addition, step 2 is only performed after multiple testing correction across all the cell types tested in the step 1
							regardless of duplications of the cell types.
							It is strongly recommended to carefully select datasets to test beforehand.
						</span>
						<select multiple size="10" class="form-control" id="cellDataSets" name="cellDataSets[]" onchange="CheckInput();">
							<option value="PsychENCODE_Developmental">PsychENCODE_Developmental</option>
							<option value="PsychENCODE_Adult">PsychENCODE_Adult</option>
							<option value="GSE97478_Mouse_Striatum_Cortex">GSE97478_Mouse_Striatum_Cortex</option>
							<option value="GSE106707_Mouse_Striatum_Cortex">GSE106707_Mouse_Striatum_Cortex</option>
							<option value="Allen_Human_LGN_level1">Allen_Human_LGN_level1</option>
							<option value="Allen_Human_LGN_level2">Allen_Human_LGN_level2</option>
							<option value="Allen_Human_MTG_level1">Allen_Human_MTG_level1</option>
							<option value="Allen_Human_MTG_level2">Allen_Human_MTG_level2</option>
							<option value="Allen_Mouse_ALM2_level1">Allen_Mouse_ALM2_level1</option>
							<option value="Allen_Mouse_ALM2_level2">Allen_Mouse_ALM2_level2</option>
							<option value="Allen_Mouse_ALM2_level3">Allen_Mouse_ALM2_level3</option>
							<option value="Allen_Mouse_LGd2_level1">Allen_Mouse_LGd2_level1</option>
							<option value="Allen_Mouse_LGd2_level2">Allen_Mouse_LGd2_level2</option>
							<option value="Allen_Mouse_LGd2_level3">Allen_Mouse_LGd2_level3</option>
							<option value="Allen_Mouse_VISp2_level1">Allen_Mouse_VISp2_level1</option>
							<option value="Allen_Mouse_VISp2_level2">Allen_Mouse_VISp2_level2</option>
							<option value="Allen_Mouse_VISp2_level3">Allen_Mouse_VISp2_level3</option>
							<option value="Allen_Mouse_ALM_level1">Allen_Mouse_ALM_level1</option>
							<option value="Allen_Mouse_ALM_level2">Allen_Mouse_ALM_level2</option>
							<option value="Allen_Mouse_LGd_level1">Allen_Mouse_LGd_level1</option>
							<option value="Allen_Mouse_LGd_level2">Allen_Mouse_LGd_level2</option>
							<option value="Allen_Mouse_VISp_level1">Allen_Mouse_VISp_level1</option>
							<option value="Allen_Mouse_VISp_level2">Allen_Mouse_VISp_level2</option>
							<option value="DroNc_Human_Hippocampus">DroNc_Human_Hippocampus</option>
							<option value="DroNc_Mouse_Hippocampus">DroNc_Mouse_Hippocampus</option>
							<option value="DropViz_all_level1">DropViz_all_level1</option>
							<option value="DropViz_all_level2">DropViz_all_level2</option>
							<option value="DropViz_CB_level1">DropViz_CB_level1</option>
							<option value="DropViz_CB_level2">DropViz_CB_level2</option>
							<option value="DropViz_ENT_level1">DropViz_ENT_level1</option>
							<option value="DropViz_ENT_level2">DropViz_ENT_level2</option>
							<option value="DropViz_FC_level1">DropViz_FC_level1</option>
							<option value="DropViz_FC_level2">DropViz_FC_level2</option>
							<option value="DropViz_GP_level1">DropViz_GP_level1</option>
							<option value="DropViz_GP_level2">DropViz_GP_level2</option>
							<option value="DropViz_HC_level1">DropViz_HC_level1</option>
							<option value="DropViz_HC_level2">DropViz_HC_level2</option>
							<option value="DropViz_PC_level1">DropViz_PC_level1</option>
							<option value="DropViz_PC_level2">DropViz_PC_level2</option>
							<option value="DropViz_SN_level1">DropViz_SN_level1</option>
							<option value="DropViz_SN_level2">DropViz_SN_level2</option>
							<option value="DropViz_STR_level1">DropViz_STR_level1</option>
							<option value="DropViz_STR_level2">DropViz_STR_level2</option>
							<option value="DropViz_TH_level1">DropViz_TH_level1</option>
							<option value="DropViz_TH_level2">DropViz_TH_level2</option>
							<option value="GSE100597_Mouse_Embryo">GSE100597_Mouse_Embryo</option>
							<option value="GSE104276_Human_Prefrontal_cortex_all_ages">GSE104276_Human_Prefrontal_cortex_all_ages</option>
							<option value="GSE104276_Human_Prefrontal_cortex_per_ages">GSE104276_Human_Prefrontal_cortex_per_ages</option>
							<option value="GSE106678_Mouse_Cortex">GSE106678_Mouse_Cortex</option>
							<option value="GSE67835_Human_Cortex">GSE67835_Human_Cortex</option>
							<option value="GSE67835_Human_Cortex_woFetal">GSE67835_Human_Cortex_woFetal</option>
							<option value="GSE81547_Human_Pancreas">GSE81547_Human_Pancreas</option>
							<option value="GSE82187_Mouse_Striatum">GSE82187_Mouse_Striatum</option>
							<option value="GSE84133_Human_Pancreas">GSE84133_Human_Pancreas</option>
							<option value="GSE84133_Mouse_Pancreas">GSE84133_Mouse_Pancreas</option>
							<option value="GSE87544_Mouse_Hypothalamus">GSE87544_Mouse_Hypothalamus</option>
							<option value="GSE89164_Mouse_Hindbrain">GSE89164_Mouse_Hindbrain</option>
							<option value="GSE89232_Human_Blood">GSE89232_Human_Blood</option>
							<option value="GSE92332_Mouse_Epithelium_SMARTseq">GSE92332_Mouse_Epithelium_SMARTseq</option>
							<option value="GSE92332_Mouse_Epithelium_droplet">GSE92332_Mouse_Epithelium_droplet</option>
							<option value="GSE93374_Mouse_Arc_ME_level1">GSE93374_Mouse_Arc_ME_level1</option>
							<option value="GSE93374_Mouse_Arc_ME_level2">GSE93374_Mouse_Arc_ME_level2</option>
							<option value="GSE93374_Mouse_Arc_ME_neurons">GSE93374_Mouse_Arc_ME_neurons</option>
							<option value="GSE98816_Mouse_Brain_Vascular">GSE98816_Mouse_Brain_Vascular</option>
							<option value="GSE99235_Mouse_Lung_Vascular">GSE99235_Mouse_Lung_Vascular</option>
							<option value="Linnarsson_GSE101601_Human_Temporal_cortex">Linnarsson_GSE101601_Human_Temporal_cortex</option>
							<option value="Linnarsson_GSE101601_Mouse_Somatosensory_cortex">Linnarsson_GSE101601_Mouse_Somatosensory_cortex</option>
							<option value="Linnarsson_GSE103840_Mouse_Dorsal_horn">Linnarsson_GSE103840_Mouse_Dorsal_horn</option>
							<option value="Linnarsson_GSE104323_Mouse_Dentate_gyrus">Linnarsson_GSE104323_Mouse_Dentate_gyrus</option>
							<option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level1">Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level1</option>
							<option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level2">Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level2</option>
							<option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level3">Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level3</option>
							<option value="Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level1">Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level1</option>
							<option value="Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level2">Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level2</option>
							<option value="Linnarsson_GSE67602_Mouse_Skin_Epidermis">Linnarsson_GSE67602_Mouse_Skin_Epidermis</option>
							<option value="Linnarsson_GSE74672_Mouse_Hypothalamus_Neurons_level2">Linnarsson_GSE74672_Mouse_Hypothalamus_Neurons_level2</option>
							<option value="Linnarsson_GSE74672_Mouse_Hypothalamus_level1">Linnarsson_GSE74672_Mouse_Hypothalamus_level1</option>
							<option value="Linnarsson_GSE75330_Mouse_Oligodendrocytes">Linnarsson_GSE75330_Mouse_Oligodendrocytes</option>
							<option value="Linnarsson_GSE76381_Human_Midbrain">Linnarsson_GSE76381_Human_Midbrain</option>
							<option value="Linnarsson_GSE76381_Mouse_Midbrain">Linnarsson_GSE76381_Mouse_Midbrain</option>
							<option value="Linnarsson_GSE78845_Mouse_Ganglia">Linnarsson_GSE78845_Mouse_Ganglia</option>
							<option value="Linnarsson_GSE95315_Mouse_Dentate_gyrus">Linnarsson_GSE95315_Mouse_Dentate_gyrus</option>
							<option value="Linnarsson_GSE95752_Mouse_Dentate_gyrus">Linnarsson_GSE95752_Mouse_Dentate_gyrus</option>
							<option value="Linnarsson_MouseBrainAtlas_level5">Linnarsson_MouseBrainAtlas_level5</option>
							<option value="Linnarsson_MouseBrainAtlas_level6_rank1">Linnarsson_MouseBrainAtlas_level6_rank1</option>
							<option value="Linnarsson_MouseBrainAtlas_level6_rank2">Linnarsson_MouseBrainAtlas_level6_rank2</option>
							<option value="Linnarsson_MouseBrainAtlas_level6_rank3">Linnarsson_MouseBrainAtlas_level6_rank3</option>
							<option value="Linnarsson_MouseBrainAtlas_level6_rank4">Linnarsson_MouseBrainAtlas_level6_rank4</option>
							<option value="MouseCellAtlas_all">MouseCellAtlas_all</option>
							<option value="MouseCellAtlas_Adult_all">MouseCellAtlas_Adult_all</option>
							<option value="MouseCellAtlas_Bladder">MouseCellAtlas_Bladder</option>
							<option value="MouseCellAtlas_Bone_Marrow">MouseCellAtlas_Bone_Marrow</option>
							<option value="MouseCellAtlas_Brain">MouseCellAtlas_Brain</option>
							<option value="MouseCellAtlas_Embryo_all">MouseCellAtlas_Embryo_all</option>
							<option value="MouseCellAtlas_Embryonic_Mesenchyme">MouseCellAtlas_Embryonic_Mesenchyme</option>
							<option value="MouseCellAtlas_Embryonic_Stem_Cell">MouseCellAtlas_Embryonic_Stem_Cell</option>
							<option value="MouseCellAtlas_Fetal_Brain">MouseCellAtlas_Fetal_Brain</option>
							<option value="MouseCellAtlas_Fetal_Intestine">MouseCellAtlas_Fetal_Intestine</option>
							<option value="MouseCellAtlas_Fetal_Liver">MouseCellAtlas_Fetal_Liver</option>
							<option value="MouseCellAtlas_Fetal_Lung">MouseCellAtlas_Fetal_Lung</option>
							<option value="MouseCellAtlas_Fetal_Stomache">MouseCellAtlas_Fetal_Stomache</option>
							<option value="MouseCellAtlas_Kidney">MouseCellAtlas_Kidney</option>
							<option value="MouseCellAtlas_Liver">MouseCellAtlas_Liver</option>
							<option value="MouseCellAtlas_Lung">MouseCellAtlas_Lung</option>
							<option value="MouseCellAtlas_Mammary_Gland">MouseCellAtlas_Mammary_Gland</option>
							<option value="MouseCellAtlas_Mesenchymal_Stem_Cell_Cultured">MouseCellAtlas_Mesenchymal_Stem_Cell_Cultured</option>
							<option value="MouseCellAtlas_Muscle">MouseCellAtlas_Muscle</option>
							<option value="MouseCellAtlas_Neonatal_Calvaria">MouseCellAtlas_Neonatal_Calvaria</option>
							<option value="MouseCellAtlas_Neonatal_Heart">MouseCellAtlas_Neonatal_Heart</option>
							<option value="MouseCellAtlas_Neonatal_Muscle">MouseCellAtlas_Neonatal_Muscle</option>
							<option value="MouseCellAtlas_Neonatal_Rib">MouseCellAtlas_Neonatal_Rib</option>
							<option value="MouseCellAtlas_Neonatal_Skin">MouseCellAtlas_Neonatal_Skin</option>
							<option value="MouseCellAtlas_Neonatal_all">MouseCellAtlas_Neonatal_all</option>
							<option value="MouseCellAtlas_Ovary">MouseCellAtlas_Ovary</option>
							<option value="MouseCellAtlas_Pancreas">MouseCellAtlas_Pancreas</option>
							<option value="MouseCellAtlas_Peripheral_Blood">MouseCellAtlas_Peripheral_Blood</option>
							<option value="MouseCellAtlas_Placenta">MouseCellAtlas_Placenta</option>
							<option value="MouseCellAtlas_Prostate">MouseCellAtlas_Prostate</option>
							<option value="MouseCellAtlas_Small_Intestine">MouseCellAtlas_Small_Intestine</option>
							<option value="MouseCellAtlas_Spleen">MouseCellAtlas_Spleen</option>
							<option value="MouseCellAtlas_Stomach">MouseCellAtlas_Stomach</option>
							<option value="MouseCellAtlas_Testis">MouseCellAtlas_Testis</option>
							<option value="MouseCellAtlas_Thymus">MouseCellAtlas_Thymus</option>
							<option value="MouseCellAtlas_Trophoblast_Stem_Cell">MouseCellAtlas_Trophoblast_Stem_Cell</option>
							<option value="MouseCellAtlas_Uterus">MouseCellAtlas_Uterus</option>
							<option value="PBMC_10x_68k">PBMC_10x_68k</option>
							<option value="TabulaMuris_FACS_all">TabulaMuris_FACS_all</option>
							<option value="TabulaMuris_FACS_Aorta">TabulaMuris_FACS_Aorta</option>
							<option value="TabulaMuris_FACS_Bladder">TabulaMuris_FACS_Bladder</option>
							<option value="TabulaMuris_FACS_Brain">TabulaMuris_FACS_Brain</option>
							<option value="TabulaMuris_FACS_Brain_Myeloid">TabulaMuris_FACS_Brain_Myeloid</option>
							<option value="TabulaMuris_FACS_Brain_Non-Myeloid">TabulaMuris_FACS_Brain_Non-Myeloid</option>
							<option value="TabulaMuris_FACS_Diaphragm">TabulaMuris_FACS_Diaphragm</option>
							<option value="TabulaMuris_FACS_Fat">TabulaMuris_FACS_Fat</option>
							<option value="TabulaMuris_FACS_Heart">TabulaMuris_FACS_Heart</option>
							<option value="TabulaMuris_FACS_Kidney">TabulaMuris_FACS_Kidney</option>
							<option value="TabulaMuris_FACS_Large_Intestine">TabulaMuris_FACS_Large_Intestine</option>
							<option value="TabulaMuris_FACS_Limb_Muscle">TabulaMuris_FACS_Limb_Muscle</option>
							<option value="TabulaMuris_FACS_Liver">TabulaMuris_FACS_Liver</option>
							<option value="TabulaMuris_FACS_Lung">TabulaMuris_FACS_Lung</option>
							<option value="TabulaMuris_FACS_Mammary_Gland">TabulaMuris_FACS_Mammary_Gland</option>
							<option value="TabulaMuris_FACS_Marrow">TabulaMuris_FACS_Marrow</option>
							<option value="TabulaMuris_FACS_Pancreas">TabulaMuris_FACS_Pancreas</option>
							<option value="TabulaMuris_FACS_Skin">TabulaMuris_FACS_Skin</option>
							<option value="TabulaMuris_FACS_Spleen">TabulaMuris_FACS_Spleen</option>
							<option value="TabulaMuris_FACS_Thymus">TabulaMuris_FACS_Thymus</option>
							<option value="TabulaMuris_FACS_Tongue">TabulaMuris_FACS_Tongue</option>
							<option value="TabulaMuris_FACS_Trachea">TabulaMuris_FACS_Trachea</option>
							<option value="TabulaMuris_droplet_all">TabulaMuris_droplet_all</option>
							<option value="TabulaMuris_droplet_Bladder">TabulaMuris_droplet_Bladder</option>
							<option value="TabulaMuris_droplet_Heart">TabulaMuris_droplet_Heart</option>
							<option value="TabulaMuris_droplet_Kidney">TabulaMuris_droplet_Kidney</option>
							<option value="TabulaMuris_droplet_Liver">TabulaMuris_droplet_Liver</option>
							<option value="TabulaMuris_droplet_Lung">TabulaMuris_droplet_Lung</option>
							<option value="TabulaMuris_droplet_Mammary">TabulaMuris_droplet_Mammary</option>
							<option value="TabulaMuris_droplet_Marrow">TabulaMuris_droplet_Marrow</option>
							<option value="TabulaMuris_droplet_Muscle">TabulaMuris_droplet_Muscle</option>
							<option value="TabulaMuris_droplet_Spleen">TabulaMuris_droplet_Spleen</option>
							<option value="TabulaMuris_droplet_Thymus">TabulaMuris_droplet_Thymus</option>
							<option value="TabulaMuris_droplet_Tongue">TabulaMuris_droplet_Tongue</option>
							<option value="TabulaMuris_droplet_Trachea">TabulaMuris_droplet_Trachea</option>
						</select>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-body" style="padding-bottom: 10;">
						<h4>Other options</h4>
						<div class="form-inline">
							Multiple test correction method:
							<select class="form-control" id="adjPmeth" name="adjPmeth" style="width:auto;">
								<option selected value="bonferroni">Bonferroni</option>
								<option value="BH">Benjamini-Hochberg (FDR)</option>
								<option value="BY">Benjamini-Yekutieli</option>
								<option value="holm">Holm</option>
								<option value="hochberg">Hochberg</option>
								<option value="hommel">Hommel</option>
							</select>
						</div>
						<br/>
						<input type="checkbox" id="step2" name="step2"> Perform step 2 (per dataset conditional analysis)
						if there is more then one significant cell type per dataset.
						<a class="infoPop" data-toggle="popover" data-content="Step 2 in the workflow is per dataset conditional analysis.
						When there are more than one significant cell types from the same dataset, FUMA will perform pair-wise conditional analyses for all possible pairs of
						significant cell types within the dataset. Based on this, forward selection will be performed to identify independent signals.
						See tutorial for details.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
						<br/>
						<input type="checkbox" id="step3" name="step3"> Perform step 3 (cross-datasets conditional analysis)
						if there is significant cell types from more than one dataset.
						<a class="infoPop" data-toggle="popover" data-content="Step 3 in the workflow is cross-datasets conditional analysis.
						When there are significant cell types from more than one dataset, FUMA will perform pair-wise conditional analyses for all possible pairs of
						significant cell types across datasets. See tutorial for details.">
							<i class="fa fa-question-circle-o fa-lg"></i>
						</a>
						<br/>
						<span class="info"><i class="fa fa-info"></i>
							Step 2 and 3 options are disabled when all scRNA datasets are selected.
						</span>
						<br/>
						<br/>
						<div class="form-inline">
							Title:
							<input type="text" class="form-control" id="title" name="title"/>
							<span class="info"><i class="fa fa-info"></i> Optional</span>
						</div>
					</div>
				</div>

				<br/>
				<div id="CheckInput"></div>
				<input type="submit" value="Submit" class="btn btn-default" id="cellSubmit" name="cellSubmit"/><br/><br/>
				{!! Form::close() !!}
			</div>
			@include('celltype.joblist')
			<div id="DIY" class="sidePanel container" style="padding-top:50px;">
				<h4>Do It Yourself</h4>
			</div>
			@include('celltype.result')
		</div>
	</div>
</div>
@stop
