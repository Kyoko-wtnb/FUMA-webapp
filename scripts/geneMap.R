##### load libraries #####
library(data.table)
library(kimisc)
library(GenomicRanges)

start_time <- Sys.time()

##### get commnad line arguments #####
args <- commandArgs(TRUE)
filedir <- args[1]
if(grepl("\\/$", filedir)==F){
	filedir <- paste0(filedir, '/')
}

##### get config parameters #####
curfile <- whereami::thisfile()
source(paste0(dirname(curfile), '/ConfigParser.R'))
config <- ConfigParser(file=paste0(dirname(curfile),'/app.config'))

params <- ConfigParser(file=paste0(filedir, 'params.config'))

genetype <- params$params$genetype
exMHC <- as.numeric(params$params$exMHC)
extMHC <- params$params$extMHC
posMap <- as.numeric(params$posMap$posMap)
posMapWindowSize <- params$posMap$posMapWindowSize
posMapAnnot <- params$posMap$posMapAnnot
posMapCADDth <- as.numeric(params$posMap$posMapCADDth)
posMapRDBth <- params$posMap$posMapRDBth
posMapChr15 <- params$posMap$posMapChr15
posMapChr15Max <- as.numeric(params$posMap$posMapChr15Max) # gives a warning
posMapChr15Meth <- params$posMap$posMapChr15Meth
eqtlMap <- as.numeric(params$eqtlMap$eqtlMap)
eqtlMaptss <- params$eqtlMap$eqtlMaptss
eqtlMapSigeqtl <- as.numeric(params$eqtlMap$eqtlMapSig)
eqtlP <- as.numeric(params$eqtlMap$eqtlMapP)
eqtlMapCADDth <- as.numeric(params$eqtlMap$eqtlMapCADDth)
eqtlMapRDBth <- params$eqtlMap$eqtlMapRDBth
eqtlMapChr15 <- params$eqtlMap$eqtlMapChr15
eqtlMapChr15Max <- as.numeric(params$eqtlMap$eqtlMapChr15Max) # gives a warning
eqtlMapChr15Meth <- params$eqtlMap$eqtlMapChr15Meth
if("posMapAnnoDs" %in% names(params$posMap)){
	posMapAnnoDs <- params$posMap$posMapAnnoDs
	posMapAnnoMeth <- params$posMap$posMapAnnoMeth
}else{
	posMapAnnoDs <- "NA"
}
if("eqtlMapAnnoDs" %in% names(params$eqtlMap)){
	eqtlMapAnnoDs <- params$eqtlMap$eqtlMapAnnoDs
	eqtlMapAnnoMeth <- params$eqtlMap$eqtlMapAnnoMeth
}else{
	eqtlMapAnnoDs <- "NA"
}

if("ciMap" %in% names(params)){
	ciMap <- as.numeric(params$ciMap$ciMap)
	ciMapEnhFilt <- as.numeric(params$ciMap$ciMapEnhFilt)
	ciMapPromFilt <- as.numeric(params$ciMap$ciMapPromFilt)
	ciMapCADDth <- as.numeric(params$ciMap$ciMapCADDth)
	ciMapRDBth <- params$ciMap$ciMapRDBth
	ciMapChr15 <- params$ciMap$ciMapChr15
	ciMapChr15Max <- as.numeric(params$ciMap$ciMapChr15Max) # gives a warning
	ciMapChr15Meth <- params$ciMap$ciMapChr15Meth
	if("ciMapAnnoDs" %in% names(params$ciMap)){
		ciMapAnnoDs <- params$ciMap$ciMapAnnoDs
		ciMapAnnoMeth <- params$ciMap$ciMapAnnoMeth
	}
}else{
	ciMap <- 0
}


if(posMapWindowSize=="NA"){
	posMapWindowSize <- NA
}else{
	posMapWindowSize <- as.numeric(posMapWindowSize)*1000
}

##### load ENSG genes #####
ENSG <- fread(paste(config$data$ENSG, params$params$ensembl, config$data$ENSGfile, sep="/"), data.table=F)

if(genetype!="all"){
	genetype <- unique(unlist(strsplit(genetype, ":")))
	ENSG <- ENSG[ENSG$gene_biotype %in% genetype,]
}
if(exMHC==1){
	start <- ENSG$start_position[ENSG$external_gene_name=="MOG"]
	end <- ENSG$end_position[ENSG$external_gene_name=="COL11A2"]
	if(extMHC!="NA"){
		extMHC <- as.numeric(unlist(strsplit(extMHC, "-")))
		if(extMHC[1]<start){start<-extMHC[1]}
		if(extMHC[2]>end){end <- extMHC[2]}
	}
	ENSG <- ENSG[!(ENSG$ensembl_gene_id%in%ENSG$ensembl_gene_id[ENSG$chromosome_name==6 & ((ENSG$end_position>=start&ENSG$end_position<=end)|(ENSG$start_position>=start&ENSG$start_position<=end))]),]
}

ENSG$chromosome_name[ENSG$chromosome_name=="X"] <- 23
ENSG$chromosome_name <- as.numeric(ENSG$chromosome_name)

##### read files #####
snps <- fread(paste0(filedir, "snps.txt"), data.table=F)
snps$posMapFilt <- 0
snps$eqtlMapFilt <- 0
snps$ciMapFilt <- 0
annot <- fread(paste0(filedir, "annot.txt"), data.table=F)
ld <- fread(paste0(filedir, "ld.txt"), data.table=F)
genes <- c()

##### positional mapping #####
if(posMap==1){
	print("Performing posMap...")
	tmp_snps <- snps
	if(posMapCADDth>0){
		tmp_snps <- tmp_snps[which(tmp_snps$CADD>=posMapCADDth),]
	}
	if(posMapRDBth!="NA"){
		tmp_snps <- tmp_snps[which(tmp_snps$RDB<=posMapRDBth),]
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
		tmp_snps <- tmp_snps[tmp_snps$uniqID %in% epi$uniqID[epi$epi<=posMapChr15Max],]
		rm(epi, temp)
	}
	if(posMapAnnoDs!="NA" & posMapAnnoMeth!="NA"){
		ds <- sub(".bed.gz", "", gsub("/", "_", unlist(strsplit(posMapAnnoDs, ":"))))
		tmp_annot <- annot[match(tmp_snps$uniqID, annot$uniqID),ds]
		#if there is more than 1 chosen annotation then use apply
		if(class(tmp_annot) == "data.frame"){
			if(posMapAnnoMeth=="any"){
				tmp_snps <- tmp_snps[which(apply(tmp_annot, 1, sum)>0),]
			}else if(posMapAnnoMeth=="majority"){
				tmp_snps <- tmp_snps[which(apply(tmp_annot, 1, sum)/length(ds)>0.5),]
			}else{
				tmp_snps <- tmp_snps[which(apply(tmp_annot, 1, sum)==length(ds)),]
			}
		}else{
			if(posMapAnnoMeth=="any"){
				tmp_snps <- tmp_snps[which(tmp_annot>0),]
			}else if(posMapAnnoMeth=="majority"){
				tmp_snps <- tmp_snps[which(tmp_annot/length(ds)>0.5),]
			}else{
				tmp_snps <- tmp_snps[which(tmp_annot==length(ds)),]
			}
		}
	}
	if(is.na(posMapWindowSize)){
		annov <- fread(paste0(filedir, "annov.txt"), data.table=F)
		annov <- annov[annov$gene %in% ENSG$ensembl_gene_id & annov$uniqID %in% tmp_snps$uniqID,]
		posMapAnnot <- unique(unlist(strsplit(posMapAnnot, ":")))
		annov <- annov[grepl(paste(posMapAnnot, collapse="|"), annov$annot),]
		snps$posMapFilt[snps$uniqID %in% annov$uniqID] <- 1
	}else{
		snps_gr <- with(tmp_snps, GRanges(seqnames=chr, IRanges(start=pos, end=pos)))
		genes_gr <- with(ENSG, GRanges(seqnames=chromosome_name, IRanges(start=start_position-posMapWindowSize, end=end_position+posMapWindowSize)))
		overlap <- findOverlaps(snps_gr, genes_gr)
		annov <- data.frame(uniqID=tmp_snps$uniqID[queryHits(overlap)], gene=ENSG$ensembl_gene_id[subjectHits(overlap)], stringsAsFactors=F)
		snps$posMapFilt[snps$uniqID %in% annov$uniqID] <- 1
	}
	genes <- c(genes, unique(annov$gene))
	annov <- unique(annov)
}

##### eqtl Mapping #####
if(eqtlMap==1){
	print("Performing eqtlMap...")
	eqtl <- fread(paste0(filedir, "eqtl.txt"), data.table=F)
	if(nrow(eqtl)>0){
		eqtl <- eqtl[eqtl$gene %in% ENSG$ensembl_gene_id,]
		eqtl$chr <- snps$chr[match(eqtl$uniqID, snps$uniqID)]
		eqtl$pos <- snps$pos[match(eqtl$uniqID, snps$uniqID)]
		eqtl$symbol <- ENSG$external_gene_name[match(eqtl$gene, ENSG$ensembl_gene_id)]
		eqtlall <- eqtl
		if(nrow(eqtlall)>0){
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
				eqtl <- eqtl[eqtl$uniqID %in% epi$uniqID[epi$epi<=eqtlMapChr15Max],]
				rm(epi, temp)
			}
			if(eqtlMapAnnoDs!="NA" & eqtlMapAnnoMeth!="NA"){
				ds <- sub(".bed.gz", "", gsub("/", "_", unlist(strsplit(eqtlMapAnnoDs, ":"))))
				tmp_annot <- annot[annot$uniqID %in% eqtl$uniqID, c("uniqID", ds)]
				if(eqtlMapAnnoMeth=="any"){
					eqtl <- eqtl[eqtl$uniqID %in% tmp_annot$uniqID[which(apply(tmp_annot[,-1], 1, sum)>0)],]
				}else if(eqtlMapAnnoMeth=="majority"){
					eqtl <- eqtl[eqtl$uniqID %in% tmp_annot$uniqID[which(apply(tmp_annot[,-1], 1, sum)/length(ds)>0.5)],]
				}else{
					eqtl <- eqtl[eqtl$uniqID %in% tmp_annot$uniqID[which(apply(tmp_annot[,-1], 1, sum)==length(ds))],]
				}
			}
			genes <- c(genes, unique(eqtl$gene))
			eqtlall$eqtlMapFilt[eqtlall$uniqID %in% eqtl$uniqID] <- 1
		}
		write.table(eqtlall, paste0(filedir, "eqtl.txt"), quote=F, row.names=F, sep="\t")
		rm(eqtlall)
		snps$eqtlMapFilt[snps$uniqID %in% eqtl$uniqID] <- 1
	}
}

##### chromatin itneraction mapipng #####
if(ciMap==1){
	print("Performing ciMap...")
	ci <- fread(paste0(filedir, "ci.txt"), data.table=F)
	cisnps <- c()
	if(nrow(ci)>0){
		ci$ciMapFilt <- 0
		ci$tmpid <- 1:nrow(ci)
		ciall <- ci
		cisnps <- unique(unlist(strsplit(ci$SNPs, ";")))
		cisnps <- snps[snps$rsID %in% cisnps,]

		if(ciMapEnhFilt==1){
			cienh <- fread(paste0(filedir, "ciSNPs.txt"), data.table=F)
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
			cisnps <- cisnps[cisnps$uniqID %in% epi$uniqID[epi$epi<=ciMapChr15Max],]
			rm(epi, temp)
		}
		if(ciMapAnnoDs!="NA" & ciMapAnnoMeth!="NA"){
			ds <- sub(".bed.gz", "", gsub("/", "_", unlist(strsplit(ciMapAnnoDs, ":"))))
			tmp_annot <- annot[annot$uniqID %in% cisnps$uniqID,c("uniqID", ds)]
			if(ciMapAnnoMeth=="any"){
				cisnps <- cisnps[cisnps$uniqID %in% tmp_annot$uniqID[which(apply(tmp_annot[,-1], 1, sum)>0)],]
			}else if(ciMapAnnoMeth=="majority"){
				cisnps <- cisnps[cisnps$uniqID %in% tmp_annot$uniqID[which(apply(tmp_annot[,-1], 1, sum)/length(ds)>0.5)],]
			}else{
				cisnps <- cisnps[cisnps$uniqID %in% tmp_annot$uniqID[which(apply(tmp_annot[,-1], 1, sum)==length(ds))],]
			}
		}
		ci_reg1 <- data.frame(t(matrix(as.numeric(unlist(strsplit(unlist(strsplit(ci$region1, ":")), "-"))), nrow=3)))
		colnames(ci_reg1) <- c("chr", "start", "end")
		ci_reg1_gr <- with(ci_reg1, GRanges(seqnames=chr, IRanges(start=start, end=end)))
		snps_gr <- with(cisnps, GRanges(seqname=chr, IRanges(start=pos, end=pos)))
		overlap <- findOverlaps(ci_reg1_gr, snps_gr)
		ci <- ci[sort(unique(queryHits(overlap))),]
		if(ciMapPromFilt==1){
			ciprom <- fread(paste0(filedir, "ciProm.txt"), data.table=F)
			ci <- ci[ci$region2 %in% ciprom$region2,]
			ciprom <- ciprom[ciprom$region2 %in% ci$region2,]

			ci$genes <- sapply(ci$region2, function(x){paste(unique(ciprom$genes[!is.na(ciprom$genes) & ciprom$region2==x]), collapse=":")})
			ci$genes[ci$genes==""] <- NA
		}
		ci <- ci[!is.na(ci$genes),]
		if(nrow(ci)>0){
			genes <- c(genes, unique(unlist(strsplit(ci$genes, ":"))))
			cisnps <- cisnps[cisnps$rsID %in% unique(unlist(strsplit(ci$SNPs, ";"))),]
			tmp_ci <- unique(ci[,c("region1", "genes")])
			tmp_ci <- unique(do.call(rbind,apply(tmp_ci, 1, function(x){data.frame(region1=x[1], gene=unlist(strsplit(x[2], ":")))})))
			tmp_ci$region1 <- as.character(tmp_ci$region1)
			tmp_ci$gene <- as.character(tmp_ci$gene)
			ci_reg1 <- data.frame(t(matrix(as.numeric(unlist(strsplit(unlist(strsplit(tmp_ci$region1, ":")), "-"))), nrow=3)))
			colnames(ci_reg1) <- c("chr", "start", "end")
			ci_reg1_gr <- with(ci_reg1, GRanges(seqnames=chr, IRanges(start=start, end=end)))
			snps_gr <- with(cisnps, GRanges(seqname=chr, IRanges(start=pos, end=pos)))
			overlap <- findOverlaps(ci_reg1_gr, snps_gr)
			ci_snps_genes <- unique(data.frame(gene=tmp_ci$gene[queryHits(overlap)], uniqID=cisnps$uniqID[subjectHits(overlap)], stringsAsFactors=F))
		}
		snps$ciMapFilt[snps$uniqID %in% cisnps$uniqID] <- 1
		ciall$ciMapFilt[ciall$tmpid %in% ci$tmpid] <- 1
		write.table(ciall[,1:(ncol(ciall)-1)], paste0(filedir, "ci.txt"), quote=F, row.names=F, sep="\t")
	}
}

write.table(snps, paste0(filedir, "snps.txt"), quote=F, row.names=F, sep="\t")

##### create gene table #####
print("Creating a gene table...")
cols <- c("ensembl_gene_id", "external_gene_name", "chromosome_name",
		"start_position", "end_position", "strand", "gene_biotype",
		"entrezID", "hgnc_symbol")
geneTable <- ENSG[ENSG$ensembl_gene_id %in% genes, cols]
colnames(geneTable) <- c("ensg", "symbol", "chr", "start", "end", "strand", "type", "entrezID", "HUGO")
if(nrow(geneTable)>0){
	geneTable$chr <- as.numeric(ifelse(geneTable$chr=="X", "23", geneTable$chr))
	geneTable <- geneTable[order(geneTable$start),]
	geneTable <- geneTable[order(geneTable$chr),]

	### gene scores
	gene_scores <- fread(paste0(config$data$geneScores, "/gene_scores.txt"), data.table=F)
	geneTable <- cbind(geneTable, gene_scores[match(geneTable$ensg, gene_scores$ensg), 5:ncol(gene_scores)])

	if(posMap==1){
		geneTable$posMapSNPs <- 0
		geneTable$posMapMaxCADD  <- 0
		if(nrow(annov)>0){
			tmp <- data.frame(table(annov$gene))
			geneTable$posMapSNPs <- tmp$Freq[match(geneTable$ensg, tmp$Var1)]
			geneTable$posMapSNPs[is.na(geneTable$posMapSNPs)] <- 0
			annov$CADD <- annot$CADD[match(annov$uniqID, annot$uniqID)]
			if(length(which(!is.na(annov$CADD)))>0){
				tmp <- with(annov[!is.na(annov$CADD),], aggregate(CADD, list(gene), max))
				geneTable$posMapMaxCADD <- tmp$x[match(geneTable$ensg, tmp$Group.1)]
				geneTable$posMapMaxCADD[is.na(geneTable$posMapMaxCADD)] <- 0
			}
		}
	}
	if(eqtlMap==1){
		geneTable$eqtlMapSNPs <- 0
		geneTable$eqtlMapminP <- NA
		geneTable$eqtlMapminQ <- NA
		geneTable$eqtlMapts <- NA
		geneTable$eqtlDirection <- NA
		if(nrow(eqtl)>0){
			tmp <- unique(eqtl[,c("uniqID", "gene")])
			tmp <- data.frame(table(tmp$gene))
			geneTable$eqtlMapSNPs <- tmp$Freq[match(geneTable$ensg, tmp$Var1)]
			geneTable$eqtlMapSNPs[is.na(geneTable$eqtlMapSNPs)] <- 0
			if(length(which(!is.na(eqtl$p)))>0){
				tmp <- with(eqtl[!is.na(eqtl$p),], aggregate(p, list(gene), min))
				geneTable$eqtlMapminP <- tmp$x[match(geneTable$ensg, tmp$Group.1)]
			}
			if(length(which(!is.na(eqtl$FDR)))>0){
				tmp <- with(eqtl[!is.na(eqtl$FDR),], aggregate(FDR, list(gene), min))
				geneTable$eqtlMapminQ <- tmp$x[match(geneTable$ensg, tmp$Group.1)]
			}
			tmp <- unique(eqtl[,c("db", "tissue", "gene")])
			tmp$tissue <- sub(".txt.gz","",tmp$tissue)
			tmp$tissue <- apply(tmp[,c("db", "tissue")], 1, function(x){if(grepl(x[1], x[2])){x[2]}else{paste(x[1], x[2], sep="/")}})
			tmp <- with(tmp, aggregate(tissue, list(gene), paste, collapse=":"))
			geneTable$eqtlMapts <- tmp$x[match(geneTable$ensg, tmp$Group.1)]
			if(length(which(!is.na(eqtl$alignedDirection)))>0){
				tmp <- with(eqtl[!is.na(eqtl$alignedDirection),], aggregate(alignedDirection, list(gene), function(x){names(sort(table(x), decreasing=T))[1]}))
				geneTable$eqtlDirection <- tmp$x[match(geneTable$ensg, tmp$Group.1)]
			}
		}
	}
	if(ciMap==1){
		geneTable$ciMap <- "No"
		geneTable$ciMapts <- NA
		if(nrow(ci)>0){
			geneTable$ciMap[geneTable$ensg %in% unlist(strsplit(ci$genes,":"))] <- "Yes"
			tmp <- unique(ci[,c("tissue/cell", "genes")])
			tmp <- unique(do.call(rbind,apply(tmp, 1, function(x){data.frame(tissue=x[1], gene=unlist(strsplit(x[2], ":")))})))
			tmp$tissue <- as.character(tmp$tissue)
			tmp <- with(tmp, aggregate(tissue, list(gene), paste, collapse=":"))
			geneTable$ciMapts <- tmp$x[match(geneTable$ensg, tmp$Group.1)]
		}
	}
	ld <- ld[ld$SNP2 %in% snps$uniqID[which(snps$posMapFilt==1 | snps$eqtlMapFilt==1 | snps$ciMapFilt==1)],]
	ld$SNP1 <- snps$rsID[match(ld$SNP1, snps$uniqID)]
	dup <- unique(ld$SNP2[duplicated(ld$SNP2)])
	isSNPs <- ld[!ld$SNP2%in%dup,2:1]
	colnames(isSNPs) <- c("uniqID", "IndSigSNPs")
	if(length(dup)>0){
		tmp <- with(ld[ld$SNP2%in%dup,], aggregate(SNP1, list(SNP2), paste, collapse=";"))
		colnames(tmp) <- c("uniqID", "IndSigSNPs")
		isSNPs <- rbind(isSNPs, tmp)
	}

	geneTable$minGwasP <- NA
	geneTable$IndSigSNPs <- NA
	tmp_table <- data.frame(ensg=geneTable$ensg, posMapP=NA, posMapISNPs=NA, posMapLocus=NA,
		eqtlMapP=NA, eqtlMapISNPs=NA, eqtlMapLocus=NA,
		ciMapP=NA, ciMapISNPs=NA, ciMapLocus=NA, stringsAsFactors=F)
	if(posMap==1){
		if(nrow(annov)>0){
			annov$gwasP <- snps$gwasP[match(annov$uniqID, snps$uniqID)]
			if(length(which(!is.na(annov$gwasP)))>0){
				tmp <- with(annov[!is.na(annov$gwasP),], aggregate(as.numeric(gwasP), list(gene), min))
				tmp_table$posMapP <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
			}
			annov$IndSigSNPs <- isSNPs$IndSigSNPs[match(annov$uniqID, isSNPs$uniqID)]
			tmp <- unique(annov[,c("gene", "IndSigSNPs")])
			tmp <- with(unique(annov[!is.na(annov$IndSigSNPs),c("gene", "IndSigSNPs")]), aggregate(IndSigSNPs, list(gene), paste, collapse=";"))
			tmp_table$posMapISNPs <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
			annov$GenomicLocus <- snps$GenomicLocus[match(annov$uniqID, snps$uniqID)]
			tmp <- with(unique(annov[,c("gene", "GenomicLocus")]), aggregate(GenomicLocus, list(gene), paste, collapse=":"))
			tmp_table$posMapLocus <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
		}
	}
	if(eqtlMap==1){
		if(nrow(eqtl)>0){
			tmp_eqtl <- unique(eqtl[,c("gene", "uniqID")])
			tmp_eqtl$gwasP <- snps$gwasP[match(tmp_eqtl$uniqID, snps$uniqID)]
			if(length(which(!is.na(tmp_eqtl$gwasP)))>0){
				tmp <- with(tmp_eqtl[!is.na(tmp_eqtl$gwasP),], aggregate(as.numeric(gwasP), list(gene), min))
				tmp_table$eqtlMapP <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
			}
			tmp_eqtl$IndSigSNPs <- isSNPs$IndSigSNPs[match(tmp_eqtl$uniqID, isSNPs$uniqID)]
			tmp <- with(unique(tmp_eqtl[!is.na(tmp_eqtl$IndSigSNPs), c("gene", "IndSigSNPs")]), aggregate(IndSigSNPs, list(gene), paste, collapse=";"))
			tmp_table$eqtlMapISNPs <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
			tmp_eqtl$GenomicLocus <- snps$GenomicLocus[match(tmp_eqtl$uniqID, snps$uniqID)]
			tmp <- with(unique(tmp_eqtl[,c("gene", "GenomicLocus")]), aggregate(GenomicLocus, list(gene), paste, collapse=":"))
			tmp_table$eqtlMapLocus <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
		}
	}
	if(ciMap==1){
		if(nrow(ci)>0){
			ci_snps_genes$gwasP <- snps$gwasP[match(ci_snps_genes$uniqID, snps$uniqID)]
			if(length(which(!is.na(ci_snps_genes$gwasP)))>0){
				tmp <- with(ci_snps_genes[!is.na(ci_snps_genes$gwasP),], aggregate(as.numeric(gwasP), list(gene), min))
				tmp_table$ciMapP <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
			}
			ci_snps_genes$IndSigSNPs <- isSNPs$IndSigSNPs[match(ci_snps_genes$uniqID, isSNPs$uniqID)]
			tmp <- with(unique(ci_snps_genes[,c("gene", "IndSigSNPs")]), aggregate(IndSigSNPs, list(gene), paste, collapse=":"))
			tmp_table$ciMapISNPs <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
			ci_snps_genes$GenomicLocus <- snps$GenomicLocus[match(ci_snps_genes$uniqID, snps$uniqID)]
			tmp <- with(unique(ci_snps_genes[,c("gene", "GenomicLocus")]), aggregate(GenomicLocus, list(gene), paste, collapse=":"))
			tmp_table$ciMapLocus <- tmp$x[match(tmp_table$ensg, tmp$Group.1)]
		}
	}
	geneTable$minGwasP <- apply(tmp_table[,c("posMapP", "eqtlMapP", "ciMapP")], 1, min, na.rm=T)
	geneTable$minGwasP[which(geneTable$minGwasP<0 | geneTable$minGwasP>1)] <- NA
	geneTable$IndSigSNPs <- apply(tmp_table[,c("posMapISNPs", "eqtlMapISNPs", "ciMapISNPs")], 1, function(x){paste(unique(unlist(strsplit(x[!is.na(x)], ";"))), collapse=";")})
	geneTable$GenomicLocus <- apply(tmp_table[,c("posMapLocus", "eqtlMapLocus", "ciMapLocus")], 1, function(x){paste(sort(as.numeric(unique(unlist(strsplit(x, ":"))))), collapse=":")})
}

geneTable <- geneTable[order(geneTable$start),]
geneTable <- geneTable[order(geneTable$chr),]
write.table(geneTable, paste0(filedir, "genes.txt"), quote=F, row.names=F, sep="\t")

##### output summary results #####
print("Creating summary files...")
summary <- data.frame(matrix(nrow=6, ncol=2))
summary[,1] <- c("#Genomic risk loci", "#lead SNPs", "#Ind. Sig. SNPs",  "#candidate SNPs", "#candidate GWAS tagged SNPs", "#mapped genes")
indS <- fread(paste0(filedir, "IndSigSNPs.txt"), data.table=F)
loci <- fread(paste0(filedir, "GenomicRiskLoci.txt"), data.table=F)
leadS <- fread(paste0(filedir, "leadSNPs.txt"), data.table=F)
summary[1,2] <- nrow(loci)
summary[2,2] <- nrow(leadS)
summary[3,2] <- nrow(indS)
summary[4,2] <- nrow(snps)
summary[5,2] <- length(which(!is.na(snps$gwasP)))
summary[6,2] <- nrow(geneTable)
write.table(summary, paste0(filedir, "summary.txt"), quote=F, row.names=F, col.names=F, sep="\t")

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
	int.table$nWithinGene[i] <- length(which(ENSG$chromosome_name==loci$chr[i] & (
		(ENSG$start_position<=loci$start[i] & ENSG$end_position>=loci$end[i])
		| (ENSG$start_position>=loci$start[i] & ENSG$start_position<=loci$end[i])
		| (ENSG$end_position>=loci$start[i] & ENSG$end_position<=loci$end[i])
		| (ENSG$start_position>=loci$start[i] & ENSG$end_position<=loci$end[i])
	)))
}

write.table(int.table, paste0(filedir, "interval_sum.txt"), quote=F, row.names=F, sep="\t")
print(paste0("Run time: ", Sys.time()-start_time))
