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

    $('.inpage').on('click', function(){
      var hashid = $(this).attr('href');
      hashid = hashid.replace(/\/\w+#/, "#");
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
    })
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
        <a href="#generalInfo">General Info</a>
        <a href="#getCandidate">Get candiates</a>
        <a href="#geneQuery">Gene functions</a>
      </div>
    <li><a href="#snp2gene">SNP2GENE<span class="sub_icon glyphicon glyphicon-info-sign"></span></a></li>
      <div class="subside" id="snp2genesub">
        <a href="#prepare-input-files">Input files</a>
        <a href="#parameters">Parameters</a>
        <a href="#results">Result page</a>
        <a href="#table-columns">Table columns</a>
        <a href="#riskloci">Risk loci and lead SNPs</a>
        <a href="#eQTLs">eQTLs</a>
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
        <p>To annotate and prioritize SNPs and genes from your GWAS summary statistics, go to <a href="{{ Config::get('app.subdir') }}/snp2gene"><strong>SNP2GENE</strong></a> which compute LD structure,
          annotate functions to SNPs, and prioritize candidate genes.</p>
        <p>If you already have a list of genes, go to <a href="{{ Config::get('app.subdir') }}/gene2func"><strong>GENE2FUNC</strong></a> to check expression pattern and shared molecular functions.<p/>
        <br/>
        <img src="{{ URL::asset('/image/pipeline.png') }}" width="600" align="middle">
      </div>
    </div>

    @include('tutorial.quickstart')

    <div id="snp2gene" class="sidePanel container" style="padding-top:50;">
      <h2>SNP2GENE</h2>
      <div style="margin-left: 40px;">
        @include('tutorial.snp2gene.inputfiles')
        <br/>
        @include('tutorial.snp2gene.parameters')
        <br/>
        @include('tutorial.snp2gene.results')
        <br/>
        @include('tutorial.snp2gene.tables')
        <br/>
        @include('tutorial.snp2gene.riskloci')
        <br/>
        @include('tutorial.snp2gene.eqtl')
      </div>
    </div>

    <div id="gene2func" class="sidePanel container" style="padding-top:50;">
      <h2>GENE2FUNC</h2>
      <div style="padding-left: 40px;">
        <h3>Submit genes</h3>
        <div style="padding-left: 40px;">
          <h4><strong>Option 1. Use mapped genes from SNP2GENE</strong></h4>
          <p>If you want to use mapped genes from SNP2GENE, just click a button in Mapped genes panel of the result page.
            It will open a new tab and automatically starts analyses.
            This will take all mapped genes and use background genes with selected gene types for gene mapping (such as "protein-coding" or "ncRNA").
            Method of multiple test correction (FDR BH), adjusted P-value cutoff (0.05) and minimum number of overlapped genes (2) are set at default values.
            These options can be fixed by resubmitting query (click "Submit" button in New Query tab).
          </p>
          <img src="{!! URL::asset('/image/snp2genejump.png') !!}" style="width:70%"/><br/>
          <br/>
          <h4><strong>Option 2. Use a list of genes of interest</strong></h4>
          <p>To analyse your genes, you have to prepare list of genes as either ENSG ID, entrez ID or gene symbol.
          Genes can be provided in the text are (one gene per line) or uploading file in the left panel. When you upload a file, genes have to be in the first column with header. Header can be anything (even just a new line is fine) but start your genes from second row.</p>
          <p>To analyse your genes, you need to specify background genes. You can choose from the gene types which is the easiest way. However, in the case that you need to use specific background genes, please provide them either in the text area of by uploading a file of the right panel.
          File format should be same as described for genes on interest.</p>
          <img src="{!! URL::asset('/image/gene2funcSubmit.png') !!}" style="width:60%"/>
        </div>

        <h3 id="gene2funcOutputs">Results and Outputs</h3>
        <div style="padding-left: 40px;">
          <h4><strong>1. Gene Expression Heatmap</strong></h4>
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
          <img src="{!! URL::asset('/image/gene2funcHeatmap.png') !!}" style="width:60%"/>
          <br/><br/>

          <h4><strong>2. Tissue specificity</strong></h4>
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
          <img src="{!! URL::asset('/image/gene2funcTs.png') !!}" style="width:60%"/>
          <br/><br/>

          <h4><strong>3. Gene Sets</strong></h4>
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
          <img src="{!! URL::asset('/image/gene2funcGS.png') !!}" style="width:70%"/>
          <br/><br/>

          <h4><strong>4. Gene Table</strong></h4>
          <p>
            Input genes are mapped to OMIM ID, UniProt ID, Drug ID of DrugBank and links to GeneCards.
            Drug IDs are assigned if the UniProt ID of the gene is one of the targets of the drug.<br/>
            Each link to OMIM, Drugbank and GeneCards will open new tab.
          </p>
          <img src="{!! URL::asset('/image/gene2funcGT.png') !!}" style="width:70%"/>

        </div>
      </div>
    </div>
  </div>

</div>
</div>
@stop
