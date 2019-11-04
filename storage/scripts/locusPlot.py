#!/usr/bin/python
import sys
import timeit
import re
import pandas as pd
import numpy as np
import json
import tabix

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results

def ArrayNotIn(a1, a2):
    tmp = np.where(np.in1d(a1, a2))[0]
    return list(set(range(0,len(a1)))-set(tmp))

start = timeit.default_timer()

filedir = sys.argv[1]
if re.match(".+\/$", filedir) is None:
	filedir += '/'
i = int(sys.argv[2])
i = i
Type = sys.argv[3]

snps = pd.read_csv(filedir+"snps.txt", sep="\t")
snpshead = list(snps.columns.values)
snps = snps.as_matrix()
ld = pd.read_csv(filedir+"ld.txt", sep="\t")
ld = ld.as_matrix()

ind = pd.read_csv(filedir+"IndSigSNPs.txt", sep="\t")
ind = ind.as_matrix()
lead = pd.read_csv(filedir+"leadSNPs.txt", sep="\t")
lead = lead.as_matrix()
loci = pd.read_csv(filedir+"GenomicRiskLoci.txt", sep="\t")
loci = loci.as_matrix()

if Type=="IndSigSNP":
	ls = str(ind[i, 2])
	l = int(ind[i, 1])
	ld = ld[ld[:,0]==ls]
	snps = snps[ArrayIn(snps[:,0], ld[:,1])]
	snps = np.c_[snps, [1]*len(snps)]
	snps[snps[:,0]==ls,len(snps[0])-1] = 2
	snps[ArrayIn(snps[:,0], lead[:,2]),len(snps[0])-1] = 3
	snps[ArrayIn(snps[:,0], loci[:,1]),len(snps[0])-1] = 4

elif Type=="leadSNP":
	if ";" in lead[i,8]:
		ls = np.array(lead[i,8].split(";"))
	else:
		ls = np.array(lead[i,8].split(":"))
	ls = snps[ArrayIn(snps[:,1], ls),0]
	ld = ld[ArrayIn(ld[:,0], ls)]
	snps = snps[ArrayIn(snps[:,0], ld[:,1])]
	snps = np.c_[snps, [1]*len(snps)]
	snps[ArrayIn(snps[:,0], ind[:,2]),len(snps[0])-1] = 2
	snps[ArrayIn(snps[:,0], lead[:,2]),len(snps[0])-1] = 3
	snps[ArrayIn(snps[:,0], loci[:,1]),len(snps[0])-1] = 4
else:
	# ls = np.array(loci[i,11].split(":"))
	# ls = snps[ArrayIn(snps[:,1], ls), 0]
	# ld = ld[ArrayIn(ld[:,0], ls)]
	snps = snps[snps[:,snpshead.index("GenomicLocus")]==i+1]
	# snps = snps[ArrayIn(snps[:,0], ld[:,1])]
	snps = np.c_[snps, [1]*len(snps)]
	snps[ArrayIn(snps[:,0], ind[:,2]),len(snps[0])-1] = 2
	snps[ArrayIn(snps[:,0], lead[:,2]),len(snps[0])-1] = 3
	snps[ArrayIn(snps[:,0], loci[:,1]),len(snps[0])-1] = 4

chrom = int(snps[0,2])
xMin = min(snps[:,3])
xMax = max(snps[:,3])

if "or" in snpshead and "se" in snpshead:
    snps = snps[:, [snpshead.index("pos"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("rsID"), snpshead.index("MAF"), snpshead.index("or"), snpshead.index("se")]]
    snpshead = ["pos", "gwasP", "ld", "r2", "IndSigSNP", "rsID", "MAF", "or", "se"]
elif "or" in snpshead:
    snps = snps[:, [snpshead.index("pos"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("rsID"), snpshead.index("MAF"), snpshead.index("or")]]
    snpshead = ["pos", "gwasP", "ld", "r2", "IndSigSNP", "rsID", "MAF", "or"]
elif "se" in snpshead:
    snps = snps[:, [snpshead.index("pos"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("rsID"), snpshead.index("MAF"), snpshead.index("se")]]
    snpshead = ["pos", "gwasP", "ld", "r2", "IndSigSNP", "rsID", "MAF", "se"]
else:
    snps = snps[:, [snpshead.index("pos"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("rsID"), snpshead.index("MAF")]]
    snpshead = ["pos", "gwasP", "ld", "r2", "IndSigSNP", "rsID", "MAF"]

# outfile = open(filedir+"locusPlot.txt", 'w')
# outfile.write("\t".join(snpshead)+"\n")
# for l in snps:
#     outfile.write("\t".join(l.astype(str))+"\n")
# outfile.close()

chrcol = 0
poscol = 1

tb = tabix.open(filedir+"all.txt.gz")
tb_snps = tb.querys(str(chrom)+":"+str(max([xMin-500000,0]))+"-"+str(xMax+500000))
allsnps = []
for l in tb_snps:
	allsnps.append([int(l[0]), int(l[1]), float(l[2])])
allsnps = np.array(allsnps)
allsnps = allsnps[ArrayNotIn(allsnps[:,poscol].astype(int), snps[:,3])]

out = {}
out["snps"] = [dict(zip(snpshead, l)) for l in snps]
out["allsnps"] = [[int(l[1]), float(l[2])] for l in allsnps]

print json.dumps(out)
