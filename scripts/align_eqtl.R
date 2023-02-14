##### load libraries #####
library(data.table)

##### get commnad line arguments #####
args <- commandArgs(TRUE)
filedir <- args[1]
if(grepl("\\/$", filedir)==F){
  filedir <- paste(filedir, '/', sep="")
}

##### read files #####
eqtl <- fread(paste(filedir, "eqtl.txt", sep=""), data.table=F)
if(nrow(eqtl)>0){
	snps <- fread(paste(filedir, "snps.txt", sep=""), data.table=F)

	eqtl$RiskIncAllele <- NA
	eqtl$alignedDirection <- NA

	##### check signed effect in the SNPs file #####
	tmp <- data.frame(uniqID=snps$uniqID)
	if("beta" %in% colnames(snps)){
		tmp$RiskIncAllele <- ifelse(snps$beta>0, snps$effect_allele, snps$non_effect_allele)
	}else if("or" %in% colnames(snps)){
		tmp$RiskIncAllele <- ifelse(snps$or>1, snps$effect_allele, snps$non_effect_allele)
	}

	if(length(!is.na(tmp$RiskIncAllele))>0){
		eqtl$RiskIncAllele <- tmp$RiskIncAllele[match(eqtl$uniqID, tmp$uniqID)]
		eqtl$alignedDirection <- ifelse(eqtl$testedAllele==eqtl$RiskIncAllele, eqtl$signed_stats, -1*eqtl$signed_stats)
		eqtl$alignedDirection[!is.na(eqtl$alignedDirection)] <- ifelse(eqtl$alignedDirection[!is.na(eqtl$alignedDirection)] > 0, "+", "-")
	}
}else{
	tmp <- colnames(eqtl)
	eqtl <- data.frame(matrix(nrow=0, ncol=10))
	colnames(eqtl) <- c(tmp, "RiskIncAllele", "alignedDirection")
}

write.table(eqtl, paste(filedir, "eqtl.txt", sep=""), quote=F, row.names=F, sep="\t")
