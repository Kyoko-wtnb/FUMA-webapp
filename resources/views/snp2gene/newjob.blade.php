<div id="newJob" class="sidePanel container" style="padding-top:50px;">
  {!! Form::open(array('url' => 'snp2gene/newJob', 'files' => true, 'novalidate'=>'novalidate')) !!}
  <!-- New -->
  <h4 style="color: #00004d">Upload your GWAS summary statistics and set parameters to obtain functional annotations of the genomic loci associated with your trait</h4>
  <br/>
  <!-- Input files upload -->
  <div class="panel panel-default" style="padding-top: 0px;">
    <div class="panel-heading input" style="padding:5px;">
      <h4>1. Upload input files <a href="#NewJobFilesPanel" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
    </div>
    <div class="panel-body collapse in" id="NewJobFilesPanel">
      <div id="fileFormatError"></div>
      <table class="table table-bordered inputTable" id="NewJobFiles" style="width: auto;">
        <tr>
          <td>GWAS summary statistics
            <a class="infoPop" data-toggle="popover" title="GWAS summary statistics input file" data-content="Every row should have information on one SNP.
            The minimum required columns are ‘chromosome, position and P-value’ or ‘rsID and P-value’.
            If you provide position, please make sure the position is in hg19.
            The input file should be plain text format and not compressed.
            If you would like to test FUMA, please check 'Use example input', this will load an example file automatically.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><input type="file" class="form-control-file" name="GWASsummary" id="GWASsummary" onchange="CheckAll()"/>
            Or <input type="checkbox" class="form-check-input" name="egGWAS" id="egGWAS" onchange="CheckAll()"/> : Use example input (Crohn's disease, Franke et al. 2010).
          </td>
          <td></td>
        </tr>
        <tr>
          <td>Pre-defined lead SNPs
            <a class="infoPop" data-toggle="popover" title="Pre-defined lead SNPs" data-content="This option can be used when you already have determined lead SNPs and do not want FUMA to do this for you. This option can be also used when you want to include specific SNPs as lead SNPs which do no reach significant P-value threshold. The input file should have 3 columns, rsID, chromosome and position with header (header could be anything but the order of columns have to match).">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><input type="file" class="form-control-file" name="leadSNPs" id="leadSNPs" onchange="CheckAll()"/></td>
          <td></td>
        </tr>
        <tr>
          <td>Identify additional independent lead SNPs
            <a class="infoPop" data-toggle="popover" title="Additional identification of lead SNPs" data-content="This option is only vallid when pre-defined lead SNPs are provided. Please uncheck this to NOT IDENTIFY additional lead SNPs than the provided ones. When this option is checked, FUMA will identify all independent lead SNPs after taking all SNPs in LD of pre-defined lead SNPs if there is any.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><input type="checkbox" class="form-check-input" name="addleadSNPs" id="addleadSNPs" value="1" checked onchange="CheckAll()"></td>
          <td></td>
        </tr>
        <tr>
          <td>Predefined genomic region
            <a class="infoPop" data-toggle="popover" title="Pre-defined genomic regions" data-content="This option can be used when you already have defined specific genomic regions of interest and only require annotations of significant SNPs and their proxi SNPs in these regions. The input file should have 3 columns, chromosome, start and end position (on hg19) with header (header could be anything but the order of columns have to match).">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><input type="file" class="form-control-file" name="regions" id="regions" onchange="CheckAll()"/></td>
          <td></td>
        </tr>
      </table>
    </div>
  </div>

  <!-- Parameters for lead SNPs and candidate SNPs -->
  <div class="panel panel-default" style="padding-top: 0px;">
    <div class="panel-heading input" style="padding:5px;">
      <h4>2. Parameters for lead SNPs and candidate SNPs identification<a href="#NewJobParamsPanel" data-toggle="collapse" class="active" style="float: right; padding-right:20px;"><i class="fa fa-chevron-up"></i></a></h4>
    </div>
    <div class="panel-body collapse in" id="NewJobParamsPanel">
      <table class="table table-bordered inputTable" id="NewJobParams" style="width: auto;">
        <tr>
          <td>Sample size (N)
            <a class="infoPop" data-toggle="popover" title="Sample size" data-content="The total number of individuals (cases + controls, or total N) used in GWAS.
            This is only used for MAGMA. It does not affect functional annotations and prioritizations.
            If you don't know the sample size, the random number should be fine (> 50), yet that does not render the gene-based tests from MAGMA invalid.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><input type="number" class="form-control" id="N" name="N" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
          <td></td>
        </tr>
        <tr>
          <td>Minimum P-value of lead SNPs (&le;)</td>
          <td><input type="number" class="form-control" id="leadP" name="leadP" value="5e-8" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
          <td></td>
        </tr>
        <tr>
          <td>r<sup>2</sup> threshold to define LD structure of lead SNPs (&ge;)</td>
          <td><input type="number" class="form-control" id="r2" name="r2" value="0.6" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
          <td></td>
        </tr>
        <tr>
          <td>Maximum P-value cutoff (&le;)
            <a class="infoPop" data-toggle="popover" title="GWAS P-value cutoff" data-content="This threshold defines the maximum P-values of SNPs to be included in the annotation. Setting it at 1 means that all SNPs that are in LD with the lead SNP will be included in the annotation and prioritization even though they may not show a significant association with the phenotype. We advise to set this threshold at least at 0.05.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
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
          <td><input type="number" class="form-control" id="maf" name="maf" value="0.01" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
          <td></td>
        </tr>
        <tr>
          <td>Maximum distance between LD blocks to merge into a locus (&le; kb)
            <a class="infoPop" data-toggle="popover" title="Maximum distance between LD blocks to merge" data-content="LD blocks clorser than the distance will be merged into a genomic locus. If this is set at 0, only phesically overlapped LD blocks will be merged. This is only for representation of GWAS risk loci which does not affect any annotation and prioritization results.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><span class="form-inline"><input type="number" class="form-control" id="mergeDist" name="mergeDist" value="250" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/> kb</span></td>
          <td></td>
        </tr>
      </table>
    </div>
  </div>

  <!-- Parameters for gene mapping -->
  <!-- <h4>3. Parameters for gene mapping</h4> -->
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
          <td><input type="checkbox" class="form-check-input" name="posMap" id="posMap" checked onchange="CheckAll();"></td>
          <td></td>
        </tr>
        <div id="posMapOptions">
          <tr class="posMapOptions">
            <td>Distance based mapping
              <a class="infoPop" data-toggle="popover" title="Distance to genes" data-content="This maps SNPs to genes purely based on distances regardless of functional consequence. Please specify maximum distance in the box below.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="checkbox" class="form-check-input" name="windowCheck" id="windowCheck" checked onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr class="posMapOptions">
            <td>Maximum distance to genes (&le; kb)
              <a class="infoPop" data-toggle="popover" title="Maximum distance to genes" data-content="This option is only valid when distance based mapping is performed. Note that 0 includes 1 kb up and down stream regions as 3' UTR and 5' UTR.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><span class="form-inline"><input type="number" class="form-control" id="posMapWindow" name="posMapWindow" value="10" min="0" max="1000" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"> kb</span></td>
            <td></td>
          </tr>
          <tr class="posMapOptions">
            <td>Annotation based mapping
              <a class="infoPop" data-toggle="popover" title="Annotation based mapping" data-content="This is alternative positional mapping. Instead of mapping all SNPs to genes based on distance, anntation based mapping filters on SNPs that have selected functional consequences on genes. Unless intergenic SNPs are selected, all SNPs have distance 0 to the genes.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
              <br/>
              <span class="info"><i class="fa fa-info"></i> Multiple annotations can be selected. <br/>
                (usually ctrl+click (windows) or command+click (OS X))
              </span>
            </td>
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
            <td><input type="checkbox" class="form-check-input" name="posMapCADDcheck" id="posMapCADDcheck" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Minimum CADD score (&ge;)
              <a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The heigher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="number" class="form-control" id="posMapCADDth" name="posMapCADDth" value="12.37" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td rowspan="2">RegulomeDB</td>
            <td>Perform SNPs filtering baed on ReguomeDB score
              <a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="checkbox" class="form-check-input" name="posMapRDBcheck" id="posMapRDBcheck" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Maximum RegulomeDB score (categorical)
              <a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least liekly. Some SNPs have 'NA' which are not assigned any score.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
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
            <td>Perform SNPs filtering based on chromatin state
              <a class="infoPop" data-toggle="popover" title="15-core chromatin state filtering" data-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="checkbox" class="form-check-input" name="posMapChr15check" id="posMapChr15check" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Tissue/cell types for 15-core chromatin state<br/>
                <span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
            </td>
            <td>
              <span class="multiSelect">
                Individual tissue/cell types:<tab><a>clear</a><br/>
                <select multiple class="form-control" id="posMapChr15Ts" name="posMapChr15Ts[]" onchange="CheckAll();">
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
                <span class="info"><i class="fa fa-info"></i> Numbers in parentheses represent the numbr of epigenoms belongs to the correspond tissue types (anatomy).</span>
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
            <td>15-core chromatin state maximum state
              <a class="infoPop" data-toggle="popover" title="The maximum chromatin state" data-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="number" class="form-control" id="posMapChr15Max" name="posMapChr15Max" value="7" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
            <td></td>
          </tr>
          <tr>
            <td>15-core chromatin state filtering method
              <a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissye/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
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
    </div>
  </div>

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
          <td><input type="checkbox" calss="form-control" name="eqtlMap", id="eqtlMap" onchange="CheckAll();"></td>
          <td></td>
        </tr>
        <!-- <div id="eqtlMapOptions"> -->
          <tr class="eqtlMapOptions">
            <td>Tissue types
              <a class="infoPop" data-toggle="popover" title="Tissue types of eQTLs" data-content="This is mandatory parameter for eQTL mapping. Currentlly 44 tissue types from GTEx and two large scale eQTL study of blood cell are available.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
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
            <td>eQTL P-value threshold
              <a class="infoPop" data-toggle="popover" title="eQTL P-value threshold" data-content="By default, only significant eQTLs are used (FDR &le; 0.05). Please UNCHECK 'Use only significant snp-gene pair' to filter eQTLs based on raw P-value.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
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
            <td><input type="checkbox" class="form-check-input" name="eqtlMapCADDcheck" id="eqtlMapCADDcheck" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Minimum CADD score (&ge;)
              <a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The heigher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="number" class="form-control" id="eqtlMapCADDth" name="eqtlMapCADDth" value="12.37" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td rowspan="2">RegulomeDB</td>
            <td>Perform SNPs filtering baed on ReguomeDB score
              <a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="checkbox" class="form-check-input" name="eqtlMapRDBcheck" id="eqtlMapRDBcheck" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Maximum RegulomeDB score (categorical)
              <a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least liekly. Some SNPs have 'NA' which are not assigned any score.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
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
            <td>Perform SNPs filtering based on chromatin state
              <a class="infoPop" data-toggle="popover" title="15-core chromatin state filtering" data-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="checkbox" class="form-check-input" name="eqtlMapChr15check" id="eqtlMapChr15check" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Tissue/cell types for 15-core chromatin state<br/>
              <span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
            </td>
            <td>
              <span class="multiSelect">
                Individual tissue/cell types: <tab><a>clear</a><br/>
                <select multiple class="form-control" id="eqtlMapChr15Ts" name="eqtlMapChr15Ts[]" onchange="CheckAll();">
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
                <span class="info"><i class="fa fa-info"></i> Numbers in parentheses represent the numbr of epigenoms belongs to the correspond tissue types (anatomy).</span>
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
            <td>15-core chromatin state maximum state
              <a class="infoPop" data-toggle="popover" title="The maximum chromatin state" data-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="number" class="form-control" id="eqtlMapChr15Max" name="eqtlMapChr15Max" value="7" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
            <td></td>
          </tr>
          <tr>
            <td>15-core chromatin state filtering method
              <a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissye/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
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
            <a class="infoPop" data-toggle="popover" title="Exclude MHC region" data-content="Please cehck to EXCLUDE MHC region.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><input type="checkbox" class="form-check-input" name="MHCregion" id="MHCregion" value="exMHC" checked onchange="CheckAll();"></td>
          <td></td>
        </tr>
        <tr>
          <td>Extended MHC region
            <a class="infoPop" data-toggle="popover" title="Extended MHC region" data-content="In case you would like to exclude an extended MHC region, please specify here. If this option is not given, the default MHC region (between MOG and COL11A2 genes) will be used.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a><br/>
            <span class="info"><i class="fa fa-info"></i>e.g. 25000000-33000000<br/>
          </td>
          <td><input type="text" class="form-control" name="extMHCregion" id="extMHCregion" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
          <td></td>
        </tr>
      </table>
    </div>
  </div>

  <!-- job title -->
  <div class="panel panel-default" style="padding:0px;">
    <div class="panel-heading input" style="padding:5px;">
      <h4>6. Title of job submittion<a href="#NewJobTitlePanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
    </div>
    <div class="panel-body collapse" id="NewJobTitlePanel">
      <table class="table table-bordered inputTable" id="NewJobSubmit" style="width: auto;">
        <tr>
          <td>Title</td>
          <td><input type="text" class="form-control" name="NewJobTitle" id="NewJobTitle" onkeyup="CheckAll();" onpaste="CheckAll();"  oninput="CheckAll();"/></td>
          <td></td>
        </tr>
      </table>
    </div>
  </div>

  <input class="btn" type="submit" value="Submit Job" name="SubmitNewJob" id="SubmitNewJob"/>
  {!! Form::close() !!}
</div>
