##### Config parser #####
# only works for INI file
#
# 20 Dec 2016
# Kyoko Watanabe
#########################

ConfigParser <- function(file){
  line = as.character(read.table(file, header=F, sep="\t")[,1])
  i=0
  config <- list()
  for(l in line){
    l = gsub(" ","", l)
    if(grepl("\\[.*\\]", l)){
      i <- i+1
      config[[i]]<-list()
      names(config)[[i]] <- sub("\\[(.*)\\]", "\\1", l)
      next
    }else{
      if(i==0){next}
      tmp <- unlist(strsplit(l, "="))
      config[[i]][length(config[[i]])+1] <- tmp[2]
      names(config[[i]])[length(config[[i]])] <- tmp[1]
    }
  }
  return(config)
}
