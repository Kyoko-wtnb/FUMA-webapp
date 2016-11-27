# Tutorial
Please read this tutorial carefully to use PARROT. There are various parameters provided by this pipeline. Those will be explained in detail. To start using right away, follow the quick start, then you will know a minimum knowledge how to use this pipeline.

For detail methods, please refer the publication.

If you have any question or suggestion, please do not hesitate to contact us!!

## Content
- [Quick Start](#quick-start)
- [SNP2GENE](#snp2gene)
  * Prepare Input Files
  * Parameters
  * Submit a new job
  * Outputs
- [GENE2FUNC](#gene2func)
  * Prepare Input
  * Output
- [External databases in the pipeline](#external-data)

## Quick Start
### Get candidates from your own GWAS summary statistics
### Test shared functions of list of genes

## SNP2GENE
>### Prepare Input Files

>GWAS summary statistics is a mandatory input of SNP2GENE process. PARROT accept various types of format. As default, ```PLINK``` for mat is selected, but please choose the format of your input file since this will cause error during process. Each option requires the following format.

>The input file must include P-value and either rsID or chromosome index and genetic position on hg19 reference genome. Alleles are not mandatory but if only one allele is provided, that is considered as affected allele. When two alleles are provided, it will depends on header. If alleles are not provided, they will be extracted from dbSNP build 146 as minor allele as affected alleles.

>If you are not sure which format to use, either edit your header or select ```Plain Text``` which will cover most of common column names.

>Delimiter can be any of white space including single space, multiple space and tab. Because of this, column name must not include any space.

>The column of chromosome can be string like "chr1" or just integer "1". When "chr" is attached, this will be removed from outputs. When the input file contains chromosome X, this will be encoded as chromosome 23, however, input file can be leave as "X".

>>#### 1. ```PLINK``` format
&ensp;As the most common file format, ```PLINK``` is the default option. Some options in PLINK do not return both A1 and A2 but as long as the file contains either SNP or CHR and BP, PARROT will cover missing values.
  * **SNP**: rsID
  * **CHR**: chromosome
  * **BP**: genomic position (hg19)
  * **A1**: affected allele
  * **A2**: another allele
  * **P**: P-value (Mandatory)

>>#### 2. ```SNPTEST``` format
&ensp;Since in the output file of SNPTEST contains lines start with '#', those lines will be skipped. Herder line should not start with '#' and should be the first line without '#' in the file.
  * **rsid**: rsID
  * **chromosome**: chromosome
  * **position**: genomic position (hg19)
  * **alleleB**: affected allele
  * **alleleA**: another alleleA
  * **frequentist_add_pvalue**: P-value

>>#### 3. ```CTGA``` format
  * **SNP**: rsID
  * **Chr**: chromosome
  * **bp**: genomic position (hg19)
  * **OtherAllele**: affected allele
  * **ReferenceAllele**: another alleleA
  * **p**: P-value

>>#### 4. ```METAL``` format
&ensp;The output of METAL (for meta analyses) only contains rsID without chromosome and genomic position. Therefore, those information will be extracted from dbSNP build 146 using rsID. For this, rsID will be first updated to build 146.
  * **MakerName**: rsID
  * **Allele1**: affected allele
  * **Allele2**: another alleleA
  * **P-value**: P-value

>>#### 5. ```Plain Text``` format
&ensp;If your file does not fit in any of above option, please use ```Plain Text``` option. The following headers are *case insensitive*.
  * **SNP|markername|rsID**: rsID
  * **CHR|chromosome|chrom**: chromosome
  * **BP|pos|position**: genomic position (hg19)
  * **A1|alt|effect_allele|allele1**: affected allele
  * **A2|ref|non_effect_allele|allele2**: another allele
  * **P|pvalue|p-value|p_value**: P-value (Mandatory)

>>-------------------------------
#### Note and Tips
The pipeline only support human genome hg19. If your input file is not in hg19, please update the genomic position using liftOver from UCSC. However, there is an option for you!! When you provide only rsID without chromosome index and genomic position, PARROT will extract them from dbSNP as hg19 genome. To do this, remove columns of chromosome index and genomic position.

>>-------------------------------

>### Parameters
PARROT provide a variety of parameters. Default setting will perform naive positional mapping which gives you all genes within LD blocks of lead SNPs. In this section, every parameter will be described details.

>>#### Input files
|Parameter|Mandatory|Description|Type|Default|
| ------- | ------- | --------- | -- | ----- |
|GWAS summary statistics| Mandatory | Input file of GWAS summary statistics|File upload|none|
|Predefined lead SNPs|Optional| Optionally, user can provide predefined lead SNPs. Please follow the format below.| File upload|none|
|Identify additional lead SNPs|Optional only when predefined lead SNPs are provided|If this option is given, PRROT will identify independent lead SNPs after defined LD block of predefined lead SNPs. Otherwise, only given lead SNPs will be analyzed.| Check | Checked|
|Predefined genetic region| Optional| Optionally, user can provide specific genomic region to perform PARROT. PARROT only look provided regions to identify lead SNPs and candidate SNPs. If you are only interested in specific regions, this will increase a speed of job.|File upload|none|

>>#### Parameters for lead SNP identification
|Parameter|Mandatory|Description|Type|Default|Direction|
| ------- | ------- | --------- | -- | ----- |
|Sample size (N)|Mandatory|The total number of sample in the GWAS. This is only used for MAGMA and LD score regression.|Integer|none|Doesn't affect any candidates|
|Maximum lead SNP P-value (<=)|Mandatory|PARROT identifies lead SNPs wiht P-value less than or equal to this threshold. This should not me changed unless GWAS is under-powered and only a few peaks are significant. |numeric|5e-8|lower: decrease #lead SNPs. higher: increase #lead SNPs which most likely increate noises|
|Minimum r2 (>=)|Mandatory|The minimum correlation to be in LD of a lead SNP.|numeric|0.6|higher: decrease #candidate SNPs and increase #lead SNPs. lower: increase #candidate SNPs and decrease #lead SNPs|
|Maximum GWAS P-value (<=)|Mandatory|This is the threshold for candidate SNPs within the LD block of a lead SNP. This will be applied only for GWAS-tagged SNPs.|numeric|0.05|higher: decrease #candidate SNPs. lower: increase #candidate SNPs.|
|Population|Mandatory|The population of reference panel to compute r2 and MAF. Five populations are available from 1000G Phase 3.|Select|EUR|-|
|Include 1000 genome variants| Mandatory| If checked, PARROT include all SNPs in strong LD with any of lead SNPs even for non-GWAS-tagged SNPs.|Yes/No|Yes|-|
|Minimum MAF (>=)|Mandatory|The minimum Minor Allele Frequency of candidate SNPs. This filter also apply to lead SNPs. If there is any pre-defined lead SNPs with MAF less than this threshold, that will be skipped.|numeric|0.01|higher: decrease #candidate SNPs. lower: increase #candidate SNPs|
|Maximum merge distance of LD (<=)|Mandatory|This is the maximum distance between LD blocks from independent lead SNPs to merge into genomic interval. When it is set at 0, only physically overlapped LD blocks are merged into genomic interval. Definition of interval is independent from definition of candidate SNPs.|numeric|250kb|-|
|Include X chromosome|Optional|It does not affect anything unless you have X chromosome in your input file. If so, please check this option to include X chromosome. Note that, not all annotation database include X chromosomes, e.g. GTEx eQTLs do not have X chromosome. |Check|Checked|-|

>#### MHC region
>>MHC region is often excluded due to the complicated LD structure. Therefore, this option is checked by default. Please uncheck to include MHC region. It doesn't change any results if there is no significant hit in the MHC region.

>>Default region is defined as between "MOG" and "COL11A2" genes. To define user own MHC region, please provide in the text box. The input format should be like "25000000-34000000".

>#### Parameters for gene mapping
There are two options for gene mapping; positional and eQTL mappings. By default, positional mapping with maximum distance 10kb is defined. Since this parameter setting largely reflect to the result of mapped genes, please set carefully.

>>##### Positional mapping
|Parameter|Mandatory|Description|Type|Default|Direction|
| ------- | ------- | --------- | -- | ----- |
|Position mapping|Optional|Whether perform positional mapping or not.|Check|Checked|-|
|Gene window|Optional|Map SNPs to gene based on physical distance|Check|Checked|-|
|Gene window size (<=)|Optional|The maximum distance to map SNPs to genes|numeric|10kb|-|
|Annotations based mapping|Optional|Map SNPs to genes baed on positional mapping such as exonic, intronic splicing, etc...|Check|Unchecked|-|
|Annotations|Mandatory only when Annotation based mapping is activated|Positional annotation to map SNPs to genes|Multiple selection|none|-|

>>##### eQTL mapping
|Parameter|Mandatory|Description|Type|Default|Direction|
| ------- | ------- | --------- | -- | ----- |
|eQTL mapping|Optional|Whether perform eQTL mapping or not|Check|Unchecked|-|
|Tissue types|Mandatory if eQTL mapping is activated|All available tissue types with Data sources are shown in the select box. ```Tissue type``` selection contain individual tissue types and ```General tissue types``` contain broad area of organ and each general tissue contains multiple individual tissue types.|Multiple selection|none|-|
|Significant eQTL only (FDR<=0.05)|Optional|To map only significant eQTL at FDR 0.05|Check|Checked|-|
|eQTL maximum P-value (<=)|Mandatory if Significant eQTL only is unchecked|This option will show up on the screen only when ```Significant eQTL only``` is unchecked. This can be used as threshold of eQTL uncorrected P-value.|numeric|1e-3|-|

>>##### Functional annotation filtering
Both positional and eQTL mappings have same options for the filtering of SNPs based on functional annotation, but parameters have to be set for each mapping separately.

>>|Parameter|Mandatory|Description|Type|Default|Direction|
| ------- | ------- | --------- | -- | ----- |
|CADD score|Optional|Whether perform filtering of SNPs by CADD score or not.|Check|Unchecked|-|
|Minimum CADD score (>=)|Mandatory if ```CADD score``` is checked|The higher CADD score, the more deleterious.|numeric|12.37|-|
|RegulomeDB score|Optional|Whether perform filtering of SNPs by RegulomeDB score or not.|Check|Unchecked|-|
|Minimum RegulomeDB score (>=)|Mandatory if ```RegulomeDB score``` is checked|RegulomeDB score is a categorical (from 1a to 7). Please refer link for details. 1a is the most likely affect regulation. Note that not all SNPs in 1000G Phase3 has this score. Those SNPs are recorded as NA. Those SNPs will be filtered out when RegulomeDB score filtering is performed.|string|7|-|
|15-core chromatin state|Optional|Whether perform filtering of SNPs by chromatin state or not.|Check|Unchecked|-|
|15-core chromatin state tissue/cell types|Mandatory if ```15-core chromatin state``` is checked|Multiple tissue/cell types can be selected from either list of individual types or general types. |Multiple selection|none|-|
|Maximum state of chromatin(<=)|Mandatory if ```15-core chromatin state``` is checked|The maximum state to filter SNPs. Between 1 and 15. Generally, above 7 is open state. Please refer link for further details.|numeric|7|-|
|Method for 15-core chromatin state filtering|Mandatory if ```15-core chromatin state``` is checked|When multiple tissue/cell types are selected, either ```any``` (a SNP has state above than threshold in any of selected tissue/cell types), ```majority``` (a SNP has state above than threshold in majority (>=50%) of selected tissue/cell type), or ```all``` (a SNP has state above than threshold in all of selected tissue/cell type).|Selection|any|-|

>### Submit a New Job
The submission page will show parameter option step by step. For example, you can only see the option for uploading files after providing email address and job title.
Each option will guide you with messages like the followings.

><div class="alert alert-info">
This is information for you.
</div>
<div class="alert alert-success">
This is the message if everything is fine.
</div>
<div class="alert alert-danger">
This is the message if there is any error or wrong input.
</div>
<div class="alert alert-warning">
This is the warning message. You are allowed to go further but please pay attention.
</div>


>>##### 1. Enter email address and job title
Email address and job title are mandatory to submit a new job. The combination of these two will create unique ID to store results. Email address will be only used to inform the completion of your job. If warning message is shown, the job with the same title already exists but you can overwrite.

>>##### 2. Select input files
GWAS summary statistics file is mandatory. Please don't forget to select correct file format. Pre-defined lead SNPs and/or genetic regions can be provided here, too.

>>##### 3. Set parameters
Please refer "Parameters" section for details.

>>##### 4. Submit!!!
Unless there is any error or wrong input, the submit button is enabled. If the submit button is still disabled, please check if there is any error message.
You will receive two emails, one is to inform that job has been submitted and second one is to inform you job has been done.
Don't worry about bookmarking the link, you will be able to query your results with email address and job title.
Usually, the job takes 20 min to 1 hour depending on parameters.
If you provide pre-defined genomic region or significant signals in the input GWAS is a few, it more likely to finish job quickly.
In that case, you can stay in the submitted page, and as soon as job is done, page will be updated to your results.

>### Outputs
Go to SNP2GENE and in the "Query existing job" panel, enter your email address and job title. If both are correct, "Go to Job" button is enabled.

>There are 5 panels in the result page.

>#### 1. Information of your job
This panel contains your email address, job title and the date of job submission.

>#### 2. Result tables
This panel contain multiple tables of your results.
>>  - Summary
    * Summary of SNPs and mapped genes
    * Positional annotation of candidate SNPs
    * Summary per interval


>>  - lead SNPs

>>  All independent lead SNPs identified by ANNOTATOR.
    * **No** : Index of lead SNPs
    * **Interval** : Index of assigned genomic interval. This matches with the index of interval table.
    * **uniqID** : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.
    * **rsID** : rsID based on dbSNP build 146.
    * **chr** : chromosome
    * **pos** : position on hg19
    * **P-value** : P-value (from the input file).
    * **nSNPs** : The number of SNPs within LD of the lead SNP given r2, including non-GWAS-tagged SNPs (which are extracted from 1000G).
    * **nGWASSNPs** : The number of GWAS-tagged SNPs within LD of the lead SNP given r2. This is a subset of "nSNPs".


>>  - Intervals

>>  Genomic intervals defined from independent lead SNPs.
  Each interval is represented by the top lead SNP which has the minimum P-value in the interval.
    * **Interval** : Index of genomic interval.
    * **uniqID** : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.
    * **rsID** : rsID of the top lead SNP based on dbSNP build 146.
    * **chr** : chromosome of top lead SNP
    * **pos** : position of top lead SNP on hg19
    * **P-value** : P-value of top lead SNP (from the input file).
    * **nLeadSNPs** : The number of lead SNPs merged into the interval.
    * **start** : Start position of the interval.
    * **start** : End postion of the interval.
    * **nSNPs** : The number of canidate SNPs in the interval, including non-GWAS-tagged SNPs (which are extracted from 1000G).
    * **nGWASSNPs** : The number of GWAS-tagged candidate SNPs within the interval. This is a subset of "nSNPs".


>>  - SNPs (annotation)

>>    All candidate SNPs with annotations. Note that depending on your mapping criterion, not all candidate SNPs are mapped to genes.
      * **uniqID** : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.
      * **rsID** : rsID based on dbSNP build 146.
      * **chr** : chromosome
      * **pos** : position on hg19
      * **P-value** : P-value (from the input file).


>>  - ANNOVAR

>>    Since one SNP can be annotated multiple positional information, the table of ANNOVAR output is separated from SNPs table. This table contain unique SNP-annotation combination.
    * **uniqID** : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.
    * **chr** : chromosome
    * **pos** : position on hg19
    * **Gene** : ENSG ID
    * **Symbol** : Gene Symbol
    * **Distance** : Distance to the gene
    * **Function** : Positional annotation
    * **Exonic function** : Functional annotation of exonic SNPs
    * **Exon** : Index of exon


>>  - Genes

>>    The summary of mapped genes based on your defined mapping criterion.
    Columns change for positional and eQTL mappings.
    When both mappings are performed, all columns exit in the table.
      * **Gene** : ENSG ID
      * **Symbol** : Gene Symbol
      * **entrezID** : entrez ID
      * **Interval** : Index of interval where mapped SNPs are from. This could contain more than one interval in the case that eQTLs are mapped to genes from distinct genomic intervals.
      * **chr** : chromosome
      * **start** : gene starting position
      * **end** : gene ending position
      * **strand** : strand od gene
      * **status** : status of gene from Ensembl
      * **type** : gene biotype from Ensembl
      * **HUGO** : HUGO gene symbol
      * **posMapSNPs** (posMap): The number of SNPs mapped to gene based on positional mapping (after functional filtering if parameters are given).
      * **posMapMaxCADD** (posMap): The maximum CADD score of mapped SNPs by positional mapping.
      * **eqtlMapSNPs** (eqtlMap): The number of SNPs mapped to the gene based on eQTL mapping.
      * **eqtlMapminP** : The minimum eQTL P-value of mapped SNPs.
      * **eqtlMapmin!** : The minimum eQTL FDR of mapped SNPs.
      * **eqtlMapts** : Tissue types of mapped eQTL SNPs.
      * **eqtlDirection** : consecutive direction of mapped eQTL SNPs.
      * **minGwasP** : The minimum P-value of mapped SNPs.
      * **leadSNPs** : All independent lead SNPs of mapped SNPs.


>>  - eQTL

>>    This table is only shown when you performed eQTL mapping.
    The table contain unique pair of SNP-gene-tissue, therefore, the same SNP could appear in the table multiple times.
      * **uniqID** : Unique ID of SNPs consists of chr:position:allele1:allele2 where alleles are alphabetically ordered.
      * **chr** : chromosome
      * **pos** : position on hg19
      * **DB** : Data source of eQTLs. Currently GTEx, BloodeQTL and BIOS are available. Please refer "External Data sources" for details.
      * **tissue** : tissue type
      * **Gene** : ENSG ID
      * **Symbol** : Gene symbol
      * **P-value** : P-value of eQTLs
      * **FDR** : FDR of eQTLs. Note that method to compute FDR differs between data sources. Please refer "External Data sources" for details.
      * **t/z** : T-statistics or z score depends on data source.


>>  - GWAScatalog

>>    List of SNPs reported in GWAScatalog which are candidate SNPs of your GWAS summary statistics. The table does not contain all recode from GWAScatalog. To get full information, please download from "Downloads" tab.
      * **Interval** : Index of interval.
      * **lead SNP** : The lad SNP of the SNP in GWAScatalog.
      * **chr** : chromosome
      * **bp** : position on hg19
      * **rsID** : rsID
      * **PMID** : PubMed ID
      * **Trait** : The trait reported in GWAScatalog
      * **FirthAuth** : First author reported in GWAScatalog
      * **Date** : Date added in GWAScatalog
      * **P-value** : Reported P-value


>>  - Parameters

>>    The table of input parameters.
      * **Job created** : Date of job created
      * **Job title** : Job title
      * **input GWAS summary statistics file** : File name of GWAS summary statistics
      * **input lead SNPs file** : File name of pre-defined lead SNPs if provided.
      * **Identify additional lead SNPs** : 1 if option is checked, 0 otherwise. If pre-defined lead SNPs are not provided, it is always 1.
      * **input genetic regions file** : File name of pre-defined genetic regions if provided.
      * **sample size** : Sample size of GWAS
      * **exclude MHC** : 1 to exclude MHC region, 0 otherwise
      * **extended MHC region** : user defined MHC region if provided, NA otherwise
      * **exclude chromosome X** : 1 to exclude X chromosome, 0 otherwise
      * **gene type** : All selected gene type.
      * **lead SNP P-value** : the maximum threshold of P-value to be lead SNP
      * **r2** : the minimum threshold for SNPs to ne in LD of the lead SNPs
      * **GWAS tagged SNPs P-value** : the maximum threshold of P-value to be candidate SNP
      * **MAF** : the minimum minor allele frequency based on 1000 genome reference of given population
      * **Include 1000G SNPs** : 1 to include non-GWAS-tagged SNPs from reference panel, 0 otherwise
      * **Interval merge max distance** : The maximum distance between LD blocks to merge into interval
      * **Positional mapping** : 1 to perform positional mapping, 0 otherwise
      * **posMap Window based** : 1 to perform positional mapping based on distance to the genes, 0 otherwise
      * **posMap Window size** : If window based positional mapping is performed, which distance (kb) as the maximum. If window based mapping is 0, this parameter set at 10 as default but will be ignored.
      * **posMap Annotation based** : Positional annotations selected if window based mapping is 0.
      * **posMap min CADD** : The minimum CADD score for SNP filtering
      * **posMap min RegulomeDB** : The minimum RegulomeDB score for SNP filtering
      * **posMap chromatin state filterinf tissues** : Select tissue/cell types, NA otherwise
      * **posMap max chromatin state** : The maximum 15-core chromatin state
      * **posMap chromatin state filtering method** : The method of chromatin state filtering
      * **eQTL mapping** : 1 to perform eQTL mapping, 0 otherwise
      * **eqtlMap tissues** : Selected tissue typed for eQTL mapping
      * **eqtlMap min CADD** : The minimum CADD score for SNP filtering
      * **eqtlMap min RegulomeDB** : The minimum RegulomeDB score for SNP filtering
      * **eqtlMap chromatin state filterinf tissues** : Select tissue/cell types, NA otherwise
      * **eqtlMap max chromatin state** : The maximum 15-core chromatin state
      * **eqtlMap chromatin state filtering method** : The method of chromatin state filtering


>>  - Doenloads

>>    To download multiple tables at the same time, go to "Downloads" tab and select files you want to download.


>#### 3. Regional plot
When you click any lead SNP or interval, regional plot will be shown in this panel. To plot with genes and other annotations, please go to "Regional plot with annotation" panel.

>#### 4. Query results
This is still under construction. Will be available soon.

>#### 5. Regional plot with annotation
This panel contains options to create regional plot with annotations.
The plot will be created in a new tab.

## GENE2FUNC
>### Submit genes
>>#### Use mapped genes from SNP2GENE
If you want to use mapped genes from SNP2GENE, just click a button in the result table panel of result page.
It will open a new tab and automatically start analyses.
This will take all mapped genes and use background genes with gene types you selected (such as "protein-coding" or "ncRNA").
Parameters for excluding chromosome X and excluding MHC region also used to filter background genes.
>>#### Use a list of genes of interest
To analyse your genes, you have to prepare list of genes as either ENSG ID, entrez ID or gene symbol.
Genes can be provided in the text are (one gene per line) or uploading file in the left panel. When you upload a file, genes have to be in the first column with header. Header can be anything (even just a new line is fine) but start your genes from second row.

>>To analyse your genes, you need to specify background genes. You can choose from the gene types which is the easiest way. However, in the case that you need to use specific background genes, please provide them either in the text area of by uploading a file of the right panel.
File format should be same as described for genes on interest.

### Check your results
Once analysis is done, the three panel will be appear in the same page.
#### Gene Expression Heatmap

#### Tissue specificity

#### Molecular functions

## External Data sources and tools
### 1. 1000 genomes project

### 2. PLINK

### 3. ANNOVAR

### 4. CADD

### 5. RegulomeDB

### 6. 15-core chromatin state

### 7. GTEx

### 8. MsigDB

### 9. WikiPathways

### 10. GWAScatalog

### 11. OMIM

### 12. DrugBank
