#!/usr/bin/python

import os
import sys
import re
import pandas as pd
import numpy as np
import ConfigParser
import tabix
from bisect import bisect_left

##### return unique element in list #####
def unique(a):
	unique = []
	[unique.append(s) for s in a if s not in unique]
	return unique

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results

def getGWAScatSNPs(snps, snpshead, gwascat_file):
	snps = snps[snps[:,3].argsort()]
	min_pos = min(snps[:,3].astype(int))
	max_pos = max(snps[:,3].astype(int))
	chrom = int(snps[0,2])

	tb = tabix.open(gwascat_file)
	tmp = tb.querys(str(chrom)+":"+str(min_pos)+"-"+str(max_pos))
	out = []
	gl_idx = snpshead.index("GenomicLocus")
	s_idx = snpshead.index("IndSigSNP")
	for l in tmp:
		if int(l[1]) in snps[:,3]:
			j = np.where(snps[:,3]==int(l[1]))[0][0]
			out.append([snps[j, gl_idx], snps[j, s_idx]]+l)
	out = np.array(out)
	return out

def main ():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./getGWAScat.py <filedir>')

	##### add '/' to the filedir #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### config variables #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

	gwascat_file = cfg.get("data", "GWAScat")

	##### read SNPs #####
	snps = pd.read_table(filedir+"snps.txt", sep="\t")
	snpshead = list(snps.columns.values)
	snps = np.array(snps)

	##### Process per risk loci #####
	out = []
	loci_idx = unique(snps[:,snpshead.index("GenomicLocus")].astype(int))
	for i in loci_idx:
		tmp_snps = snps[snps[:,snpshead.index("GenomicLocus")].astype(int)==i]
		tmp_out = getGWAScatSNPs(tmp_snps, snpshead, gwascat_file)
		if len(tmp_out)>0:
			if len(out)==0:
				out = tmp_out
			else:
				out = np.r_[out, tmp_out]

	##### output #####
	with open(filedir+"gwascatalog.txt", 'w') as o:
		o.write("\t".join(["GenomicLocus", "IndSigSNP", "chr", "bp", "snp",
		"DateAddedToCatalog", "PMID", "FirstAuth", "Date", "Journal", "Link", "Study",
		"Trait", "InitialN", "ReplicationN", "Region", "ReportedGene", "MappedGene",
		"UpGene", "DownGene", "SNP_Gene_ID", "UpGeneDist", "DownGeneDist", "Strongest",
		"SNPs", "marged", "SNP_ID_cur", "Context", "intergenic", "RiskAF", "P",
		"Pmlog", "Ptext", "OrBeta", "95CI", "Platform", "CNV"])+"\n")
		for l in out:
			o.write("\t".join([str(x) for x in l])+"\n")

if __name__ == "__main__": main()
