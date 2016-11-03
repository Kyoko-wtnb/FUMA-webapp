library(data.table)
args <- commandArgs(TRUE)
filedir <- args[1]
g <- args[2]
g <- unlist(strsplit(g, ":"))
bkg <- args[3]
bkg <- unlist(strsplit(bkg, ":"))
type <- 0
load(paste(filedir, "../../data/ENSG.all.genes.RData", sep=""))
load(paste(filedir, "../../data/gtex.avg.ts.RData", sep=""))

## type
# 0-> symbol
# 1-> ensg
# 2-> entrezID

if(length(which(g %in% ENSG.all.genes$external_gene_name))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$external_gene_name%in%g]
  type <- 0
}else if(length(which(g %in% ENSG.all.genes$ensembl_gene_id))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$ensembl_gene_id%in%g]
  type <- 1
}else if(length(which(g %in% ENSG.all.genes$entrezID))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$entrezID%in%g]
  type <- 2
}

bkg <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$gene_biotype %in% bkg]

gtex.exp <- gtex.avg.ts[rownames(gtex.avg.ts) %in% genes, ]
if(type==0){
  rownames(gtex.exp) <- paste(rownames(gtex.exp), ENSG.all.genes$external_gene_name[match(rownames(gtex.exp), ENSG.all.genes$ensembl_gene_id)], sep=":")
}else if(type==2){
  rownames(gtex.exp) <- paste(rownames(gtex.exp), ENSG.all.genes$entrezID[match(rownames(gtex.exp), ENSG.all.genes$ensembl_gene_id)], sep=":")
}

hc <- hclust(dist(gtex.exp), method="average")
gtex.exp <- gtex.exp[rownames(gtex.exp)[hc$order],]

gtex.exp <- melt(gtex.exp)
colnames(gtex.exp) <- c("gene", "tissue", "exp")
write.table(gtex.exp, paste(filedir, "exp.txt", sep=""), quote=F, row.names=F, sep="\t")

rm(hc, gtex.exp)

source(paste(filedir, "../../data/GeneSet.R", sep=""))

DEG <- DEGtest(genes, allgenes=bkg, MHC=FALSE)
DEG$logP <- -log10(DEG$p)
DEG$logFDR <- -log10(DEG$FDR)
write.table(DEG, paste(filedir, "DEG.txt", sep=""), quote=F, row.names=F, sep="\t")
rm(DEG)
DEGgeneral <- DEGgeneraltest(genes, allgenes=bkg, MHC=FALSE)
DEGgeneral$logP <- -log10(DEGgeneral$p)
DEGgeneral$logFDR <- -log10(DEGgeneral$FDR)
write.table(DEGgeneral, paste(filedir, "DEGgeneral.txt", sep=""), quote=F, row.names=F, sep="\t")
rm(DEGgeneral)
GS <- GeneSetTest(ENSG.all.genes$entrezID[ENSG.all.genes$ensembl_gene_id %in% genes], allgenes=ENSG.all.genes$entrezID[ENSG.all.genes$ensembl_gene_id %in% bkg])
GS <- entrez2symbol(GS)
GS$logP <- -log10(GS$p)
GS$logFDR <- -log10(GS$FDR)
write.table(GS, paste(filedir, "GS.txt", sep=""), quote=F, row.names=F, sep="\t")
