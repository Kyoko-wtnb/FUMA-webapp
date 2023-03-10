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
eqtlplot <- as.numeric(args[6])
ciplot <- as.numeric(args[7])
ensg_v <- args[8]


curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

ENSG <- fread(paste(config$data$ENSG, ensg_v, config$data$ENSGfile, sep="/"), data.table=F)
ENSG$chromosome_name[ENSG$chromosome_name=="X"] <- 23

ENSG <- ENSG[ENSG$chromosome_name==chr,]
g <- ENSG$ensembl_gene_id[(ENSG$start_position <= xmin & ENSG$end_position>=xmax)
  | (ENSG$start_position>=xmin & ENSG$start_position<=xmax)
  | (ENSG$end_position>=xmin & ENSG$end_position<=xmax)
  | (ENSG$start_position >= xmin & ENSG$end_position<=xmax)
]

if(length(g)==0){
  start <- min(abs(ENSG$end_position-xmin))
  end <- min(abs(ENSG$start_position-xmax))
  g <- ENSG$ensembl_gene_id[which(abs(ENSG$end_position-xmin)==start)]
  g <- c(g, ENSG$ensembl_gene_id[which(abs(ENSG$start_position-xmax)==start)])
}

g <- unique(c(g, ENSG$ensembl_gene_id[ENSG$external_gene_name %in% eqtlgenes]))

gmin <- min(ENSG$start_position[ENSG$ensembl_gene_id %in% g])
gmax <- max(ENSG$end_position[ENSG$ensembl_gene_id %in% g])

g <- unique(c(g, ENSG$ensembl_gene_id[
  (ENSG$start_position>=gmin & ENSG$start_position<=gmax)
  | (ENSG$end_position>=gmin & ENSG$end_position<=gmax)
]))

rm(ENSG)
library(biomaRt)
#ensembl <- useMart(biomart = "ENSEMBL_MART_ENSEMBL", host="grch37.ensembl.org", path="/biomart/martservice", dataset="hsapiens_gene_ensembl")
ensembl <- useEnsembl(biomart="ensembl", dataset="hsapiens_gene_ensembl", GRCh=37, version=sub("v", "", ensg_v))
exons <- getBM(attributes = c("ensembl_gene_id", "external_gene_name", "start_position", "end_position", "strand", "gene_biotype", "exon_chrom_start", "exon_chrom_end"), filter="ensembl_gene_id", values=g, mart = ensembl, useCache=FALSE)
genes <- unique(exons[,1:6])
genes <- genes[order(genes$start_position),]

mappedGenes <- fread(paste(filedir, "genes.txt", sep=""), data.table=F)
if(eqtlplot==0 & ciplot==0){
	if("posMapSNPs" %in% colnames(mappedGenes)){
		mappedGenes <- mappedGenes[mappedGenes$posMapSNPs>0,]
	}
}else if(eqtlplot==1 & ciplot==0){
	if("posMapSNPs" %in% colnames(mappedGenes)){
		mappedGenes <- mappedGenes[mappedGenes$posMapSNPs>0 | mappedGenes$eqtlMapSNPs>0,]
	}else{
		mappedGenes <- mappedGenes[mappedGenes$eqtlMapSNPs>0,]
	}
}else if(eqtlplot==0 & ciplot==1){
	if("posMapSNPs" %in% colnames(mappedGenes)){
		mappedGenes <- mappedGenes[mappedGenes$posMapSNPs>0 | mappedGenes$ciMap=="Yes",]
	}else{
		mappedGenes <- mappedGenes[mappedGenes$ciMap=="Yes",]
	}
}
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
