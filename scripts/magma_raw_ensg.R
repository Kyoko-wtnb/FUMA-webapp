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

ENSG <- fread(paste0(config$data$ENSG, "/", config$version$ENSG_latest, "/", config$data$ENSGfile), data.table=F)
magma_raw <- fread(paste0(filedir, "magma.genes.raw"), sep=";", header=F)[[1]]
out <- magma_raw[grepl("^#", magma_raw)]
magma_raw <- magma_raw[!grepl("^#", magma_raw)]
g <- sapply(magma_raw, function(x){unlist(strsplit(x, " "))[1]})
if(length(which(g %in% ENSG$external_gene_name))>0){
	ensg <- ENSG$ensembl_gene_id[match(g, ENSG$external_gene_name)]
}else if(length(which(g %in% ENSG$entrezID))>0){
	ensg <- ENSG$ensembl_gene_id[match(g, ENSG$entrezID)]
}else{
	ensg <- g
}

ensg[is.na(ensg)] <- g[is.na(ensg)]
out <- c(out, sapply(1:length(magma_raw), function(x){
	tmp <- unlist(strsplit(magma_raw[x], " "))
	tmp[1] <- ensg[x]
	paste(tmp, collapse=" ")
}))
system(paste0("mv ", filedir, "magma.genes.raw ", filedir, "magma.genes.raw.original"))
write.table(out, paste0(filedir, "magma.genes.raw"), quote=F, row.names=F, col.names=F)
