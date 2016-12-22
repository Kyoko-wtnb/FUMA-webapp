@extends('layouts.master')
@section('head')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}"></script>
<script type="text/javascript">
  $(document).ready(function(){
    var hashid = window.location.hash;
    var side = [];
    $('.sidebar-nav li a').each(function(){
      side.push($(this).attr("href"));
    })
    if(hashid==""){
      $('a[href*="#overview"]').trigger('click');
    }else{
      if(side.indexOf(hashid)>=0){
        // $(hashid).show();
        $('a[href*="'+hashid+'"]').trigger('click');
      }else{
        $('.subside a').each(function(){
          if($(this).attr("href")==hashid){
            var parent = '#'+$(this).parent().attr("id").replace("sub", "");
            // $(parent).show();
            $('a[href*="'+parent+'"]').trigger('click');
            $(this).trigger('click');
          }
        });
      }
    }

    $('.infoPop').popover();
  });

</script>
@stop

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
        <a href="#geneQuery">Gene functions</a>
      </div>
    <li><a href="#snp2gene">SNP2GENE<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
      <div class="subside" id="snp2genesub">
        <a href="#prepare-input-files">Input files</a>
        <a href="#parameters">Parameters</a>
        <!-- <a href="#submit-job">Subit your job</a> -->
        <a href="#outputs">Outputs</a>
        <!-- <a href="#examples">Example senarios</a> -->
      </div>
    <li><a href="#gene2func">GENE2FUNC<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
      <div class="subside" id="gene2funcsub">
        <a href="#submit-genes">Submit genes</a>
        <a href="#gene2funcOutputs">Outputs</a>
      </div>
  </ul>
</div>

  <!-- <div class="col-md-10"> -->
<div id="page-content-wrapper">
  <div class="page-content inset">
    <div id="test"></div>
    <div id="overview" class="sidePanel container" style="padding-top:50px;">
      <h3>Overview of the FUMA GWAS</h3>
      <div style="margin-left: 40px;">
        <p>The platform mainly consists of two separate process, SNP2GENE and GENE2FUNC.</p>
        <p>To annotate and prioritize SNPs and genes from your GWAS summary statistics, go to <a href="/IPGAP/snp2gene"><strong>SNP2GENE</strong></a> which compute LD structure,
          annotate functions to SNPs, and prioritize candidate genes.</p>
        <p>If you already have a list of genes, go to <a href="/IPGAP/gene2func"><strong>GENE2FUNC</strong></a> to check expression pattern and shared molecular functions.<p/>
        <br/>
        <img src="{{ URL::asset('/image/pipeline.png') }}" width="600" align="middle">
      </div>
    </div>

    <div id="quick-start" class="sidePanel container" style="padding-top:50px;">
      <h2>Quick Start</h2>
      <div style="margin-left: 40px;">
        <h3>General Information</h3>
          <p>
            Each page will contain information and description of inputs and results to help you understand without go through entire tutorial.<br/>
            <div style="padding-left: 40px">
              <span class="info"><i class="fa fa-info"></i> This is information of inputs or results.</span><br/><br/>
              <a class="infoPop" data-toggle="popover" data-content="This popuover will show brief description."><i class="fa fa-question-circle-o fa-lg"></i></a> :click this question mark to display brief description.<br/><br/>
              <span class="alert alert-info" style="padding: 5px;">
                This is for optional inputs/parameters.
              </span><br/><br/>
              <span class="alert alert-success" style="padding: 5px;">
                This is the message if everything is fine.
              </span><br/><br/>
              <span class="alert alert-danger" style="padding: 5px;">
                This is the message if the input/parameter is mandatory and not given or invalid input is given.
              </span><br/><br/>
              <span class="alert alert-warning" style="padding: 5px;">
                This is the warning message for the input/parameter. It can be ignored but need to be paid an attention.
              </span><br/><br/>
            </p>
          </div>

        <h3 id="getCandidate">Get candidates from your own GWAS summary statistics</h3>
        <p>You can obtain functional annotation of SNPs and map them to genes.
          By setting parameters, you are also able to prioritize genes by your criterion.</p>
        <div style="margin-left: 40px">
          <p><h4><strong>1. Registration/Login</strong></h4>
            If you haven't registered yet, please do so from <a href="{{ url('/register') }}">Register</a>.<br/>
            Before you submit your GWAS summary statistics, please log in to your account.<br/><br/>
            <img src="{!! URL::asset('/image/homereg.png') !!}" style="max-width:80%"/><br/>
          </p>

          <p><h4><strong>2. Submit new job at <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a></strong></h4>
            GWAS summary statistics is a mandatory input and a variety of file formats are supported.
            Please refer the section of input files of this tutorial <a href="{{ Config::get('app.subdir') }}/tutorial#prepare-input-files">here</a> for details.
            If your file is an ouput of PLINK, SNPTEST or METAL, you can directory submit the file (no need to chagne columns).<br/>
            Optionally, if you already know lead SNPs and you want to use them as lead SNPs, you can upload a file with 3 columns; rsID, chromosome and position.<br/>
            In addition, if you are interested in specific genomic regions, you can also provide them by uploading a file with 3 columns; chromosome, start and end position.<br/><br/>
            <img src="{!! URL::asset('/image/newjobfile.png') !!}" style="max-width:80%"/><br/>
          </p>

          <p><h4><strong>3. Set parameters</strong></h4>
            Please check your parameters carefully. Default setting perform identification of lead SNPs at r2=0.6 and maps SNPs to genes up to 10kb apart.<br/>
            To filter SNPs by functional annotations and use eQTL mapping, please refer the parmeters section from <a href="{{ Config::get('app.subdir') }}/tutorial#parameters">here</a>.<br/>
            If all inputs are valid, 'Submit Job' button will be activated. Once you submit a job, this will be listed in My Jobs.<br/><br/>
            <img src="{!! URL::asset('/image/submitjob.png') !!}" style="max-width:70%"/><br/>
          </p>

          <p><h4><strong>4. Check your results</strong></h4>
            One process is done, you will receive an email.
            Unless an error occured during the process, the email includes the link to results page (this again requires login).
            You can also access to the results page from My Job list.

            The result page display 4 additional side bars.<br/>
            <strong>Genome-wide plots</strong>: Manhattan plots and Q-Q plots for GWAS sumary statistics and gene-based test by MAGMA.<br/>
            <strong>Summary of results</strong>: Summarised results such as the number of candidate SNPs and mapped genes for overall and per genomic loci.<br/>
            <strong>Results</strong>: Tables of lead SNPs, genomic risk loci, candidate SNPs with annotations, eQTLs (only when eQTL mapping is performed), mapped genes and GWAS-catalog reported SNPs matched with candidate SNPs.
            You can also create interactive regional plot with functional annotations from this tab.<br/>
            <strong>Downloads</strong>: Download Results as text files.<br/>
            Details for each panel are described in this tutorial <a href="{{ Config::get('app.subdir') }}/tutorial#outputs">here</a>.<br/><br/>
            <img src="{!! URL::asset('/image/result.png') !!}" style="max-width:70%"/><br/><br/>
            <img src="{!! URL::asset('/image/resultpanels.png') !!}" style="max-width:90%"/><br/>
          </p>
        </div>
        <br/>
        <h3 id="geneQuery">Tissue specific gene expression and shared biological functions of a list of genes</h3>
        <p>In the <a href="{{ Config::get('app.subdir') }}/gene2func"><strong>GENE2FUNC</strong></a>, you can check expression in different tissue types, tissue specificity and enrichment of publicly available gene sets of genes of interest.<br/>
          You can use mapped genes from SNP2GENE by clicking the button in the result page (Results tab).<br/>
          You have to provide a list of genes of interest and background genes (for hypergeometric test).<br/><br/>
          <img src="{!! URL::asset('/image/gene2funcSubmit.png') !!}" style="max-width:70%"/><br/>
        </p>
        <div style="margin-left: 40px">
          <p>
            Once genes are submitted, four extra side bars wil be shown.<br/>
            <strong>Gene Expression</strong>: The heatmap of gene expression of 53 tissue types from GTEx.<br/>
            <strong>Tissue Specificity</strong>: The bar plots of enrichment of differentially expressed genes across tissue types.<br/>
            <strong>Gene Sets</strong>: Plots and tables of enriched gene sets.<br/>
            <strong>Gene Table</strong>: Table of input genes with lnks to OMIM, Drugbank and GeneCards.<br/>
            Details for each panel are described in this tutorial <a href="{{ Config::get('app.subdir') }}/tutorial#gene2funcOutputs">here</a>.<br/><br/>
            <img src="{!! URL::asset('/image/gene2funcResults.png') !!}" style="max-width:70%"/><br/>
          </p>
        </div>
        <br/>
      </div>
    </div>

    <div id="snp2gene" class="sidePanel container" style="padding-top:50;">
      <h2>SNP2GENE</h2>
      <div style="margin-left: 40px;">
        <h3 id="prepare-input-files">Prepare Input Files</h3>
        <h4>1. GWAS summary statistics</h4>
        <p>GWAS summary statistics is a mandatory input of <strong>SNP2GENE</strong> process.
          FUMA accept various types of format. For example, PLINK, SNPTEST and METAL output formats can be used as it is.
        </p>

        <div style="margin-left: 40px;">
          <h4>acceptable file format</h4>
          <p>The input file must include P-value and either rsID or chromosome + genetic position on hg19 reference genome.
            Alleles are not mandatory but if only one allele is provided, that is considered as affected allele.
            When two alleles are provided, affected allele will be defined depending on header.
            If alleles are not provided, they will be extracted from 1000 genomes referece panel as minor allele as affected alleles.
          </p>
          <p>Column names will be matched with the following headers (case insensitive).</p>
          <ul>
            <li><strong>SNP | markername | rsID</strong>: rsID</li>
            <li><strong>CHR | chromosome | chrom</strong>: chromosome</li>
            <li><strong>BP | pos | position</strong>: genomic position (hg19)</li>
            <li><strong>A1 | alt | effect_allele | allele1 | alleleB</strong>: affected allele</li>
            <li><strong>A2 | ref | non_effect_allele | allele2 | alleleA</strong>: another allele</li>
            <li><strong>P | pvalue | p-value | p_value | frequentist_add_pvalue | pval</strong>: P-value (Mandatory)</li>
          </ul>
          <p style="color: #000099;"><i class="fa fa-info"></i> Please be carefull for alleles header in whcih A1 and Allele1 are effect allele while alleleA is non-effect allele.<br/>
            Even if wrong labels are proveded for alleles, it does not affect any annotation and prioritization results, but please be aware of that when you interpret results.
          </p>
          <p>Delimiter can be any of white space including single space, multiple space and tab.
            Because of this, each element including column names must not include any space.
          </p>
          <p>The column of chromosome can be string like "chr1" or just integer like 1.
            When "chr" is attached, this will be removed in output files.
            When the input file contains chromosome X, this will be encoded as chromosome 23, however, input file can be leave as "X".
          </p>
          <p>rsID in input file could be in any dbSNP build but will be updated to build 146 in the outout files.
          </p>
          <p>Extra columns will be ignored and will not be included in any output.
          </p>
          <p>Any rows start with "#" wiil be ignored.
          </p>
        </div>
        <hr>
        <!-- <div style="padding-left:40px;"> -->
          <h4>Note and Tips</h4>
          <p>The pipeline only support human genome hg19.
            If your input file is not based on hg19, please update the genomic position using liftOver from UCSC.
            However, there is an option for you!! When you provide only rsID without chromosome and genomic position, FUMA will extract them from 1000 genomes reference panel based on hg19.
            To do this, remove columns of chromosome and genomic position of rename headers to ignore those columns.
            Note that extracting chromosme and genomic position will take extra time.
          </p>
        <!-- </div> -->
        <hr>

        <h4>2. Pre-defined lead SNPs</h4>
        <p>This is an optional input file. If you wnat to specify lead SNPs, input file should have the following 3 columns.<br/>
        </p>
        <div style="padding-left: 40px:">
          <ul>
            <li><strong>rsID</strong> : rsID of the lead SNPs</li>
            <li><strong>chr</strong> : chromosome</li>
            <li><strong>pos</strong> : genomic position (hg19)</li>
          </ul>
          <p style="color: #000099;"><i class="fa fa-info"></i>
            The order of column has to be the same as shown above but header could be anything.
            Extra columns will be ignored.
          </p>
        </div>
        <hr>
          <h4>Note and Tips</h4>
          <p>This option would be useful when<br/>
            1. You have lead SNPs of interest but they do not reach significant P-value threshold.<br/>
            2. You are only interested in specific lead SNPs and do not want to identify additional lead SNPs which are independent.
            In this case, you also have to UNCHECK option of <code>Identify additional independent lead SNPs</code>.
          </p>
        <hr>

        <h4>3. Pre-defined genomic region</h4>
        <p>This is an option input file. If you want to analyse only specific genomic region of GWAS, input file shoud have 3 columns.<br/>
        </p>
        <div style="padding-left: 40px:">
          <ul>
            <li><strong>chr</strong> : chromosome</li>
            <li><strong>start</strong> : start position of the genomic region of interest (hg19)</li>
            <li><strong>end</strong> : end position of the genomic region of interest (hg19)</li>
          </ul>
          <p style="color: #000099;"><i class="fa fa-info"></i>
            The order of column has to be the same as shown above but header could be anything.
            Extra columns will be ignored.
          </p>
        </div>
        <hr>
          <h4>Note and Tips</h4>
          <p>This option would be useful when you have already done some followup analyses of your GWAS and are interested in specific genomic regions.<br/>
            When pre-defined genomic region is provided, regardless of parameters, only lead SNPs and SNPs in LD with them within provided regions will be reported in outputs.
          </p>
        <hr>
        <br/>

        <h3 id="parameters">Parameters</h3>
        <p>FUMA provides a variety of parameters.
          Default setting will perform naive positional mapping which maps all independent lead SNPs and SNPs in LD to genes up to 10kb apart.
          In this section, every parameter will be described details.
        </p>
        <p>Each of user inputs and parameters have status as described below.
          Please make sure all input has non-red status, otherwise the submit button will not be activated.<br/><br/>
          <span class="alert alert-info" style="padding: 5px;">
            This is for optional inputs/parameters.
          </span><br/><br/>
          <span class="alert alert-success" style="padding: 5px;">
            This is the message if everything is fine.
          </span><br/><br/>
          <span class="alert alert-danger" style="padding: 5px;">
            This is the message if the input/parameter is mandatory and not given or invalid input is given.
          </span><br/><br/>
          <span class="alert alert-warning" style="padding: 5px;">
            This is the warning message for the input/parameter. It can be ignored but need to be paid an attention.
          </span><br/><br/>

        </p>
        <br/>
        <h4 id="input-files">1. Input files</h4>
        <div style="margin-left: 40px;">
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
                <td>Input file of GWAS summary statistics.
                  Only plain test is acceptable. Please uncompress archive files.
                  As well as full results of GWAS summary statistics, subset of results can also be used.
                  e.g. If you would like to look up specific SNPs, you can filter out other SNPs.
                  Please follow <a href="{{ Config::get('app. subdir') }}/tutorial#prepare-input-files">Input files</a> section for file format.
                </td>
                <td>File upload</td>
                <td>none</td>
              </tr>
              <tr>
                <td>Pre-defined lead SNPs</td>
                <td>Optional</td>
                <td>Optional pre-defined lead SNPs. The file should have 3 coulmns, rsID, chromsome and position.</td>
                <td>File upload</td>
                <td>none</td>
              </tr>
              <tr>
                <td>Identify additional lead SNPs</td>
                <td>Optional only when predefined lead SNPs are provided</td>
                <td>If this option is CHECKED, FUMA will identify additional independent lead SNPs after defined LD block of pre-defined lead SNPs.
                  Otherwise, only given lead SNPs and SNPs in LD of them will be used for further annotation.
                </td>
                <td>Check</td>
                <td>Checked</td>
              </tr>
              <tr>
                <td>Pre-defined genetic region</td>
                <td>Optional</td>
                <td>Optional pre-defined genomic regions.
                  FUMA only looks provided regions to identify lead SNPs and SNPs in LD of them.
                  If you are only interested in specific regions, this will increase a speed of job.
                </td>
                <td>File upload</td>
                <td>none</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h4>2. Parameters for lead SNPs and candidate SNPs identification</h4>
        <div style="margin-left: 40px;">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Parameter</th>
                <th>Mandatory</th>
                <th style="width: 40%;">Description</th>
                <th>Type</th>
                <th>Default</th>
                <th style="width: 20%;">Direction</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                <td>Sample size (N)</td>
                <td>Mandatory</td>
                <td>the total number of samples in the GWAS.
                  This is only used for MAGMA to compute gene-based test.</td>
                <td>Integer</td>
                <td>none</td>
                <td>Does not affect any candidates</td>
              </tr>
              <tr>
                <td>Maximum lead SNP P-value (&le;)</td>
                <td>Mandatory</td>
                <td>FUMA identifies lead SNPs wiht P-value less than or equal to this threshold and independent from each other.
                  This should not be changed unless GWAS is under-powered and only a few peaks are significant.
                </td>
                <td>numeric</td>
                <td>5e-8</td>
                <td><span style="color: blue;">lower</span>: decrease #lead SNPs. <br/>
                  <span style="color:red;">higher</span>: increase #lead SNPs.
                </td>
              </tr>
              <tr>
                <td>Minimum r<sup>2</sup> (&ge;)</td>
                <td>Mandatory</td>
                <td>The minimum correlation to be in LD of a lead SNP.
                  Independent lead SNPs have r<sup>2</sup> less than this threshold from each other.
                </td>
                <td>numeric</td>
                <td>0.6</td>
                <td><span style="color:red;">higher</span>: decrease #candidate SNPs and increase #lead SNPs.<br/>
                  <span style="color: blue;">lower</span>: increase #candidate SNPs and decrease #lead SNPs.
                </td>
              </tr>
              <tr>
                <td>Maximum GWAS P-value (&le;)</td>
                <td>Mandatory</td>
                <td>This is the threshold for candidate SNPs in LD of lead SNPs.
                  This will be applied only for GWAS-tagged SNPs while SNPs which do not exist in GWAS input but extracted from 1000 genoms reference will not be applied this filtering.
                </td>
                <td>numeric</td>
                <td>0.05</td>
                <td><span style="color:red;">higher</span>: decrease #candidate SNPs.<br/>
                  <span style="color: blue;">lower</span>: increase #candidate SNPs.
                </td>
              </tr>
              <tr>
                <td>Population</td>
                <td>Mandatory</td>
                <td>The population of reference panel to compute r<sup>2</sup> and MAF.
                  Currently five populations are available from 1000 genomes Phase 3.
                </td>
                <td>Select</td>
                <td>EUR</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Include 1000 genomes reference variants</td>
                <td>Mandatory</td>
                <td>If Yes, all SNPs in strong LD with any of lead SNPs including non-GWAS-tagged SNPs are selected as cnadidate SNPs.</td>
                <td>Yes/No</td>
                <td>Yes</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Minimum MAF (&ge;)</td>
                <td>Mandatory</td>
                <td>The minimum Minor Allele Frequency of candidate SNPs.
                  This filter also applies to lead SNPs.
                  If there is any pre-defined lead SNPs with MAF less than this threshold, those SNPs will be skipped.
                </td>
                <td>numeric</td>
                <td>0.01</td>
                <td><span style="color:red;">higher</span>: decrease #candidate SNPs.<br/>
                   <span style="color: blue;">lower</span>: increase #candidate SNPs.
                 </td>
              </tr>
              <tr>
                <td>Maximum distance of LD blocks to merge (&le;)</td>
                <td>Mandatory</td>
                <td>This is the maximum distance between LD blocks from independent lead SNPs to merge into a genomic locus.
                  When this is set at 0, only physically overlapped LD blocks are merged.
                  Defining of genomic loci is independent from identification of candidate SNPs.
                  Therefore, this does no change any results of candidates, however those genomic loci will be used to summarize results.
                </td>
                <td>numeric</td>
                <td>250kb</td>
                <td><span style="color:red;">higher</span>: decrease #genomic loci.<br/>
                   <span style="color: blue;">lower</span>: increase #genomic loci.
                 </td>
              </tr>
            </tbody>
          </table>
        </div>

        <h4>3. Parameters for gene mapping</h4>
        <div style="margin-left: 40px;">
          <p>There are two options for gene mapping; positional and eQTL mappings. By default, positional mapping with maximum distance 10kb is performed.
            Since parameters in this section largely affect the result of mapped genes, please set carefully.
          </p>
          <br/>
          <h4>3.1 Positional mapping</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Parameter</th>
                <th>Mandatory</th>
                <th style="width:40%;">Description</th>
                <th>Type</th>
                <th>Default</th>
                <th style="width:20%;">Direction</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Position mapping</td>
                <td>Optional</td>
                <td>Whether perform positional mapping or not.
                  Positional mapping is based on distance from SNPs to genes.
                  Users can choose from distance based and annotation based maping in the following parameters.
                </td>
                <td>Check</td>
                <td>Checked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Distance based mapping</td>
                <td>Optional</td>
                <td>Map SNPs to genes based on physical distance.
                  When this option is selected, the following option (maximum distance to genes) is mandatory.
                </td>
                <td>Check</td>
                <td>Checked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Maximum distance to genes (&le;)</td>
                <td>Optional</td>
                <td>The maximum distance to map SNPs to genes.
                  This option is used only when <code>Distance based mapping</code> is CHECKED.
                  When this is set at 0, 1 kb up- and down-stream (3UTR and 5UTR) will be included as 0.
                </td>
                <td>numeric</td>
                <td>10kb</td>
                <td><span style="color:red;">higher</span>: increase #mapped genes.<br/>
                   <span style="color: blue;">lower</span>: decrease #mapped genes.
                </td>
              </tr>
              <tr>
                <td>Annotation based mapping</td>
                <td>Optional</td>
                <td>Instead of distance based mapping which is purely based on phisical distance, annotation based mapping maps only SNPs have selected functional consequence on genes.
                  Annotations are based on ANNVAR outputs.
                  For example, when exonic is slected, only genes with exonic SNPs which are in LD of lead SNPs will be prioritized.
                </td>
                <td>Multiple selection</td>
                <td>none</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>

          <h4>3.2 eQTL mapping</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Parameter</th>
                <th>Mandatory</th>
                <th style="width:40%;">Description</th>
                <th>Type</th>
                <th>Default</th>
                <th style="width:20%;">Direction</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>eQTL mapping</td>
                <td>Optional</td>
                <td>Whether perform eQTL mapping or not.
                  eQTL mapping maps SNPs to genes which likely affect expression of thoses genes up to 1 Mb (cis-eQTL).
                  eQTLs are highly tissue specific and tissue types can be selected in the following option.
                </td>
                <td>Check</td>
                <td>Unchecked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Tissue types</td>
                <td>Mandatory if <code>eQTL mapping</code> is activated</td>
                <td>All available tissue types with data sources are shown in the select box.
                  <code>Tissue type</code> selection contain individual tissue types and
                  <code>General tissue types</code> contain broad area of organ and each general tissue contains multiple individual tissue types.
                </td>
                <td>Multiple selection</td>
                <td>none</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Significant eQTL only (FDR&le;0.05)</td>
                <td>Optional</td>
                <td>Map only significant SNP-gene pair at FDR 0.05.
                  The method to compute FDR is different between data sources.
                  Please refer original data source from <a href="{{ Config::get('app.subdir') }}/links">links</a> for details.
                </td>
                <td>Check</td>
                <td>Checked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>eQTL maximum P-value (&le;)</td>
                <td>Mandatory if <code>Significant eQTL only</code> is UNCHECKED</td>
                <td>This can be used as threshold of eQTL uncorrected P-value.</td>
                <td>numeric</td>
                <td>1e-3</td>
                <td><span style="color:red;">higher</span>: increase #eQTLs and #mapped genes.<br/>
                   <span style="color: blue;">lower</span>: decrease #eQTLs and #mapped genes.</td>
              </tr>
            </tbody>
          </table>
          <h4>3.3 Functional annotation filtering</h4>
          <p>Both positional and eQTL mappings have the following options separately for the filtering of SNPs based on functional annotation.</p>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Parameter</th>
                <th>Mandatory</th>
                <th style="width:40%;">Description</th>
                <th>Type</th>
                <th>Default</th>
                <th style="width:20%;">Direction</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>CADD score</td>
                <td>Optional</td>
                <td>Whether perform filtering of SNPs by CADD score or not.<br/>
                    CADD score is the score of deleteriousness of SNPs predicted by 63 fucntional annotations.
                    12.37 is the threshold to be deleterious suggested by Kicher et al (2014).
                    Plesase refer original publication for details from <a href="{{ Config::get('app.subdir') }}/links">links</a>.
                </td>
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
                <td><span style="color:red;">higher</span>: less SNPs will be mapped to genes.<br/>
                   <span style="color: blue;">lower</span>: more SNPs will be mapped to genes.</td>
                </td>
              </tr>
              <tr>
                <td>RegulomeDB score</td>
                <td>Optional</td>
                <td>Whether perform filtering of SNPs by RegulomeDB score or not.<br/>
                  RegulomeDB score is a categorical score representing regulatory functionality of SNPs based on eQTLs and chromatin marks.
                  Plesase refer original publication for details from <a href="{{ Config::get('app.subdir') }}/links">links</a>.
                </td>
                <td>Check</td>
                <td>Unchecked</td>
                <td>-</td>
              </tr>
              <tr>
                <td>Minimum RegulomeDB score (&gt;=)</td>
                <td>Mandatory if <code>RegulomeDB score</code> is checked</td>
                <td>RegulomeDB score is a categorical (from 1a to 7).
                  Score 1a means that those SNPs are most likely affect regulatory elements and 7 means that those SNPs do not have any annotations.
                  SNPs are recorded as NA if they are not present in the database.
                  These SNPs will be filtered out when RegulomeDB score filtering is performed.</td>
                <td>string</td>
                <td>7</td>
                <td><span style="color:red;">higher</span>: more SNPs will be mapped to genes.<br/>
                   <span style="color: blue;">lower</span>: less SNPs will be mapped to genes.</td>
                </td>
              </tr>
              <tr>
                <td>15-core chromatin state</td>
                <td>Optional</td>
                <td>Whether perform filtering of SNPs by chromatin state or not.<br/>
                  The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states predicted by ChromHMM based on 5 chromatin marks for 127 epigenomes.
                </td>
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
                <td>The maximum state to filter SNPs. Between 1 and 15.
                  Generally, bewteen 1 and 7 is open state.
                </td>
                <td>numeric</td>
                <td>7</td>
                <td><span style="color:red;">higher</span>: more SNPs will be mapped to genes.<br/>
                   <span style="color: blue;">lower</span>: less SNPs will be mapped to genes.</td>
                </td>
              </tr>
              <tr>
                <td>Method for 15-core chromatin state filtering</td>
                <td>Mandatory if <code>15-core chromatin state</code> is checked</td>
                <td>When multiple tissue/cell types are selected, either
                  <code>any</code> (a SNP has state above than threshold in any of selected tissue/cell types),
                  <code>majority</code> (a SNP has state above than threshold in majority (&ge;50%) of selected tissue/cell type), or
                  <code>all</code> (a SNP has state above than threshold in all of selected tissue/cell type).
                </td>
                <td>Selection</td>
                <td>any</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
          <br/>
        </div>

        <h4>4. Gene types</h4>
        <div style="margin-left: 40px;">
          <p>Biotype of genes to map can be selected. Please refer Ensembl for details of biotypes.</p>
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
                <td>Gene type</td>
                <td>Mandatory</td>
                <td>Gene type to map.
                  This is based on gene_biotype obtained from BioMart of Ensembl build 85.
                  Please refer <a href="http://vega.sanger.ac.uk/info/about/gene_and_transcript_types.html">here</a> for details
                </td>
                <td>Multiple selection.</td>
                <td>Protein coding genes.</td>
              </tr>
            </tbody>
          </table>
          <br/>
        </div>

        <h4 id="mhc-region">5. MHC region</h4>
        <div style="margin-left: 40px;">
          <p>MHC region is often excluded due to the complicated LD structure. Therefore, this option is checked by default. Please uncheck to include MHC region. It doesn&#39;t change any results if there is no significant hit in the MHC region.</p>
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
                <td>Exclude MHC region</td>
                <td>Optional</td>
                <td>Whether exclude MHC region or not. Default region is defined as between "MOG" and "COL11A2" genes.</td>
                <td>Check</td>
                <td>Checked</td>
              </tr>
              <tr>
                <td>Extended MHC region</td>
                <td>Optional</td>
                <td>Use specified MHC region to exclude (oftenly used to exclude extended region, but shorter region can also be provided.)
                  The input format should be like "25000000-34000000".
                </td>
                <td>Text</td>
                <td>Null</td>
              </tr>
            </tbody>
          </table>
          <br/>
        </div>
        <h4>6. Title of job submission</h4>
        <div style="margin-left: 40px;">
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
                <td>Title</td>
                <td>Optional</td>
                <td>This is not mandatory but this would be usefull to keep track your jobs.</td>
                <td>Text</td>
                <td>Null</td>
              </tr>
            </tbody>
          </table>
        </div>
        <br/>

        <h3 id="outputs">Outputs</h3>
        <p>One process is done, you will receive an email.
          Unless an error occured during the process, the email includes the link to results page (this again requires login).
          You can also access to the results page from My Job list.
          The result page display 4 additional side bars.
        </p><br/>
        <img src="{!! URL::asset('/image/result.png') !!}" style="max-width:80%"/><br/><br/>

        <h4>2. Genome-wide plots</h4>
        <p>This panel displays manhattan plots and Q-Q plots for both GWAS summary statistics (input file) and gene-based association test.<br/>
          Images are downloadable as PNG files.
        </p>
        <ul>
          <li>Plots for GWAS summary statistics<br/>
            To minimize overlapped data points in the plot, they are filtered based on the following criteria.
            Please be aware that, since majority od overlapped data points are not displayed in the plot, those plots are approximated plots.
            <ul>
              <li>Manhattan plot: Overlapped data points (SNPs) were filtered to make the plot one data point per pixel only when average data points per pixel (x-axis) across y-axis is above 1.
                For each pixel, data point was randomly selected.
                This filtering was only performed SNPs with P-value &ge; 1e-5 to avoid over filtering.</li>
              <li>Q-Q plot: Overlapped data points (SNPs) were filtered such that one data point per pixel.
                For each pixel, data point was randomly selected.
                This filtering was only performed SNPs with P-value &ge; 1e-5 to avoid over filtering.</li>
            </ul>
          </li>
          <li>Plots for gene-based test<br/>
            Gene based test was performed by using MAGMA with default setting.
            SNPs were assigned to the genes obtained from Ensembl build 85 (only protein-coding genes).
            MAGMA results are available from the download button.
          </li>
        </ul>
        <br/>
        <img src="{!! URL::asset('/image/snp2geneGWplot.png') !!}" style="max-width:80%"/><br/><br/>
        <br/>

        <h4>3. Summary of results</h4>
        <p>This panel shows summary of your GWAS input. Images are downloadable as PNG files.</p>
        <ul>
          <li>Summary of SNPs and mapped genes<ul>
            <li><strong>#lead SNPs</strong>: The number of independent lead SNPs identified.</li>
            <li><strong>#Intervals</strong>: The number of genomic loci defined from the independent lead SNPs.</li>
            <li><strong>#candidate SNPs</strong>: The number of candidate SNPs which are in LD (given r<sup>2</sup>) of one of the independet lead SNPs.
              This includes non-GWAS tagged SNPs which is extracted from 1000 genomes reference panel.
              When SNPs were filtered based on functional annotation for gene mapping, this number is before the functional filtering.</li>
            <li><strong>#candidate GWAS tagged SNPs</strong>: The number of candidate SNPs (described above) which are tagged in GWAS (exists in your input file).</li>
            <li><strong>#mapped genes</strong>: The number of genes mapped by user-defined parameters.</li>
          </ul></li>
          <li>Positional annotation of candidate SNPs<br/>
            This is a histogram of the number of SNPs per functional consequences on genes.
            When SNPs have more than one (different) annotations, those are counted for each annotation.
            SNPs assigned NA might be because alleles do not matche with fasta file in ANNOVAR Ensembl genes.
          </li>

          <li>Summary per genomic locus<br/>
            This histogram display, the size of loci,  the number of candidate SNPs, the number of mapped genes and number of genes phisically locating within define locus per genomic locus.
          </li>
        </ul>
        <br/>
        <img src="{!! URL::asset('/image/snp2geneSummary.png') !!}" style="max-width:80%"/><br/><br/>
        <br/>

        <h4>4. Result tables</h4>
        <p>This panel contain multiple tables of your results.
          Here are descriptions for columns in each tables. Each columns will be described in the following section.
          Downloadable text files have the same column as shown in the interface unless methioned.
        </p>
        <p>By clicking one of the rows of tables of lead SNPs or genomic risk loci, it will create regional plots of candidate SNPs (GWAS P-value).
          Optionally, regional plot with genes and functional annotations can be created from the panel at the bottom of the page.
        </p>
        Options for regional plot with annotations.<br/>
        <ul>
          <li>GWAS association statistics: input P-value</li>
          <li>CADD score</li>
          <li>RegulomeDB score</li>
          <li>15-core chromatin state: tissue/cell types have to be selected.</li>
          <li>eQTLs: This is only available when eQTL mapping is performed. eQTLs are plotted per gene and colored per tissue types.</li>
        </ul>
        <br/>
        <img src="{!! URL::asset('/image/snp2geneResults.png') !!}" style="max-width:80%"/><br/>
        <img src="{!! URL::asset('/image/snp2geneAnnotPlot.png') !!}" style="max-width:70%"/><br/><br/>
        <br/>
        <h4>Description of tables</h4>
        <div style="margin-left: 40px;">
          <ul>
            <li><p>lead SNPs / leadSNPs.txt</p>
            <p>All independent lead SNPs identified by FUMA.</p>
            <ul>
              <li><strong>No</strong> : Index of lead SNPs</li>
              <li><strong>Interval</strong> : Index of assigned genomic interval (risk locus). This matches with the index of interval table.</li>
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
            <li><p>Genomic risk loci / intervals.txt</p>
            <p>Genomic risk loci (intervals) defined from independent lead SNPs.
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
              <li><strong>CADD</strong> : CADD score which is computed based on 67 annotations. The higher score, the more deleterious the SNP is. 12.37 is the suggested threshold by <a href="https://www.ncbi.nlm.nih.gov/pubmed/24487276" target="_blank">Kicher et al.</a></li>
              <li><strong>RDB</strong> : RegulomeDB score which is the categorical score (from 1a to 7). 1a is the highest score that the SNP has the most biological evidence to be regulatory element.</li>
              <li><strong>E001~E129</strong> : Chromatin state predicted by ChrHMM. ID of tissue cell types and description of 15 states are available from <a href="/IPGAP/link">Link</a>.</li>
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
            <li><p>Mapped genes / genes.txt</p>
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
            <li><p>Parameters / params.config</p>
            <p>The table of input parameters. The downloadable file is config file with INI format.</p>
            <ul>
              <li><strong>created_at</strong> : Date of job created</li>
              <li><strong>title</strong> : Job title</li>
              <li><strong>gwasfile</strong> : File name of GWAS summary statistics</li>
              <li><strong>leadSNPsfile</strong> : File name of pre-defined lead SNPs if provided.</li>
              <li><strong>addleadSNPs</strong> : 1 if option is checked, 0 otherwise. If pre-defined lead SNPs are not provided, it is always 1.</li>
              <li><strong>regionsfile</strong> : File name of pre-defined genetic regions if provided.</li>
              <li><strong>N</strong> : Sample size of GWAS</li>
              <li><strong>exMHC</strong> : 1 to exclude MHC region, 0 otherwise</li>
              <li><strong>extMHC</strong> : user defined MHC region if provided, NA otherwise</li>
              <li><strong>genetype</strong> : All selected gene type.</li>
              <li><strong>leadP</strong> : the maximum threshold of P-value to be lead SNP</li>
              <li><strong>r2</strong> : the minimum threshold for SNPs to ne in LD of the lead SNPs</li>
              <li><strong>gwasP</strong> : the maximum threshold of P-value to be candidate SNP</li>
              <li><strong>pop</strong> : The population of reference panel</li>
              <li><strong>MAF</strong> : the minimum minor allele frequency based on 1000 genome reference of given population</li>
              <li><strong>Incl1KGSNPs</strong> : 1 to include non-GWAS-tagged SNPs from reference panel, 0 otherwise</li>
              <li><strong>mergeDist</strong> : The maximum distance between LD blocks to merge into interval</li>
              <li><strong>posMap</strong> : 1 to perform positional mapping, 0 otherwise</li>
              <li><strong>posMapWindow</strong> : 1 to perform positional mapping based on distance to the genes, 0 otherwise</li>
              <li><strong>posMapWindowSize</strong> : If window based positional mapping is performed, which distance (kb) as the maximum. If window based mapping is 0, this parameter set at 10 as default but will be ignored.</li>
              <li><strong>posMapAnnot</strong> : Positional annotations selected if window based mapping is 0.</li>
              <li><strong>posMapCADDth</strong> : The minimum CADD score for SNP filtering</li>
              <li><strong>posMapRDBth</strong> : The minimum RegulomeDB score for SNP filtering</li>
              <li><strong>posMapChr15</strong> : Select tissue/cell types, NA otherwise</li>
              <li><strong>posMapChr15Max</strong> : The maximum 15-core chromatin state</li>
              <li><strong>posMapChr15Meth</strong> : The method of chromatin state filtering</li>
              <li><strong>eqtlMap</strong> : 1 to perform eQTL mapping, 0 otherwise</li>
              <li><strong>eqtlMaptss</strong> : Selected tissue typed for eQTL mapping</li>
              <li><strong>eqtlMapSig</strong> : 1 to use only significant snp-gene pairs, 0 otherwise</li>
              <li><strong>eqtlMapP</strong> : The P-value threshold for eQTLs if <code> eqtlMap significant only</code> is not selected.</li>
              <li><strong>eqtlMapCADDth</strong> : The minimum CADD score for SNP filtering</li>
              <li><strong>eqtlMapRDBth</strong> : The minimum RegulomeDB score for SNP filtering</li>
              <li><strong>eqtlMapChr15</strong> : Select tissue/cell types, NA otherwise</li>
              <li><strong>eqtlMapChr15Max</strong> : The maximum 15-core chromatin state</li>
              <li><strong>eqtlMapChr15Meth</strong> : The method of chromatin state filtering</li>
            </ul>
            </li>
          </ul>
        </div>
        <br/>

        <h4>5. Downloads</h4>
        <p>All results are downloadable as text file.
          Columns are same as descrived above.<br/>
        </p>

        <div style="padding-left: 40px;">
          When SNP table is downloaded, <strong>ld.txt</strong> will be also downloaded at the same tome.
          This file contains r2 computed from 1000G reference panel for all pair of one of the independent lead SNPs and all other SNPs within the LD.
          <ul>
            <li><strong>SNP1</strong> : One of the independent lead SNPs</li>
            <li><strong>SNP2</strong> : One of the candidate SNPs (including lead SNPs)</li>
            <li><strong>r2</strong> : r2 computed using 1000G reference panel of user defined population</li>
          </ul>
        </div>
        <br/>
      </div>
    </div>

    <div id="gene2func" class="sidePanel container" style="padding-top:50;">
      <h2>GENE2FUNC</h2>
      <div style="padding-left: 40px;">
        <h3>Submit genes</h3>
        <div style="padding-left: 40px;">
          <h4>Option 1. Use mapped genes from SNP2GENE</h4>
          <p>If you want to use mapped genes from SNP2GENE, just click a button in Mapped genes panel of the result page.
            It will open a new tab and automatically starts analyses.
            This will take all mapped genes and use background genes with selected gene types for gene mapping (such as "protein-coding" or "ncRNA").
            Method of multiple test correction (FDR BH), adjusted P-value cutoff (0.05) and minimum number of overlapped genes (2) are set at default values.
            These options can be fixed by resubmitting query (click "Submit" button in New Query tab).
          </p>
          <img src="{!! URL::asset('/image/snp2genejump.png') !!}" style="max-width:80%"/><br/>

          <h4>Option 2. Use a list of genes of interest</h4>
          <p>To analyse your genes, you have to prepare list of genes as either ENSG ID, entrez ID or gene symbol.
          Genes can be provided in the text are (one gene per line) or uploading file in the left panel. When you upload a file, genes have to be in the first column with header. Header can be anything (even just a new line is fine) but start your genes from second row.</p>
          <p>To analyse your genes, you need to specify background genes. You can choose from the gene types which is the easiest way. However, in the case that you need to use specific background genes, please provide them either in the text area of by uploading a file of the right panel.
          File format should be same as described for genes on interest.</p>
          <img src="{!! URL::asset('/image/gene2funcSubmit.png') !!}" style="max-width:80%"/>
        </div>

        <h3 id="gene2funcOutputs">Results and Outputs</h3>
        <div style="padding-left: 40px;">
          <h4>1. Gene Expression Heatmap</h4>
          <p>
            The heatmap displays two expression values.<br/>
            1) <b>Average RPKM per tissue</b> : This is averaged RPKM per tissue per gene following to winsorization at 50 and log 2 transformation with pseudocount 1.
            This allows to compare across tissues and genes. Hence, cells filled in red represent higher expression compared to cells filled in blue.<br/>
            2) <b>Average of normarized RPKM per tissue</b> : This is average of normalized expression (zero mean across samples) following to log 2 transformation of RPKM with pseudocount 1.
            This allows to compare scross tissues (horizontal comparison), however expression values of genes within a tissue (vertial comparison) are not comparable.
            Hence, cells filled in red represents higher expression of the genes in a corresponding tissue compared to other tissue, but it DOES NOT represent higher expression compared to other genes.
          </p>
          <p>Tissues (column) and genes (row) can be ordered by alphabetically or cluster (hiarachial clustering). <br/>
            The heatmap is downloadable as PNG file. Note that currentlly displaying image will be downloaded.
          </p>
          <img src="{!! URL::asset('/image/gene2funcHeatmap.png') !!}" style="max-width:80%"/>
          <br/><br/>

          <h4>2. Tissue specificity</h4>
          <p>
             Differentially expressed gene (DEG) sets for 53 tissue types from GTEx were contracted by performing two-sided t-test for any one of tissues agains all others.
             For this, expresstion values were normalized (zero-mean) following to log 2 transformation of RPKM.
             Genes which with P-value &le; 0.05 after bonferroni correction and absolute log Fold Change &ge; 0.58 were defined as differentially expressed genes in a given tissue compared to others.
             On top of DEG, up-regrated DEG and down-regulated DEG were also contracted by taking sign of t-statistics into account.
             The same process was performed for 30 general tissue types.<br/>
          </p>
          <p>Input genes were tested against each of DEG sets.
            Significant enrichment at FDR &le; 0.05 are coloured in red.<br/>
            Results and images are downloadable as text files and PNG files.
          </p>
          <img src="{!! URL::asset('/image/gene2funcTs.png') !!}" style="max-width:80%"/>
          <br/><br/>

          <h4>3. Gene Sets</h4>
          <p>
            Hypergeometric tests are performed to test if genes of interest are overrepresented in any of gene sets.
            Multiple test correction is performed per category, (i.e. canonical pathways, GO biological processes and so on, separately).
            Gene sets were obtained from MsigDB, WikiPathways and reported genes from GWAS-catalog.
          </p>
          <p>
            Entire results are downloadable as a text file at the top of the page. <br/>
            In each category, plot view and table view are selectable.
            In the plot view, images are downloadable as PNG file.
          </p>
          <img src="{!! URL::asset('/image/gene2funcGS.png') !!}" style="max-width:80%"/>
          <br/><br/>

          <h4>4. Gene Table</h4>
          <p>
            Input genes are mapped to OMIM ID, UniProt ID, Drug ID of DrugBank and links to GeneCards.
            Drug IDs are assigned if the UniProt ID of the gene is one of the targets of the drug.<br/>
            OMIM IDs and Drug IDs are linked to the original sources which opens the new tab.
            Links of GeneCards will be displayed in the frame below.
          </p>
          <img src="{!! URL::asset('/image/gene2funcGT.png') !!}" style="max-width:80%"/>

        </div>
      </div>
    </div>
  </div>

</div>
</div>
@stop
