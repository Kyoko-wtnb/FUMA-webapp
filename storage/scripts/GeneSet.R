##### GeneSet enrichment analyses #####
# All MsigDB and humna brain gene set
#
# 21 aug 2016
# Kyoko Watanabe
#######################################

GeneSetTest <- function(genes, allgenes, adjP.method="BH", adjP.cutoff=0.05, MHC=TRUE, MHCextend=NULL, testCategory="all", minOverlap=2){
  require(data.table)
  #require(ggplot2)
  if(MHC==FALSE){
#local     load("/media/sf_Documents/VU/Data/ENSG.all.genes.RData")
    load("/data/ENSG/ENSG.all.genes.RData") #webserver

    if(is.null(MHCextend)){
      start <- ENSG.all.genes$start_position[ENSG.all.genes$external_gene_name=="MOG"]
      end <- ENSG.all.genes$end_position[ENSG.all.genes$external_gene_name=="COL11A2"]
    }else{
      start <- MHCextend[1]
      end <- MHCextend[2]
    }
    MHCgenes <- ENSG.all.genes$entrezID[ENSG.all.genes$chromosome_name==6 & ((ENSG.all.genes$end_position>=start&ENSG.all.genes$end_position<=end)|(ENSG.all.genes$start_position>=start&ENSG.all.genes$start_position<=end))]
    allgenes <- allgenes[!(allgenes %in% MHCgenes)]
    genes <- genes[genes %in% allgenes]
    cat("Excluding genes in MHC region\n")
  }
  genes <- genes[genes%in%allgenes]
#local   if(testCategory[1]=="all"){files = list.files("/media/sf_Documents/VU/Data/GeneSet/", ".+\\.txt")}
  if(testCategory[1]=="all"){files = list.files("/data/GeneSet/", ".+\\.txt")} #webserver
  else{files = testCategory}
  files <- files[!grepl("Human_Adult_Brain", files)]
  results <- data.frame(matrix(vector(), 0, 7, dimnames = list(c(), c("Category", "GeneSet", "N", "N_overlap", "p", "FDR", "genes"))))
  N <- length(allgenes)
  for(i in 1:length(files)){
#local     data <- fread(paste("/media/sf_Documents/VU/Data/GeneSet/", files[i], sep=""), head=F)
    data <- fread(paste("/data/GeneSet/", files[i], sep=""), head=F) #webserver

    colnames(data) <- c("GeneSet", "n", "genes")

    for(j in 1:nrow(data)){
      tempg <- unlist(strsplit(data$genes[j], ":"))
      tempg <- tempg[tempg %in% allgenes]
      data$n[j] <- length(tempg)
      data$genes[j] <- paste(tempg, collapse = ":")
    }

    temp <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "FDR", "genes"))))
    m <- length(genes)
    for(j in 1:nrow(data)){
      temp[j,1] <- data$GeneSet[j]
      n <- data$n[j]
      temp[j,2] <- data$n[j]
      GSgenes <- unlist(strsplit(data$genes[j], ":"))
      x <- length(which(genes %in% GSgenes))
      temp[j,3] <- x
      temp[j,6] <- paste(genes[which(genes %in% GSgenes)], collapse=":")
      if(x==0){temp[j,4]<-1}
      else{temp$p[j] <- phyper(x, n, N-n, m, lower.tail = F)}
    }
    temp[,5] <- p.adjust(temp$p, method=adjP.method)
    temp <- temp[temp$FDR <= adjP.cutoff,]
    temp <- temp[temp$N_overlap>=minOverlap,]
    temp <- temp[order(temp$p),]
    ct <- sub("(.+)\\.txt", "\\1", files[i])
    if(nrow(temp)>0){
      results <- rbind(results, data.frame(Category = rep(ct, nrow(temp)), temp))
    }
    cat(ct, ": enriched gene set #", nrow(temp), "\n")
  }
  cat("Tested genes #", length(genes), "\n")
  cat("Total genes #", length(allgenes), "\n")
  #if(nrow(results)>0){
  #  print(ggplot(results, aes(x=reorder(GeneSet, adj.p), y=-log10(adj.p)))+geom_bar(stat="identity", aes(fill=Category))+facet_grid(~Category, space = "free_x", scales = "free_x")+theme(axis.text.x=element_text(angle=60, hjust=1, size=8), legend.position="none"))
  #}
  return(results)
}

DEGtest <- function(genes, allgenes, adjP.method="BH", MHC=TRUE){
  require(data.table)
#local   load("/media/sf_Documents/VU/DAta/GTEx/gtex.avg.RPKM.genes.RData")
  load("/data/GeneExp/GTEx/gtex.avg.RPKM.genes.RData") #webserver
  if(MHC==FALSE){
#local     load("/media/sf_Documents/VU/Data/ENSG.all.genes.RData")
    load("/data/ENSG/ENSG.all.genes.RData") #webserver
    start <- ENSG.all.genes$start_position[ENSG.all.genes$external_gene_name=="MOG"]
    end <- ENSG.all.genes$end_position[ENSG.all.genes$external_gene_name=="COL11A2"]
    MHCgenes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$chromosome_name==6 & ((ENSG.all.genes$end_position>=start&ENSG.all.genes$end_position<=end)|(ENSG.all.genes$start_position>=start&ENSG.all.genes$start_position<=end))]
    allgenes <- allgenes[!(allgenes %in% MHCgenes)]
    cat("Excluding genes in MHC region\n")
  }
  allgenes <- allgenes[allgenes %in% gtex.avg.RPKM.genes]
  genes <- genes[genes %in% allgenes]
#local   file = "/media/sf_Documents/VU/Data/GTEx/gtex.v6.DEG.gmt"
  file = "/data/GeneExp/GTEx/gtex.v6.DEG.gmt" #webserver
  data <- fread(file, head=F)
  colnames(data) <- c("GeneSet", "n", "genes")
  results <- data.frame(matrix(vector(), 0, 7, dimnames = list(c(), c("Category", "GeneSet", "N", "N_overlap", "p", "FDR", "genes"))))
  N <- length(allgenes)
  for(i in 1:nrow(data)){
    tempg <- unlist(strsplit(data$genes[i], ":"))
    tempg <- tempg[tempg %in% allgenes]
    data$n[i] <- length(tempg)
    data$genes[i] <- paste(tempg, collapse = ":")
  }

  temp <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "FDR", "genes"))))
  m <- length(genes)
  for(j in 1:nrow(data)){
    temp[j,1] <- data$GeneSet[j]
    n <- data$n[j]
    temp[j,2] <- data$n[j]
    GSgenes <- unlist(strsplit(data$genes[j], ":"))
    x <- length(which(genes %in% GSgenes))
    temp[j,3] <- x
    temp[j,6] <- paste(genes[which(genes %in% GSgenes)], collapse=":")
    if(x==0){temp[j,4]<-1}
    else{temp$p[j] <- phyper(x, n, N-n, m, lower.tail = F)}
  }
  temp[,5] <- p.adjust(temp$p, method=adjP.method)

  ct <- c("DEG.up", "DEG.down", "DEG.twoside")
  for(i in ct){
    tr <- temp[grepl(paste("\\", sub(".+(\\..+)", "\\1", i),sep=""), temp$GeneSet),]
    tr$FDR <- p.adjust(tr$p, method=adjP.method)
    tr$GeneSet <- sub("(.+)\\..+", "\\1", tr$GeneSet)
    tr <- tr[order(tr$GeneSet),]
    results <- rbind(results, data.frame(Category=rep(i, nrow(tr)), tr))
  }
  cat("Tested genes #", length(genes),"\n")
  cat("Total genes #", length(allgenes), "\n")
  #if(MHC){title=paste("DEG with MHC: ", name, sep="")}
  #else{title=paste("DEG wo MHC: ", name, sep="")}
  #print(ggplot(results, aes(x=GeneSet, y=-log10(adj.p)))+geom_bar(stat="identity")+geom_hline(yintercept = -log10(0.05), linetype="dashed", color="pink")+facet_grid(Category~.)+theme(axis.text.x=element_text(angle=60, hjust=1, size=8))+ggtitle(title))
  return(results)
}

DEGgeneraltest <- function(genes, allgenes, adjP.method="BH", MHC=TRUE){
  require(data.table)
#local   load("/media/sf_Documents/VU/Data/GTEx/gtex.avg.RPKM.genes.RData")
  load("/data/GeneExp/GTEx/gtex.avg.RPKM.genes.RData") #webserver
 #require(ggplot2)
  if(MHC==FALSE){
#local     load("/media/sf_Documents/VU/Data/ENSG.all.genes.RData")
    load("/data/ENSG/ENSG.all.genes.RData") #webserver
    start <- ENSG.all.genes$start_position[ENSG.all.genes$external_gene_name=="MOG"]
    end <- ENSG.all.genes$end_position[ENSG.all.genes$external_gene_name=="COL11A2"]
    MHCgenes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$chromosome_name==6 & ((ENSG.all.genes$end_position>=start&ENSG.all.genes$end_position<=end)|(ENSG.all.genes$start_position>=start&ENSG.all.genes$start_position<=end))]
    allgenes <- allgenes[!(allgenes %in% MHCgenes)]
    cat("Excluding genes in MHC region\n")
  }
  allgenes <- allgenes[allgenes %in% gtex.avg.RPKM.genes]
  genes <- genes[genes %in% allgenes]
#local   file = "/media/sf_Documents/VU/Data/GTEx/gtex.v6.DEG.general.gmt"
  file = "/data/GeneExp/GTEx/gtex.v6.DEG.general.gmt" #webserver
  data <- fread(file, head=F)
  colnames(data) <- c("GeneSet", "n", "genes")
  results <- data.frame(matrix(vector(), 0, 7, dimnames = list(c(), c("Category", "GeneSet", "N", "N_overlap", "p", "FDR", "genes"))))
  N <- length(allgenes)
  for(i in 1:nrow(data)){
    tempg <- unlist(strsplit(data$genes[i], ":"))
    tempg <- tempg[tempg %in% allgenes]
    data$n[i] <- length(tempg)
    data$genes[i] <- paste(tempg, collapse = ":")
  }

  temp <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "FDR", "genes"))))
  m <- length(genes)
  for(j in 1:nrow(data)){
    temp[j,1] <- data$GeneSet[j]
    n <- data$n[j]
    temp[j,2] <- data$n[j]
    GSgenes <- unlist(strsplit(data$genes[j], ":"))
    x <- length(which(genes %in% GSgenes))
    temp[j,3] <- x
    temp[j,6] <- paste(genes[which(genes %in% GSgenes)], collapse=":")
    if(x==0){temp[j,4]<-1}
    else{temp$p[j] <- phyper(x, n, N-n, m, lower.tail = F)}
  }
  temp[,5] <- p.adjust(temp$p, method=adjP.method)

  ct <- c("DEG.up", "DEG.down", "DEG.twoside")
  for(i in ct){
    tr <- temp[grepl(paste("\\", sub(".+(\\..+)", "\\1", i),sep=""), temp$GeneSet),]
    tr$FDR <- p.adjust(tr$p, method=adjP.method)
    tr$GeneSet <- sub("(.+)\\..+", "\\1", tr$GeneSet)
    tr <- tr[order(tr$GeneSet),]
    results <- rbind(results, data.frame(Category=rep(i, nrow(tr)), tr))
  }
  cat("Tested genes #", length(genes),"\n")
  cat("Total genes #", length(allgenes), "\n")
  #if(MHC){title=paste("DEG with MHC: ", name, sep="")}
  #else{title=paste("DEG wo MHC: ", name, sep="")}
  #print(ggplot(results, aes(x=GeneSet, y=-log10(adj.p)))+geom_bar(stat="identity")+geom_hline(yintercept = -log10(0.05), linetype="dashed", color="pink")+facet_grid(Category~.)+theme(axis.text.x=element_text(angle=60, hjust=1, size=8))+ggtitle(title))
  return(results)
}

BSDEGtest <- function(genes, allgenes, adjP.method="BH", MHC=TRUE, name){
  require(data.table)
  #require(ggplot2)
  if(MHC==FALSE){
    load("~/Documents/VU/Data/ENSG.all.genes.RData")
    start <- ENSG.all.genes$start_position[ENSG.all.genes$external_gene_name=="MOG"]
    end <- ENSG.all.genes$end_position[ENSG.all.genes$external_gene_name=="COL11A2"]
    MHCgenes <- ENSG.all.genes$external_gene_name[ENSG.all.genes$chromosome_name==6 & ((ENSG.all.genes$end_position>=start&ENSG.all.genes$end_position<=end)|(ENSG.all.genes$start_position>=start&ENSG.all.genes$start_position<=end))]
    allgenes <- allgenes[!(allgenes %in% MHCgenes)]
    cat("Excluding genes in MHC region\n")
  }
  genes <- genes[genes %in% allgenes]
  file = "/media/sf_Documents/VU/Data/BrainSpan/BS.DEG.gmt"
  data <- fread(file, head=F)
  colnames(data) <- c("GeneSet", "n", "genes")
  results <- data.frame(matrix(vector(), 0, 7, dimnames = list(c(), c("Category", "GeneSet", "N", "N_overlap", "p", "adj.p", "genes"))))
  N <- length(allgenes)
  for(i in 1:nrow(data)){
    tempg <- unlist(strsplit(data$genes[i], ":"))
    tempg <- tempg[tempg %in% allgenes]
    data$n[i] <- length(tempg)
    data$genes[i] <- paste(tempg, collapse = ":")
  }

  temp <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "adj.p", "genes"))))
  m <- length(genes)
  for(j in 1:nrow(data)){
    temp[j,1] <- data$GeneSet[j]
    n <- data$n[j]
    temp[j,2] <- data$n[j]
    GSgenes <- unlist(strsplit(data$genes[j], ":"))
    x <- length(which(genes %in% GSgenes))
    temp[j,3] <- x
    temp[j,6] <- paste(genes[which(genes %in% GSgenes)], collapse=":")
    if(x==0){temp[j,4]<-1}
    else{temp$p[j] <- phyper(x, n, N-n, m, lower.tail = F)}
  }
  temp[,5] <- p.adjust(temp$p, method=adjP.method)

  ct <- c("DEG.up", "DEG.down", "DEG.twoside")
  for(i in ct){
    tr <- temp[grepl(paste("\\", sub(".+(\\..+)", "\\1", i),sep=""), temp$GeneSet),]
    tr$adj.p <- p.adjust(tr$p, method=adjP.method)
    tr$GeneSet <- sub("(.+)\\..+", "\\1", tr$GeneSet)
    tr <- tr[order(tr$GeneSet),]
    results <- rbind(results, data.frame(Category=rep(i, nrow(tr)), tr))
  }
  cat("Tested genes #", length(genes),"\n")
  cat("Total genes #", length(allgenes), "\n")
  #if(MHC){title=paste("DEG with MHC: ", name, sep="")}
  #else{title=paste("DEG wo MHC: ", name, sep="")}
  #print(ggplot(results, aes(x=GeneSet, y=-log10(adj.p)))+geom_bar(stat="identity")+geom_hline(yintercept = -log10(0.05), linetype="dashed", color="pink")+facet_grid(Category~.)+theme(axis.text.x=element_text(angle=60, hjust=1, size=8))+ggtitle(title))
  return(results)
}

entrez2symbol <- function(data){
#local   load("/media/sf_Documents/VU/Data/ENSG.all.genes.RData")
  load("/data/ENSG/ENSG.all.genes.RData") #webserver
  for(i in 1:nrow(data)){
    data$genes[i] <- paste(ENSG.all.genes$external_gene_name[ENSG.all.genes$entrezID %in% unlist(strsplit(data$genes[i], ":"))], collapse = ":")
  }
  return(data)
}

PrerankedTest <- function(data, geneset, nPerm=1000000){
  colnames(data) <- c("gene", "score")
  N <- length(which(geneset %in% data$gene))
  genes <- data
  genes$ES <- 0
  genes$ES[which(genes$gene %in% geneset)] <- 1/length(which(genes$gene %in% geneset))
  genes$ES[which(!(genes$gene %in% geneset))] <- -1/length(which(!(genes$gene %in% geneset)))
  genes$csum <- cumsum(genes$ES)
  ES <- max(genes$csum)
  data$ES <- genes$csum
  permES <- vector()

  pb = txtProgressBar(min=0, max=100, initial=0, style=3)
  for(i in 1:nPerm){
    g <- sample(genes$gene, N)
    genes$tempES <- 0
    genes$tempES[which(genes$gene %in% g)] <- 1/length(which(genes$gene %in% g))
    genes$tempES[which(!(genes$gene %in% g))] <- -1/length(which(!(genes$gene %in% g)))
    genes$csum <- cumsum(genes$tempES)
    permES <- c(permES, max(genes$tempES))
    setTxtProgressBar(pb, i*100/nPerm)
  }
  pval = length(which(permES>=ES))/nPerm
  return(list(overlappedGene=N, ES=data, P=pval))
}
