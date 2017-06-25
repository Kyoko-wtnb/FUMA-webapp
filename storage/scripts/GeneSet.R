##### GeneSet enrichment analyses #####
# DEG enrichment test
#
# 21 aug 2016
# 15 Mar 2017 added ExpTs test
# Kyoko Watanabe
#######################################

DEGtest <- function(genes, allgenes, adjP.method="BH", MHC=TRUE, ensgdir, filedir){
  require(data.table)
  load(paste(filedir, "/gtex.avg.RPKM.genes.RData", sep=""))
  if(MHC==FALSE){
    ENSG <- fread(config$data$ENSG, data.table=F)
    start <- ENSG$start_position[ENSG$external_gene_name=="MOG"]
    end <- ENSG$end_position[ENSG$external_gene_name=="COL11A2"]
    MHCgenes <- ENSG$ensembl_gene_id[ENSG$chromosome_name==6 & ((ENSG$end_position>=start&ENSG$end_position<=end)|(ENSG$start_position>=start&ENSG$start_position<=end))]
    allgenes <- allgenes[!(allgenes %in% MHCgenes)]
    cat("Excluding genes in MHC region\n")
  }
  allgenes <- allgenes[allgenes %in% gtex.avg.RPKM.genes]
  genes <- genes[genes %in% allgenes]
  file = paste(filedir, "/gtex.v6.DEG.gmt", sep="")
  data <- fread(file, head=F)
  colnames(data) <- c("GeneSet", "n", "genes")
  results <- data.frame(matrix(vector(), 0, 7, dimnames = list(c(), c("Category", "GeneSet", "N", "N_overlap", "p", "adjP", "genes"))))
  N <- length(allgenes)
  for(i in 1:nrow(data)){
    tempg <- unlist(strsplit(data$genes[i], ":"))
    tempg <- tempg[tempg %in% allgenes]
    data$n[i] <- length(tempg)
    data$genes[i] <- paste(tempg, collapse = ":")
  }

  temp <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "adjP", "genes"))))
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
    tr$adjP <- p.adjust(tr$p, method=adjP.method)
    tr$GeneSet <- sub("(.+)\\..+", "\\1", tr$GeneSet)
    tr <- tr[order(tr$GeneSet),]
    results <- rbind(results, data.frame(Category=rep(i, nrow(tr)), tr))
  }
  return(results)
}

DEGgeneraltest <- function(genes, allgenes, adjP.method="BH", MHC=TRUE, ensgdir ,filedir){
  require(data.table)
  load(paste(filedir, "/gtex.avg.RPKM.genes.RData", sep=""))
  if(MHC==FALSE){
    ENSG <- fread(config$data$ENSG, data.table=F)
    start <- ENSG$start_position[ENSG$external_gene_name=="MOG"]
    end <- ENSG$end_position[ENSG$external_gene_name=="COL11A2"]
    MHCgenes <- ENSG$ensembl_gene_id[ENSG$chromosome_name==6 & ((ENSG$end_position>=start&ENSG$end_position<=end)|(ENSG$start_position>=start&ENSG$start_position<=end))]
    allgenes <- allgenes[!(allgenes %in% MHCgenes)]
    cat("Excluding genes in MHC region\n")
  }
  allgenes <- allgenes[allgenes %in% gtex.avg.RPKM.genes]
  genes <- genes[genes %in% allgenes]
  file = paste(filedir, "/gtex.v6.DEG.general.gmt", sep="")
  data <- fread(file, head=F)
  colnames(data) <- c("GeneSet", "n", "genes")
  results <- data.frame(matrix(vector(), 0, 7, dimnames = list(c(), c("Category", "GeneSet", "N", "N_overlap", "p", "adjP", "genes"))))
  N <- length(allgenes)
  for(i in 1:nrow(data)){
    tempg <- unlist(strsplit(data$genes[i], ":"))
    tempg <- tempg[tempg %in% allgenes]
    data$n[i] <- length(tempg)
    data$genes[i] <- paste(tempg, collapse = ":")
  }

  temp <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "adjP", "genes"))))
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
    tr$adjP <- p.adjust(tr$p, method=adjP.method)
    tr$GeneSet <- sub("(.+)\\..+", "\\1", tr$GeneSet)
    tr <- tr[order(tr$GeneSet),]
    results <- rbind(results, data.frame(Category=rep(i, nrow(tr)), tr))
  }
  return(results)
}

ExpTstest <- function(genes, allgenes, adjP.method="BH", MHC=TRUE, ensgdir ,filedir){
  require(data.table)
  load(paste(filedir, "/gtex.avg.RPKM.genes.RData", sep=""))
  if(MHC==FALSE){
    ENSG <- fread(config$data$ENSG, data.table=F)
    start <- ENSG$start_position[ENSG$external_gene_name=="MOG"]
    end <- ENSG$end_position[ENSG$external_gene_name=="COL11A2"]
    MHCgenes <- ENSG$ensembl_gene_id[ENSG$chromosome_name==6 & ((ENSG$end_position>=start&ENSG$end_position<=end)|(ENSG$start_position>=start&ENSG$start_position<=end))]
    allgenes <- allgenes[!(allgenes %in% MHCgenes)]
    cat("Excluding genes in MHC region\n")
  }
  allgenes <- allgenes[allgenes %in% gtex.avg.RPKM.genes]
  genes <- genes[genes %in% allgenes]
  file = paste(filedir, "/ExpTs.txt", sep="")
  data <- fread(file, head=F)
  colnames(data) <- c("GeneSet", "n", "genes")
  N <- length(allgenes)
  for(i in 1:nrow(data)){
    tempg <- unlist(strsplit(data$genes[i], ":"))
    tempg <- tempg[tempg %in% allgenes]
    data$n[i] <- length(tempg)
    data$genes[i] <- paste(tempg, collapse = ":")
  }
  results <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "adjP", "genes"))))
  m <- length(genes)
  for(j in 1:nrow(data)){
    results[j,1] <- data$GeneSet[j]
    n <- data$n[j]
    results[j,2] <- data$n[j]
    GSgenes <- unlist(strsplit(data$genes[j], ":"))
    x <- length(which(genes %in% GSgenes))
    results[j,3] <- x
    results[j,6] <- paste(genes[which(genes %in% GSgenes)], collapse=":")
    if(x==0){results[j,4]<-1}
    else{results$p[j] <- phyper(x, n, N-n, m, lower.tail = F)}
  }
  results[,5] <- p.adjust(results$p, method=adjP.method)
  return(results)
}

ExpTsGeneraltest <- function(genes, allgenes, adjP.method="BH", MHC=TRUE, ensgdir ,filedir){
  require(data.table)
  load(paste(filedir, "/gtex.avg.RPKM.genes.RData", sep=""))
  if(MHC==FALSE){
    ENSG <- fread(config$data$ENSG, data.table=F)
    start <- ENSG$start_position[ENSG$external_gene_name=="MOG"]
    end <- ENSG$end_position[ENSG$external_gene_name=="COL11A2"]
    MHCgenes <- ENSG$ensembl_gene_id[ENSG$chromosome_name==6 & ((ENSG$end_position>=start&ENSG$end_position<=end)|(ENSG$start_position>=start&ENSG$start_position<=end))]
    allgenes <- allgenes[!(allgenes %in% MHCgenes)]
    cat("Excluding genes in MHC region\n")
  }
  allgenes <- allgenes[allgenes %in% gtex.avg.RPKM.genes]
  genes <- genes[genes %in% allgenes]
  file = paste(filedir, "/ExpTsGeneral.txt", sep="")
  data <- fread(file, head=F)
  colnames(data) <- c("GeneSet", "n", "genes")
  N <- length(allgenes)
  for(i in 1:nrow(data)){
    tempg <- unlist(strsplit(data$genes[i], ":"))
    tempg <- tempg[tempg %in% allgenes]
    data$n[i] <- length(tempg)
    data$genes[i] <- paste(tempg, collapse = ":")
  }
  results <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "adjP", "genes"))))
  m <- length(genes)
  for(j in 1:nrow(data)){
    results[j,1] <- data$GeneSet[j]
    n <- data$n[j]
    results[j,2] <- data$n[j]
    GSgenes <- unlist(strsplit(data$genes[j], ":"))
    x <- length(which(genes %in% GSgenes))
    results[j,3] <- x
    results[j,6] <- paste(genes[which(genes %in% GSgenes)], collapse=":")
    if(x==0){results[j,4]<-1}
    else{results$p[j] <- phyper(x, n, N-n, m, lower.tail = F)}
  }
  results[,5] <- p.adjust(results$p, method=adjP.method)
  return(results)
}
