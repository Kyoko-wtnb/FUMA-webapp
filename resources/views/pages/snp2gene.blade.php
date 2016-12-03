@extends('layouts.master')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.0/js/bootstrap-select.min.js"></script>
<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="//d3js.org/d3.v3.min.js"></script>
<script src="//labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}"/>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
  });
  var status = "{{$status}}";
  var id = "{{$jobID}}";
  console.log(id);
  var jobid = id;
  // console.log(jobID);
</script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/InputParameters.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/snp2geneResults.js') !!}"></script>

@section('content')
<div id="wrapper" class="active">
  <div id="sidebar-wrapper">
    <ul class="sidebar-nav" id="sidebar-menu">
      <li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
    </ul>
    <ul class="sidebar-nav" id="sidebar">
      <li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
      <li><a href="#queryJob">Query Existing Job<i class="sub_icon fa fa-search"></i></a></li>
      <div id="jobinfoSide">
        <li><a href="#jobInfo">Job Info<i class="sub_icon fa fa-info-circle"></i></a></li>
      </div>
      <div id="resultsSide">
        <li><a href="#genomePlots">Genome-wide plots<i class="sub_icon fa fa-bar-chart"></i></a></li>
        <li><a href="#summaryTable">Summary of results<i class="sub_icon fa fa-bar-chart"></i></a></li>
        <li><a href="#tables">Tables<i class="sub_icon fa fa-table"></i></a></li>
        <li><a href="#downloads">Downloads<i class="sub_icon fa fa-download"></i></a></li>
        <li><a href="#annotPlot">Regional plot<i class="sub_icon fa fa-bar-chart"></i></a></li>
      </div>
    </ul>
  </div>

  <div id="page-content-wrapper">
    <div class="page-content inset">
      <div id="queryJob" class="sidePanel container" style="padding-top:50px;">
        {!! Form::open(array('url' => 'snp2gene/queryJob')) !!}
        <!-- Query existing job -->
        <h3>Query existing job</h3>
        <div class="panel panel-default">
          <div class="panel-heading">
            <div class="panel-title">Email address and Job title</div>
          </div>
          <div class="panel-body">
            E-mail address: <input type="text" name="JobQueryEmail" id="JobQueryEmail" onkeyup="JobQueryCheck();" onpaste="JobQueryCheck();"  oninput="JobQueryCheck();"/><br/>
            <div id="existing_jobs"></div>
            Job title: <input type="text" name="JobQueryTitle" id="JobQueryTitle" onkeyup="JobQueryCheck();" onpaste="JobQueryCheck();"  oninput="JobQueryCheck();"/><br/>
            <div id="JobQueryChecked"></div>
          </div>
        </div>
        <input class="btn" type="submit" value="Go to Job" name="go2job" id="go2job"/>
        {!! Form::close() !!}
      </div>

      <div id="newJob" class="sidePanel container" style="padding-top:50px;">
        {!! Form::open(array('url' => 'snp2gene/newJob', 'files' => true, 'novalidate'=>'novalidate')) !!}
        <!-- New -->
        <h3>New job submission</h3>
        <!-- Input files upload -->
        <h4>1. Upload input files</h4>
        <table class="table table-bordered inputTable" id="NewJobFiles" style="width: auto;">
          <tr>
            <td>GWAS summary statistics</td>
            <td><input type="file" class="form-control-file" name="GWASsummary" id="GWASsummary" onchange="CheckAll()"/></td>
            <td></td>
          </tr>
          <tr>
            <td>GWAS file format</td>
            <td>
              <select class="form-control" name="gwasformat" id="gwasformat" onchange="CheckAll()">
                <option value="PLINK" selected>PLINK</option>
                <option value="SNPTEST">SNPTEST</option>
                <option value="GCTA">GCTA</option>
                <option value="METAL">METAL</option>
                <option value="Plain">Plain text</option>
              </select>
            </td>
            <td></td>
          </tr>
          <tr>
            <td>Predefined lead SNPs</td>
            <td><input type="file" class="form-control-file" name="leadSNPs" id="leadSNPs" onchange="CheckAll()"/></td>
            <td></td>
          </tr>
          <tr>
            <td>Identify additional independent lead SNPs</td>
            <td><input type="checkbox" class="form-check-input" name="addleadSNPs" id="addleadSNPs" value="1" checked onchange="CheckAll()"></td>
            <td></td>
          </tr>
          <tr>
            <td>Predefined genetic region</td>
            <td><input type="file" class="form-control-file" name="regions" id="regions" onchange="CheckAll()"/></td>
            <td></td>
          </tr>
        </table>
        <br/>

        <!-- Parameters for lead SNPs and candidate SNPs -->
        <h4>2. Parameters for lead SNPs and candidate SNPs identification</h4>
        <table class="table table-bordered inputTable" id="NewJobParams" style="width: auto;">
          <tr>
            <td>Sample size (N)</td>
            <td><input type="number" class="form-control" id="N" name="N" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>P-value for lead SNPs (&le;)</td>
            <td><input type="number" class="form-control" id="leadP" name="leadP" value="5e-8" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
            <td></td>
          </tr>
          <tr>
            <td>Minimum r2 to be in LD (&ge;)</td>
            <td><input type="number" class="form-control" id="r2" name="r2" value="0.6" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>GWAS P-value cutoff (&le;)</td>
            <td><input type="number" class="form-control" id="gwasP" name="gwasP" value="0.05" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
            <td></td>
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
            <td>Include 1000 genome variant (non-GWAS tagged SNPs in LD)</td>
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
            <td>MAF cutoff (&ge;)</td>
            <td><input type="number" class="form-control" id="maf" name="maf" value="0.01" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
            <td></td>
          </tr>
          <tr>
            <td>Maximum distance between LD blocks to merge into intervals (&le; kb)</td>
            <td><span class="form-inline"><input type="number" class="form-control" id="mergeDist" name="mergeDist" value="250" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/> kb</span></td>
            <td></td>
          </tr>
        </table>
        <br/>

        <!-- Parameters for gene mapping -->
        <h4>3. Parameters for gene mapping</h4>
        <div class="panel panel-default"><div class="panel-body">
          <h4>Positional mapping</h4>
          <table class="table table-bordered inputTable" id="NewJobPosMap" style="width: auto;">
            <tr>
              <td>Perform positional mapping</td>
              <td><input type="checkbox" class="form-check-input" name="posMap" id="posMap" checked onchange="CheckAll();"></td>
              <td></td>
            </tr>
            <div id="posMapOptions">
              <tr class="posMapOptions">
                <td>Distance based mapping</td>
                <td><input type="checkbox" class="form-check-input" name="windowCheck" id="windowCheck" checked onchange="CheckAll();"></td>
                <td></td>
              </tr>
              <tr class="posMapOptions">
                <td>Maximum distance to genes (&le; kb)</td>
                <td><span class="form-inline"><input type="number" class="form-control" id="posMapWindow" name="posMapWindow" value="10" min="0" max="1000" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"> kb</span></td>
                <td></td>
              </tr>
              <tr class="posMapOptions">
                <td>Annotation based mapping</td>
                <td>
                  <span class="multiSelect">
                    <a>clear</a><br/>
                    <select multiple class="form-control" id="posMapAnnot" name="posMapAnnot[]" onchange="CheckAll();">
                      <option value="exonic">exonic</option>
                      <option value="splicing">splicing</option>
                      <option value="intronic">intronic</option>
                      <option value="3utr">3UTR</option>
                      <option value="5utr">5UTR</option>
                      <option value="upstream">upstream</option>
                      <option value="downstream">downstream</option>
                    </select>
                  </span>
                </td>
                <td></td>
              </tr class="posMapOptions">
            </div>
          </table>

          <div id="posMapOptFilt">
            Optional SNP filtering by functional annotation
            <table class="table table-bordered inputTable" id="posMapOptFiltTable" style="width: auto;">
              <tr>
                <td rowspan="2">CADD</td>
                <td>Perform SNPs filtering based on CADD score.</td>
                <td><input type="checkbox" class="form-check-input" name="posMapCADDcheck" id="posMapCADDcheck" onchange="CheckAll();"></td>
                <td></td>
              </tr>
              <tr>
                <td>Minimum CADD score (&ge;)</td>
                <td><input type="number" class="form-control" id="posMapCADDth" name="posMapCADDth" value="12.37" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
                <td></td>
              </tr>
              <tr>
                <td rowspan="2">RegulomeDB</td>
                <td>Perform SNPs filtering baed on ReguomeDB score</td>
                <td><input type="checkbox" class="form-check-input" name="posMapRDBcheck" id="posMapRDBcheck" onchange="CheckAll();"></td>
                <td></td>
              </tr>
              <tr>
                <td>Maximum RegulomeDB score (categorical)</td>
                <td>
                  <!-- <input type="text" class="form-control" id="posMapRDBth" name="posMapRDBth" value="7" style="width: 80px;"> -->
                  <select class="form-control" id="posMapRDBth" name="posMapRDBth" onchange="CheckAll();">
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
                <td>Perform SNPs filtering based on chromatin state</td>
                <td><input type="checkbox" class="form-check-input" name="posMapChr15check" id="posMapChr15check" onchange="CheckAll();"></td>
                <td></td>
              </tr>
              <tr>
                <td>Tissue/cell types for 15-core chromatin state</td>
                <td>
                  <span class="multiSelect">
                    Individual tissue/cell types:<tab><a>clear</a><br/>
                    <select multiple class="form-control" style="width: 250px; overflow:auto;" id="posMapChr15Ts" name="posMapChr15Ts[]" onchange="CheckAll();">
                      <option value="all">All</option>
                      <option value='E001'>E001 (ESC) ES-I3 Cells</option>
                      <option value='E002'>E002 (ESC) ES-WA7 Cells</option>
                      <option value='E003'>E003 (ESC) H1 Cells</option>
                      <option value='E004'>E004 (ESC Derived) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
                      <option value='E005'>E005 (ESC Derived) H1 BMP4 Derived Trophoblast Cultured Cells</option>
                      <option value='E006'>E006 (ESC Derived) H1 Derived Mesenchymal Stem Cells</option>
                      <option value='E007'>E007 (ESC Derived) H1 Derived Neuronal Progenitor Cultured Cells</option>
                      <option value='E008'>E008 (ESC) H9 Cells</option>
                      <option value='E009'>E009 (ESC Derived) H9 Derived Neuronal Progenitor Cultured Cells</option>
                      <option value='E010'>E010 (ESC Derived) H9 Derived Neuron Cultured Cells</option>
                      <option value='E011'>E011 (ESC Derived) hESC Derived CD184+ Endoderm Cultured Cells</option>
                      <option value='E012'>E012 (ESC Derived) hESC Derived CD56+ Ectoderm Cultured Cells</option>
                      <option value='E013'>E013 (ESC Derived) hESC Derived CD56+ Mesoderm Cultured Cells</option>
                      <option value='E014'>E014 (ESC) HUES48 Cells</option>
                      <option value='E015'>E015 (ESC) HUES6 Cells</option>
                      <option value='E016'>E016 (ESC) HUES64 Cells</option>
                      <option value='E017'>E017 (Lung) IMR90 fetal lung fibroblasts Cell Line</option>
                      <option value='E018'>E018 (iPSC) iPS-15b Cells</option>
                      <option value='E019'>E019 (iPSC) iPS-18 Cells</option>
                      <option value='E020'>E020 (iPSC) iPS-20b Cells</option>
                      <option value='E021'>E021 (iPSC) iPS DF 6.9 Cells</option>
                      <option value='E022'>E022 (iPSC) iPS DF 19.11 Cells</option>
                      <option value='E023'>E023 (Fat) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
                      <option value='E024'>E024 (ESC) ES-UCSF4  Cells</option>
                      <option value='E025'>E025 (Fat) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
                      <option value='E026'>E026 (Stromal Connective) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
                      <option value='E027'>E027 (Breast) Breast Myoepithelial Primary Cells</option>
                      <option value='E028'>E028 (Breast) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
                      <option value='E029'>E029 (Blood) Primary monocytes from peripheral blood</option>
                      <option value='E030'>E030 (Blood) Primary neutrophils from peripheral blood</option>
                      <option value='E031'>E031 (Blood) Primary B cells from cord blood</option>
                      <option value='E032'>E032 (Blood) Primary B cells from peripheral blood</option>
                      <option value='E033'>E033 (Blood) Primary T cells from cord blood</option>
                      <option value='E034'>E034 (Blood) Primary T cells from peripheral blood</option>
                      <option value='E035'>E035 (Blood) Primary hematopoietic stem cells</option>
                      <option value='E036'>E036 (Blood) Primary hematopoietic stem cells short term culture</option>
                      <option value='E037'>E037 (Blood) Primary T helper memory cells from peripheral blood 2</option>
                      <option value='E038'>E038 (Blood) Primary T helper naive cells from peripheral blood</option>
                      <option value='E039'>E039 (Blood) Primary T helper naive cells from peripheral blood</option>
                      <option value='E040'>E040 (Blood) Primary T helper memory cells from peripheral blood 1</option>
                      <option value='E041'>E041 (Blood) Primary T helper cells PMA-I stimulated</option>
                      <option value='E042'>E042 (Blood) Primary T helper 17 cells PMA-I stimulated</option>
                      <option value='E043'>E043 (Blood) Primary T helper cells from peripheral blood</option>
                      <option value='E044'>E044 (Blood) Primary T regulatory cells from peripheral blood</option>
                      <option value='E045'>E045 (Blood) Primary T cells effector/memory enriched from peripheral blood</option>
                      <option value='E046'>E046 (Blood) Primary Natural Killer cells from peripheral blood</option>
                      <option value='E047'>E047 (Blood) Primary T CD8+ naive cells from peripheral blood</option>
                      <option value='E048'>E048 (Blood) Primary T CD8+ memory cells from peripheral blood</option>
                      <option value='E049'>E049 (Stromal Connective) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
                      <option value='E050'>E050 (Blood) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
                      <option value='E051'>E051 (Blood) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
                      <option value='E052'>E052 (Muscle) Muscle Satellite Cultured Cells</option>
                      <option value='E053'>E053 (Brain) Cortex derived primary cultured neurospheres</option>
                      <option value='E054'>E054 (Brain) Ganglion Eminence derived primary cultured neurospheres</option>
                      <option value='E055'>E055 (Skin) Foreskin Fibroblast Primary Cells skin01</option>
                      <option value='E056'>E056 (Skin) Foreskin Fibroblast Primary Cells skin02</option>
                      <option value='E057'>E057 (Skin) Foreskin Keratinocyte Primary Cells skin02</option>
                      <option value='E058'>E058 (Skin) Foreskin Keratinocyte Primary Cells skin03</option>
                      <option value='E059'>E059 (Skin) Foreskin Melanocyte Primary Cells skin01</option>
                      <option value='E061'>E061 (Skin) Foreskin Melanocyte Primary Cells skin03</option>
                      <option value='E062'>E062 (Blood) Primary mononuclear cells from peripheral blood</option>
                      <option value='E063'>E063 (Fat) Adipose Nuclei</option>
                      <option value='E065'>E065 (Vascular) Aorta</option>
                      <option value='E066'>E066 (Liver) Liver</option>
                      <option value='E067'>E067 (Brain) Brain Angular Gyrus</option>
                      <option value='E068'>E068 (Brain) Brain Anterior Caudate</option>
                      <option value='E069'>E069 (Brain) Brain Cingulate Gyrus</option>
                      <option value='E070'>E070 (Brain) Brain Germinal Matrix</option>
                      <option value='E071'>E071 (Brain) Brain Hippocampus Middle</option>
                      <option value='E072'>E072 (Brain) Brain Inferior Temporal Lobe</option>
                      <option value='E073'>E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
                      <option value='E074'>E074 (Brain) Brain Substantia Nigra</option>
                      <option value='E075'>E075 (GI Colon) Colonic Mucosa</option>
                      <option value='E076'>E076 (GI Colon) Colon Smooth Muscle</option>
                      <option value='E077'>E077 (GI Duodenum) Duodenum Mucosa</option>
                      <option value='E078'>E078 (GI Duodenum) Duodenum Smooth Muscle</option>
                      <option value='E079'>E079 (GI Esophagus) Esophagus</option>
                      <option value='E080'>E080 (Adrenal) Fetal Adrenal Gland</option>
                      <option value='E081'>E081 (Brain) Fetal Brain Male</option>
                      <option value='E082'>E082 (Brain) Fetal Brain Female</option>
                      <option value='E083'>E083 (Heart) Fetal Heart</option>
                      <option value='E084'>E084 (GI Intestine) Fetal Intestine Large</option>
                      <option value='E085'>E085 (GI Intestine) Fetal Intestine Small</option>
                      <option value='E086'>E086 (Kidney) Fetal Kidney</option>
                      <option value='E087'>E087 (Pancreas) Pancreatic Islets</option>
                      <option value='E088'>E088 (Lung) Fetal Lung</option>
                      <option value='E089'>E089 (Muscle) Fetal Muscle Trunk</option>
                      <option value='E090'>E090 (Muscle) Fetal Muscle Leg</option>
                      <option value='E091'>E091 (Placenta) Placenta</option>
                      <option value='E092'>E092 (GI Stomach) Fetal Stomach</option>
                      <option value='E093'>E093 (Thymus) Fetal Thymus</option>
                      <option value='E094'>E094 (GI Stomach) Gastric</option>
                      <option value='E095'>E095 (Heart) Left Ventricle</option>
                      <option value='E096'>E096 (Lung) Lung</option>
                      <option value='E097'>E097 (Ovary) Ovary</option>
                      <option value='E098'>E098 (Pancreas) Pancreas</option>
                      <option value='E099'>E099 (Placenta) Placenta Amnion</option>
                      <option value='E100'>E100 (Muscle) Psoas Muscle</option>
                      <option value='E101'>E101 (GI Rectum) Rectal Mucosa Donor 29</option>
                      <option value='E102'>E102 (GI Rectum) Rectal Mucosa Donor 31</option>
                      <option value='E103'>E103 (GI Rectum) Rectal Smooth Muscle</option>
                      <option value='E104'>E104 (Heart) Right Atrium</option>
                      <option value='E105'>E105 (Heart) Right Ventricle</option>
                      <option value='E106'>E106 (GI Colon) Sigmoid Colon</option>
                      <option value='E107'>E107 (Muscle) Skeletal Muscle Male</option>
                      <option value='E108'>E108 (Muscle) Skeletal Muscle Female</option>
                      <option value='E109'>E109 (GI Intestine) Small Intestine</option>
                      <option value='E110'>E110 (GI Stomach) Stomach Mucosa</option>
                      <option value='E111'>E111 (GI Stomach) Stomach Smooth Muscle</option>
                      <option value='E112'>E112 (Thymus) Thymus</option>
                      <option value='E113'>E113 (Spleen) Spleen</option>
                      <option value='E114'>E114 (Lung) A549 EtOH 0.02pct Lung Carcinoma Cell Line</option>
                      <option value='E115'>E115 (Blood) Dnd41 TCell Leukemia Cell Line</option>
                      <option value='E116'>E116 (Blood) GM12878 Lymphoblastoid Cells</option>
                      <option value='E117'>E117 (Cervix) HeLa-S3 Cervical Carcinoma Cell Line</option>
                      <option value='E118'>E118 (Liver) HepG2 Hepatocellular Carcinoma Cell Line</option>
                      <option value='E119'>E119 (Breast) HMEC Mammary Epithelial Primary Cells</option>
                      <option value='E120'>E120 (Muscle) HSMM Skeletal Muscle Myoblasts Cells</option>
                      <option value='E121'>E121 (Muscle) HSMM cell derived Skeletal Muscle Myotubes Cells</option>
                      <option value='E122'>E122 (Vascular) HUVEC Umbilical Vein Endothelial Primary Cells</option>
                      <option value='E123'>E123 (Blood) K562 Leukemia Cells</option>
                      <option value='E124'>E124 (Blood) Monocytes-CD14+ RO01746 Primary Cells</option>
                      <option value='E125'>E125 (Brain) NH-A Astrocytes Primary Cells</option>
                      <option value='E126'>E126 (Skin) NHDF-Ad Adult Dermal Fibroblast Primary Cells</option>
                      <option value='E127'>E127 (Skin) NHEK-Epidermal Keratinocyte Primary Cells</option>
                      <option value='E128'>E128 (Lung) NHLF Lung Fibroblast Primary Cells</option>
                      <option value='E129'>E129 (Bone) Osteoblast Primary Cells</option>
                    </select>
                  </span>
                  <br/>
                  <span class="multiSelect">
                    General tissue/cell types:<tab><a>clear</a><br/>
                    <select multiple class="form-control" id="posMapChr15Gts" name="posMapChr15Gts[]" onchange="CheckAll();">
                      <option value="all">All</option>
                      <option value='E080'>Adrenal (1)</option>
                      <option value='E062:E034:E045:E033:E044:E043:E039:E041:E042:E040:E037:E048:E038:E047:E029:E031:E035:E051:E050:E036:E032:E046:E030:E115:E116:E123:E124'>Blood (27)</option>
                      <option value='E129'>Bone (1)</option>
                      <option value='E054:E053:E071:E074:E068:E069:E072:E067:E073:E070:E082:E081:E125'>Brain (13)</option>
                      <option value='E028:E027:E119'>Breast (3)</option>
                      <option value='E117'>Cervix (1)</option>
                      <option value='E002:E008:E001:E015:E014:E016:E003:E024'>ESC (8)</option>
                      <option value='E007:E009:E010:E013:E012:E011:E004:E005:E006'>ESC Derived (9)</option>
                      <option value='E025:E023:E063'>Fat (3)</option>
                      <option value='E076:E106:E075'>GI Colon (3)</option>
                      <option value='E078:E077'>GI Duodenum (2)</option>
                      <option value='E079'>GI Esophagus (1)</option>
                      <option value='E085:E084:E109'>GI Intestine (3)</option>
                      <option value='E103:E101:E102'>GI Rectum (3)</option>
                      <option value='E111:E092:E110:E094'>GI Stomach (4)</option>
                      <option value='E083:E104:E095:E105'>Heart (4)</option>
                      <option value='E020:E019:E018:E021:E022'>iPSC (5)</option>
                      <option value='E086'>Kidney (1)</option>
                      <option value='E066:E118'>Liver (2)</option>
                      <option value='E017:E088:E096:E114:E128'>Lung (5)</option>
                      <option value='E052:E100:E108:E107:E089:E120:E121:E090'>Muscle (8)</option>
                      <option value='E097'>Ovary (1)</option>
                      <option value='E087:E098'>Pancreas (2)</option>
                      <option value='E099:E091'>Placenta (2)</option>
                      <option value='E055:E056:E059:E061:E057:E058:E126:E127'>Skin (8)</option>
                      <option value='E113'>Spleen (1)</option>
                      <option value='E026:E049'>Stromal Connective (2)</option>
                      <option value='E112:E093'>Thymus (2)</option>
                      <option value='E065:E122'>Vascular (2)</option>
                    </select>
                  </span>
                </td>
                <td></td>
              </tr>
              <tr>
                <td>15-core chromatin state maximum state</td>
                <td><input type="number" class="form-control" id="posMapChr15Max" name="posMapChr15Max" value="7" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
                <td></td>
              </tr>
              <tr>
                <td>15-core chromatin state filtering method</td>
                <td>
                  <select  class="form-control" id="posMapChr15Meth" name="posMapChr15Meth" onchange="CheckAll();">
                    <option selected value="any">any</option>
                    <option value="majority">majority</option>
                    <option value="all">all</option>
                  </select>
                </td>
                <td></td>
              </tr>
            </table>
          </div>
        </div></div>

        <div class="panel panel-default"><div class="panel-body">
          <h4>eQTL mapping</h4>
          <table class="table table-bordered inputTable" id="NewJobEqtlMap" style="width: auto;">
            <tr>
              <td>Perform eQTL mapping</td>
              <td><input type="checkbox" calss="form-control" name="eqtlMap", id="eqtlMap" onchange="CheckAll();"></td>
              <td></td>
            </tr>
            <!-- <div id="eqtlMapOptions"> -->
              <tr class="eqtlMapOptions">
                <td>Tissue types</td>
                <td>
                  <span class="multiSelect">
                    Tissue types: <tab><a>clear</a><br/>
                    <select multiple class="form-control" id="eqtlMapTs" name="eqtlMapTs[]" onchange="CheckAll();">
                      <option value="all">All</option>
                      <option value='GTEx_Adipose_Subcutaneous'>GTEx Adipose Subcutaneous (Adipose Tissue)</option>
                      <option value='GTEx_Adipose_Visceral_Omentum'>GTEx Adipose Visceral Omentum (Adipose Tissue)</option>
                      <option value='GTEx_Adrenal_Gland'>GTEx Adrenal Gland (Adrenal Gland)</option>
                      <option value='GTEx_Artery_Aorta'>GTEx Artery Aorta (Blood Vessel)</option>
                      <option value='GTEx_Artery_Coronary'>GTEx Artery Coronary (Blood Vessel)</option>
                      <option value='GTEx_Artery_Tibial'>GTEx Artery Tibial (Blood Vessel)</option>
                      <option value='GTEx_Brain_Anterior_cingulate_cortex_BA24'>GTEx Brain Anterior cingulate cortex BA24 (Brain)</option>
                      <option value='GTEx_Brain_Caudate_basal_ganglia'>GTEx Brain Caudate basal ganglia (Brain)</option>
                      <option value='GTEx_Brain_Cerebellar_Hemisphere'>GTEx Brain Cerebellar Hemisphere (Brain)</option>
                      <option value='GTEx_Brain_Cerebellum'>GTEx Brain Cerebellum (Brain)</option>
                      <option value='GTEx_Brain_Cortex'>GTEx Brain Cortex (Brain)</option>
                      <option value='GTEx_Brain_Frontal_Cortex_BA9'>GTEx Brain Frontal Cortex BA9 (Brain)</option>
                      <option value='GTEx_Brain_Hippocampus'>GTEx Brain Hippocampus (Brain)</option>
                      <option value='GTEx_Brain_Hypothalamus'>GTEx Brain Hypothalamus (Brain)</option>
                      <option value='GTEx_Brain_Nucleus_accumbens_basal_ganglia'>GTEx Brain Nucleus accumbens basal ganglia (Brain)</option>
                      <option value='GTEx_Brain_Putamen_basal_ganglia'>GTEx Brain Putamen basal ganglia (Brain)</option>
                      <option value='GTEx_Breast_Mammary_Tissue'>GTEx Breast Mammary Tissue (Breast)</option>
                      <option value='GTEx_Cells_EBV-transformed_lymphocytes'>GTEx Cells EBV-transformed lymphocytes (Blood)</option>
                      <option value='GTEx_Cells_Transformed_fibroblasts'>GTEx Cells Transformed fibroblasts (Skin)</option>
                      <option value='GTEx_Colon_Sigmoid'>GTEx Colon Sigmoid (Colon)</option>
                      <option value='GTEx_Colon_Transverse'>GTEx Colon Transverse (Colon)</option>
                      <option value='GTEx_Esophagus_Gastroesophageal_Junction'>GTEx Esophagus Gastroesophageal Junction (Esophagus)</option>
                      <option value='GTEx_Esophagus_Mucosa'>GTEx Esophagus Mucosa (Esophagus)</option>
                      <option value='GTEx_Esophagus_Muscularis'>GTEx Esophagus Muscularis (Esophagus)</option>
                      <option value='GTEx_Heart_Atrial_Appendage'>GTEx Heart Atrial Appendage (Heart)</option>
                      <option value='GTEx_Heart_Left_Ventricle'>GTEx Heart Left Ventricle (Heart)</option>
                      <option value='GTEx_Liver'>GTEx Liver (Liver)</option>
                      <option value='GTEx_Lung'>GTEx Lung (Lung)</option>
                      <option value='GTEx_Muscle_Skeletal'>GTEx Muscle Skeletal (Muscle)</option>
                      <option value='GTEx_Nerve_Tibial'>GTEx Nerve Tibial (Nerve)</option>
                      <option value='GTEx_Ovary'>GTEx Ovary (Ovary)</option>
                      <option value='GTEx_Pancreas'>GTEx Pancreas (Pancreas)</option>
                      <option value='GTEx_Pituitary'>GTEx Pituitary (Pituitary)</option>
                      <option value='GTEx_Prostate'>GTEx Prostate (Prostate)</option>
                      <option value='GTEx_Skin_Not_Sun_Exposed_Suprapubic'>GTEx Skin Not Sun Exposed Suprapubic (Skin)</option>
                      <option value='GTEx_Skin_Sun_Exposed_Lower_leg'>GTEx Skin Sun Exposed Lower leg (Skin)</option>
                      <option value='GTEx_Small_Intestine_Terminal_Ileum'>GTEx Small Intestine Terminal Ileum (Small Intestine)</option>
                      <option value='GTEx_Spleen'>GTEx Spleen (Spleen)</option>
                      <option value='GTEx_Stomach'>GTEx Stomach (Stomach)</option>
                      <option value='GTEx_Testis'>GTEx Testis (Testis)</option>
                      <option value='GTEx_Thyroid'>GTEx Thyroid (Thyroid)</option>
                      <option value='GTEx_Uterus'>GTEx Uterus (Uterus)</option>
                      <option value='GTEx_Vagina'>GTEx Vagina (Vagina)</option>
                      <option value='GTEx_Whole_Blood'>GTEx Whole Blood (Blood)</option>
                      <option value='BloodeQTL_BloodeQTL'>Westra et al (2013). Blood (Blood)</option>
                      <option value='BIOSQTL_BIOS_eQTL_geneLevel'>BBMRI BIOS. Blood (Blood)</option>
                    </select>
                  </span>
                  <br/>
                  <span class="multiSelect">
                    General tissue types: <tab><a>clear</a><br/>
                    <select multiple class="form-control" id="eqtlMapGts" name="eqtlMapGts[]" onchange="CheckAll();">
                      <option value="all">All</option>
                      <option value='GTEx_Adipose_Subcutaneous:GTEx_Adipose_Visceral_Omentum'>GTEx Adipose Tissue (2)</option>
                      <option value='GTEx_Adrenal_Gland'>GTEx Adrenal Gland (1)</option>
                      <option value='GTEx_Cells_EBV-transformed_lymphocytes:GTEx_Whole_Blood'>GTEx Blood (2)</option>
                      <option value='GTEx_Artery_Aorta:GTEx_Artery_Coronary:GTEx_Artery_Tibial'>GTEx Blood Vessel (3)</option>
                      <option value='GTEx_Brain_Anterior_cingulate_cortex_BA24:GTEx_Brain_Caudate_basal_ganglia:GTEx_Brain_Cerebellar_Hemisphere:GTEx_Brain_Cerebellum:GTEx_Brain_Cortex:GTEx_Brain_Frontal_Cortex_BA9:GTEx_Brain_Hippocampus:GTEx_Brain_Hypothalamus:GTEx_Brain_Nucleus_accumbens_basal_ganglia:GTEx_Brain_Putamen_basal_ganglia'>GTEx Brain (10)</option>
                      <option value='GTEx_Breast_Mammary_Tissue'>GTEx Breast (1)</option>
                      <option value='GTEx_Colon_Sigmoid:GTEx_Colon_Transverse'>GTEx Colon (2)</option>
                      <option value='GTEx_Esophagus_Gastroesophageal_Junction:GTEx_Esophagus_Mucosa:GTEx_Esophagus_Muscularis'>GTEx Esophagus (3)</option>
                      <option value='GTEx_Heart_Atrial_Appendage:GTEx_Heart_Left_Ventricle'>GTEx Heart (2)</option>
                      <option value='GTEx_Liver'>GTEx Liver (1)</option>
                      <option value='GTEx_Lung'>GTEx Lung (1)</option>
                      <option value='GTEx_Muscle_Skeletal'>GTEx Muscle (1)</option>
                      <option value='GTEx_Nerve_Tibial'>GTEx Nerve (1)</option>
                      <option value='GTEx_Ovary'>GTEx Ovary (1)</option>
                      <option value='GTEx_Pancreas'>GTEx Pancreas (1)</option>
                      <option value='GTEx_Pituitary'>GTEx Pituitary (1)</option>
                      <option value='GTEx_Prostate'>GTEx Prostate (1)</option>
                      <option value='GTEx_Cells_Transformed_fibroblasts:GTEx_Skin_Not_Sun_Exposed_Suprapubic:GTEx_Skin_Sun_Exposed_Lower_leg'>GTEx Skin (3)</option>
                      <option value='GTEx_Small_Intestine_Terminal_Ileum'>GTEx Small Intestine (1)</option>
                      <option value='GTEx_Spleen'>GTEx Spleen (1)</option>
                      <option value='GTEx_Stomach'>GTEx Stomach (1)</option>
                      <option value='GTEx_Testis'>GTEx Testis (1)</option>
                      <option value='GTEx_Thyroid'>GTEx Thyroid (1)</option>
                      <option value='GTEx_Uterus'>GTEx Uterus (1)</option>
                      <option value='GTEx_Vagina'>GTEx Vagina (1)</option>
                    </select>
                  </span>
                </td>
                <td></td>
              </tr>
              <tr class="eqtlMapOptions">
                <td>eQTL P-value threshold</td>
                <td>
                  <span class="form-inline">Use only significant snp-gene pairs: <input type="checkbox" class="form-control" name="sigeqtlCheck" id="sigeqtlCheck" checked onchange="CheckAll();"> (FDR&le;0.05)</span><br/>
                  OR<br/>
                  <span class="form-inline">(nominal) P-value cutoff (&le;): <input type="number" class="form-control" name="eqtlP" id="eqtlP" value="1e-3" onchange="CheckAll();"></span>
                </td>
                <td></td>
              </tr>
            <!-- </div> -->
          </table>

          <div id="eqtlMapOptFilt">
            Optional SNP filtering by functional annotation
            <table class="table table-bordered inputTable" id="eqtlMapOptFiltTable">
              <tr>
                <td rowspan="2">CADD</td>
                <td>Perform SNPs filtering based on CADD score.</td>
                <td><input type="checkbox" class="form-check-input" name="eqtlMapCADDcheck" id="eqtlMapCADDcheck" onchange="CheckAll();"></td>
                <td></td>
              </tr>
              <tr>
                <td>Minimum CADD score (&ge;)</td>
                <td><input type="number" class="form-control" id="eqtlMapCADDth" name="eqtlMapCADDth" value="12.37" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
                <td></td>
              </tr>
              <tr>
                <td rowspan="2">RegulomeDB</td>
                <td>Perform SNPs filtering baed on ReguomeDB score</td>
                <td><input type="checkbox" class="form-check-input" name="eqtlMapRDBcheck" id="eqtlMapRDBcheck" onchange="CheckAll();"></td>
                <td></td>
              </tr>
              <tr>
                <td>Maximum RegulomeDB score (categorical)</td>
                <td>
                  <!-- <input type="text" class="form-control" id="eqtlMapRDBth" name="eqtlMapRDBth" value="7"> -->
                  <select class="form-control" id="eqtlMapRDBth" name="eqtlMapRDBth" onchange="CheckAll();">
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
                <td>Perform SNPs filtering based on chromatin state</td>
                <td><input type="checkbox" class="form-check-input" name="eqtlMapChr15check" id="eqtlMapChr15check" onchange="CheckAll();"></td>
                <td></td>
              </tr>
              <tr>
                <td>Tissue/cell types for 15-core chromatin state</td>
                <td>
                  <span class="multiSelect">
                    Individual tissue/cell types: <tab><a>clear</a><br/>
                    <select multiple class="form-control" style="width: 250px; overflow:auto;" id="eqtlMapChr15Ts" name="eqtlMapChr15Ts[]" onchange="CheckAll();">
                      <option value="all">All</option>
                      <option value='E001'>E001 (ESC) ES-I3 Cells</option>
                      <option value='E002'>E002 (ESC) ES-WA7 Cells</option>
                      <option value='E003'>E003 (ESC) H1 Cells</option>
                      <option value='E004'>E004 (ESC Derived) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
                      <option value='E005'>E005 (ESC Derived) H1 BMP4 Derived Trophoblast Cultured Cells</option>
                      <option value='E006'>E006 (ESC Derived) H1 Derived Mesenchymal Stem Cells</option>
                      <option value='E007'>E007 (ESC Derived) H1 Derived Neuronal Progenitor Cultured Cells</option>
                      <option value='E008'>E008 (ESC) H9 Cells</option>
                      <option value='E009'>E009 (ESC Derived) H9 Derived Neuronal Progenitor Cultured Cells</option>
                      <option value='E010'>E010 (ESC Derived) H9 Derived Neuron Cultured Cells</option>
                      <option value='E011'>E011 (ESC Derived) hESC Derived CD184+ Endoderm Cultured Cells</option>
                      <option value='E012'>E012 (ESC Derived) hESC Derived CD56+ Ectoderm Cultured Cells</option>
                      <option value='E013'>E013 (ESC Derived) hESC Derived CD56+ Mesoderm Cultured Cells</option>
                      <option value='E014'>E014 (ESC) HUES48 Cells</option>
                      <option value='E015'>E015 (ESC) HUES6 Cells</option>
                      <option value='E016'>E016 (ESC) HUES64 Cells</option>
                      <option value='E017'>E017 (Lung) IMR90 fetal lung fibroblasts Cell Line</option>
                      <option value='E018'>E018 (iPSC) iPS-15b Cells</option>
                      <option value='E019'>E019 (iPSC) iPS-18 Cells</option>
                      <option value='E020'>E020 (iPSC) iPS-20b Cells</option>
                      <option value='E021'>E021 (iPSC) iPS DF 6.9 Cells</option>
                      <option value='E022'>E022 (iPSC) iPS DF 19.11 Cells</option>
                      <option value='E023'>E023 (Fat) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
                      <option value='E024'>E024 (ESC) ES-UCSF4  Cells</option>
                      <option value='E025'>E025 (Fat) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
                      <option value='E026'>E026 (Stromal Connective) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
                      <option value='E027'>E027 (Breast) Breast Myoepithelial Primary Cells</option>
                      <option value='E028'>E028 (Breast) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
                      <option value='E029'>E029 (Blood) Primary monocytes from peripheral blood</option>
                      <option value='E030'>E030 (Blood) Primary neutrophils from peripheral blood</option>
                      <option value='E031'>E031 (Blood) Primary B cells from cord blood</option>
                      <option value='E032'>E032 (Blood) Primary B cells from peripheral blood</option>
                      <option value='E033'>E033 (Blood) Primary T cells from cord blood</option>
                      <option value='E034'>E034 (Blood) Primary T cells from peripheral blood</option>
                      <option value='E035'>E035 (Blood) Primary hematopoietic stem cells</option>
                      <option value='E036'>E036 (Blood) Primary hematopoietic stem cells short term culture</option>
                      <option value='E037'>E037 (Blood) Primary T helper memory cells from peripheral blood 2</option>
                      <option value='E038'>E038 (Blood) Primary T helper naive cells from peripheral blood</option>
                      <option value='E039'>E039 (Blood) Primary T helper naive cells from peripheral blood</option>
                      <option value='E040'>E040 (Blood) Primary T helper memory cells from peripheral blood 1</option>
                      <option value='E041'>E041 (Blood) Primary T helper cells PMA-I stimulated</option>
                      <option value='E042'>E042 (Blood) Primary T helper 17 cells PMA-I stimulated</option>
                      <option value='E043'>E043 (Blood) Primary T helper cells from peripheral blood</option>
                      <option value='E044'>E044 (Blood) Primary T regulatory cells from peripheral blood</option>
                      <option value='E045'>E045 (Blood) Primary T cells effector/memory enriched from peripheral blood</option>
                      <option value='E046'>E046 (Blood) Primary Natural Killer cells from peripheral blood</option>
                      <option value='E047'>E047 (Blood) Primary T CD8+ naive cells from peripheral blood</option>
                      <option value='E048'>E048 (Blood) Primary T CD8+ memory cells from peripheral blood</option>
                      <option value='E049'>E049 (Stromal Connective) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
                      <option value='E050'>E050 (Blood) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
                      <option value='E051'>E051 (Blood) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
                      <option value='E052'>E052 (Muscle) Muscle Satellite Cultured Cells</option>
                      <option value='E053'>E053 (Brain) Cortex derived primary cultured neurospheres</option>
                      <option value='E054'>E054 (Brain) Ganglion Eminence derived primary cultured neurospheres</option>
                      <option value='E055'>E055 (Skin) Foreskin Fibroblast Primary Cells skin01</option>
                      <option value='E056'>E056 (Skin) Foreskin Fibroblast Primary Cells skin02</option>
                      <option value='E057'>E057 (Skin) Foreskin Keratinocyte Primary Cells skin02</option>
                      <option value='E058'>E058 (Skin) Foreskin Keratinocyte Primary Cells skin03</option>
                      <option value='E059'>E059 (Skin) Foreskin Melanocyte Primary Cells skin01</option>
                      <option value='E061'>E061 (Skin) Foreskin Melanocyte Primary Cells skin03</option>
                      <option value='E062'>E062 (Blood) Primary mononuclear cells from peripheral blood</option>
                      <option value='E063'>E063 (Fat) Adipose Nuclei</option>
                      <option value='E065'>E065 (Vascular) Aorta</option>
                      <option value='E066'>E066 (Liver) Liver</option>
                      <option value='E067'>E067 (Brain) Brain Angular Gyrus</option>
                      <option value='E068'>E068 (Brain) Brain Anterior Caudate</option>
                      <option value='E069'>E069 (Brain) Brain Cingulate Gyrus</option>
                      <option value='E070'>E070 (Brain) Brain Germinal Matrix</option>
                      <option value='E071'>E071 (Brain) Brain Hippocampus Middle</option>
                      <option value='E072'>E072 (Brain) Brain Inferior Temporal Lobe</option>
                      <option value='E073'>E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
                      <option value='E074'>E074 (Brain) Brain Substantia Nigra</option>
                      <option value='E075'>E075 (GI Colon) Colonic Mucosa</option>
                      <option value='E076'>E076 (GI Colon) Colon Smooth Muscle</option>
                      <option value='E077'>E077 (GI Duodenum) Duodenum Mucosa</option>
                      <option value='E078'>E078 (GI Duodenum) Duodenum Smooth Muscle</option>
                      <option value='E079'>E079 (GI Esophagus) Esophagus</option>
                      <option value='E080'>E080 (Adrenal) Fetal Adrenal Gland</option>
                      <option value='E081'>E081 (Brain) Fetal Brain Male</option>
                      <option value='E082'>E082 (Brain) Fetal Brain Female</option>
                      <option value='E083'>E083 (Heart) Fetal Heart</option>
                      <option value='E084'>E084 (GI Intestine) Fetal Intestine Large</option>
                      <option value='E085'>E085 (GI Intestine) Fetal Intestine Small</option>
                      <option value='E086'>E086 (Kidney) Fetal Kidney</option>
                      <option value='E087'>E087 (Pancreas) Pancreatic Islets</option>
                      <option value='E088'>E088 (Lung) Fetal Lung</option>
                      <option value='E089'>E089 (Muscle) Fetal Muscle Trunk</option>
                      <option value='E090'>E090 (Muscle) Fetal Muscle Leg</option>
                      <option value='E091'>E091 (Placenta) Placenta</option>
                      <option value='E092'>E092 (GI Stomach) Fetal Stomach</option>
                      <option value='E093'>E093 (Thymus) Fetal Thymus</option>
                      <option value='E094'>E094 (GI Stomach) Gastric</option>
                      <option value='E095'>E095 (Heart) Left Ventricle</option>
                      <option value='E096'>E096 (Lung) Lung</option>
                      <option value='E097'>E097 (Ovary) Ovary</option>
                      <option value='E098'>E098 (Pancreas) Pancreas</option>
                      <option value='E099'>E099 (Placenta) Placenta Amnion</option>
                      <option value='E100'>E100 (Muscle) Psoas Muscle</option>
                      <option value='E101'>E101 (GI Rectum) Rectal Mucosa Donor 29</option>
                      <option value='E102'>E102 (GI Rectum) Rectal Mucosa Donor 31</option>
                      <option value='E103'>E103 (GI Rectum) Rectal Smooth Muscle</option>
                      <option value='E104'>E104 (Heart) Right Atrium</option>
                      <option value='E105'>E105 (Heart) Right Ventricle</option>
                      <option value='E106'>E106 (GI Colon) Sigmoid Colon</option>
                      <option value='E107'>E107 (Muscle) Skeletal Muscle Male</option>
                      <option value='E108'>E108 (Muscle) Skeletal Muscle Female</option>
                      <option value='E109'>E109 (GI Intestine) Small Intestine</option>
                      <option value='E110'>E110 (GI Stomach) Stomach Mucosa</option>
                      <option value='E111'>E111 (GI Stomach) Stomach Smooth Muscle</option>
                      <option value='E112'>E112 (Thymus) Thymus</option>
                      <option value='E113'>E113 (Spleen) Spleen</option>
                      <option value='E114'>E114 (Lung) A549 EtOH 0.02pct Lung Carcinoma Cell Line</option>
                      <option value='E115'>E115 (Blood) Dnd41 TCell Leukemia Cell Line</option>
                      <option value='E116'>E116 (Blood) GM12878 Lymphoblastoid Cells</option>
                      <option value='E117'>E117 (Cervix) HeLa-S3 Cervical Carcinoma Cell Line</option>
                      <option value='E118'>E118 (Liver) HepG2 Hepatocellular Carcinoma Cell Line</option>
                      <option value='E119'>E119 (Breast) HMEC Mammary Epithelial Primary Cells</option>
                      <option value='E120'>E120 (Muscle) HSMM Skeletal Muscle Myoblasts Cells</option>
                      <option value='E121'>E121 (Muscle) HSMM cell derived Skeletal Muscle Myotubes Cells</option>
                      <option value='E122'>E122 (Vascular) HUVEC Umbilical Vein Endothelial Primary Cells</option>
                      <option value='E123'>E123 (Blood) K562 Leukemia Cells</option>
                      <option value='E124'>E124 (Blood) Monocytes-CD14+ RO01746 Primary Cells</option>
                      <option value='E125'>E125 (Brain) NH-A Astrocytes Primary Cells</option>
                      <option value='E126'>E126 (Skin) NHDF-Ad Adult Dermal Fibroblast Primary Cells</option>
                      <option value='E127'>E127 (Skin) NHEK-Epidermal Keratinocyte Primary Cells</option>
                      <option value='E128'>E128 (Lung) NHLF Lung Fibroblast Primary Cells</option>
                      <option value='E129'>E129 (Bone) Osteoblast Primary Cells</option>
                    </select><br/>
                  </span>
                  <span class="multiSelect">
                    General tissue/cell types: <tab><a>clear</a><br/>
                    <select multiple class="form-control" id="eqtlMapChr15Gts" name="eqtlMapChr15Gts[]" onchange="CheckAll();">
                      <option value="all">All</option>
                      <option value='E080'>Adrenal (1)</option>
                      <option value='E062:E034:E045:E033:E044:E043:E039:E041:E042:E040:E037:E048:E038:E047:E029:E031:E035:E051:E050:E036:E032:E046:E030:E115:E116:E123:E124'>Blood (27)</option>
                      <option value='E129'>Bone (1)</option>
                      <option value='E054:E053:E071:E074:E068:E069:E072:E067:E073:E070:E082:E081:E125'>Brain (13)</option>
                      <option value='E028:E027:E119'>Breast (3)</option>
                      <option value='E117'>Cervix (1)</option>
                      <option value='E002:E008:E001:E015:E014:E016:E003:E024'>ESC (8)</option>
                      <option value='E007:E009:E010:E013:E012:E011:E004:E005:E006'>ESC Derived (9)</option>
                      <option value='E025:E023:E063'>Fat (3)</option>
                      <option value='E076:E106:E075'>GI Colon (3)</option>
                      <option value='E078:E077'>GI Duodenum (2)</option>
                      <option value='E079'>GI Esophagus (1)</option>
                      <option value='E085:E084:E109'>GI Intestine (3)</option>
                      <option value='E103:E101:E102'>GI Rectum (3)</option>
                      <option value='E111:E092:E110:E094'>GI Stomach (4)</option>
                      <option value='E083:E104:E095:E105'>Heart (4)</option>
                      <option value='E020:E019:E018:E021:E022'>iPSC (5)</option>
                      <option value='E086'>Kidney (1)</option>
                      <option value='E066:E118'>Liver (2)</option>
                      <option value='E017:E088:E096:E114:E128'>Lung (5)</option>
                      <option value='E052:E100:E108:E107:E089:E120:E121:E090'>Muscle (8)</option>
                      <option value='E097'>Ovary (1)</option>
                      <option value='E087:E098'>Pancreas (2)</option>
                      <option value='E099:E091'>Placenta (2)</option>
                      <option value='E055:E056:E059:E061:E057:E058:E126:E127'>Skin (8)</option>
                      <option value='E113'>Spleen (1)</option>
                      <option value='E026:E049'>Stromal Connective (2)</option>
                      <option value='E112:E093'>Thymus (2)</option>
                      <option value='E065:E122'>Vascular (2)</option>
                    </select>
                  </span>
                </td>
                <td></td>
              </tr>
              <tr>
                <td>15-core chromatin state maximum state</td>
                <td><input type="number" class="form-control" id="eqtlMapChr15Max" name="eqtlMapChr15Max" value="7" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
                <td></td>
              </tr>
              <tr>
                <td>15-core chromatin state filtering method</td>
                <td>
                  <select  class="form-control" id="eqtlMapChr15Meth" name="eqtlMapChr15Meth" onchange="CheckAll();">
                    <option selected value="any">any</option>
                    <option value="majority">majority</option>
                    <option value="all">all</option>
                  </select>
                </td>
                <td></td>
              </tr>
            </table>
          </div>
        </div></div>
        <br/>

        <!-- Gene type multiple selection -->
        <h4>4. Gene types</h4>
        <table class="table table-bordered inputTable" id="NewJobGene" style="width: auto;">
          <tr>
            <td>Gene type</td>
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
        <br/>
        <!-- MHC regions -->
        <h4>5. MHC region</h4>
        <table class="table table-bordered inputTable" id="NewJobMHC" style="width: auto;">
          <tr>
            <td>Exclude MHC region</td>
            <td><input type="checkbox" class="form-check-input" name="MHCregion" id="MHCregion" value="exMHC" checked onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Extended MHC region<br/><tab>*e.g. 25000000-33000000<br/>
            <tab>*If this is not filled, usual MHC region will be used.<br/></td>
            <td><input type="text" class="form-control" name="extMHCregion" id="extMHCregion" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
            <td></td>
          </tr>
        </table>
        <br/>

        <!-- email and job title -->
        <h4>6. email and job title</h4>
        <table class="table table-bordered inputTable" id="NewJobSubmit" style="width: auto;">
          <tr>
            <td>E-mail address</td>
            <td><input type="text" class="form-control" name="NewJobEmail" id="NewJobEmail" onkeyup="CheckAll();" onpaste="CheckAll();"  oninput="CheckAll();"/></td>
            <td></td>
          </tr>
          <tr>
            <td>Job title</td>
            <td><input type="text" class="form-control" name="NewJobTitle" id="NewJobTitle" onkeyup="CheckAll();" onpaste="CheckAll();"  oninput="CheckAll();"/></td>
            <td></td>
          </tr>
        </table>
        <br/>

        <input class="btn" type="submit" value="Submit Job" name="SubmitNewJob" id="SubmitNewJob"/>
        {!! Form::close() !!}
      </div>

      <!-- results panel -->
      <!-- job info table -->
      <div class="sidePanel container" style="padding-top:50px;" id="jobInfo">
        <h3 style="color: #00004d">Information of your job</h3>
        <div id="jobInfoTable"></div>
        <div id="test"></div>
      </div>

      <!-- genome wide plots -->
      <div class="sidePanel container" style="padding-top:50px;" id="genomePlots">
        <h3>Genome Wide Plot</h3>
        <div id="gPlotPanel" class="collapse in">
          <div id="manhattanPane" style="position: relative;">
            <h3>Manhattan Plot (GWAS summary statistics)</h3>
            <div id="manhattan" class="canvasarea"></div>
          </div>
          <div id="geneManhattanPane" style="position: relative;">
            <h3>Mahattan Plot (gene-based test)</h3>
            <div id="genesManhattan" class="canvasarea"></div>
          </div>
          <div id="QQplotPane" style="position: relative;">
            <h3>QQ plots</h3>
            <div class="col-md-6" id="QQplot" class="canvasarea">
            </div>
            <div class="col-md-6" id="geneQQplot" class="canvasarea">
            </div>
          </div>
        </div>
      </div>

      <!-- Summary panel -->
      <div class="sidePanel container" style="padding-top:50px;" id="summaryTable">
        <div class="row">
          <div class="col-md-5" id="sumTable" style="text-align:center;">
            <h4>Summary of SNPs and mapped genes</h4>
          </div>
          <div class="col-md-7" id="snpAnnotPlot" style="text-align:center;">
            <h4>Positional annotations of candidate SNPs</h4>
            <!-- <svg id="SnpAnnotPlotSVG"></svg><br/> -->
            <!-- <button class="btn" id="posAnnotPlotDown" value="Download img">Download img</button><br/> -->
          </div>
        </div>
        <br/>
        <div id="intervalPlot" style="text-align:center;"><h4>Summary per interval</h4></div>
      </div>

      <!-- result tables -->
      <div class="sidePanel container" style="padding-top:50px;" id="tables">
        <div class="panel panel-default"><div class="panel-body">
          <a href="#tablesPanel" data-toggle="collapse" style="color: #00004d"><h3>Result tables</h3></a>
            <div id="tablesPanel" class="collapse in">
            <form action="geneSubmit" method="post" target="_blank">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="jobID" value="<?php echo $jobID;?>"/>
              <input type="submit" class="btn" id="geneQuerySubmit" name="geneQuerySubmit" value="Use results for GENE2FUNC (open new tab)">
            </form>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <!-- <li role="presentation" class="active"><a href="#summaryTable" aria-controls="summaryTable" rolw="tab" data-toggle="tab">Summary</a></li> -->
              <li role="presentation" class="active"><a href="#leadSNPtablePane" aria-controls="leadSNPtablePane" rolw="tab" data-toggle="tab">lead SNPs</a></li>
              <li role="presentation"><a href="#intervalTablePane" aria-controls="intervalTablePane" rolw="tab" data-toggle="tab">Intervals</a></li>
              <li role="presentation"><a href="#SNPtablePane" aria-controls="SNPtablePane" rolw="tab" data-toggle="tab">SNPs (annotations)</a></li>
              <li role="presentation"><a href="#annovTablePane" aria-controls="annovTablePane" rolw="tab" data-toggle="tab">ANNOVAR</a></li>
              <li role="presentation"><a href="#geneTablePane" aria-controls="geneTablePane" rolw="tab" data-toggle="tab">Genes</a></li>
              <li role="presentation" id="eqtlTableTab"><a href="#eqtlTablePane" aria-controls="eqtlTablePane" rolw="tab" data-toggle="tab">eQTL</a></li>
              <li role="presentation" id="gwascatTableTab"><a href="#gwascatTablePane" aria-controls="gwascatTablePane" rolw="tab" data-toggle="tab">GWAScatalog</a></li>
              <!-- <li role="presentation"><a href="#exacTablePane" aria-controls="exacTablePane" rolw="tab" data-toggle="tab">ExAC</a></li> -->
              <li role="presentation"><a href="#paramsPane" aria-controls="paramsPane" rolw="tab" data-toggle="tab">Parameters</a></li>
              <!-- <li role="presentation"><a href="#downloads" aria-controls="downloads" rolw="tab" data-toggle="tab">Downloads</a></li> -->
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="leadSNPtablePane">
              <br/>
              <table id="leadSNPtable" class="display dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>No</th><th>Interval</th><th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>P-value</th><th>nSNPs</th><th>nGWASSNPs</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="intervalTablePane">
              <br/>
              <table id="intervalTable" class="display dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>Interval</th><th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>P-value</th><th>nLeadSNPs</th><th>start</th><th>end</th><th>nSNPs</th><th>nGWASSNPs</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="SNPtablePane">
              <br/>
              <table id="SNPtable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>uniqID</th><th>rsID</th><th>chr</th><th>bp</th><th>MAF</th><th>P-value</th><th>Interval</th><th>r2</th><th>leadSNP</th><th>Nearest gene</th>
                    <th>dist</th><th>position</th><th>CADD</th><th>RDB</th><th>minChrState(127)</th><th>commonChrState(127)</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="annovTablePane">
              <table id="annovTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>uniqID</th><th>chr</th><th>bp</th><th>Gene</th><th>Symbol</th><th>Distance</th><th>Function</th><th>Exonic function</th><th>Exon</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="geneTablePane">
              <br/>
              <table id="geneTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="eqtlTablePane">
              <br/>
              <table id="eqtlTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>uniqID</th><th>chr</th><th>bp</th><th>DB</th><th>tissue</th><th>Gene</th><th>Symbol</th><th>P-value</th><th>FDR</th><th>t/z</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="gwascatTablePane">
              <br/>
              <p>Please download a output file (gwascatalog.txt) from "Download" tab to get full information</p>
              <table id="gwascatTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>Interval</th><th>lead SNP</th><th>chr</th><th>bp</th><th>rsID</th><th>PMID</th><th>Trait</th><th>FirstAuth</th><th>Date</th><th>P-value</th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- <div role="tabpanel" class="tab-pane" id="exacTablePane">
              <br/>
              <table id="exacTable" class="display dt-body-right dt-head-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                <thead>
                  <tr>
                    <th>Interval</th><th>uniqID</th><th>chr</th><th>bp</th><th>ref</th><th>alt</th><th>Annotation</th><th>Gene</th><th>MAF</th>
                    <th>MAF(FIN)</th><th>MAF(NFE)</th><th>MAF(AMR)</th><th>MAF(AFR)</th><th>MAF(EAS)</th><th>MAF(SAS)</th><th>MAF(OTH)<th>
                  </tr>
                </thead>
              </table>
            </div> -->
            <div role="tabpanel" class="tab-pane" id="paramsPane">
              <br/>
              <div id="paramTable"></div>
            </div>

          </div>
          </div>
        </div></div>

        <!-- region plot -->
        <div id="regionalPlot">
          <div class="panel panel-default"><div class="panel-body">
            <a href="#regionalPlotPanel" data-toggle="collapse" style="color: #00004d"><h3>Regional Plot (GWAS association)</h3></a>
            <div class="row collapse in" id="regionalPlotPanel">
              <div class="col-md-9">
                <div id="locusPlot" style="text-align: center;">
                  <a id="plotClear" style="position: absolute;right: 30px;">Clear</a>
                </div>
              </div>
              <div class="col-md-3">
                <div id="selectedLeadSNP"></div>
              </div>
            </div>
          </div></div>
        </div>
      </div>


      <!-- Downloads -->
      <div class="sidePanel container" style="padding-top:50px;" id="downloads">
        <h4>Download files </h4>
        <form action="filedown" method="post" target="_blank">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="jobID" value="<?php echo $jobID;?>"/>
          <!-- <input type="checkbox" name="allfiles" id="allfiles" checked onchange="DownloadFiles();">All files</br> -->
          <input type="checkbox" name="paramfile" id="paramfile" checked onchange="DownloadFiles();">Parameters</br>
          <input type="checkbox" name="leadfile" id="leadfile" checked onchange="DownloadFiles();">lead SNP table (independent lead SNPs) </br>
          <input type="checkbox" name="intervalfile" id="intervalfile" checked onchange="DownloadFiles();">Interval table <br/>
          <input type="checkbox" name="snpsfile" id="snpsfile" checked onchange="DownloadFiles();"> SNP table (Candidate SNPs with chr, bp, P-value, CADD, RDB, nearest gene, interval and lead SNPs)<br/>
          <input type="checkbox" name="annovfile" id="annovfile" checked onchange="DownloadFiles();">ANNOVAR results (uniqID, annotation, gene and distance, SNP-gene pair per line)<br/>
          <input type="checkbox" name="annotfile" id="annotfile" checked onchange="DownloadFiles();">Annotations (CADD, RDB and Chromatin state of 127 tissue/cell types)<br/>
          <input type="checkbox" name="genefile" id="genefile" checked onchange="DownloadFiles();">Gene table (mapped genes)<br/>
          <div id="eqtlfiledown"><input type="checkbox" name="eqtlfile" id="eqtlfile" checked onchange="DownloadFiles();">eQTL table (eQTL of selected tissue types)<br/></div>
          <!-- <input type="checkbox" name="exacfile" id="exacfile" checked onchange="DownloadFiles();">ExAC variants (rare variants from ExAC within intervals)<br/> -->
          <input type="checkbox" name="gwascatfile" id="gwascatfile" checked onchange="DownloadFiles();">GWAScatalog (full recode from GWAScatalog)<br/>
          <a id="allfiles"> Select All </a><tab><a id="clearfiles"> Clear</a><br/>
          <br/>
          <input class="btn" type="submit" name="download" id="download" value="Download files"/>
        </form>
      </div>

      <!-- Annotation plot options -->
      <div class="sidePanel container" style="padding-top:50px;" id="annotPlot">
        <div class="panel panel-default"><div class="panel-body">
          <h3>Regional plot with annotation</h3>
          <div id="annotPlotPanel">
            <!-- {!! Form::open(array('url' => 'snp2gene/annotPlot')) !!} -->
            <!-- {!! Form::token() !!} -->
            <form action="annotPlot" method="post" target="_blank">
            Select region to plot: <span style="color:red">Mandatory</span><br/>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="jobID" value="<?php echo $jobID;?>"/>
            <tab><input type="checkbox" name="annotPlotSelect_leadSNP" id="annotPlotSelect_leadSNP" value="null" onchange="annotSelect('leadSNP')"/>LD block of selected lead SNP<br/>
            <tab><input type="checkbox" name="annotPlotSelect_interval" id="annotPlotSelect_interval" value="null" onchange="annotSelect('interval')"/>Selected interval<br/>
            <!-- <tab><input type="checkbox" name="annotPlotSelect_SNP" id="annotPlotSelect_SNP" value="null" onchange="annotSelect('SNP')"/>LD block which selected SNP belongs<br/> -->
            <!-- <tab><input type="checkbox" id="annotPlotSelect" value="leadSNP"/>Create regional plot for all intervals<br/> -->
            Select annotation(s) to plot:<br/>
            <tab><input type="checkbox" name="annotPlot_GWASp" id="annotPlot_GWASp" checked/>GWAS association statistics<br/>
            <tab><input type="checkbox" name="annotPlot_CADD" id="annotPlot_CADD" checked/>CADD score<br/>
            <tab><input type="checkbox" name="annotPlot_RDB" id="annotPlot_RDB" checked/>RegulomeDB score<br/>
            <tab><input type="checkbox" name="annotPlot_Chrom15" id="annotPlot_Chrom15" onchange="annotSelect();"/>Chromatine 15 state
              <div id="annotPlotChr15Opt">
              <tab><tab><span style="color:red;">Please select at least one tissue type.</span><br/>
              <tab><tab>Individual tissue/cell types: <a id="annotPlotChr15TsClear">clear</a><br/>
              <tab><tab><select multiple size="5" id="annotPlotChr15Ts" name="annotPlotChr15Ts[]" onchange="annotSelect()">
                <option value="all">All</option>
                <option value='E001'>E001 (ESC) ES-I3 Cells</option>
                <option value='E002'>E002 (ESC) ES-WA7 Cells</option>
                <option value='E003'>E003 (ESC) H1 Cells</option>
                <option value='E004'>E004 (ESC Derived) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
                <option value='E005'>E005 (ESC Derived) H1 BMP4 Derived Trophoblast Cultured Cells</option>
                <option value='E006'>E006 (ESC Derived) H1 Derived Mesenchymal Stem Cells</option>
                <option value='E007'>E007 (ESC Derived) H1 Derived Neuronal Progenitor Cultured Cells</option>
                <option value='E008'>E008 (ESC) H9 Cells</option>
                <option value='E009'>E009 (ESC Derived) H9 Derived Neuronal Progenitor Cultured Cells</option>
                <option value='E010'>E010 (ESC Derived) H9 Derived Neuron Cultured Cells</option>
                <option value='E011'>E011 (ESC Derived) hESC Derived CD184+ Endoderm Cultured Cells</option>
                <option value='E012'>E012 (ESC Derived) hESC Derived CD56+ Ectoderm Cultured Cells</option>
                <option value='E013'>E013 (ESC Derived) hESC Derived CD56+ Mesoderm Cultured Cells</option>
                <option value='E014'>E014 (ESC) HUES48 Cells</option>
                <option value='E015'>E015 (ESC) HUES6 Cells</option>
                <option value='E016'>E016 (ESC) HUES64 Cells</option>
                <option value='E017'>E017 (Lung) IMR90 fetal lung fibroblasts Cell Line</option>
                <option value='E018'>E018 (iPSC) iPS-15b Cells</option>
                <option value='E019'>E019 (iPSC) iPS-18 Cells</option>
                <option value='E020'>E020 (iPSC) iPS-20b Cells</option>
                <option value='E021'>E021 (iPSC) iPS DF 6.9 Cells</option>
                <option value='E022'>E022 (iPSC) iPS DF 19.11 Cells</option>
                <option value='E023'>E023 (Fat) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
                <option value='E024'>E024 (ESC) ES-UCSF4  Cells</option>
                <option value='E025'>E025 (Fat) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
                <option value='E026'>E026 (Stromal Connective) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
                <option value='E027'>E027 (Breast) Breast Myoepithelial Primary Cells</option>
                <option value='E028'>E028 (Breast) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
                <option value='E029'>E029 (Blood) Primary monocytes from peripheral blood</option>
                <option value='E030'>E030 (Blood) Primary neutrophils from peripheral blood</option>
                <option value='E031'>E031 (Blood) Primary B cells from cord blood</option>
                <option value='E032'>E032 (Blood) Primary B cells from peripheral blood</option>
                <option value='E033'>E033 (Blood) Primary T cells from cord blood</option>
                <option value='E034'>E034 (Blood) Primary T cells from peripheral blood</option>
                <option value='E035'>E035 (Blood) Primary hematopoietic stem cells</option>
                <option value='E036'>E036 (Blood) Primary hematopoietic stem cells short term culture</option>
                <option value='E037'>E037 (Blood) Primary T helper memory cells from peripheral blood 2</option>
                <option value='E038'>E038 (Blood) Primary T helper naive cells from peripheral blood</option>
                <option value='E039'>E039 (Blood) Primary T helper naive cells from peripheral blood</option>
                <option value='E040'>E040 (Blood) Primary T helper memory cells from peripheral blood 1</option>
                <option value='E041'>E041 (Blood) Primary T helper cells PMA-I stimulated</option>
                <option value='E042'>E042 (Blood) Primary T helper 17 cells PMA-I stimulated</option>
                <option value='E043'>E043 (Blood) Primary T helper cells from peripheral blood</option>
                <option value='E044'>E044 (Blood) Primary T regulatory cells from peripheral blood</option>
                <option value='E045'>E045 (Blood) Primary T cells effector/memory enriched from peripheral blood</option>
                <option value='E046'>E046 (Blood) Primary Natural Killer cells from peripheral blood</option>
                <option value='E047'>E047 (Blood) Primary T CD8+ naive cells from peripheral blood</option>
                <option value='E048'>E048 (Blood) Primary T CD8+ memory cells from peripheral blood</option>
                <option value='E049'>E049 (Stromal Connective) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
                <option value='E050'>E050 (Blood) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
                <option value='E051'>E051 (Blood) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
                <option value='E052'>E052 (Muscle) Muscle Satellite Cultured Cells</option>
                <option value='E053'>E053 (Brain) Cortex derived primary cultured neurospheres</option>
                <option value='E054'>E054 (Brain) Ganglion Eminence derived primary cultured neurospheres</option>
                <option value='E055'>E055 (Skin) Foreskin Fibroblast Primary Cells skin01</option>
                <option value='E056'>E056 (Skin) Foreskin Fibroblast Primary Cells skin02</option>
                <option value='E057'>E057 (Skin) Foreskin Keratinocyte Primary Cells skin02</option>
                <option value='E058'>E058 (Skin) Foreskin Keratinocyte Primary Cells skin03</option>
                <option value='E059'>E059 (Skin) Foreskin Melanocyte Primary Cells skin01</option>
                <option value='E061'>E061 (Skin) Foreskin Melanocyte Primary Cells skin03</option>
                <option value='E062'>E062 (Blood) Primary mononuclear cells from peripheral blood</option>
                <option value='E063'>E063 (Fat) Adipose Nuclei</option>
                <option value='E065'>E065 (Vascular) Aorta</option>
                <option value='E066'>E066 (Liver) Liver</option>
                <option value='E067'>E067 (Brain) Brain Angular Gyrus</option>
                <option value='E068'>E068 (Brain) Brain Anterior Caudate</option>
                <option value='E069'>E069 (Brain) Brain Cingulate Gyrus</option>
                <option value='E070'>E070 (Brain) Brain Germinal Matrix</option>
                <option value='E071'>E071 (Brain) Brain Hippocampus Middle</option>
                <option value='E072'>E072 (Brain) Brain Inferior Temporal Lobe</option>
                <option value='E073'>E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
                <option value='E074'>E074 (Brain) Brain Substantia Nigra</option>
                <option value='E075'>E075 (GI Colon) Colonic Mucosa</option>
                <option value='E076'>E076 (GI Colon) Colon Smooth Muscle</option>
                <option value='E077'>E077 (GI Duodenum) Duodenum Mucosa</option>
                <option value='E078'>E078 (GI Duodenum) Duodenum Smooth Muscle</option>
                <option value='E079'>E079 (GI Esophagus) Esophagus</option>
                <option value='E080'>E080 (Adrenal) Fetal Adrenal Gland</option>
                <option value='E081'>E081 (Brain) Fetal Brain Male</option>
                <option value='E082'>E082 (Brain) Fetal Brain Female</option>
                <option value='E083'>E083 (Heart) Fetal Heart</option>
                <option value='E084'>E084 (GI Intestine) Fetal Intestine Large</option>
                <option value='E085'>E085 (GI Intestine) Fetal Intestine Small</option>
                <option value='E086'>E086 (Kidney) Fetal Kidney</option>
                <option value='E087'>E087 (Pancreas) Pancreatic Islets</option>
                <option value='E088'>E088 (Lung) Fetal Lung</option>
                <option value='E089'>E089 (Muscle) Fetal Muscle Trunk</option>
                <option value='E090'>E090 (Muscle) Fetal Muscle Leg</option>
                <option value='E091'>E091 (Placenta) Placenta</option>
                <option value='E092'>E092 (GI Stomach) Fetal Stomach</option>
                <option value='E093'>E093 (Thymus) Fetal Thymus</option>
                <option value='E094'>E094 (GI Stomach) Gastric</option>
                <option value='E095'>E095 (Heart) Left Ventricle</option>
                <option value='E096'>E096 (Lung) Lung</option>
                <option value='E097'>E097 (Ovary) Ovary</option>
                <option value='E098'>E098 (Pancreas) Pancreas</option>
                <option value='E099'>E099 (Placenta) Placenta Amnion</option>
                <option value='E100'>E100 (Muscle) Psoas Muscle</option>
                <option value='E101'>E101 (GI Rectum) Rectal Mucosa Donor 29</option>
                <option value='E102'>E102 (GI Rectum) Rectal Mucosa Donor 31</option>
                <option value='E103'>E103 (GI Rectum) Rectal Smooth Muscle</option>
                <option value='E104'>E104 (Heart) Right Atrium</option>
                <option value='E105'>E105 (Heart) Right Ventricle</option>
                <option value='E106'>E106 (GI Colon) Sigmoid Colon</option>
                <option value='E107'>E107 (Muscle) Skeletal Muscle Male</option>
                <option value='E108'>E108 (Muscle) Skeletal Muscle Female</option>
                <option value='E109'>E109 (GI Intestine) Small Intestine</option>
                <option value='E110'>E110 (GI Stomach) Stomach Mucosa</option>
                <option value='E111'>E111 (GI Stomach) Stomach Smooth Muscle</option>
                <option value='E112'>E112 (Thymus) Thymus</option>
                <option value='E113'>E113 (Spleen) Spleen</option>
                <option value='E114'>E114 (Lung) A549 EtOH 0.02pct Lung Carcinoma Cell Line</option>
                <option value='E115'>E115 (Blood) Dnd41 TCell Leukemia Cell Line</option>
                <option value='E116'>E116 (Blood) GM12878 Lymphoblastoid Cells</option>
                <option value='E117'>E117 (Cervix) HeLa-S3 Cervical Carcinoma Cell Line</option>
                <option value='E118'>E118 (Liver) HepG2 Hepatocellular Carcinoma Cell Line</option>
                <option value='E119'>E119 (Breast) HMEC Mammary Epithelial Primary Cells</option>
                <option value='E120'>E120 (Muscle) HSMM Skeletal Muscle Myoblasts Cells</option>
                <option value='E121'>E121 (Muscle) HSMM cell derived Skeletal Muscle Myotubes Cells</option>
                <option value='E122'>E122 (Vascular) HUVEC Umbilical Vein Endothelial Primary Cells</option>
                <option value='E123'>E123 (Blood) K562 Leukemia Cells</option>
                <option value='E124'>E124 (Blood) Monocytes-CD14+ RO01746 Primary Cells</option>
                <option value='E125'>E125 (Brain) NH-A Astrocytes Primary Cells</option>
                <option value='E126'>E126 (Skin) NHDF-Ad Adult Dermal Fibroblast Primary Cells</option>
                <option value='E127'>E127 (Skin) NHEK-Epidermal Keratinocyte Primary Cells</option>
                <option value='E128'>E128 (Lung) NHLF Lung Fibroblast Primary Cells</option>
                <option value='E129'>E129 (Bone) Osteoblast Primary Cells</option>
              </select><br/>

              <tab><tab>General tissue/cell types: <a id="annotPlotChr15GtsClear">clear</a><br/>
              <tab><tab><select multiple size="5" id="annotPlotChr15Gts" name="annotPlotChr15Gts[]" onchange="annotSelect()">
                <option value="all">All</option>
                <option value='E080'>Adrenal (1)</option>
                <option value='E062:E034:E045:E033:E044:E043:E039:E041:E042:E040:E037:E048:E038:E047:E029:E031:E035:E051:E050:E036:E032:E046:E030:E115:E116:E123:E124'>Blood (27)</option>
                <option value='E129'>Bone (1)</option>
                <option value='E054:E053:E071:E074:E068:E069:E072:E067:E073:E070:E082:E081:E125'>Brain (13)</option>
                <option value='E028:E027:E119'>Breast (3)</option>
                <option value='E117'>Cervix (1)</option>
                <option value='E002:E008:E001:E015:E014:E016:E003:E024'>ESC (8)</option>
                <option value='E007:E009:E010:E013:E012:E011:E004:E005:E006'>ESC Derived (9)</option>
                <option value='E025:E023:E063'>Fat (3)</option>
                <option value='E076:E106:E075'>GI Colon (3)</option>
                <option value='E078:E077'>GI Duodenum (2)</option>
                <option value='E079'>GI Esophagus (1)</option>
                <option value='E085:E084:E109'>GI Intestine (3)</option>
                <option value='E103:E101:E102'>GI Rectum (3)</option>
                <option value='E111:E092:E110:E094'>GI Stomach (4)</option>
                <option value='E083:E104:E095:E105'>Heart (4)</option>
                <option value='E020:E019:E018:E021:E022'>iPSC (5)</option>
                <option value='E086'>Kidney (1)</option>
                <option value='E066:E118'>Liver (2)</option>
                <option value='E017:E088:E096:E114:E128'>Lung (5)</option>
                <option value='E052:E100:E108:E107:E089:E120:E121:E090'>Muscle (8)</option>
                <option value='E097'>Ovary (1)</option>
                <option value='E087:E098'>Pancreas (2)</option>
                <option value='E099:E091'>Placenta (2)</option>
                <option value='E055:E056:E059:E061:E057:E058:E126:E127'>Skin (8)</option>
                <option value='E113'>Spleen (1)</option>
                <option value='E026:E049'>Stromal Connective (2)</option>
                <option value='E112:E093'>Thymus (2)</option>
                <option value='E065:E122'>Vascular (2)</option>
              </select>
              </div>
              <br/>
            <div id="check_eqtl_annotPlot"><tab><input type="checkbox" name="annotPlot_eqtl" id="annotPlot_eqtl" checked/>eQTL<br/></div>
            <div id="CheckAnnotPlotOpt"></div>
            <input class="btn" type="submit" name="submit" id= "annotPlotSubmit" value="Plot"><br/>
            </form>
          </div>
        </div></div>
      </div>
    </div>
  </div>
</div>


@stop
