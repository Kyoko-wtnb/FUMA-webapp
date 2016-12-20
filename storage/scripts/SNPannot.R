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
load(paste(config$data$ENSG,"/ENSG.all.genes.RData", sep=""))
annov$symbol <- ENSG.all.genes$external_gene_name[match(annov$gene, ENSG.all.genes$ensembl_gene_id)]
annov$symbol[is.na(annov$symbol)] <- annov$gene[is.na(annov$symbol)]
annov$chr <- snps$chr[match(annov$uniqID, snps$uniqID)]
annov$pos <- snps$pos[match(annov$uniqID, snps$uniqID)]
snps$nearestGene <- sapply(snps$uniqID, function(x){paste(annov$symbol[annov$uniqID==x & annov$dist==min(annov$dist[annov$uniqID==x])], collapse=":")})
#snps$nearestGene[is.na(snps$nearestGene)] <- sapply(snps$uniqID[is.na(snps$nearestGene)], function(x){paste(annov$gene[annov$uniqID==x & annov$dist==min(annov$dist[annov$uniqID==x])], collapse=":")})
snps$dist <- sapply(snps$uniqID, function(x){min(annov$dist[annov$uniqID==x])})
snps$func <- sapply(snps$uniqID, function(x){paste(annov$annot[annov$uniqID==x & annov$dist==min(annov$dist[annov$uniqID==x])], collapse=":")})
snps$CADD <- annot$CADD[match(snps$uniqID, annot$uniqID)]
snps$RDB <- annot$RDB[match(snps$uniqID, annot$uniqID)]
snps$minChrState <- apply(annot[,4:ncol(annot)], 1, min)
snps$commonChrState <- apply(annot[,4:ncol(annot)], 1, function(x){names(sort(table(x), decreasing=T))[1]})
write.table(snps, paste(filedir, "snps.txt", sep=""), quote=F, row.names=F, sep="\t")
write.table(annov, paste(filedir, "annov.txt", sep=""), quote=F, row.names=F, sep="\t")

annov <- unique(annov[,c(1,3)])
annov.table <- table(annov$annot)
annov.table <- data.frame(annot=names(annov.table), count = as.numeric(annov.table))
write.table(annov.table, paste(filedir, "snpsannot.txt", sep=""), quote=F, row.names=F, sep="\t")
