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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tree-multiselect@2.6.3/dist/jquery.tree-multiselect.min.js"></script>
<link href="{{ asset('/css/tree_multiselect.css') }}" rel="stylesheet">

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
<script type="text/javascript" src="{!! URL::asset('js/cell_results.js') !!}?135"></script>
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
						</span> <br>

						<div>
						<select multiple="multiple" class="form-control" style="display: none;" id="cellDataSets" name="cellDataSets[]" onchange="CheckInput();">
							<option value="TabulaMuris_FACS_Aorta" data-section="Aorta/Mouse" data-key="0">TabulaMuris_FACS_Aorta</option>
							<option value="MouseCellAtlas_Bladder" data-section="Bladder/Mouse" data-key="0">MouseCellAtlas_Bladder</option>
							<option value="TabulaMuris_FACS_Bladder" data-section="Bladder/Mouse" data-key="1">TabulaMuris_FACS_Bladder</option>
							<option value="TabulaMuris_droplet_Bladder" data-section="Bladder/Mouse" data-key="2">TabulaMuris_droplet_Bladder</option>
							<option value="GSE89232_Human_Blood" data-section="Blood/Human" data-key="0">GSE89232_Human_Blood</option>
							<option value="MouseCellAtlas_Peripheral_Blood" data-section="Blood/Mouse" data-key="0">MouseCellAtlas_Peripheral_Blood</option>
							<option value="MouseCellAtlas_Bone_Marrow" data-section="Bone Marrow/Mouse" data-key="0">MouseCellAtlas_Bone_Marrow</option>
							<option value="TabulaMuris_FACS_Marrow" data-section="Bone Marrow/Mouse" data-key="1">TabulaMuris_FACS_Marrow</option>
							<option value="TabulaMuris_droplet_Marrow" data-section="Bone Marrow/Mouse" data-key="2">TabulaMuris_droplet_Marrow</option>
							<option value="Allen_Human_LGN_level1" data-section="Brain/Human" data-key="0">Allen_Human_LGN_level1</option>
							<option value="Allen_Human_LGN_level2" data-section="Brain/Human" data-key="1">Allen_Human_LGN_level2</option>
							<option value="Allen_Human_MTG_level1" data-section="Brain/Human" data-key="2">Allen_Human_MTG_level1</option>
							<option value="Allen_Human_MTG_level2" data-section="Brain/Human" data-key="3">Allen_Human_MTG_level2</option>
							<option value="DroNc_Human_Hippocampus" data-section="Brain/Human" data-key="4">DroNc_Human_Hippocampus</option>
							<option value="GSE104276_Human_Prefrontal_cortex_all_ages" data-section="Brain/Human" data-key="5">GSE104276_Human_Prefrontal_cortex_all_ages</option>
							<option value="GSE104276_Human_Prefrontal_cortex_per_ages" data-section="Brain/Human" data-key="6">GSE104276_Human_Prefrontal_cortex_per_ages</option>
							<option value="GSE67835_Human_Cortex" data-section="Brain/Human" data-key="7">GSE67835_Human_Cortex</option>
							<option value="GSE67835_Human_Cortex_woFetal" data-section="Brain/Human" data-key="8">GSE67835_Human_Cortex_woFetal</option>
							<option value="Linnarsson_GSE101601_Human_Temporal_cortex" data-section="Brain/Human" data-key="9">Linnarsson_GSE101601_Human_Temporal_cortex</option>
							<option value="Linnarsson_GSE76381_Human_Midbrain" data-section="Brain/Human" data-key="10">Linnarsson_GSE76381_Human_Midbrain</option>
							<option value="PsychENCODE_Developmental" data-section="Brain/Human" data-key="11">PsychENCODE_Developmental</option>
							<option value="PsychENCODE_Adult" data-section="Brain/Human" data-key="12">PsychENCODE_Adult</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level1_Fetal" data-section="Brain/Human" data-key="13">GSE168408_Human_Prefrontal_Cortex_level1_Fetal</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level1_Neonatal" data-section="Brain/Human" data-key="14">GSE168408_Human_Prefrontal_Cortex_level1_Neonatal</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level1_Infancy" data-section="Brain/Human" data-key="15">GSE168408_Human_Prefrontal_Cortex_level1_Infancy</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level1_Childhood" data-section="Brain/Human" data-key="16">GSE168408_Human_Prefrontal_Cortex_level1_Childhood</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level1_Adolescence" data-section="Brain/Human" data-key="17">GSE168408_Human_Prefrontal_Cortex_level1_Adolescence</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level1_Adult" data-section="Brain/Human" data-key="18">GSE168408_Human_Prefrontal_Cortex_level1_Adult</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level2_Fetal" data-section="Brain/Human" data-key="19">GSE168408_Human_Prefrontal_Cortex_level2_Fetal</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level2_Neonatal" data-section="Brain/Human" data-key="20">GSE168408_Human_Prefrontal_Cortex_level2_Neonatal</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level2_Infancy" data-section="Brain/Human" data-key="21">GSE168408_Human_Prefrontal_Cortex_level2_Infancy</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level2_Childhood" data-section="Brain/Human" data-key="22">GSE168408_Human_Prefrontal_Cortex_level2_Childhood</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level2_Adolescence" data-section="Brain/Human" data-key="23">GSE168408_Human_Prefrontal_Cortex_level2_Adolescence</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level2_Adult" data-section="Brain/Human" data-key="24">GSE168408_Human_Prefrontal_Cortex_level2_Adult</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level3_Fetal" data-section="Brain/Human" data-key="25">GSE168408_Human_Prefrontal_Cortex_level3_Fetal</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level3_Neonatal" data-section="Brain/Human" data-key="26">GSE168408_Human_Prefrontal_Cortex_level3_Neonatal</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level3_Infancy" data-section="Brain/Human" data-key="27">GSE168408_Human_Prefrontal_Cortex_level3_Infancy</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level3_Childhood" data-section="Brain/Human" data-key="28">GSE168408_Human_Prefrontal_Cortex_level3_Childhood</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level3_Adolescence" data-section="Brain/Human" data-key="29">GSE168408_Human_Prefrontal_Cortex_level3_Adolescence</option>
							<option value="GSE168408_Human_Prefrontal_Cortex_level3_Adult" data-section="Brain/Human" data-key="30">GSE168408_Human_Prefrontal_Cortex_level3_Adult</option>
							<option value="Allen_Mouse_ALM2_level1" data-section="Brain/Mouse" data-key="0">Allen_Mouse_ALM2_level1</option>
							<option value="Allen_Mouse_ALM2_level2" data-section="Brain/Mouse" data-key="1">Allen_Mouse_ALM2_level2</option>
							<option value="Allen_Mouse_ALM2_level3" data-section="Brain/Mouse" data-key="2">Allen_Mouse_ALM2_level3</option>
							<option value="Allen_Mouse_LGd2_level1" data-section="Brain/Mouse" data-key="3">Allen_Mouse_LGd2_level1</option>
							<option value="Allen_Mouse_LGd2_level2" data-section="Brain/Mouse" data-key="4">Allen_Mouse_LGd2_level2</option>
							<option value="Allen_Mouse_LGd2_level3" data-section="Brain/Mouse" data-key="5">Allen_Mouse_LGd2_level3</option>
							<option value="Allen_Mouse_VISp2_level1" data-section="Brain/Mouse" data-key="6">Allen_Mouse_VISp2_level1</option>
							<option value="Allen_Mouse_VISp2_level2" data-section="Brain/Mouse" data-key="7">Allen_Mouse_VISp2_level2</option>
							<option value="Allen_Mouse_VISp2_level3" data-section="Brain/Mouse" data-key="8">Allen_Mouse_VISp2_level3</option>
							<option value="Allen_Mouse_ALM_level1" data-section="Brain/Mouse" data-key="9">Allen_Mouse_ALM_level1</option>
							<option value="Allen_Mouse_ALM_level2" data-section="Brain/Mouse" data-key="10">Allen_Mouse_ALM_level2</option>
							<option value="Allen_Mouse_LGd_level1" data-section="Brain/Mouse" data-key="11">Allen_Mouse_LGd_level1</option>
							<option value="Allen_Mouse_LGd_level2" data-section="Brain/Mouse" data-key="12">Allen_Mouse_LGd_level2</option>
							<option value="Allen_Mouse_VISp_level1" data-section="Brain/Mouse" data-key="13">Allen_Mouse_VISp_level1</option>
							<option value="Allen_Mouse_VISp_level2" data-section="Brain/Mouse" data-key="14">Allen_Mouse_VISp_level2</option>
							<option value="DroNc_Mouse_Hippocampus" data-section="Brain/Mouse" data-key="15">DroNc_Mouse_Hippocampus</option>
							<option value="DropViz_all_level1" data-section="Brain/Mouse" data-key="16">DropViz_all_level1</option>
							<option value="DropViz_all_level2" data-section="Brain/Mouse" data-key="17">DropViz_all_level2</option>
							<option value="DropViz_CB_level1" data-section="Brain/Mouse" data-key="18">DropViz_CB_level1</option>
							<option value="DropViz_CB_level2" data-section="Brain/Mouse" data-key="19">DropViz_CB_level2</option>
							<option value="DropViz_ENT_level1" data-section="Brain/Mouse" data-key="20">DropViz_ENT_level1</option>
							<option value="DropViz_ENT_level2" data-section="Brain/Mouse" data-key="21">DropViz_ENT_level2</option>
							<option value="DropViz_FC_level1" data-section="Brain/Mouse" data-key="22">DropViz_FC_level1</option>
							<option value="DropViz_FC_level2" data-section="Brain/Mouse" data-key="23">DropViz_FC_level2</option>
							<option value="DropViz_GP_level1" data-section="Brain/Mouse" data-key="24">DropViz_GP_level1</option>
							<option value="DropViz_GP_level2" data-section="Brain/Mouse" data-key="25">DropViz_GP_level2</option>
							<option value="DropViz_HC_level1" data-section="Brain/Mouse" data-key="26">DropViz_HC_level1</option>
							<option value="DropViz_HC_level2" data-section="Brain/Mouse" data-key="27">DropViz_HC_level2</option>
							<option value="DropViz_PC_level1" data-section="Brain/Mouse" data-key="28">DropViz_PC_level1</option>
							<option value="DropViz_PC_level2" data-section="Brain/Mouse" data-key="29">DropViz_PC_level2</option>
							<option value="DropViz_SN_level1" data-section="Brain/Mouse" data-key="30">DropViz_SN_level1</option>
							<option value="DropViz_SN_level2" data-section="Brain/Mouse" data-key="31">DropViz_SN_level2</option>
							<option value="DropViz_STR_level1" data-section="Brain/Mouse" data-key="32">DropViz_STR_level1</option>
							<option value="DropViz_STR_level2" data-section="Brain/Mouse" data-key="33">DropViz_STR_level2</option>
							<option value="DropViz_TH_level1" data-section="Brain/Mouse" data-key="34">DropViz_TH_level1</option>
							<option value="DropViz_TH_level2" data-section="Brain/Mouse" data-key="35">DropViz_TH_level2</option>
							<option value="GSE106678_Mouse_Cortex" data-section="Brain/Mouse" data-key="36">GSE106678_Mouse_Cortex</option>
							<option value="GSE82187_Mouse_Striatum" data-section="Brain/Mouse" data-key="37">GSE82187_Mouse_Striatum</option>
							<option value="GSE87544_Mouse_Hypothalamus" data-section="Brain/Mouse" data-key="38">GSE87544_Mouse_Hypothalamus</option>
							<option value="GSE89164_Mouse_Hindbrain" data-section="Brain/Mouse" data-key="39">GSE89164_Mouse_Hindbrain</option>
							<option value="GSE93374_Mouse_Arc_ME_level1" data-section="Brain/Mouse" data-key="40">GSE93374_Mouse_Arc_ME_level1</option>
							<option value="GSE93374_Mouse_Arc_ME_level2" data-section="Brain/Mouse" data-key="41">GSE93374_Mouse_Arc_ME_level2</option>
							<option value="GSE93374_Mouse_Arc_ME_neurons" data-section="Brain/Mouse" data-key="42">GSE93374_Mouse_Arc_ME_neurons</option>
							<option value="GSE98816_Mouse_Brain_Vascular" data-section="Brain/Mouse" data-key="43">GSE98816_Mouse_Brain_Vascular</option>
							<option value="Linnarsson_GSE101601_Mouse_Somatosensory_cortex" data-section="Brain/Mouse" data-key="44">Linnarsson_GSE101601_Mouse_Somatosensory_cortex</option>
							<option value="Linnarsson_GSE103840_Mouse_Dorsal_horn" data-section="Brain/Mouse" data-key="45">Linnarsson_GSE103840_Mouse_Dorsal_horn</option>
							<option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level1" data-section="Brain/Mouse" data-key="46">Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level1</option>
							<option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level2" data-section="Brain/Mouse" data-key="47">Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level2</option>
							<option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level3" data-section="Brain/Mouse" data-key="48">Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level3</option>
							<option value="Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level1" data-section="Brain/Mouse" data-key="49">Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level1</option>
							<option value="Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level2" data-section="Brain/Mouse" data-key="50">Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level2</option>
							<option value="Linnarsson_GSE74672_Mouse_Hypothalamus_Neurons_level2" data-section="Brain/Mouse" data-key="51">Linnarsson_GSE74672_Mouse_Hypothalamus_Neurons_level2</option>
							<option value="Linnarsson_GSE74672_Mouse_Hypothalamus_level1" data-section="Brain/Mouse" data-key="52">Linnarsson_GSE74672_Mouse_Hypothalamus_level1</option>
							<option value="Linnarsson_GSE75330_Mouse_Oligodendrocytes" data-section="Brain/Mouse" data-key="53">Linnarsson_GSE75330_Mouse_Oligodendrocytes</option>
							<option value="Linnarsson_GSE76381_Mouse_Midbrain" data-section="Brain/Mouse" data-key="54">Linnarsson_GSE76381_Mouse_Midbrain</option>
							<option value="Linnarsson_GSE78845_Mouse_Ganglia" data-section="Brain/Mouse" data-key="55">Linnarsson_GSE78845_Mouse_Ganglia</option>
							<option value="Linnarsson_GSE95752_Mouse_Dentate_gyrus" data-section="Brain/Mouse" data-key="56">Linnarsson_GSE95752_Mouse_Dentate_gyrus</option>
							<option value="Linnarsson_GSE95315_Mouse_Dentate_gyrus" data-section="Brain/Mouse" data-key="57">Linnarsson_GSE95315_Mouse_Dentate_gyrus</option>
							<option value="Linnarsson_GSE104323_Mouse_Dentate_gyrus" data-section="Brain/Mouse" data-key="58">Linnarsson_GSE104323_Mouse_Dentate_gyrus</option>
							<option value="MouseCellAtlas_Brain" data-section="Brain/Mouse" data-key="59">MouseCellAtlas_Brain</option>
							<option value="MouseCellAtlas_Fetal_Brain" data-section="Brain/Mouse" data-key="60">MouseCellAtlas_Fetal_Brain</option>
							<option value="MouseCellAtlas_Neonatal_Calvaria" data-section="Brain/Mouse" data-key="61">MouseCellAtlas_Neonatal_Calvaria</option>
							<option value="TabulaMuris_FACS_Brain" data-section="Brain/Mouse" data-key="62">TabulaMuris_FACS_Brain</option>
							<option value="TabulaMuris_FACS_Brain_Myeloid" data-section="Brain/Mouse" data-key="63">TabulaMuris_FACS_Brain_Myeloid</option>
							<option value="TabulaMuris_FACS_Brain_Non-Myeloid" data-section="Brain/Mouse" data-key="64">TabulaMuris_FACS_Brain_Non-Myeloid</option>
							<option value="GSE106707_Mouse_Striatum_Cortex" data-section="Brain/Mouse" data-key="65">GSE106707_Mouse_Striatum_Cortex</option>
							<option value="GSE97478_Mouse_Striatum_Cortex" data-section="Brain/Mouse" data-key="66">GSE97478_Mouse_Striatum_Cortex</option>
							<option value="Linnarsson_MouseBrainAtlas_level5" data-section="Brain/Mouse" data-key="67">Linnarsson_MouseBrainAtlas_level5</option>
							<option value="Linnarsson_MouseBrainAtlas_level6_rank1" data-section="Brain/Mouse" data-key="68">Linnarsson_MouseBrainAtlas_level6_rank1</option>
							<option value="Linnarsson_MouseBrainAtlas_level6_rank2" data-section="Brain/Mouse" data-key="69">Linnarsson_MouseBrainAtlas_level6_rank2</option>
							<option value="Linnarsson_MouseBrainAtlas_level6_rank3" data-section="Brain/Mouse" data-key="70">Linnarsson_MouseBrainAtlas_level6_rank3</option>
							<option value="Linnarsson_MouseBrainAtlas_level6_rank4" data-section="Brain/Mouse" data-key="71">Linnarsson_MouseBrainAtlas_level6_rank4</option>
							<option value="MouseCellAtlas_Mammary_Gland" data-section="Breast/Mouse" data-key="0">MouseCellAtlas_Mammary_Gland</option>
							<option value="TabulaMuris_FACS_Mammary_Gland" data-section="Breast/Mouse" data-key="1">TabulaMuris_FACS_Mammary_Gland</option>
							<option value="TabulaMuris_droplet_Mammary" data-section="Breast/Mouse" data-key="2">TabulaMuris_droplet_Mammary</option>
							<option value="GSE100597_Mouse_Embryo" data-section="Embryo/Mouse" data-key="0">GSE100597_Mouse_Embryo</option>
							<option value="MouseCellAtlas_Embryo_all" data-section="Embryo/Mouse" data-key="1">MouseCellAtlas_Embryo_all</option>
							<option value="GSE92332_Mouse_Epithelium_SMARTseq" data-section="Epithelial/Mouse" data-key="0">GSE92332_Mouse_Epithelium_SMARTseq</option>
							<option value="GSE92332_Mouse_Epithelium_droplet" data-section="Epithelial/Mouse" data-key="1">GSE92332_Mouse_Epithelium_droplet</option>
							<option value="TabulaMuris_FACS_Diaphragm" data-section="Diaphram/Mouse" data-key="0">TabulaMuris_FACS_Diaphragm</option>
							<option value="TabulaMuris_FACS_Fat" data-section="Fat/Mouse" data-key="0">TabulaMuris_FACS_Fat</option>
							<option value="MouseCellAtlas_Neonatal_Heart" data-section="Heart/Mouse" data-key="0">MouseCellAtlas_Neonatal_Heart</option>
							<option value="TabulaMuris_FACS_Heart" data-section="Heart/Mouse" data-key="1">TabulaMuris_FACS_Heart</option>
							<option value="TabulaMuris_droplet_Heart" data-section="Heart/Mouse" data-key="2">TabulaMuris_droplet_Heart</option>
							<option value="MouseCellAtlas_Kidney" data-section="Kidney/Mouse" data-key="0">MouseCellAtlas_Kidney</option>
							<option value="TabulaMuris_FACS_Kidney" data-section="Kidney/Mouse" data-key="1">TabulaMuris_FACS_Kidney</option>
							<option value="TabulaMuris_droplet_Kidney" data-section="Kidney/Mouse" data-key="2">TabulaMuris_droplet_Kidney</option>
							<option value="TabulaMuris_FACS_Large_Intestine" data-section="Large Intestine/Mouse" data-key="0">TabulaMuris_FACS_Large_Intestine</option>
							<option value="MouseCellAtlas_Fetal_Liver" data-section="Liver/Mouse" data-key="0">MouseCellAtlas_Fetal_Liver</option>
							<option value="MouseCellAtlas_Liver" data-section="Liver/Mouse" data-key="1">MouseCellAtlas_Liver</option>
							<option value="TabulaMuris_FACS_Liver" data-section="Liver/Mouse" data-key="2">TabulaMuris_FACS_Liver</option>
							<option value="TabulaMuris_droplet_Liver" data-section="Liver/Mouse" data-key="3">TabulaMuris_droplet_Liver</option>
							<option value="GSE99235_Mouse_Lung_Vascular" data-section="Lung/Mouse" data-key="0">GSE99235_Mouse_Lung_Vascular</option>
							<option value="MouseCellAtlas_Fetal_Lung" data-section="Lung/Mouse" data-key="1">MouseCellAtlas_Fetal_Lung</option>
							<option value="MouseCellAtlas_Lung" data-section="Lung/Mouse" data-key="2">MouseCellAtlas_Lung</option>
							<option value="TabulaMuris_FACS_Lung" data-section="Lung/Mouse" data-key="3">TabulaMuris_FACS_Lung</option>
							<option value="TabulaMuris_droplet_Lung" data-section="Lung/Mouse" data-key="4">TabulaMuris_droplet_Lung</option>
							<option value="MouseCellAtlas_Muscle" data-section="Muscle/Mouse" data-key="0">MouseCellAtlas_Muscle</option>
							<option value="TabulaMuris_FACS_Limb_Muscle" data-section="Muscle/Mouse" data-key="1">TabulaMuris_FACS_Limb_Muscle</option>
							<option value="MouseCellAtlas_Neonatal_Muscle" data-section="Muscle/Mouse" data-key="2">MouseCellAtlas_Neonatal_Muscle</option>
							<option value="TabulaMuris_droplet_Muscle" data-section="Muscle/Mouse" data-key="3">TabulaMuris_droplet_Muscle</option>
							<option value="MouseCellAtlas_Ovary" data-section="Ovary/Mouse" data-key="0">MouseCellAtlas_Ovary</option>
							<option value="GSE81547_Human_Pancreas" data-section="Pancreas/Human" data-key="0">GSE81547_Human_Pancreas</option>
							<option value="GSE84133_Human_Pancreas" data-section="Pancreas/Human" data-key="1">GSE84133_Human_Pancreas</option>
							<option value="GSE84133_Mouse_Pancreas" data-section="Pancreas/Mouse" data-key="0">GSE84133_Mouse_Pancreas</option>
							<option value="MouseCellAtlas_Pancreas" data-section="Pancreas/Mouse" data-key="1">MouseCellAtlas_Pancreas</option>
							<option value="TabulaMuris_FACS_Pancreas" data-section="Pancreas/Mouse" data-key="2">TabulaMuris_FACS_Pancreas</option>
							<option value="MouseCellAtlas_Placenta" data-section="Placenta/Mouse" data-key="0">MouseCellAtlas_Placenta</option>
							<option value="MouseCellAtlas_Prostate" data-section="Prostate/Mouse" data-key="0">MouseCellAtlas_Prostate</option>
							<option value="MouseCellAtlas_Neonatal_Rib" data-section="Ribs/Mouse" data-key="0">MouseCellAtlas_Neonatal_Rib</option>
							<option value="Linnarsson_GSE67602_Mouse_Skin_Epidermis" data-section="Skin/Mouse" data-key="0">Linnarsson_GSE67602_Mouse_Skin_Epidermis</option>
							<option value="TabulaMuris_FACS_Skin" data-section="Skin/Mouse" data-key="1">TabulaMuris_FACS_Skin</option>
							<option value="MouseCellAtlas_Neonatal_Skin" data-section="Skin/Mouse" data-key="2">MouseCellAtlas_Neonatal_Skin</option>
							<option value="MouseCellAtlas_Fetal_Intestine" data-section="Intestine/Mouse" data-key="0">MouseCellAtlas_Fetal_Intestine</option>
							<option value="MouseCellAtlas_Small_Intestine" data-section="Small Intestine/Mouse" data-key="0">MouseCellAtlas_Small_Intestine</option>
							<option value="MouseCellAtlas_Spleen" data-section="Spleen/Mouse" data-key="0">MouseCellAtlas_Spleen</option>
							<option value="TabulaMuris_FACS_Spleen" data-section="Spleen/Mouse" data-key="1">TabulaMuris_FACS_Spleen</option>
							<option value="TabulaMuris_droplet_Spleen" data-section="Spleen/Mouse" data-key="2">TabulaMuris_droplet_Spleen</option>
							<option value="MouseCellAtlas_Mesenchymal_Stem_Cell_Cultured" data-section="Stem Cell/Mouse" data-key="0">MouseCellAtlas_Mesenchymal_Stem_Cell_Cultured</option>
							<option value="MouseCellAtlas_Trophoblast_Stem_Cell" data-section="Stem Cell/Mouse" data-key="1">MouseCellAtlas_Trophoblast_Stem_Cell</option>
							<option value="MouseCellAtlas_Embryonic_Mesenchyme" data-section="Stem Cell/Mouse" data-key="2">MouseCellAtlas_Embryonic_Mesenchyme</option>
							<option value="MouseCellAtlas_Embryonic_Stem_Cell" data-section="Stem Cell/Mouse" data-key="3">MouseCellAtlas_Embryonic_Stem_Cell</option>
							<option value="MouseCellAtlas_Fetal_Stomache" data-section="Stomach/Mouse" data-key="0">MouseCellAtlas_Fetal_Stomache</option>
							<option value="MouseCellAtlas_Stomach" data-section="Stomach/Mouse" data-key="1">MouseCellAtlas_Stomach</option>
							<option value="MouseCellAtlas_Testis" data-section="Testis/Mouse" data-key="0">MouseCellAtlas_Testis</option>
							<option value="MouseCellAtlas_Thymus" data-section="Thymus/Mouse" data-key="0">MouseCellAtlas_Thymus</option>
							<option value="TabulaMuris_FACS_Thymus" data-section="Thymus/Mouse" data-key="1">TabulaMuris_FACS_Thymus</option>
							<option value="TabulaMuris_droplet_Thymus" data-section="Thymus/Mouse" data-key="2">TabulaMuris_droplet_Thymus</option>
							<option value="TabulaMuris_FACS_Tongue" data-section="Tongue/Mouse" data-key="0">TabulaMuris_FACS_Tongue</option>
							<option value="TabulaMuris_droplet_Tongue" data-section="Tongue/Mouse" data-key="1">TabulaMuris_droplet_Tongue</option>
							<option value="TabulaMuris_FACS_Trachea" data-section="Trachea/Mouse" data-key="0">TabulaMuris_FACS_Trachea</option>
							<option value="TabulaMuris_droplet_Trachea" data-section="Trachea/Mouse" data-key="1">TabulaMuris_droplet_Trachea</option>
							<option value="MouseCellAtlas_Uterus" data-section="Uterus/Mouse" data-key="0">MouseCellAtlas_Uterus</option>
							<option value="MouseCellAtlas_all" data-section="Other/Mouse" data-key="0">MouseCellAtlas_all</option>
							<option value="MouseCellAtlas_Adult_all" data-section="Other/Mouse" data-key="1">MouseCellAtlas_Adult_all</option>
							<option value="MouseCellAtlas_Neonatal_all" data-section="Other/Mouse" data-key="2">MouseCellAtlas_Neonatal_all</option>
							<option value="PBMC_10x_68k" data-section="Other/Human" data-key="3">PBMC_10x_68k</option>
							<option value="TabulaMuris_FACS_all" data-section="Other/Mouse" data-key="4">TabulaMuris_FACS_all</option>
							<option value="TabulaMuris_droplet_all" data-section="Other/Mouse" data-key="5">TabulaMuris_droplet_all</option>
						</select>
						</div>

						<script>
						var params = { sortable: true };
						$("select#cellDataSets").treeMultiselect({searchable: true, searchParams: ['section', 'text'], hideSidePanel: true, startCollapsed: true});
						</script>
						
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
