library(data.table)
library(kimisc)
args <- commandArgs(TRUE)
filedir <- args[1]

curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

snps <- fread(paste(filedir, "snps.txt", sep=""), data.table=F)
ld <- fread(paste(filedir, "ld.txt", sep=""), data.table=F)
annov <- fread(paste(filedir, "annov.txt", sep=""), data.table=F)
annot <- fread(paste(filedir, "annot.txt", sep=""), data.table=F)
annot <- annot[annot$uniqID %in% snps$uniqID,]
ENSG <- fread(config$data$ENSG, data.table=F)
annov$symbol <- ENSG$external_gene_name[match(annov$gene, ENSG$ensembl_gene_id)]
annov$symbol[is.na(annov$symbol)] <- annov$gene[is.na(annov$symbol)]
annov$chr <- snps$chr[match(annov$uniqID, snps$uniqID)]
annov$pos <- snps$pos[match(annov$uniqID, snps$uniqID)]
tmp <- merge(aggregate(dist ~ uniqID, annov, min), annov, by=c("dist", "uniqID"))
tmp2 <- aggregate(symbol ~ uniqID, tmp, paste, collapse=":")
snps$nearestGene <- tmp2$symbol[match(snps$uniqID, tmp2$uniqID)]
tmp2 <- aggregate(dist ~ uniqID, tmp, min)
snps$dist <- tmp2$dist[match(snps$uniqID, tmp2$uniqID)]
tmp2 <- aggregate(annot ~ uniqID, tmp, paste, collapse=":")
snps$func <- tmp2$annot[match(snps$uniqID, tmp2$uniqID)]
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
