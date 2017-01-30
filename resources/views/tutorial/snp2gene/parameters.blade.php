<h3 id="parameters">Parameters</h3>
<p>Annotation and prioritization depends on several settings, which can be adjusted if desired.
  The default settings will result in performing naive positional mapping which maps all independent lead SNPs and SNPs in LD to genes up to 10kb apart.
  It does not include eQTL mapping by default, and it also does not filter on specific functional consequences of SNPs.
  If for example you are interested in prioritizing genes only when they are indicated by an eQTL that is in LD with a significant lead SNP, or by exonic SNPs, then you need to adjust the parameter settings.
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
    This is the warning message for the input/parameter. Please check your input settings.
  </span><br/><br/>
</p>
<p>In this section, every parameter that can be adjusted will be described in detail.
</p>

<div style="margin-left: 40px;">
<h4 id="input-files"><strong>1. Input files</strong></h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th style="width: 20%">Parameter</th>
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
          Plain text file or zipped or gzipped files are acceptable.
          The maximum file size which can be uploaded is 600Mb.
          As well as full results of GWAS summary statistics, subset of results can also be used.
          e.g. If you would like to look up specific SNPs, you can filter out other SNPs.
          Please refer to the <a class="inpage" href="{{ Config::get('app. subdir') }}/tutorial#prepare-input-files">Input files</a> section for specific file format.
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
        <td>If this option is CHECKED, FUMA will identify additional independent lead SNPs after defininig the LD block for pre-defined lead SNPs.
          Otherwise, only given lead SNPs and SNPs in LD of them will be used for further annotations.
        </td>
        <td>Check</td>
        <td>Checked</td>
      </tr>
      <tr>
        <td>Pre-defined genetic region</td>
        <td>Optional</td>
        <td>Optional pre-defined genomic regions.<br/>
          FUMA only looks at provided regions to identify lead SNPs and SNPs in LD of them.
          If you are only interested in specific regions, this option will increase the speed of process.
        </td>
        <td>File upload</td>
        <td>none</td>
      </tr>
    </tbody>
  </table>
</div>
<br/>
<div style="margin-left: 40px;">
  <h4><strong>2. Parameters for lead SNPs and candidate SNPs identification</strong></h4>
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
        <td>The total number of individuals in the GWAS or the number of individuals per SNP.
          This is only used for MAGMA to compute the gene-based P-values.
          For total sample size, input should be an integer.
          When the input file of GWAS summary statistics contains a column of sample size per SNP, the colum nname can be provided in the second text box.<br/>
          <span class="info"><i class="fa fa-info"></i> When column name is provided, please make sure that the column only contains integers (no float or scientific notation).
            If there are any float values, they will be rouded up by FUMA.
          </span>
        </td>
        <td>Integer or text</td>
        <td>none</td>
        <td>Does not affect any candidates</td>
      </tr>
      <tr>
        <td>Maximum lead SNP P-value (&le;)</td>
        <td>Mandatory</td>
        <td>FUMA identifies lead SNPs wiht P-value less than or equal to this threshold and independent from each other.
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
        <td>The minimum r<sup>2</sup> for determining LD with independent genome-wide significant SNPs, which is used to determine the borders of the genomic risk loci.
          SNPs with r<sup>2</sup> &ge; uder defined threshold with any of the detected independent significant SNPs will be included for further annotations and are used fro gene prioritization.
          Note that the identification of independent lead SNPs is independent from this and is based on fized r<sup>2</sup> of 0.1.
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
        <td>This is the P-value threshold for candidate SNPs in LD of independent significant SNPs.
          This will be applied only for GWAS-tagged SNPs as SNPs which do not exist in the GWAS input but are extracted from 1000 genoms reference do not have P-value.
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
        <td>If Yes, all SNPs in strong LD with any of independent significant SNPs including non-GWAS-tagged SNPs will be included and used for gene prioritization.</td>
        <td>Yes/No</td>
        <td>Yes</td>
        <td>-</td>
      </tr>
      <tr>
        <td>Minimum MAF (&ge;)</td>
        <td>Mandatory</td>
        <td>The minimum Minor Allele Frequency to be included in annotation and prioritization.
          MAF is computed based on 1000 genomes reference panel (Phase 3).
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
        <td>This is the maximum distance between LD blocks of independent significant SNPs to merge into a single genomic locus.
          When this is set at 0, only physically overlapping LD blocks are merged.
          Defining genomic loci does not affect identifying which SNPs fulfill selection criteria to be used for annotation and prioritization.
          It will only result in a different number of reported risk loci, which can be desired when certain loci are partly overlapping or physically very close.        </td>
        <td>numeric</td>
        <td>250kb</td>
        <td><span style="color:red;">higher</span>: decrease #genomic loci.<br/>
           <span style="color: blue;">lower</span>: increase #genomic loci.
         </td>
      </tr>
    </tbody>
  </table>
</div>
<br/>
<div style="margin-left: 40px;">
  <h4><strong>3. Parameters for gene mapping</strong></h4>
  <p>There are two options for gene mapping; positional and eQTL mappings. By default, positional mapping with maximum distance 10kb is performed.
    Since parameters in this section largely affect the result of mapped genes, please set carefully.
  </p>
  <h4><strong>3.1 Positional mapping</strong></h4>
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
        <td>Positional mapping</td>
        <td>Optional</td>
        <td>Check this if you want to perform positional mapping.
          Positional mapping can be performed either purly based on phisical distance or based on functional consequences of SNPs on genes.
          These parameters can be specified in the option below.
        </td>
        <td>Check</td>
        <td>Checked</td>
        <td>-</td>
      </tr>
      <tr>
        <td>Distance to genes or functional consequences of SNPs on genes to map</td>
        <td>Mandatory if positional mapping is activated.</td>
        <td>Positional mappiing criterion either map SNPs to genes purly based on phisical distances or functional consequences of SNPs on genes. <br/>
          When maximum distance is provided SNPs are mapped to genes based on the distance.
          Alternatively, specific functional consequences of SNPs on genes can be selected which filtered SNPs to map to genes.
          Note that when functional consequences are selected, all SNPs are locating on the gene body (distance 0) except upstream and downstream SNPs whic hare up to 1kb apart from TSS or TSE. <br/>
          <span class="info"><i class="fa fa-info"></i>
            When the maximum distance is set at > 0kb and < 1kb all upstream and downstream SNPs are included since the actual distance is not provided by ANNOVAR.
            Therefore, the maximum distance > 0kb and < 1kb is same as the maximum distance 1 kb.
          </span>
        </td>
        <td>Integer / Multiple selection</td>
        <td>Maximum distance 10 kb</td>
        <td>-</td>
      </tr>
      <!-- <tr>
        <td>Maximum distance to genes (&le;)</td>
        <td>Optional</td>
        <td>The maximum distance to map SNPs to genes.
          This option is used only when <code>Distance based mapping</code> is CHECKED.
          When this is set at 0, 1 kb up- and down-stream region of genes will be included.
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
        <td>Instead of distance based mapping which is purely based on physical distance, annotation based mapping maps only SNPs have selected functional consequence on gene functions.
          Annotations are based on ANNOVAR outputs.
          For example, when exonic is slected, only genes with exonic SNPs which are in LD of lead SNPs will be prioritized.
        </td>
        <td>Multiple selection</td>
        <td>none</td>
        <td>-</td>
      </tr> -->
    </tbody>
  </table>

  <h4><strong>3.2 eQTL mapping</strong></h4>
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
        <td>Check this if you want to perform eQTL mapping.
          eQTL mapping will map SNPs to genes which likely affect expression of thoses genes up to 1 Mb (cis-eQTL).
          eQTLs are highly tissue specific and tissue types can be selected in the following option.
          eQTL mapping can be used together with positional mapping.
        </td>
        <td>Check</td>
        <td>Unchecked</td>
        <td>-</td>
      </tr>
      <tr>
        <td>Tissue types</td>
        <td>Mandatory if <code>eQTL mapping</code> is CHECKED</td>
        <td>All available tissue types with data sources are shown in the select boxes.
          For detail of eQTL data resources, please refer to the <a href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">eQTL</a> section in this tutorial.
        </td>
        <td>Multiple selection</td>
        <td>none</td>
        <td>-</td>
      </tr>
      <tr>
        <td>eQTL maximum P-value (&le;)</td>
        <td>Optional</td>
        <td>The P-value threshold of eQTLs.
          Two options are available, <code>Use only significant snp-gene pairs</code> or nominal P-value threshold.
          When <code>Use only significant snp-gene pairs</code> is checked, only eQTLs with FDR &le; 0.05 will be used.
          Otherwise, defined nominal P-value is used to filter eQTLs.<br/>
          <span class="info"><i class="fa fa-info"></i>
            Some of eQTL data source only contained eQTLs with a certain FDR threshold.
            Please refer to the <a href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">eQTLs</a> section for details of each data sources.
          </span>
        </td>
        <td>Check / Numeric</td>
        <td>Checked / 1e-3</td>
        <td><span style="color:red;">higher</span>: increase #eQTLs and #mapped genes.<br/>
           <span style="color: blue;">lower</span>: decrease #eQTLs and #mapped genes.</td>
      </tr>
    </tbody>
  </table>

  <h4><strong>3.3 Functional annotation filtering</strong></h4>
  <p>Both positional and eQTL mappings have the following options separately for the filtering of SNPs based on functional annotation.
    All filters below apply to selected SNPs in LD with independent significant SNPs that are used to prioritize genes and influence the number of SNPs that are mapped to genes, and consequently influence the number of prioritized genes.
  </p>
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
        <td>Check this if you want to perform filtering of SNPs by CADD score.
          This applies to selected SNPs in LD with independent significant SNPs that are used to prioritize genes.
          CADD score is the score of deleteriousness of SNPs predicted by 63 functional annotations.
          12.37 is the threshold to be deleterious suggested by Kicher et al (2014).
          Plesase refer to the original publication for details from <a href="{{ Config::get('app.subdir') }}/links">links</a>.
        </td>
        <td>Check</td>
        <td>Unchecked</td>
        <td>-</td>
      </tr>
      <tr>
        <td>Minimum CADD score (&ge;)</td>
        <td>Mandatory if <code>CADD score</code> is checked</td>
        <td>The higher the CADD score, the more deleterious.</td>
        <td>numeric</td>
        <td>12.37</td>
        <td><span style="color:red;">higher</span>: less SNPs will be mapped to genes.<br/>
           <span style="color: blue;">lower</span>: more SNPs will be mapped to genes.</td>
        </td>
      </tr>
      <tr>
        <td>RegulomeDB score</td>
        <td>Optional</td>
        <td>Check if you want to perform filtering of SNPs by RegulomeDB score.
          This applies to selected SNPs in LD with independent significant SNPs that are used to prioritize genes.
          RegulomeDB score is a categorical score representing regulatory functionality of SNPs based on eQTLs and chromatin marks.
          Plesase refer to the original publication for details from <a href="{{ Config::get('app.subdir') }}/links">links</a>.
        </td>
        <td>Check</td>
        <td>Unchecked</td>
        <td>-</td>
      </tr>
      <tr>
        <td>Minimum RegulomeDB score (&ge;)</td>
        <td>Mandatory if <code>RegulomeDB score</code> is checked</td>
        <td>RegulomeDB score is a categorical score from 1a to 7)
          Score 1a means that those SNPs are most likely affecting regulatory elements and 7 means that those SNPs do not have any annotations.
          SNPs are recorded as NA if they are not present in the database.
          SNPs with NA will not be included for filtering on RegulomeDB score.
        </td>
        <td>string</td>
        <td>7</td>
        <td><span style="color:red;">higher</span>: more SNPs will be mapped to genes.<br/>
           <span style="color: blue;">lower</span>: less SNPs will be mapped to genes.</td>
        </td>
      </tr>
      <tr>
        <td>15-core chromatin state</td>
        <td>Optional</td>
        <td>Check if you want to perform filtering of SNPs by chromatin state.
          This applies to selected SNPs in LD with independent significant SNPs that are used to prioritize genes.
          The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states predicted by ChromHMM based on 5 chromatin marks for 127 epigenomes.
        </td>
        <td>Check</td>
        <td>Unchecked</td>
        <td>-</td>
      </tr>
      <tr>
        <td>15-core chromatin state tissue/cell types</td>
        <td>Mandatory if <code>15-core chromatin state</code> is checked</td>
        <td>Multiple tissue/cell types can be selected from the list.</td>
        <td>Multiple selection</td>
        <td>none</td>
        <td>-</td>
      </tr>
      <tr>
        <td>Maximum state of chromatin(&le;)</td>
        <td>Mandatory if <code>15-core chromatin state</code> is checked</td>
        <td>The maximum state to filter SNPs. Between 1 and 15.
          Generally, between 1 and 7 is open state.
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
          <code>any</code> (filtered on SNPs which have state above than threshold in any of selected tissue/cell types),
          <code>majority</code> (filtered on SNPs which have state above than threshold in majority (&ge;50%) of selected tissue/cell type), or
          <code>all</code> (filtered on SNPs which have state above than threshold in all of selected tissue/cell type).
        </td>
        <td>Selection</td>
        <td>any</td>
        <td>-</td>
      </tr>
    </tbody>
  </table>
  <br/>
</div>

<div style="margin-left: 40px;">
  <h4><strong>4. Gene types</strong></h4>
  <p>Biotype of genes to map can be selected. Please refer to Ensembl for details of biotypes.</p>
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
          Please see <a href="http://vega.sanger.ac.uk/info/about/gene_and_transcript_types.html">here</a> for details
        </td>
        <td>Multiple selection.</td>
        <td>Protein coding genes.</td>
      </tr>
    </tbody>
  </table>
  <br/>
</div>

<div style="margin-left: 40px;">
  <h4><strong>5. MHC region</strong></h4>
  <p>The MHC region is often excluded due to its complicated LD structure.
    Therefore, this option is checked by default.
    Please uncheck to include MHC region.
    Note that it doesn't change any results if there is no significant hit in the MHC region.
  </p>
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
        <td>Check if you want to exclude the MHC region or not. The default region is defined as between "MOG" and "COL11A2" genes.</td>
        <td>Check</td>
        <td>Checked</td>
      </tr>
      <tr>
        <td>Extended MHC region</td>
        <td>Optional</td>
        <td>User specified MHC region to exclude (for extended or shorter region).
          The input format should be like "25000000-34000000" on hg19.
        </td>
        <td>Text</td>
        <td>Null</td>
      </tr>
    </tbody>
  </table>
  <br/>
</div>

<div style="margin-left: 40px;">
  <h4><strong>6. Title of job submission</strong></h4>
  <p>
    Title of job submission can be provided at above the "Submit Job" button.
    This is not mandatory but this would be usefull to keep track your jobs.
  </p>

</div>
