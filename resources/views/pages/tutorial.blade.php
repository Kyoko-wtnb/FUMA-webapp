@extends('layouts.master')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#overview').show();
  });

  // function onScroll(event){
  //   var scrollPos = $(document).scrollTop();
  //   $('#sidebar.nav li a').each(function(){
  //     var curLink = $(this);
  //     var refElement = $(curLink.attr("href"));
  //     if (refElement.position().top <= scrollPos && refElement.position().top + refElement.height() > scrollPos){
  //       $('#sidebar.nav').find(".active").removeClass("active");
  //       $(this).parent().addClass("active");
  //     }
  //   });
  // }
</script>
@section('content')
<div id="wrapper" class="active">
<div id="sidebar-wrapper">
  <ul class="sidebar-nav" id="sidebar-menu">
    <li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
  </ul>
  <ul class="sidebar-nav" id="sidebar">
    <li class="active"><a href="#overview">Overview<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
    <li><a href="#quick-start">Quick Start<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
      <div class="subside" id="quick-startsub">
        <a href="#getCandidate">Get candiates</a>
        <a href="#geneQuery">Qury gene functions</a>
      </div>
    <li><a href="#snp2gene">SNP2GENE<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
      <div class="subside" id="snp2genesub">
        <a href="#prepare-input-files">Input files</a>
        <a href="#parameters">Parameters</a>
        <a href="#submit-job">Subit your job</a>
        <a href="#outputs">Outputs</a>
        <a href="#examples">Example senarios</a>
      </div>
    <li><a href="#gene2func">GENE2FUNC<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
      <div class="subside" id="gene2funcsub">
        <a href="#submit-genes">Submit your genes</a>
        <a href="#gene2funcOutputs">Outputs</a>
      </div>
  </ul>
</div>


<!-- <div class="row"> -->
  <!-- <div class="col-md-2" id="leftCol">
    <ul class="nav nav-stacked" id="sidebar">
      <li class="active"><a href="#overview">Overview</a></li>
      <li><a href="#quick-start">Quick Start</a></li>
      <li><a href="#snp2gene">SNP2GENE</a></li>
      <ul class="nav nav-stacked" id="snp2geneSub">
        <li><a href="#prepare-input-files">Prepare Input Files</a></li>
        <li><a href="#parameters">Parameters</a></li>
        <li><a href="#submit-job">Submit a New Job</a></li>
        <li><a href="#outputs">Outputs</a></li>
      </ul>
      <li><a href="#gene2func">GENE2FUNC</a></li>
    </ul>
  </div> -->

  <!-- <div class="col-md-10"> -->
<div id="page-content-wrapper">
  <div class="page-content inset">
    <div id="test"></div>
    <div id="overview" class="sidePanel container" style="padding-top:50;">
      <h2 >Tutorial</h2>
      <p>Please read this tutorial carefully to use IPGAP.
        There are various parameters provided by this pipeline.
        Those will be explained in detail. To start using right away, follow the quick start,
        then you will know a minimum knowledge how to use this pipeline.</p>
      <p>For detail methods, please refer the publication.</p>
      <p>If you have any question or suggestion, please do not hesitate to contact us!!</p>
      <br/>

      <h2>Overview of the IPGAP</h2>
      <div style="margin-left: 40px;">
        <p>The platform mainly consists of two separate process, SNP2GENE and GENE2FUNC.</p>
        <p>To annotate and obtain candidates from your GWAS summary statistics, go to <strong>SNP2GENE</strong> which compute LD structure,
          annotate SNPs, and prioritize candidate genes.</p>
        <p>If you already have a list of genes, go to <strong>GENE2FUNC</strong> to check expressiion pattern and shared molecular functions.<p/>
        <br/>
        <img src="{{ URL::asset('/image/pipeline.png') }}" width="500" height="730" align="middle">
      </div>
    </div>

    <div id="quick-start" class="sidePanel container" style="padding-top:50;">
      <h2>Quick Start</h2>
      <p>In this page, we quickly go through what you can do and what you can get from the IPGAP.
        You will get a minimum knowledge of the IPGAP and will be able to start using.
        Whenever you get questions, please go back to tutorial for detail explanations.
        If you cannot find out your question, we are welcome you to send us email.
      </p>
      <div style="margin-left: 40px;">
        <h3 id="getCandidate">Get candidates from your own GWAS summary statistics</h3>
        <p>You can obtain functional annotation of SNPs and map them to genes.
          By setting parameter, you are also able to prioritize genes by your criterion.</p>
        <div style="margin-left: 40px">
          <p>1. Go to <code>SNP2GENE</code> and upload GWAS summary statistics file.<br/>
            <tab>Avariety of input formats are supported. Please refer tutorial of <a href="#snp2gene">SNP2GENE</a> for details.
            If you are not sure, chose <code>Plain text</code> which fits most of the headers.<br/>
            Optionally, if you already know lead SNPs and you want to use them as lead SNPs, you can upload a file with 3 column; rsID, chromosome and position.<br/>
            In addition, if you are interested in specific genomic regions, you can also provide them by uploading file with 3 columns; chromosome, start and end position.<br/>
          </p>

          <p>2. Set parameters.<br/>
            <tab>Please check your parameter carefully. Default setting perform identification of lead SNPs at r2=0.6 and maps SNPs to genes up to 10kb apart.<br/>
            To filter SNPs by functional annotations and use eQTL mapping, please refer parmeter section under <a href="#snp2gene">SNP2GENE</a>.</p>
          </p>

          <p>3. Wait until you get a email.<br/>
            <tab>Once job is submitted, you will receive one email for "Job submission" and another one for "Job completion".
            You can stay at the page which shows loading page and will redirect to results page automatically, but you can query your job later on.
            Once you get a "Job completer" email, you are redy to query your results.
          </p>

          <p>4. Check your results.<br/>
            <tab>Go back to <code>SNP2GENE</code> and enter you email address and job title.
            This will query your job and show all results.<br/>
            In the results page, you can brows your results, download them, and create regionsl plots.
          </p>
        </div>
        <br/>
        <h3 id="geneQuery">Identify tissue specificity and shared biological functions of a list of genes</h3>

        <br/>
      </div>
    </div>

    <div id="snp2gene" class="sidePanel" style="padding-top:50;">
      <h2>SNP2GENE</h2>
      <div style="margin-left: 40px;">
        <h3 id="prepare-input-files">Prepare Input Files</h3>
        <p>GWAS summary statistics is a mandatory input of <code>SNP2GENE</code> process. IPGAP accept various types of format. As default, <code>PLINK</code> for mat is selected, but please choose the format of your input file since this will cause error during process. Each option requires the following format.</p>
        <p>The input file must include P-value and either rsID or chromosome index and genetic position on hg19 reference genome. Alleles are not mandatory but if only one allele is provided, that is considered as affected allele. When two alleles are provided, it will depends on header. If alleles are not provided, they will be extracted from dbSNP build 146 as minor allele as affected alleles.</p>
        <p>If you are not sure which format to use, either edit your header or select <code>Plain Text</code> which will cover most of common column names.</p>
        <p>Delimiter can be any of white space including single space, multiple space and tab. Because of this, column name must not include any space.</p>
        <p>The column of chromosome can be string like &quot;chr1&quot; or just integer &quot;1&quot;. When &quot;chr&quot; is attached, this will be removed from outputs. When the input file contains chromosome X, this will be encoded as chromosome 23, however, input file can be leave as &quot;X&quot;.</p>
        <div style="margin-left: 40px;">
          <h4 id="1-plink-format">1. <code>PLINK</code> format</h4>
          <p>&ensp;As the most common file format, <code>PLINK</code> is the default option. Some options in PLINK do not return both A1 and A2 but as long as the file contains either SNP or CHR and BP, IPGAP will cover missing values.</p>
          <ul>
            <li><strong>SNP</strong>: rsID</li>
            <li><strong>CHR</strong>: chromosome</li>
            <li><strong>BP</strong>: genomic position (hg19)</li>
            <li><strong>A1</strong>: affected allele</li>
            <li><strong>A2</strong>: another allele</li>
            <li><strong>P</strong>: P-value (Mandatory)</li>
          </ul>
          <h4 id="2-snptest-format">2. <code>SNPTEST</code> format</h4>
          <p>&ensp;Since in the output file of SNPTEST contains lines start with &#39;#&#39;, those lines will be skipped. Herder line should not start with &#39;#&#39; and should be the first line without &#39;#&#39; in the file.</p>
          <ul>
            <li><strong>rsid</strong>: rsID</li>
            <li><strong>chromosome</strong>: chromosome</li>
            <li><strong>position</strong>: genomic position (hg19)</li>
            <li><strong>alleleB</strong>: affected allele</li>
            <li><strong>alleleA</strong>: another alleleA</li>
            <li><strong>frequentist_add_pvalue</strong>: P-value</li>
          </ul>
          <h4 id="3-ctga-format">3. <code>CTGA</code> format</h4>
          <ul>
            <li><strong>SNP</strong>: rsID</li>
            <li><strong>Chr</strong>: chromosome</li>
            <li><strong>bp</strong>: genomic position (hg19)</li>
            <li><strong>OtherAllele</strong>: affected allele</li>
            <li><strong>ReferenceAllele</strong>: another alleleA</li>
            <li><strong>p</strong>: P-value</li>
          </ul>
          <h4 id="4-metal-format">4. <code>METAL</code> format</h4>
          <p>&ensp;The output of METAL (for meta analyses) only contains rsID without chromosome and genomic position. Therefore, those information will be extracted from dbSNP build 146 using rsID. For this, rsID will be first updated to build 146.</p>
          <ul>
            <li><strong>MakerName</strong>: rsID</li>
            <li><strong>Allele1</strong>: affected allele</li>
            <li><strong>Allele2</strong>: another alleleA</li>
            <li><strong>P-value</strong>: P-value</li>
          </ul>
          <h4 id="5-plain-text-format">5. <code>Plain Text</code> format</h4>
          <p>&ensp;If your file does not fit in any of above option, please use <code>Plain Text</code> option. The following headers are <em>case insensitive</em>.</p>
          <ul>
            <li><strong>SNP|markername|rsID</strong>: rsID</li>
            <li><strong>CHR|chromosome|chrom</strong>: chromosome</li>
            <li><strong>BP|pos|position</strong>: genomic position (hg19)</li>
            <li><strong>A1|alt|effect_allele|allele1</strong>: affected allele</li>
            <li><strong>A2|ref|non_effect_allele|allele2</strong>: another allele</li>
            <li><strong>P|pvalue|p-value|p_value</strong>: P-value (Mandatory)</li>
          </ul>
          <hr>
          <h4 id="note-and-tips">Note and Tips</h4>
          <p>The pipeline only support human genome hg19. If your input file is not in hg19, please update the genomic position using liftOver from UCSC. However, there is an option for you!! When you provide only rsID without chromosome index and genomic position, IPGAP will extract them from dbSNP as hg19 genome. To do this, remove columns of chromosome index and genomic position.</p>
          <hr>
        </div>
        <h3 id="parameters">Parameters</h3>
        <p>IPGAP provide a variety of parameters. Default setting will perform naive positional mapping which gives you all genes within LD blocks of lead SNPs. In this section, every parameter will be described details.</p>
        <div style="margin-left: 40px;">
          <h4 id="input-files">Input files</h4>
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
                <td>GWAS summary statistics</td>
                <td>Mandatory</td>
                <td>Input file of GWAS summary statistics</td>
                <td>File upload</td>
                <td>none</td>
              </tr>
              <tr>
                <td>Predefined lead SNPs</td>
                <td>Optional</td>
                <td>Optionally, user can provide predefined lead SNPs. Please follow the format below.</td>
                <td>File upload</td>
                <td>none</td>
              </tr>
              <tr>
                <td>Identify additional lead SNPs</td>
                <td>Optional only when predefined lead SNPs are provided</td>
                <td>If this option is given, PRROT will identify independent lead SNPs after defined LD block of predefined lead SNPs. Otherwise, only given lead SNPs will be analyzed.</td>
                <td>Check</td>
                <td>Checked</td>
              </tr>
              <tr>
                <td>Predefined genetic region</td>
                <td>Optional</td>
                <td>Optionally, user can provide specific genomic region to perform IPGAP. IPGAP only look provided regions to identify lead SNPs and candidate SNPs. If you are only interested in specific regions, this will increase a speed of job.</td>
                <td>File upload</td>
                <td>none</td>
              </tr>
            </tbody>
          </table>
          <h4 id="parameters-for-lead-snp-identification">Parameters for lead SNP identification</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Parameter</th>
                <th>Mandatory</th>
                <th>Description</th>
                <th>Type</th>
                <th>Default</th>
                <th>Direction</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                <td>Sample size (N)</td>
                <td>Mandatory</td>
                <td>The total number of sample in the GWAS. This is only used for MAGMA and LD score regression.</td>
                <td>Integer</td>
                <td>none</td>
                <td>Doesn&#39;t affect any candidates</td>
              </tr>
              <tr>
                <td>Maximum lead SNP P-value (&lt;=)</td>
                <td>Mandatory</td>
                <td>IPGAP identifies lead SNPs wiht P-value less than or equal to this threshold. This should not me changed unless GWAS is under-powered and only a few peaks are significant.</td>
                <td>numeric</td>
                <td>5e-8</td>
                <td>lower: decrease #lead SNPs. higher: increase #lead SNPs which most likely increate noises</td>
              </tr>
              <tr>
                <td>Minimum r2 (&gt;=)</td>
                <td>Mandatory</td>
                <td>The minimum correlation to be in LD of a lead SNP.</td>
                <td>numeric</td>
                <td>0.6</td>
                <td>higher: decrease #candidate SNPs and increase #lead SNPs. lower: increase #candidate SNPs and decrease #lead SNPs</td>
              </tr>
              <tr>
                <td>Maximum GWAS P-value (&lt;=)</td>
                <td>Mandatory</td>
                <td>This is the threshold for candidate SNPs within the LD block of a lead SNP. This will be applied only for GWAS-tagged SNPs.</td>
                <td>numeric</td>
                <td>0.05</td>
                <td>higher: decrease #candidate SNPs. lower: increase #candidate SNPs.</td>
              </tr>
              <tr>
                <td>Population</td>
                <td>Mandatory</td>
                <td>The population of reference panel to compute r2 and MAF. Five populations are available from 1000G Phase 3.</td>
                <td>Select</td>
                <td>EUR</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Include 1000 genome variants</td>
                <td>Mandatory</td>
                <td>If checked, IPGAP include all SNPs in strong LD with any of lead SNPs even for non-GWAS-tagged SNPs.</td>
                <td>Yes/No</td>
                <td>Yes</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Minimum MAF (&gt;=)</td>
                <td>Mandatory</td>
                <td>The minimum Minor Allele Frequency of candidate SNPs. This filter also apply to lead SNPs. If there is any pre-defined lead SNPs with MAF less than this threshold, that will be skipped.</td>
                <td>numeric</td>
                <td>0.01</td>
                <td>higher: decrease #candidate SNPs. lower: increase #candidate SNPs</td>
              </tr>
              <tr>
                <td>Maximum merge distance of LD (&lt;=)</td>
                <td>Mandatory</td>
                <td>This is the maximum distance between LD blocks from independent lead SNPs to merge into genomic interval. When it is set at 0, only physically overlapped LD blocks are merged into genomic interval. Definition of interval is independent from definition of candidate SNPs.</td>
                <td>numeric</td>
                <td>250kb</td>
              <td>-</td>
              </tr>
            </tbody>
          </table>
          <br/>
          <h4 id="mhc-region">MHC region</h4>
          <p>MHC region is often excluded due to the complicated LD structure. Therefore, this option is checked by default. Please uncheck to include MHC region. It doesn&#39;t change any results if there is no significant hit in the MHC region.</p>
          <p>Default region is defined as between &quot;MOG&quot; and &quot;COL11A2&quot; genes. To define user own MHC region, please provide in the text box. The input format should be like &quot;25000000-34000000&quot;.</p>
          <br/>
          <h4 id="parameters-for-gene-mapping">Parameters for gene mapping</h4>
          <p>There are two options for gene mapping; positional and eQTL mappings. By default, positional mapping with maximum distance 10kb is defined. Since this parameter setting largely reflect to the result of mapped genes, please set carefully.</p>
          <br/>
          <h4 id="positional-mapping">Positional mapping</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Parameter</th>
                <th>Mandatory</th>
                <th>Description</th>
                <th>Type</th>
                <th>Default</th>
                <th>Direction</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Position mapping</td>
                <td>Optional</td>
                <td>Whether perform positional mapping or not.</td>
                <td>Check</td>
                <td>Checked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Gene window</td>
                <td>Optional</td>
                <td>Map SNPs to gene based on physical distance</td>
                <td>Check</td>
                <td>Checked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Gene window size (&lt;=)</td>
                <td>Optional</td>
                <td>The maximum distance to map SNPs to genes</td>
                <td>numeric</td>
                <td>10kb</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Annotations based mapping</td>
                <td>Optional</td>
                <td>Map SNPs to genes baed on positional mapping such as exonic, intronic splicing, etc...</td>
                <td>Check</td>
                <td>Unchecked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Annotations</td>
                <td>Mandatory only when Annotation based mapping is activated</td>
                <td>Positional annotation to map SNPs to genes</td>
                <td>Multiple selection</td>
                <td>none</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
          <h4 id="eqtl-mapping">eQTL mapping</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Parameter</th>
                <th>Mandatory</th>
                <th>Description</th>
                <th>Type</th>
                <th>Default</th>
                <th>Direction</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>eQTL mapping</td>
                <td>Optional</td>
                <td>Whether perform eQTL mapping or not</td>
                <td>Check</td>
                <td>Unchecked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Tissue types</td>
                <td>Mandatory if eQTL mapping is activated</td>
                <td>All available tissue types with Data sources are shown in the select box. <code>Tissue type</code> selection contain individual tissue types and <code>General tissue types</code> contain broad area of organ and each general tissue contains multiple individual tissue types.</td>
                <td>Multiple selection</td>
                <td>none</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Significant eQTL only (FDR&lt;=0.05)</td>
                <td>Optional</td>
                <td>To map only significant eQTL at FDR 0.05</td>
                <td>Check</td>
                <td>Checked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>eQTL maximum P-value (&lt;=)</td>
                <td>Mandatory if Significant eQTL only is unchecked</td>
                <td>This option will show up on the screen only when <code>Significant eQTL only</code> is unchecked. This can be used as threshold of eQTL uncorrected P-value.</td>
                <td>numeric</td>
                <td>1e-3</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
          <h4 id="functional-annotation-filtering">Functional annotation filtering</h4>
          <p>Both positional and eQTL mappings have same options for the filtering of SNPs based on functional annotation, but parameters have to be set for each mapping separately.</p>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Parameter</th>
                <th>Mandatory</th>
                <th>Description</th>
                <th>Type</th>
                <th>Default</th>
                <th>Direction</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>CADD score</td>
                <td>Optional</td>
                <td>Whether perform filtering of SNPs by CADD score or not.</td>
                <td>Check</td>
                <td>Unchecked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Minimum CADD score (&gt;=)</td>
                <td>Mandatory if <code>CADD score</code> is checked</td>
                <td>The higher CADD score, the more deleterious.</td>
                <td>numeric</td>
                <td>12.37</td>
                <td>-</td>
              </tr>
              <tr>
                <td>RegulomeDB score</td>
                <td>Optional</td>
                <td>Whether perform filtering of SNPs by RegulomeDB score or not.</td>
                <td>Check</td>
                <td>Unchecked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Minimum RegulomeDB score (&gt;=)</td>
                <td>Mandatory if <code>RegulomeDB score</code> is checked</td>
                <td>RegulomeDB score is a categorical (from 1a to 7). Please refer link for details. 1a is the most likely affect regulation. Note that not all SNPs in 1000G Phase3 has this score. Those SNPs are recorded as NA. Those SNPs will be filtered out when RegulomeDB score filtering is performed.</td>
                <td>string</td>
                <td>7</td>
                <td>-</td>
              </tr>
              <tr>
                <td>15-core chromatin state</td>
                <td>Optional</td>
                <td>Whether perform filtering of SNPs by chromatin state or not.</td>
                <td>Check</td>
                <td>Unchecked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>15-core chromatin state tissue/cell types</td>
                <td>Mandatory if <code>15-core chromatin state</code> is checked</td>
                <td>Multiple tissue/cell types can be selected from either list of individual types or general types.</td>
                <td>Multiple selection</td>
                <td>none</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Maximum state of chromatin(&lt;=)</td>
                <td>Mandatory if <code>15-core chromatin state</code> is checked</td>
                <td>The maximum state to filter SNPs. Between 1 and 15. Generally, above 7 is open state. Please refer link for further details.</td>
                <td>numeric</td>
                <td>7</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Method for 15-core chromatin state filtering</td>
                <td>Mandatory if <code>15-core chromatin state</code> is checked</td>
                <td>When multiple tissue/cell types are selected, either <code>any</code> (a SNP has state above than threshold in any of selected tissue/cell types), <code>majority</code> (a SNP has state above than threshold in majority (&gt;=50%) of selected tissue/cell type), or <code>all</code> (a SNP has state above than threshold in all of selected tissue/cell type).</td>
                <td>Selection</td>
                <td>any</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>

        </div>
        <h3 id="submit-job">Submit your job</h3>
        <p>The submission page will show parameter option step by step. For example, you can only see the option for uploading files after providing email address and job title.
        Each option will guide you with messages like the followings.</p>
        <div class="alert alert-info">
          This is information for you.
        </div>
        <div class="alert alert-success">
          This is the message if everything is fine.
        </div>
        <div class="alert alert-danger">
          This is the message if there is any error or wrong input.
        </div>
        <div class="alert alert-warning">
          This is the warning message. You are allowed to go further but please pay attention.
        </div>
        <div style="margin-left: 40px;">
          <h4 id="1-enter-email-address-and-job-title">1. Enter email address and job title</h4>
          <p>Email address and job title are mandatory to submit a new job. The combination of these two will create unique ID to store results. Email address will be only used to inform the completion of your job. If warning message is shown, the job with the same title already exists but you can overwrite.</p>
          <h4 id="2-select-input-files">2. Select input files</h4>
          <p>GWAS summary statistics file is mandatory. Please don't forget to select correct file format. Pre-defined lead SNPs and/or genetic regions can be provided here, too.</p>
          <h4 id="3-set-parameters">3. Set parameters</h4>
          <p>Please refer &quot;Parameters&quot; section for details.</p>
          <h4 id="4-submit-">4. Submit!!!</h4>
          <p>Unless there is any error or wrong input, the submit button is enabled. If the submit button is still disabled, please check if there is any error message.
          You will receive two emails, one is to inform that job has been submitted and second one is to inform you job has been done.
          Don't worry about bookmarking the link, you will be able to query your results with email address and job title.
          Usually, the job takes 20 min to 1 hour depending on parameters.
          If you provide pre-defined genomic region or significant signals in the input GWAS is a few, it more likely to finish job quickly.
          In that case, you can stay in the submitted page, and as soon as job is done, page will be updated to your results.</p>
        </div>
        <h3 id="outputs">Outputs</h3>
        <p>Go to SNP2GENE and in the &quot;Query existing job&quot; panel, enter your email address and job title. If both are correct, &quot;Go to Job&quot; button is enabled.</p>
        <p>There are 5 panels in the result page.</p>
        <h4>1. Information of your job</h4>
        <p>This panel contains your email address, job title and the date of job submission.</p>
        <h4>2. Genome-wide plots</h4>
        <p>This panel displays manhattan plots and Q-Q plots for both SNP and gene-based association test.</p>
        <ul>
          <li>Plots for SNPs<br/>
            To minimize overlapped data points in the plot, they are filtered based on the following criteria.
            Please be aware that, since majority od overlapped data points are not displayed in the plot, those plots are approximated plots.
            <ul>
              <li>Manhattan plot: </li>
              <li>Q-Q plot: </li>
            </ul>
          </li>
          <li>Plots for gene-based test<br/>
            Gene based test was performed by using MAGMA with default setting.
            SNPs were assigned to the genes obtained from Ensembl build 85 (only protein-coding genes).
            MAGMA results are available from the download button.
          </li>
        </ul>

        <h4>3. Summary of results</h4>
        <p>This panel shows summary of your GWAS input.</p>
        <ul>
          <li>Summary of SNPs and mapped genes<ul>
            <li><strong>#lead SNPs</strong>: The number of independent lead SNPs identified.</li>
            <li><strong>#Intervals</strong>: The number of genomic intervals defined from the independent lead SNPs.</li>
            <li><strong>#candidate SNPs</strong>: The number of candidate SNPs which are in LD (given r2) of one of the independet lead SNPs.
              This includes non-GWAS tagged SNPs which is extracted from 1000G reference panel.
              When SNPs were filtered based on functional annotation for gene mapping, this number if before the functional filtering.</li>
            <li><strong>#candidate GWAS tagged SNPs</strong>: The number of candidate SNPs (described above) which are tagged in GWAS (exists in your input file).</li>
            <li><strong>#mapped genes</strong>: The number of genes mapped by user-defined parameters.</li>
          </ul></li>
          <li>Positional annotation of candidate SNPs</li>

          <li>Summary per interval</li>
        </ul>
        <h4>4. Result tables</h4>
        <p>This panel contain multiple tables of your results.
        Here are descriptions for columns in each tables.
        Downloadable text files have the same column as shown in the interface unless methioned.</p>
        <p>By clicking one of the rows of tables of independent lead SNPs or genomic intervals, it will create regional plots of candidate SNPs (GWAS P-value).
          To create plots with genes and other functional annotations, please go to Regional plot panel.</p>
        <div style="margin-left: 40px;">
          <ul>
            <li><p>lead SNPs / leadSNPs.txt</p>
            <p>All independent lead SNPs identified by IPGAP.</p>
            <ul>
              <li><strong>No</strong> : Index of lead SNPs</li>
              <li><strong>Interval</strong> : Index of assigned genomic interval. This matches with the index of interval table.</li>
              <li><strong>uniqID</strong> : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
              <li><strong>rsID</strong> : rsID based on dbSNP build 146.</li>
              <li><strong>chr</strong> : chromosome</li>
              <li><strong>pos</strong> : position on hg19</li>
              <li><strong>P-value</strong> : P-value (from the input file).</li>
              <li><strong>nSNPs</strong> : The number of SNPs within LD of the lead SNP given r2, including non-GWAS-tagged SNPs (which are extracted from 1000G).</li>
              <li><strong>nGWASSNPs</strong> : The number of GWAS-tagged SNPs within LD of the lead SNP given r2. This is a subset of &quot;nSNPs&quot;.</li>
            </ul>
            </li>
          </ul>
          <ul>
            <li><p>Intervals / intervals.txt</p>
            <p>Genomic intervals defined from independent lead SNPs.
            Each interval is represented by the top lead SNP which has the minimum P-value in the interval.</p>
            <ul>
              <li><strong>Interval</strong> : Index of genomic interval.</li>
              <li><strong>uniqID</strong> : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
              <li><strong>rsID</strong> : rsID of the top lead SNP based on dbSNP build 146.</li>
              <li><strong>chr</strong> : chromosome of top lead SNP</li>
              <li><strong>pos</strong> : position of top lead SNP on hg19</li>
              <li><strong>P-value</strong> : P-value of top lead SNP (from the input file).</li>
              <li><strong>nLeadSNPs</strong> : The number of lead SNPs merged into the interval.</li>
              <li><strong>start</strong> : Start position of the interval.</li>
              <li><strong>start</strong> : End postion of the interval.</li>
              <li><strong>nSNPs</strong> : The number of canidate SNPs in the interval, including non-GWAS-tagged SNPs (which are extracted from 1000G).</li>
              <li><strong>nGWASSNPs</strong> : The number of GWAS-tagged candidate SNPs within the interval. This is a subset of &quot;nSNPs&quot;.</li>
            </ul>
            </li>
          </ul>
          <ul>
            <li><p>SNPs (annotation) / snps.txt</p>
            <p>All candidate SNPs with annotations. Note that depending on your mapping criterion, not all candidate SNPs are mapped to genes.</p>
            <ul>
              <li><strong>uniqID</strong> : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
              <li><strong>rsID</strong> : rsID based on dbSNP build 146.</li>
              <li><strong>chr</strong> : chromosome</li>
              <li><strong>pos</strong> : position on hg19</li>
              <li><strong>ref</strong> : Reference allele. Non-effect allele if it is provided in the input GWAS summary statistics file. If not, this is the reference allele in 1000G.</li>
              <li><strong>alt</strong> : Alternative allele. Effect allele if it is provided in the input GWAS summary statistics file. If not, this is the alternative (minor) allele in 1000G.</li>
              <li><strong>MAF</strong> : Minor allele frequency computed based on 1000G.</li>
              <li><strong>gwasP</strong> : P-value (from the input file).</li>
              <li><strong>r2</strong> : The maximum r2 of the SNP with one of the independent lead SNP (this dosen't have to be top lead SNPs in the intervals).</li>
              <li><strong>leadSNP</strong> : rsID of a independent lead SNP which has the maximum r2 of the SNP.</li>
              <li><strong>Interval</strong> : Index of the interval.</li>
              <li><strong>nearestGene</strong> : The nearest Gene of the SNP. Genes are ecoded in symbol, if it is available. If not, ENSG ID is shown. Genes here include all transcripts from Ensembl gene build 85 includeing non-protein coding genes and RNAs.</li>
              <li><strong>dist</strong> : Distance to the nearest gene.</li>
              <li><strong>func</strong> : Potisional annotation obtained from ANNOVAR. For exonic SNPs, detail annotation (e.g. non-synonymous, stop gain and so on) is available in ANNOVAR table (annov.txt).</li>
              <li><strong>CADD</strong> : CADD score which is computed based on 67 annotations. The higher score, the more deleterious the SNP is. 12.37 is the suggested threshold by Kicher et al(ref).</li>
              <li><strong>RDB</strong> : RegulomeDB score which is the categorical score (from 1a to 7). 1a is the highest score that the SNP has the most biological evidence to be regulatory element.</li>
              <li><strong>minChrState</strong> : The minimum 15-core chromatin state over 127 tissue/cell type.</li>
              <li><strong>commonChrState</strong> : The majority of the 15-core chromatin state over 127 tissue/cell types.</li>
            </ul>
            </li>
          </ul>
          <ul>
            <li><p>annot.txt (Not shown in the interface but downloadable)</p></li>
            <p>This file contains annotation of candidate SNPs.
              CADD score, RegulomeDB score and summarized chromatin state are shown in the SNPs table.
              This file contains all 127 tissue/cell types of chromatin states</p>
            <ul>
              <li><strong>uniqID</strong> : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
              <li><strong>CADD</strong> : CADD score which is computed based on 67 annotations. The higher score, the more deleterious the SNP is. 12.37 is the suggested threshold by Kicher et al(ref).</li>
              <li><strong>RDB</strong> : RegulomeDB score which is the categorical score (from 1a to 7). 1a is the highest score that the SNP has the most biological evidence to be regulatory element.</li>
              <li><strong>E001~E129</strong> : Chromatin state predicted by ChrHMM. ID of tissue cell types and description of 15 states are available under external data sources secton.</li>
            </ul>
          </ul>
          <ul>
            <li><p>ANNOVAR / annov.txt</p>
            <p>Since one SNP can be annotated multiple positional information, the table of ANNOVAR output is separated from SNPs table. This table contain unique SNP-annotation combination.</p>
            <ul>
              <li><strong>uniqID</strong> : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
              <li><strong>chr</strong> : chromosome</li>
              <li><strong>pos</strong> : position on hg19</li>
              <li><strong>Gene</strong> : ENSG ID</li>
              <li><strong>Symbol</strong> : Gene Symbol</li>
              <li><strong>Distance</strong> : Distance to the gene</li>
              <li><strong>Function</strong> : Positional annotation</li>
              <li><strong>Exonic function</strong> : Functional annotation of exonic SNPs</li>
              <li><strong>Exon</strong> : Index of exon</li>
            </ul>
            </li>
          </ul>
          <ul>
            <li><p>Genes / genes.txt</p>
            <p>The summary of mapped genes based on your defined mapping criterion.
             Columns change for positional and eQTL mappings.
             When both mappings are performed, all columns exit in the table.</p>
            <ul>
              <li><strong>Gene</strong> : ENSG ID</li>
              <li><strong>Symbol</strong> : Gene Symbol</li>
              <li><strong>entrezID</strong> : entrez ID</li>
              <li><strong>Interval</strong> : Index of interval where mapped SNPs are from. This could contain more than one interval in the case that eQTLs are mapped to genes from distinct genomic intervals.</li>
              <li><strong>chr</strong> : chromosome</li>
              <li><strong>start</strong> : gene starting position</li>
              <li><strong>end</strong> : gene ending position</li>
              <li><strong>strand</strong> : strand od gene</li>
              <li><strong>status</strong> : status of gene from Ensembl</li>
              <li><strong>type</strong> : gene biotype from Ensembl</li>
              <li><strong>HUGO</strong> : HUGO (HGNC) gene symbol</li>
              <li><strong>posMapSNPs</strong> (posMap): The number of SNPs mapped to gene based on positional mapping (after functional filtering if parameters are given).</li>
              <li><strong>posMapMaxCADD</strong> (posMap): The maximum CADD score of mapped SNPs by positional mapping.</li>
              <li><strong>eqtlMapSNPs</strong> (eqtlMap): The number of SNPs mapped to the gene based on eQTL mapping.</li>
              <li><strong>eqtlMapminP</strong> (eqtlMap): The minimum eQTL P-value of mapped SNPs.</li>
              <li><strong>eqtlMapmin!</strong> (eqtlMap): The minimum eQTL FDR of mapped SNPs.</li>
              <li><strong>eqtlMapts</strong> (eqtlMap): Tissue types of mapped eQTL SNPs.</li>
              <li><strong>eqtlDirection</strong> (eqtlMap): consecutive direction of mapped eQTL SNPs.</li>
              <li><strong>minGwasP</strong> : The minimum P-value of mapped SNPs.</li>
              <li><strong>leadSNPs</strong> : All independent lead SNPs of mapped SNPs.</li>
            </ul>
            </li>
          </ul>
          <ul>
            <li><p>eQTL / eqtl.txt</p>
            <p>This table is only shown when you performed eQTL mapping.
             The table contain unique pair of SNP-gene-tissue, therefore, the same SNP could appear in the table multiple times.</p>
            <ul>
              <li><strong>uniqID</strong> : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
              <li><strong>chr</strong> : chromosome</li>
              <li><strong>pos</strong> : position on hg19</li>
              <li><strong>DB</strong> : Data source of eQTLs. Currently GTEx, BloodeQTL and BIOS are available. Please refer &quot;External Data sources&quot; for details.</li>
              <li><strong>tissue</strong> : tissue type</li>
              <li><strong>Gene</strong> : ENSG ID</li>
              <li><strong>Symbol</strong> : Gene symbol</li>
              <li><strong>P-value</strong> : P-value of eQTLs</li>
              <li><strong>FDR</strong> : FDR of eQTLs. Note that method to compute FDR differs between data sources. Please refer &quot;External Data sources&quot; for details.</li>
              <li><strong>tz</strong> : T-statistics or z score depends on data source.</li>
            </ul>
            </li>
          </ul>
          <ul>
            <li><p>GWAScatalog / gwascatalog.txt</p>
              <p>List of SNPs reported in GWAScatalog which are candidate SNPs of your GWAS summary statistics. The table does not contain all recode from GWAScatalog. To get full information, please download from &quot;Downloads&quot; tab.</p>
              <ul>
                <li><strong>Interval</strong> : Index of interval.</li>
                <li><strong>lead SNP</strong> : The lad SNP of the SNP in GWAScatalog.</li>
                <li><strong>chr</strong> : chromosome</li>
                <li><strong>bp</strong> : position on hg19</li>
                <li><strong>snp</strong> : rsID of reported SNP in GWAS catalog</li>
                <li><strong>PMID</strong> : PubMed ID</li>
                <li><strong>Trait</strong> : The trait reported in GWAScatalog</li>
                <li><strong>FirthAuth</strong> : First author reported in GWAScatalog</li>
                <li><strong>Date</strong> : Date added in GWAScatalog</li>
                <li><strong>P-value</strong> : Reported P-value</li>
              </ul>
            </li>
          </ul>
          <ul>
            <li><p>Parameters / params.txt</p>
            <p>The table of input parameters.</p>
            <ul>
              <li><strong>Job created</strong> : Date of job created</li>
              <li><strong>Job title</strong> : Job title</li>
              <li><strong>input GWAS summary statistics file</strong> : File name of GWAS summary statistics</li>
              <li><strong>input lead SNPs file</strong> : File name of pre-defined lead SNPs if provided.</li>
              <li><strong>Identify additional lead SNPs</strong> : 1 if option is checked, 0 otherwise. If pre-defined lead SNPs are not provided, it is always 1.</li>
              <li><strong>input genetic regions file</strong> : File name of pre-defined genetic regions if provided.</li>
              <li><strong>sample size</strong> : Sample size of GWAS</li>
              <li><strong>exclude MHC</strong> : 1 to exclude MHC region, 0 otherwise</li>
              <li><strong>extended MHC region</strong> : user defined MHC region if provided, NA otherwise</li>
              <li><strong>exclude chromosome X</strong> : 1 to exclude X chromosome, 0 otherwise</li>
              <li><strong>gene type</strong> : All selected gene type.</li>
              <li><strong>lead SNP P-value</strong> : the maximum threshold of P-value to be lead SNP</li>
              <li><strong>r2</strong> : the minimum threshold for SNPs to ne in LD of the lead SNPs</li>
              <li><strong>GWAS tagged SNPs P-value</strong> : the maximum threshold of P-value to be candidate SNP</li>
              <li><strong>MAF</strong> : the minimum minor allele frequency based on 1000 genome reference of given population</li>
              <li><strong>Include 1000G SNPs</strong> : 1 to include non-GWAS-tagged SNPs from reference panel, 0 otherwise</li>
              <li><strong>Interval merge max distance</strong> : The maximum distance between LD blocks to merge into interval</li>
              <li><strong>Positional mapping</strong> : 1 to perform positional mapping, 0 otherwise</li>
              <li><strong>posMap Window based</strong> : 1 to perform positional mapping based on distance to the genes, 0 otherwise</li>
              <li><strong>posMap Window size</strong> : If window based positional mapping is performed, which distance (kb) as the maximum. If window based mapping is 0, this parameter set at 10 as default but will be ignored.</li>
              <li><strong>posMap Annotation based</strong> : Positional annotations selected if window based mapping is 0.</li>
              <li><strong>posMap min CADD</strong> : The minimum CADD score for SNP filtering</li>
              <li><strong>posMap min RegulomeDB</strong> : The minimum RegulomeDB score for SNP filtering</li>
              <li><strong>posMap chromatin state filterinf tissues</strong> : Select tissue/cell types, NA otherwise</li>
              <li><strong>posMap max chromatin state</strong> : The maximum 15-core chromatin state</li>
              <li><strong>posMap chromatin state filtering method</strong> : The method of chromatin state filtering</li>
              <li><strong>eQTL mapping</strong> : 1 to perform eQTL mapping, 0 otherwise</li>
              <li><strong>eqtlMap tissues</strong> : Selected tissue typed for eQTL mapping</li>
              <li><strong>eqtlMap min CADD</strong> : The minimum CADD score for SNP filtering</li>
              <li><strong>eqtlMap min RegulomeDB</strong> : The minimum RegulomeDB score for SNP filtering</li>
              <li><strong>eqtlMap chromatin state filterinf tissues</strong> : Select tissue/cell types, NA otherwise</li>
              <li><strong>eqtlMap max  chromatin state</strong> : The maximum 15-core chromatin state</li>
              <li><strong>eqtlMap chromatin state filtering method</strong> : The method of chromatin state filtering</li>
            </ul>
            </li>
          </ul>
        </div>
        <br/>

        <h4>5. Downloads</h4>
        <p>To download multiple tables at the same time, go to &quot;Downloads&quot; tab and select files you want to download.</p>
        <!-- <h4>4. Query results</h4>
        <p>This is still under construction. Will be available soon.</p> -->
        <br/>

        <h4>6. Regional plot (with annotation)</h4>
        <p>This panel contains options to create regional plot with annotations.
        The plot will be created in a new tab.</p>
        <br/>

        <h3 id="examples">Example senarios</h3>
        <h4>Senario 1: You have got a new results of GWAS and want to know which genes are there.</h4>
        <h4>Senario 2: You have got a new results of GWAS and want to prioritize candidate SNPs and genes with a sprcific criteria</h4>
        <h4>Senario 3: You are interested in a specific locus with significant hit in GWAS</h4>

      </div>
    </div>

    <div id="gene2func" class="sidePanel" style="padding-top:50;">
      <h2>GENE2FUNC</h2>
      <div style="padding-left: 40px;">
        <h3 id="submit-genes">Submit genes</h3>
        <div style="padding-left: 40px;">
          <h4 id="use-mapped-genes-from-snp2gene">Use mapped genes from SNP2GENE</h4>
          <p>If you want to use mapped genes from SNP2GENE, just click a button in the result table panel of result page.
          It will open a new tab and automatically start analyses.
          This will take all mapped genes and use background genes with gene types you selected (such as &quot;protein-coding&quot; or &quot;ncRNA&quot;).
          Parameters for excluding chromosome X and excluding MHC region also used to filter background genes.</p>
          <h4 id="use-a-list-of-genes-of-interest">Use a list of genes of interest</h4>
          <p>To analyse your genes, you have to prepare list of genes as either ENSG ID, entrez ID or gene symbol.
          Genes can be provided in the text are (one gene per line) or uploading file in the left panel. When you upload a file, genes have to be in the first column with header. Header can be anything (even just a new line is fine) but start your genes from second row.</p>
          <p>To analyse your genes, you need to specify background genes. You can choose from the gene types which is the easiest way. However, in the case that you need to use specific background genes, please provide them either in the text area of by uploading a file of the right panel.
          File format should be same as described for genes on interest.</p>
        </div>

        <h3 id="gene2funcOutputs">Results and Outputs</h3>
        <div style="padding-left: 40px;">
          <p>Once analysis is done, the three panel will be appear in the same page.</p>
          <h4 id="gene-expression-heatmap">Gene Expression Heatmap</h4>
          <h4 id="tissue-specificity">Tissue specificity</h4>
          <h4 id="molecular-functions">Molecular functions</h4>
        </div>
      </div>
    </div>
  </div>

</div>
</div>
@stop
