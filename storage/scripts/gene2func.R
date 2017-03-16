library(data.table)
library(kimisc)
args <- commandArgs(TRUE)
filedir <- args[1]

curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

params <- ConfigParser(file=paste(filedir,'params.config', sep=""))
gtype <- params$params$gtype
gval <- params$params$gval
bkgtype <- params$params$bkgtype
bkgval <- params$params$bkgval
MHC <- as.numeric(params$params$MHC)

if(gtype == "text"){
  genes <- unlist(strsplit(gval, ":"))
}else{
  genes <- fread(paste(filedir, gval, sep=""), head=F, data.table=F)
  genes <- genes[,1]
}

load(paste(config$data$ENSG, "/ENSG.all.genes.RData", sep=""))

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

if(length(which(toupper(genes) %in% toupper(ENSG.all.genes$external_gene_name)))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[toupper(ENSG.all.genes$external_gene_name) %in% toupper(genes)]
  type <- 0
}else if(length(which(toupper(genes) %in% toupper(ENSG.all.genes$ensembl_gene_id)))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[toupper(ENSG.all.genes$ensembl_gene_id) %in% toupper(genes)]
  type <- 1
}else if(length(which(genes %in% ENSG.all.genes$entrezID))>0){
  genes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$entrezID%in%genes]
  type <- 2
}else{
  stop("gene ID did not match")
}


if(length(which(toupper(bkgenes) %in% toupper(ENSG.all.genes$external_gene_name)))>0){
  bkgenes <- ENSG.all.genes$ensembl_gene_id[toupper(ENSG.all.genes$external_gene_name)%in%toupper(bkgenes)]
}else if(length(which(toupper(bkgenes) %in% toupper(ENSG.all.genes$ensembl_gene_id)))>0){
  bkgenes <- ENSG.all.genes$ensembl_gene_id[toupper(ENSG.all.genes$ensembl_gene_id) %in% toupper(bkgenes)]
}else if(length(which(bkgenes %in% ENSG.all.genes$entrezID))>0){
  bkgenes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$entrezID%in%bkgenes]
}

load(paste(config$data$GTExExp, "/gtex.avg.log2RPKM.ts.RData", sep=""))
load(paste(config$data$GTExExp, "/gtex.avg.ts.RData", sep=""))

if(length(which(rownames(gtex.avg.ts) %in% genes))>1){
  gtex.exp.log2 <- gtex.avg.log2RPKM.ts[rownames(gtex.avg.log2RPKM.ts) %in% genes, ]
  gtex.exp.norm <- gtex.avg.ts[rownames(gtex.avg.ts) %in% genes, ]
  rm(gtex.avg.ts, gtex.avg.log2RPKM.ts)
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
}else if(length(which(rownames(gtex.avg.ts) %in% genes))==1){
  gtex.exp.log2 <- gtex.avg.log2RPKM.ts[rownames(gtex.avg.log2RPKM.ts) %in% genes, ]
  gtex.exp.norm <- gtex.avg.ts[rownames(gtex.avg.ts) %in% genes, ]
  rm(gtex.avg.ts, gtex.avg.log2RPKM.ts)
  gname <- ENSG.all.genes$external_gene_name[ENSG.all.genes$ensembl_gene_id %in% genes]
  cat(gname)
  row.order <- data.frame(gene=gname, alph=1, clstLog2=1, clstNorm=1)
  write.table(row.order, paste(filedir, "exp.row.txt", sep=""), quote=F, row.names=F, sep="\t")

  col.order <- data.frame(ts=names(gtex.exp.log2), alph=1:length(gtex.exp.log2), clstLog2=1:length(gtex.exp.log2), clstNorm=1:length(gtex.exp.log2))
  write.table(col.order, paste(filedir, "exp.col.txt", sep=""), quote=F, row.names=F, sep="\t")

  gtex.exp <- data.frame(matrix(nrow=53, ncol=4))
  colnames(gtex.exp) <- c("gene", "ts", "log2", "norm")
  gtex.exp$gene <- gname
  gtex.exp$ts <- names(gtex.exp.log2)
  gtex.exp$log2 <- gtex.exp.log2
  gtex.exp$norm <- gtex.exp.norm
  write.table(gtex.exp, paste(filedir, "exp.txt", sep=""), quote=F, row.names=F, sep="\t")
}else{
  stop("No gene exsits in expression data")
}
rm(hc, gtex.exp, gtex.exp.log2, gtex.exp.norm)

if(length(which(genes %in% bkgenes))>1){
  source(paste(config$data$scripts, "/GeneSet.R", sep=""))

  DEG <- DEGtest(genes, allgenes=bkgenes, adjP.method="bonferroni", MHC=MHC, ensgdir=config$data$ENSG, filedir=config$data$GTExExp)
  #DEG$logP <- -log10(DEG$p)
  #DEG$logFDR <- -log10(DEG$FDR)
  write.table(DEG, paste(filedir, "DEG.txt", sep=""), quote=F, row.names=F, sep="\t")
  rm(DEG)
  DEGgeneral <- DEGgeneraltest(genes, allgenes=bkgenes, adjP.method="bonferroni", MHC=MHC, ensgdir=config$data$ENSG, filedir=config$data$GTExExp)
  #DEGgeneral$logP <- -log10(DEGgeneral$p)
  #DEGgeneral$logFDR <- -log10(DEGgeneral$FDR)
  write.table(DEGgeneral, paste(filedir, "DEGgeneral.txt", sep=""), quote=F, row.names=F, sep="\t")
  rm(DEGgeneral)
  ExpTs <- ExpTstest(genes, allgenes=bkgenes, adjP.method="bonferroni", MHC=MHC, ensgdir=config$data$ENSG, filedir=config$data$GTExExp)
  write.table(ExpTs, paste(filedir, "ExpTs.txt", sep=""), quote=F, row.names=F, sep="\t")
  ExpTsG <- ExpTsGeneraltest(genes, allgenes=bkgenes, adjP.method="bonferroni", MHC=MHC, ensgdir=config$data$ENSG, filedir=config$data$GTExExp)
  write.table(ExpTsG, paste(filedir, "ExpTsGeneral.txt", sep=""), quote=F, row.names=F, sep="\t")
}

geneTable <- ENSG.all.genes[toupper(ENSG.all.genes$ensembl_gene_id) %in% toupper(genes),]
geneTable <- subset(geneTable, select=c("ensembl_gene_id", "entrezID", "external_gene_name"))
colnames(geneTable) <- c("ensg", "entrezID", "symbol")
load(paste(config$data$geneIDs, "/entrez2mim.RData", sep=""))

load(paste(config$data$geneIDs, "/entrez2uniprot.RData", sep=""))
geneTable$OMIM <- entrez2mim$mim[match(geneTable$entrezID, entrez2mim$entrezID)]
geneTable$uniprotID <- entrez2uniprot$uniprotID[match(geneTable$entrezID, entrez2uniprot$entrezID)]
geneTable$DrugBank <- NA
load(paste(config$data$geneIDs, "/DrugBank.RData", sep=""))

geneTable$DrugBank <- DrugBank$DrugBank[match(geneTable$uniprotID, DrugBank$uniprotID)]
write.table(geneTable, paste(filedir, "geneTable.txt", sep=""), quote=F, row.names=F, sep="\t")
