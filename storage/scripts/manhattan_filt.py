#!/usr/bin/python
# USAGE: ./manhattan_filt.py <filedir>

import sys
import os
#import json
import pandas as pd
import numpy as np
import math
import re

filedir = sys.argv[1]
if re.match(".+\/$", filedir) is None:
	filedir += '/'

GWAS = pd.read_table(filedir+"input.snps", sep="\s+", usecols=["chr", "bp", "p"], dtype="str")
chrcol = 0
poscol = 1
pcol = 2

GWAS = GWAS.as_matrix()
width = 800 #px
height = 300 #px
minP = min(GWAS[GWAS[:,pcol].astype(float)>1e-300,pcol].astype(float))
lowPs = len(np.where(GWAS[:,pcol].astype(float)==0)[0])
yMax = -math.log10(minP)
l = 3100000000/(width/2)
h = yMax/(height/2)
#print yMax
outfile = open(filedir+"manhattan.txt", 'w')
for chrom in range(1,24):
	plotSNPs = []
	if chrom==1:
		plotSNPs.append(['chr', 'bp', 'p'])
	temp = GWAS[GWAS[:,chrcol].astype(int)==chrom]
	if len(temp)==0:
		continue
	xMax = max(temp[:,poscol].astype(int))
	cur_h = 0
	while True:
		if cur_h >= -math.log10(1e-5):
			break
		t = temp[-np.log10(temp[:,pcol].astype(float))>=cur_h]
		t = t[-np.log10(t[:,pcol].astype(float))<(cur_h+h)]
		#print t.shape
		if len(t)==0:
			cur_h += h
			continue
		dens = len(t)/(float(xMax)/float(l))
		if dens<=1:
			break

		cur_x = 0
		while cur_x<xMax:
			tn = t[t[:,poscol].astype(int)>=cur_x]
			tn = tn[tn[:,poscol].astype(int)<cur_x+l]
			tn = tn[-np.log10(tn[:,pcol].astype(float))>=cur_h]
			tn = tn[-np.log10(tn[:,pcol].astype(float))<cur_h+h]
			if len(tn)==0:
				cur_x += l
				continue
			elif len(tn)>2:
				tn = tn[np.random.randint(len(tn), size=1)]
			for i in tn:
				plotSNPs.append(i)
			cur_x += l

		cur_h += h
	for i in temp[-np.log10(temp[:,pcol].astype(float))>=cur_h]:
		plotSNPs.append(i)
	#print len(plotSNPs)
	if chrom==1:
		outfile.write("\t".join(plotSNPs[0])+"\n")
		plotSNPs = plotSNPs[1:]
	plotSNPs = np.array(plotSNPs, dtype="object")
	plotSNPs = plotSNPs[plotSNPs[:,1].argsort()]
	for i in plotSNPs:
		outfile.write("\t".join(i)+"\n")
		#outfile.write("\t".join(i.astype(str))+"\n")
	print "Chromosome ",chrom," done!!"


#outfile.write("\t".join(plotSNPs[0])+"\n")
#plotSNPs = plotSNPs[1:]
#plotSNPs = np.array(plotSNPs)
#plotSNPs[:,0] = plotSNPs[:,0].astype(int)
#plotSNPs[:,1] = plotSNPs[:,1].astype(int)
#plotSNPs = plotSNPs[plotSNPs[:,1].argsort()]
#plotSNPs = plotSNPs[plotSNPs[:,0].argsort()]
#plotSNPs = plotSNPs[np.argsort(plotSNPs[:,1])]
#plotSNPs = plotSNPs[np.argsort(plotSNPs[:,0])]
#for i in plotSNPs:
#	outfile.write("\t".join(i.astype(str))+"\n")
