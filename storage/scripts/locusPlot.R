library(data.table)
args <- commandArgs(TRUE)
filedir <- args[1]
snps <- fread(paste(filedir, "snps.txt", sep=""), data.table=F)
ld <- fread(paste(filedir, "ld.txt", sep=""), data.table=F)

i <- as.numeric(args[2]);
i <- i+1
type <- args[3]

if(type=="leadSNP"){
  loci.table <- fread(paste(filedir, "leadSNPs.txt",sep=""), data.table=F)
  ls <- loci.table$uniqID[i]
  interval <- loci.table$interval[i]
  ld <- ld[ld$SNP1 == ls,]

  snps <- snps[snps$Interval == interval,]
  snps$logP <- -log10(snps$gwasP)
  snps$logP[is.na(snps$logP)] <- 0
  snps$ld <- 0
  snps$ld[snps$uniqID %in% ld$SNP2] <- 1
  snps$ld[snps$uniqID == ls] <- 2
}else{
  intervals <- fread(paste(filedir, "intervals.txt", sep=""), data.table=F)
  intervals <- data.frame(intervals)
  ls <- snps$uniqID[snps$rsID %in% unlist(strsplit(intervals$leadSNPs[i], ":"))]
  ld <- ld[ld$SNP1 %in% ls,]
  chr <- intervals$chr[i]
  snps <- snps[snps$chr==chr,]
  snps <- snps[snps$uniqID %in% ld$SNP2[ld$SNP1 %in% ls],]
  snps$logP <- -log10(snps$gwasP)
  snps$logP[is.na(snps$logP)] <- 0
  snps$ld <- 1
  snps$ld[snps$uniqID %in% ls] <- 2
}

if("or" %in% colnames(snps) & "se" %in% colnames(snps)){
  snps <- subset(snps, select=c("pos", "gwasP","logP", "ld", "r2", "rsID", "MAF", "or", "se"))
}else if("or" %in% colnames(snps)){
  snps <- subset(snps, select=c("pos", "gwasP","logP", "ld", "r2", "rsID", "MAF", "or"))
}else if("se" %in% colnames(snps)){
  snps <- subset(snps, select=c("pos", "gwasP","logP", "ld", "r2", "rsID", "MAF", "se"))
}else{
  snps <- subset(snps, select=c("pos", "gwasP","logP", "ld", "r2", "rsID", "MAF"))
}
write.table(snps, paste(filedir, "locusPlot.txt", sep=""), quote=F, row.names=F, sep="\t")
