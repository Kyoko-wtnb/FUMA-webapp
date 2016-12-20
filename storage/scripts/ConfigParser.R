##### Config parser #####
# only works for INI file
#
# 20 Dec 2016
# Kyoko Watanabe
#########################

ConfigParser <- function(file){
  line = as.character(read.table(file, header=F)[,1])
  i=0
  j=0;
  config <- list()
  for(l in line){
    if(grepl("\\[.*\\]", l)){
      i <- i+1
      j<-1
      config[[i]]<-list()
      names(config)[[i]] <- sub("\\[(.*)\\]", "\\1", l)
      next
    }else{
      tmp <- unlist(strsplit(l, "="))
      if(j==1){
        config[[i]][length(config[[i]])] <- tmp[2]
        j <- 0
      }else{
        config[[i]][length(config[[i]])+1] <- tmp[2]
      }
      names(config[[i]])[length(config[[i]])] <- tmp[1]
    }
  }
  return(config)
}