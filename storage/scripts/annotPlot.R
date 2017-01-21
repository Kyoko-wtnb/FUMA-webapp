library(data.table)
library(kimisc)
args <- commandArgs(TRUE)
filedir <- args[1]
type <- args[2]
rowI <- as.numeric(args[3])
rowI <- rowI+1
GWAS <- as.numeric(args[4])
CADD <- as.numeric(args[5])
RDB <- as.numeric(args[6])
eqtlplot <- as.numeric(args[7])
Chr15 <- as.numeric(args[8])
Chr15ts <- args[9]

curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

snps <- fread(paste(filedir, "annotPlot.txt", sep=""), data.table=F)
if(eqtlplot==1){
  eqtl <- fread(paste(filedir, "eqtlplot.txt", sep=""), data.table=F)
}

load(paste(config$data$ENSG, "/ENSG.all.genes.RData", sep=""))

xmin <- min(snps$pos)-500000
xmax <- max(snps$pos)+500000
ENSG.all.genes <- ENSG.all.genes[ENSG.all.genes$chromosome_name==snps$chr[1],]
g <- ENSG.all.genes$ensembl_gene_id[(ENSG.all.genes$start_position <= xmin & ENSG.all.genes$end_position>=xmax)
  | (ENSG.all.genes$start_position>=xmin & ENSG.all.genes$start_position<=xmax)
  | (ENSG.all.genes$end_position>=xmin & ENSG.all.genes$end_position<=xmax)
  | (ENSG.all.genes$start_position >= xmin & ENSG.all.genes$end_position<=xmax)
]

if(length(g)==0){
  start <- min(abs(ENSG.all.genes$end_position-xmin))
  end <- min(abs(ENSG.all.genes$start_position-xmax))
  g <- ENSG.all.genes$ensembl_gene_id[which(abs(ENSG.all.genes$end_position-xmin)==start)]
  g <- c(g, ENSG.all.genes$ensembl_gene_id[which(abs(ENSG.all.genes$start_position-xmax)==start)])
}

if(eqtlplot==1){
  g <- unique(c(g, eqtl$gene))
}

gmin <- min(ENSG.all.genes$start_position[ENSG.all.genes$ensembl_gene_id %in% g])
gmax <- max(ENSG.all.genes$end_position[ENSG.all.genes$ensembl_gene_id %in% g])

g <- unique(c(g, ENSG.all.genes$ensembl_gene_id[
  (ENSG.all.genes$start_position>=gmin & ENSG.all.genes$start_position<=gmax)
  | (ENSG.all.genes$end_position>=gmin & ENSG.all.genes$end_position<=gmax)
]))

rm(ENSG.all.genes)
library(biomaRt)
ensembl <- useMart(biomart = "ENSEMBL_MART_ENSEMBL", host="grch37.ensembl.org", path="/biomart/martservice", dataset="hsapiens_gene_ensembl")
exons <- getBM(attributes = c("ensembl_gene_id", "external_gene_name", "start_position", "end_position", "strand", "gene_biotype", "exon_chrom_start", "exon_chrom_end"), filter="ensembl_gene_id", values=g, mart = ensembl)
genes <- unique(exons[,1:6])
genes <- genes[order(genes$start_position),]

write.table(exons, paste(filedir, "exons.txt", sep=""), quote=F, row.names=F, sep="\t")
write.table(genes, paste(filedir, "genesplot.txt", sep=""), quote=F, row.names=F, sep="\t")
