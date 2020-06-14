##### load libraries #####
library(data.table)
library(GenomicRanges)
library(kimisc)

start_time <- Sys.time()

##### get commnad line arguments #####
args <- commandArgs(TRUE)
filedir <- args[1]
if(grepl("\\/$", filedir)==F){
	filedir <- paste0(filedir, '/')
}

##### get config parameters #####
curfile <- thisfile()
source(paste0(dirname(curfile), '/ConfigParser.R'))
config <- ConfigParser(file=paste0(dirname(curfile),'/app.config'))
params <- ConfigParser(file=paste0(filedir, 'params.config'))

datadir = config$CI$CIdata
reg_datadir = config$CI$RoadmapData
ciMapBuiltin = unlist(strsplit(params$ciMap$ciMapBuiltin, ":"))
if("all" %in% ciMapBuiltin){
	ciMapBuiltin = paste0("HiC/GSE87112/", list.files(paste0(datadir,"/HiC/GSE87112"), pattern=".*.txt.gz"))
}
ciMapFileN = as.numeric(params$ciMap$ciMapFileN)
if(ciMapFileN>0){
	ciMapFiles = unlist(strsplit(params$ciMap$ciMapFiles, ":"))
}
ciMapFDR = as.numeric(params$ciMap$ciMapFDR)
ciMapRoadmap = unlist(strsplit(params$ciMap$ciMapRoadmap, ":"))
if("all" %in% ciMapRoadmap){
	ciMapRoadmap = c("all")
}
promoter = as.numeric(unlist(strsplit(params$ciMap$ciMapPromWindow, "-")))
genetype = unlist(strsplit(params$params$genetype, ":"))
ensg_v = params$params$ensembl

##### read files #####
loci = fread(paste0(filedir, "GenomicRiskLoci.txt"), data.table=F)
snps = fread(paste0(filedir, "snps.txt"), data.table=F)
genes = fread(paste0(config$data$ENSG, "/", ensg_v, "/", config$data$ENSGfile), data.table=F)
if(!"all" %in% genetype){
	genes = genes[genes$gene_biotype %in% genetype,]
}
genes$prom_start <- with(genes, ifelse(strand==1, start_position-promoter[1], end_position-promoter[2]))
genes$prom_end <- with(genes, ifelse(strand==1, start_position+promoter[2], end_position+promoter[1]))
genes$chromosome_name[genes$chromosome_name=="X"] <- 23

##### map chromatin interactions to SNPs #####
loci_gr <- with(loci, GRanges(seqnames=chr, IRanges(start=start, end=end)))
snps_gr <- with(snps, GRanges(seqnames=chr, IRanges(start=pos, end=pos)))
genes_gr <- with(genes, GRanges(seqnames=chromosome_name, IRanges(start=prom_start, end=prom_end)))
ci <- data.frame()
if(ciMapFileN > 0){
	for(f in ciMapFiles){
		print(f)
		tmp_f <- unlist(strsplit(f, "/"))
		ci_tmp <- fread(cmd=paste0("gzip -cd ", filedir, tmp_f[length(tmp_f)]), data.table=F)
		colnames(ci_tmp) <- c("chr1", "start1", "end1", "chr2", "start2", "end2", "FDR")
		ci_tmp <- ci_tmp[ci_tmp$FDR<ciMapFDR,]
		ci_tmp$chr1 <- sub("^chr", "", ci_tmp$chr1)
		ci_tmp$chr1[ci_tmp$chr1=="X" | ci_tmp$chr1=="x"] <- 23
		ci_tmp$chr2 <- sub("^chr", "", ci_tmp$chr2)
		ci_tmp$chr2[ci_tmp$chr2=="X" | ci_tmp$chr2=="x"] <- 23
		ci_tmp <- ci_tmp[ci_tmp$chr1 %in% 1:23 & ci_tmp$chr2 %in% 1:23,]
		tmp_out <- data.frame()
		if(nrow(ci_tmp) > 0){
			ci1_gr <- with(ci_tmp, GRanges(seqname=chr1, IRanges(start=start1, end=end1)))
			overlap <- findOverlaps(loci_gr, ci1_gr)
			if(length(queryHits(overlap))>0){
				tmp <- data.frame(GenomicLocus=queryHits(overlap), ci_tmp[subjectHits(overlap),])
				tmp_out <- tmp
			}
			if(!grepl("oneway", f)){
				ci2_gr <- with(ci_tmp, GRanges(seqname=chr2, IRanges(start=start2, end=end2)))
				overlap <- findOverlaps(loci_gr, ci2_gr)
				if(length(queryHits(overlap))>0){
					tmp <- data.frame(GenomicLocus=queryHits(overlap), ci_tmp[subjectHits(overlap), c(4:6,1:3,7)])
					colnames(tmp) <- c("GenomicLocus", "chr1", "start1", "end1", "chr2", "start2", "end2", "FDR")
					if(nrow(tmp_out)==0){tmp_out <- tmp}
					else{tmp_out <- rbind(tmp_out, tmp)}
				}
			}
		}
		if(nrow(tmp_out)>0){
			tmp_out$type <- unlist(strsplit(f, "/"))[1]
			tmp_out$DB <- unlist(strsplit(f, "/"))[2]
			tmp_out$`tissue/cell` <- sub(".txt.gz", "", unlist(strsplit(f, "/"))[3])
			tmp_out$`inter/intra` <- with(tmp_out, ifelse(chr1==chr2, "intra", "inter"))
			if(nrow(ci)==0){ci <- tmp_out}
			else{ci <- rbind(ci, tmp_out)}
		}
	}
}

if(ciMapBuiltin[1]!="NA"){
	for(f in ciMapBuiltin){
		print(f)
		ci_tmp <- fread(cmd=paste0("gzip -cd ", datadir, "/", f), data.table=F)
		colnames(ci_tmp) <- c("chr1", "start1", "end1", "chr2", "start2", "end2", "FDR")
		ci_tmp <- ci_tmp[ci_tmp$FDR<ciMapFDR,]
		ci_tmp$chr1 <- sub("^chr", "", ci_tmp$chr1)
		ci_tmp$chr1[ci_tmp$chr1=="X" | ci_tmp$chr1=="x"] <- 23
		ci_tmp$chr2 <- sub("^chr", "", ci_tmp$chr2)
		ci_tmp$chr2[ci_tmp$chr2=="X" | ci_tmp$chr2=="x"] <- 23
		ci_tmp <- ci_tmp[ci_tmp$chr1 %in% 1:23 & ci_tmp$chr2 %in% 1:23,]
		tmp_out <- data.frame()
		if(nrow(ci_tmp) > 0){
			ci1_gr <- with(ci_tmp, GRanges(seqname=chr1, IRanges(start=start1, end=end1)))
			overlap <- findOverlaps(loci_gr, ci1_gr)
			if(length(queryHits(overlap))>0){
				tmp <- data.frame(GenomicLocus=queryHits(overlap), ci_tmp[subjectHits(overlap),])
				tmp_out <- tmp
			}
			if(!grepl("oneway", f)){
				ci2_gr <- with(ci_tmp, GRanges(seqname=chr2, IRanges(start=start2, end=end2)))
				overlap <- findOverlaps(loci_gr, ci2_gr)
				if(length(queryHits(overlap))>0){
					tmp <- data.frame(GenomicLocus=queryHits(overlap), ci_tmp[subjectHits(overlap), c(4:6,1:3,7)])
					colnames(tmp) <- c("GenomicLocus", "chr1", "start1", "end1", "chr2", "start2", "end2", "FDR")
					if(nrow(tmp_out)==0){tmp_out <- tmp}
					else{tmp_out <- rbind(tmp_out, tmp)}
				}
			}
		}
		if(nrow(tmp_out)>0){
			tmp_out$type <- unlist(strsplit(f, "/"))[1]
			tmp_out$DB <- unlist(strsplit(f, "/"))[2]
			tmp_out$`tissue/cell` <- sub(".txt.gz", "", unlist(strsplit(f, "/"))[3])
			tmp_out$`inter/intra` <- with(tmp_out, ifelse(chr1==chr2, "intra", "inter"))
			if(nrow(ci)==0){ci <- tmp_out}
			else{ci <- rbind(ci, tmp_out)}
		}
	}
}

### find overlapping SNPs
if(nrow(ci)==0){
	print("No chromatin interaction was found")
	ciSNPs <- data.frame()
	ciProm <- data.frame()
}else{
	ci_gr <- with(ci, GRanges(seqname=chr1, IRanges(start=start1, end=end1)))
	overlap <- findOverlaps(ci_gr, snps_gr)
	if(length(queryHits(overlap))==0){
		tmp_snps <- data.frame(Group.1=NA, x=NA)
	}else if(length(queryHits(overlap))==1){
		tmp_snps <- data.frame(Group.1=queryHits(overlap), x=snps$rsID[subjectHits(overlap)], stringsAsFactors=F)
	}else{
		tmp_snps <- aggregate(subjectHits(overlap), list(queryHits(overlap)), function(x){paste(snps$rsID[x], collapse=";")})
		tmp_snps <- tmp_snps[tmp_snps$x!="",]
	}
	ci$SNPs <- NA
	ci$SNPs[tmp_snps$Group.1] <- tmp_snps$x
	ci <- ci[!is.na(ci$SNPs),]
	if(nrow(ci)==0){
		print("No chromatin interaction was found")
		ciSNPs <- data.frame()
		ciProm <- data.frame()
	}else{
		### find overlapping genes
		ci_gr <- with(ci, GRanges(seqname=chr2, IRanges(start=start2, end=end2)))
		overlap <- findOverlaps(ci_gr, genes_gr)
		if(length(queryHits(overlap))==0){
			tmp_genes <- data.frame(Group.1=NA, x=NA)
		}else if(length(queryHits(overlap))==1){
			tmp_genes <- data.frame(Group.1=queryHits(overlap), x=genes$ensembl_gene_id[subjectHits(overlap)], stringsAsFactors=F)
		}else{
			tmp_genes <- aggregate(subjectHits(overlap), list(queryHits(overlap)), function(x){paste(genes$ensembl_gene_id[x], collapse=":")})
		}
		ci$genes <- NA
		ci$genes[tmp_genes$Group.1] <- tmp_genes$x
		ci$region1 <- gsub(" ", "", apply(ci[,2:4], 1, function(x){paste0(x[1], ":", x[2], "-", x[3])}))
		ci$region2 <- gsub(" ", "", apply(ci[,5:7], 1, function(x){paste0(x[1], ":", x[2], "-", x[3])}))

		##### annotate enhancers for SNPs #####
		print("Annotating enhancers...")
		insnps <- snps[snps$rsID %in% unique(unlist(strsplit(ci$SNPs,";"))),]
		snps_gr <- with(insnps, GRanges(seqnames=chr, IRanges(start=pos, end=pos)))
		ciSNPs <- data.frame()
		write.table(paste0(loci$chr,":",loci$start,"-",loci$end), paste0(filedir, "tmp.region"), quote=F, row.names=F, col.names=F)
		### enh
		system(paste0("xargs -a ",filedir, "tmp.region -I {} tabix ", reg_datadir,"/enh/enh.bed.gz {} >", filedir, "tmp.reg"))
		if(file.info(paste0(filedir, "tmp.reg"))$size>0){
			reg <- fread(paste0(filedir, "tmp.reg"), header=F, data.table=F)
			reg <- unique(reg)
			colnames(reg) <- c("chr", "start", "end", "eid")
			if(!"all" %in% ciMapRoadmap){reg <- reg[reg$eid %in% ciMapRoadmap,]}
			if(nrow(reg)>0){
				reg[,1:3] <- apply(reg[,1:3], 2, as.numeric)
				reg$start <- reg$start+1
				reg$end <- reg$end+1
				reg_gr <- with(reg, GRanges(seqnames=chr, IRanges(start=start, end=end)))
				overlap <- findOverlaps(snps_gr, reg_gr)
				if(length(queryHits(overlap))>0){
					tmp_out <- data.frame(insnps[queryHits(overlap), c("uniqID", "rsID", "chr", "pos")])
					tmp_out$reg_region <- gsub(" ", "", apply(reg[subjectHits(overlap),], 1, function(x){paste0(x[1],":",x[2],"-",x[3])}))
					tmp_out$type <- "enh"
					tmp_out$`tissue/cell` <- reg$eid[subjectHits(overlap)]
					if(nrow(ciSNPs)==0){ciSNPs <- tmp_out}
					else{ciSNPs <- rbind(ciSNPs, tmp_out)}
				}
			}
		}
		### dyadic
		system(paste0("xargs -a ",filedir, "tmp.region -I {} tabix ", reg_datadir,"/dyadic/dyadic.bed.gz {} >", filedir, "tmp.reg"))
		if(file.info(paste0(filedir, "tmp.reg"))$size>0){
			reg <- fread(paste0(filedir, "tmp.reg"), header=F, data.table=F)
			reg <- unique(reg)
			colnames(reg) <- c("chr", "start", "end", "eid")
			if(!"all" %in% ciMapRoadmap){reg <- reg[reg$eid %in% ciMapRoadmap,]}
			if(nrow(reg)>0){
				reg[,1:3] <- apply(reg[,1:3], 2, as.numeric)
				reg$start <- reg$start+1
				reg$end <- reg$end+1
				reg_gr <- with(reg, GRanges(seqnames=chr, IRanges(start=start, end=end)))
				overlap <- findOverlaps(snps_gr, reg_gr)
				if(length(queryHits(overlap))>0){
					tmp_out <- data.frame(insnps[queryHits(overlap), c("uniqID", "rsID", "chr", "pos")])
					tmp_out$reg_region <- gsub(" ", "", apply(reg[subjectHits(overlap),], 1, function(x){paste0(x[1],":",x[2],"-",x[3])}))
					tmp_out$type <- "dyadic"
					tmp_out$`tissue/cell` <- reg$eid[subjectHits(overlap)]
					if(nrow(ciSNPs)==0){ciSNPs <- tmp_out}
					else{ciSNPs <- rbind(ciSNPs, tmp_out)}
				}
			}
		}

		##### annotate promoter to genes #####
		print("Annotating promoters...")
		ciProm <- data.frame()
		reg2 <- unique(ci[,5:7])
		reg2 <- reg2[with(reg2, order(chr2, start2)),]
		reg2_gr <- with(reg2, GRanges(seqnames=chr2, IRanges(start=start2, end=end2)))
		overlap <- as.data.frame(findOverlaps(reg2_gr, reg2_gr, maxgap=100000))
		reg2_reduce <- reg2
		reg2_reduce$start2 <- with(overlap, aggregate(subjectHits, list(queryHits), function(x){min(reg2$start2[x])}))$x
		reg2_reduce$end2 <- with(overlap, aggregate(subjectHits, list(queryHits), function(x){max(reg2$end2[x])}))$x
		reg2_reduce <- as.data.frame(reduce(with(reg2_reduce, GRanges(seqnames=chr2, IRanges(start=start2, end=end2)))))
		reg2_gr <- with(reg2, GRanges(seqnames=chr2, IRanges(start=start2, end=end2)))
		write.table(paste0(reg2_reduce$seqnames,":",reg2_reduce$start,"-",reg2_reduce$end), paste0(filedir, "tmp.region"), quote=F, row.names=F, col.names=F)
		### prom
		system(paste0("xargs -a ",filedir, "tmp.region -I {} tabix ", reg_datadir,"/prom/prom.bed.gz {} >", filedir, "tmp.reg"))
		if(file.info(paste0(filedir, "tmp.reg"))$size>0){
			reg <- fread(paste0(filedir, "tmp.reg"), header=F, data.table=F)
			reg <- unique(reg)
			colnames(reg) <- c("chr", "start", "end", "eid")
			reg[,1:3] <- apply(reg[,1:3], 2, as.numeric)
			if(!"all" %in% ciMapRoadmap){reg <- reg[reg$eid %in% ciMapRoadmap,]}
			if(nrow(reg)>0){
				reg$start <- reg$start+1
				reg$end <- reg$end+1
				reg_gr <- with(reg, GRanges(seqnames=chr, IRanges(start=start, end=end)))
				overlap <- findOverlaps(reg_gr, genes_gr)
				reg$genes <- NA
				if(length(queryHits(overlap))>0){
					tmp_genes <- aggregate(subjectHits(overlap), list(queryHits(overlap)), function(x){paste(genes$ensembl_gene_id[x], collapse=":")})
					reg$genes[tmp_genes$Group.1] <- tmp_genes$x
				}
				overlap <- findOverlaps(reg2_gr, reg_gr)
				if(length(queryHits(overlap))>0){
					tmp_out <- data.frame(
						region2=gsub(" ", "",apply(reg2[queryHits(overlap),], 1, function(x){paste0(x[1],":",x[2],"-",x[3])})),
						reg_region=gsub(" ", "", apply(reg[subjectHits(overlap),], 1, function(x){paste0(x[1],":",x[2],"-",x[3])}))
					)
					tmp_out$type <- "prom"
					tmp_out$`tissue/cell` <- reg$eid[subjectHits(overlap)]
					tmp_out$genes <- reg$genes[subjectHits(overlap)]
					if(nrow(ciProm)==0){ciProm <- tmp_out}
					else{ciProm <- rbind(ciProm, tmp_out)}
				}
			}
		}
		### dyadic
		system(paste0("xargs -a ",filedir, "tmp.region -I {} tabix ", reg_datadir,"/dyadic/dyadic.bed.gz {} >", filedir, "tmp.reg"))
		if(file.info(paste0(filedir, "tmp.reg"))$size>0){
			reg <- fread(paste0(filedir, "tmp.reg"), header=F, data.table=F)
			reg <- unique(reg)
			colnames(reg) <- c("chr", "start", "end", "eid")
			reg[,1:3] <- apply(reg[,1:3], 2, as.numeric)
			if(!"all" %in% ciMapRoadmap){reg <- reg[reg$eid %in% ciMapRoadmap,]}
			if(nrow(reg)>0){
				reg$start <- reg$start+1
				reg$end <- reg$end+1
				reg_gr <- with(reg, GRanges(seqnames=chr, IRanges(start=start, end=end)))
				overlap <- findOverlaps(reg_gr, genes_gr)
				reg$genes <- NA
				if(length(queryHits(overlap))>0){
					tmp_genes <- aggregate(subjectHits(overlap), list(queryHits(overlap)), function(x){paste(genes$ensembl_gene_id[x], collapse=":")})
					reg$genes[tmp_genes$Group.1] <- tmp_genes$x
				}
				overlap <- findOverlaps(reg2_gr, reg_gr)
				if(length(queryHits(overlap))>0){
					tmp_out <- data.frame(
						region2=gsub(" ", "",apply(reg2[queryHits(overlap),], 1, function(x){paste0(x[1],":",x[2],"-",x[3])})),
						reg_region=gsub(" ", "", apply(reg[subjectHits(overlap),], 1, function(x){paste0(x[1],":",x[2],"-",x[3])}))
					)
					tmp_out$type <- "dyadic"
					tmp_out$`tissue/cell` <- reg$eid[subjectHits(overlap)]
					tmp_out$genes <- reg$genes[subjectHits(overlap)]
					if(nrow(ciProm)==0){ciProm <- tmp_out}
					else{ciProm <- rbind(ciProm, tmp_out)}
				}
			}
		}
	}
}

##### output #####
if(nrow(ci)==0){
	write.table("GenomicLocus\tregion1\tregion2\tFDR\ttype\tDB\ttissue/cell\tinter/intra\tSNPs\tgenes\tciMapFilt", paste0(filedir, "ci.txt"), quote=F, row.names=F, sep="\t", col.names=F)
}else{
	ci <- ci[,c(1,15,16,8:14)]
	write.table(ci, paste0(filedir, "ci.txt"), quote=F, row.names=F, sep="\t")
}
if(nrow(ciSNPs)==0){
	write.table("uniqID\trsID\tchr\tpos\treg_region\ttype\ttissue/cell", paste0(filedir, "ciSNPs.txt"), quote=F, row.names=F, sep="\t", col.names=F)
}else{
	write.table(ciSNPs, paste0(filedir, "ciSNPs.txt"), quote=F, row.names=F, sep="\t")
}
if(nrow(ciProm)==0){
	write.table("region2\treg_region\ttype\ttissue/cell\tgenes", paste0(filedir, "ciProm.txt"), quote=F, row.names=F, sep="\t", col.names=F)
}else{
	write.table(ciProm, paste0(filedir, "ciProm.txt"), quote=F, row.names=F, sep="\t")
}
system(paste0("rm ",filedir,"tmp.region ",filedir,"tmp.reg"))
print(paste0("Run time: ", Sys.time()-start_time))
