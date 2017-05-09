library(data.table)
library(kimisc)
library(rjson)
args <- commandArgs(TRUE)
filedir <- args[1]
chr <- args[2]
xmin <- as.numeric(args[3])-500000
xmax <- as.numeric(args[4])+500000
eqtlgenes <- args[5]
eqtlgenes = unlist(strsplit(eqtlgenes, ":"))


curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

load(paste(config$data$ENSG, "/ENSG.all.genes.RData", sep=""))

ENSG.all.genes <- ENSG.all.genes[ENSG.all.genes$chromosome_name==chr,]
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

g <- unique(c(g, eqtlgenes[eqtlgenes %in% ENSG.all.genes$external_gene_name]))

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

mappedGenes <- fread(paste(filedir, "genes.txt", sep=""), data.table=F)
mappedGenes <- mappedGenes$symbol[mappedGenes$ensg %in% genes$ensembl_gene_id]

#write.table(exons, paste(filedir, "exons.txt", sep=""), quote=F, row.names=F, sep="\t")
#write.table(genes, paste(filedir, "genesplot.txt", sep=""), quote=F, row.names=F, sep="\t")

colnames(genes) <- NULL
colnames(exons) <- NULL
genes <- unname(split(genes, 1:nrow(genes)))
exons <- unname(split(exons, 1:nrow(exons)))

out <- list(genes, exons, mappedGenes)
names(out) <- c("genes", "exons", "mappedGenes")
cat(toJSON(out))
