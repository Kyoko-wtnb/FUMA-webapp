<div id="quick-start" class="sidePanel container" style="padding-top:50px;">
  <h2>Quick Start</h2>
  <div style="margin-left: 40px;">
    <h3 id="generalInfo">General Information</h3>
      <p>
        Each page will contain information and brief description of inputs and results to help you understand them without going through entire tutorial.<br/>
        <div style="padding-left: 40px">
          <span class="info"><i class="fa fa-info"></i> This is information of inputs or results.</span><br/><br/>
          <a class="infoPop" data-toggle="popover" data-content="This popuover will show brief description. Click anywhere outside of this popover to close.">
            <i class="fa fa-question-circle-o fa-lg"></i>
          </a> :click this question mark to display brief description.<br/><br/>
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
        Before you submit your GWAS summary statistics, please log in to your account.
        You can login from either <a href="{{ url('/login') }}">login</a> page or <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a> page directry.<br/><br/>
        <img src="{!! URL::asset('/image/homereg.png') !!}" style="width:80%"/><br/>
      </p><br/>

      <p><h4><strong>2. Submit new job at <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a></strong></h4>
        GWAS summary statistics is a mandatory input and a variety of file formats are supported.
        Please refer the section of <a class="inpage" href="{{ Config::get('app.subdir') }}/tutorial#prepare-input-files">Input files</a> for details.
        If your file is an ouput of PLINK, SNPTEST or METAL, you can directory submit the file without specifying column names.<br/>
        Optionally, if you would like to specify lead SNPs, you can upload a file with 3 columns; rsID, chromosome and position.<br/>
        In addition, if you are interested in specific genomic regions, you can also provide them by uploading a file with 3 columns; chromosome, start and end position.<br/><br/>
        <img src="{!! URL::asset('/image/newjobfile.png') !!}" style="width:80%"/><br/>
      </p><br/>

      <p><h4><strong>3. Set parameters</strong></h4>
        In the same page as you specify input files, there are a variety of optional parameters.
        Please check your parameters carefully. Default setting perform identification of lead SNPs at r2=0.6 and maps SNPs to genes up to 10kb apart.<br/>
        To filter SNPs by functional annotations and use eQTL mapping, please refer the parmeters section from <a class="inpage" href="{{ Config::get('app.subdir') }}/tutorial#parameters">here</a>.<br/>
        If all inputs are valid, 'Submit Job' button will be activated. Once you submit a job, this will be listed in My Jobs.<br/><br/>
        <img src="{!! URL::asset('/image/submitjob.png') !!}" style="width:70%"/><br/>
      </p><br/>

      <p><h4><strong>4. Check your results</strong></h4>
        Once process is done, you will receive an email.
        Unless an error occured during the process, the email includes the link to the result page (this again requires login).
        You can also access to the results page from My Jobs page. <br/>
        The result page display 4 additional side bars.<br/>
        <strong>Genome-wide plots</strong>: Manhattan plots and Q-Q plots for GWAS sumary statistics and gene-based test by MAGMA.<br/>
        <strong>Summary of results</strong>: Summarised results such as the number of candidate SNPs and mapped genes for overall and per genomic loci.<br/>
        <strong>Results</strong>: Tables of lead SNPs, genomic risk loci, candidate SNPs with annotations, eQTLs (only when eQTL mapping is performed), mapped genes and GWAS-catalog reported SNPs matched with candidate SNPs.
        You can also create interactive regional plots with functional annotations from this tab.<br/>
        <strong>Downloads</strong>: Download results as text files.<br/>
        Details for each panel are described in the <a class="inpage" href="{{ Config::get('app.subdir') }}/tutorial#outputs">SNP2GENE Outputs</a> section of this tutorial.<br/><br/>
        <img src="{!! URL::asset('/image/result.png') !!}" style="width:70%"/><br/><br/>
      </p>
    </div>
    <br/>
    <h3 id="geneQuery">Tissue specific gene expression and shared biological functions of a list of genes</h3>
    <p>In the <a href="{{ Config::get('app.subdir') }}/gene2func"><strong>GENE2FUNC</strong></a>, you can check expression in different tissue types, tissue specificity and enrichment of publicly available gene sets of genes of interest.<br/></p>
    <div style="margin-left: 40px">
      <p><h4><strong>1. Submit a list of genes</strong></h4>
        Both a list of genes of interest and background genes (for hypergeometric test) are mandatory input.<br/>
        You can use mapped genes from SNP2GENE by clicking the button in the result page (Results tab).<br/><br/>
        <img src="{!! URL::asset('/image/gene2funcSubmit.png') !!}" style="width:70%"/><br/>
      </p><br/>

      <p><h4><strong>2. Results</strong></h4>
        Once genes are submitted, four extra side bars wil be shown.<br/>
        <strong>Gene Expression</strong>: The heatmap of gene expression of 53 tissue types from GTEx.<br/>
        <strong>Tissue Specificity</strong>: The bar plots of enrichment of differentially expressed genes across tissue types.<br/>
        <strong>Gene Sets</strong>: Plots and tables of enriched gene sets.<br/>
        <strong>Gene Table</strong>: Table of input genes with lnks to OMIM, Drugbank and GeneCards.<br/>
        Details for each panel are described in the <a class="inpage" href="{{ Config::get('app.subdir') }}/tutorial#gene2funcOutputs">GENE2FUNC Outputs</a> section of  this tutorial.<br/><br/>
      </p>
    </div>
  </div>
</div>
