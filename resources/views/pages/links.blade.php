@extends('layouts.master')
@section('head')
<script type="text/javascript">
  var loggedin = "{{ Auth::check() }}";
</script>
@stop
@section('content')
<div class="container" style="padding-top: 50px;">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Data source/tool</th>
        <th style="width: 25%;">Used for</th>
        <th style="width: 35%;">Links</th>
        <th>Last update</th>
        <th style="width: 25%;">Reference</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1000 genoms progect Phase 3</td>
        <td>
          Reference panel used to compute r2 and MAF.
        </td>
        <td>
          Info: <a href="http://www.internationalgenome.org/" target="_blank">http://www.internationalgenome.org/</a><br/>
          Data: <a href="ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/" target="_blank">ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/</a>
        </td>
        <td>5 December 2016</td>
        <td>
          1000 Genomes Project Consortium, et al. 2015. Aglobal reference for human genetic variation. <i>Nature.</i> <b>526</b>, 68-74.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/26432245" target="_blank">PMID:26432245</a>
        </td>
      </tr>
      <tr>
        <td>PLINK</td>
        <td>Used to compute r2 and MAF.</td>
        <td>Info and download: <a href="https://www.cog-genomics.org/plink2" target="_target">https://www.cog-genomics.org/plink2</a></td>
        <td>5 December 2016</td>
        <td>
          Purcell, S., et al. 2007. PLINK: A tool set for whole-genome association and population-based linkage analyses. <i>Am. J. Hum. Genet.</i> <b>81</b>, 559-575.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/17701901" target="_blank">PMID:17701901</a>
        </td>
      </tr>
      <tr>
        <td>MAGMA</td>
        <td>Used for gene analysis and gene-set analysis.</td>
        <td>Info and download: <a target="_blank" href="https://ctg.cncr.nl/software/magma">https://ctg.cncr.nl/software/magma</a></td>
        <td>30 Jan 2017</td>
        <td>
          de Leeuw, C., et al. 2015. MAGMA: Generalized gene-set analysis of GWAS data. <i>PLoS Comput. Biol.</i> <b>11</b>, DOI:10.1371/journal.pcbi.1004219. <br/>
          <a href="https://www.ncbi.nlm.nih.gov/pmc/articles/PMC4401657/" target="_blank">PMCID:PMC4401657</a>
        </td>
      </tr>
      <tr>
        <td>ANNOVAR</td>
        <td>A variant annotation tool used to obtain functional consequences of SNPs on gene functions.</td>
        <td>Info and download: <a href="http://annovar.openbioinformatics.org/en/latest/" target="_blank">http://annovar.openbioinformatics.org/en/latest/</a></td>
        <td>5 December 2016</td>
        <td>
          Wang, K., Li, M. and Hakonarson, H. 2010. ANNOVAR: functional annotation of genetic variants from high-throughput sequencing data. <i>Nucleic Acids Res.</i> <b>38</b>:e164<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/20601685" target="_blank">PMID:20601685</a>
        </td>
      </tr>
      <tr>
        <td>CADD v1.3</td>
        <td>A deleterious score of variants computed by integrating 63 functional annotations. The higher the score, the more deleterious.</td>
        <td>
          Info: <a href="http://cadd.gs.washington.edu/" target="_blank">http://cadd.gs.washington.edu/</a><br/>
          Data: <a href="http://cadd.gs.washington.edu/download" target="_blank">http://cadd.gs.washington.edu/download</a>
        </td>
        <td>5 December 2016</td>
        <td>
          Kicher, M., et al. 2014. A general framework for estimating the relative pathgeneticity of human genetic variants. <i>Nat. Genet.</i> <b>46</b>, 310-315.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/24487276" target="_blank">PMID:24487276</a>
        </td>
      </tr>
      <tr>
        <td>RegulomeDB v1.1</td>
        <td>A categorical score to guide interpretation of regulatory variants.</td>
        <td>
          Info: <a href="http://regulomedb.org/index" target="_blank">http://regulomedb.org/index</a><br/>
          Data: <a href="http://regulomedb.org/downloads/RegulomeDB.dbSNP141.txt.gz" target="_blank">http://regulomedb.org/downloads/RegulomeDB.dbSNP141.txt.gz</a>
        </td>
        <td>5 December 2016</td>
        <td>
          Boyle, AP., et al. 2012. Annotation of functional variation in persoanl genomes using RegulomeDB. <i>Genome Res.</i> <b>22</b>, 1790-7.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/22955989" target="_blank">PMID:22955989</a>
        </td>
      </tr>
      <tr>
        <td>15-core chromatin state</td>
        <td>Chromatin state for 127 epigenomes was lerned by ChromHMM derived from 5 chromatin markers (H3K4me3, H3K4me1, H3K36me3, H3K27me3, H3K9me3).</td>
        <td style="word-break: break-all;">
          Info: <a href="http://egg2.wustl.edu/roadmap/web_portal/chr_state_learning.html" target="_blank">http://egg2.wustl.edu/roadmap/web_portal/chr_state_learning.html</a><br/>
          Data: <a href="http://egg2.wustl.edu/roadmap/data/byFileType/chromhmmSegmentations/ChmmModels/coreMarks/jointModel/final/all.mnemonics.bedFiles.tgz" target="_blank">http://egg2.wustl.edu/roadmap/data/byFileType/chromhmmSegmentations/ChmmModels/coreMarks/jointModel/final/all.mnemonics.bedFiles.tgz</a>
        </td>
        <td>5 December 2016</td>
        <td>
          Roadmap Epigenomics Consortium, et al. 2015. Integrative analysis of 111 reference human epigenomes. <i>Nature.</i> <b>518</b>, 317-330.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/25693563" target="_blank">PMID:25693563</a><br/>
          Ernst, J. and Kellis, M. 2012. ChromHMM: automating chromatin-state discovery and characterization. <i>Nat. Methods.</i> <b>28</b>, 215-6.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/22373907" target="_blank">PMID:22373907</a>
        </td>
      </tr>
      <tr>
        <td>GTEx v6</td>
        <td>eQTLs and gene expression used in the pipeline were obtained from GTEx v6.<br/>
          For gene expression 53 tissue types are available and 44 of those which have more than 70 samples are included in eQTL analyses.
        </td>
        <td>
          Info and data: <a href="http://www.gtexportal.org/home/" target="_blank">http://www.gtexportal.org/home/</a>
        </td>
        <td>5 December 2016</td>
        <td>
          GTEx Consortium. 2015. Human genomics, The genotype-tissue expression (GTEx) pilot analysis: multitissue gene regulation in humans. <i>Science.</i> <b>348</b>, 648-60.<br/>
           <a href="https://www.ncbi.nlm.nih.gov/pubmed/25954001" target="_blank">PMID:25954001</a>
        </td>
      </tr>
      <tr>
        <td>Blood eQTL Browser</td>
        <td>eQTLs of blood cells. Only cis-eQTLs with FDR &le; 0.05 are available in FUMA.</td>
        <td>Info and data: <a href="http://genenetwork.nl/bloodeqtlbrowser/" taget="_blank">http://genenetwork.nl/bloodeqtlbrowser/</a></td>
        <td>17 January 2017</td>
        <td>
          Westra et al. 2013. Systematic identification of trans eQTLs as putative divers of known disease associations. <i>Nat. Genet.</i> <b>45</b>, 1238-1243.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/3991562" target="_blank">PMID:3991562</a>
        </td>
      </tr>
      <tr>
        <td>BIOS QTL browser</td>
        <td>eQTLs of blood cells in Dutch population. Only cis-eQTLs (gene-level) with FDR &le; 0.05 are available in FUMA.</td>
        <td>Info and data: <a href="http://genenetwork.nl/biosqtlbrowser/" target="_blank">http://genenetwork.nl/biosqtlbrowser/</a></td>
        <td>17 January 2017</td>
        <td>
          Zhernakova et al. 2017. Identification of context-dependent expression quantitative trait loci in whole blood. <i>Nat. Genet.</i> <b>49</b>, 139-145.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/27918533" target="_blank">PMID:27918533</a>
        </td>
      </tr>
      <tr>
        <td>BRAINEAC</td>
        <td>eQTLs of 10 brain regions. Cis-eQTLs with nominal P-value &lt; 0.05 are available in FUMA.</td>
        <td>Info and data: <a href="http://www.braineac.org/" target="_blank">http://www.braineac.org/</a></td>
        <td>26 January 2017</td>
        <td>
          Ramasamy et al. 2014. Genetic variability in the regulation of gene expression in ten regions of the human brain. <i>Nat. Neurosci.</i> <b>17</b>, 1418-1428.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/25174004" target="_blank">PMID:27918533</a>
        </td>
      </tr>
	  <tr>
        <td>GSE87112 (Hi-C)</td>
        <td>Hi-C data (significant loops) of 21 tissue/cell types. Pre-processed data (output of Fit-Hi-C) is used in FUMA.</td>
        <td>Info and data: <a href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE87112" target="_blank">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE87112</a></td>
        <td>9 May 2017</td>
        <td>
          Schmitt, A.D. et al. 2016. A compendium of chromatin contact maps reveals spatially active regions in the human genome. <i>Cell Rep.</i> <b>17</b>, 2042-2059.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/27851967" target="_blank">PMID:27851967</a>
        </td>
      </tr>
	  <tr>
        <td>Enhancer and promoter regions</td>
        <td>Predicted enhancer and promoter regions (including dyadic) from Roadmap Epigenomics Projects. 111 epigenomes are available.</td>
        <td>Info: <a href="http://egg2.wustl.edu/roadmap/web_portal/DNase_reg.html" target="_blank">http://egg2.wustl.edu/roadmap/web_portal/DNase_reg.html</a><br/>
			Data: <a href="http://egg2.wustl.edu/roadmap/data/byDataType/dnase/" target="_blank">http://egg2.wustl.edu/roadmap/data/byDataType/dnase/</a>
		</td>
        <td>9 May 2017</td>
        <td>
			Roadmap Epigenomics Consortium, et al. 2015. Integrative analysis of 111 reference human epigenomes. <i>Nature.</i> <b>518</b>, 317-330.<br/>
            <a href="https://www.ncbi.nlm.nih.gov/pubmed/25693563" target="_blank">PMID:25693563</a><br/>
            Ernst, J. and Kellis, M. 2012. ChromHMM: automating chromatin-state discovery and characterization. <i>Nat. Methods.</i> <b>28</b>, 215-6.<br/>
            <a href="https://www.ncbi.nlm.nih.gov/pubmed/22373907" target="_blank">PMID:22373907</a>
        </td>
      </tr>
	  <tr>
        <td>MsigDB v5.2</td>
        <td>Collection of publicly available gene sets. Data sets include e.g. KEGG, Reactome, BioCarta, GO terms and so on.</td>
        <td>Info and data: <a href="http://software.broadinstitute.org/gsea/msigdb" target="_blank">http://software.broadinstitute.org/gsea/msigdb</a></td>
        <td>5 December 2016</td>
        <td>
          Liberzon, A. et al. 2011. Molecular signatures database (MSigDB) 3.0. <i>Bioinformatics.</i> <b>27</b>, 1739-40.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/21546393" target="_blank">PMID:21546393</a>
        </td>
      </tr>
      <tr>
        <td>WikiPathways</td>
        <td>The curated biological pathways.</td>
        <td style="word-break: break-all;">
          Info: <a href="http://wikipathways.org/index.php/WikiPathways" target="_blank">http://wikipathways.org/index.php/WikiPathways</a><br/>
          Data: <a href=">http://data.wikipathways.org/20161110/gmt/wikipathways-20161110-gmt-Homo_sapiens.gmt" target="_blank">http://data.wikipathways.org/20161110/gmt/wikipathways-20161110-gmt-Homo_sapiens.gmt</a>
        </td>
        <td>5 December 2016</td>
        <td>
          Kutmon, M., et al. 2016. WikiPathways: capturing the full diversity of pahtway knowledge. <i>Nucleic Acids Res.</i> <b>44</b>, 488-494.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/26481357" target="_blank">PMID:26481357</a>
        </td>
      </tr>
      <tr>
        <td>GWAS-catalog e85 2016-09-27</td>
        <td>A database of reported snp-trait associations.</td>
        <td>
          Info: <a href="https://www.ebi.ac.uk/gwas/" target="_blank">https://www.ebi.ac.uk/gwas/</a><br/>
          Data: <a href="https://www.ebi.ac.uk/gwas/docs/downloads" target="_blank">https://www.ebi.ac.uk/gwas/docs/downloads</a>
        </td>
        <td>5 December 2016</td>
        <td>
          MacArthur, J., et al. 2016. The new NHGRI-EBI Catalog of published genome-wide association studies (GWAS Catalog). <i>Nucleic Acids Res.</i> pii:gkw1133.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/27899670" target="_blank">PMID:27899670</a>
        </td>
      </tr>
      <tr>
        <td>DrugBank</td>
        <td>Targeted genes (protein) of drugs in DrugBank was obtained to assign drug ID for input genes.</td>
        <td>
          Info: <a href="https://www.ncbi.nlm.nih.gov/pubmed/27899670" target="_blank">https://www.ncbi.nlm.nih.gov/pubmed/27899670</a><br/>
          Data: <a href="https://www.drugbank.ca/releases/latest#protein-identifiers" target="_blank">https://www.drugbank.ca/releases/latest#protein-identifiers</a>
        </td>
        <td>5 December 2016</td>
        <td>
          Wishart, DS., et al. 2008. DrugBank: a knowledgebase for drugs, drug actions and drug targets. <i>Nucleic Acis Res.</i> <b>36</b>, D901-6.<br/>
          <a href="https://www.ncbi.nlm.nih.gov/pubmed/18048412" target="_blank">PMID:18048412</a>
        </td>
      </tr>
	  <tr>
		  <td>pLI</td>
		  <td>A gene score annotated to prioritized genes. The score is the probability of being loss-of-function intolerance.
		  </td>
		  <td>
			  Info: <a href="http://exac.broadinstitute.org/" target="_blank">http://exac.broadinstitute.org/</a>
			  Data: <a href="ftp://ftp.broadinstitute.org/pub/ExAC_release/release0.3.1/functional_gene_constraint" target="_blank">ftp://ftp.broadinstitute.org/pub/ExAC_release/release0.3.1/functional_gene_constraint</a>
		  </td>
		  <td>27 April 2017</td>
		  <td>
			  Lek, M. et al. 2016. Analyses of protein-coding genetic variation in 60,706 humans. <i>Nature.</i> <b>536</b>, 285-291.<br/>
			  <a href="https://www.ncbi.nlm.nih.gov/pubmed/27535533" target="_blank">PMID:27535533</a>
		  </td>
	  </tr>
	  <tr>
		  <td>ncRVIS</td>
		  <td>A gene score annotated to prioritized genes. The score is the non-coding residual variation intolerance score.
		  </td>
		  <td>
			  Info: <a href="http://journals.plos.org/plosgenetics/article?id=10.1371/journal.pgen.1005492" target="_blank">http://journals.plos.org/plosgenetics/article?id=10.1371/journal.pgen.1005492</a>
			  Data: <a href="http://journals.plos.org/plosgenetics/article/file?type=supplementary&id=info:doi/10.1371/journal.pgen.1005492.s011" target="_blank">http://journals.plos.org/plosgenetics/article/file?type=supplementary&id=info:doi/10.1371/journal.pgen.1005492.s011</a>
		  </td>
		  <td>27 April 2017</td>
		  <td>
			  Petrovski, S. et al. 2015. The intolerance of regulatory sequence to genetic variation predict gene dosage sensitivity. <i>PLOS Genet.</i> <b>11</b>, e1005492.<br/>
			  <a href="https://www.ncbi.nlm.nih.gov/pubmed/26332131" target="_blank">PMID:26332131</a>
		  </td>
	  </tr>
    </tbody>
  </table>
</div>
@stop
