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

##### re-count SNPs per IndSig SNPs #####
# temporal solution until bug is solved in getLD.py
indS <- fread(paste(filedir, "IndSigSNPs.txt", sep=""), data.table=F)
ld <- fread(paste(filedir, "ld.txt", sep=""), data.table=F)
tmp <- with(ld, aggregate(SNP2, list(SNP1), length))
indS$nSNPs <- tmp$x[match(indS$uniqID, tmp$Group.1)]
tmp <- with(ld[ld$SNP2 %in% snps$uniqID[!is.na(snps$gwasP)],], aggregate(SNP2, list(SNP1), length))
indS$nGWASSNPs <- tmp$x[match(indS$uniqID, tmp$Group.1)]
write.table(indS, paste(filedir, "IndSigSNPs.txt", sep=""), quote=F, row.names=F, sep="\t")
rm(indS, ld)
#####

annov <- fread(paste(filedir, "annov.txt", sep=""), data.table=F)
annot <- fread(paste(filedir, "annot.txt", sep=""), data.table=F)
annot <- annot[match(snps$uniqID, annot$uniqID),]
ENSG <- fread(paste(config$data$ENSG, params$params$ensembl, config$data$ENSGfile, sep="/"), data.table=F)
annov$symbol <- ENSG$external_gene_name[match(annov$gene, ENSG$ensembl_gene_id)]
annov$symbol[is.na(annov$symbol)] <- annov$gene[is.na(annov$symbol)]
annov$chr <- snps$chr[match(annov$uniqID, snps$uniqID)]
annov$pos <- snps$pos[match(annov$uniqID, snps$uniqID)]

ENSG$chromosome_name[ENSG$chromosome_name=="X"] <- 23
genes_gr <- with(ENSG, GRanges(seqnames=chromosome_name, IRanges(start=start_position, end=end_position)))
snps_gr <- with(snps, GRanges(seqname=chr, IRanges(start=pos, end=pos)))
nearest <- distanceToNearest(snps_gr, genes_gr, select="all", ignore.strand=TRUE)
nearest <- as.data.frame(nearest)
tmp <- with(nearest, aggregate(subjectHits, list(queryHits), function(x){paste(ENSG$external_gene_name[x], collapse=":")}))
snps$nearestGene <- NA
snps$nearestGene[tmp$Group.1] <- tmp$x
tmp <- with(nearest, aggregate(distance, list(queryHits), function(x){paste(x, collapse=":")}))
snps$dist <- NA
snps$dist[tmp$Group.1] <- tmp$x
tmp <- with(annov, aggregate(annot, list(uniqID), function(x){paste(unique(x), collapse=":")}))
snps$func <- tmp$x[match(snps$uniqID, tmp$Group.1)]
snps$CADD <- annot$CADD[match(snps$uniqID, annot$uniqID)]
snps$RDB <- annot$RDB[match(snps$uniqID, annot$uniqID)]
snps$RDB[snps$RDB==""] <- NA
snps$minChrState[match(annot$uniqID, snps$uniqID)] <- apply(annot[,4:ncol(annot)], 1, min)
snps$commonChrState[match(annot$uniqID, snps$uniqID)] <- apply(annot[,4:ncol(annot)], 1, function(x){names(sort(table(x), decreasing=T))[1]})
write.table(annov, paste(filedir, "annov.txt", sep=""), quote=F, row.names=F, sep="\t")
write.table(snps, paste(filedir, "snps.txt", sep=""), quote=F, row.names=F, sep="\t")

## additional annotation
if(params$posMap$posMapAnnoDs!="NA" | params$eqtlMap$eqtlMapAnnoDs!="NA" | params$ciMap$ciMapAnnoDs!="NA"){
	annot_ds <- unique(unlist(strsplit(c(params$posMap$posMapAnnoDs, params$eqtlMap$eqtlMapAnnoDs, params$ciMap$ciMapAnnoDs), ":")))
	annot_ds <- annot_ds[annot_ds!="NA"]
	bed_out <- data.frame()
	for(ds in annot_ds){
		bed <- fread(cmd=paste0("gzip -cd ", config$data$annot_bed, "/", ds), header=F, data.table=F)[,1:3]
		colnames(bed) <- c("chr", "start", "end")
		bed <- with(bed, GRanges(seqnames=chr, IRanges(start=start+1, end=end)))
		o <- findOverlaps(snps_gr, bed)
		if(length(o)>0){
			annot$tmp <- ifelse(1:nrow(annot) %in% queryHits(o), 1, 0)
			colnames(annot)[ncol(annot)] <- sub(".bed.gz", "", gsub("/", "_", ds))
			bed <- as.data.frame(bed[subjectHits(o)])[,1:3]
			colnames(bed) <- c("chr", "start", "end")
			bed_out <- rbind(bed_out, data.frame(bed, dataset=sub(".bed.gz", "", ds)))
		}
	}
	if(nrow(bed_out)>0){
		write.table(bed_out, paste0(filedir, "annot.bed"), quote=F, row.names=F, sep="\t")
	}
	write.table(annot, paste0(filedir, "annot.txt"), quote=F, row.names=F, sep="\t")
}

ref.count <- fread(paste0(config$data$refgenome, "/", params$params$refpanel, "/", params$params$pop, "/", params$params$pop, ".annov.count"), data.table=F)
colnames(ref.count)[2:3] <- c("ref.count", "ref.prop")
#ref.count <- cbind(ref.count, annov.table[match(ref.count$annot, annov.table$annot),-1])
annov <- unique(annov[,c(1,3)])
annov.table <- table(annov$annot[!is.na(annov$annot)])
ref.count$count <- ifelse(ref.count$annot %in% names(annov.table), as.numeric(annov.table[ref.count$annot]), 0)
ref.count$prop <- ref.count$count/sum(ref.count$count)
ref.count$enrichment <- ref.count$prop/ref.count$ref.prop
N <- sum(ref.count$ref.count)
n <- sum(ref.count$count)

# This function takes input "x", which is one row with two columns from the ref.count data frame
# The values in x are (in order):
#    ref.count: How many times did we count this annotation for the 1KG reference panel?
#    count: How many times did we count this annotation for our selected variants?
# This function also uses two variables that are defined outside the function
#    N: How many times total have we counted any annotation for the 1KG data?
#    n: How many times total have we counted any annotation for our selected variants?
# It uses these values to set up the following table for a Fisher exact test:
#             | selected | nonselected | allvariants
#    curanno  |   x[2]   |             |   x[1]
#    othanno  |          |             |
#    allanno  |    n     |             |    N
calcFisher <- function(x) {
  x <- as.numeric(x)
  curanno_selected <- x[2]
  othanno_selected <- n - x[2]
  curanno_nonselected <- x[1] - x[2]
  othanno_nonselected <- N - n - curanno_nonselected
  data <- matrix(c(curanno_selected,othanno_selected,curanno_nonselected,othanno_nonselected), ncol=2)
  return(fisher.test(data)$p.value)
}

ref.count$fisher.P <- apply(ref.count[,c(2,4)], 1, calcFisher)

write.table(ref.count, paste(filedir, "annov.stats.txt", sep=""), quote=F, row.names=F, sep="\t")
