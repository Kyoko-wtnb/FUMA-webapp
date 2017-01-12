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

magma <- fread(paste(filedir, "magma.genes.out", sep=""), data.table=F)
load(paste(config$data$ENSG, "/ENSG.all.genes.RData", sep=""))

magma$SYMBOL <- ENSG.all.genes$external_gene_name[match(magma$GENE, ENSG.all.genes$ensembl_gene_id)]
write.table(magma, paste(filedir, "magma.genes.out", sep=""), quote=F, row.names=F, sep="\t")
