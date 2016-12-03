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
load(paste(filedir, "../../data/gtex.avg.ts.RData", sep="")) #local
#webserver load("/data/GeneExp/GTEx/gtex.avg.ts.RData")

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

if(Xchr==1){
  ENSG.all.genes <- ENSG.all.genes[ENSG.all.genes$chromosome_name != 23,]
}

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
#GS <- GeneSetTest(unique(ENSG.all.genes$entrezID[ENSG.all.genes$ensembl_gene_id %in% genes]), allgenes=unique(ENSG.all.genes$entrezID[ENSG.all.genes$ensembl_gene_id %in% bkgenes]), MHC=MHC)
#GS <- entrez2symbol(GS)
#GS$logP <- -log10(GS$p)
#GS$logFDR <- -log10(GS$FDR)
#write.table(GS, paste(filedir, "GS.txt", sep=""), quote=F, row.names=F, sep="\t")
