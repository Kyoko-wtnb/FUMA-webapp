#!/usr/bin/python

import sys
import os
import re
import pandas as pd
import numpy as np
import tabix
import ConfigParser

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results


filedir = sys.argv[1]
if re.match(".+\/$", filedir) is None:
	filedir += '/'

cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = ConfigParser.ConfigParser()
param.read(filedir+'params.config')

ldfiledir = cfg.get('data', 'refgenome')
pop = param.get('params', 'pop')
# r2 = param.get('params', 'r2')
# gwasP = param.get('params', 'gwasP')
# leadP = param.get('params', 'leadP')
# maf = param.get('params', 'MAF')
# mergeDist = param.get('params', 'mergeDist')
# inlead = param.get('inputfiles', 'leadSNPsfile')
# if inlead=="NA":
# 	inlead = None
#
# snps = pd.read_table(filedir+"snps.txt")
# snpshead = snps.columns.values
# snps = snps.as_matrix()
# ld = pd.read_table(filedir+"ld.txt")
# ld = ld.as_matrix()
#
# ld = ld[ld[:,2]>=r2]


indSNPs = pd.read_table(filedir+'IndSigSNPs.txt')
indSNPs = indSNPs.as_matrix()
indSNPs = indSNPs[indSNPs[:,6].astype(float).argsort()]

checked = []
leadSNPs = []
# leadSNPs.append(["No", "GenomicLocus", "uniqID", "chr", "pos", "rsID", "P", "IndSigSNPs"])

for snp in indSNPs:
    if snp[3] in checked:
        continue
    ldfile = ldfiledir+'/'+pop+'/'+pop+'.chr'+str(snp[4])+'.ld.gz';
    tb = tabix.open(ldfile)
    ld = tb.querys(str(snp[4])+":"+str(snp[5])+"-"+str(snp[5]))
    inSNPs = []
    inSNPs.append(snp[3])
    for l in ld:
        if float(l[4])<0.1:
            continue
        if l[3] in indSNPs[:,3]:
            checked.append(l[3])
            inSNPs.append(l[3])
    leadSNPs.append([len(leadSNPs)+1, snp[1], snp[2], snp[4], snp[5], snp[3], snp[6], len(inSNPs), ":".join(inSNPs)])

leadSNPs = np.array(leadSNPs)
# leadSNPs = leadSNPs[leadSNPs[:,3].astype(int).argsort()]
# leadSNPs = leadSNPs[leadSNPs[:,2].astype(int).argsort()]
leadSNPs = leadSNPs[np.lexsort((leadSNPs[:,4].astype(int),leadSNPs[:,3].astype(int)))]

for i in range(0, len(leadSNPs)):
    leadSNPs[i,0] = i+1

fout = open(filedir+"leadSNPs.txt", 'w')
fout.write("\t".join(["No", "GenomicLocus", "uniqID", "chr", "pos", "rsID", "p", "nIndSigSNPs", "IndSigSNPs"])+"\n")
for l in leadSNPs:
    fout.write("\t".join(l.astype(str))+"\n")
fout.close()

loci = pd.read_table(filedir+"GenomicRiskLoci.txt")
locihead = list(loci.columns.values)
locihead.append("nLeadSNPs")
locihead.append("LeadSNPs")

loci = np.c_[loci, [0]*len(loci), [0]*len(loci)]
for i in range(0, len(loci)):
	loci[i,12] = sum(x==loci[i,0] for x in leadSNPs[:,1].astype(int))
	loci[i,13] = ":".join(leadSNPs[leadSNPs[:,1].astype(int)==loci[i,0],5])

fout = open(filedir+"GenomicRiskLoci.txt", 'w')
fout.write("\t".join(locihead)+"\n")
for l in loci:
	fout.write("\t".join(l.astype(str))+"\n")
fout.close()
