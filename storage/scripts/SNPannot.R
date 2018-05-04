library(data.table)
library(kimisc)
library(GenomicRanges)
args <- commandArgs(TRUE)
filedir <- args[1]

curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))
params <- ConfigParser(file=paste0(filedir, "params.config"))

snps <- fread(paste(filedir, "snps.txt", sep=""), data.table=F)
ld <- fread(paste(filedir, "ld.txt", sep=""), data.table=F)
annov <- fread(paste(filedir, "annov.txt", sep=""), data.table=F)
annot <- fread(paste(filedir, "annot.txt", sep=""), data.table=F)
annot <- annot[annot$uniqID %in% snps$uniqID,]
ENSG <- fread(paste(config$data$ENSG, params$params$ensembl, config$data$ENSGfile, sep="/"), data.table=F)
annov$symbol <- ENSG$external_gene_name[match(annov$gene, ENSG$ensembl_gene_id)]
annov$symbol[is.na(annov$symbol)] <- annov$gene[is.na(annov$symbol)]
annov$chr <- snps$chr[match(annov$uniqID, snps$uniqID)]
annov$pos <- snps$pos[match(annov$uniqID, snps$uniqID)]

genes_gr <- with(ENSG, GRanges(seqnames=chromosome_name, IRanges(start=start_position, end=end_position)))
snps_gr <- with(snps, GRanges(seqname=chr, IRanges(start=pos, end=pos)))
nearest <- distanceToNearest(snps_gr, genes_gr, select="all", ignore.strand=TRUE)
nearest <- as.data.frame(nearest)
snps$nearestGene <- with(nearest, aggregate(subjectHits, list(queryHits), function(x){paste(ENSG$external_gene_name[x], collapse=":")}))$x
snps$dist <- with(nearest, aggregate(distance, list(queryHits), function(x){paste(x, collapse=":")}))$x
tmp <- with(annov, aggregate(annot, list(uniqID), function(x){paste(unique(x), collapse=":")}))
snps$func <- tmp$x[match(snps$uniqID, tmp$Group.1)]
snps$CADD <- annot$CADD[match(snps$uniqID, annot$uniqID)]
snps$RDB <- annot$RDB[match(snps$uniqID, annot$uniqID)]
snps$RDB[snps$RDB==""] <- NA
snps$minChrState <- apply(annot[,4:ncol(annot)], 1, min)
snps$commonChrState <- apply(annot[,4:ncol(annot)], 1, function(x){names(sort(table(x), decreasing=T))[1]})
write.table(snps, paste(filedir, "snps.txt", sep=""), quote=F, row.names=F, sep="\t")
write.table(annov, paste(filedir, "annov.txt", sep=""), quote=F, row.names=F, sep="\t")

annov <- unique(annov[,c(1,3)])
annov.table <- table(annov$annot)
annov.table <- data.frame(annot=names(annov.table), count = as.numeric(annov.table))
write.table(annov.table, paste(filedir, "snpsannot.txt", sep=""), quote=F, row.names=F, sep="\t")
