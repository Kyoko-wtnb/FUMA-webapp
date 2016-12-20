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
if re.match("\/$", filedir) is None:
	filedir += '/'

GWAS = pd.read_table(filedir+"input.snps", sep="\s+")
chrcol = 0
poscol = 1
pcol = 5

GWAS = GWAS.as_matrix()
width = 800 #px
height = 300 #px
yMax = max(-np.log10(GWAS[:,pcol].tolist()))
l = 3100000000/(width/2)
h = yMax/(height/2)
#print yMax
outfile = open(filedir+"manhattan.txt", 'w')
for chrom in range(1,23):
	plotSNPs = []
	if chrom==1:
		plotSNPs.append(['chr', 'bp', 'p'])
	temp = GWAS[GWAS[:,chrcol]==chrom]
	temp = temp[:,[0,1,5]]
	if temp.shape[0]==0:
		continue
	#print temp.shape
	xMax = max(temp[:,1])
	cur_h = 0
	while True:
		if cur_h >= -math.log(1e-5, 10):
			break
		t = temp[-np.log10(temp[:,2].tolist())>=cur_h]
		t = t[-np.log10(t[:,2].tolist())<(cur_h+h)]
		#print t.shape
		if t.shape[0]==0:
			cur_h += h
			continue
		dens = t.shape[0]/(float(xMax)/float(l))
		if dens<=1:
			break

		cur_x = 0
		while cur_x<xMax:
			tn = t[t[:,1]>=cur_x]
			tn = tn[tn[:,1]<cur_x+l]
			tn = tn[-np.log10(tn[:,2].tolist())>=cur_h]
			tn = tn[-np.log10(tn[:,2].tolist())<cur_h+h]
			if tn.shape[0]==0:
				cur_x += l
				continue
			elif tn.shape[0]>2:
				tn = tn[np.random.randint(tn.shape[0], size=1)]
			for i in tn:
				plotSNPs.append(i.tolist())
			cur_x += l

		cur_h += h
	for i in temp[-np.log10(temp[:,2].tolist())>=cur_h]:
		plotSNPs.append(i.tolist())
	#print len(plotSNPs)
	if chrom==1:
		outfile.write("\t".join(plotSNPs[0])+"\n")
		plotSNPs = plotSNPs[1:]
	plotSNPs = np.array(plotSNPs)
	plotSNPs[:,1] = plotSNPs[:,1].astype(int)
	plotSNPs = plotSNPs[np.argsort(plotSNPs[:,1])]
	for i in plotSNPs:
		outfile.write(str(int(i[0]))+"\t"+str(int(i[1]))+"\t"+str(i[2])+"\n")
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
