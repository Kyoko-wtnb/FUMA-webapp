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

magmaset <- fread(paste(filedir, "magma.sets.out", sep=""), data.table=F)
magmaset$Pbon <- p.adjust(magmaset$P)
magmaset <- magmaset[order(magmaset$Pbon),]
magmaset <- subset(magmaset, select=c("FULL_NAME", "NGENES", "BETA", "BETA_STD", "SE", "P", "Pbon"))
if(length(which(magmaset$Pbon<0.05)<10)){
  magmaset <- magmaset[1:10,]
}else{
  magmaset <- magmaset[magmaset$Pbon<0.05,]
}
write.table(magmaset, paste(filedir, "magma.sets.top", sep=""), sep="\t", quote=F, row.names=F)
