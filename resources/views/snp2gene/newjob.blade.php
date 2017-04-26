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
            If you provide position, please make sure the position is on hg19.
            The file could be complete results of GWAS or a subset of SNPs can be used as an input.
            The input file should be plain text, zip or gzip files.
            If you would like to test FUMA, please check 'Use example input', this will load an example file automatically.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><input type="file" class="form-control-file" name="GWASsummary" id="GWASsummary"/>
            Or <input type="checkbox" class="form-check-input" name="egGWAS" id="egGWAS" onchange="CheckAll()"/> : Use example input (Crohn's disease, Franke et al. 2010).
          </td>
          <td></td>
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
            <!-- <span class="form-inline">MAF: <input type="text" class="form-control" id="mafcol" name="mafcol"></span>
            <a class="infoPop" data-toggle="popover" title="Minor allele frequency" data-content="Only when provided the column name of MAF, FUMA will use the input MAF for filtering of SNPs.
            Otherwise, MAF of reference panel (1000G Phase3) of defined population will be used.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a><br/> -->
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
            This is only used for MAGMA. When total sample size is defined, the same number will be used for all SNPs.
            If you have column 'N' in yout input GWAS summary statistics file, specified column will be used for N per SNP.
            It does not affect functional annotations and prioritizations.
            If you don't know the sample size, the random number should be fine (> 50), yet that does not render the gene-based tests from MAGMA invalid.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td>
            Total sample size (integer): <input type="number" class="form-control" id="N" name="N" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();">
            OR<br/>
            Column name for N per SNP (text): <input type="text" class="form-control" id="Ncol" name="Ncol" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();">
          </td>
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
            <td>Distance to genes or <br>functional consequences of SNPs on genes to map
              <a class="infoPop" data-toggle="popover" title="Positional mapping" data-content="
              Positional mapping can be performed purly based on the phisical distance between SNPs and genes by providing the maximum distance.
              Optionally, functional consequences of SNPs on genes can be selected to map only specific SNPs such as SNPs locating on exonic regions.
              Note that when functional consequnces are selected, only SNPs locationg on the gene body (distance 0) are mapped to genes except upstream and downstream SNPs which are up to 1kb apart from TSS or TES.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td>
              <span class="form-inline">Maximum distance: <input type="number" class="form-control" id="posMapWindow" name="posMapWindow" value="10" min="0" max="1000" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"> kb</span><br/>
              OR<br/>
              Functional consequences of SNPs on genes:<br/>
              <span class="multiSelect">
                <a>clear</a><br/>
                <select multiple class="form-control" id="posMapAnnot" name="posMapAnnot[]" onchange="CheckAll();">
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
                <a style="float:right; padding-right:20px;">clear</a><br/>
                <select multiple class="form-control" size="10" id="posMapChr15Ts" name="posMapChr15Ts[]" onchange="CheckAll();">
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
                <a style="float:right; padding-right:20px;">clear</a><br/>
                <select multiple class="form-control" id="eqtlMapTs" name="eqtlMapTs[]" size="10" onchange="CheckAll();">
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
                <a style="float:right; padding-right:20px;">clear</a><br/>
                <select multiple class="form-control" size="10" id="eqtlMapChr15Ts" name="eqtlMapChr15Ts[]" onchange="CheckAll();">
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

  <div class="panel panel-default" style="padding: 0px;">
    <div class="panel-heading input" style="padding:5px;">
      <h4>3-3. Gene Mapping (3D Chromatin Interaction mapping)<a href="#NewJobCiMapPanel" data-toggle="collapse" style="float: right; padding-right:20px;"><i class="fa fa-chevron-down"></i></a></h4>
    </div>
    <div class="panel-body collapse" id="NewJobCiMapPanel">
      <h4>3D chromatin interaction mapping</h4>
      <table class="table table-bordered inputTable" id="NewJobCiMap" style="width: auto;">
        <tr>
          <td>Perform 3D chromatin interaction mapping
            <a class="infoPop" data-toggle="popover" title="3D chromatin interaction mapping" data-content="3D chromatin interaction mapping maps SNPs to genes based on chromatin interactions such as Hi-C and ChIA-PET. Please check to perform this mapping.">
              <i class="fa fa-question-circle-o fa-lg"></i>
            </a>
          </td>
          <td><input type="checkbox" calss="form-control" name="ciMap", id="ciMap" onchange="CheckAll();"></td>
          <td></td>
        </tr>
        <!-- <div id="eqtlMapOptions"> -->
          <tr class="ciMapOptions">
            <td>Buildin Hi-C data (GSE87112)
              <a class="infoPop" data-toggle="popover" title="Buildin Hi-C data" data-content="Hi-C datasets of 21 tissue and cell types from GSE87112 are selectabe as buildin data. Multiple tissue and cell types can be selected.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td>
              <span class="multiSelect">
                <a style="float:right; padding-right:20px;">clear</a><br/>
                <select multiple class="form-control" id="ciMapBuildin" name="ciMapBuildin[]" size="10" onchange="CheckAll();">
                    <option value="all">All</option>
				  	<option value="HiC/GSE87112/Adrenal.txt.gz">HiC(GSE87112) Adrenal</option>
					<option value="HiC/GSE87112/Aorta.txt.gz">HiC(GSE87112) Aorta</option>
					<option value="HiC/GSE87112/Bladder.txt.gz">HiC(GSE87112) Bladder</option>
					<option value="HiC/GSE87112/Dorsolateral_Prefrontal_Cortex.txt.gz">HiC(GSE87112) Dorsolateral_Prefrontal_Cortex</option>
					<option value="HiC/GSE87112/Hippocampus.txt.gz">HiC(GSE87112) Hippocampus</option>
					<option value="HiC/GSE87112/Left_Ventricle.txt.gz">HiC(GSE87112) Left_Ventricle</option>
					<option value="HiC/GSE87112/Liver.txt.gz">HiC(GSE87112) Liver</option>
					<option value="HiC/GSE87112/Lung.txt.gz">HiC(GSE87112) Lung</option>
					<option value="HiC/GSE87112/Neural_Progenitor_Cell.txt.gz">HiC(GSE87112) Neural_Progenitor_Cell</option>
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
					<option value="HiC/GSE87112/Trophoblast-like_Cell.txt.gz">HiC(GSE87112) Trophoblast-like_Cell</option>
					<option value="HiC/GSE87112/hESC.txt.gz">HiC(GSE87112) hESC</option>
                </select>
              </span>
            </td>
            <td></td>
          </tr>
		  <tr class="ciMapOptions">
            <td>FDR threshold
              <a class="infoPop" data-toggle="popover" title="FDR threshold for significant interaction" data-content="Significane of interaction for buildin Hi-C datasets are computed by Fit-Hi-C (see tutorial for details). The default threshold is FDR &le; 1e-6 as suggested by Schmit et al. (2016).">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td>
              <span class="form-inline">FDR cutoff (&le;): <input type="number" class="form-control" name="ciMapFDR" id="ciMapFDR" value="1e-6" onchange="CheckAll();"></span>
            </td>
            <td></td>
          </tr>
		  <tr class="ciMapOptions">
			 <td>Enhancers
				  <a class="infoPop" data-toggle="popover" title="Enhancers" data-content="Candidate SNPs which have significant interactions are mapped to enhancer of selected tissue/cell types. 111 epigenomes from Roadmap Epigenetics are available. The list also include dyadic enhancer/promoter regions.">
	                <i class="fa fa-question-circle-o fa-lg"></i>
	              </a>
			  </td>
			  <td>
				  <span class="multiselect">
					  <a style="float:right; padding-right:20px;">clear</a><br/>
					  <select multiple class="form-control" id="ciMapEnhancers" name="ciMapEnhancers[]" size="10" onchange="CheckAll();">
						<option value="all">All</option>
						<option class="level1" value="null">Adrenal (1)</option>
						<option class="level2" value="enh_E080">Enhancer E080 (Other) Fetal Adrenal Gland</option>
						<option class="level2" value="dyadic_E080">Dyadic E080 (Other) Fetal Adrenal Gland</option>
						<option class="level1" value="null">Blood (27)</option>
						<option class="level2" value="enh_E029">Enhancer E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
						<option class="level2" value="dyadic_E029">Dyadic E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
						<option class="level2" value="enh_E030">Enhancer E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
						<option class="level2" value="dyadic_E030">Dyadic E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
						<option class="level2" value="enh_E031">Enhancer E031 (HSC & B-cell) Primary B cells from cord blood</option>
						<option class="level2" value="dyadic_E031">Dyadic E031 (HSC & B-cell) Primary B cells from cord blood</option>
						<option class="level2" value="enh_E032">Enhancer E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
						<option class="level2" value="dyadic_E032">Dyadic E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
						<option class="level2" value="enh_E033">Enhancer E033 (Blood & T-cell) Primary T cells from cord blood</option>
						<option class="level2" value="dyadic_E033">Dyadic E033 (Blood & T-cell) Primary T cells from cord blood</option>
						<option class="level2" value="enh_E034">Enhancer E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
						<option class="level2" value="dyadic_E034">Dyadic E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
						<option class="level2" value="enh_E035">Enhancer E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
						<option class="level2" value="dyadic_E035">Dyadic E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
						<option class="level2" value="enh_E036">Enhancer E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
						<option class="level2" value="dyadic_E036">Dyadic E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
						<option class="level2" value="enh_E037">Enhancer E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
						<option class="level2" value="dyadic_E037">Dyadic E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
						<option class="level2" value="enh_E038">Enhancer E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
						<option class="level2" value="dyadic_E038">Dyadic E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
						<option class="level2" value="enh_E039">Enhancer E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
						<option class="level2" value="dyadic_E039">Dyadic E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
						<option class="level2" value="enh_E040">Enhancer E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
						<option class="level2" value="dyadic_E040">Dyadic E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
						<option class="level2" value="enh_E041">Enhancer E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
						<option class="level2" value="dyadic_E041">Dyadic E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
						<option class="level2" value="enh_E042">Enhancer E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
						<option class="level2" value="dyadic_E042">Dyadic E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
						<option class="level2" value="enh_E043">Enhancer E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
						<option class="level2" value="dyadic_E043">Dyadic E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
						<option class="level2" value="enh_E044">Enhancer E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
						<option class="level2" value="dyadic_E044">Dyadic E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
						<option class="level2" value="enh_E045">Enhancer E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
						<option class="level2" value="dyadic_E045">Dyadic E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
						<option class="level2" value="enh_E046">Enhancer E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
						<option class="level2" value="dyadic_E046">Dyadic E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
						<option class="level2" value="enh_E047">Enhancer E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
						<option class="level2" value="dyadic_E047">Dyadic E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
						<option class="level2" value="enh_E048">Enhancer E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
						<option class="level2" value="dyadic_E048">Dyadic E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
						<option class="level2" value="enh_E050">Enhancer E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
						<option class="level2" value="dyadic_E050">Dyadic E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
						<option class="level2" value="enh_E051">Enhancer E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
						<option class="level2" value="dyadic_E051">Dyadic E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
						<option class="level2" value="enh_E062">Enhancer E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
						<option class="level2" value="dyadic_E062">Dyadic E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
						<option class="level1" value="null">Bone (1)</option>
						<option class="level1" value="null">Brain (13)</option>
						<option class="level2" value="enh_E053">Enhancer E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
						<option class="level2" value="dyadic_E053">Dyadic E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
						<option class="level2" value="enh_E054">Enhancer E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
						<option class="level2" value="dyadic_E054">Dyadic E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
						<option class="level2" value="enh_E067">Enhancer E067 (Brain) Brain Angular Gyrus</option>
						<option class="level2" value="dyadic_E067">Dyadic E067 (Brain) Brain Angular Gyrus</option>
						<option class="level2" value="enh_E068">Enhancer E068 (Brain) Brain Anterior Caudate</option>
						<option class="level2" value="dyadic_E068">Dyadic E068 (Brain) Brain Anterior Caudate</option>
						<option class="level2" value="enh_E069">Enhancer E069 (Brain) Brain Cingulate Gyrus</option>
						<option class="level2" value="dyadic_E069">Dyadic E069 (Brain) Brain Cingulate Gyrus</option>
						<option class="level2" value="enh_E070">Enhancer E070 (Brain) Brain Germinal Matrix</option>
						<option class="level2" value="dyadic_E070">Dyadic E070 (Brain) Brain Germinal Matrix</option>
						<option class="level2" value="enh_E071">Enhancer E071 (Brain) Brain Hippocampus Middle</option>
						<option class="level2" value="dyadic_E071">Dyadic E071 (Brain) Brain Hippocampus Middle</option>
						<option class="level2" value="enh_E072">Enhancer E072 (Brain) Brain Inferior Temporal Lobe</option>
						<option class="level2" value="dyadic_E072">Dyadic E072 (Brain) Brain Inferior Temporal Lobe</option>
						<option class="level2" value="enh_E073">Enhancer E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
						<option class="level2" value="dyadic_E073">Dyadic E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
						<option class="level2" value="enh_E074">Enhancer E074 (Brain) Brain Substantia Nigra</option>
						<option class="level2" value="dyadic_E074">Dyadic E074 (Brain) Brain Substantia Nigra</option>
						<option class="level2" value="enh_E081">Enhancer E081 (Brain) Fetal Brain Male</option>
						<option class="level2" value="dyadic_E081">Dyadic E081 (Brain) Fetal Brain Male</option>
						<option class="level2" value="enh_E082">Enhancer E082 (Brain) Fetal Brain Female</option>
						<option class="level2" value="dyadic_E082">Dyadic E082 (Brain) Fetal Brain Female</option>
						<option class="level1" value="null">Breast (3)</option>
						<option class="level2" value="enh_E027">Enhancer E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
						<option class="level2" value="dyadic_E027">Dyadic E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
						<option class="level2" value="enh_E028">Enhancer E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
						<option class="level2" value="dyadic_E028">Dyadic E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
						<option class="level1" value="null">Cervix (1)</option>
						<option class="level1" value="null">ESC (8)</option>
						<option class="level2" value="enh_E001">Enhancer E001 (ESC) ES-I3 Cells</option>
						<option class="level2" value="dyadic_E001">Dyadic E001 (ESC) ES-I3 Cells</option>
						<option class="level2" value="enh_E002">Enhancer E002 (ESC) ES-WA7 Cells</option>
						<option class="level2" value="dyadic_E002">Dyadic E002 (ESC) ES-WA7 Cells</option>
						<option class="level2" value="enh_E003">Enhancer E003 (ESC) H1 Cells</option>
						<option class="level2" value="dyadic_E003">Dyadic E003 (ESC) H1 Cells</option>
						<option class="level2" value="enh_E008">Enhancer E008 (ESC) H9 Cells</option>
						<option class="level2" value="dyadic_E008">Dyadic E008 (ESC) H9 Cells</option>
						<option class="level2" value="enh_E014">Enhancer E014 (ESC) HUES48 Cells</option>
						<option class="level2" value="dyadic_E014">Dyadic E014 (ESC) HUES48 Cells</option>
						<option class="level2" value="enh_E015">Enhancer E015 (ESC) HUES6 Cells</option>
						<option class="level2" value="dyadic_E015">Dyadic E015 (ESC) HUES6 Cells</option>
						<option class="level2" value="enh_E016">Enhancer E016 (ESC) HUES64 Cells</option>
						<option class="level2" value="dyadic_E016">Dyadic E016 (ESC) HUES64 Cells</option>
						<option class="level2" value="enh_E024">Enhancer E024 (ESC) ES-UCSF4  Cells</option>
						<option class="level2" value="dyadic_E024">Dyadic E024 (ESC) ES-UCSF4  Cells</option>
						<option class="level1" value="null">ESC Derived (9)</option>
						<option class="level2" value="enh_E004">Enhancer E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
						<option class="level2" value="dyadic_E004">Dyadic E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
						<option class="level2" value="enh_E005">Enhancer E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
						<option class="level2" value="dyadic_E005">Dyadic E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
						<option class="level2" value="enh_E006">Enhancer E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
						<option class="level2" value="dyadic_E006">Dyadic E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
						<option class="level2" value="enh_E007">Enhancer E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
						<option class="level2" value="dyadic_E007">Dyadic E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
						<option class="level2" value="enh_E009">Enhancer E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
						<option class="level2" value="dyadic_E009">Dyadic E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
						<option class="level2" value="enh_E010">Enhancer E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
						<option class="level2" value="dyadic_E010">Dyadic E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
						<option class="level2" value="enh_E011">Enhancer E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
						<option class="level2" value="dyadic_E011">Dyadic E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
						<option class="level2" value="enh_E012">Enhancer E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
						<option class="level2" value="dyadic_E012">Dyadic E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
						<option class="level2" value="enh_E013">Enhancer E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
						<option class="level2" value="dyadic_E013">Dyadic E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
						<option class="level1" value="null">Fat (3)</option>
						<option class="level2" value="enh_E023">Enhancer E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
						<option class="level2" value="dyadic_E023">Dyadic E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
						<option class="level2" value="enh_E025">Enhancer E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
						<option class="level2" value="dyadic_E025">Dyadic E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
						<option class="level2" value="enh_E063">Enhancer E063 (Adipose) Adipose Nuclei</option>
						<option class="level2" value="dyadic_E063">Dyadic E063 (Adipose) Adipose Nuclei</option>
						<option class="level1" value="null">GI Colon (3)</option>
						<option class="level2" value="enh_E075">Enhancer E075 (Digestive) Colonic Mucosa</option>
						<option class="level2" value="dyadic_E075">Dyadic E075 (Digestive) Colonic Mucosa</option>
						<option class="level2" value="enh_E076">Enhancer E076 (Sm. Muscle) Colon Smooth Muscle</option>
						<option class="level2" value="dyadic_E076">Dyadic E076 (Sm. Muscle) Colon Smooth Muscle</option>
						<option class="level2" value="enh_E106">Enhancer E106 (Digestive) Sigmoid Colon</option>
						<option class="level2" value="dyadic_E106">Dyadic E106 (Digestive) Sigmoid Colon</option>
						<option class="level1" value="null">GI Duodenum (2)</option>
						<option class="level2" value="enh_E077">Enhancer E077 (Digestive) Duodenum Mucosa</option>
						<option class="level2" value="dyadic_E077">Dyadic E077 (Digestive) Duodenum Mucosa</option>
						<option class="level2" value="enh_E078">Enhancer E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
						<option class="level2" value="dyadic_E078">Dyadic E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
						<option class="level1" value="null">GI Esophagus (1)</option>
						<option class="level2" value="enh_E079">Enhancer E079 (Digestive) Esophagus</option>
						<option class="level2" value="dyadic_E079">Dyadic E079 (Digestive) Esophagus</option>
						<option class="level1" value="null">GI Intestine (3)</option>
						<option class="level2" value="enh_E084">Enhancer E084 (Digestive) Fetal Intestine Large</option>
						<option class="level2" value="dyadic_E084">Dyadic E084 (Digestive) Fetal Intestine Large</option>
						<option class="level2" value="enh_E085">Enhancer E085 (Digestive) Fetal Intestine Small</option>
						<option class="level2" value="dyadic_E085">Dyadic E085 (Digestive) Fetal Intestine Small</option>
						<option class="level2" value="enh_E109">Enhancer E109 (Digestive) Small Intestine</option>
						<option class="level2" value="dyadic_E109">Dyadic E109 (Digestive) Small Intestine</option>
						<option class="level1" value="null">GI Rectum (3)</option>
						<option class="level2" value="enh_E101">Enhancer E101 (Digestive) Rectal Mucosa Donor 29</option>
						<option class="level2" value="dyadic_E101">Dyadic E101 (Digestive) Rectal Mucosa Donor 29</option>
						<option class="level2" value="enh_E102">Enhancer E102 (Digestive) Rectal Mucosa Donor 31</option>
						<option class="level2" value="dyadic_E102">Dyadic E102 (Digestive) Rectal Mucosa Donor 31</option>
						<option class="level2" value="enh_E103">Enhancer E103 (Sm. Muscle) Rectal Smooth Muscle</option>
						<option class="level2" value="dyadic_E103">Dyadic E103 (Sm. Muscle) Rectal Smooth Muscle</option>
						<option class="level1" value="null">GI Stomach (4)</option>
						<option class="level2" value="enh_E092">Enhancer E092 (Digestive) Fetal Stomach</option>
						<option class="level2" value="dyadic_E092">Dyadic E092 (Digestive) Fetal Stomach</option>
						<option class="level2" value="enh_E094">Enhancer E094 (Digestive) Gastric</option>
						<option class="level2" value="dyadic_E094">Dyadic E094 (Digestive) Gastric</option>
						<option class="level2" value="enh_E110">Enhancer E110 (Digestive) Stomach Mucosa</option>
						<option class="level2" value="dyadic_E110">Dyadic E110 (Digestive) Stomach Mucosa</option>
						<option class="level2" value="enh_E111">Enhancer E111 (Sm. Muscle) Stomach Smooth Muscle</option>
						<option class="level2" value="dyadic_E111">Dyadic E111 (Sm. Muscle) Stomach Smooth Muscle</option>
						<option class="level1" value="null">Heart (4)</option>
						<option class="level2" value="enh_E083">Enhancer E083 (Heart) Fetal Heart</option>
						<option class="level2" value="dyadic_E083">Dyadic E083 (Heart) Fetal Heart</option>
						<option class="level2" value="enh_E095">Enhancer E095 (Heart) Left Ventricle</option>
						<option class="level2" value="dyadic_E095">Dyadic E095 (Heart) Left Ventricle</option>
						<option class="level2" value="enh_E104">Enhancer E104 (Heart) Right Atrium</option>
						<option class="level2" value="dyadic_E104">Dyadic E104 (Heart) Right Atrium</option>
						<option class="level2" value="enh_E105">Enhancer E105 (Heart) Right Ventricle</option>
						<option class="level2" value="dyadic_E105">Dyadic E105 (Heart) Right Ventricle</option>
						<option class="level1" value="null">Kidney (1)</option>
						<option class="level2" value="enh_E086">Enhancer E086 (Other) Fetal Kidney</option>
						<option class="level2" value="dyadic_E086">Dyadic E086 (Other) Fetal Kidney</option>
						<option class="level1" value="null">Liver (2)</option>
						<option class="level2" value="enh_E066">Enhancer E066 (Other) Liver</option>
						<option class="level2" value="dyadic_E066">Dyadic E066 (Other) Liver</option>
						<option class="level1" value="null">Lung (5)</option>
						<option class="level2" value="enh_E017">Enhancer E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
						<option class="level2" value="dyadic_E017">Dyadic E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
						<option class="level2" value="enh_E088">Enhancer E088 (Other) Fetal Lung</option>
						<option class="level2" value="dyadic_E088">Dyadic E088 (Other) Fetal Lung</option>
						<option class="level2" value="enh_E096">Enhancer E096 (Other) Lung</option>
						<option class="level2" value="dyadic_E096">Dyadic E096 (Other) Lung</option>
						<option class="level1" value="null">Muscle (7)</option>
						<option class="level2" value="enh_E052">Enhancer E052 (Myosat) Muscle Satellite Cultured Cells</option>
						<option class="level2" value="dyadic_E052">Dyadic E052 (Myosat) Muscle Satellite Cultured Cells</option>
						<option class="level2" value="enh_E089">Enhancer E089 (Muscle) Fetal Muscle Trunk</option>
						<option class="level2" value="dyadic_E089">Dyadic E089 (Muscle) Fetal Muscle Trunk</option>
						<option class="level2" value="enh_E100">Enhancer E100 (Muscle) Psoas Muscle</option>
						<option class="level2" value="dyadic_E100">Dyadic E100 (Muscle) Psoas Muscle</option>
						<option class="level2" value="enh_E107">Enhancer E107 (Muscle) Skeletal Muscle Male</option>
						<option class="level2" value="dyadic_E107">Dyadic E107 (Muscle) Skeletal Muscle Male</option>
						<option class="level2" value="enh_E108">Enhancer E108 (Muscle) Skeletal Muscle Female</option>
						<option class="level2" value="dyadic_E108">Dyadic E108 (Muscle) Skeletal Muscle Female</option>
						<option class="level1" value="null">Muscle Leg (1)</option>
						<option class="level2" value="enh_E090">Enhancer E090 (Muscle) Fetal Muscle Leg</option>
						<option class="level2" value="dyadic_E090">Dyadic E090 (Muscle) Fetal Muscle Leg</option>
						<option class="level1" value="null">Ovary (1)</option>
						<option class="level2" value="enh_E097">Enhancer E097 (Other) Ovary</option>
						<option class="level2" value="dyadic_E097">Dyadic E097 (Other) Ovary</option>
						<option class="level1" value="null">Pancreas (2)</option>
						<option class="level2" value="enh_E087">Enhancer E087 (Other) Pancreatic Islets</option>
						<option class="level2" value="dyadic_E087">Dyadic E087 (Other) Pancreatic Islets</option>
						<option class="level2" value="enh_E098">Enhancer E098 (Other) Pancreas</option>
						<option class="level2" value="dyadic_E098">Dyadic E098 (Other) Pancreas</option>
						<option class="level1" value="null">Placenta (2)</option>
						<option class="level2" value="enh_E091">Enhancer E091 (Other) Placenta</option>
						<option class="level2" value="dyadic_E091">Dyadic E091 (Other) Placenta</option>
						<option class="level2" value="enh_E099">Enhancer E099 (Other) Placenta Amnion</option>
						<option class="level2" value="dyadic_E099">Dyadic E099 (Other) Placenta Amnion</option>
						<option class="level1" value="null">Skin (8)</option>
						<option class="level2" value="enh_E055">Enhancer E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
						<option class="level2" value="dyadic_E055">Dyadic E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
						<option class="level2" value="enh_E056">Enhancer E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
						<option class="level2" value="dyadic_E056">Dyadic E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
						<option class="level2" value="enh_E057">Enhancer E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
						<option class="level2" value="dyadic_E057">Dyadic E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
						<option class="level2" value="enh_E058">Enhancer E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
						<option class="level2" value="dyadic_E058">Dyadic E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
						<option class="level2" value="enh_E059">Enhancer E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
						<option class="level2" value="dyadic_E059">Dyadic E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
						<option class="level2" value="enh_E061">Enhancer E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
						<option class="level2" value="dyadic_E061">Dyadic E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
						<option class="level1" value="null">Spleen (1)</option>
						<option class="level2" value="enh_E113">Enhancer E113 (Other) Spleen</option>
						<option class="level2" value="dyadic_E113">Dyadic E113 (Other) Spleen</option>
						<option class="level1" value="null">Stromal Connective (2)</option>
						<option class="level2" value="enh_E026">Enhancer E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
						<option class="level2" value="dyadic_E026">Dyadic E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
						<option class="level2" value="enh_E049">Enhancer E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
						<option class="level2" value="dyadic_E049">Dyadic E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
						<option class="level1" value="null">Thymus (2)</option>
						<option class="level2" value="enh_E093">Enhancer E093 (Thymus) Fetal Thymus</option>
						<option class="level2" value="dyadic_E093">Dyadic E093 (Thymus) Fetal Thymus</option>
						<option class="level2" value="enh_E112">Enhancer E112 (Thymus) Thymus</option>
						<option class="level2" value="dyadic_E112">Dyadic E112 (Thymus) Thymus</option>
						<option class="level1" value="null">Vascular (2)</option>
						<option class="level2" value="enh_E065">Enhancer E065 (Heart) Aorta</option>
						<option class="level2" value="dyadic_E065">Dyadic E065 (Heart) Aorta</option>
						<option class="level1" value="null">iPSC (5)</option>
						<option class="level2" value="enh_E018">Enhancer E018 (iPSC) iPS-15b Cells</option>
						<option class="level2" value="dyadic_E018">Dyadic E018 (iPSC) iPS-15b Cells</option>
						<option class="level2" value="enh_E019">Enhancer E019 (iPSC) iPS-18 Cells</option>
						<option class="level2" value="dyadic_E019">Dyadic E019 (iPSC) iPS-18 Cells</option>
						<option class="level2" value="enh_E020">Enhancer E020 (iPSC) iPS-20b Cells</option>
						<option class="level2" value="dyadic_E020">Dyadic E020 (iPSC) iPS-20b Cells</option>
						<option class="level2" value="enh_E021">Enhancer E021 (iPSC) iPS DF 6.9 Cells</option>
						<option class="level2" value="dyadic_E021">Dyadic E021 (iPSC) iPS DF 6.9 Cells</option>
						<option class="level2" value="enh_E022">Enhancer E022 (iPSC) iPS DF 19.11 Cells</option>
						<option class="level2" value="dyadic_E022">Dyadic E022 (iPSC) iPS DF 19.11 Cells</option>
					  </select>
				  </span>
			  </td>
			  <td>
			  </td>
		  </tr>
		  <tr class="ciMapOptions">
			  <td>Promoters
				  <a class="infoPop" data-toggle="popover" title="Promoters" data-content="Genomic regions which have sinificant interactions with candidate SNPs  are mapped to promoters of selected tissue/cell types. 111 epigenomes from Roadmap Epigenetics are available. The list also include dyadic enhancer/promoter regions.">
	                <i class="fa fa-question-circle-o fa-lg"></i>
	              </a>
			  </td>
			  <td>
				  <span class="multiselect">
					  <a style="float:right; padding-right:20px;">clear</a><br/>
					  <select multiple class="form-control" id="ciMapPromoters" name="ciMapPromoters[]" size="10" onchange="CheckAll();">
						<option value="all">All</option>
						<option class="level1" value="null">Adrenal (1)</option>
						<option class="level2" value="prom_E080">Promoter E080 (Other) Fetal Adrenal Gland</option>
						<option class="level2" value="dyadic_E080"> Dyadic E080 (Other) Fetal Adrenal Gland</option>
						<option class="level1" value="null">Blood (27)</option>
						<option class="level2" value="prom_E029">Promoter E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
						<option class="level2" value="dyadic_E029"> Dyadic E029 (HSC & B-cell) Primary monocytes from peripheral blood</option>
						<option class="level2" value="prom_E030">Promoter E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
						<option class="level2" value="dyadic_E030"> Dyadic E030 (HSC & B-cell) Primary neutrophils from peripheral blood</option>
						<option class="level2" value="prom_E031">Promoter E031 (HSC & B-cell) Primary B cells from cord blood</option>
						<option class="level2" value="dyadic_E031"> Dyadic E031 (HSC & B-cell) Primary B cells from cord blood</option>
						<option class="level2" value="prom_E032">Promoter E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
						<option class="level2" value="dyadic_E032"> Dyadic E032 (HSC & B-cell) Primary B cells from peripheral blood</option>
						<option class="level2" value="prom_E033">Promoter E033 (Blood & T-cell) Primary T cells from cord blood</option>
						<option class="level2" value="dyadic_E033"> Dyadic E033 (Blood & T-cell) Primary T cells from cord blood</option>
						<option class="level2" value="prom_E034">Promoter E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
						<option class="level2" value="dyadic_E034"> Dyadic E034 (Blood & T-cell) Primary T cells from peripheral blood</option>
						<option class="level2" value="prom_E035">Promoter E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
						<option class="level2" value="dyadic_E035"> Dyadic E035 (HSC & B-cell) Primary hematopoietic stem cells</option>
						<option class="level2" value="prom_E036">Promoter E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
						<option class="level2" value="dyadic_E036"> Dyadic E036 (HSC & B-cell) Primary hematopoietic stem cells short term culture</option>
						<option class="level2" value="prom_E037">Promoter E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
						<option class="level2" value="dyadic_E037"> Dyadic E037 (Blood & T-cell) Primary T helper memory cells from peripheral blood 2</option>
						<option class="level2" value="prom_E038">Promoter E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
						<option class="level2" value="dyadic_E038"> Dyadic E038 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
						<option class="level2" value="prom_E039">Promoter E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
						<option class="level2" value="dyadic_E039"> Dyadic E039 (Blood & T-cell) Primary T helper naive cells from peripheral blood</option>
						<option class="level2" value="prom_E040">Promoter E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
						<option class="level2" value="dyadic_E040"> Dyadic E040 (Blood & T-cell) Primary T helper memory cells from peripheral blood 1</option>
						<option class="level2" value="prom_E041">Promoter E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
						<option class="level2" value="dyadic_E041"> Dyadic E041 (Blood & T-cell) Primary T helper cells PMA-I stimulated</option>
						<option class="level2" value="prom_E042">Promoter E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
						<option class="level2" value="dyadic_E042"> Dyadic E042 (Blood & T-cell) Primary T helper 17 cells PMA-I stimulated</option>
						<option class="level2" value="prom_E043">Promoter E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
						<option class="level2" value="dyadic_E043"> Dyadic E043 (Blood & T-cell) Primary T helper cells from peripheral blood</option>
						<option class="level2" value="prom_E044">Promoter E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
						<option class="level2" value="dyadic_E044"> Dyadic E044 (Blood & T-cell) Primary T regulatory cells from peripheral blood</option>
						<option class="level2" value="prom_E045">Promoter E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
						<option class="level2" value="dyadic_E045"> Dyadic E045 (Blood & T-cell) Primary T cells effector/memory enriched from peripheral blood</option>
						<option class="level2" value="prom_E046">Promoter E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
						<option class="level2" value="dyadic_E046"> Dyadic E046 (HSC & B-cell) Primary Natural Killer cells from peripheral blood</option>
						<option class="level2" value="prom_E047">Promoter E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
						<option class="level2" value="dyadic_E047"> Dyadic E047 (Blood & T-cell) Primary T CD8+ naive cells from peripheral blood</option>
						<option class="level2" value="prom_E048">Promoter E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
						<option class="level2" value="dyadic_E048"> Dyadic E048 (Blood & T-cell) Primary T CD8+ memory cells from peripheral blood</option>
						<option class="level2" value="prom_E050">Promoter E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
						<option class="level2" value="dyadic_E050"> Dyadic E050 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Female</option>
						<option class="level2" value="prom_E051">Promoter E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
						<option class="level2" value="dyadic_E051"> Dyadic E051 (HSC & B-cell) Primary hematopoietic stem cells G-CSF-mobilized Male</option>
						<option class="level2" value="prom_E062">Promoter E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
						<option class="level2" value="dyadic_E062"> Dyadic E062 (Blood & T-cell) Primary mononuclear cells from peripheral blood</option>
						<option class="level1" value="null">Bone (1)</option>
						<option class="level1" value="null">Brain (13)</option>
						<option class="level2" value="prom_E053">Promoter E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
						<option class="level2" value="dyadic_E053"> Dyadic E053 (Neurosph) Cortex derived primary cultured neurospheres</option>
						<option class="level2" value="prom_E054">Promoter E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
						<option class="level2" value="dyadic_E054"> Dyadic E054 (Neurosph) Ganglion Eminence derived primary cultured neurospheres</option>
						<option class="level2" value="prom_E067">Promoter E067 (Brain) Brain Angular Gyrus</option>
						<option class="level2" value="dyadic_E067"> Dyadic E067 (Brain) Brain Angular Gyrus</option>
						<option class="level2" value="prom_E068">Promoter E068 (Brain) Brain Anterior Caudate</option>
						<option class="level2" value="dyadic_E068"> Dyadic E068 (Brain) Brain Anterior Caudate</option>
						<option class="level2" value="prom_E069">Promoter E069 (Brain) Brain Cingulate Gyrus</option>
						<option class="level2" value="dyadic_E069"> Dyadic E069 (Brain) Brain Cingulate Gyrus</option>
						<option class="level2" value="prom_E070">Promoter E070 (Brain) Brain Germinal Matrix</option>
						<option class="level2" value="dyadic_E070"> Dyadic E070 (Brain) Brain Germinal Matrix</option>
						<option class="level2" value="prom_E071">Promoter E071 (Brain) Brain Hippocampus Middle</option>
						<option class="level2" value="dyadic_E071"> Dyadic E071 (Brain) Brain Hippocampus Middle</option>
						<option class="level2" value="prom_E072">Promoter E072 (Brain) Brain Inferior Temporal Lobe</option>
						<option class="level2" value="dyadic_E072"> Dyadic E072 (Brain) Brain Inferior Temporal Lobe</option>
						<option class="level2" value="prom_E073">Promoter E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
						<option class="level2" value="dyadic_E073"> Dyadic E073 (Brain) Brain Dorsolateral Prefrontal Cortex</option>
						<option class="level2" value="prom_E074">Promoter E074 (Brain) Brain Substantia Nigra</option>
						<option class="level2" value="dyadic_E074"> Dyadic E074 (Brain) Brain Substantia Nigra</option>
						<option class="level2" value="prom_E081">Promoter E081 (Brain) Fetal Brain Male</option>
						<option class="level2" value="dyadic_E081"> Dyadic E081 (Brain) Fetal Brain Male</option>
						<option class="level2" value="prom_E082">Promoter E082 (Brain) Fetal Brain Female</option>
						<option class="level2" value="dyadic_E082"> Dyadic E082 (Brain) Fetal Brain Female</option>
						<option class="level1" value="null">Breast (3)</option>
						<option class="level2" value="prom_E027">Promoter E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
						<option class="level2" value="dyadic_E027"> Dyadic E027 (Epithelial) Breast Myoepithelial Primary Cells</option>
						<option class="level2" value="prom_E028">Promoter E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
						<option class="level2" value="dyadic_E028"> Dyadic E028 (Epithelial) Breast variant Human Mammary Epithelial Cells (vHMEC)</option>
						<option class="level1" value="null">Cervix (1)</option>
						<option class="level1" value="null">ESC (8)</option>
						<option class="level2" value="prom_E001">Promoter E001 (ESC) ES-I3 Cells</option>
						<option class="level2" value="dyadic_E001"> Dyadic E001 (ESC) ES-I3 Cells</option>
						<option class="level2" value="prom_E002">Promoter E002 (ESC) ES-WA7 Cells</option>
						<option class="level2" value="dyadic_E002"> Dyadic E002 (ESC) ES-WA7 Cells</option>
						<option class="level2" value="prom_E003">Promoter E003 (ESC) H1 Cells</option>
						<option class="level2" value="dyadic_E003"> Dyadic E003 (ESC) H1 Cells</option>
						<option class="level2" value="prom_E008">Promoter E008 (ESC) H9 Cells</option>
						<option class="level2" value="dyadic_E008"> Dyadic E008 (ESC) H9 Cells</option>
						<option class="level2" value="prom_E014">Promoter E014 (ESC) HUES48 Cells</option>
						<option class="level2" value="dyadic_E014"> Dyadic E014 (ESC) HUES48 Cells</option>
						<option class="level2" value="prom_E015">Promoter E015 (ESC) HUES6 Cells</option>
						<option class="level2" value="dyadic_E015"> Dyadic E015 (ESC) HUES6 Cells</option>
						<option class="level2" value="prom_E016">Promoter E016 (ESC) HUES64 Cells</option>
						<option class="level2" value="dyadic_E016"> Dyadic E016 (ESC) HUES64 Cells</option>
						<option class="level2" value="prom_E024">Promoter E024 (ESC) ES-UCSF4  Cells</option>
						<option class="level2" value="dyadic_E024"> Dyadic E024 (ESC) ES-UCSF4  Cells</option>
						<option class="level1" value="null">ESC Derived (9)</option>
						<option class="level2" value="prom_E004">Promoter E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
						<option class="level2" value="dyadic_E004"> Dyadic E004 (ES-deriv) H1 BMP4 Derived Mesendoderm Cultured Cells</option>
						<option class="level2" value="prom_E005">Promoter E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
						<option class="level2" value="dyadic_E005"> Dyadic E005 (ES-deriv) H1 BMP4 Derived Trophoblast Cultured Cells</option>
						<option class="level2" value="prom_E006">Promoter E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
						<option class="level2" value="dyadic_E006"> Dyadic E006 (ES-deriv) H1 Derived Mesenchymal Stem Cells</option>
						<option class="level2" value="prom_E007">Promoter E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
						<option class="level2" value="dyadic_E007"> Dyadic E007 (ES-deriv) H1 Derived Neuronal Progenitor Cultured Cells</option>
						<option class="level2" value="prom_E009">Promoter E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
						<option class="level2" value="dyadic_E009"> Dyadic E009 (ES-deriv) H9 Derived Neuronal Progenitor Cultured Cells</option>
						<option class="level2" value="prom_E010">Promoter E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
						<option class="level2" value="dyadic_E010"> Dyadic E010 (ES-deriv) H9 Derived Neuron Cultured Cells</option>
						<option class="level2" value="prom_E011">Promoter E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
						<option class="level2" value="dyadic_E011"> Dyadic E011 (ES-deriv) hESC Derived CD184+ Endoderm Cultured Cells</option>
						<option class="level2" value="prom_E012">Promoter E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
						<option class="level2" value="dyadic_E012"> Dyadic E012 (ES-deriv) hESC Derived CD56+ Ectoderm Cultured Cells</option>
						<option class="level2" value="prom_E013">Promoter E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
						<option class="level2" value="dyadic_E013"> Dyadic E013 (ES-deriv) hESC Derived CD56+ Mesoderm Cultured Cells</option>
						<option class="level1" value="null">Fat (3)</option>
						<option class="level2" value="prom_E023">Promoter E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
						<option class="level2" value="dyadic_E023"> Dyadic E023 (Mesench) Mesenchymal Stem Cell Derived Adipocyte Cultured Cells</option>
						<option class="level2" value="prom_E025">Promoter E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
						<option class="level2" value="dyadic_E025"> Dyadic E025 (Mesench) Adipose Derived Mesenchymal Stem Cell Cultured Cells</option>
						<option class="level2" value="prom_E063">Promoter E063 (Adipose) Adipose Nuclei</option>
						<option class="level2" value="dyadic_E063"> Dyadic E063 (Adipose) Adipose Nuclei</option>
						<option class="level1" value="null">GI Colon (3)</option>
						<option class="level2" value="prom_E075">Promoter E075 (Digestive) Colonic Mucosa</option>
						<option class="level2" value="dyadic_E075"> Dyadic E075 (Digestive) Colonic Mucosa</option>
						<option class="level2" value="prom_E076">Promoter E076 (Sm. Muscle) Colon Smooth Muscle</option>
						<option class="level2" value="dyadic_E076"> Dyadic E076 (Sm. Muscle) Colon Smooth Muscle</option>
						<option class="level2" value="prom_E106">Promoter E106 (Digestive) Sigmoid Colon</option>
						<option class="level2" value="dyadic_E106"> Dyadic E106 (Digestive) Sigmoid Colon</option>
						<option class="level1" value="null">GI Duodenum (2)</option>
						<option class="level2" value="prom_E077">Promoter E077 (Digestive) Duodenum Mucosa</option>
						<option class="level2" value="dyadic_E077"> Dyadic E077 (Digestive) Duodenum Mucosa</option>
						<option class="level2" value="prom_E078">Promoter E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
						<option class="level2" value="dyadic_E078"> Dyadic E078 (Sm. Muscle) Duodenum Smooth Muscle</option>
						<option class="level1" value="null">GI Esophagus (1)</option>
						<option class="level2" value="prom_E079">Promoter E079 (Digestive) Esophagus</option>
						<option class="level2" value="dyadic_E079"> Dyadic E079 (Digestive) Esophagus</option>
						<option class="level1" value="null">GI Intestine (3)</option>
						<option class="level2" value="prom_E084">Promoter E084 (Digestive) Fetal Intestine Large</option>
						<option class="level2" value="dyadic_E084"> Dyadic E084 (Digestive) Fetal Intestine Large</option>
						<option class="level2" value="prom_E085">Promoter E085 (Digestive) Fetal Intestine Small</option>
						<option class="level2" value="dyadic_E085"> Dyadic E085 (Digestive) Fetal Intestine Small</option>
						<option class="level2" value="prom_E109">Promoter E109 (Digestive) Small Intestine</option>
						<option class="level2" value="dyadic_E109"> Dyadic E109 (Digestive) Small Intestine</option>
						<option class="level1" value="null">GI Rectum (3)</option>
						<option class="level2" value="prom_E101">Promoter E101 (Digestive) Rectal Mucosa Donor 29</option>
						<option class="level2" value="dyadic_E101"> Dyadic E101 (Digestive) Rectal Mucosa Donor 29</option>
						<option class="level2" value="prom_E102">Promoter E102 (Digestive) Rectal Mucosa Donor 31</option>
						<option class="level2" value="dyadic_E102"> Dyadic E102 (Digestive) Rectal Mucosa Donor 31</option>
						<option class="level2" value="prom_E103">Promoter E103 (Sm. Muscle) Rectal Smooth Muscle</option>
						<option class="level2" value="dyadic_E103"> Dyadic E103 (Sm. Muscle) Rectal Smooth Muscle</option>
						<option class="level1" value="null">GI Stomach (4)</option>
						<option class="level2" value="prom_E092">Promoter E092 (Digestive) Fetal Stomach</option>
						<option class="level2" value="dyadic_E092"> Dyadic E092 (Digestive) Fetal Stomach</option>
						<option class="level2" value="prom_E094">Promoter E094 (Digestive) Gastric</option>
						<option class="level2" value="dyadic_E094"> Dyadic E094 (Digestive) Gastric</option>
						<option class="level2" value="prom_E110">Promoter E110 (Digestive) Stomach Mucosa</option>
						<option class="level2" value="dyadic_E110"> Dyadic E110 (Digestive) Stomach Mucosa</option>
						<option class="level2" value="prom_E111">Promoter E111 (Sm. Muscle) Stomach Smooth Muscle</option>
						<option class="level2" value="dyadic_E111"> Dyadic E111 (Sm. Muscle) Stomach Smooth Muscle</option>
						<option class="level1" value="null">Heart (4)</option>
						<option class="level2" value="prom_E083">Promoter E083 (Heart) Fetal Heart</option>
						<option class="level2" value="dyadic_E083"> Dyadic E083 (Heart) Fetal Heart</option>
						<option class="level2" value="prom_E095">Promoter E095 (Heart) Left Ventricle</option>
						<option class="level2" value="dyadic_E095"> Dyadic E095 (Heart) Left Ventricle</option>
						<option class="level2" value="prom_E104">Promoter E104 (Heart) Right Atrium</option>
						<option class="level2" value="dyadic_E104"> Dyadic E104 (Heart) Right Atrium</option>
						<option class="level2" value="prom_E105">Promoter E105 (Heart) Right Ventricle</option>
						<option class="level2" value="dyadic_E105"> Dyadic E105 (Heart) Right Ventricle</option>
						<option class="level1" value="null">Kidney (1)</option>
						<option class="level2" value="prom_E086">Promoter E086 (Other) Fetal Kidney</option>
						<option class="level2" value="dyadic_E086"> Dyadic E086 (Other) Fetal Kidney</option>
						<option class="level1" value="null">Liver (2)</option>
						<option class="level2" value="prom_E066">Promoter E066 (Other) Liver</option>
						<option class="level2" value="dyadic_E066"> Dyadic E066 (Other) Liver</option>
						<option class="level1" value="null">Lung (5)</option>
						<option class="level2" value="prom_E017">Promoter E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
						<option class="level2" value="dyadic_E017"> Dyadic E017 (IMR90) IMR90 fetal lung fibroblasts Cell Line</option>
						<option class="level2" value="prom_E088">Promoter E088 (Other) Fetal Lung</option>
						<option class="level2" value="dyadic_E088"> Dyadic E088 (Other) Fetal Lung</option>
						<option class="level2" value="prom_E096">Promoter E096 (Other) Lung</option>
						<option class="level2" value="dyadic_E096"> Dyadic E096 (Other) Lung</option>
						<option class="level1" value="null">Muscle (7)</option>
						<option class="level2" value="prom_E052">Promoter E052 (Myosat) Muscle Satellite Cultured Cells</option>
						<option class="level2" value="dyadic_E052"> Dyadic E052 (Myosat) Muscle Satellite Cultured Cells</option>
						<option class="level2" value="prom_E089">Promoter E089 (Muscle) Fetal Muscle Trunk</option>
						<option class="level2" value="dyadic_E089"> Dyadic E089 (Muscle) Fetal Muscle Trunk</option>
						<option class="level2" value="prom_E100">Promoter E100 (Muscle) Psoas Muscle</option>
						<option class="level2" value="dyadic_E100"> Dyadic E100 (Muscle) Psoas Muscle</option>
						<option class="level2" value="prom_E107">Promoter E107 (Muscle) Skeletal Muscle Male</option>
						<option class="level2" value="dyadic_E107"> Dyadic E107 (Muscle) Skeletal Muscle Male</option>
						<option class="level2" value="prom_E108">Promoter E108 (Muscle) Skeletal Muscle Female</option>
						<option class="level2" value="dyadic_E108"> Dyadic E108 (Muscle) Skeletal Muscle Female</option>
						<option class="level1" value="null">Muscle Leg (1)</option>
						<option class="level2" value="prom_E090">Promoter E090 (Muscle) Fetal Muscle Leg</option>
						<option class="level2" value="dyadic_E090"> Dyadic E090 (Muscle) Fetal Muscle Leg</option>
						<option class="level1" value="null">Ovary (1)</option>
						<option class="level2" value="prom_E097">Promoter E097 (Other) Ovary</option>
						<option class="level2" value="dyadic_E097"> Dyadic E097 (Other) Ovary</option>
						<option class="level1" value="null">Pancreas (2)</option>
						<option class="level2" value="prom_E087">Promoter E087 (Other) Pancreatic Islets</option>
						<option class="level2" value="dyadic_E087"> Dyadic E087 (Other) Pancreatic Islets</option>
						<option class="level2" value="prom_E098">Promoter E098 (Other) Pancreas</option>
						<option class="level2" value="dyadic_E098"> Dyadic E098 (Other) Pancreas</option>
						<option class="level1" value="null">Placenta (2)</option>
						<option class="level2" value="prom_E091">Promoter E091 (Other) Placenta</option>
						<option class="level2" value="dyadic_E091"> Dyadic E091 (Other) Placenta</option>
						<option class="level2" value="prom_E099">Promoter E099 (Other) Placenta Amnion</option>
						<option class="level2" value="dyadic_E099"> Dyadic E099 (Other) Placenta Amnion</option>
						<option class="level1" value="null">Skin (8)</option>
						<option class="level2" value="prom_E055">Promoter E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
						<option class="level2" value="dyadic_E055"> Dyadic E055 (Epithelial) Foreskin Fibroblast Primary Cells skin01</option>
						<option class="level2" value="prom_E056">Promoter E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
						<option class="level2" value="dyadic_E056"> Dyadic E056 (Epithelial) Foreskin Fibroblast Primary Cells skin02</option>
						<option class="level2" value="prom_E057">Promoter E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
						<option class="level2" value="dyadic_E057"> Dyadic E057 (Epithelial) Foreskin Keratinocyte Primary Cells skin02</option>
						<option class="level2" value="prom_E058">Promoter E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
						<option class="level2" value="dyadic_E058"> Dyadic E058 (Epithelial) Foreskin Keratinocyte Primary Cells skin03</option>
						<option class="level2" value="prom_E059">Promoter E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
						<option class="level2" value="dyadic_E059"> Dyadic E059 (Epithelial) Foreskin Melanocyte Primary Cells skin01</option>
						<option class="level2" value="prom_E061">Promoter E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
						<option class="level2" value="dyadic_E061"> Dyadic E061 (Epithelial) Foreskin Melanocyte Primary Cells skin03</option>
						<option class="level1" value="null">Spleen (1)</option>
						<option class="level2" value="prom_E113">Promoter E113 (Other) Spleen</option>
						<option class="level2" value="dyadic_E113"> Dyadic E113 (Other) Spleen</option>
						<option class="level1" value="null">Stromal Connective (2)</option>
						<option class="level2" value="prom_E026">Promoter E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
						<option class="level2" value="dyadic_E026"> Dyadic E026 (Mesench) Bone Marrow Derived Cultured Mesenchymal Stem Cells</option>
						<option class="level2" value="prom_E049">Promoter E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
						<option class="level2" value="dyadic_E049"> Dyadic E049 (Mesench) Mesenchymal Stem Cell Derived Chondrocyte Cultured Cells</option>
						<option class="level1" value="null">Thymus (2)</option>
						<option class="level2" value="prom_E093">Promoter E093 (Thymus) Fetal Thymus</option>
						<option class="level2" value="dyadic_E093"> Dyadic E093 (Thymus) Fetal Thymus</option>
						<option class="level2" value="prom_E112">Promoter E112 (Thymus) Thymus</option>
						<option class="level2" value="dyadic_E112"> Dyadic E112 (Thymus) Thymus</option>
						<option class="level1" value="null">Vascular (2)</option>
						<option class="level2" value="prom_E065">Promoter E065 (Heart) Aorta</option>
						<option class="level2" value="dyadic_E065"> Dyadic E065 (Heart) Aorta</option>
						<option class="level1" value="null">iPSC (5)</option>
						<option class="level2" value="prom_E018">Promoter E018 (iPSC) iPS-15b Cells</option>
						<option class="level2" value="dyadic_E018"> Dyadic E018 (iPSC) iPS-15b Cells</option>
						<option class="level2" value="prom_E019">Promoter E019 (iPSC) iPS-18 Cells</option>
						<option class="level2" value="dyadic_E019"> Dyadic E019 (iPSC) iPS-18 Cells</option>
						<option class="level2" value="prom_E020">Promoter E020 (iPSC) iPS-20b Cells</option>
						<option class="level2" value="dyadic_E020"> Dyadic E020 (iPSC) iPS-20b Cells</option>
						<option class="level2" value="prom_E021">Promoter E021 (iPSC) iPS DF 6.9 Cells</option>
						<option class="level2" value="dyadic_E021"> Dyadic E021 (iPSC) iPS DF 6.9 Cells</option>
						<option class="level2" value="prom_E022">Promoter E022 (iPSC) iPS DF 19.11 Cells</option>
						<option class="level2" value="dyadic_E022"> Dyadic E022 (iPSC) iPS DF 19.11 Cells</option>
					  </select>
				  </span>
			  </td>
			  <td>
			  </td>
		  </tr>

        <!-- </div> -->
      </table>

      <div id="ciMapOptFilt">
        Optional SNP filtering by functional annotation for eQTL mapping<br/>
        <span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by eQTL mapping criterion.<br/>
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
            <td><input type="checkbox" class="form-check-input" name="ciMapCADDcheck" id="ciMapCADDcheck" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Minimum CADD score (&ge;)
              <a class="infoPop" data-toggle="popover" title="CADD score" data-content="CADD score is the score of deleteriousness of SNPs. The heigher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="number" class="form-control" id="ciMapCADDth" name="ciMapCADDth" value="12.37" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td rowspan="2">RegulomeDB</td>
            <td>Perform SNPs filtering baed on ReguomeDB score
              <a class="infoPop" data-toggle="popover" title="RegulomeDB Score filtering" data-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td><input type="checkbox" class="form-check-input" name="ciMapRDBcheck" id="ciMapRDBcheck" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Maximum RegulomeDB score (categorical)
              <a class="infoPop" data-toggle="popover" title="RegulomeDB score" data-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least liekly. Some SNPs have 'NA' which are not assigned any score.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td>
              <!-- <input type="text" class="form-control" id="ciMapRDBth" name="ciMapRDBth" value="7"> -->
              <select class="form-control" id="ciMapRDBth" name="ciMapRDBth" onchange="CheckAll();">
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
            <td><input type="checkbox" class="form-check-input" name="ciMapChr15check" id="ciMapChr15check" onchange="CheckAll();"></td>
            <td></td>
          </tr>
          <tr>
            <td>Tissue/cell types for 15-core chromatin state<br/>
              <span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be selected.</span>
            </td>
            <td>
              <span class="multiSelect">
                <a style="float:right; padding-right:20px;">clear</a><br/>
                <select multiple class="form-control" size="10" id="ciMapChr15Ts" name="ciMapChr15Ts[]" onchange="CheckAll();">
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
            <td><input type="number" class="form-control" id="ciMapChr15Max" name="ciMapChr15Max" value="7" onkeyup="CheckAll();" onpaste="CheckAll();" oninput="CheckAll();"/></td>
            <td></td>
          </tr>
          <tr>
            <td>15-core chromatin state filtering method
              <a class="infoPop" data-toggle="popover" title="Filtering method for chromatin state" data-content="When multiple tissye/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
                <i class="fa fa-question-circle-o fa-lg"></i>
              </a>
            </td>
            <td>
              <select  class="form-control" id="ciMapChr15Meth" name="ciMapChr15Meth" onchange="CheckAll();">
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
          <td>
			  <span class="form-inline">
				  <input type="checkbox" class="form-check-input" name="MHCregion" id="MHCregion" value="exMHC" checked onchange="CheckAll();">
				  <select class="form-control" id="MHCopt" name="MHCopt" onchange="CheckAll();">
					  <option value="all">from all (annotations and MAGMA)</option>
					  <option selected value="annot">from only annotations</option>
					  <option value="magma">from only MAGMA</option>
				  </select>
			  </span>
		  </td>
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

  <span class="form-inline">
    <span style="font-size:18px;">Title of job submission</span>:
    <input type="text" class="form-control" name="NewJobTitle" id="NewJobTitle"/><br/>
    <span class="info"><i class="fa fa-info"></i>
      This is not mandatory, but job title might help you to track your jobs.
    </span>
  </span><br/><br/>

  <input class="btn" type="submit" value="Submit Job" name="SubmitNewJob" id="SubmitNewJob"/>
  <span style="color: red; font-size:18px;">
    <i class="fa fa-exclamation-triangle"></i> After submitting, please wait a couple of seconds until the file is uploaded, and do not move away from the submission page.
  </span>
  {!! Form::close() !!}
</div>
