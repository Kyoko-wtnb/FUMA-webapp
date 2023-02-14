#!/usr/bin/python
import os
import sys
import pandas as pd
import numpy as np
import ConfigParser
import re

def main():
	##### check arguments #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./magma.py <filedir>')

	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### get config files #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

	param_cfg = ConfigParser.ConfigParser()
	param_cfg.read(filedir+'params.config')

	##### parameters #####
	N = param_cfg.get('params', 'N')
	if N=="NA":
		N = None
	magmafiles = cfg.get('magma', 'magmafiles')
	magmadir = cfg.get('magma', 'magmadir')
	mhc = int(param_cfg.get('params', 'exMHC'))
	mhc_region = param_cfg.get('params', 'extMHC')
	mhcopt = param_cfg.get('params', 'MHCopt')
	if mhc==1 and mhcopt=="annot":
		mhc = 0
	mhc_start = 29614758
	mhc_end = 33170276
	if not mhc_region=="NA":
		tmp = mhc_region.split("-")
		mhc_start = int(tmp[0])
		mhc_end = int(tmp[1])

	refpanel = param_cfg.get('params', 'refpanel')
	pop = param_cfg.get('params', 'pop')
	ensg_v = param_cfg.get('params', 'ensembl')
	magma_window = param_cfg.get('magma', 'magma_window').replace(" ","")
	magma_exp = param_cfg.get('magma', 'magma_exp').split(":")

	magma_file = "magma.input"
	snps_file = "input.snps"

	### gene annotation
	## create annotation file if necessary
	annot_file = magmafiles+"/"+refpanel+"/"+pop+"_ENSG"+ensg_v+"_w"+magma_window.replace(",", "_")+".genes.annot"
	if not os.path.isfile(annot_file):
		if "," in magma_window:
			w = magma_window
		else:
			w = magma_window+","+magma_window
		command = magmadir+"/magma --annotate window="+w
		command += " --snp-loc "+magmafiles+"/"+refpanel+"/"+pop+".bim"
		command += " --gene-loc "+magmafiles+"/ENSG"+ensg_v+".coding.genes.txt"
		command += " --out "+magmafiles+"/"+refpanel+"/"+pop+"_ENSG"+ensg_v+"_w"+magma_window.replace(",", "_")
		os.system(command)

	### MAGMA gene analysis
	command = "awk 'NR>=2' "+filedir+snps_file
	if not N is None:
		if mhc==1:
			command += " | awk '{if($1!=6){print $0}else if($2<"+str(mhc_start)+" || $2>"
			command += str(mhc_end)+"){print $0}}'"
			command += " | awk '{if($3<$4){print $1 "+'":"'+" $2 "+'":"'+" $3 "+'":"'+" $4 "+'"\t"'
			command += " $6}else{print$1 "+'":"'+" $2 "+'":"'+" $4 "+'":"'+" $3"+'"\t"'+" $6}}'"
			command += " | sort -u -k 1,1 > "+filedir+magma_file
		else:
			command += " | awk '{if($3<$4){print $1 "+'":"'+" $2 "+'":"'+" $3 "+'":"'+" $4 "+'"\t"'
			command += " $6}else{print$1 "+'":"'+" $2 "+'":"'+" $4 "+'":"'+" $3"+'"\t"'+" $6}}'"
			command += " | sort -u -k 1,1 > "+filedir+magma_file
		os.system(command)
		command = magmadir+"/magma --bfile "+magmafiles+"/"+refpanel+"/"+pop
		command += " --pval "+filedir+magma_file+" N="+str(N)
		command += " --gene-annot "+annot_file
		command += " --out "+filedir+"magma"
		os.system(command)
	else:
		header = []
		with open(filedir+snps_file, 'r') as fin:
			header = fin.readline().strip().split("\t")
		Ncol = len(header)
		if mhc==1:
			command += " | awk '{if($1!=6){print $0}else if($2<"+str(mhc_start)+" || $2>"
			command += str(mhc_end)+"){print $0}}'"
			command += " | awk '{if($3<$4){print $1 "+'":"'+" $2 "+'":"'+" $3 "+'":"'+" $4 "+'"\t"'+" $6 "+'"\t"'
			command += " int($"+str(Ncol)+")}else{print$1 "+'":"'+" $2 "+'":"'+" $4 "+'":"'+" $3"+'"\t"'+" $6 "+'"\t"'+" int($"+str(Ncol)+")}}'"
			command += " | sort -u -k 1,1 > "+filedir+magma_file
		else:
			command += " | awk '{if($3<$4){print $1 "+'":"'+" $2 "+'":"'+" $3 "+'":"'+" $4 "+'"\t"'+" $6 "+'"\t"'
			command += " int($"+str(Ncol)+")}else{print$1 "+'":"'+" $2 "+'":"'+" $4 "+'":"'+" $3"+'"\t"'+" $6 "+'"\t"'+" int($"+str(Ncol)+")}}'"
			command += " | sort -u -k 1,1 > "+filedir+magma_file
		os.system(command)
		command = magmadir+"/magma --bfile "+magmafiles+"/"+refpanel+"/"+pop
		command += " --pval "+filedir+magma_file+" ncol=3"
		command += " --gene-annot "+annot_file
		command += " --out "+filedir+"magma"
		os.system(command)

	if not os.path.isfile(filedir+"magma.genes.out"):
		sys.exit("MAGMA ERROR")

	os.system("sed 's/ \\+/\\t/g' "+filedir+"magma.genes.out > "+filedir+"temp.txt")
	os.system("mv "+filedir+"temp.txt "+filedir+"magma.genes.out")

	### MAGMA gene set analysis
	command = magmadir+"/magma --gene-results "+filedir+"magma.genes.raw"
	command += " --set-annot "+magmafiles+"/magma_GS.txt"
	command += " --out "+filedir+"magma"
	os.system(command)
	### MAGMA gene expression analyses
	for f in magma_exp:
		tmp = f.split("/")
		out = tmp[len(tmp)-1]
		command = magmadir+"/magma --gene-results "+filedir+"magma.genes.raw"
		command += " --gene-covar "+magmafiles+"/"+f+".txt"
		command += " --model direction=greater condition-hide=Average"
		command += " --out "+filedir+"magma_exp_"+out
		os.system(command)

	os.system("Rscript "+os.path.dirname(os.path.realpath(__file__))+"/magma_gene.R "+filedir+" "+ensg_v)

if __name__=="__main__": main()
