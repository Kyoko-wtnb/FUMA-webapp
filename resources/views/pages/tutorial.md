# Tutorial
Please read this tutorial carefully to use PARROT. There are various parameters provided by this pipeline. Those will be explained in detail. To start using right away, follow the quick start, then you will know a minimum knowledge how to use this pipeline.

For detail methods, please refer the publication.

If you have any question or suggestion, please do not hesitate to contact us!!

## Content
- [Quick Start](#quick-start)
- [SNP2GENE](#snp2gene)
  * Prepare Input Files
  * Parameters
  * Submit your job
  * Check your results
  * Get your results and output files
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

#### Input files
|Parameter|Mandatory|Description|Type|Default|
| ------- | ------- | --------- | -- | ----- |
|GWAS summary statistics| Mandatory | Input file of GWAS summary statistics|File upload|none|
|Predefined lead SNPs|Optional| Optionally, user can provide predefined lead SNPs. Please follow the format below.| File upload|none|
|Identify additional lead SNPs|Optional only when predefined lead SNPs are provided|If this option is given, PRROT will identify independent lead SNPs after defined LD block of predefined lead SNPs. Otherwise, only given lead SNPs will be analyzed.| Check | Checked|
|Predefined genetic region| Optional| Optionally, user can provide specific genomic region to perform PARROT. PARROT only look provided regions to identify lead SNPs and candidate SNPs. If you are only interested in specific regions, this will increase a speed of job.|File upload|none|

#### Parameters for lead SNP identification
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



#### General parameters

#### Parameters for gene mapping



## GENE2FUNC

## External Data
