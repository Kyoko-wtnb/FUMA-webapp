<h3 id="eQTLs">eQTLs</h3>
FUMA contains several data sources of eQTL information, and can include more sources when available.
Each data source is described in more detail in this section.
<h4><strong>eQTL data sources</strong></h4>
<div style="padding-left: 40px;">
	<h4><strong>1. GTEx v6</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://www.gtexportal.org/home/datasets">http://www.gtexportal.org/home/datasets</a>.
		Under the section of GTEx V6, from single tissue eQTL data both <span style="color: blue;">GTEx_analysis_V6_eQTLs.tar.gz</span>
		for significant SNP-gene association based on permutation, and
		<span style="color: blue;">GTEx_Analysis_V6_all-snp-gene-associations.tar</span> for every SNP-gene association test (including non-significant paris)
		were downloaded.<br/>
		GTEx eQTL v6 contains 44 different tissue types across 23 general tissue types.
	</p>
	<p><strong>Description</strong><br/>
		FUMA contains all SNP-gene pairs of cis-eQTL including non-significant association.
		Significant eQTLs are defined as FDR &le; 0.05.
		The gene FDR is pre-calculated by GTEx and every gene-tissue pair has a defined P-value threshold for eQTLs based on permutation.
	</p>
	<p><strong>Samples</strong><br/>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="#gtexTable" data-toggle="collapse">GTEx eQTL tissue types and sample size</a><br/>
			</div>
			<div id="gtexTable" class="panel-body collapse">
				<span class="info"><i class="fa fa-info"></i> The table contains the list of tissue types available in GTEx v6 for cis-eQTL (only tissues with genotyped sample size &ge; 70).</span>
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
						<tr><td>Brain</td><td>Brain Spinal cord cervical c-1</td><td>59</td></tr>
						<tr><td>Brain</td><td>Brain Substantia nigra</td><td>56</td></tr>
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
						<tr><td>Salivary Gland</td><td>Minor Salivary Gland</td><td>51</td></tr>
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
	</p>

	<h4><strong>2. Blood eQTL browser (Westra et al. 2013)</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL data was downloaded from <a href="http://genenetwork.nl/bloodeqtlbrowser/">http://genenetwork.nl/bloodeqtlbrowser/</a>.
	</p>
	<p><strong>Description</strong><br/>
		The data only include eQTLs with FDR &le; 0.5.
		Genes in the original files were mapped to Ensembl ID in which genes are removed if they are not mapped to Ensembl ID.
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
		The data only include eQTLs with FDR &le; 0.05.
	</p>
	<p><strong>Samples</strong><br/>
		2,116 whole peripheral blood samples of healthy adults from 4 Durch cohorts (<a href="https://www.ncbi.nlm.nih.gov/pubmed/27918533">Zhernakova et al. 2017</a>).
	</p><br/>

	<h4><strong>4. BRAINEAC</strong></h4>
	<p><strong>Data source</strong><br/>
		eQTL was obtained from <a target="_blank" href="http://www.braineac.org/">http://www.braineac.org/</a>.<br/>
	</p>
	<p><strong>Description</strong><br/>
		The data include all eQTLs with nominal P-value < 0.05.
		Since tested allele was not provided in the original data source, minor alleles in 1000 genome phase 3 are asigned as tested alleles.
		eQTLs were identified for each of the following 10 brain regions and based on averaged expression across them.<br/>
		<span class="info"><i class="fa fa-info"></i>
			Since tested allele was not provided in the original data source, assigned "NA" in the result table.
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
	</p>
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
