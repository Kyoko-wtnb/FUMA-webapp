library(data.table)
library(kimisc)
args <- commandArgs(TRUE)

##### get params #####
filedir <- args[1]
if(grepl("\\/$", filedir)==F){
	filedir <- paste0(filedir, '/')
}

curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))
magmafiles <- config$magma$magmafiles
magmadir <- config$magma$magmadir

params <- ConfigParser(file=paste(filedir,'params.config', sep=""))
adjPmeth <- params$params$adjPmeth
step2 <- as.numeric(params$params$step2)
step3 <- as.numeric(params$params$step3)
datasets <- unlist(strsplit(params$params$datasets, ":"))

##### Map to ENSG ID #####
if(params$params$snp2geneID=="NA" & params$params$ensg_id==0){
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
}

##### Step 1 #####
step1_command <- paste0(magmadir, "/magma --gene-results ", filedir, "magma.genes.raw",
				" --gene-covar ", magmafiles, "/celltype/[ds].txt --model condition-hide=Average direction=greater",
				" --out ", filedir, "magma_celltype_[ds]")
step1_command <- sapply(datasets, function(x){gsub("\\[ds\\]", x, step1_command)})
write.table(step1_command, paste0(filedir, "step1.sh"), quote=F, row.names=F, col.names=F)
system(paste0("bash ", filedir, "step1.sh"))
rm(step1_command)
### multple testing correction
step1 <- data.frame()
for(ds in datasets){
	tmp <- fread(cmd=paste0("grep -v '^#' ", filedir, "magma_celltype_", ds, ".gsa.out"), data.table=F)
	if("FULL_NAME" %in% colnames(tmp)){
		tmp$VARIABLE <- tmp$FULL_NAME
		tmp <- tmp[,-ncol(tmp)]
	}
	tmp <- tmp[order(tmp$P),]
	tmp$ds <- ds
	tmp$P.adj.pds <- p.adjust(tmp$P, method=adjPmeth)
	if(nrow(step1)==0){step1 <- tmp}
	else{step1 <- rbind(step1, tmp)}
}
step1$P.adj <- p.adjust(step1$P, method=adjPmeth)
tmp_out <- step1[,c("ds", "VARIABLE", "NGENES", "BETA", "BETA_STD", "SE", "P", "P.adj.pds", "P.adj")]
colnames(tmp_out)[1:2] <- c("Dataset", "Cell_type")
write.table(tmp_out, paste0(filedir, "magma_celltype_step1.txt"), quote=F, row.names=F, sep="\t")
rm(tmp_out)

##### Step 2 and 3 #####
if(step2==1){
	step1 <- step1[which(step1$P.adj<0.05),]
	if(nrow(step1)>1){
		step2_ds <- table(step1$ds)
		if(length(which(step2_ds>1))==0){
			step1$cond_state <- "single"
			step1$cond_cell_type <- NA
			step2_out <- data.frame()
		}else{
			step2_command <- c()
			for(ds in names(step2_ds)[step2_ds>1]){
				step2_command <- c(step2_command, paste0(magmadir, "/magma --gene-results ", filedir, "magma.genes.raw",
							" --gene-covar ", magmafiles, "/celltype/", ds, ".txt --model condition-hide=Average direction=greater",
							" analyse=list,", paste(step1$VARIABLE[step1$ds==ds], collapse=","), " joint-pairs",
							" --out ", filedir, "magma_celltype_step2_", ds))
			}
			write.table(step2_command, paste0(filedir, "step2.sh"), quote=F, row.names=F, col.names=F)
			system(paste0("bash ", filedir, "step2.sh"))
			rm(step2_command)
			step1$cond_state <- NA
			step1$cond_state[step1$ds %in% names(step2_ds)[step2_ds==1]] <- "single"
			step1$cond_cell_type <- NA
			step2_out <- data.frame()
			for(ds in names(step2_ds)[step2_ds>1]){
			  tmp.sig <- step1[step1$ds==ds,]
			  #!!! if the file doesn't exist
			  tmp <- fread(cmd=paste0("grep -v '^#' ", filedir, "magma_celltype_step2_", ds, ".gsa.out"), data.table=F)
			  if("FULL_NAME" %in% colnames(tmp)){
			    tmp$VARIABLE <- tmp$FULL_NAME
			    tmp <- tmp[,-ncol(tmp)]
			  }
			  tmp$Marginal.P <- tmp.sig$P[match(tmp$VARIABLE, tmp.sig$VARIABLE)]
			  tmp <- tmp[with(tmp, order(MODEL, Marginal.P)),]
			  tmp$PS <- -log10(tmp$P)/-log10(tmp$Marginal.P)
			  if(nrow(step2_out)==0){step2_out <- data.frame(tmp, ds=ds)}
			  else{step2_out <- rbind(step2_out, data.frame(tmp, ds=ds))}
			  checked <- c()
			  while(length(checked)<nrow(tmp.sig)){
			    top <- tmp.sig$VARIABLE[!tmp.sig$VARIABLE %in% checked][1]
			    ### check if there is main drover for this cell type
			    top.check <- c()
			    for(m in unique(tmp$MODEL[tmp$VARIABLE==top])){
			      t <- tmp[tmp$MODEL==m,]
			      if(all(is.na(t$P))){next}
			      if((t$PS[2]>=0.2 & t$PS[1]<0.2)){
			        top.check <- c(top.check, t$VARIABLE[2])
			      }
			    }
			    if(length(top.check)>0){
			      top <- tmp.sig$VARIABLE[tmp.sig$VARIABLE %in% top.check][1]
			    }
			    checked <- c(checked, top)
			    for(m in unique(tmp$MODEL[tmp$VARIABLE==top])){
			      t <- tmp[tmp$MODEL==m,]
			      if(t$VARIABLE[1]!=top){t <- t[2:1,]}
			      if(all(is.na(t$P))){
			        if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "colinear"
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
			        }else{
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "colinear", sep=";")
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
			        }
			        tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "colinear-drop"
			        tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
			        checked <- c(checked, t$VARIABLE[2])
			        tmp <- tmp[tmp$MODEL!=m,]
			      }else if(all(t$PS>=0.8)){
			        #indep
			        tmp <- tmp[tmp$MODEL!=m,]
			      }else if(all(t$P>=0.05)){
			        if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "joint"
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
			        }else{
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "joint", sep=";")
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
			        }
			        tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "joint-drop"
			        tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
			        checked <- c(checked, t$VARIABLE[2])
			        tmp <- tmp[tmp$MODEL!=m,]
			      }else if(all(t$PS<0.2)){
  			        if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
  			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "partial-joint"
  			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
  			        }else{
  			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "partial-joint", sep=";")
  			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
  			        }
  			        tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "joint-drop"
  			        tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
  			        checked <- c(checked, t$VARIABLE[2])
  			        tmp <- tmp[tmp$MODEL!=m,]
				  }else if(t$PS[1]>=0.5 & t$P[2]>=0.05){
			        if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "main"
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
			        }else{
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "main", sep=";")
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
			        }
			        tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "drop"
			        tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
			        checked <- c(checked, t$VARIABLE[2])
			        tmp <- tmp[tmp$MODEL!=m,]
			      }else if(t$PS[1]>=0.8 & t$PS[2]<0.2){
			        if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "main"
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
			        }else{
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "main", sep=";")
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
			        }
			        tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "partial-drop"
			        tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
			        checked <- c(checked, t$VARIABLE[2])
			        tmp <- tmp[tmp$MODEL!=m,]
			 	  }else if(all(t$PS>=0.5)){
  			        if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
  			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "partial-joint"
  			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
  			        }else{
  			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "partial-joint", sep=";")
  			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
  			        }
  			        tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "partial-joint"
  			        tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
  			        #checked <- c(checked, t$VARIABLE[2])
  			        tmp <- tmp[tmp$MODEL!=m,]
  			      }else if(all(t$PS>=0.2)){
  			        if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
  			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "partial-joint"
  			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
  			        }else{
  			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "partial-joint", sep=";")
  			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
  			        }
  			        tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "partial-joint-drop"
  			        tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
  			        checked <- c(checked, t$VARIABLE[2])
  			        tmp <- tmp[tmp$MODEL!=m,]
  			      }else if(t$PS[1]>=0.2 & t$P[2]>=0.05){
			          if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
			            tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "partial-joint"
			            tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
			          }else{
			            tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "partial-joint", sep=";")
			            tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
			          }
			          tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "joint-drop"
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
			          checked <- c(checked, t$VARIABLE[2])
			          tmp <- tmp[tmp$MODEL!=m,]
			      }else if(t$PS[1]>=0.2 & t$PS[2]<0.2){
			        if(is.na(tmp.sig$cond_state[tmp.sig$VARIABLE==top])){
			          tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- "partial-joint"
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- t$VARIABLE[2]
	          	    }else{
		              tmp.sig$cond_state[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_state[tmp.sig$VARIABLE==top], "partial-joint", sep=";")
			          tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top] <- paste(tmp.sig$cond_cell_type[tmp.sig$VARIABLE==top], t$VARIABLE[2], sep=";")
			        }
			        tmp.sig$cond_state[tmp.sig$VARIABLE==t$VARIABLE[2]] <- "partial-joint-drop"
			        tmp.sig$cond_cell_type[tmp.sig$VARIABLE==t$VARIABLE[2]] <- top
			        checked <- c(checked, t$VARIABLE[2])
			        tmp <- tmp[tmp$MODEL!=m,]
			      }else{
			        print(t)
			      }
			    }
			    tmp <- tmp[!tmp$MODEL %in% unique(tmp$MODEL[tmp$VARIABLE %in% checked]),]
			  }
			  tmp.sig$cond_state[is.na(tmp.sig$cond_state)] <- "indep"
			  step1$cond_state[step1$ds==ds] <- tmp.sig$cond_state
			  step1$cond_cell_type[step1$ds==ds] <- tmp.sig$cond_cell_type
			}
		}
		rm(tmp, tmp.sig, t)
		step1_out <- step1[,-2]
		step1_out <- step1_out[,c(7,1:6,8:11)]
		colnames(step1_out)[1:2] <- c("Dataset", "Cell_type")
		step1_out$step3 <- with(step1_out, ifelse(grepl("drop", cond_state), 0, 1))
		write.table(step1_out, paste0(filedir, "step1_2_summary.txt"), quote=F, row.names=F, sep="\t")
		if(nrow(step2_out)>0){
		  step2_out <- step2_out[,-2]
		  step2_out <- step2_out[,c(10,1:9)]
		  colnames(step2_out)[1:2] <- c("Dataset", "Cell_type")
		  step2_out$MODEL <- rep(1:(nrow(step2_out)/2), each=2)
		  write.table(step2_out, paste0(filedir, "magma_celltype_step2.txt"), quote=F, row.names=F, sep="\t")
		}
		rm(step1_out)
	}

	if(step3==1){
	  if(length(unique(step1$ds))>1){
	    step1 <- step1[!grepl("drop", step1$cond_state),]
	    step3_ds <- unique(step1$ds)
	    ### condition average of the other dataset and pair-wise
	    step3_avg <- data.frame()
	    step3_cond <- data.frame()
	    for(i in 1:(length(step3_ds)-1)){
	      ds1 <- step3_ds[i]
	      exp1 <- fread(paste0(magmafiles, "/celltype/", ds1, ".txt"), data.table=F)
	      exp1 <- exp1[,c("GENE", step1$VARIABLE[step1$ds==ds1], "Average")]
	      colnames(exp1)[2:ncol(exp1)] <- paste(ds1, colnames(exp1)[2:ncol(exp1)], sep=":")
	      colnames(exp1)[ncol(exp1)] <- "Average1"
	      for(j in (i+1):length(step3_ds)){
	        ds2 <- step3_ds[j]
	        exp2 <- fread(paste0(magmafiles, "/celltype/", ds2, ".txt"), data.table=F)
	        exp2 <- exp2[,c("GENE", step1$VARIABLE[step1$ds==ds2], "Average")]
	        colnames(exp2)[2:ncol(exp2)] <- paste(ds2, colnames(exp2)[2:ncol(exp2)], sep=":")
	        colnames(exp2)[ncol(exp2)] <- "Average2"
	        exp <- cbind(exp1, exp2[match(exp1$GENE, exp2$GENE), -1])
	        exp <- exp[!is.na(exp$Average2),]
	        write.table(exp, paste0(filedir, "step3_exp.txt"), quote=F, row.names=F, sep="\t")
	        step3_command <- paste0(magmadir, "/magma --gene-results ", filedir, "magma.genes.raw",
	                                " --gene-covar ", filedir, "step3_exp.txt max-miss=0.1 --model condition-hide=Average1,Average2 direction=greater",
	                                " --out ", filedir, "magma_celltype_step3_avg")
	        res <- system(step3_command, ignore.stdout = T)
	        if(res>0){
	          #!!! implement for error
	          print(paste("error: Average ", ds1, ds2))
	        }else{
	          tmp <- fread(cmd=paste0("grep -v '^#' ", filedir, "magma_celltype_step3_avg.gsa.out"), data.table=F)
	          if("FULL_NAME" %in% colnames(tmp)){
	            tmp$VARIABLE <- tmp$FULL_NAME
	            tmp <- tmp[,-ncol(tmp)]
	          }
	          tmp$ds <- sub("(.+):.+", "\\1", tmp$VARIABLE)
	          tmp$cond_ds <- ifelse(tmp$ds==ds1, ds2, ds1)
	          tmp$VARIABLE <- sub(".+:(.+)", "\\1", tmp$VARIABLE)
	          step3_avg <- rbind(step3_avg, tmp)
	        }
	        step3_command <- paste0(magmadir, "/magma --gene-results ", filedir, "magma.genes.raw",
	                                " --gene-covar ", filedir, "step3_exp.txt max-miss=0.1 --model condition-hide=Average1,Average2 direction=greater joint-pairs",
	                                " --out ", filedir, "magma_celltype_step3")
	        res <- system(step3_command, ignore.stdout = T)
	        if(res>0){
	          tmp_ts <- colnames(exp)[-1]
	          tmp_ts <- tmp_ts[!grepl("Average",tmp_ts)]
	          tmp <- data.frame()
	          for(tmp_i in 1:(length(tmp_ts)-1)){
	            for(tmp_j in (tmp_i+1):length(tmp_ts)){
	              tmp <- rbind(tmp, data.frame(VARIABLE=tmp_ts[c(tmp_i,tmp_j)], TYPE="COVAR", MODEL=1, NGENES=NA, BETA=NA, BETA_STD=NA, SE=NA, P=NA))
	            }
	          }
	          tmp$MODEL <- rep(1:(nrow(tmp)/2), each=2)
	        }else{
	          tmp <- fread(cmd=paste0("grep -v '^#' ", filedir, "magma_celltype_step3.gsa.out"), data.table=F)
	          if("FULL_NAME" %in% colnames(tmp)){
	            tmp$VARIABLE <- tmp$FULL_NAME
	            tmp <- tmp[,-ncol(tmp)]
	          }
	        }
	        tmp$ds <- sub("(.+):.+", "\\1", tmp$VARIABLE)
	        tmp$VARIABLE <- sub(".+:(.+)", "\\1", tmp$VARIABLE)
	        check.model <- with(tmp, aggregate(ds, list(MODEL), function(x){length(unique(x))}))
	        tmp <- tmp[tmp$MODEL %in% check.model$Group.1[check.model$x==2],]
	        step3_cond <- rbind(step3_cond, tmp)
	      }
	    }

	    rm(exp, exp1, exp2)
	    system(paste0("rm ", filedir, "step3_exp.txt"))

	    ### add within dataset conditional analyses
	    step3_cond <- step3_cond[,-2]
	    step3_cond <- step3_cond[,c(8,1:7)]
	    colnames(step3_cond)[1:2] <- c("Dataset", "Cell_type")
	    for(ds in step3_ds){
	      if(length(which(step1$ds==ds))>1){
	        tmp <- step2_out[step2_out$Dataset==ds & step2_out$Cell_type %in% step1$VARIABLE[step1$ds==ds],]
	        tmp.model <- table(tmp$MODEL)
	        tmp <- tmp[tmp$MODEL %in% names(tmp.model)[tmp.model==2],]
	        if(nrow(tmp)>0){
	          step3_cond <- rbind(step3_cond, tmp[,1:8])
	        }
	      }
	    }
	    step3_cond$MODEL <- rep(1:(nrow(step3_cond)/2), each=2)

	    step3_cond$label <- paste(step3_cond$Dataset, step3_cond$Cell_type, sep=":")
	    step3_cond$label[seq(1,nrow(step3_cond),2)] <- paste(step3_cond$Dataset[seq(2,nrow(step3_cond),2)], step3_cond$label[seq(1,nrow(step3_cond),2)], sep=":")
	    step3_cond$label[seq(2,nrow(step3_cond),2)] <- paste(step3_cond$Dataset[seq(1,nrow(step3_cond),2)], step3_cond$label[seq(2,nrow(step3_cond),2)], sep=":")
	    step3_avg$label <- paste(step3_avg$cond_ds, step3_avg$ds, step3_avg$VARIABLE, sep=":")
	    tmp <- step3_avg[match(step3_cond$label, step3_avg$label),c(4:7,9)]
	    colnames(tmp)[ncol(tmp)] <- "ds"
	    colnames(tmp) <- paste0("CDM.", colnames(tmp))
	    step3_cond <- cbind(step3_cond[,-ncol(step3_cond)], tmp)
	    step3_cond$Marginal.P <- step1$P[match(paste(step3_cond$Dataset, step3_cond$Cell_type, sep=":"), paste(step1$ds, step1$VARIABLE, sep=":"))]
  		step3_cond$PS <- -log10(step3_cond$P)/-log10(with(step3_cond, ifelse(is.na(CDM.P), Marginal.P, CDM.P)))
  		step3_cond$PS.avg <- -log10(step3_cond$CDM.P)/-log10(step3_cond$Marginal.P)
	    write.table(step3_cond, paste0(filedir, "magma_celltype_step3.txt"), quote=F, row.names=F, sep="\t")
	  }
	}
}
