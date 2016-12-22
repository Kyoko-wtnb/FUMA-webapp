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

if(Chr15==1){
  Chr15ts <- unlist(strsplit(Chr15ts, ":"))
  Chr15ts <- sort(Chr15ts)
}

curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

snps <- fread(paste(filedir, "snps.txt", sep=""), data.table=F)
loci.table <- fread(paste(filedir, "leadSNPs.txt", sep=""), data.table=F)
ld <- fread(paste(filedir, "ld.txt", sep=""), data.table=F)
if(type=="leadSNP"){
  i <- loci.table$interval[rowI]
  snps <- snps[snps$Interval==i,]
  ld <- ld[ld$SNP2 %in% snps$uniqID,]
  snps$ld <- 0
  snps$ld[snps$uniqID %in% ld$SNP2[ld$SNP1==loci.table$uniqID[rowI]]] <- 1
  snps$ld[snps$uniqID == loci.table$uniqID[rowI]] <- 2
}else if(type=="interval"){
  snps <- snps[snps$Interval==rowI,]
  ld <- ld[ld$SNP2 %in% snps$uniqID,]
  snps$ld <- 1
  snps$ld[snps$uniqID %in% ld$SNP1] <- 2
}
rm(ld, loci.table)
snps$logP <- -log10(snps$gwasP)
snps$logP[is.na(snps$logP)] <- 0

snps <- subset(snps, select=c("uniqID", "chr", "pos", "rsID", "leadSNP", "gwasP", "logP", "r2", "ld", "CADD", "RDB", "nearestGene", "func"))

if(Chr15==1){
  annot <- fread(paste(filedir, "annot.txt", sep=""), data.table=F)
  annot <- annot[annot$uniqID %in% snps$uniqID, ]
  if(Chr15ts[1]=="all"){Chr15ts<-colnames(annot)[4:ncol(annot)]}
  for(i in 1:length(Chr15ts)){
    n <- which(colnames(annot)==Chr15ts[i])
    cat(Chr15ts[i], "\t", n, "\n")
    snps <- cbind(snps, annot[match(snps$uniqID, annot$uniqID),n])
    colnames(snps)[ncol(snps)] <- Chr15ts[i]
  }
}

if(eqtlplot==1){
  eqtl <- fread(paste(filedir, "eqtl.txt", sep=""), data.table=F);
  eqtl <- eqtl[eqtl$uniqID %in% snps$uniqID,]
  eqtl$logP <- -log10(eqtl$p)
  snps$eqtl <- NA
  for(i in 1:nrow(snps)){
    if(snps$uniqID[i] %in% eqtl$uniqID){
      snps$eqtl[i] <- paste(apply(eqtl[eqtl$uniqID==snps$uniqID[i],c(2,3,11,6,8)], 1, paste, collapse=":"), collapse="<br/>")
    }
  }
  eqtl$ld <- snps$ld[match(eqtl$uniqID, snps$uniqID)]
  eqtl <- subset(eqtl, select=c("gene", "symbol", "tissue", "pos", "logP", "ld"))
  write.table(eqtl, paste(filedir, "eqtlplot.txt", sep=""), quote=F, row.names=F, sep="\t")
}
write.table(snps, paste(filedir, "annotPlot.txt", sep=""), quote=F, row.names=F, sep="\t")

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

write.table(length(g), paste(filedir, "test.txt", sep=""))
rm(ENSG.all.genes)
library(biomaRt)
ensembl <- useMart(biomart = "ENSEMBL_MART_ENSEMBL", host="grch37.ensembl.org", path="/biomart/martservice", dataset="hsapiens_gene_ensembl")
exons <- getBM(attributes = c("ensembl_gene_id", "external_gene_name", "start_position", "end_position", "strand", "gene_biotype", "exon_chrom_start", "exon_chrom_end"), filter="ensembl_gene_id", values=g, mart = ensembl)
genes <- unique(exons[,1:6])
genes <- genes[order(genes$start_position),]

write.table(exons, paste(filedir, "exons.txt", sep=""), quote=F, row.names=F, sep="\t")
write.table(genes, paste(filedir, "genesplot.txt", sep=""), quote=F, row.names=F, sep="\t")
