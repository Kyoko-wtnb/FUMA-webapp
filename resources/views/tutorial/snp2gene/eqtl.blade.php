<h3 id="eQTLs">eQTLs</h3>
FUMA contains several data sources of eQTLs and each data source is described in this section.
<h4><strong>eQTL data sources</strong></h4>
<div style="padding-left: 40px;">
	<h4><strong>1. GTEx v6</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://www.gtexportal.org/home/datasets">http://www.gtexportal.org/home/datasets</a>.
		Under the section of GTEx V6, from single tissue eQTL data both <span style="color: blue;">GTEx_analysis_V6_eQTLs.tar.gz</span>
		for significant SNP-gene association based on permutation, and
		<span style="color: blue;">GTEx_Analysis_V6_all-snp-gene-associations.tar</span> for every SNP-gene association test (including non-significant paris)
		were downloaded.<br/>
		GTEx eQTL v6 contains 44 different tissue types across 30 general tissue types.
	</p>
	<p><strong>Description</strong><br/>
		FUMA contains all SNP-gene pairs of cis-eQTL with nominal P-value &lt; 0.05 (including non-significant associations).
		Significant eQTLs are defined as FDR (gene q-value) &le; 0.05.
		The gene FDR is pre-calculated by GTEx and every gene-tissue pair has a defined P-value threshold for eQTLs based on permutation.<br/>
		Signed statistics are t-statistics.
	</p>
	<p><strong>Samples</strong><br/>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="#gtexTable" data-toggle="collapse">GTEx eQTL tissue types and sample size</a><br/>
			</div>
			<div id="gtexTable" class="panel-body collapse">
				<span class="info"><i class="fa fa-info"></i>
					The table contains the list of tissue types available in GTEx v6 for cis-eQTL (only tissues with genotyped sample size &ge; 70).
				</span>
				<table class="table table-bordered">
					<thead>
						<th>General tissue type</th>
						<th>Tissue type</th>
						<th>Genotyped sample size</th>
					</thead>
					<tbody>
						<tr><td>Adipose Tissue</td><td>Adipose Subcutaneous</td><td>298</td></tr>
						<tr><td>Adipose Tissue</td><td>Adipose Visceral Omentum</td><td>185</td></tr>
						<tr><td>Adrenal Gland</td><td>Adrenal Gland</td><td>126</td></tr>
						<tr><td>Blood</td><td>Cells EBV-transformed lymphocytes</td><td>114</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Aorta</td><td>197</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Coronary</td><td>118</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Tibial</td><td>285</td></tr>
						<tr><td>Blood</td><td>Whole Blood</td><td>338</td></tr>
						<tr><td>Brain</td><td>Brain Anterior cingulate cortex BA24</td><td>72</td></tr>
						<tr><td>Brain</td><td>Brain Caudate basal ganglia</td><td>100</td></tr>
						<tr><td>Brain</td><td>Brain Cerebellar Hemisphere</td><td>89</td></tr>
						<tr><td>Brain</td><td>Brain Cerebellum</td><td>103</td></tr>
						<tr><td>Brain</td><td>Brain Cortex</td><td>96</td></tr>
						<tr><td>Brain</td><td>Brain Frontal Cortex BA9</td><td>92</td></tr>
						<tr><td>Brain</td><td>Brain Hippocampus</td><td>81</td></tr>
						<tr><td>Brain</td><td>Brain Hypothalamus</td><td>81</td></tr>
						<tr><td>Brain</td><td>Brain Nucleus accumbens basal ganglia</td><td>93</td></tr>
						<tr><td>Brain</td><td>Brain Putamen basal ganglia</td><td>82</td></tr>
						<tr><td>Breast</td><td>Breast Mammary Tissue</td><td>183</td></tr>
						<tr><td>Colon</td><td>Colon Sigmoid</td><td>124</td></tr>
						<tr><td>Colon</td><td>Colon Transverse</td><td>169</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Gastroesophageal Junction</td><td>127</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Mucosa</td><td>241</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Muscularis</td><td>218</td></tr>
						<tr><td>Heart</td><td>Heart Atrial Appendage</td><td>159</td></tr>
						<tr><td>Heart</td><td>Heart Left Ventricle</td><td>190</td></tr>
						<tr><td>Liver</td><td>Liver</td><td>97</td></tr>
						<tr><td>Lung</td><td>Lung</td><td>278</td></tr>
						<tr><td>Muscle</td><td>Muscle Skeletal</td><td>361</td></tr>
						<tr><td>Nerve</td><td>Nerve Tibial</td><td>256</td></tr>
						<tr><td>Ovary</td><td>Ovary</td><td>85</td></tr>
						<tr><td>Pancreas</td><td>Pancreas</td><td>149</td></tr>
						<tr><td>Pituitary</td><td>Pituitary</td><td>87</td></tr>
						<tr><td>Prostate</td><td>Prostate</td><td>87</td></tr>
						<tr><td>Skin</td><td>Cells Transformed fibroblasts</td><td>272</td></tr>
						<tr><td>Skin</td><td>Skin Not Sun Exposed Suprapubic</td><td>196</td></tr>
						<tr><td>Skin</td><td>Skin Sun Exposed Lower leg</td><td>302</td></tr>
						<tr><td>Small Intestine</td><td>Small Intestine Terminal Ileum</td><td>77</td></tr>
						<tr><td>Spleen</td><td>Spleen</td><td>89</td></tr>
						<tr><td>Stomach</td><td>Stomach</td><td>170</td></tr>
						<tr><td>Testis</td><td>Testis</td><td>157</td></tr>
						<tr><td>Thyroid</td><td>Thyroid</td><td>278</td></tr>
						<tr><td>Uterus</td><td>Uterus</td><td>70</td></tr>
						<tr><td>Vagina</td><td>Vagina</td><td>79</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</p><br/>

	<h4><strong>2. Blood eQTL browser (Westra et al. 2013)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://genenetwork.nl/bloodeqtlbrowser/">http://genenetwork.nl/bloodeqtlbrowser/</a>.
	</p>
	<p><strong>Description</strong><br/>
		The data only include eQTLs with FDR &le; 0.5.
		Genes in the original files were mapped to Ensembl ID in which genes are removed if they are not mapped to Ensembl ID.<br/>
		Signed statistics are Z-scores.
	</p>
	<p><strong>Samples</strong><br/>
		5,311 peripheral blood samples from 7 studies (<a href="https://www.ncbi.nlm.nih.gov/pubmed/3991562">Westra et al. 2013</a>).
	</p><br/>

	<h4><strong>3. BIOS QTL browser (Zhernakova et al. 2017)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://genenetwork.nl/biosqtlbrowser/">http://genenetwork.nl/biosqtlbrowser/</a>.
		<span style="color:blue;">Cis-eQTLs Gene-level all primary effects</span> was downloaded which includes all SNP-gene pairs with FDR &le; 0.05.
	</p>
	<p><strong>Description</strong><br/>
		The data only include eQTLs with FDR &le; 0.05.<br/>
		Signed statistics are betas.
	</p>
	<p><strong>Samples</strong><br/>
		2,116 whole peripheral blood samples of healthy adults from 4 Dutch cohorts (<a href="https://www.ncbi.nlm.nih.gov/pubmed/27918533">Zhernakova et al. 2017</a>).
	</p><br/>

	<h4><strong>4. BRAINEAC</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL was obtained from <a target="_blank" href="http://www.braineac.org/">http://www.braineac.org/</a>.<br/>
	</p>
	<p><strong>Description</strong><br/>
		The data include all eQTLs with nominal P-value < 0.05.
		Since tested allele was not provided in the original data source, minor alleles in 1000 genome phase 3 are assigned as tested alleles.<br/>
		Signed statistics are t-statistics.<br/>
		eQTLs were identified for each of the following 10 brain regions and based on averaged expression across all of them.<br/>
		<span class="info"><i class="fa fa-info"></i>
			Alignment of risk increasing allele and eQTL tested allele was not performed for this data source,
			since tested allele is not available in the original data source
			(replaced with "NA" in the result table).
		</span>
		<ul>
			<li>Cerebellar cortex</li>
			<li>Frontal cortex</li>
			<li>Hippocampus</li>
			<li>Inferior olivary nucleus (sub-dissected from the medulla)</li>
			<li>Occipital cortex</li>
			<li>Putamen (at the level of the anterior commissure)</li>
			<li>Substantia nigra</li>
			<li>Temporal cortex</li>
			<li>Thalamus (at the level of the lateral geniculate nucleus)</li>
			<li>Intralobular white matter</li>
		</ul>
	</p>
	<p><strong>Samples</strong><br/>
		134 neuropathologically confirmed control individuals of European descent from <a target="_blank" href="https://ukbec.wordpress.com/">UK Brain Expression Consortium</a>
		(<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/25174004">Ramasamy et al. 2014</a>).
	</p><br/>

	<h4><strong>5. GTEx v7</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://www.gtexportal.org/home/datasets">http://www.gtexportal.org/home/datasets</a>.
		Under the section of GTEx V7, from single tissue eQTL data both <span style="color: blue;">GTEx_analysis_v7_eQTLs.tar.gz</span>
		for significant SNP-gene association based on permutation, and
		<span style="color: blue;">GTEx_Analysis_v7_all_associations.tar.gz</span> for every SNP-gene association test (including non-significant pairs)
		were downloaded.<br/>
		GTEx eQTL v7 contains 53 different tissue types across 30 general tissue types.
	</p>
	<p><strong>Description</strong><br/>
		FUMA contains all SNP-gene pairs of cis-eQTL with nominal P-value &lt; 0.05 (including non-significant associations).
		Significant eQTLs are defined as FDR (gene q-value) &le; 0.05.
		The gene FDR is pre-calculated by GTEx and every gene-tissue pair has a defined P-value threshold for eQTLs based on permutation.<br/>
		Signed statistics are betas.
	</p>
	<p><strong>Samples</strong><br/>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="#gtexTable_v7" data-toggle="collapse">GTEx eQTL tissue types and sample size</a><br/>
			</div>
			<div id="gtexTable_v7" class="panel-body collapse">
				<span class="info"><i class="fa fa-info"></i>
					The table contains the list of tissue types available in GTEx v7 for cis-eQTL (only tissues with genotyped sample size &ge; 70).
				</span>
				<table class="table table-bordered">
					<thead>
						<th>General tissue type</th>
						<th>Tissue type</th>
						<th>Genotyped sample size</th>
					</thead>
					<tbody>
						<tr><td>Adipose Tissue</td><td>Adipose Subcutaneous</td><td>385</td></tr>
						<tr><td>Adipose Tissue</td><td>Adipose Visceral Omentum</td><td>313</td></tr>
						<tr><td>Adrenal Gland</td><td>Adrenal Gland</td><td>175</td></tr>
						<tr><td>Blood</td><td>Cells EBV-transformed lymphocytes</td><td>117</td></tr>
						<tr><td>Blood</td><td>Whole Blood</td><td>369</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Aorta</td><td>267</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Coronary</td><td>152</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Tibial</td><td>388</td></tr>
						<tr><td>Brain</td><td>Brain Amygdala</td><td>88</td></tr>
						<tr><td>Brain</td><td>Brain Anterior cingulate cortex BA24</td><td>109</td></tr>
						<tr><td>Brain</td><td>Brain Caudate basal ganglia</td><td>144</td></tr>
						<tr><td>Brain</td><td>Brain Cerebellar Hemisphere</td><td>125</td></tr>
						<tr><td>Brain</td><td>Brain Cerebellum</td><td>154</td></tr>
						<tr><td>Brain</td><td>Brain Cortex</td><td>136</td></tr>
						<tr><td>Brain</td><td>Brain Frontal Cortex BA9</td><td>118</td></tr>
						<tr><td>Brain</td><td>Brain Hippocampus</td><td>111</td></tr>
						<tr><td>Brain</td><td>Brain Hypothalamus</td><td>108</td></tr>
						<tr><td>Brain</td><td>Brain Nucleus accumbens basal ganglia</td><td>130</td></tr>
						<tr><td>Brain</td><td>Brain Putamen basal ganglia</td><td>111</td></tr>
						<tr><td>Brain</td><td>Brain Spinal cord cervical c-1</td><td>83</td></tr>
						<tr><td>Brain</td><td>Brain Substantia nigra</td><td>80</td></tr>
						<tr><td>Breast</td><td>Breast Mammary Tissue</td><td>251</td></tr>
						<tr><td>Colon</td><td>Colon Sigmoid</td><td>203</td></tr>
						<tr><td>Colon</td><td>Colon Transverse</td><td>246</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Gastroesophageal Junction</td><td>213</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Mucosa</td><td>358</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Muscularis</td><td>335</td></tr>
						<tr><td>Heart</td><td>Heart Atrial Appendage</td><td>264</td></tr>
						<tr><td>Heart</td><td>Heart Left Ventricle</td><td>272</td></tr>
						<tr><td>Liver</td><td>Liver</td><td>153</td></tr>
						<tr><td>Lung</td><td>Lung</td><td>383</td></tr>
						<tr><td>Muscle</td><td>Muscle Skeletal</td><td>491</td></tr>
						<tr><td>Nerve</td><td>Nerve Tibial</td><td>361</td></tr>
						<tr><td>Ovary</td><td>Ovary</td><td>122</td></tr>
						<tr><td>Pancreas</td><td>Pancreas</td><td>220</td></tr>
						<tr><td>Pituitary</td><td>Pituitary</td><td>157</td></tr>
						<tr><td>Prostate</td><td>Prostate</td><td>132</td></tr>
						<tr><td>Salivary Gland</td><td>Minor Salivary Gland</td><td>85</td></tr>
						<tr><td>Skin</td><td>Cells Transformed fibroblasts</td><td>300</td></tr>
						<tr><td>Skin</td><td>Skin Not Sun Exposed Suprapubic</td><td>335</td></tr>
						<tr><td>Skin</td><td>Skin Sun Exposed Lower leg</td><td>414</td></tr>
						<tr><td>Small Intestine</td><td>Small Intestine Terminal Ileum</td><td>122</td></tr>
						<tr><td>Spleen</td><td>Spleen</td><td>146</td></tr>
						<tr><td>Stomach</td><td>Stomach</td><td>237</td></tr>
						<tr><td>Testis</td><td>Testis</td><td>225</td></tr>
						<tr><td>Thyroid</td><td>Thyroid</td><td>399</td></tr>
						<tr><td>Uterus</td><td>Uterus</td><td>101</td></tr>
						<tr><td>Vagina</td><td>Vagina</td><td>106</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</p><br/>

	<h4><strong>6. MuTHER (Grundberg et al. 2012)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://www.muther.ac.uk/">http://www.muther.ac.uk/</a>.
	</p>
	<p><strong>Description</strong><br/>
		Chromosome coordinate was lifted over to hg19 from hg18 using liftOver software.
		Gene names are mapped to Ensembl ID (excluded genes which are not mapped to ENSG ID).
		Since only tested allele was provided, other allele was extracted from 1000G EUR population.
		FDR (or any corrected P-value) was not available in the original data (in the FUMA, FDR column was replaced with NA).
		<br/>
		Signed statistics are betas.
		<br/>
		<span class="info"><i class="fa fa-info"></i>
			Since FDR is not available, MuTHER eQTLs can be only used when P-value threshold provided by user,
			not "only significant snp-gene pairs" option.
		</span>
	</p>
	<p><strong>Samples</strong><br/>
		856 female individuals of European descent recruited from
		the TwinsUK Adult twin registry (<a href="https://www.ncbi.nlm.nih.gov/pubmed/22941192">Grundberg et al. 2012</a>).
		<ul>
			<li>Adipose (N=855)</li>
			<li>Skin (N=847)</li>
			<li>LCL (N=837)</li>
		</ul>
	</p><br/>

	<h4><strong>7. xQTLServer (Ng et al. 2017)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://mostafavilab.stat.ubc.ca/xqtl/">http://mostafavilab.stat.ubc.ca/xqtl/</a>.
	</p>
	<p><strong>Description</strong><br/>
		Gene names are mapped to Ensembl ID (excluded genes which are not mapped to ENSG ID).
		Since alleles were not available in the original data, extracted from 1000G EUR population based on chromosome coordinate.
		FDR was not provided in the original data source, but the FDR column was replaced with Bonferroni corrected p-value,
		as it was used in the original study (corrected for all tested SNP-gene pairs 60,456,556).
		<br/>
		Signed statistics are not available.
		<br/>
		<span class="info"><i class="fa fa-info"></i>
			Alignment of risk increasing allele and eQTL tested allele was not performed for this data source,
			since tested allele and signed statistics are not available in the original data source
			(replaced with "NA" in the result table).
		</span>
	</p>
	<p><strong>Samples</strong><br/>
		494 dorsolateral prefrontal cortex samples (<a href="https://www.ncbi.nlm.nih.gov/pubmed/28869584">Ng et al. 2017</a>).
	</p><br/>

	<h4><strong>8. CommonMind Consortium (Fromer et al. 2016)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="https://www.synapse.org//#!Synapse:syn5585484">https://www.synapse.org//#!Synapse:syn5585484</a>.
		Both eQTLs with and without SVA are included.
	</p>
	<p><strong>Description</strong><br/>
		Publicly available eQTLs from CMC (without application) is binned by FDR.
		Therefore, nominal P-value is not available (replaced with NA).
		FDR was binned into the following four groups, &lt;0.2, &lt;0.1, &lt;0.05 and &lt;0.01.
		As numeric value is required for filtering during SNP2GENE process, those categorical values are replaced with
		0.199, 0.099, 0.049 and 0.009 respectively.
		<br/>
		Signed statistics are not available but since expressed increasing allele was provided, signed_stats column is replaced with 1.
		<br/>
		Trans eQTLs are also available for CMC data set (as a separated option from cis-eQTLs).
	</p>
	<p><strong>Samples</strong><br/>
		Post-mortem brain samples from 467 Caucasian individuals (209 with SCZ, 206 controls and 52 AFF cases; <a href="https://www.ncbi.nlm.nih.gov/pubmed/27668389">Fromer et al. 2016</a>).
	</p><br/>

	<h4><strong>9. eQTLGen (Vosa et al. 2018)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://www.eqtlgen.org/index.html">http://www.eqtlgen.org/index.html</a>.
		For cis-eQTLs, <span style="color:blue">cis-eQTLs_full_20180905.txt.gz</span>,
		for trans-eQTLs, <span style="color:blue">trans-eQTL_significant_20181017.txt.gz</span> was used.
	</p>
	<p><strong>Description</strong><br/>
		Full summary statistics were downloaded.
		For cis-eQTLs, full summary statistics was downloaded.
		In the dataset, every SNP-gene pair with a distance &lt;1Mb from the center of the gene and tested in at least 2 cohorts was included.
		For trans-eQTLs, only significant eQTLs were included in FUMA since the cross-mapping effects were not filtered in the downloadable full summary statistics.
		In the original study, every SNP-gene pair with a distance &gt;5Mb and tested in at least 2 cohorts was included.
		FDR was estimated based on permutations.
		Please refer the original study for more details (<a href="https://www.biorxiv.org/content/early/2018/10/19/447367">Vosa et al. 2018</a>).
		Ensembl gene ID is used as provided in the original file.
		<br/>
		Signed statistics are z-scores.
	</p>
	<p><strong>Samples</strong><br/>
		Meta-analysis of cis-/trans-eQTLs from 37 datasets with a total of 31,684 individuals.
	</p><br/>

	<h4><strong>10. PsychENCODE (Wang et al. 2018)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://resource.psychencode.org">http://resource.psychencode.org</a>.
		We used significant (<span style="color:blue">DER-08a_hg19_eQTL.significant</span>).
	</p>
	<p><strong>Description</strong><br/>
		The available eQTLs were filtered based on an FDR &lt;0.05 and an expression &gt;0.1 FPKM in at least 10 samples.
		Please refer the original study for more details (<a href="https://science.sciencemag.org/content/362/6420/eaat8464.full">Wang et al. 2018</a>).
		Ensembl gene ID is used as provided in the original file.
		<br/>
		The signed statistics are betas.
	</p>
	<p><strong>Samples</strong><br/>
		The eQTLs were identified from 1387 individuals.
		<br/>
	</p><br/>

	<h4><strong>11. DICE (Schmiedel et al. 2018)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="https://dice-database.org/downloads#eqtl_download">https://dice-database.org/downloads#eqtl_download</a>.
		The cis-eQTLs were obtained from the DICE eQTL section of the website.
	</p>
	<p><strong>Description</strong><br/>
		Only significant eQTLs are present in the dataset.
		The available eQTLs were filtered based on FDR&lt;0.05, nominal P-value&lt;0.0001, and TPM&gt;0.1.
		FDR was estimated using permutation.
		Please refer the original study for more details (<a href="https://www.sciencedirect.com/science/article/pii/S009286741831331X?via%3Dihub">Schmiedel et al. 2018</a>).
		Ensembl gene ID is used as provided in the original file.
		<span class="info"><i class="fa fa-info"></i>
			FDR was not provided in the original source, but since the eQTLs were already filtered on FDR&lt;0.05
			all eQTLs were assigned to FDR 0.049 to be able to pass the filtering of the "only significant snp-gene pairs" option.
		</span>
		<br/>
		Signed statistics are betas.
		<strong>The cell types were:</strong>
		<ul>
			<li>Naive B cells</li>
			<li>Activated CD4 T cells</li>
			<li>Naive CD4 T cells</li>
			<li>Activated CD8 T cells</li>
			<li>Naive CD8 T cells</li>
			<li>Classical Monocytes</li>
			<li>Non-classical Monocytes</li>
			<li>NK cell, CD56dim CD16+</li>
			<li>TFH CD4 T cells</li>
			<li>TH117 CD4 T cells</li>
			<li>TH1 CD4 T cells</li>
			<li>TH2 CD4 T cells</li>
			<li>Memory TREG CD4 T cells</li>
			<li>Naive TREG CD4 T cells</li>
    	</ul>
	</p>
	<p><strong>Samples</strong><br/>
		The eQTLs were identified in 13 immune cell types isolated from 106 leukapheresis samples provided by 91 healthy subjects.
	</p><br/>

	<h4><strong>12. van der Wijst et al. scRNA eQTLs (van der Wijst et al. 2018)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="https://molgenis26.target.rug.nl/downloads/scrna-seq/">https://molgenis26.target.rug.nl/downloads/scrna-seq/</a>.
	</p>
	<p><strong>Description</strong><br/>
		The tested allele was specified in the data, but the other allele was not.
		FDR was estimated using permutation.
		Please refer the original study for more details (<a href="https://www.ncbi.nlm.nih.gov/pmc/articles/PMC5905669/">van der Wijst et al. 2018</a>).
		Ensembl gene ID is used as provided in the original file.
		The summary statistics are Z scores.
		<br/>
		<strong>The cell types were:</strong>
		<ul>
			<li>B cells</li>
			<li>CD4 T cells</li>
			<li>CD8 T cells</li>
			<li>Peripheral blood mononuclear cells (PBMC)</li>
			<li>Monocytes</li>
			<li>Classical monocytes</li>
			<li>Non-classical monocytes</li>
			<li>Natural killer (NK) cells</li>
			<li>Dendritic cells (DC)</li>
    	</ul>
	</p>
	<p><strong>Samples</strong><br/>
		The eQTLs were identified from 25,000 peripheral blood mononuclear cells (PBMCs) from 45 donors.
	</p><br/>

	<h4><strong>13. GTEx v8</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://www.gtexportal.org/home/datasets">http://www.gtexportal.org/home/datasets</a>.
		Under the section of GTEx V8, from single tissue eQTL data both <span style="color: blue;">GTEx_Analysis_v8_eQTL.tar</span>
		for significant SNP-gene associations, and all tested pairs of SNP-gene were obtained from GCP (including non-significant pairs).<br/>
		GTEx eQTL v8 contains 54 different tissue types across 30 general tissue types.
	</p>
	<p><strong>Description</strong><br/>
		FUMA contains all SNP-gene pairs of cis-eQTL with nominal P-value &lt; 0.05 (including non-significant associations).
		Significant eQTLs are defined as FDR (gene q-value) &le; 0.05.
		The gene FDR is pre-calculated by GTEx and every gene-tissue pair has a defined P-value threshold for eQTLs based on permutation.<br/>
		Signed statistics are betas.
	</p>
	<p><strong>Samples</strong><br/>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="#gtexTable_v8" data-toggle="collapse">GTEx eQTL tissue types and sample size</a><br/>
			</div>
			<div id="gtexTable_v8" class="panel-body collapse">
				<span class="info"><i class="fa fa-info"></i>
					The table contains the list of tissue types available in GTEx v8 for cis-eQTL (only tissues with genotyped sample size &ge; 70).
				</span>
				<table class="table table-bordered">
					<thead>
						<th>General tissue type</th>
						<th>Tissue type</th>
						<th>Genotyped sample size</th>
					</thead>
					<tbody>
						<tr><td>Adipose Tissue</td><td>Adipose Subcutaneous</td><td>581</td></tr>
						<tr><td>Adipose Tissue</td><td>Adipose Visceral Omentum</td><td>469</td></tr>
						<tr><td>Adrenal Gland</td><td>Adrenal Gland</td><td>233</td></tr>
						<tr><td>Blood</td><td>Cells EBV-transformed lymphocytes</td><td>147</td></tr>
						<tr><td>Blood</td><td>Whole Blood</td><td>670</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Aorta</td><td>387</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Coronary</td><td>213</td></tr>
						<tr><td>Blood Vessel</td><td>Artery Tibial</td><td>584</td></tr>
						<tr><td>Brain</td><td>Brain Amygdala</td><td>129</td></tr>
						<tr><td>Brain</td><td>Brain Anterior cingulate cortex BA24</td><td>147</td></tr>
						<tr><td>Brain</td><td>Brain Caudate basal ganglia</td><td>194</td></tr>
						<tr><td>Brain</td><td>Brain Cerebellar Hemisphere</td><td>175</td></tr>
						<tr><td>Brain</td><td>Brain Cerebellum</td><td>209</td></tr>
						<tr><td>Brain</td><td>Brain Cortex</td><td>205</td></tr>
						<tr><td>Brain</td><td>Brain Frontal Cortex BA9</td><td>175</td></tr>
						<tr><td>Brain</td><td>Brain Hippocampus</td><td>165</td></tr>
						<tr><td>Brain</td><td>Brain Hypothalamus</td><td>170</td></tr>
						<tr><td>Brain</td><td>Brain Nucleus accumbens basal ganglia</td><td>202</td></tr>
						<tr><td>Brain</td><td>Brain Putamen basal ganglia</td><td>170</td></tr>
						<tr><td>Brain</td><td>Brain Spinal cord cervical c-1</td><td>126</td></tr>
						<tr><td>Brain</td><td>Brain Substantia nigra</td><td>114</td></tr>
						<tr><td>Breast</td><td>Breast Mammary Tissue</td><td>396</td></tr>
						<tr><td>Colon</td><td>Colon Sigmoid</td><td>318</td></tr>
						<tr><td>Colon</td><td>Colon Transverse</td><td>368</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Gastroesophageal Junction</td><td>330</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Mucosa</td><td>497</td></tr>
						<tr><td>Esophagus</td><td>Esophagus Muscularis</td><td>465</td></tr>
						<tr><td>Heart</td><td>Heart Atrial Appendage</td><td>372</td></tr>
						<tr><td>Heart</td><td>Heart Left Ventricle</td><td>386</td></tr>
						<tr><td>Kidney</td><td>Kidney Cortex</td><td>73</td></tr>
						<tr><td>Liver</td><td>Liver</td><td>208</td></tr>
						<tr><td>Lung</td><td>Lung</td><td>515</td></tr>
						<tr><td>Muscle</td><td>Muscle Skeletal</td><td>706</td></tr>
						<tr><td>Nerve</td><td>Nerve Tibial</td><td>532</td></tr>
						<tr><td>Ovary</td><td>Ovary</td><td>167</td></tr>
						<tr><td>Pancreas</td><td>Pancreas</td><td>305</td></tr>
						<tr><td>Pituitary</td><td>Pituitary</td><td>237</td></tr>
						<tr><td>Prostate</td><td>Prostate</td><td>221</td></tr>
						<tr><td>Salivary Gland</td><td>Minor Salivary Gland</td><td>144</td></tr>
						<tr><td>Skin</td><td>Cells Clustured fibroblasts</td><td>483</td></tr>
						<tr><td>Skin</td><td>Skin Not Sun Exposed Suprapubic</td><td>517</td></tr>
						<tr><td>Skin</td><td>Skin Sun Exposed Lower leg</td><td>605</td></tr>
						<tr><td>Small Intestine</td><td>Small Intestine Terminal Ileum</td><td>174</td></tr>
						<tr><td>Spleen</td><td>Spleen</td><td>227</td></tr>
						<tr><td>Stomach</td><td>Stomach</td><td>324</td></tr>
						<tr><td>Testis</td><td>Testis</td><td>322</td></tr>
						<tr><td>Thyroid</td><td>Thyroid</td><td>574</td></tr>
						<tr><td>Uterus</td><td>Uterus</td><td>142</td></tr>
						<tr><td>Vagina</td><td>Vagina</td><td>156</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</p><br/>

	<h4><strong>14. eQTL Catalogue</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from the eQTLcatalogue (not from the original data source).
		The paths to individual datasets can be found at <a href="https://github.com/eQTL-Catalogue/eQTL-Catalogue-resources/blob/master/tabix/tabix_ftp_paths.tsv">https://github.com/eQTL-Catalogue/eQTL-Catalogue-resources/blob/master/tabix/tabix_ftp_paths.tsv</a>.
		Only the gene level (ge) files were included.
		Details of each dataset are described below.
		Datasets which were already present on FUMA have not been included (DICE & xQTLServer).
	</p>
	<p><strong>Description</strong><br/>
		The eQTLs were mapped to hg19 from hg38 using liftOver software.
		Significant eQTLs are defined using a nominal p-value (0.00001).
		More information on the methods used to generate the eQTL data can be found at <a href="https://www.ebi.ac.uk/eqtl/Methods/">https://www.ebi.ac.uk/eqtl/Methods/</a>.<br/>
	</p>
	<p><strong>Datasets</strong><br/>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="#eQTLcatalogueTable" data-toggle="collapse">eQTL Catalogue datasets, tissue types, and sample sizes</a><br/>
			</div>
			<div id="eQTLcatalogueTable" class="panel-body collapse">
				<span class="info"><i class="fa fa-info"></i>
					The table contains the list of datasets included from the eQTL Catalogue.
				</span>
				<table class="table table-bordered">
					<thead>
						<th>Dataset</th>
						<th>Pubmed ID</th>
						<th>Tissue types</th>
						<th>Conditions</th>
						<th>Sample size (Samples/Donors)</th>
					</thead>
					<tbody>
						<tr><td>Alasoo_2018</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/29379200">29379200</a></td><td>Macrophage</td><td>Naive, IFNg, Salmonella, IFNg + Salmonella</td><td>336/84</td></tr>
						<tr><td>BLUEPRINT</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/27863251">27863251</a></td><td>Monocytes, neutrophils, T-cells</td><td></td><td>554/197</td></tr>
						<tr><td>BrainSeq</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/30050107">30050107</a></td><td>Dorsolateral prefrontal cortex</td><td></td><td>484/484</td></tr>
						<tr><td>CEDAR</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/29930244">29930244</a></td><td>CD4 and CD8 T-cells, monocytes, neutrophils, platelet, B-cells, ileum, rectum, transverse colon</td><td></td><td>2338/322</td></tr>
						<tr><td>Fairfax_2012</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/22446964">22446964</a></td><td>B-cells</td><td></td><td>282/282</td></tr>
						<tr><td>Fairfax_2014</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/24604202">24604202</a></td><td>Monocytes</td><td>Naive, IFN24, LPS2, LPS24</td><td>1372/424</td></tr>
						<tr><td>GENCORD</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/23755361">23755361</a></td><td>Lymphoblastoid cell lines, fibroblasts, T-cells</td><td></td><td>560/195</td></tr>
						<tr><td>GEUVADIS</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/24037378">24037378</a></td><td>Lymphoblastoid cell lines</td><td></td><td>445/445</td></tr>
						<tr><td>HipSci</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/28489815">28489815</a></td><td>iPSCs</td><td></td><td>322/322</td></tr>
						<tr><td>Kasela_2017</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/28248954">28248954</a></td><td>CD4 and CD8 T-cells</td><td></td><td>533/297</td></tr>
						<tr><td>Lepik_2017</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/28922377">28922377</a></td><td>Blood</td><td></td><td>491/491</td></tr>
						<tr><td>Naranbhai_2015</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/26151758">26151758</a></td><td>Neutrophils</td><td></td><td>93/93</td></tr>
						<tr><td>Nedelec_2016</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/27768889">27768889</a></td><td>Macrophages</td><td>Naive, Listeria, Salmonella</td><td>493/168</td></tr>
						<tr><td>Quach_2016</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/27768888">27768888</a></td><td>Monocytes</td><td>Naive, LPS, Pam3CSK4, R848, IAV</td><td>969/200</td></tr>
						<tr><td>Schwartzentruber_2018</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/29229984">29229984</a></td><td>Sensory neurons</td><td></td><td>98/98</td></tr>
						<tr><td>TwinsUK</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/25436857">25436857</a></td><td>Fat, Lymphoblastoid cell lines, skin, blood</td><td></td><td>1364/433</td></tr>
						<tr><td>van_de_Bunt_2015</td><td><a href="https://www.ncbi.nlm.nih.gov/pubmed/26624892">26624892</a></td><td>Pancreatic islets</td><td></td><td>117/117</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</p><br/>
	
	<h4><strong>15. EyeGEx </strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from the GTEx website <a href="https://gtexportal.org/home/datasets">https://gtexportal.org/home/datasets</a>. The file containing the cis-eQTLs can be downloaded from  <a href="https://storage.googleapis.com/gtex_external_datasets/eyegex_data/single_tissue_eqtl_data/Retina.nominal.eQTLs.with_thresholds.tar">https://storage.googleapis.com/gtex_external_datasets/eyegex_data/single_tissue_eqtl_data/Retina.nominal.eQTLs.with_thresholds.tar</a>.
		All eQTLs identified in the data were included.
	</p>
	<p><strong>Description</strong><br/>
		Please refer to the original study for more details (<a href="https://www.nature.com/articles/s41588-019-0351-9">Ratnapriya et al. 2019</a>).
		Ensembl gene ID is used as provided in the original file. FDR adjusted P-values were calculated based on gene-level FDR threshold.
		<br/>
		The signed statistics are betas.
	</p>
	<p><strong>Samples</strong><br/>
		The eQTLs were identified from 406 individuals.
		<br/>
	</p><br/>

</div>
<br/>
<h4><strong>Alignment of risk increasing allele in GWAS and tested allele of eQTLs</strong></h4>
<div style="padding-left: 40px;">
	<h4><strong>Risk increasing allele in GWAS</strong></h4>
	<p>
		When "beta" or "OR" column is provided in the input GWAS file, risk increasing alleles are defined as follows:
		if beta > 0 or OR > 1, effect/risk allele is defined as the risk increasing allele,
		if beta < 0 or OR < 1, non-effect/non-risk allele is defined as the risk increasing allele.<br/>
		If signed effect is not provided in the input GWAS file, risk increasing allele is not defined ("NA").
		SNPs which are not in the input GWAS file but obtained from reference panel due to high LD are also encoded as "NA".
		<span class="info"><i class="fa fa-info"></i>
			When both effect and non-effect alleles are not provided in the input GWAS file, this alignment is not relevant.
			Please be careful to interpret the results.
		</span>
	</p>
	<h4><strong>Aligned direction of eQTLs</strong></h4>
	<p>
		The sign of the t-statistics or z-score of the original eQTL data sources represents the direction of effect of tested allele.
		To obtain the direction of effect for risk increasing allele of GWAS, risk increasing allele and tested allele of eQTLs are aligned as follows:
		if risk increasing allele is the same allele as tested allele of the eQTL, direction is the same as the sign of the original t-statistics/z-score,
		if risk increasing allele is not same allele as tested allele of the eQTL, direction of t-statistics/z-score was flipped.<br/>
		Direction is either "+" (risk increasing allele increases the expression of the gene) or "-" (risk increasing allele decreases the expression of the gene).
	</p>
	<h4><strong>Examples</strong></h4>
	<p>Here are some examples how the alleles are aligned.</p>
	<table class="table table-bordered" style="text-align: center;">
		<thead>
			<th>uniqID</th>
			<th>effect allale</th>
			<th>non-effect allele</th>
			<th>beta</th>
			<th>risk increasing allele</th>
			<th>tested allele of eQTL</th>
			<th>t-statistics of eQTL</th>
			<th>aligned direction</th>
		</thead>
		<tbody>
			<tr>
				<td>1:201885026:C:T</td>
				<td>T</td>
				<td>C</td>
				<td>0.22</td>
				<td>T</td>
				<td>T</td>
				<td>-7.98</td>
				<td>-</td>
			</tr>
			<tr>
				<td>11:43843579:C:G</td>
				<td>C</td>
				<td>G</td>
				<td>0.004</td>
				<td>C</td>
				<td>G</td>
				<td>17.23</td>
				<td>-</td>
			</tr>
			<tr>
				<td>16:28537971:C:T</td>
				<td>T</td>
				<td>C</td>
				<td>-0.028</td>
				<td>C</td>
				<td>C</td>
				<td>5.04</td>
				<td>+</td>
			</tr>
		</tbody>
	</table>
</div>
