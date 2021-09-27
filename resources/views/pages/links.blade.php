@extends('layouts.master')
@section('head')
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
var loggedin = "{{ Auth::check() }}";
</script>
@stop
@section('content')
<div class="container" style="padding-top: 50px;">
	<div class="alert alert-warning">
		<i class="fa fa-exclamation-triangle"></i>
		For scRNA-seq datasets in cell type analysis section, please see <a target="_blank" href="{{ Config::get('app.subdir') }}/tutorial#datasets">tutorial for links and references.</a>
	</div>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Data source/tool</th>
				<th style="width: 25%;">Used for</th>
				<th style="width: 30%;">Links</th>
				<th>Last update</th>
				<th style="width: 25%;">Reference</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>1000 Genome Project Phase 3</td>
				<td>
					Reference panel used to compute r<sup>2</sup> and MAF.
				</td>
				<td style="word-wrap:break-word;word-break:break-all;">
					Info: <a href="http://www.internationalgenome.org/" target="_blank">http://www.internationalgenome.org/</a><br/>
					Data: <a href="ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/" target="_blank">ftp://ftp.1000genomes.ebi.ac.uk/vol1/ftp/release/20130502/</a>
				</td>
				<td>27 May 2019</td>
				<td>
					1000 Genomes Project Consortium, et al. 2015. A global reference for human genetic variation. <i>Nature.</i> <b>526</b>, 68-74.<br/>
				<a href="https://www.ncbi.nlm.nih.gov/pubmed/26432245" target="_blank">PMID:26432245</a>
				</td>
			</tr>
			<tr>
				<td>PLINK v1.9</td>
				<td>Used to compute r2 and MAF.</td>
				<td>Info and download: <a href="https://www.cog-genomics.org/plink2" target="_target">https://www.cog-genomics.org/plink2</a></td>
				<td>27 May 2019</td>
				<td>
					Purcell, S., et al. 2007. PLINK: A tool set for whole-genome association and population-based linkage analyses. <i>Am. J. Hum. Genet.</i> <b>81</b>, 559-575.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/17701901" target="_blank">PMID:17701901</a>
				</td>
			</tr>
			<tr>
				<td>MAGMA v1.08</td>
				<td>Used for gene analysis and gene-set analysis.</td>
				<td>Info and download: <a target="_blank" href="https://ctg.cncr.nl/software/magma">https://ctg.cncr.nl/software/magma</a></td>
				<td>9 Sep 2020</td>
				<td>
					de Leeuw, C., et al. 2015. MAGMA: Generalized gene-set analysis of GWAS data. <i>PLoS Comput. Biol.</i> <b>11</b>, DOI:10.1371/journal.pcbi.1004219. <br/>
					<a href="https://www.ncbi.nlm.nih.gov/pmc/articles/PMC4401657/" target="_blank">PMCID:PMC4401657</a>
				</td>
			</tr>
			<tr>
				<td>ANNOVAR</td>
				<td>A variant annotation tool used to obtain functional consequences of SNPs on gene functions.</td>
				<td>Info and download: <a href="http://annovar.openbioinformatics.org/en/latest/" target="_blank">http://annovar.openbioinformatics.org/en/latest/</a></td>
				<td>5 Dec 2016</td>
				<td>
					Wang, K., Li, M. and Hakonarson, H. 2010. ANNOVAR: functional annotation of genetic variants from high-throughput sequencing data. <i>Nucleic Acids Res.</i> <b>38</b>:e164<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/20601685" target="_blank">PMID:20601685</a>
				</td>
			</tr>
			<tr>
				<td>CADD v1.4</td>
				<td>A deleterious score of variants computed by integrating 63 functional annotations. The higher the score, the more deleterious.</td>
				<td>
					Info: <a href="http://cadd.gs.washington.edu/" target="_blank">http://cadd.gs.washington.edu/</a><br/>
					Data: <a href="http://cadd.gs.washington.edu/download" target="_blank">http://cadd.gs.washington.edu/download</a>
				</td>
				<td>27 May 2019</td>
				<td>
					Kicher, M., et al. 2014. A general framework for estimating the relative pathogeneticity of human genetic variants. <i>Nat. Genet.</i> <b>46</b>, 310-315.<br/>
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
				<td>5 Dec 2016</td>
				<td>
					Boyle, AP., et al. 2012. Annotation of functional variation in personal genomes using RegulomeDB. <i>Genome Res.</i> <b>22</b>, 1790-7.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/22955989" target="_blank">PMID:22955989</a>
				</td>
			</tr>
			<tr>
				<td>15-core chromatin state</td>
				<td>Chromatin state for 127 epigenomes was learned by ChromHMM derived from 5 chromatin markers (H3K4me3, H3K4me1, H3K36me3, H3K27me3, H3K9me3).</td>
				<td style="word-break: break-all;">
					Info: <a href="http://egg2.wustl.edu/roadmap/web_portal/chr_state_learning.html" target="_blank">http://egg2.wustl.edu/roadmap/web_portal/chr_state_learning.html</a><br/>
					Data: <a href="http://egg2.wustl.edu/roadmap/data/byFileType/chromhmmSegmentations/ChmmModels/coreMarks/jointModel/final/all.mnemonics.bedFiles.tgz" target="_blank">http://egg2.wustl.edu/roadmap/data/byFileType/chromhmmSegmentations/ChmmModels/coreMarks/jointModel/final/all.mnemonics.bedFiles.tgz</a>
				</td>
				<td>5 Dec 2016</td>
				<td>
					Roadmap Epigenomics Consortium, et al. 2015. Integrative analysis of 111 reference human epigenomes. <i>Nature.</i> <b>518</b>, 317-330.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/25693563" target="_blank">PMID:25693563</a><br/>
					Ernst, J. and Kellis, M. 2012. ChromHMM: automating chromatin-state discovery and characterization. <i>Nat. Methods.</i> <b>28</b>, 215-6.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/22373907" target="_blank">PMID:22373907</a>
				</td>
			</tr>
			<tr>
			<td>GTEx v6/v7/v8</td>
				<td>eQTLs and gene expression used in the pipeline were obtained from GTEx.<br/>
				</td>
				<td>
					Info and data: <a href="http://www.gtexportal.org/home/" target="_blank">http://www.gtexportal.org/home/</a>
				</td>
				<td>14 Oct 2019</td>
				<td>
					GTEx Consortium. 2015. Human genomics, The genotype-tissue expression (GTEx) pilot analysis: multitissue gene regulation in humans. <i>Science.</i> <b>348</b>, 648-60.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/25954001" target="_blank">PMID:25954001</a>
					<br/>
					GTEx Consortium. 2017. Genetic effects on gene expression across human tissues. <i>Nature.</i> <b>550</b>, 204-213.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/29022597" target="_blank">PMID:29022597</a>
					Aguet, et al. 2019. The GTEx consortium atlas of genetic regulatory effects across human tissues. <i>bioRxiv.</i> doi: https://doi.org/10.1101/787903.<br/>
					<a href="https://www.biorxiv.org/content/10.1101/787903v1" target="_blank">https://doi.org/10.1101/787903</a>
				</td>
			</tr>
			<tr>
				<td>Blood eQTL Browser</td>
				<td>eQTLs of blood cells. Only cis-eQTLs with FDR &le; 0.05 are available in FUMA.</td>
				<td>Info and data: <a href="http://genenetwork.nl/bloodeqtlbrowser/" taget="_blank">http://genenetwork.nl/bloodeqtlbrowser/</a></td>
				<td>17 January 2017</td>
				<td>
					Westra et al. 2013. Systematic identification of trans eQTLs as putative divers of known disease associations. <i>Nat. Genet.</i> <b>45</b>, 1238-1243.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/24013639" target="_blank">PMID:24013639</a>
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
				<td>MuTHER</td>
				<td>eQTLs in Adipose, LCL and Skin samples (only cis eQTLs).</td>
				<td>
					Info: <a href="http://www.muther.ac.uk/" target="_blank">http://www.muther.ac.uk/</a><br/>
					Data: <a href="http://www.muther.ac.uk/Data.html" target="_blank">http://www.muther.ac.uk/Data.html</a>
				</td>
				<td>21 January 2018</td>
				<td>
					Grundberg et al. 2012. Mapping cis and trans regulatory effects across multiple tissues in twins. <i>Nat. Genet.</i> <b>44</b>, 1084-1089.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/22941192" target="_blank">PMID:22941192</a>
				</td>
			</tr>
			<tr>
				<td>xQTLServer</td>
				<td>eQTLs in dorsolateral prefrontal cortex samples.</td>
				<td>
					Info and data: <a href="http://mostafavilab.stat.ubc.ca/xqtl/" target="_blank">http://mostafavilab.stat.ubc.ca/xqtl/</a>
				</td>
				<td>21 January 2018</td>
				<td>
					Ng et al. 2017. An xQTL map integrates the genetic architecture of the human brain's transcriptome and epigenome. <i>Nat. Neurosci.</i> <b>20</b>, 1418-1426.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/28869584" target="_blank">PMID:28869584</a>
				</td>
			</tr>
			<tr>
				<td>CommonMind Consortium</td>
				<td>eQTLs in brain samples. Both cis and trans eQTLs are available</td>
				<td>
					Info and data: <a href="https://www.synapse.org//#!Synapse:syn5585484" target="_blank">https://www.synapse.org//#!Synapse:syn5585484</a>
				</td>
				<td>21 January 2018</td>
				<td>
					Fromer et al. 2016. Gene expression elucidates functional impact of polygenic risk for schizophrenia. <i>Nat. Neurosci.</i> <b>16</b>, 1442-1453.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/27668389" target="_blank">PMID:27668389</a>
				</td>
			</tr>
			<tr>
				<td>eQTLGen</td>
				<td>Meta-analysis of cis and trans eQTLs based on 37 data sets (in total of 31,684 individuals).</td>
				<td>
					Info: <a href="http://www.eqtlgen.org/index.html" target="_blank">http://www.eqtlgen.org/index.html</a><br/>
					Data: <a href="https://molgenis26.gcc.rug.nl/downloads/eqtlgen/cis-eqtl/cis-eQTLs_full_20180905.txt.gz" target="_blank">https://molgenis26.gcc.rug.nl/downloads/eqtlgen/cis-eqtl/cis-eQTLs_full_20180905.txt.gz</a>,
					<a href="https://molgenis26.gcc.rug.nl/downloads/eqtlgen/trans-eqtl/trans-eQTL_significant_20181017.txt.gz" target="_blank">https://molgenis26.gcc.rug.nl/downloads/eqtlgen/trans-eqtl/trans-eQTL_significant_20181017.txt.gz</a>
				</td>
				<td>20 Oct 2018</td>
				<td>
					Vosa et al. 2018. Unraveling the polygenic architecture of complex traits using blood eQTL meta-analysis. <i>bioRxiv</i><br/>
					<a href="https://www.biorxiv.org/content/early/2018/10/19/447367" target="_blank">https://doi.org/10.1101/447367</a>
				</td>
			</tr>
			<tr>
				<td>DICE</td>
				<td>eQTLs of 15 types of immune cells.</td>
				<td>
					Info: <a target="_blank" href="https://dice-database.org/landing">https://dice-database.org/landing</a><br/>
					Data: <a target="_blank" href="https://dice-database.org/downloads">https://dice-database.org/downloads</a>
				</td>
				<td>27 May 2019</td>
				<td>
					Schmiedel et al. 2018. Impact of genetic polymorphisms on human immune cell gene expression. <i>Cell</i> <b>175</b>, 1701-1715.e16.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/30449622" target="_blank">PMID:30449622</a>
				</td>
			</tr>
			<tr>
				<td>van der Wijst et al. scRNA eQTLs</td>
				<td>eQTLs based on scRNA-seq of 9 cell types.</td>
				<td>
					Info and data: <a target="_blank" href="https://molgenis26.target.rug.nl/downloads/scrna-seq/">https://molgenis26.target.rug.nl/downloads/scrna-seq/</a>
				</td>
				<td>27 May 2019</td>
				<td>
					van der Wijst et al. 2018. Single-cell RNA sequencing identifies celltype-specific eQTLs and co-expression QTLs. <i>Nat. Genet.</i> <b>50</b>, 493-497.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/29610479" target="_blank">PMID:29610479</a>
				</td>
			</tr>
			<tr>
				<td>PsychENCODE</td>
				<td>SNP annotations (enhancer, H3K27ac markers), eQTLs and HiC based enhancer-promoter interactions.</td>
				<td>
					Info and data: <a target="_blank" href="http://resource.psychencode.org/">http://resource.psychencode.org/</a>
				</td>
				<td>27 May 2019</td>
				<td>
					Wang et al. 2018. Comprehensive functional genomic resource and integrative model for the human brain. <i>Science</i> <b>14</b>, eaat8464.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/30545857" target="_blank">PMID:30545857</a>
				</td>
			</tr>
			<tr>
				<td>eQTL Catalogue</td>
				<td>Gene level eQTL data generated from a variety of studies, where all of the eQTL datasets were produced in a uniform manner.</td>
				<td>
					Info: <a target="_blank" href="https://www.ebi.ac.uk/eqtl/">https://www.ebi.ac.uk/eqtl/</a> <br/>
					Data: <a target="_blank" href="https://github.com/eQTL-Catalogue/eQTL-Catalogue-resources/blob/master/tabix/tabix_ftp_paths.tsv">https://github.com/eQTL-Catalogue/eQTL-Catalogue-resources/blob/master/tabix/tabix_ftp_paths.tsv</a>
				</td>
				<td>16 March 2020</td>
				<td>
					See tutorial <a target="_blank" href="https://fuma.ctglab.nl/tutorial#eQTLs">https://fuma.ctglab.nl/tutorial#eQTLs</a>.
				</td>
			</tr>
			<tr>
				<td>FANTOM5</td>
				<td>SNP annotations (enhancer and promoter) and enhancer-promoter correlations.</td>
				<td>
					Info: <a target="_blank" href="http://fantom.gsc.riken.jp/5/">http://fantom.gsc.riken.jp/5/</a><br/>
					Data: <a target="_blank" href="http://fantom.gsc.riken.jp/5/data/">http://fantom.gsc.riken.jp/5/data/</a>,
					<a target="_blank" href="http://slidebase.binf.ku.dk/human_enhancers/presets">http://slidebase.binf.ku.dk/human_enhancers/presets</a>
				</td>
				<td>27 May 2019</td>
				<td>
					Andersson et al. 2014. An atlas of active enhancers across human cell types and tissues. <i>Nature</i> <b>507</b>, 455-461.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/24670763" target="_blank">PMID:24670763</a><br>
					FANTOM Consortium. A promoter-level mammalian expression atlas. <i>Nature</i> <b>507</b>, 462-470.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/24670764" target="_blank">PMID:24670764</a><br>
					Bertin et al. 2017. Linking FANTOM5 CAGE peaks to annotations with CAGEscan. <i>Sci. Data</i> <b>4</b>, 170147.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/28972578" target="_blank">PMID:28972578</a><br>
				</td>
			</tr>
			<tr>
				<td>BrainSpan</td>
				<td>Gene expression data of developmental brain samples.</td>
				<td>
					Info and data: <a href="http://www.brainspan.org/static/download" target="_blank">http://www.brainspan.org/static/download</a>
				</td>
				<td>31 January 2018</td>
				<td>
					Kang et al. 2011. Spatio-temporal transcriptome of the human brain. <i>Nature</i> <b>478</b>, 483-489.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/22031440" target="_blank">PMID:22031440</a>
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
				<td>Giusti-Rodriguez et al. 2019 (Hi-C)</td>
				<td>Hi-C data (significant loops) of adult and fetal cortex.
					Only significant loops after Bonferroni correction (Pbon < 0.001) are available.
				</td>
				<td>The data was kindly shared by Patric F. Sullivan.</td>
				<td>13 Feb 2019</td>
				<td>
					Giusti-Rodriguez, P. et al. 2019. Using three-dimentional regulatory chromatin interactions from adult and fetal cortex to interpret genetic results for psychiatric disorders and cognitive traits. <i>bioRxiv.</i><br/>
					<a href="https://www.biorxiv.org/content/10.1101/406330v2" target="_blank">https://doi.org/10.1101/406330</a>
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
				<td>MsigDB v7.0</td>
				<td>Collection of publicly available gene sets. Data sets include e.g. KEGG, Reactome, BioCarta, GO terms and so on.</td>
				<td>Info and data: <a href="http://software.broadinstitute.org/gsea/msigdb" target="_blank">http://software.broadinstitute.org/gsea/msigdb</a></td>
				<td>14 Oct 2019</td>
				<td>
					Liberzon, A. et al. 2011. Molecular signatures database (MSigDB) 3.0. <i>Bioinformatics.</i> <b>27</b>, 1739-40.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/21546393" target="_blank">PMID:21546393</a>
				</td>
			</tr>
			<tr>
				<td>WikiPathways v20191010</td>
				<td>The curated biological pathways.</td>
				<td style="word-break: break-all;">
					Info: <a href="http://wikipathways.org/index.php/WikiPathways" target="_blank">http://wikipathways.org/index.php/WikiPathways</a><br/>
					Data: <a href=">http://data.wikipathways.org/20161110/gmt/wikipathways-20161110-gmt-Homo_sapiens.gmt" target="_blank">http://data.wikipathways.org/20161110/gmt/wikipathways-20161110-gmt-Homo_sapiens.gmt</a>
				</td>
				<td>14 Oct 2019</td>
				<td>
					Kutmon, M., et al. 2016. WikiPathways: capturing the full diversity of pahtway knowledge. <i>Nucleic Acids Res.</i> <b>44</b>, 488-494.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/26481357" target="_blank">PMID:26481357</a>
				</td>
			</tr>
			<tr>
				<td>GWAS-catalog e104_2021-09-15</td>
				<td>A database of reported SNP-trait associations.</td>
				<td>
					Info: <a href="https://www.ebi.ac.uk/gwas/" target="_blank">https://www.ebi.ac.uk/gwas/</a><br/>
					Data: <a href="https://www.ebi.ac.uk/gwas/downloads" target="_blank">https://www.ebi.ac.uk/gwas/downloads</a>
				</td>
				<td>18 Sept 2021</td>
				<td>
					MacArthur, J., et al. 2016. The new NHGRI-EBI Catalog of published genome-wide association studies (GWAS Catalog). <i>Nucleic Acids Res.</i> pii:gkw1133.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/27899670" target="_blank">PMID:27899670</a>
				</td>
			</tr>
			<tr>
				<td>DrugBank v5.1.4</td>
				<td>Targeted genes (protein) of drugs in DrugBank was obtained to assign drug ID for input genes.</td>
				<td>
					Info: <a href="https://www.ncbi.nlm.nih.gov/pubmed/27899670" target="_blank">https://www.ncbi.nlm.nih.gov/pubmed/27899670</a><br/>
					Data: <a href="https://www.drugbank.ca/releases/latest#protein-identifiers" target="_blank">https://www.drugbank.ca/releases/latest#protein-identifiers</a>
				</td>
				<td>14 Oct 2019</td>
				<td>
					Wishart, DS., et al. 2008. DrugBank: a knowledgebase for drugs, drug actions and drug targets. <i>Nucleic Acis Res.</i> <b>36</b>, D901-6.<br/>
					<a href="https://www.ncbi.nlm.nih.gov/pubmed/18048412" target="_blank">PMID:18048412</a>
				</td>
			</tr>
			<tr>
				<td>pLI</td>
				<td>A gene score annotated to prioritized genes. The score is the probability of being loss-of-function intolerance.
				</td>
				<td style="word-break:break-all;">
					Info: <a href="http://exac.broadinstitute.org/" target="_blank">http://exac.broadinstitute.org/</a><br/>
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
					Info: <a href="http://journals.plos.org/plosgenetics/article?id=10.1371/journal.pgen.1005492" target="_blank">http://journals.plos.org/plosgenetics/article?id=10.1371/journal.pgen.1005492</a><br/>
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
