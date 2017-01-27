library(data.table)
args <- commandArgs(TRUE)
filedir <- args[1]
snps <- fread(paste(filedir, "snps.txt", sep=""), data.table=F)
ld <- fread(paste(filedir, "ld.txt", sep=""), data.table=F)
r2th <- as.numeric(args[2])
gwasPth <- as.numeric(args[3])
leadP <- as.numeric(args[4])
minMAF <- as.numeric(args[5])
mergeDist <- as.numeric(args[6])*1000
inlead <- args[7]
if(inlead != 0){
  inlead <- fread(paste(filedir, "input.lead", sep=""), data.table=F)
}else{
  inlead <- data.frame(matrix(vector(), 0, 3))
}
colnames(inlead) <- c("rsID", "chr", "pos")
inlead$uniqID <- snps$uniqID[match(inlead$rsID, snps$rsID)]


ld <- ld[ld$r2>=r2th,]
snps <- snps[(snps$gwasP<=gwasPth | is.na(snps$gwasP) | snps$uniqID %in% inlead$uniqID) & snps$MAF>=minMAF & snps$uniqID %in% ld$SNP2,]
ld <- ld[ld$SNP1 %in% snps$uniqID[snps$gwasP <= leadP | snps$uniqID %in% inlead$uniqID],]
ld <- ld[ld$SNP2 %in% snps$uniqID,]

inlead <- inlead[inlead$uniqID %in% snps$uniqID,]

indS <- data.frame(matrix(vector(), 0, 8, dimnames = list(c(),c("No","uniqID", "rsID", "chr", "pos", "p", "nSNPs", "nGWASSNPs"))))
leadSNPs <- unique(ld$SNP1)

while(length(leadSNPs)>0){
  j <- nrow(indS)+1
  ls <- snps$uniqID[snps$uniqID %in% leadSNPs][which.min(snps$gwasP[snps$uniqID %in% leadSNPs])]
  indS[j,2] <- ls
  indS[j,3] <- snps$rsID[which(snps$uniqID==ls)]
  indS[j,4] <- snps$chr[which(snps$uniqID==ls)]
  indS[j,5] <- snps$pos[which(snps$uniqID==ls)]
  indS[j,6] <- snps$gwasP[which(snps$uniqID==ls)]
  indS[j,7] <- length(which(ld$SNP1==ls))
  indS[j,8] <- length(which(ld$SNP2[ld$SNP1==ls] %in% snps$uniqID[!is.na(snps$gwasP)]))

  leadSNPs <- leadSNPs[!(leadSNPs %in% ld$SNP2[which(ld$SNP1==ls)])]
}

indS <- indS[order(indS$pos),]
indS <- indS[order(indS$chr),]
indS$No <- 1:nrow(indS)

ld <- ld[ld$SNP1 %in% indS$uniqID,]
snps <- snps[snps$uniqID %in% ld$SNP2,]

indS$GenomicLocus <- NA
loci <- data.frame(matrix(vector(), 0, 12, dimnames=list(c(), c("GenomicLocus","uniqID", "rsID", "chr", "pos", "p", "start", "end", "nIndSigSNPs", "IndSigSNPs", "nSNPs", "nGWASSNPs"))))
j<-1
loci[j,1] <- j
loci[j,2] <- indS$uniqID[1]
loci[j,3] <- indS$rsID[1]
loci[j,4] <- indS$chr[1]
loci[j,5] <- indS$pos[1]
loci[j,6] <- indS$p[1]
loci[j,9] <- 1
loci[j,7] <- min(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==indS$uniqID[j]]])
loci[j,8] <- max(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==indS$uniqID[j]]])
loci[j,10] <- indS$rsID[1]
indS$GenomicLocus[j] <- 1
if(nrow(indS)>1){
  for(i in 2:nrow(indS)){
    if(loci$chr[j]==indS$chr[i] & min(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==indS$uniqID[i]]])-loci$end[j]<=mergeDist){
      if(indS$p[i] < loci[j,6]){
      loci[j,2] <- indS$uniqID[i]
      loci[j,3] <- indS$rsID[i]
      loci[j,4] <- indS$chr[i]
      loci[j,5] <- indS$pos[i]
      loci[j,6] <- indS$p[i]
      }
      loci[j,9] <- loci[j,7]+1
      loci[j,7] <- min(c(loci[j,8], min(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==indS$uniqID[i]]])))
      loci[j,8] <- max(c(loci[j,9], max(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==indS$uniqID[i]]])))
      loci[j,10] <- paste(loci[j,10], indS$rsID[i], sep=":")
      indS$GenomicLocus[i] <- j
    }else{
      j <- j+1
      loci[j,1] <- j
      loci[j,2] <- indS$uniqID[i]
      loci[j,3] <- indS$rsID[i]
      loci[j,4] <- indS$chr[i]
      loci[j,5] <- indS$pos[i]
      loci[j,6] <- indS$p[i]
      loci[j,9] <- 1
      loci[j,7] <- min(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==indS$uniqID[i]]])
      loci[j,8] <- max(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==indS$uniqID[i]]])
      loci[j,10] <- indS$rsID[i]
      indS$GenomicLocus[i] <- j
    }
  }
}

loci <- loci[,c(1:8,11,12,9,10)]

snps$r2 <- sapply(snps$uniqID, function(x){max(ld$r2[ld$SNP2==x])})
snps$IndSigSNP <- sapply(snps$uniqID, function(x){paste(snps$rsID[snps$uniqID %in%ld$SNP1[ld$SNP2==x & ld$r2==max(ld$r2[ld$SNP2==x])]], collapse=":")})
snps$GenomicLocus <- NA
for(i in 1:nrow(loci)){
  ls <- snps$uniqID[snps$rsID %in% unlist(strsplit(loci$IndSigSNPs[i], ":"))]
  loci$nSNPs[i] <- length(unique(ld$SNP2[ld$SNP1 %in% ls]))
  loci$nGWASSNPs[i] <- length(unique(ld$SNP2[ld$SNP1 %in% ls & ld$SNP2 %in% snps$uniqID[!is.na(snps$gwasP)]]))
  snps$GenomicLocus[snps$chr==loci$chr[i]&snps$pos>=loci$start[i] & snps$pos<=loci$end[i]] <- i
}

annov.input <- data.frame(chr=snps$chr, start=snps$pos, end=snps$pos, ref=snps$ref, alt=snps$alt)
#n <- t(sapply(snps$uniqID, function(x){unlist(strsplit(x, ":"))[3:4]}))
#annov.input$ref <- n[,1]
#annov.input$alt <- n[,2]

indS <- subset(indS, select=c("No","GenomicLocus", "uniqID", "rsID", "chr", "pos", "p", "nSNPs", "nGWASSNPs"))

write.table(indS, paste(filedir,"IndSigSNPs.txt",sep=""), quote=F, row.names=F, sep="\t")
write.table(loci, paste(filedir, "GenomicRiskLoci.txt", sep=""), quote=F, row.names=F, sep="\t")
write.table(snps, paste(filedir,"snps.txt",sep=""), quote=F, row.names=F, sep="\t")
write.table(ld, paste(filedir,"ld.txt",sep=""), quote=F, row.names=F, sep="\t")
write.table(annov.input, paste(filedir, "annov.input", sep=""), quote=F, row.names=F, col.names=F, sep="\t")
