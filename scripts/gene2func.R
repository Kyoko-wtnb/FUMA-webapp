library(data.table)
library(kimisc)
args <- commandArgs(TRUE)

##### DEG function #####
DEGtest <- function(genes, allgenes, adjP.method="BH", file){
	data <- fread(file, head=F)
	colnames(data) <- c("GeneSet", "n", "genes")
	allgenes <- allgenes[allgenes %in% unlist(strsplit(data$genes[data$GeneSet=="all"], ":"))]
	results <- data.frame(matrix(vector(), 0, 7, dimnames = list(c(), c("Category", "GeneSet", "N", "N_overlap", "p", "adjP", "genes"))))
	N <- length(allgenes)
	for(i in 1:nrow(data)){
		tempg <- unlist(strsplit(data$genes[i], ":"))
		tempg <- tempg[tempg %in% allgenes]
		data$n[i] <- length(tempg)
		data$genes[i] <- paste(tempg, collapse = ":")
	}

	temp <- data.frame(matrix(vector(),0,6 ,dimnames = list(c(),c("GeneSet", "N_genes", "N_overlap", "p", "adjP", "genes"))))
	m <- length(genes)
	for(j in 1:nrow(data)){
		temp[j,1] <- data$GeneSet[j]
		n <- data$n[j]
		temp[j,2] <- data$n[j]
		GSgenes <- unlist(strsplit(data$genes[j], ":"))
		x <- length(which(genes %in% GSgenes))
		temp[j,3] <- x
		temp[j,6] <- paste(genes[which(genes %in% GSgenes)], collapse=":")
		if(x==0){temp[j,4]<-1}
		else{temp$p[j] <- phyper(x-1, n, N-n, m, lower.tail = F)}
	}
	temp[,5] <- p.adjust(temp$p, method=adjP.method)

	ct <- c("DEG.up", "DEG.down", "DEG.twoside")
	for(i in ct){
		tr <- temp[grepl(paste("\\", sub(".+(\\..+)", "\\1", i),sep=""), temp$GeneSet),]
		tr$adjP <- p.adjust(tr$p, method=adjP.method)
		tr$GeneSet <- sub("(.+)\\..+", "\\1", tr$GeneSet)
		results <- rbind(results, data.frame(Category=rep(i, nrow(tr)), tr))
	}
	return(results)
}

##### get arguments #####
filedir <- args[1]

##### get params #####
curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

params <- ConfigParser(file=paste(filedir,'params.config', sep=""))
gtype <- params$params$gtype
gval <- params$params$gval
bkgtype <- params$params$bkgtype
bkgval <- params$params$bkgval
gene_exp <- unlist(strsplit(params$params$gene_exp, ":"))
MHC <- as.numeric(params$params$MHC)

##### all genes #####
ENSG <- fread(paste(config$data$ENSG, params$params$ensembl, config$data$ENSGfile, sep="/") , data.table=F)

##### input genes #####
### input
if(gtype == "text"){
	genes <- unlist(strsplit(gval, ":"))
}else{
	genes <- fread(paste(filedir, gval, sep=""), head=F, data.table=F)
	genes <- genes[,1]
}
### background
if(bkgtype == "select"){
	bkg = unlist(strsplit(bkgval, ":"))
	if(bkg[1]=="all"){
		bkgenes = ENSG$entrezID
	}else{
		bkgenes = ENSG$ensembl_gene_id[ENSG$gene_biotype %in% bkg]
	}
}else if(bkgtype == "text"){
	bkgenes = unlist(strsplit(bkgval, ":"))
}else{
	bkgenes = fread(paste(filedir, bkgval, sep=""), head=F, data.table=F)
	bkgenes = bkgenes[,1]
}
### summary
summary <- matrix(c("Number of input genes", as.character(length(genes))), c(1,2))
summary <- rbind(summary, c("Number of background genes", as.character(length(bkgenes))))

##### MHC exclude #####
if(MHC==1){
	cat("Excluding genes in MHC region\n")
	start <- ENSG$start_position[ENSG$external_gene_name=="MOG"]
	end <- ENSG$end_position[ENSG$external_gene_name=="COL11A2"]
	MHCgenes <- ENSG$ensembl_gene_id[ENSG$chromosome_name==6 & ((ENSG$end_position>=start&ENSG$end_position<=end)|(ENSG$start_position>=start&ENSG$start_position<=end))]
	ENSG <- ENSG[!ENSG$ensembl_gene_id%in%MHCgenes,]
}

### convert genes to ENSG
geneIDs <- data.frame(input=genes)
if(length(which(toupper(genes) %in% toupper(ENSG$external_gene_name)))>0){
	colnames(geneIDs)[1] <- "symbol"
	geneIDs$ensg <- ENSG$ensembl_gene_id[match(toupper(geneIDs$symbol), toupper(ENSG$external_gene_name))]
	geneIDs$ensg[is.na(geneIDs$ensg)] <- ENSG$ensembl_gene_id[match(toupper(geneIDs$symbol[is.na(geneIDs$ensg)]), ENSG$hgnc_symbol)]
	geneIDs$entrez <- ENSG$entrezID[match(geneIDs$ensg, ENSG$ensembl_gene_id)]
	genes <- geneIDs$ensg[!is.na(geneIDs$ensg)]
}else if(length(which(toupper(genes) %in% toupper(ENSG$ensembl_gene_id)))>0){
	colnames(geneIDs)[1] <- "ensg"
	genes <- ENSG$ensembl_gene_id[toupper(ENSG$ensembl_gene_id) %in% toupper(genes)]
	geneIDs$entrez <- ENSG$entrezID[match(geneIDs$ensg, ENSG$ensembl_gene_id)]
	geneIDs$symbol <- ENSG$external_gene_name[match(geneIDs$ensg, ENSG$ensembl_gene_id)]
}else if(length(which(genes %in% ENSG$entrezID))>0){
	colnames(geneIDs)[1] <- "entrez"
	genes <- ENSG$ensembl_gene_id[ENSG$entrezID%in%genes]
	geneIDs$ensg <- ENSG$ensembl_gene_id[match(geneIDs$entrez, ENSG$entrezID)]
	geneIDs$symbol <- ENSG$external_gene_name[match(geneIDs$ensg, ENSG$ensembl_gene_id)]
}else{
 	stop("gene ID did not match")
}
summary <- rbind(summary, c("Number of input genes with recognised Ensembl ID", as.character(length(genes))))
summary <- rbind(summary, c("Input genes without recognised Ensembl ID", paste(geneIDs[!geneIDs$ensg%in%genes,1], collapse=":")))

if(length(which(toupper(bkgenes) %in% toupper(ENSG$external_gene_name)))>0){
	tmp <- bkgenes[!toupper(bkgenes)%in%toupper(ENSG$external_gene_name) & !toupper(bkgenes)%in%toupper(ENSG$hgnc_symbol)]
	bkgenes <- ENSG$ensembl_gene_id[toupper(ENSG$external_gene_name)%in%toupper(bkgenes) | toupper(ENSG$hgnc_symbol)%in%toupper(bkgenes)]
}else if(length(which(toupper(bkgenes) %in% toupper(ENSG$ensembl_gene_id)))>0){
	tmp <- bkgenes[!toupper(bkgenes) %in% toupper(ENSG$ensembl_gene_id)]
	bkgenes <- ENSG$ensembl_gene_id[toupper(ENSG$ensembl_gene_id) %in% toupper(bkgenes)]
}else if(length(which(bkgenes %in% ENSG$entrezID))>0){
	tmp <- bkgenes[!bkgenes %in% ENSG$entrezID]
	bkgenes <- ENSG$ensembl_gene_id[ENSG$entrezID%in%bkgenes]
}
summary <- rbind(summary, c("Number of background genes with recognised Ensembl ID", as.character(length(bkgenes))))
if(bkgtype=="select"){
	summary <- rbind(summary, c("Background genes without recognised Ensembl ID", ""))
}else{
	summary <- rbind(summary, c("Background genes without recognised Ensembl ID", paste(tmp, collapse=":")))
}
summary <- rbind(summary, c("Number of input genes with unique entrez ID", as.character(length(unique(geneIDs$entrez)))))
summary <- rbind(summary, c("Number of background genes with unique entrez ID", as.character(length(unique(ENSG$entrezID[ENSG$ensembl_gene_id%in%bkgenes])))))

##### write summary #####
summary[summary[,2]=="", 2] <- "NA"
write.table(summary, paste0(filedir, "summary.txt"), quote=F, row.names=F, col.names=F, sep="\t")
write.table(geneIDs, paste0(filedir, "geneIDs.txt"), quote=F, row.names=F, sep="\t")

if(length(gene_exp)>0 | gene_exp!="NA"){
	##### gene expression #####
	for(f in gene_exp){
		load(paste0(config$data$GeneExp, "/", f, ".RData"))
		if(length(which(genes %in% rownames(exp)))>1){
			exp <- exp[rownames(exp) %in% genes,]
			out <- data.frame(ensg=rownames(exp), symbol=ENSG$external_gene_name[match(rownames(exp),ENSG$ensembl_gene_id)])
			out <- cbind(out, exp)
			fname <- unlist(strsplit(f, "/"))
			write.table(out, paste0(filedir, fname[length(fname)], "_exp.txt"), quote=F, row.names=F, sep="\t")
			load(paste0(config$data$GeneExp, "/", sub("log2", "norm", f), ".RData"))
			exp <- exp[rownames(exp) %in% genes,]
			out <- data.frame(ensg=rownames(exp), symbol=ENSG$external_gene_name[match(rownames(exp),ENSG$ensembl_gene_id)])
			out <- cbind(out, exp)
			write.table(out, paste0(filedir, sub("log2", "norm", fname[length(fname)]), "_exp.txt"), quote=F, row.names=F, sep="\t")
		}
	}
	##### DEG test #####
	if(length(which(genes %in% bkgenes))>1){
		for(f in gene_exp){
			f <- sub("(.+)_avg.+", "\\1", f)
			DEG <- DEGtest(genes, allgenes=bkgenes, adjP.method="bonferroni", file=paste0(config$data$GeneExp, "/", f, "_DEG.txt"))
			fname <- unlist(strsplit(f, "/"))
			write.table(DEG, paste0(filedir, fname[length(fname)], "_DEG.txt"), quote=F, row.names=F, sep="\t")
		}
	}
}

geneTable <- ENSG[toupper(ENSG$ensembl_gene_id) %in% toupper(genes),]
geneTable <- subset(geneTable, select=c("ensembl_gene_id", "entrezID", "external_gene_name", "hgnc_symbol"))
colnames(geneTable) <- c("ensg", "entrezID", "symbol", "hgnc_symbol")
geneTable$OMIM <- ENSG$mim[match(geneTable$ensg, ENSG$ensembl_gene_id)]
geneTable$uniprotID <- ENSG$uniprot[match(geneTable$ensg, ENSG$ensembl_gene_id)]
geneTable$DrugBank <- NA
load(paste(config$data$geneIDs, "/DrugBank.RData", sep=""))

geneTable$DrugBank <- DrugBank$DrugBank[match(geneTable$uniprotID, DrugBank$uniportID)]
write.table(geneTable, paste(filedir, "geneTable.txt", sep=""), quote=F, row.names=F, sep="\t")
