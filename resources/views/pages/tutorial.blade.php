@extends('layouts.master')

@section('content')

<div class="row">
  <div class="col-md-3 row-offcanvas row-offcanvas-left">
    <ul class="nav nav-pills nav-stacked">
        <li class="nav-item"><a class="nav-link" href="#overview">Overview</a></li>
        <li class="nav-item"><a class="nav-link" href="#quick-start">Quick Start</a></li>
        <li class="nav-item"><a class="nav-link" href="#snp2gene">SNP2GENE</a></li>
        <li class="nav-item"><a class="nav-link" href="#gene2func">GENE2FUNC</a></li>
        <li class="nav-item"><a class="nav-link" href="external-data">External Data</a></li>
    </ul>
  </div>
  <div class="col-md-9 sidebar-offcanvas" id="sidebar" role="navigation">
    <div id="overview">
      <h1 >Tutorial</h1>
      <p>Please read this tutorial carefully to use PARROT.
        There are various parameters provided by this pipeline.
        Those will be explained in detail. To start using right away, follow the quick start,
        then you will know a minimum knowledge how to use this pipeline.</p>
      <p>For detail methods, please refer the publication.</p>
      <p>If you have any question or suggestion, please do not hesitate to contact us!!</p>
    </div>
  </div>

  <div id="quick-start">

  </div>

  <div id="#snp2gene">
    <h2 id="snp2gene">SNP2GENE</h2>
    <blockquote>
    <h3 id="prepare-input-files">Prepare Input Files</h3>
    <p>GWAS summary statistics is a mandatory input of SNP2GENE process. PARROT accept various types of format. As default, <code>PLINK</code> for mat is selected, but please choose the format of your input file since this will cause error during process. Each option requires the following format.</p>
    <p>The input file must include P-value and either rsID or chromosome index and genetic position on hg19 reference genome. Alleles are not mandatory but if only one allele is provided, that is considered as affected allele. When two alleles are provided, it will depends on header. If alleles are not provided, they will be extracted from dbSNP build 146 as minor allele as affected alleles.</p>
    <p>If you are not sure which format to use, either edit your header or select <code>Plain Text</code> which will cover most of common column names.</p>
    <p>Delimiter can be any of white space including single space, multiple space and tab. Because of this, column name must not include any space.</p>
    <p>The column of chromosome can be string like &quot;chr1&quot; or just integer &quot;1&quot;. When &quot;chr&quot; is attached, this will be removed from outputs. When the input file contains chromosome X, this will be encoded as chromosome 23, however, input file can be leave as &quot;X&quot;.</p>
    <blockquote>
    <h4 id="1-plink-format">1. <code>PLINK</code> format</h4>
    <p>&ensp;As the most common file format, <code>PLINK</code> is the default option. Some options in PLINK do not return both A1 and A2 but as long as the file contains either SNP or CHR and BP, PARROT will cover missing values.</p>
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
    <p>The pipeline only support human genome hg19. If your input file is not in hg19, please update the genomic position using liftOver from UCSC. However, there is an option for you!! When you provide only rsID without chromosome index and genomic position, PARROT will extract them from dbSNP as hg19 genome. To do this, remove columns of chromosome index and genomic position.</p>
    <hr>
    </blockquote>
    <h3 id="parameters">Parameters</h3>
    <p>PARROT provide a variety of parameters. Default setting will perform naive positional mapping which gives you all genes within LD blocks of lead SNPs. In this section, every parameter will be described details.</p>
    </blockquote>
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
    <td>Optionally, user can provide specific genomic region to perform PARROT. PARROT only look provided regions to identify lead SNPs and candidate SNPs. If you are only interested in specific regions, this will increase a speed of job.</td>
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
    <td>PARROT identifies lead SNPs wiht P-value less than or equal to this threshold. This should not me changed unless GWAS is under-powered and only a few peaks are significant.</td>
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
    <td>If checked, PARROT include all SNPs in strong LD with any of lead SNPs even for non-GWAS-tagged SNPs.</td>
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
    <h4 id="general-parameters">General parameters</h4>
    <h4 id="parameters-for-gene-mapping">Parameters for gene mapping</h4>
  </div>
</div>
@stop
