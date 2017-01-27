#!/usr/bin/python
import sys
import os
import re
import pandas as pd
import numpy as np
import math

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results

def ArrayNotIn(a1, a2):
    temp = np.where(np.in1d(a1, a2))[0]
    a1 = range(0, len(a1))
    results = []
    for i in a1:
        if i not in temp:
            results.append(i)
    return results

filedir = sys.argv[1]
if re.match("\/$", filedir) is None:
	filedir += '/'

filedir = sys.argv[1]
Type = sys.argv[2]
i = int(sys.argv[3])
GWAS = int(sys.argv[4])
CADD = int(sys.argv[5])
RDB = int(sys.argv[6])
eqtlplot = int(sys.argv[7])
Chr15 = int(sys.argv[8])
Chr15ts = sys.argv[9]
Chr15all = 0

if Chr15==1:
    Chr15ts = Chr15ts.split(":")
    for c in Chr15ts:
        if c=="all":
            Chr15all = 1
            break

# cfg = ConfigParser.ConfigParser()
# cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

snps = pd.read_table(filedir+"snps.txt", sep="\t")
snpshead = list(snps.columns.values)
snps = snps.as_matrix()
ld = pd.read_table(filedir+"ld.txt", sep="\t")
ld = ld.as_matrix()
ind = pd.read_table(filedir+"IndSigSNPs.txt", sep="\t")
ind = ind.as_matrix()
lead = pd.read_table(filedir+"leadSNPs.txt", sep="\t")
lead = lead.as_matrix()
loci = pd.read_table(filedir+"GenomicRiskLoci.txt", sep="\t")
loci = loci.as_matrix()

# if Type=="leadSNP":
# 	lead = pd.read_table(filedir+"IndSigSNPs.txt", sep="\t")
# 	lead = lead.as_matrix()
# 	ls = str(lead[i, 2])
# 	loci = int(lead[i,1])
# 	ld = ld[ld[:,0]==ls]
# 	snps = snps[snps[:,snpshead.index("GenomicLocus")]==loci]
# 	snps = np.c_[snps, [0]*len(snps)]
# 	snps[ArrayIn(snps[:,0], ld[:,1]),len(snps[0])-1] = 1
# 	snps[snps[:,0]==ls,len(snps[0])-1] = 2
# 	snps = snps[snps[:, len(snps[0])-1]>0]
# else:
#     loci = pd.read_table(filedir+"GenomicRiskLoci.txt", sep="\t")
#     loci = loci.as_matrix()
#     ls = np.array(loci[i,11].split(":"))
#     ls = snps[ArrayIn(snps[:,1], ls), 0]
#     ld = ld[ArrayIn(ld[:,0], ls)]
#     snps = snps[snps[:,snpshead.index("GenomicLocus")]==i+1]
#     snps = snps[ArrayIn(snps[:,0], ld[:,1])]
#     snps = np.c_[snps, [1]*len(snps)]
#     snps[ArrayIn(snps[:,0],ls),len(snps[0])-1] = 2
if type=="IndSigSNP":
	ls = str(ind[i, 2])
	l = int(ind[i, 1])
	ld = ld[ld[:,0]==ls]
	snps = snps[ArrayIn(snps[:,0], ld[:,1])]
	snps = np.c_[snps, [1]*len(snps)]
	snps[snps[:,0]==ls,len(snps[0])-1] = 2
	snps[ArrayIn(snps[:,0], lead[:,2]),len(snps[0])-1] = 3
	snps[ArrayIn(snps[:,0], loci[:,1]),len(snps[0])-1] = 4

elif type=="leadSNP":
	ls = np.array(lead[i,8].split(":"))
	ls = snps[ArrayIn(snps[:,1], ls),0]
	ld = ld[ArrayIn(ld[:,0], ls)]
	snps = snps[ArrayIn(snps[:,0], ld[:,1])]
	snps = np.c_[snps, [1]*len(snps)]
	snps[ArrayIn(snps[:,0], ind[:,2]),len(snps[0])-1] = 2
	snps[ArrayIn(snps[:,0], lead[:,2]),len(snps[0])-1] = 3
	snps[ArrayIn(snps[:,0], loci[:,1]),len(snps[0])-1] = 4
else:
	ls = np.array(loci[i,11].split(":"))
	ls = snps[ArrayIn(snps[:,1], ls), 0]
	ld = ld[ArrayIn(ld[:,0], ls)]
	snps = snps[snps[:,snpshead.index("GenomicLocus")]==i+1]
	snps = snps[ArrayIn(snps[:,0], ld[:,1])]
	snps = np.c_[snps, [1]*len(snps)]
	snps[ArrayIn(snps[:,0], ind[:,2]),len(snps[0])-1] = 2
	snps[ArrayIn(snps[:,0], lead[:,2]),len(snps[0])-1] = 3
	snps[ArrayIn(snps[:,0], loci[:,1]),len(snps[0])-1] = 4

chrom = int(snps[0,2])
xMin = min(snps[:,3])
xMax = max(snps[:,3])

if "or" in snpshead and "se" in snpshead:
    snps = snps[:, [snpshead.index("uniqID"), snpshead.index("chr"), snpshead.index("pos"), snpshead.index("rsID"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("MAF"), snpshead.index("CADD"), snpshead.index("RDB"), snpshead.index("nearestGene"), snpshead.index("func"), snpshead.index("or"), snpshead.index("se")]]
    snpshead = ["uniqID", "chr","pos", "rsID", "gwasP", "ld", "r2", "IndSigSNP", "MAF", "CADD", "RDB", "nearestGene", "func", "or", "se"]
elif "or" in snpshead:
    snps = snps[:, [snpshead.index("uniqID"), snpshead.index("chr"), snpshead.index("pos"), snpshead.index("rsID"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("MAF"), snpshead.index("CADD"), snpshead.index("RDB"), snpshead.index("nearestGene"), snpshead.index("func"), snpshead.index("or")]]
    snpshead = ["uniqID", "chr","pos", "rsID", "gwasP", "ld", "r2", "IndSigSNP", "MAF", "CADD", "RDB", "nearestGene", "func", "or"]
elif "se" in snpshead:
    snps = snps[:, [snpshead.index("uniqID"), snpshead.index("chr"), snpshead.index("pos"), snpshead.index("rsID"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("MAF"), snpshead.index("CADD"), snpshead.index("RDB"), snpshead.index("nearestGene"), snpshead.index("func"), snpshead.index("se")]]
    snpshead = ["uniqID", "chr","pos", "rsID", "gwasP", "ld", "r2", "IndSigSNP", "MAF", "CADD", "RDB", "nearestGene", "func", "se"]
else:
    snps = snps[:, [snpshead.index("uniqID"), snpshead.index("chr"), snpshead.index("pos"), snpshead.index("rsID"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("MAF"), snpshead.index("CADD"), snpshead.index("RDB"), snpshead.index("nearestGene"), snpshead.index("func")]]
    snpshead = ["uniqID", "chr","pos", "rsID", "gwasP", "ld", "r2", "IndSigSNP", "MAF", "CADD", "RDB", "nearestGene", "func"]

snps[:, snpshead.index("RDB")] = snps[:, snpshead.index("RDB")].astype(str)
snps[snps[:, snpshead.index("RDB")]=="nan", snpshead.index("RDB")]=["NA"]

chrcol = 0
poscol = 1

temp = pd.read_table(filedir+"all.txt", sep="\t")
temp = temp.as_matrix()
temp = temp[temp[:,chrcol]==chrom]
temp = temp[temp[:,poscol]>=xMin-500000]
temp = temp[temp[:,poscol]<=xMax+500000]

temp = temp[ArrayNotIn(temp[:,poscol], snps[:,3])]

outfile = open(filedir+"temp.txt", 'w')
outfile.write("chr\tpos\tgwasP\n")
for l in temp:
    outfile.write(str(int(l[0]))+"\t"+str(int(l[1]))+"\t"+str(l[2])+"\n")

if Chr15==1:
    annot = pd.read_table(filedir+"annot.txt", sep="\t")
    annothead = list(annot.columns.values)
    annot = annot.as_matrix()
    annot = annot[ArrayIn(annot[:,0], snps[:,0])]

    if Chr15all==1:
        Chr15ts = list(annothead[3:len(annothead)])
    for c in Chr15ts:
        snps = np.c_[snps, annot[:,annothead.index(c)]]
        snpshead.append(c)

if eqtlplot==1:
    eqtl = pd.read_table(filedir+"eqtl.txt", sep="\t")
    eqtlhead = list(eqtl.columns.values)
    eqtl = eqtl.as_matrix()
    eqtl = eqtl[ArrayIn(eqtl[:,0], snps[:,0])]
    snps = np.c_[snps, ["NA"]*len(snps)]
    snpshead.append("eqtl")

    for l in range(0,len(snps)):
        if snps[l,0] in eqtl[:,0]:
            temp = eqtl[eqtl[:,0]==snps[l,0]]
            out = []
            for e in temp:
                out.append(":".join(e.astype(str)[[1,2,10,5,7]]))
            snps[l, len(snps[0])-1] = "</br>".join(out)
    outfile = open(filedir+"eqtlplot.txt", 'w')
    outfile.write("\t".join(eqtlhead)+"\n")
    for j in eqtl:
        outfile.write("\t".join(j.astype(str))+"\n")

outfile = open(filedir+"annotPlot.txt", 'w')
outfile.write("\t".join(snpshead)+"\n")
for l in snps:
    outfile.write("\t".join(l.astype(str))+"\n")
outfile.close()
