library(data.table)
library(kimisc)
args <- commandArgs(TRUE)
filedir <- args[1]
if(grepl("\\/$", filedir)==F){
  filedir <- paste(filedir, '/', sep="")
}
ensg_v <- args[2]

curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

magma <- fread(paste(filedir, "magma.genes.out", sep=""), data.table=F)
ENSG <- fread(paste0(config$data$ENSG, "/", ensg_v, "/", config$data$ENSGfile), data.table=F)

magma$SYMBOL <- ENSG$external_gene_name[match(magma$GENE, ENSG$ensembl_gene_id)]
write.table(magma, paste(filedir, "magma.genes.out", sep=""), quote=F, row.names=F, sep="\t")

if(file.exists(paste0(filedir, "magma.gsa.out"))){
  magmaset <- fread(paste0(filedir, "magma.gsa.out"), skip=3, data.table=F)
}else{
  magmaset <- fread(paste0(filedir, "magma.sets.out"), skip=3, data.table=F)
}

magmaset$Pbon <- p.adjust(magmaset$P)
magmaset <- magmaset[order(magmaset$P),]
magmaset <- subset(magmaset, select=c("FULL_NAME", "NGENES", "BETA", "BETA_STD", "SE", "P", "Pbon"))
if(length(which(magmaset$Pbon<0.05))<10){
  magmaset <- magmaset[1:10,]
}else{
  magmaset <- magmaset[magmaset$Pbon<0.05,]
}
write.table(magmaset, paste(filedir, "magma.sets.top", sep=""), sep="\t", quote=F, row.names=F)
