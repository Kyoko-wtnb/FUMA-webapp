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

loci.table <- data.frame(matrix(vector(), 0, 8, dimnames = list(c(),c("No","uniqID", "rsID", "chr", "pos", "p", "nSNPs", "nGWASSNPs"))))
leadSNPs <- unique(ld$SNP1)

while(length(leadSNPs)>0){
  j <- nrow(loci.table)+1
  ls <- snps$uniqID[snps$uniqID %in% leadSNPs][which.min(snps$gwasP[snps$uniqID %in% leadSNPs])]
  loci.table[j,2] <- ls
  loci.table[j,3] <- snps$rsID[snps$uniqID==ls]
  loci.table[j,4] <- snps$chr[snps$uniqID==ls]
  loci.table[j,5] <- snps$pos[snps$uniqID==ls]
  loci.table[j,6] <- snps$gwasP[snps$uniqID==ls]
  loci.table[j,7] <- length(which(ld$SNP1==ls))
  loci.table[j,8] <- length(which(ld$SNP2[ld$SNP1==ls] %in% snps$uniqID[!is.na(snps$gwasP)]))

  leadSNPs <- leadSNPs[!(leadSNPs %in% ld$SNP2[ld$SNP1==ls])]
}
loci.table <- loci.table[order(loci.table$pos),]
loci.table <- loci.table[order(loci.table$chr),]
loci.table$No <- 1:nrow(loci.table)

ld <- ld[ld$SNP1 %in% loci.table$uniqID,]
snps <- snps[snps$uniqID %in% ld$SNP2,]

loci.table$interval <- NA
intervals <- data.frame(matrix(vector(), 0, 12, dimnames=list(c(), c("No","uniqID", "toprsID", "chr", "pos", "p", "nLeadSNPs", "start", "end", "leadSNPs", "nSNPs", "nGWASSNPs"))))
j<-1
intervals[j,1] <- j
intervals[j,2] <- loci.table$uniqID[1]
intervals[j,3] <- loci.table$rsID[1]
intervals[j,4] <- loci.table$chr[1]
intervals[j,5] <- loci.table$pos[1]
intervals[j,6] <- loci.table$p[1]
intervals[j,7] <- 1
intervals[j,8] <- min(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==loci.table$uniqID[j]]])
intervals[j,9] <- max(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==loci.table$uniqID[j]]])
intervals[j,10] <- loci.table$rsID[1]
loci.table$interval[j] <- 1
if(nrow(loci.table)>1){
  for(i in 2:nrow(loci.table)){
    if(intervals$chr[j]==loci.table$chr[i] & min(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==loci.table$uniqID[i]]])-intervals$end[j]<=mergeDist){
      if(loci.table$p[i] < intervals[j,6]){
      intervals[j,2] <- loci.table$uniqID[i]
      intervals[j,3] <- loci.table$rsID[i]
      intervals[j,4] <- loci.table$chr[i]
      intervals[j,5] <- loci.table$pos[i]
      intervals[j,6] <- loci.table$p[i]
      }
      intervals[j,7] <- intervals[j,7]+1
      intervals[j,8] <- min(c(intervals[j,8], min(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==loci.table$uniqID[i]]])))
      intervals[j,9] <- max(c(intervals[j,9], max(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==loci.table$uniqID[i]]])))
      intervals[j,10] <- paste(intervals[j,10], loci.table$rsID[i], sep=":")
      loci.table$interval[i] <- j
    }else{
      j <- j+1
      intervals[j,1] <- j
      intervals[j,2] <- loci.table$uniqID[i]
      intervals[j,3] <- loci.table$rsID[i]
      intervals[j,4] <- loci.table$chr[i]
      intervals[j,5] <- loci.table$pos[i]
      intervals[j,6] <- loci.table$p[i]
      intervals[j,7] <- 1
      intervals[j,8] <- min(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==loci.table$uniqID[i]]])
      intervals[j,9] <- max(snps$pos[snps$uniqID %in% ld$SNP2[ld$SNP1==loci.table$uniqID[i]]])
      intervals[j,10] <- loci.table$rsID[i]
      loci.table$interval[i] <- j
    }
  }
}

snps$r2 <- sapply(snps$uniqID, function(x){max(ld$r2[ld$SNP2==x])})
snps$leadSNP <- sapply(snps$uniqID, function(x){paste(snps$rsID[snps$uniqID %in%ld$SNP1[ld$SNP2==x & ld$r2==max(ld$r2[ld$SNP2==x])]], collapse=":")})
snps$Interval <- NA
for(i in 1:nrow(intervals)){
  ls <- snps$uniqID[snps$rsID %in% unlist(strsplit(intervals$leadSNPs[i], ":"))]
  intervals$nSNPs[i] <- length(unique(ld$SNP2[ld$SNP1 %in% ls]))
  intervals$nGWASSNPs[i] <- length(unique(ld$SNP2[ld$SNP1 %in% ls & ld$SNP2 %in% snps$uniqID[!is.na(snps$gwasP)]]))
  snps$Interval[snps$chr==intervals$chr[i]&snps$pos>=intervals$start[i] & snps$pos<=intervals$end[i]] <- i
}

annov.input <- data.frame(chr=snps$chr, start=snps$pos, end=snps$pos, ref=snps$ref, alt=snps$alt)
#n <- t(sapply(snps$uniqID, function(x){unlist(strsplit(x, ":"))[3:4]}))
#annov.input$ref <- n[,1]
#annov.input$alt <- n[,2]

write.table(loci.table, paste(filedir,"leadSNPs.txt",sep=""), quote=F, row.names=F, sep="\t")
write.table(intervals, paste(filedir, "intervals.txt", sep=""), quote=F, row.names=F, sep="\t")
write.table(snps, paste(filedir,"snps.txt",sep=""), quote=F, row.names=F, sep="\t")
write.table(ld, paste(filedir,"ld.txt",sep=""), quote=F, row.names=F, sep="\t")
write.table(annov.input, paste(filedir, "annov.input", sep=""), quote=F, row.names=F, col.names=F, sep="\t")
