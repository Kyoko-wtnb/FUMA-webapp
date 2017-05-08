library(data.table)
library(kimisc)
args <- commandArgs(TRUE)
filedir <- args[1]
if(grepl("\\/$", filedir)==F){
  filedir <- paste(filedir, '/', sep="")
}

curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

params <- ConfigParser(file=paste(filedir, 'params.config', sep=""))

genetype <- params$params$genetype
exMHC <- as.numeric(params$params$exMHC)
extMHC <- params$params$extMHC
posMap <- as.numeric(params$posMap$posMap)
posMapWindowSize <- params$posMap$posMapWindowSize
posMapAnnot <- params$posMap$posMapAnnot
posMapCADDth <- as.numeric(params$posMap$posMapCADDth)
posMapRDBth <- params$posMap$posMapRDBth
posMapChr15 <- params$posMap$posMapChr15
posMapChr15Max <- as.numeric(params$posMap$posMapChr15Max)
posMapChr15Meth <- params$posMap$posMapChr15Meth
eqtlMap <- as.numeric(params$eqtlMap$eqtlMap)
eqtlMaptss <- params$eqtlMap$eqtlMaptss
eqtlMapSigeqtl <- as.numeric(params$eqtlMap$eqtlMapSig)
eqtlP <- as.numeric(params$eqtlMap$eqtlMapP)
eqtlMapCADDth <- as.numeric(params$eqtlMap$eqtlMapCADDth)
eqtlMapRDBth <- params$eqtlMap$eqtlMapRDBth
eqtlMapChr15 <- params$eqtlMap$eqtlMapChr15
eqtlMapChr15Max <- as.numeric(params$eqtlMap$eqtlMapChr15Max)
eqtlMapChr15Meth <- params$eqtlMap$eqtlMapChr15Meth
if("ciMap" %in% names(params)){
	ciMap <- as.numeric(params$ciMap$ciMap)
	ciMapEnhFilt <- as.numeric(params$ciMap$ciMapEnhFilt)
	ciMapPromFilt <- as.numeric(params$ciMap$ciMapPromFilt)
	ciMapCADDth <- as.numeric(params$ciMap$ciMapCADDth)
	ciMapRDBth <- params$ciMap$ciMapRDBth
	ciMapChr15 <- params$ciMap$ciMapChr15
	ciMapChr15Max <- as.numeric(params$ciMap$ciMapChr15Max)
	ciMapChr15Meth <- params$ciMap$ciMapChr15Meth
}else{
	ciMap <- 0
}


if(posMapWindowSize=="NA"){
  posMapWindowSize <- NA
}else{
  posMapWindowSize <- as.numeric(posMapWindowSize)*1000
}

load(paste(config$data$ENSG, "/ENSG.all.genes.RData", sep=""))

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
snps$posMapFilt <- 0
snps$eqtlMapFilt <- 0
snps$ciMapFilt <- 0
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
    annov <- annov[annov$uniqID %in% annot$uniqID[annot$RDB<=posMapRDBth],]
  }
  if(posMapChr15!="NA"){
    if(grepl("all", posMapChr15)){
      posMapChr15 <- colnames(annot)[4:ncol(annot)]
    }else{
      posMapChr15 <- unique(unlist(strsplit(posMapChr15, ":")))
    }
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
        temp$epi <- apply(temp[,2:ncol(temp)], 1, function(x){as.numeric(names(sort(table(x), decreasing=T))[1])})
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
  if(is.na(posMapWindowSize)){
    posMapAnnot <- unique(unlist(strsplit(posMapAnnot, ":")))
    annov <- annov[grepl(paste(posMapAnnot, collapse="|"), annov$annot),]
    genes <- c(genes, unique(annov$gene))
    snps$posMapFilt[snps$uniqID %in% annov$uniqID] <- 1
  }else{
    annov <- annov[annov$dist <= posMapWindowSize,]
    genes <- c(genes, unique(annov$gene))
    snps$posMapFilt[snps$uniqID %in% annov$uniqID] <- 1
  }
}

if(eqtlMap==1){
  eqtl <- fread(paste(filedir, "eqtl.txt", sep=""), data.table=F)
  if(nrow(eqtl)>0){
  	eqtl <- eqtl[eqtl$gene %in% ENSG.all.genes$ensembl_gene_id,]
    eqtl$chr <- snps$chr[match(eqtl$uniqID, snps$uniqID)]
    eqtl$pos <- snps$pos[match(eqtl$uniqID, snps$uniqID)]
    eqtl$symbol <- ENSG.all.genes$external_gene_name[match(eqtl$gene, ENSG.all.genes$ensembl_gene_id)]
    eqtlall <- eqtl
    eqtlall$eqtlMapFilt <- 0
    if(eqtlMapCADDth>0){
      eqtl <- eqtl[eqtl$uniqID %in% annot$uniqID[annot$CADD>=eqtlMapCADDth],]
    }
    if(eqtlMapRDBth!="NA"){
      eqtl <- eqtl[eqtl$uniqID %in% eqtl$uniqID[annot$RDB<=eqtlMapRDBth],]
    }
    if(eqtlMapChr15!="NA"){
      if(grepl("all", eqtlMapChr15)){
        eqtlMapChr15 <- colnames(annot)[4:ncol(annot)]
      }else{
        eqtlMapChr15 <- unique(unlist(strsplit(eqtlMapChr15, ":")))
      }
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
          temp$epi <- apply(temp[,2:ncol(temp)], 1, function(x){as.numeric(names(sort(table(x), decreasing=T))[1])})
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
      epi <- epi[epi$epi<=eqtlMapChr15Max,]
      eqtl <- eqtl[eqtl$uniqID %in% epi$uniqID,]
      rm(epi, temp)
    }
    genes <- c(genes, unique(eqtl$gene))
    eqtlall$eqtlMapFilt[eqtlall$uniqID %in% eqtl$uniqID] <- 1
    write.table(eqtlall, paste(filedir, "eqtl.txt", sep=""), quote=F, row.names=F, sep="\t")
    snps$eqtlMapFilt <- sapply(snps$uniqID, function(x){if(x %in% eqtlall$uniqID){max(eqtlall$eqtlMapFilt[eqtlall$uniqID==x])}else{0}})
  }
}

if(ciMap==1){
	ci <- fread(paste(filedir, "ci.txt", sep=""), data.table=F)
	cisnps <- unique(unlist(strsplit(ci$SNPs, ":")))
	cisnps <- snps[snps$rsID %in% cisnps,]
	if(ciMapEnhFilt==1){
		cienh <- fread(paste(filedir, "ciSNPs.txt", sep=""), data.table=F)
		cisnps <- cisnps[cisnps$uniqID %in% cienh$uniqID,]
	}
	if(ciMapCADDth>0){
		cisnps <- cisnps[cisnps$uniqID %in% annot$uniqID[annot$CADD >= ciMapCADDth],]
	}
	if(ciMapRDBth!="NA"){
		cisnps <- cisnps[cisnps$uniqID %in% annot$uniqID[annot$RDB <= ciMapRDBth],]
	}
	if(ciMapChr15!="NA"){
      if(grepl("all", ciMapChr15)){
        ciMapChr15 <- colnames(annot)[4:ncol(annot)]
      }else{
        ciMapChr15 <- unique(unlist(strsplit(ciMapChr15, ":")))
      }
      epi <- data.frame(uniqID=snps$uniqID, epi=NA)
      temp <- subset(annot, select=c("uniqID", ciMapChr15))
      if(ciMapChr15Meth=="any"){
        if(length(ciMapChr15)>1){
          temp$epi <- apply(temp[,2:ncol(temp)], 1, min)
        }else{
          temp$epi <- temp[,2]
        }
      }else if(ciMapChr15Meth=="majority"){
        if(length(ciMapChr15)>1){
          temp$epi <- apply(temp[,2:ncol(temp)], 1, function(x){as.numeric(names(sort(table(x), decreasing=T))[1])})
        }else{
          temp$epi <- temp[,2]
        }
      }else{
        if(length(ciMapChr15)>1){
          temp$epi <- apply(temp[,2:ncol(temp)], 1, max)
        }else{
          temp$epi <- temp[,2]
        }
      }
      epi$epi <- temp$epi[match(epi$uniqID, temp$uniqID)]
      epi <- epi[epi$epi<=ciMapChr15Max,]
      cisnps <- cisnps[cisnps$uniqID %in% epi$uniqID,]
      rm(epi, temp)
    }
	cicheck <- sapply(ci$SNPs, function(x){if(length(which(unlist(strsplit(x, ":")) %in% cisnps$rsID))>0){1}else{0}})
	ci <- ci[cicheck==1,]
	if(ciMapPromFilt==1){
		ciprom <- fread(paste(filedir, "ciProm.txt", sep=""), data.table=F)
		ci <- ci[ci$region2 %in% ciprom$region2,]
	}
	ci <- ci[!is.na(ci$genes),]
	genes <- c(genes, unique(unlist(strsplit(ci$genes, ":"))))
	cisnps <- cisnps[cisnps$rsID %in% unique(unlist(strsplit(ci$SNPs, ":"))),]
	snps$ciMapFilt[snps$uniqID %in% cisnps$uniqID] <- 1
}

write.table(snps, paste(filedir, "snps.txt", sep=""), quote=F, row.names=F, sep="\t")

geneTable <- ENSG.all.genes[ENSG.all.genes$ensembl_gene_id %in% genes,]
colnames(geneTable) <- c("ensg", "symbol", "chr", "start", "end", "strand", "status", "type", "entrezID","HUGO")
if(nrow(geneTable)>0){
  geneTable$chr <- as.numeric(geneTable$chr)

  pli <- fread(paste(config$data$geneIDs, "/pLI_exac.txt", sep=""), data.table=F)
  geneTable$pLI <- pli$pLI[match(geneTable$ensg, pli$ensg)]

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
  if(ciMap==1){
  	geneTable$ciMap <- "No"
	geneTable$ciMap[geneTable$ensg %in% unlist(strsplit(ci$genes,":"))] <- "Yes"
  }
  geneTable$minGwasP <- NA
  geneTable$IndSigSNPs <- NA
  if(posMap==1){
    geneTable$minGwasP <- sapply(geneTable$ensg, function(x){
		if(length(which(!is.na(snps$gwasP) & snps$uniqID %in% annov$uniqID[which(annov$gene==x)]))>0){
			min(snps$gwasP[!is.na(snps$gwasP) & snps$uniqID %in% annov$uniqID[annov$gene==x]])
		}else{NA}
	})
    geneTable$IndSigSNPs <- sapply(geneTable$ensg, function(x){
		if(length(which(snps$uniqID %in% annov$uniqID[which(annov$gene==x)]))>0){
			paste(unique(snps$rsID[snps$uniqID %in% ld$SNP1[ld$SNP2 %in% annov$uniqID[which(annov$gene==x)]]]), collapse = ":")
		}else{NA}
	})
  }
  if(eqtlMap==1){
    geneTable$minGwasP <- sapply(geneTable$ensg, function(x){
		if(length(which(!is.na(snps$gwasP) & snps$uniqID %in% eqtl$uniqID[eqtl$gene==x]))>0){
			min(c(snps$gwasP[!is.na(snps$gwasP) & snps$uniqID %in% eqtl$uniqID[eqtl$gene==x]], geneTable$minGwasP[geneTable$ensg==x]), na.rm=T)
		}else{geneTable$minGwasP[geneTable$ensg==x]}
	})
    geneTable$IndSigSNPs <- sapply(geneTable$ensg, function(x){
		if(length(which(snps$uniqID %in% eqtl$uniqID[eqtl$gene==x]))>0){
			if(is.na(geneTable$IndSigSNPs[geneTable$ensg==x])){paste(unique(snps$rsID[snps$uniqID %in% ld$SNP1[ld$SNP2 %in% eqtl$uniqID[eqtl$gene==x]]]), collapse = ":")}
			else{paste(unique(c(snps$rsID[snps$uniqID %in% ld$SNP1[ld$SNP2 %in% eqtl$uniqID[eqtl$gene==x]]], unlist(strsplit(geneTable$IndSigSNPs[geneTable$ensg==x], ":")))), collapse = ":")}
		}else{
			if(is.na(geneTable$IndSigSNPs[geneTable$ensg==x])){NA}
			else{geneTable$IndSigSNPs[geneTable$ensg==x]}
		}
	})
  }
  if(ciMap==1){
  	geneTable$minGwasP <- sapply(geneTable$ensg, function(x){
		tmp <- unique(unlist(strsplit(ci$SNPs[grepl(x, ci$genes)], ":")))
		if(length(which(!is.na(cisnps$gwasP[cisnps$rsID %in% tmp])))>0){
			min(c(cisnps$gwasP[!is.na(cisnps$gwasP) & cisnps$rsID %in% tmp], geneTable$minGwasP[geneTable$ensg==x]), na.rm=T)
		}else{geneTable$minGwasP[geneTable$ensg==x]}
	})
	geneTable$IndSigSNPs <- sapply(geneTable$ensg, function(x){
		tmp <- unique(unlist(strsplit(ci$SNPs[grepl(x, ci$genes)], ":")))
		if(length(tmp)==0){
			geneTable$IndSigSNPs[geneTable$ensg==x]
		}else if(is.na(geneTable$IndSigSNPs[geneTable$ensg==x])){
			paste(unique(snps$rsID[snps$uniqID %in% ld$SNP1[ld$SNP2 %in% cisnps$uniqID[cisnps$rsID %in% tmp]]]), collapse=":")
		}else{
			paste(unique(c(snps$rsID[snps$uniqID %in% ld$SNP1[ld$SNP2 %in% cisnps$uniqID[cisnps$rsID %in% tmp]]], unique(unlist(strsplit(geneTable$IndSigSNPs[geneTable$ensg==x], ":"))))), collapse=":")
		}
	})
  }

  leadS <- fread(paste(filedir, "IndSigSNPs.txt", sep=""), data.table=F)
  geneTable$GenomicLocus <- NA
  for(i in 1:nrow(geneTable)){
    ls <- unlist(strsplit(geneTable$IndSigSNPs[i], ":"))
    geneTable$GenomicLocus[i] <- paste(unique(leadS$GenomicLocus[leadS$rsID %in% ls]), collapse=":")
  }
}

write.table(geneTable, paste(filedir, "genes.txt", sep=""), quote=F, row.names=F, sep="\t")

summary <- data.frame(matrix(nrow=6, ncol=2))
summary[,1] <- c("#Genomic risk loci", "#lead SNPs", "#Ind. Sig. SNPs",  "#candidate SNPs", "#candidate GWAS tagged SNPs", "#mapped genes")
indS <- fread(paste(filedir, "IndSigSNPs.txt", sep=""), data.table=F)
loci <- fread(paste(filedir, "GenomicRiskLoci.txt", sep=""), data.table=F)
leadS <- fread(paste(filedir, "leadSNPs.txt", sep=""), data.table=F)
summary[1,2] <- nrow(loci)
summary[2,2] <- nrow(leadS)
summary[3,2] <- nrow(indS)
summary[4,2] <- nrow(snps)
summary[5,2] <- length(which(!is.na(snps$gwasP)))
summary[6,2] <- nrow(geneTable)
write.table(summary, paste(filedir, "summary.txt", sep=""), quote=F, row.names=F, col.names=F, sep="\t")

int.table <- data.frame(GenomicLocus=loci$GenomicLocus, label=NA, nSNPs=NA, size=NA, nGenes=NA, nWithinGene=NA)
int.table$label <- paste(loci$chr, paste(loci$start, loci$end, sep="-"), sep=":")
temp <- table(snps$GenomicLocus)
temp <- data.frame(name=names(temp), n=as.numeric(temp))
int.table$nSNPs <- temp$n[match(int.table$GenomicLocus, temp$name)]
int.table$nSNPs[is.na(int.table$nSNPs)] <- 0
int.table$size <- loci$end - loci$start
if(nrow(geneTable)>0){
  temp <- table(unlist(strsplit(geneTable$GenomicLocus, ":")))
  temp <- data.frame(name=names(temp), n=as.numeric(temp))
  int.table$nGenes <- temp$n[match(int.table$GenomicLocus, temp$name)]
}

int.table$nGenes[is.na(int.table$nGenes)] <- 0
for(i in 1:nrow(int.table)){
  int.table$nWithinGene[i] <- length(which(ENSG.all.genes$chromosome_name==loci$chr[i] & (
    (ENSG.all.genes$start_position<=loci$start[i] & ENSG.all.genes$end_position>=loci$end[i])
    | (ENSG.all.genes$start_position>=loci$start[i] & ENSG.all.genes$start_position<=loci$end[i])
    | (ENSG.all.genes$end_position>=loci$start[i] & ENSG.all.genes$end_position<=loci$end[i])
    | (ENSG.all.genes$start_position>=loci$start[i] & ENSG.all.genes$end_position<=loci$end[i])
  )))
}

write.table(int.table, paste(filedir, "interval_sum.txt", sep=""), quote=F, row.names=F, sep="\t")
