library(data.table)
args <- commandArgs(TRUE)
filedir <- args[1]
genetype <- args[2]
exMHC <- as.numeric(args[3])
extMHC <- args[4]
posMap <- as.numeric(args[5])
posMapWindow <- as.numeric(args[6])
posMapWindowSize <- as.numeric(args[7])*1000
posMapAnnot <- args[8]
posMapCADDth <- as.numeric(args[9])
posMapRDBth <- args[10]
posMapChr15 <- args[11]
posMapChr15Max <- as.numeric(args[12])
posMapChr15Meth <- args[13]
eqtlMap <- as.numeric(args[14])
eqtlMaptss <- args[15]
eqtlMapSigeqtl <- as.numeric(args[16])
eqtlP <- as.numeric(args[17])
eqtlMapCADDth <- as.numeric(args[18])
eqtlMapRDBth <- args[19]
eqtlMapChr15 <- args[20]
eqtlMapChr15Max <- as.numeric(args[21])
eqtlMapChr15Meth <- args[22]

#write.table(c(filedir, genetype, exMHC, extMHC, posMap, posMapWindow, posMapWindowSize, posMapAnnot, posMapCADDth, posMapRDBth, posMapChr15, posMapChr15Max, posMapChr15Meth,
#  eqtlMap, eqtlMaptss, eqtlMapSigeqtl, eqtlP, eqtlMapCADDth, eqtlMapRDBth, eqtlMapChr15, eqtlMapChr15Max, eqtlMapChr15Meth), "../files/1/test.txt")

#local load(paste(filedir, "../../data/ENSG.all.genes.RData", sep=""))
load("/data/ENSG/ENSG.all.genes.RData") #webserver

if(genetype!="all"){
  genetype <- unique(unlist(strsplit(genetype, ":")))
  ENSG.all.genes <- ENSG.all.genes[ENSG.all.genes$gene_biotype %in% genetype,]
}
if(exMHC==1){
  start <- ENSG.all.genes$start_position[ENSG.all.genes$external_gene_name=="MOG"]
  end <- ENSG.all.genes$end_position[ENSG.all.genes$external_gene_name=="COL11A2"]
  if(extMHC!="NA"){
    extMHC <- as.numeric(unlist(strsplit(extMHC, "-")))
    if(extMHC[1]<start){start<-extMHC[1]}
    if(extMHC[2]>end){end <- extMHC[2]}
  }
  MHCgenes <- ENSG.all.genes$ensembl_gene_id[ENSG.all.genes$chromosome_name==6 & ((ENSG.all.genes$end_position>=start&ENSG.all.genes$end_position<=end)|(ENSG.all.genes$start_position>=start&ENSG.all.genes$start_position<=end))]
  ENSG.all.genes <- ENSG.all.genes[!(ENSG.all.genes$ensembl_gene_id%in%MHCgenes),]
}
snps <- fread(paste(filedir, "snps.txt", sep=""), data.table=F)
annot <- fread(paste(filedir, "annot.txt", sep=""), data.table=F)
ld <- fread(paste(filedir, "ld.txt", sep=""), data.table=F)
genes <- vector()
if(posMap==1){
  annov <- fread(paste(filedir, "annov.txt", sep=""), data.table=F)
  annov <- annov[annov$gene %in% ENSG.all.genes$ensembl_gene_id,]
  if(posMapCADDth>0){
    annov <- annov[annov$uniqID %in% annot$uniqID[annot$CADD>=posMapCADDth],]
  }
  if(posMapRDBth!="NA"){
    annov <- annov[annov$uniqID %in% annot$uniqID[annot$RDB>=posMapRDBth],]
  }
  if(posMapChr15!="NA"){
    posMapChr15 <- unique(unlist(strsplit(posMapChr15, ":")))
    epi <- data.frame(uniqID=snps$uniqID, epi=NA)
    temp <- subset(annot, select=c("uniqID", posMapChr15))
    if(posMapChr15Meth=="any"){
      if(length(posMapChr15)>1){
        temp$epi <- apply(temp[,2:ncol(temp)], 1, min)
      }else{
        temp$epi <- temp[,2]
      }
    }else if(posMapChr15Meth=="majority"){
      if(length(posMapChr15)>1){
        temp$epi <- apply(temp[,2:ncol(temp)], 1, function(x){as.numeric(names(sort(table(x)), decreasing=T)[1])})
      }else{
        temp$epi <- temp[,2]
      }
    }else{
      if(length(posMapChr15)>1){
        temp$epi <- apply(temp[,2:ncol(temp)], 1, max)
      }else{
        temp$epi <- temp[,2]
      }
    }
    epi$epi <- temp$epi[match(epi$uniqID, temp$uniqID)]
    epi <- epi[epi$epi<=posMapChr15Max,]
    annov <- annov[annov$uniqID %in% epi$uniqID,]
    rm(epi, temp)
  }
  if(posMapWindow==1){
    annov <- annov[annov$dist <= posMapWindowSize,]
  }else{
    posMapAnnot <- unique(unlist(strsplit(posMapAnnot, ":")))
    annov <- annov[grepl(paste(posMapAnnot, collapse="|"), annov$annot),]
  }

  genes <- c(genes, unique(annov$gene))
}

if(eqtlMap==1){
  eqtl <- fread(paste(filedir, "eqtl.txt", sep=""), data.table=F)
  if(nrow(eqtl)>0){
  	eqtl <- eqtl[eqtl$gene %in% ENSG.all.genes$ensembl_gene_id,]
    if(eqtlMapCADDth>0){
      eqtl <- eqtl[eqtl$uniqID %in% annot$uniqID[annot$CADD>=eqtlMapCADDth],]
    }
    if(eqtlMapRDBth!="NA"){
      eqtl <- eqtl[eqtl$uniqID %in% eqtl$uniqID[annot$RDB>=eqtlMapRDBth],]
    }
    if(eqtlMapChr15!="NA"){
      eqtlMapChr15 <- unique(unlist(strsplit(eqtlMapChr15, ":")))
      epi <- data.frame(uniqID=snps$uniqID, epi=NA)
      temp <- subset(annot, select=c("uniqID", eqtlMapChr15))
      if(eqtlMapChr15Meth=="any"){
        if(length(eqtlMapChr15)>1){
          temp$epi <- apply(temp[,2:ncol(temp)], 1, min)
        }else{
          temp$epi <- temp[,2]
        }
      }else if(eqtlMapChr15Meth=="majority"){
        if(length(eqtlMapChr15)>1){
          temp$epi <- apply(temp[,2:ncol(temp)], 1, function(x){as.numeric(names(sort(table(x)), decreasing=T)[1])})
        }else{
          temp$epi <- temp[,2]
        }
      }else{
        if(length(eqtlMapChr15)>1){
          temp$epi <- apply(temp[,2:ncol(temp)], 1, max)
        }else{
          temp$epi <- temp[,2]
        }
      }
      epi$epi <- temp$epi[match(epi$uniqID, temp$uniqID)]
      epi <- epi[epi$epi<=posMapChr15Max,]
      eqtl <- eqtl[eqtl$uniqID %in% epi$uniqID,]
      rm(epi, temp)
    }
    genes <- c(genes, unique(eqtl$gene))
    eqtl$chr <- snps$chr[match(eqtl$uniqID, snps$uniqID)]
    eqtl$pos <- snps$pos[match(eqtl$uniqID, snps$uniqID)]
    eqtl$symbol <- ENSG.all.genes$external_gene_name[match(eqtl$gene, ENSG.all.genes$ensembl_gene_id)]
    write.table(eqtl, paste(filedir, "eqtl.txt", sep=""), quote=F, row.names=F, sep="\t")
  }
}

geneTable <- ENSG.all.genes[ENSG.all.genes$ensembl_gene_id %in% genes,]
colnames(geneTable) <- c("ensg", "symbol", "chr", "start", "end", "strand", "status", "type", "entrezID","HUGO")
if(nrow(geneTable)>0){
  geneTable$chr <- as.numeric(geneTable$chr)
  if(posMap==1){
    geneTable$posMapSNPs <- sapply(geneTable$ensg, function(x){length(which(annov$gene==x))})
    geneTable$posMapMaxCADD <- sapply(geneTable$ensg, function(x){if(x %in% annov$gene){max(annot$CADD[annot$uniqID%in%annov$uniqID[annov$gene==x]])}else{0}})
  }
  if(eqtlMap==1){
    geneTable$eqtlMapSNPs <- sapply(geneTable$ensg, function(x){length(unique(eqtl$uniqID[eqtl$gene==x]))})
    geneTable$eqtlMapminP <- sapply(geneTable$ensg, function(x){if(x %in% eqtl$gene){min(eqtl$p[eqtl$gene==x])}else{NA}})
    geneTable$eqtlMapminQ <- sapply(geneTable$ensg, function(x){if(x %in% eqtl$gene[!is.na(eqtl$FDR)]){min(eqtl$FDR[eqtl$gene==x & !is.na(eqtl$FDR)])}else{NA}})
    geneTable$eqtlMapts <- sapply(geneTable$ensg, function(x){if(x %in% eqtl$gene){paste(sub("gtex.", "",unique(eqtl$tissue[eqtl$gene==x])), collapse = ":")}else{NA}})
    geneTable$eqtlDirection <- NA
    geneTable$eqtlDirection <- sapply(geneTable$ensg, function(x){if(x %in% eqtl$gene){if(length(which(eqtl$tz[eqtl$gene==x]>0))>=length(which(eqtl$tz[eqtl$gene==x]<0))){"+"}else{"-"}}else{"NA"}})
  }
  if(posMap==1 & eqtlMap==1){
    geneTable$minGwasP <- sapply(geneTable$ensg, function(x){if(length(which(!is.na(snps$gwasP) & (snps$uniqID %in% annov$uniqID[annov$gene==x] | snps$uniqID %in% eqtl$uniqID[eqtl$gene==x])))>0)
    {min(snps$gwasP[!is.na(snps$gwasP) & (snps$uniqID %in% annov$uniqID[annov$gene==x] | snps$uniqID %in% eqtl$uniqID[eqtl$gene==x])])}else{NA}})
    geneTable$leadSNPs <- sapply(geneTable$ensg, function(x){paste(unique(snps$rsID[snps$uniqID %in% ld$SNP1[ld$SNP2 %in% annov$uniqID[annov$gene==x] | ld$SNP2 %in% eqtl$uniqID[eqtl$gene==x]]]), collapse = ":")})
  }else if(posMap==1){
    geneTable$minGwasP <- sapply(geneTable$ensg, function(x){if(length(which(!is.na(snps$gwasP) & snps$uniqID %in% annov$uniqID[which(annov$gene==x)]))>0)
    {min(snps$gwasP[!is.na(snps$gwasP) & snps$uniqID %in% annov$uniqID[annov$gene==x]])}else{NA}})
    geneTable$leadSNPs <- sapply(geneTable$ensg, function(x){paste(unique(snps$rsID[snps$uniqID %in% ld$SNP1[ld$SNP2 %in% annov$uniqID[which(annov$gene==x)]]]), collapse = ":")})
  }else{
    geneTable$minGwasP <- sapply(geneTable$ensg, function(x){if(length(which(!is.na(snps$gwasP) & snps$uniqID %in% eqtl$uniqID[eqtl$gene==x]))>0)
    {min(snps$gwasP[!is.na(snps$gwasP) & snps$uniqID %in% eqtl$uniqID[eqtl$gene==x]])}else{NA}})
    geneTable$leadSNPs <- sapply(geneTable$ensg, function(x){paste(unique(snps$rsID[snps$uniqID %in% ld$SNP1[ld$SNP2 %in% eqtl$uniqID[eqtl$gene==x]]]), collapse = ":")})
  }

  loci.table <- fread(paste(filedir, "leadSNPs.txt", sep=""), data.table=F)
  geneTable$interval <- NA
  for(i in 1:nrow(geneTable)){
    ls <- unlist(strsplit(geneTable$leadSNPs[i], ":"))
    geneTable$interval[i] <- paste(unique(loci.table$interval[loci.table$rsID %in% ls]), collapse=":")
  }
}

write.table(geneTable, paste(filedir, "genes.txt", sep=""), quote=F, row.names=F, sep="\t")

summary <- data.frame(matrix(nrow=5, ncol=2))
summary[,1] <- c("#lead SNPs", "#Genomic risk loci", "#candidate SNPs", "#candidate GWAS tagged SNPs", "#mapped genes")
loci.table <- fread(paste(filedir, "leadSNPs.txt", sep=""), data.table=F)
intervals <- fread(paste(filedir, "intervals.txt", sep=""), data.table=F)
summary[1,2] <- nrow(loci.table)
summary[2,2] <- nrow(intervals)
summary[3,2] <- nrow(snps)
summary[4,2] <- length(which(!is.na(snps$gwasP)))
summary[5,2] <- nrow(geneTable)
write.table(summary, paste(filedir, "summary.txt", sep=""), quote=F, row.names=F, col.names=F, sep="\t")

int.table <- data.frame(interval=intervals$Interval, label=NA, nSNPs=NA, size=NA, nGenes=NA, nWithinGene=NA)
int.table$label <- paste(intervals$chr, paste(intervals$start, intervals$end, sep="-"), sep=":")
temp <- table(snps$Interval)
temp <- data.frame(name=names(temp), n=as.numeric(temp))
int.table$nSNPs <- temp$n[match(int.table$interval, temp$name)]
int.table$nSNPs[is.na(int.table$nSNPs)] <- 0
int.table$size <- intervals$end - intervals$start
if(nrow(geneTable)>0){
  temp <- table(unlist(strsplit(geneTable$interval, ":")))
  temp <- data.frame(name=names(temp), n=as.numeric(temp))
  int.table$nGenes <- temp$n[match(int.table$interval, temp$name)]
}

int.table$nGenes[is.na(int.table$nGenes)] <- 0
for(i in 1:nrow(int.table)){
  int.table$nWithinGene[i] <- length(which(ENSG.all.genes$chromosome_name==intervals$chr[i] & (
    (ENSG.all.genes$start_position<=intervals$start[i] & ENSG.all.genes$end_position>=intervals$end[i])
    | (ENSG.all.genes$start_position>=intervals$start[i] & ENSG.all.genes$start_position<=intervals$end[i])
    | (ENSG.all.genes$end_position>=intervals$start[i] & ENSG.all.genes$end_position<=intervals$end[i])
    | (ENSG.all.genes$start_position>=intervals$start[i] & ENSG.all.genes$end_position<=intervals$end[i])
  )))
}

write.table(int.table, paste(filedir, "interval_sum.txt", sep=""), quote=F, row.names=F, sep="\t")
