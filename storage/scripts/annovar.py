#!/usr/bin/python
import time
import os
import subprocess
import sys
import re
import pandas as pd
import numpy as np
import tabix
import glob
import ConfigParser
from bisect import bisect_left

##### Return index of a1 which do not exist in a2 #####
def ArrayNotIn(a1, a2):
    tmp = np.where(np.in1d(a1, a2))[0]
    return list(set(range(0,len(a1)))-set(tmp))

def getAnnov(snps, chrom, annovin, dbSNP):
	tb = tabix.open(dbSNP+"/dbSNP146.chr"+str(chrom)+".vcf.gz")
	start = min(snps[:,3].astype(int))
	end = max(snps[:,3].astype(int))

	spos = set(snps[:,3].astype(int))
	snps = snps[snps[:,3].argsort()]
	pos = snps[:,3]

	annov = []
	checked = []

	cur_start = start
	cur_end = start + 100000
	if cur_end>end:
		cur_end = end
	while cur_start <= end:
		maf = tb.querys(str(chrom)+":"+str(cur_start)+"-"+str(cur_end))
		for l in maf:
			if l[1] in checked:
				continue
			if int(l[1]) in spos:
				j = bisect_left(pos, l[1])
				if "," in l[4]:
					allele2 = l[4].split(",")
					alleles = []
					for a2 in allele2:
						alleles.append(":".join(sorted([l[3],a2])))
					if ":".join(sorted([snps[j,4],snps[j,5]])) not in alleles:
						continue
				else:
					if ":".join(sorted([l[3],l[4]])) != ":".join(sorted([snps[j,4],snps[j,5]])):
						continue
				if l[3] == str(snps[j,4]):
					annov.append([str(chrom).replace('23', 'X'), snps[j,3], str(int(snps[j,3])+len(l[3])-1), l[3], snps[j,5]])
				elif l[3] == str(snps[j,5]):
					annov.append([str(chrom).replace('23', 'X'), snps[j,3], str(int(snps[j,3])+len(l[3])-1), l[3], snps[j,4]])
				else:
					annov.append([str(chrom).replace('23', 'X'), snps[j,3], str(int(snps[j,3])+len(snps[j,4])-1), snps[j,4], snps[j,5]])
				checked.append(str(snps[j,3]))
		cur_start = cur_end+1
		cur_end = cur_start + 100000
		if cur_end>end:
			cur_end = end
	snps = snps[ArrayNotIn(snps[:,3], checked)]
	for l in snps:
		annov.append([str(chrom).replace('23', 'X'), l[3], str(int(l[3])+len(l[4])-1), l[4], l[5]])

	annov = np.array(annov)
	annov = annov[annov[:,1].argsort()]
	with open(annovin, 'a+') as o:
	 	np.savetxt(o, annov, delimiter="\t", fmt="%s")
	return

def main():
	##### check arguments #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./getLD.py <filedir>')

	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### get config files #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')
	dbSNP = dbSNP = cfg.get('data', 'dbSNP')
	annov = cfg.get('annovar', 'annovdir')
	humandb = cfg.get('annovar', 'humandb')

	##### read files #####
	snps = pd.read_csv(filedir+"snps.txt", header=0, dtype=str, sep="\t")
	snpshead = list(snps.columns.values)
	snps = np.array(snps)

	loci = pd.read_csv(filedir+"GenomicRiskLoci.txt", header=0, dtype=str, sep="\t")
	loci = np.array(loci)

	idx = snpshead.index("GenomicLocus")
	annovin = filedir+"annov.input"
	f = open(annovin, "w")
	f.close()
	for l in loci:
		getAnnov(snps[snps[:,idx]==l[0]], l[3], annovin, dbSNP)

	##### ANNOVAR #####
	annovout = filedir+"annov"
	os.system(annov+"/annotate_variation.pl -out "+annovout+" -build hg19 "+annovin+" "+humandb+"/ -dbtype ensGene")
	annov1 = filedir+"annov.variant_function"
	annov2 = filedir+"annov.txt"
	os.system("perl "+os.path.dirname(os.path.realpath(__file__))+"/annov_geneSNPs.pl "+annov1+" "+annov2)

if __name__ == "__main__": main()
