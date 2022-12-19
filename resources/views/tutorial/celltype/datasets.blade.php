<h3 id="datasets">Data sets</h3>
Each data set available on FUMA is described details.
Scripts for pre-processing are available on github repository at
<a target="_blank" href="https://github.com/Kyoko-wtnb/FUMA_scRNA_data">https://github.com/Kyoko-wtnb/FUMA_scRNA_data</a>.
Processed data can be also downloaded from this repository (so you can run it by yourself!!).
<br/>
Pre-process was performed as the following steps. Please see each script for more details.
<ol>
	<li>
		When the obtained value was the read count,
		the count was converted into the count per million (CPM) to allow
		correction for the total number of reads per cell.
	</li>
	<li>
		QC of cells was performed as described in the original study
		unless the obtained data was already QCed.
	</li>
	<li>
		Cells with uninformative cell type labels
		(e.g. ‘unclassified’ or ‘unknown’) were excluded, unless specified.
	</li>
	<li>
		The expression value (UMI count, CPM, RPKM or TPM) was log2 transformed with pseudo-count 1 (unless the it's already done) and per gene per cell type average was computed.
		When there were multiple levels of cell type labels, the average expression was computed for each level separately.
	</li>
	<li>
		Genes provided in the processed datasets were mapped to human Ensembl gene ID (v92 GRCh37).
	</li>
</ol>
<br/>

<table class="table table-bordered" style="font-size:12px;">
	<thead>
		<th>Data name</th>
		<th>Link</th>
		<th>Description</th>
		<th>Reference</th>
		<th>Last update</th>
	</thead>
	<tbody>
		<tr>
			<td>GSE168408</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				Website: <a target="_blank" href="http://brain.listerlab.org/">http://brain.listerlab.org/</a>,
				Data: <a target="_blank" href="https://console.cloud.google.com/storage/browser/neuro-dev/Processed_data;tab=objects?prefix=&forceOnObjectsSortingFiltering=false">https://console.cloud.google.com/storage/browser/neuro-dev/Processed_data;tab=objects?prefix=&forceOnObjectsSortingFiltering=false</a>
			</td>
			<td>Human Prefrontal Cortex.<br/>
				26 postmortem prefrontal cortex samples spanning 6 stages: Fetal, Neonatal, Infancy, Childhood, Adolescence, and Adult resulting in 154,738 single nuclei.
				3 levels of annotation: level 1 consists of 3 cell types, level 2 consists of 18 cell types, and level 3 consists of 86 cell types. 
				From 26,747 genes, 26,671 genes were mapped to hs ENSG ID.
				In total, 18 data sets were created (6 stages for each of the 3 levels)
			</td>
			<td>Herring et al. 2022. Human prefrontal cortex gene regulatory dynamics from gestation to adulthood at single-cell resolution.
				<i>Cell.</i> <b>185</b>, 4428-4447.<br/>
				<a target="_blank" href="https://pubmed.ncbi.nlm.nih.gov/36318921/">PMID: 36318921</a>
			</td>
			<td>19 December 2022</td>
		</tr>
		<tr>
			<td>Tabula Muris</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				FACS: <a target="_blank" href="https://figshare.com/articles/Single-cell_RNA-seq_data_from_Smart-seq2_sequencing_of_FACS_sorted_cells_v2_/5829687">
					https://figshare.com/articles/Single-cell_RNA-seq_data_from_Smart-seq2_sequencing_of_FACS_sorted_cells_v2_/5829687
				</a>,
				droplet: <a target="_blank" href="https://figshare.com/articles/Single-cell_RNA-seq_data_from_microfluidic_emulsion/5715025">
					https://figshare.com/articles/Single-cell_RNA-seq_data_from_microfluidic_emulsion/5715025
				</a>
			</td>
			<td>Multiple tissues/organs of mouse samples.<br/>
				<b>FACS</b>: From 53,760 cells in the raw read count matrix,
				44,949 cells exist in the annotation file.
				Cells with label "unknown" were included as it was stated in the original study
				that they are potential novel cell types.
				From 23,433 genes, 15,131 genes were mapped to hs ENSG ID.
				In total, 22 data sets were created (1 for cell types from all tissues/organs together,
				20 for each tissue/organ separately and 1 for all Brain cell types
				(including both Brain_Meyloid and Brain_Non-Meyloid)).<br/>
				<b>droplet</b>: From 2,990,808 cells in the raw read count matrix, 54,837 cells exist in the annotation file.
				Cells with label "unknown" were included as it was stated in the original study that they are potential novel cell types.
				From 23,433 genes, 15,131 genes were mapped to hs ENSG ID.
				In total, 13 data sets were created (1 for cell types from all tissues/organs together and 12 for each tissue/organ separately).
			</td>
			<td>The Tabula Muris Consortium et al. 2018.
				Single-cell transcriptomics of 20 mouse organs creates a Tabula Muris.
				<i>Nature.</i> <b>562</b>, 367-372.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/30283141">PMID: 30283141</a>
			</td>
			<td>4 Feb 2019</td>
		</tr>
		<tr>
			<td>Mouse Cell Atlas (GSE108097)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				Website: <a target="_blank" href="http://bis.zju.edu.cn/MCA/">http://bis.zju.edu.cn/MCA/</a>,
				Data: <a target="_blank" href="https://figshare.com/s/865e694ad06d5857db4b">https://figshare.com/s/865e694ad06d5857db4b</a>
			</td>
			<td>Multiple tissues/organs of mouse samples.<br/>
				A file "Figure2-batch-removed.txt.gz" was used in which batch was removed and
				cells were already QCed.
				61,637 cells were available and not additional filtering was performed.
				From 25,133 genes, 15,640 genes were mapped to hs ENSG ID.
				In total 37 data sets were created as the following;
				1) all tissues/developmental stages together (731 unique cell types),
				2) only adult mouse samples (437 cell types from 18 tissue),
				3) only embryo samples (including fetal tissues, 137 cell types),
				4) only neonatal samples (108 cell types),
				5-37) per tissue per sample type (adult, embryo, neonatal and cell line, 33 combination in total).
			</td>
			<td>Han et al. 2018.
				Mapping the Mouse Cell Atlas by Microwell-Seq. <i>Cell.</i> <b>172</b>, 1091-1107.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/29474909">PMID: 29474909</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>Allen Brain Atlas Cell Type</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				Human<br/>
				LGN: <a target="_blank" href="http://celltypes.brain-map.org/api/v2/well_known_file_download/694416667">
					http://celltypes.brain-map.org/api/v2/well_known_file_download/694416667
				</a>,
				MTG: <a target="_blank" href="http://celltypes.brain-map.org/api/v2/well_known_file_download/694416044">
					http://celltypes.brain-map.org/api/v2/well_known_file_download/694416044
				</a>
				<br/>
				Mouse new version<br/>
				ALM2: <a target="_blank" href="http://celltypes.brain-map.org/api/v2/well_known_file_download/694413179">
					http://celltypes.brain-map.org/api/v2/well_known_file_download/694413179
				</a>,
				LGd2: <a target="_blank" href="http://download.alleninstitute.org/informatics-archive/current-release/rna_seq/mouse_LGd_gene_expression_matrices_2018-06-14.zip">
					http://download.alleninstitute.org/informatics-archive/current-release/rna_seq/mouse_LGd_gene_expression_matrices_2018-06-14.zip
				</a>,
				VISp2: <a target="_blank" href="http://celltypes.brain-map.org/api/v2/well_known_file_download/694413985">
					http://celltypes.brain-map.org/api/v2/well_known_file_download/694413985
				</a>
				<br/>
				Mouse old version<br/>
				ALM: <a target="_blank" href="https://portals.broadinstitute.org/single_cell/study/a-transcriptomic-taxonomy-of-adult-mouse-visual-cortex-visp">
					https://portals.broadinstitute.org/single_cell/study/a-transcriptomic-taxonomy-of-adult-mouse-visual-cortex-visp
				</a>,
				LGp: <a target="_blank" href="https://portals.broadinstitute.org/single_cell#study-a-transcriptomic-taxonomy-of-adult-mouse-anterior-lateral-motor-cortex-alm">
					https://portals.broadinstitute.org/single_cell#study-a-transcriptomic-taxonomy-of-adult-mouse-anterior-lateral-motor-cortex-alm
				</a>,
				VISp: <a target="_blank" href="https://portals.broadinstitute.org/single_cell#study-a-transcriptomic-taxonomy-of-adult-mouse-lateral-geniculate-complex-lgd">
					https://portals.broadinstitute.org/single_cell#study-a-transcriptomic-taxonomy-of-adult-mouse-lateral-geniculate-complex-lgd
				</a>
			</td>
			<td>Human and mouse brain samples.<br/>
				For each data, level 1, 2 and 3 cell types were processed separately.<br/>
				For Human MTG, LGN and mouse ALM2, LGd2 and VISp2, sum of read counts for exon and introns
				were computed for each gene, to obtain gene level read counts.<br/>
				<b>Human MTG</b>: From 15,928 cells, 325 with "no class" were excluded, resulted in 15,603 cells.
				From 50,281 genes, 29,115 genes were mapped to unique ENSG ID.<br/>
				<b>Human LGN</b>: From 1,576 cells, 23 with "no class" were excluded, resulted in 1,553 cells.
				From 50,281 genes, 29,115 genes were mapped to unique ENSG ID.<br/>
				<b>Mouse ALM2</b>: From 10,068 cells, 99 which "Low Quality" and 396 cells with "no class" were excluded,
				resulted in 9,573 cells.
				From 45,768 genes, 16,093 genes were mapped to unique hs ENSG ID.<br/>
				<b>Mouse LGd2</b>: From 1,996 cells, 122 with "Outlier" were excluded, resulted in 1,874 cells.
				From 45,768 genes, 16,093 genes were mapped to unique hs ENSG ID.<br/>
				<b>Mouse VISp2</b>: From 15,413 cells, 490 with "Low Quality" and 674 cells with "no class" were excluded,
				resulted in 14,249 cells.
				From 45,768 genes, 16,093 genes were mapped to unique hs ENSG ID.<br/>
				<b>Mouse ALM</b>: All 1,301 cells were used.
				From 45,764 genes, 16,068 genes were mapped to unique hs ENSG ID.<br/>
				<b>Mouse LGd</b>: From 1,827 cells, 17 cels with label "Outlier" were excluded, resulted in 1,810 cells.
				From 45,761 genes, 15,837 genes were mapped to unique hs ENSG ID.<br/>
				<b>Mouse VISp</b>: All 1,679 cells were used.
				From 24,057 genes, 15,097 genes were mapped to unique hs ENSG ID.
			</td>
			<td>(For Human MTG)<br/>
				Hodge, et al. 2018. Conserved cell types with divergent features between human and mouse cortex.
				<i>bioRxiv.</i>
				<a target="_blank" href="https://www.biorxiv.org/content/10.1101/384826v1">doi: https://doi.org/10.1101/384826</a>
				(For Mouse VISp2 and ALM2)<br/>
				Tasic et al. 2018. Shared and distinct transcriptomic cell types across neocortical areas.
				<i>Nature</i> <b>563</b>, 72-78.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/30382198">PMID: 30382198</a>
				(For Mouse VISp data set)<br/>
				Tasic et al. 2016. Adult mouse cortical cell taxonomy revealed by single cell transcriptomics.
				<i>Nat. Neurosci.</i> <b>19</b>, 335-346.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/26727548">PMID: 26727548</a>
			</td>
			<td>4 Feb 2019</td>
		</tr>
		<tr>
			<td>DropViz</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="http://dropviz.org/">http://dropviz.org/</a>
			</td>
			<td>Mouse brain samples.<br/>
				"Metacells" data downloaded from DropViz website was used
				which is the aggregated data per 565 sub-cluster not the individual cell level UMI counts.
				The UMI was the sum of all the cells in a subcluster, therefore we converted to CPM.
				In the annotation of each sub-cluster, "class" column was used as level 1 cell type and subcluster was used as level 2.
				From 32,307 genes, 16,097 genes were mapped to hs ENSG ID.
			</td>
			<td>Arpair et al. 2018. Molecular diversity and specializations among the cells of adult mouse brain.
				<i>Cell.</i> <b>9</b>, 1015-1030.
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/30096299">PMID: 30096299</a>
			</td>
			<td>4 Feb 2019</td>
		</tr>
		<tr>
			<td>DroNc</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				Human: <a target="_blank" href="https://www.gtexportal.org/">https://www.gtexportal.org/</a> /
				<a target="_blank" href="https://portals.broadinstitute.org/single_cell#study-dronc-seq-single-nucleus-rna-seq-on-human-archived-brain">https://portals.broadinstitute.org/single_cell#study-dronc-seq-single-nucleus-rna-seq-on-human-archived-brain</a>,
				Mouse: <a target="_blank" href="https://portals.broadinstitute.org/single_cell#study-dronc-seq-single-nucleus-rna-seq-on-mouse-archived-brain">https://portals.broadinstitute.org/single_cell#study-dronc-seq-single-nucleus-rna-seq-on-mouse-archived-brain</a>
			</td>
			<td>Human and mouse brain samples.<br/>
				<b>Human</b>: Expression data was downloaded from GTEx website (also available from Broadinstitute Single Cell Portal).
				Cells with cluster 1-14 or 16 were used since those clusters were assigned in the original study.
				The cell type label was manually assigned to the cluster index based on the figure 2a in the original paper.
				From 14,963 cells, 14,137 cells were used. From 32,111 genes, 31,852 genes were mapped to ENSG ID.<br/>
				<b>Mouse</b>: Cells with label "Unclassified", "Doublets" or "ChP" were excluded as they are not assigned in the original study.
				From 13,313 cells, 11,148 cells were used. From 17,3080 genes, 13,335 genes were mapped to hs ENSG ID.
			</td>
			<td>Habib et al. 2017. Massively parallel single-nucleus RNA-seq with DroNc-seq.
				<i>Nat. Methods.</i> <b>14</b>, 955-958.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/28846088">PMID: 28846088</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>Mouse Brain Atlas (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="http://mousebrain.org/">http://mousebrain.org/</a>
			</td>
			<td>Mouse brain samples.<br/>
				Five expression matrices were obtained for level 5, level 6 rank 1-4.
				Note that the expression value was already aggregated per cell type and we did not use individual cell level expression data.
				Each of 5 data sets were processed separately.
				From 27,997 genes, 16,420 genes were mapped to hs ENSG ID.
			</td>
			<td>Zeisel et al. 2018. Molecular architecture of the mouse nervous system.
				<i>Cell.</i> <b>9</b> 999-1014.
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/30096314">PMID: 30096314</a>
			</td>
			<td>4 Feb 2019</td>
		</tr>
		<tr>
			<td>GSE59739 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE59739">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE59739</a>
			</td>
			<td>Mouse brain samples (dorsal root ganglion L4-L6 from 6-8 weeks old mice).<br/>
				Expression data was obtained from GEO and annotation of each cell was extracted from family soft file.
				Cells with label NF, NP, PEP or TH in Level 1 cell types were used to be consistent with the original study.
				From 865 cells in the expression data, 622 cells were used.
				From 25,333 genes, 15,084 genes were mapped to hs ENSG ID.
				Per cell type average expression was computed for level 1, 2 and 3 separately.
			</td>
			<td>Usoskin et al. 2015. Unbiased classification of sensory neuron types by large-scale single-cell RNS sequencing.
				<i>Nat. Neurosci.</i> <b>18</b>, 145-153.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/25420068">PMID: 25420068</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE60361 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://storage.googleapis.com/linnarsson-lab-www-blobs/blobs/cortex">https://storage.googleapis.com/linnarsson-lab-www-blobs/blobs/cortex</a>
			</td>
			<td>Mouse brain samples (cortex and hippocampus from P22-P32 mice).<br/>
				3,005 cells were available.
				From 19,972 genes, 15,161 genes were mapped to hs ENSG ID.
				Per cell type average expression was computed for level 1 and level 2 separately.
				For level 2, 189 cells with label "none" were excluded.
			</td>
			<td>Zeisel et al. 2015. Brain structure. Cell types in the mouse cortex and hippocampus revealed by single-cell RNA-seq.
				<i>Science.</i> <b>347</b>, 1138-1142.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/25700174">PMID: 25700174</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE75330 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE75330">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE75330</a>
			</td>
			<td>Mouse brain samples (oligodendrocytes from day21-90 mice).<br/>
				5,069 cells were available. From 23,556 genes, 15,816 genes were mapped to hs ENSG.
			</td>
			<td>Marques et al. 2016.
				Oligodendrocyte heterogeneity in the mouse juvenile and adult central nervous system.
				<i>Science.</i> <b>352</b>, 1326-1329.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27284195">PMID: 27784195</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE78845 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE78845">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE78845</a>
			</td>
			<td>Mouse brain samples (stellate and thoracic sympathetic ganglia from postnatal day 27-33 mice).<br/>
				Cells with label "unclassified" were excluded.
				From 298 cells, 213 cells were used.
				From 16,892 genes, 13,804 genes were mapped to hs ENSG ID.
			</td>
			<td>Furlan et al. 2016. Visceral motor neuron diversity delineates a cellular basis for nipple-and plio-erection muscle control.
				<i>Nat. Neurosci.</i> <b>19</b>, 1331-1340.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27571008">https://www.ncbi.nlm.nih.gov/pubmed/27571008</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE76381 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE76381">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE76381</a>
			</td>
			<td> Human brain samples (ventral midbrain from 6-11 weeks embryos) and mouse brain samples (ventral midbrain from E11.5-E18.5 embryos).<br/>
				Only human embryo (1,977 cells) and mouse embryo (1,907 cells) data set were used.
				Cells with label "Unk" (unknown) were excluded.
				For human, from 1,977 cells, 1695 cell were used. From 19,531 genes, 16,885 genes were mapped to ENSG ID.
				For mouse, from 1,907 cells, 1,518 cells were used. From 24,378 genes, 15,826 genes were mapped to hs ENSG ID.
			</td>
			<td>La Manno et al. 2016. Molecular diversity of midbrain development in mouse, human, and stem cells.
				<i>Cell.</i> <b>167</b>, 556-580.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27716510">PMID: 27716510</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE95752, GSE95315 and GSE104323 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				GSE95752: <a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE95752">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE95752</a>,
				GSE95315: <a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE95315">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE95315</a>,
				GSE104323: <a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE104323">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE104323</a>
			</td>
			<td>Mouse brain samples (dentate gyrus from P5-P26 and P50-P65 for GSE95752, P12-P35 for GSE95315 and E16.5 and P0-P132 for GSE104323).<br/>
				<b>GSE95752</b>: 2,303 cells were available. From 16,131 genes, 143,470 genes were mapped to hs ENSG ID.<br/>
				<b>GSE95315</b>: Cell types obtained from family soft file (41 types) are merged into 22 cluster as presented in the original study.
				5,454 cells were available. From 14,545 genes, 12,640 genes were mapped to hs ENSG ID.<br/>
				<b>GSE104323</b>: From 24,216 cells, 24,185 cells with valid cell labels were used (cells with blank in the cell type column were excluded).
				From 27,933 genes, 16,146 genes were mapped to hs ENSG ID.
			</td>
			<td>Hochgerner et al. 2018. Conserved properties of dentate gyrus neurogenesis across postnatal development revealed by single-cell RNA sequencing.
				<i>Nat. Neurosci.</i> <b>21</b>, 290-299.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/29335606">PMID: 29335606</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE101601 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE101601">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE101601</a>
			</td>
			<td>Human brain samples (Temporal cortex from post-mortem samples) and mouse brain samples (Somatosensory cortex from postnatal days 21-37 mice).<br/>
				Human (2,028 cells) and mouse (2,192 cells) data sets were processed separately.
				For human, from 28,274 genes, 21,459 genes were mapped to ENSG ID.
				For mouse, from 24,339 genes, 15,826 genes were mapped to hs ENSG ID.
			</td>
			<td>Hochgerner et al. 2017. STRT-seq-2i: dual-index 5' single cell and nucleus RNA-seq on an addressable microwell array.
				<i>Sci. Rep.</i> <b>7</b>: 16327.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/29180631">PMID: 29180631</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE74672 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE74672">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE74672</a>
			</td>
			<td>Mouse brain samples (hypothalamus from postnatal days 14-28 mice).<br/>
				Only 2881 cells were available in the expression file,
				though it was mentioned that 3131 cells in the original paper.
				From 24,341 genes, 15,826 genes were mapped to hs ENSG ID.
				Per cell type average expression was computed for level 1 and 2 separately.
				Level 2 label was only available for neurons.
				From 898 neurons, 126 cells with level 2 label "uc" (unclassified) were excluded.
			</td>
			<td>Romanov et al. 2017. Molecular interrogation of hypothalamic organization reveals distinct dopamine neuronal subtypes.
				<i>Nat. Neurosci.</i> <b>20</b>, 176-188.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27991900">PMID: 27991900</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE67602 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE67602">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE67602</a>
			</td>
			<td>Mouse epidermis from dorsal skin (~8 weeks).<br/>
				1,422 cells were available. From 25,932 genes, 15,802 genes were mapped to hs ENSG.
			</td>
			<td>Joost et al. 2016. Single-cell transcriptomics reveals that differentiation and spatial signatures shape epidermal and hair follicle heterogeneity.
				<i>Cell Syst.</i> <b>3</b>, 221-237.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27641957">PMID: 27641957</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE103840 (Linnarsson's lab)</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE103840">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE103840</a>
			</td>
			<td>Mouse brain samples (dorsal horn from 3-4 weeks old mice).<br/>
				1,545 cells were available. From 24,378 genes, 15,826 genes were mapped to hs ENSG ID.
			</td>
			<td>Haring et al. 2018. Neuronal atlas of the dorsal horn defines its architecture and links sensory input to transcriptional cell types.
				<i>Nat. Neurosci.</i> <b>21</b>, 869-880.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/29686262">PMID: 29686262</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE87544</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE87544">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE87544</a>
			</td>
			<td>Mouse brain samples (hypothalamus from 8-10 weeks l=old mice).<br/>
				From 14,437 cells, 6,507 cells with condition "Normal" were extracted.
				Cells with label "zothers" were further excluded resulted in 5,350 cells.
				To be consistent with the original study, cells with <=2000 genes expressed (0 expression) were excluded.
				In total, 1,039 cells were used.
				From 23,284 genes, 15,116 genes were mapped to hs ENSG ID.
				In the original study, there are 45 cell types but
				in the downloadable data there was no NFO but instead IMO and SCO.
				By checking with the authors, IMO (immature oligodendrocyte) = NFO and SCO (Subcommissural organ) is extra.
			</td>
			<td>Chen et al. 2017. Single-cell RNA-seq reveals hypothalamic cell diversity.
				<i>Cell Rep.</i> <b>18</b>, 3227-3241.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/28355573">PMID: 28355573</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE98816 and GSE92235</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				GSE98816: <a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE98816">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE98816</a>,
				GSE92235: <a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE92235">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE92235</a>
			</td>
			<td>Mouse brain vascular cells (GSE98816) and lung vascular cells (GSE92235) from 10-19 weeks old mice.<br/>
				Cell type label was obtained directly from the authors by requesting.<br/>
				<b>GSE98816</b>: 3,186 cells were available. From 19,937 genes, 15,302 genes were mapped to hs ENSG ID.<br/>
				<b>GSE92235</b>: 1,504 cells were available. From 21,948 genes, 15,801 genes were mapped to hs ENSG ID.
			</td>
			<td>Vanlandewijck et al. 2018. A molecular atlas of cell types and zonation in the brain vasculature.
				<i>Nature.</i> <b>554</b>, 475-480.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/29443965">PMID: 29443965</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE81547</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE81547">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE81547</a>
			</td>
			<td>Human pancreas samples (healthy donors between 1 month to 54 years old).<br/>
				2,544 cells were available.
				Cells with label "unsure" was defined as "PP" in the original study.
				From 23,465 genes, 20,706 genes were mapped to hs ENSG ID.
			</td>
			<td>Enge et al. 2017. Single-cell analysis of human pancreas reveals transcriptional signatures of ageing and somatic mutation patterns.
				<i>Cell.</i> <b>171</b>, 321-330.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/28965763">PMID: 28965763</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE104276</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE104276">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE104276</a>
			</td>
			<td>Human brain samples (prefrontal cortex from 8-26 weeks after gestation).<br/>
				2,309 cells were available. From 24,153 genes, 21,177 genes were mapped to ENSG ID.
				Two data sets were created;
				1) per cell type average across different ages,
				2) per cell type per age average expression.
			</td>
			<td>Zhong et al. 2018. A single-cell RNA-seq survey of the developmental landscape of the human prefrontal cortex.
				<i>Nature.</i> <b>555</b>, 524-528.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/29539641">PMID: 29539641</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE82187</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE82187">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE82187</a>
			</td>
			<td>Mouse brain samples (striatum from 5-7 weeks old mice).<br/>
				Only microfluid data was used since FACS data was limited to neurons.
				From 1,208 cells, 705 cells from microfluid were used.
				From 18,840 genes, 14,189 genes were mapped to hs ENSG ID.
			</td>
			<td>Gokce et al. 2016. Cellular taxonomy of the mouse striatum as revealed by single-cell RNA-seq.
				<i>Cell Repo.</i> <b>16</b>, 1126-1137.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27425622">PMID: 27425622</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE89232</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE89232">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE89232</a>
			</td>
			<td>Human blood samples.<br/>
				957 cells were available.
				From, 20.689 genes, 17,035 genes were mapped to hs ENSG ID.
			</td>
			<td>Breton et al. 2016. Human dendritic cells (DCs) are derived from distinct circulating precursors that are precommitted to become CD1c+ or CD141+ DCs.
				<i>J. Exp. Med.</i> <b>213</b>, 2861-2870.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27864467">PMID: 27864467</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE100597</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE100597">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE100597</a>
			</td>
			<td>Mouse embryos (E3.5, E4.5, E5.5 and E6.5).<br/>
				Developmental stage was used as cell label.
				721 cells were available. From 24,83 genes, 14,513 genes were mapped to hs ENSG ID.
			</td>
			<td>Mohammed et al. 20174. Single-cell landscape of transcriptional heterogeneity and cell fate decisions during mouse early gastrulation.
				<i> Cell Repo.</i> <b>20</b>, 1215-1228.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/28768204">PMID: 28768204</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE93374</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE93374">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE93374</a>
			</td>
			<td>Mouse brain samples (hypothalamic arcuate-median eminence complex from 4-12 weeks old mice).<br/>
				Cells with label "miss" in the column "clust_all" were excluded.
				From 21,086 cells, 20,921 cells were used.
				Level 1, level 2 and clusters for neurons were processed separately resulted in three data sets.
				For clusters for neurons, non-neuronal cells were excluded (with label "miss" in "clust_neurons" column; 13,079 neuronal cells in total).
				From 19,743 genes, 14,366 genes were mapped to hs ENSG ID.
			</td>
			<td>Campbell et al. 2017. A molecular census of arcuate hypothalamus and median eminence cell types.
				<i>Nat. Neurosci.</i> <b>20</b>, 484-496.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/28166221">PMID: 28166221</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE92332</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE92332">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE92332</a>
			</td>
			<td>Mouse small intestine epithelium samples (7-10 weeks old mice).<br/>
				Expression data was obtained for SMATRseq (1,522 cells) and droplet (7,216 cells) data set.
				Each data set was processed separately.
				For SMARTseq, from 20,108 genes, 14,714 genes were mapped to hs ENSG ID.
				For droplet, from 15,971 genes, 12,865 genes were mapped to hs ENSG ID.
			</td>
			<td>Haber et al. 2017. A single-cell survey of the small intestinal epithelium.
				<i>Nature.</i> <b>551</b>, 333-339.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/29144463">PMID: 29144463</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE89164</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE89164">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE89164</a>
			</td>
			<td>Mouse brain samples (hindbrain from P0 mice).<br/>
				Two count matrices for mouse replicates were combined and extracted 4366 cells exist in the cluster information.
				Cell label was manually assigned to the cluster index based on the original study.
				From 20,648 genes, 13,176 genes were mapped to hs ENSG ID.
			</td>
			<td>Alies et al. 2017. Cell fixation and preservation for droplet-based single-cell transcriptomics.
				<i>BMC Biol.</i> <b>15</b>: 44.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/28526029">PMID: 28526029</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE67835</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE67835">https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE67835</a>
			</td>
			<td>Human brain samples (cortex from adult and fetal samples).<br/>
				Two data sets with and without fetal sample were created.
				466 cells were available (of which 135 cells were fetal samples).
				From 22,088 genes, 19,749 genes were mapped to ENSG ID.
			</td>
			<td>Darmanis et al. 2015. A survey of human brain transcriptome diversity at the single cell level.
				<i>Proc. Natl. Acad. Sci. USA.</i> <b>112</b>, 7285-90.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/26060301">PMID: 26060301</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE106678</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://portals.broadinstitute.org/single_cell/study/snucdrop-seq-dissecting-cell-type-composition-and-activity-dependent-transcriptional-state-in-mammalian-brains-by-massively-parallel-single-nucleus-rna-seq">https://portals.broadinstitute.org/single_cell/study/snucdrop-seq-dissecting-cell-type-composition-and-activity-dependent-transcriptional-state-in-mammalian-brains-by-massively-parallel-single-nucleus-rna-seq</a>
			</td>
			<td>Mouse brain samples (cortex from 6-10 weeks old mice).<br/>
				Expression data was obtained from Broadinstitute Single Cell Portal.
				18,194 cells were available.
				From 30,341 genes, 15,782 genes were mapped to hs ENSG ID.
			</td>
			<td>Hu et al. 2017. Dissecting cell-type composition and activity-dependent transcriptional state in mammalian brains by massively parallel single-nucleus RNA-seq.
				<i>Mol. Cell.</i> <b>68</b>, 1006-1015.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/29220646">PMID: 29220646</a>
			</td>
			<td>17 July 2018</td>
		</tr>
		<tr>
			<td>GSE84133</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE84133">
					https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE84133
				</a>
			</td>
			<td>
				Human and mouse pancreas samples.<br/>
				<strong>Humam</strong>: Human sample 4 was excluded as it is with sample status T2D.
				All 7,266 cell were used and from 20,125 genes 19,546 genes were mapped to unique ENSG ID.
				<br/>
				<strong>Mouse</strong>: All 1,886 cells were and from 14,878 genes, 12,741 genes were mapped to unique hs ENSG ID.
			</td>
			<td>Baron, et al. 2016. A single-cell transcriptomic map of the human and mouse pancreas reveals inter- and intra-cell population structure.
				<i>Cell Systems</i> <b>3</b>, 346-360.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/27667365">PMID: 27667365</a>
			</td>
			<td>4 Feb 2019</td>
		</tr>
		<tr>
			<td>10x PBMC</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://community.10xgenomics.com/t5/Data-Sharing/10x-Single-Cell-3-Paper-Zheng-et-al-2016-Datasets/td-p/231">
					https://community.10xgenomics.com/t5/Data-Sharing/10x-Single-Cell-3-Paper-Zheng-et-al-2016-Datasets/td-p/231
				</a>
			</td>
			<td>
				Human peripheral blood mononuclear cells (PBMCs).<br/>
				Cell label was downloaded from github repository https://github.com/10XGenomics/single-cell-3prime-paper.
				Data set was downloaded from 10X website directory.
				All 68,579 cells were used. Genes were annotated to ENSG ID in
				the original data (32,738 genes).
			</td>
			<td>Zheng. et al. 2017. Massively parallel digital transcriptional profiling of single cells.
				<i>Nat. Communs.</i> <b>8</b>, 14049.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/28091601">PMID: 28091601</a>
			</td>
			<td>4 Feb 2019</td>
		</tr>
		<tr>
			<td>PsychENCODE</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="http://resource.psychencode.org/">
					http://resource.psychencode.org/
				</a>
			</td>
			<td>
				Human developmental and adult brain samples.<br/>
				For developmental dataset, 4,249 cells were available.
				For adult dataset, from 27,412 cells, 32 cells with cell type label NA were excluded,
				resulted in 27,380 cells.
				From 15,086 and 17,176 genes, 15,019 and 16,243 genes were mapped to unique ENSG ID for developmental and adult datasets, respectively.
			</td>
			<td>Wang. et al. 2018. Comprehensive functional genomic resource and integrative model for the human brain.
				<i>Science.</i> <b>362</b>, eaat8464.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/30545857">PMID: 30545857</a>
			</td>
			<td>19 May 2019</td>
		</tr>
		<tr>
			<td>GSE97478, GSE106707</td>
			<td style="word-wrap:break-word;word-break:break-all;">
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE97478">
					https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE97478
				</a><br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE106707">
					https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=GSE106707
				</a>
			</td>
			<td>
				Mouse striatum and cortex samples.<br/>
				<strong>GSE97478</strong>: 1,122 cells were available.
				From 12,936 genes, 11,299 genes were mapped to unique hs ENSG ID.<br/>
				<strong>GSE106707</strong>: 3,417 cells were available.
				From 10,002 genes, 8,940 genes were mapped to unique hs ENSG ID.
			</td>
			<td>Muoz-Manchado. et al. 2018. Diversity of interneurons in the dorsal striatum revealed by single-cell RNA sequencing and PatchSeq.
				<i>Cell Rep.</i> <b>24</b>, 2179-2190.e7.<br/>
				<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/30134177">PMID: 30134177</a>
			</td>
			<td>19 May 2019</td>
		</tr>
	</tbody>
</table>
