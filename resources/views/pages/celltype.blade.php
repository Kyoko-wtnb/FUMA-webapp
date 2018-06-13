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
var page = "{{$page}}";
var prefix = "{{$prefix}}";
var subdir = "{{ Config::get('app.subdir') }}";
var loggedin = "{{ Auth::check() }}";
</script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}?131"></script>
<script type="text/javascript" src="{!! URL::asset('js/cell_results.js') !!}?131"></script>
<script type="text/javascript" src="{!! URL::asset('js/celltype.js') !!}?131"></script>
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
			<li class="active"><a href="#DIY">Do It Yourself<i class="sub_icon fa fa-wrench"></i></a></li>
			<div id="resultSide">
				<li><a href="#Summary">Summary<i class="sub_icon fa fa-table"></i></a></li>
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
							You can only select one of the succeeded SNP2GENE jobs in your acount.<br/>
							When you select a job ID, FUMA will automatically check if MAGMA was performed in the selected job.
						</span>
						<select class="form-control" id="s2gID" name="s2gID" onchange="CheckInput();">
						</select>
						<br/>
						2. Upload your own genes.raw file<br/>
						<span class="info"><i class="fa fa-info"></i>
							You can only upload a file with extention "genes.raw"
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
						Select single-cell expression data sets to perform MAGMA gene-property analysis
						<select multiple size="10" class="form-control" id="cellDataSets" name="cellDataSets[]" onchange="CheckInput();">
							<option value="Allen_Mouse_ALM_level1_log2RPKM">Allen_Mouse_ALM_level1</option>
							<option value="Allen_Mouse_ALM_level2_log2RPKM">Allen_Mouse_ALM_level2</option>
							<option value="Allen_Mouse_LGd_level1_log2RPKM">Allen_Mouse_LGd_level1</option>
							<option value="Allen_Mouse_LGd_level2_log2RPKM">Allen_Mouse_LGd_level2</option>
							<option value="Allen_Mouse_VIp_level1_log2RPKM">Allen_Mouse_VIp_level1</option>
							<option value="Allen_Mouse_VIp_level2_log2RPKM">Allen_Mouse_VIp_level2</option>
							<option value="DroNc_Human_Hippocampus_log2UMI">DroNc_Human_Hippocampus</option>
							<option value="DroNc_Mouse_Hippocampus_log2UMI">DroNc_Mouse_Hippocampus</option>
							<option value="DropViz_Cerebellum_level1_log2UMI">DropViz_Cerebellum_level1</option>
							<option value="DropViz_Cerebellum_level2_log2UMI">DropViz_Cerebellum_level2</option>
							<option value="DropViz_Endopedunclar_Nucleus_level1_log2UMI">DropViz_Endopedunclar_Nucleus_level1</option>
							<option value="DropViz_Endopedunclar_Nucleus_level2_log2UMI">DropViz_Endopedunclar_Nucleus_level2</option>
							<option value="DropViz_Frontal_Cortex_level1_log2UMI">DropViz_Frontal_Cortex_level1</option>
							<option value="DropViz_Frontal_Cortex_level2_log2UMI">DropViz_Frontal_Cortex_level2</option>
							<option value="DropViz_Globus_Pallidus_Extermus_level1_log2UMI">DropViz_Globus_Pallidus_Extermus_level1</option>
							<option value="DropViz_Globus_Pallidus_Extermus_level2_log2UMI">DropViz_Globus_Pallidus_Extermus_level2</option>
							<option value="DropViz_Hippocampus_level1_log2UMI">DropViz_Hippocampus_level1</option>
							<option value="DropViz_Hippocampus_level2_log2UMI">DropViz_Hippocampus_level2</option>
							<option value="DropViz_Posterior_Cortex_level1_log2UMI">DropViz_Posterior_Cortex_level1</option>
							<option value="DropViz_Posterior_Cortex_level2_log2UMI">DropViz_Posterior_Cortex_level2</option>
							<option value="DropViz_Striatum_level1_log2UMI">DropViz_Striatum_level1</option>
							<option value="DropViz_Striatum_level2_log2UMI">DropViz_Striatum_level2</option>
							<option value="DropViz_Substantia_Nigra_level1_log2UMI">DropViz_Substantia_Nigra_level1</option>
							<option value="DropViz_Substantia_Nigra_level2_log2UMI">DropViz_Substantia_Nigra_level2</option>
							<option value="DropViz_Thalamus_level1_log2UMI">DropViz_Thalamus_level1</option>
							<option value="DropViz_Thalamus_level2_log2UMI">DropViz_Thalamus_level2</option>
							<option value="DropViz_all_level1_log2UMI">DropViz_all_level1</option>
							<option value="GSE100597_Mouse_Embryo_log2CPM">GSE100597_Mouse_Embryo</option>
							<option value="GSE101601_Human_Temporal_cortex_log2UMI">GSE101601_Human_Temporal_cortex</option>
							<option value="GSE101601_Mouse_Somatosensory_cortex_log2UMI">GSE101601_Mouse_Somatosensory_cortex</option>
							<option value="GSE104276_Human_Prefrontal_cortex_all_ages_log2UMI">GSE104276_Human_Prefrontal_cortex_all_ages</option>
							<option value="GSE104276_Human_Prefrontal_cortex_per_ages_log2UMI">GSE104276_Human_Prefrontal_cortex_per_ages</option>
							<option value="GSE104323_Mouse_Dentate_gyrus_log2UMI">GSE104323_Mouse_Dentate_gyrus</option>
							<option value="GSE106678_Mouse_Cortex_log2UMI">GSE106678_Mouse_Cortex</option>
							<option value="GSE59739_Mouse_Dorsal_root_ganglion_level1_log2CPM">GSE59739_Mouse_Dorsal_root_ganglion_level1</option>
							<option value="GSE59739_Mouse_Dorsal_root_ganglion_level2_log2CPM">GSE59739_Mouse_Dorsal_root_ganglion_level2</option>
							<option value="GSE59739_Mouse_Dorsal_root_ganglion_level3_log2CPM">GSE59739_Mouse_Dorsal_root_ganglion_level3</option>
							<option value="GSE60361_Mouse_Cortex_Hippocampus_level1_log2UMI">GSE60361_Mouse_Cortex_Hippocampus_level1</option>
							<option value="GSE60361_Mouse_Cortex_Hippocampus_level2_log2UMI">GSE60361_Mouse_Cortex_Hippocampus_level2</option>
							<option value="GSE67602_Mouse_Skin_Epidermis_log2UMI">GSE67602_Mouse_Skin_Epidermis</option>
							<option value="GSE67835_Human_Cortex_log2CPM">GSE67835_Human_Cortex</option>
							<option value="GSE67835_Human_Cortex_woFetal_log2CPM">GSE67835_Human_Cortex_woFetal</option>
							<option value="GSE74672_Mouse_Hypothalamus_Neurons_level2_log2UMI">GSE74672_Mouse_Hypothalamus_Neurons_level2</option>
							<option value="GSE74672_Mouse_Hypothalamus_level1_log2UMI">GSE74672_Mouse_Hypothalamus_level1</option>
							<option value="GSE75330_Mouse_Oligodendrocytes_log2UMI">GSE75330_Mouse_Oligodendrocytes</option>
							<option value="GSE76381_Human_Midbrain_log2UMI">GSE76381_Human_Midbrain</option>
							<option value="GSE76381_Mouse_Midbrain_log2UMI">GSE76381_Mouse_Midbrain</option>
							<option value="GSE78845_Mouse_Ganglia_log2UMI">GSE78845_Mouse_Ganglia</option>
							<option value="GSE81547_Human_Pancreas_log2CPM">GSE81547_Human_Pancreas</option>
							<option value="GSE82187_microfluid_Mouse_Striatum_log10CPM">GSE82187_microfluid_Mouse_Striatum</option>
							<option value="GSE87544_Mouse_Hypothalamus_log2UMI">GSE87544_Mouse_Hypothalamus</option>
							<option value="GSE89164_Mouse_Hindbrain_log2UMI">GSE89164_Mouse_Hindbrain</option>
							<option value="GSE89232_Human_Blood_log2TPM">GSE89232_Human_Blood</option>
							<option value="GSE92332_Mouse_Epithelium_SMARTseq_log2TPM">GSE92332_Mouse_Epithelium_SMARTseq</option>
							<option value="GSE92332_Mouse_Epithelium_droplet_log2UMI">GSE92332_Mouse_Epithelium_droplet</option>
							<option value="GSE93374_Mouse_Arc_ME_level1_log2UMI">GSE93374_Mouse_Arc_ME_level1</option>
							<option value="GSE93374_Mouse_Arc_ME_level2_log2UMI">GSE93374_Mouse_Arc_ME_level2</option>
							<option value="GSE93374_Mouse_Arc_ME_neurons_log2UMI">GSE93374_Mouse_Arc_ME_neurons</option>
							<option value="GSE95315_Mouse_Dentate_gyrus_log2UMI">GSE95315_Mouse_Dentate_gyrus</option>
							<option value="GSE95752_Mouse_Dentate_gyrus_log2UMI">GSE95752_Mouse_Dentate_gyrus</option>
							<option value="GSE98816_Mouse_Brain_Vascular_log2CPM">GSE98816_Mouse_Brain_Vascular</option>
							<option value="GSE99235_Mouse_Lung_Vascular_log2CPM">GSE99235_Mouse_Lung_Vascular</option>
							<option value="MouseCellAtlas_Adult_all_log2UMI">MouseCellAtlas_Adult_all</option>
							<option value="MouseCellAtlas_Bladder_log2UMI">MouseCellAtlas_Bladder</option>
							<option value="MouseCellAtlas_Bone_Marrow_log2UMI">MouseCellAtlas_Bone_Marrow</option>
							<option value="MouseCellAtlas_Brain_log2UMI">MouseCellAtlas_Brain</option>
							<option value="MouseCellAtlas_Embryo_all_log2UMI">MouseCellAtlas_Embryo_all</option>
							<option value="MouseCellAtlas_Embryonic_Mesenchyme_log2UMI">MouseCellAtlas_Embryonic_Mesenchyme</option>
							<option value="MouseCellAtlas_Embryonic_Stem_Cell_log2UMI">MouseCellAtlas_Embryonic_Stem_Cell</option>
							<option value="MouseCellAtlas_Fetal_Brain_log2UMI">MouseCellAtlas_Fetal_Brain</option>
							<option value="MouseCellAtlas_Fetal_Intestine_log2UMI">MouseCellAtlas_Fetal_Intestine</option>
							<option value="MouseCellAtlas_Fetal_Liver_log2UMI">MouseCellAtlas_Fetal_Liver</option>
							<option value="MouseCellAtlas_Fetal_Lung_log2UMI">MouseCellAtlas_Fetal_Lung</option>
							<option value="MouseCellAtlas_Fetal_Stomache_log2UMI">MouseCellAtlas_Fetal_Stomache</option>
							<option value="MouseCellAtlas_Kidney_log2UMI">MouseCellAtlas_Kidney</option>
							<option value="MouseCellAtlas_Liver_log2UMI">MouseCellAtlas_Liver</option>
							<option value="MouseCellAtlas_Lung_log2UMI">MouseCellAtlas_Lung</option>
							<option value="MouseCellAtlas_Mammary_Gland_log2UMI">MouseCellAtlas_Mammary_Gland</option>
							<option value="MouseCellAtlas_Mesenchymal_Stem_Cell_Cultured_log2UMI">MouseCellAtlas_Mesenchymal_Stem_Cell_Cultured</option>
							<option value="MouseCellAtlas_Muscle_log2UMI">MouseCellAtlas_Muscle</option>
							<option value="MouseCellAtlas_Neonatal_Calvaria_log2UMI">MouseCellAtlas_Neonatal_Calvaria</option>
							<option value="MouseCellAtlas_Neonatal_Heart_log2UMI">MouseCellAtlas_Neonatal_Heart</option>
							<option value="MouseCellAtlas_Neonatal_Muscle_log2UMI">MouseCellAtlas_Neonatal_Muscle</option>
							<option value="MouseCellAtlas_Neonatal_Rib_log2UMI">MouseCellAtlas_Neonatal_Rib</option>
							<option value="MouseCellAtlas_Neonatal_Skin_log2UMI">MouseCellAtlas_Neonatal_Skin</option>
							<option value="MouseCellAtlas_Neonatal_all_log2UMI">MouseCellAtlas_Neonatal_all</option>
							<option value="MouseCellAtlas_Ovary_log2UMI">MouseCellAtlas_Ovary</option>
							<option value="MouseCellAtlas_Pancreas_log2UMI">MouseCellAtlas_Pancreas</option>
							<option value="MouseCellAtlas_Peripheral_Blood_log2UMI">MouseCellAtlas_Peripheral_Blood</option>
							<option value="MouseCellAtlas_Placenta_log2UMI">MouseCellAtlas_Placenta</option>
							<option value="MouseCellAtlas_Prostate_log2UMI">MouseCellAtlas_Prostate</option>
							<option value="MouseCellAtlas_Small_Intestine_log2UMI">MouseCellAtlas_Small_Intestine</option>
							<option value="MouseCellAtlas_Spleen_log2UMI">MouseCellAtlas_Spleen</option>
							<option value="MouseCellAtlas_Stomach_log2UMI">MouseCellAtlas_Stomach</option>
							<option value="MouseCellAtlas_Testis_log2UMI">MouseCellAtlas_Testis</option>
							<option value="MouseCellAtlas_Thymus_log2UMI">MouseCellAtlas_Thymus</option>
							<option value="MouseCellAtlas_Trophoblast_Stem_Cell_log2UMI">MouseCellAtlas_Trophoblast_Stem_Cell</option>
							<option value="MouseCellAtlas_Uterus_log2UMI">MouseCellAtlas_Uterus</option>
							<option value="MouseCellAtlas_all_log2UMI">MouseCellAtlas_all</option>
							<option value="TabulaMuris_FACS_Aorta_log2CPM">TabulaMuris_FACS_Aorta</option>
							<option value="TabulaMuris_FACS_Bladder_log2CPM">TabulaMuris_FACS_Bladder</option>
							<option value="TabulaMuris_FACS_Brain_Microglia_log2CPM">TabulaMuris_FACS_Brain_Microglia</option>
							<option value="TabulaMuris_FACS_Brain_Neurons_log2CPM">TabulaMuris_FACS_Brain_Neurons</option>
							<option value="TabulaMuris_FACS_Colon_log2CPM">TabulaMuris_FACS_Colon</option>
							<option value="TabulaMuris_FACS_Diaphragm_log2CPM">TabulaMuris_FACS_Diaphragm</option>
							<option value="TabulaMuris_FACS_Fat_log2CPM">TabulaMuris_FACS_Fat</option>
							<option value="TabulaMuris_FACS_Heart_log2CPM">TabulaMuris_FACS_Heart</option>
							<option value="TabulaMuris_FACS_Kidney_log2CPM">TabulaMuris_FACS_Kidney</option>
							<option value="TabulaMuris_FACS_Liver_log2CPM">TabulaMuris_FACS_Liver</option>
							<option value="TabulaMuris_FACS_Lung_log2CPM">TabulaMuris_FACS_Lung</option>
							<option value="TabulaMuris_FACS_Mammary_log2CPM">TabulaMuris_FACS_Mammary</option>
							<option value="TabulaMuris_FACS_Marrow_log2CPM">TabulaMuris_FACS_Marrow</option>
							<option value="TabulaMuris_FACS_Muscle_log2CPM">TabulaMuris_FACS_Muscle</option>
							<option value="TabulaMuris_FACS_Pancreas_log2CPM">TabulaMuris_FACS_Pancreas</option>
							<option value="TabulaMuris_FACS_Skin_log2CPM">TabulaMuris_FACS_Skin</option>
							<option value="TabulaMuris_FACS_Spleen_log2CPM">TabulaMuris_FACS_Spleen</option>
							<option value="TabulaMuris_FACS_Thymus_log2CPM">TabulaMuris_FACS_Thymus</option>
							<option value="TabulaMuris_FACS_Tongue_log2CPM">TabulaMuris_FACS_Tongue</option>
							<option value="TabulaMuris_FACS_Trachea_log2CPM">TabulaMuris_FACS_Trachea</option>
							<option value="TabulaMuris_FACS_all_log2CPM">TabulaMuris_FACS_all</option>
							<option value="TabulaMuris_droplet_Bladder_log2UMI">TabulaMuris_droplet_Bladder</option>
							<option value="TabulaMuris_droplet_Heart_log2UMI">TabulaMuris_droplet_Heart</option>
							<option value="TabulaMuris_droplet_Kidney_log2UMI">TabulaMuris_droplet_Kidney</option>
							<option value="TabulaMuris_droplet_Liver_log2UMI">TabulaMuris_droplet_Liver</option>
							<option value="TabulaMuris_droplet_Lung_log2UMI">TabulaMuris_droplet_Lung</option>
							<option value="TabulaMuris_droplet_Mammary_log2UMI">TabulaMuris_droplet_Mammary</option>
							<option value="TabulaMuris_droplet_Marrow_log2UMI">TabulaMuris_droplet_Marrow</option>
							<option value="TabulaMuris_droplet_Muscle_log2UMI">TabulaMuris_droplet_Muscle</option>
							<option value="TabulaMuris_droplet_Spleen_log2UMI">TabulaMuris_droplet_Spleen</option>
							<option value="TabulaMuris_droplet_Thymus_log2UMI">TabulaMuris_droplet_Thymus</option>
							<option value="TabulaMuris_droplet_Tongue_log2UMI">TabulaMuris_droplet_Tongue</option>
							<option value="TabulaMuris_droplet_Trachea_log2UMI">TabulaMuris_droplet_Trachea</option>
							<option value="TabulaMuris_droplet_all_log2UMI">TabulaMuris_droplet_all</option>
						</select>
					</div>
				</div>
				<div class="form-inline">
					Title:
					<input type="text" class="form-control" id="title" name="title"/>
					<span class="info"><i class="fa fa-info"></i> Optional</span>
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
	        @include('celltype.summary')
			@include('celltype.result')
		</div>
	</div>
</div>
@stop
