library(data.table)
args <- commandArgs(TRUE)
filedir <- args[1]
gtype <- args[2]
gval <- args[3]
bkgtype <- args[4]
bkgval <- args[5]
MHC <- as.numeric(args[6])

if(gtype == "text"){
  genes <- unlist(strsplit(gval, ":"))
}else{
  genes <- fread(paste(filedir, gval, sep=""), head=F, data.table=F)
  genes <- genes[,1]
}

load(paste(filedir, "../../data/ENSG.all.genes.RData", sep="")) #local
#webserver load("/data/ENSG/ENSG.all.genes.RData")

if(bkgtype == "select"){
  bkg = unlist(strsplit(bkgval, ":"))
  if(bkg[1]=="all"){
    bkgenes = ENSG.all.genes$entrezID
  }else{
    bkgenes = ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$gene_biotype %in% bkg]
  }
}else if(bkgtype == "text"){
  bkgenes = unlist(strsplit(bkgval, ":"))
}else{
  bkgenes = fread(paste(filedir, bkgval, sep=""), head=F, data.table=F)
  blgenes = bkgenes[,1]
}

#if(Xchr==1){
#  ENSG.all.genes <- ENSG.all.genes[ENSG.all.genes$chromosome_name != 23,]
#}

if(MHC==1){
  MHC = FALSE
}else{
  MHC = TRUE
}

type <- 0
## type
# 0-> symbol
# 1-> ensg
# 2-> entrezID

if(length(which(genes %in% ENSG.all.genes$external_gene_name))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$external_gene_name%in%genes]
  type <- 0
}else if(length(which(genes %in% ENSG.all.genes$ensembl_gene_id))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$ensembl_gene_id%in%genes]
  type <- 1
}else if(length(which(g %in% ENSG.all.genes$entrezID))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$entrezID%in%genes]
  type <- 2
}

if(length(which(bkgenes %in% ENSG.all.genes$external_gene_name))>0){
  bkgenes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$external_gene_name%in%bkgenes]
}else if(length(which(genes %in% ENSG.all.genes$ensembl_gene_id))>0){
  bkgenes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$ensembl_gene_id%in%bkgenes]
}else if(length(which(g %in% ENSG.all.genes$entrezID))>0){
  bkgenes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$entrezID%in%bkgenes]
}

#bkg <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$gene_biotype %in% bkg]


load(paste(filedir, "../../data/gtex.avg.log2RPKM.ts.RData", sep="")) #local
#webserver load("/data/GeneExp/GTEx/gtex.avg.log2RPKM.ts.RData")
load(paste(filedir, "../../data/gtex.avg.ts.RData", sep="")) #local
#webserver load("/data/GeneExp/GTEx/gtex.avg.ts.RData")

gtex.exp.log2 <- gtex.avg.log2RPKM.ts[rownames(gtex.avg.log2RPKM.ts) %in% genes, ]
gtex.exp.norm <- gtex.avg.ts[rownames(gtex.avg.ts) %in% genes, ]
rm(gtex.avg.ts, gtex.avg.log2RPKM.ts)
#if(type==0){
#  rownames(gtex.exp) <- paste(rownames(gtex.exp), ENSG.all.genes$external_gene_name[match(rownames(gtex.exp), ENSG.all.genes$ensembl_gene_id)], sep=":")
#}else if(type==2){
#  rownames(gtex.exp) <- paste(rownames(gtex.exp), ENSG.all.genes$entrezID[match(rownames(gtex.exp), ENSG.all.genes$ensembl_gene_id)], sep=":")
#}
rownames(gtex.exp.log2) <- ENSG.all.genes$external_gene_name[match(rownames(gtex.exp.log2), ENSG.all.genes$ensembl_gene_id)]
rownames(gtex.exp.norm) <- ENSG.all.genes$external_gene_name[match(rownames(gtex.exp.norm), ENSG.all.genes$ensembl_gene_id)]

g.sort <- 1:nrow(gtex.exp.log2)
names(g.sort) <- sort(rownames(gtex.exp.log2))
row.order <- data.frame(gene=rownames(gtex.exp.log2), alph=g.sort[rownames(gtex.exp.log2)], clstLog2=NA, clstNorm=NA)

hc <- hclust(dist(gtex.exp.log2))
#gtex.exp <- gtex.exp[rownames(gtex.exp)[hc$order],]
names(g.sort) <- hc$labels[hc$order]
row.order$clstLog2 <- g.sort[rownames(gtex.exp.log2)]
hc <- hclust(dist(gtex.exp.norm))
names(g.sort) <- hc$labels[hc$order]
row.order$clstNorm <- g.sort[rownames(gtex.exp.log2)]
write.table(row.order, paste(filedir, "exp.row.txt", sep=""), quote=F, row.names=F, sep="\t")

ts.sort <- 1:ncol(gtex.exp.log2)
names(ts.sort) <- sort(colnames(gtex.exp.log2))
col.order <- data.frame(ts=colnames(gtex.exp.log2), alph=ts.sort[colnames(gtex.exp.log2)], clstLog2=NA, clstNorm=NA)

hc <- hclust(dist(t(gtex.exp.log2)))
names(ts.sort) <- hc$labels[hc$order]
col.order$clstLog2 <- ts.sort[colnames(gtex.exp.log2)]
hc <- hclust(dist(t(gtex.exp.norm)))
names(ts.sort) <- hc$labels[hc$order]
col.order$clstNorm <- ts.sort[colnames(gtex.exp.log2)]
write.table(col.order, paste(filedir, "exp.col.txt", sep=""), quote=F, row.names=F, sep="\t")

gtex.exp <- melt(gtex.exp.log2)
colnames(gtex.exp) <- c("gene", "ts", "log2")
temp <- melt(gtex.exp.norm)
gtex.exp$norm <- temp$value
write.table(gtex.exp, paste(filedir, "exp.txt", sep=""), quote=F, row.names=F, sep="\t")
rm(hc, gtex.exp, gtex.exp.log2, gtex.exp.norm, temp)

source(paste(filedir, "../../scripts/GeneSet.R", sep="")) #local
#webserver source("/var/www/IPGAP/storage/scripts/GeneSet.R")

DEG <- DEGtest(genes, allgenes=bkgenes, MHC=MHC)
DEG$logP <- -log10(DEG$p)
DEG$logFDR <- -log10(DEG$FDR)
write.table(DEG, paste(filedir, "DEG.txt", sep=""), quote=F, row.names=F, sep="\t")
rm(DEG)
DEGgeneral <- DEGgeneraltest(genes, allgenes=bkgenes, MHC=MHC)
DEGgeneral$logP <- -log10(DEGgeneral$p)
DEGgeneral$logFDR <- -log10(DEGgeneral$FDR)
write.table(DEGgeneral, paste(filedir, "DEGgeneral.txt", sep=""), quote=F, row.names=F, sep="\t")
rm(DEGgeneral)

geneTable <- ENSG.all.genes[ENSG.all.genes$ensembl_gene_id %in% genes,]
geneTable <- subset(geneTable, select=c("ensembl_gene_id", "entrezID", "external_gene_name"))
colnames(geneTable) <- c("ensg", "entrezID", "symbol")
load(paste(filedir, "../../data/entrez2mim.RData", sep="")) #local
#webserver load("/data/genes/entrez2mim.RData")

load(paste(filedir, "../../data/entrez2uniprot.RData", sep="")) #local
#webserver load("/data/genes/entrez2uniprot.RData")
geneTable$OMIM <- entrez2mim$mim[match(geneTable$entrezID, entrez2mim$entrezID)]
geneTable$uniprotID <- entrez2uniprot$uniprotID[match(geneTable$entrezID, entrez2uniprot$entrezID)]
geneTable$DrugBank <- NA
load(paste(filedir, "../../data/DrugBank.RData", sep="")) #local
#webserver load("/data/genes/DrugBank.RData")

geneTable$DrugBank <- DrugBank$DrugBank[match(geneTable$uniprotID, DrugBank$uniprotID)]
write.table(geneTable, paste(filedir, "geneTable.txt", sep=""), quote=F, row.names=F, sep="\t")

#GS <- GeneSetTest(unique(ENSG.all.genes$entrezID[ENSG.all.genes$ensembl_gene_id %in% genes]), allgenes=unique(ENSG.all.genes$entrezID[ENSG.all.genes$ensembl_gene_id %in% bkgenes]), MHC=MHC)
#GS <- entrez2symbol(GS)
#GS$logP <- -log10(GS$p)
#GS$logFDR <- -log10(GS$FDR)
#write.table(GS, paste(filedir, "GS.txt", sep=""), quote=F, row.names=F, sep="\t")
