<h3 id="prepare-input-files">Prepare Input Files</h3>
<div style="margin-left: 40px;">
  <h4><strong>1. GWAS summary statistics</strong></h4>
  <p>GWAS summary statistics is a mandatory input of <strong>SNP2GENE</strong> process.
    FUMA accept various types of format. For example, PLINK, SNPTEST and METAL output formats can be used as it is.
    <span class="info"><i class="fa fa-info"></i>
      Indels and variants which do no exists in 1000 genomes reference panle (Phase3) will be removed from any analyses.
    </span>
  </p>
  <p><strong>Mandatory columns</strong><br/>
    The input file must include P-value and either rsID or chromosome + genetic position on hg19 reference genome.
    Whenevr rsID is provided, it is updated to dbSNP build 146.
    When either chromosome or position is missing, they are extracted from dbSNP build 146 based on rsID.
    When rsID is missing, it is extracted from dbSNP build 146 based on chromosome and position.
    When all of them (rsID, chromosome and position) are provided, they are kept as input except rsID which is updated to dbSNP build 146.<br/>
    The column of chromosome can be string like "chr1" or just integer like 1.
    When "chr" is attached, this will be removed in output files.
    When the input file contains chromosome X, this will be encoded as chromosome 23, however, input file can be leave as "X".
  </p>
  <p><strong>Allele columns</strong><br/>
    Alleles are not mandatory but if only one allele is provided, that is considered as affected allele.
    When two alleles are provided, affected allele will be defined depending on header.
    If alleles are not provided, they will be extracted from 1000 genomes referece panel as minor allele as affected alleles.
    Whenever alleles are provided, they are matched with dbSNP build 146 if extraction of rsID, chromosome or position is necessary.<br/>
    Alleles are case insensitive.
  </p>
  <p><strong>Headers</strong><br/>
    Column names can be optionally provided, otherwise automatically detected based on the following headers (case insensitive).</p>
    <ul>
      <li><strong>SNP | snpid | markername | rsID</strong>: rsID</li>
      <li><strong>CHR | chromosome | chrom</strong>: chromosome</li>
      <li><strong>BP | pos | position</strong>: genomic position (hg19)</li>
      <li><strong>A1 | alt | effect_allele | allele1 | alleleB</strong>: affected allele</li>
      <li><strong>A2 | ref | non_effect_allele | allele2 | alleleA</strong>: another allele</li>
      <li><strong>P | pvalue | p-value | p_value | frequentist_add_pvalue | pval</strong>: P-value (Mandatory)</li>
      <li><strong>OR</strong>: Odds Ratio</li>
      <li><strong>Beta | be</strong>: Beta</li>
      <li><strong>SE</strong>: Standard error</li>
    </ul>
    <span class="info"><i class="fa fa-info"></i> Column for "N" will be described in the <a href="{{ Config::get('app.subdir') }}/tutorial#parameters">Parameters</a> section.</span><br/>
    <span class="info"><i class="fa fa-info"></i> Please be carefull for alleles header in whcih A1 and Allele1 are effect allele while alleleA is non-effect allele.<br/>
      Even if wrong labels are proveded for alleles, it does not affect any annotation and prioritization results, but please be aware of that when you interpret results.
    </span><br/>
    Extra columns will be ignored and will not be included in any output.<br/>
    Any rows start with "#" wiil be ignored.
  </p>

  <p><strong>Delimiter</strong><br/>
    Delimiter can be any of white space including single space, multiple space and tab.
    Because of this, each element including column names must not include any space.
  </p>

  <hr>
  <h4>Note and Tips</h4>
  <p>
    When the input file has all of the following columns; rsID, chromosome, position, allele1 and allele2, the process will be much quicker than extracting information.
  </p>
  <p>The pipeline only support human genome <span style="color: red;">hg19</span>.
    If your input file is not based on hg19, please update the genomic position using liftOver from UCSC.
    However, there is an option for you!! When you provide only rsID without chromosome and genomic position, FUMA will extract them from dbSNP build 146 based on hg19.
    To do this, remove columns of chromosome and genomic position or rename headers to ignore those columns.
    Note that extracting chromosme and genomic position will take extra time.
  </p>
  <hr>
</div>

<div style="margin-left: 40px;">
  <h4><strong>2. Pre-defined lead SNPs</strong></h4>
  <p>This is an optional input file. If you wnat to specify lead SNPs, input file should have the following 3 columns.<br/>
  </p>

  <ul>
    <li><strong>rsID</strong> : rsID of the lead SNPs</li>
    <li><strong>chr</strong> : chromosome</li>
    <li><strong>pos</strong> : genomic position (hg19)</li>
  </ul>
  <p style="color: #000099;"><i class="fa fa-info"></i>
    The order of column has to be the same as shown above but header could be anything.
    Extra columns will be ignored.
  </p>
  <hr>
    <h4>Note and Tips</h4>
    <p>This option would be useful when<br/>
      1. You have lead SNPs of interest but they do not reach significant P-value threshold.<br/>
      2. You are only interested in specific lead SNPs and do not want to identify additional lead SNPs which are independent.
      In this case, you also have to UNCHECK option of <code>Identify additional independent lead SNPs</code>.
    </p>
  <hr>
</div>

<div style="margin-left: 40px;">
  <h4><strong>3. Pre-defined genomic region</strong></h4>
  <p>This is an option input file. If you want to analyse only specific genomic region of GWAS, input file shoud have 3 columns.<br/>
  </p>
  <ul>
    <li><strong>chr</strong> : chromosome</li>
    <li><strong>start</strong> : start position of the genomic region of interest (hg19)</li>
    <li><strong>end</strong> : end position of the genomic region of interest (hg19)</li>
  </ul>
  <p style="color: #000099;"><i class="fa fa-info"></i>
    The order of column has to be the same as shown above but header could be anything.
    Extra columns will be ignored.
  </p>
  <hr>
    <h4>Note and Tips</h4>
    <p>This option would be useful when you have already done some followup analyses of your GWAS and are interested in specific genomic regions.<br/>
      When pre-defined genomic region is provided, regardless of parameters, only lead SNPs and SNPs in LD with them within provided regions will be reported in outputs.
    </p>
  <hr>
</div>
