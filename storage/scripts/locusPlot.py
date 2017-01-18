#!/usr/bin/python
import sys
import timeit
import re
import pandas as pd
import numpy as np

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

start = timeit.default_timer()

filedir = sys.argv[1]
if re.match("\/$", filedir) is None:
	filedir += '/'
i = int(sys.argv[2])
i = i
type = sys.argv[3]

snps = pd.read_table(filedir+"snps.txt", sep="\t")
snpshead = list(snps.columns.values)
snps = snps.as_matrix()
ld = pd.read_table(filedir+"ld.txt", sep="\t")
ld = ld.as_matrix()

if type=="leadSNP":
    lead = pd.read_table(filedir+"leadSNPs.txt", sep="\t")
    lead = lead.as_matrix()
    ls = str(lead[i, 1])
    loci = int(lead[i, 8])
    ld = ld[ld[:,0]==ls]

    snps = snps[snps[:,snpshead.index("Interval")]==loci]
    snps = np.c_[snps, [0]*len(snps)]
    snps[ArrayIn(snps[:,0], ld[:,1]),len(snps[0])-1] = 1
    snps[snps[:,0]==ls,len(snps[0])-1] = 2
    snps = snps[snps[:, len(snps[0])-1]>0]
else:
    loci = pd.read_table(filedir+"intervals.txt", sep="\t")
    loci = loci.as_matrix()
    ls = np.array(loci[i,9].split(":"))
    ls = snps[ArrayIn(snps[:,1], ls), 0]
    ld = ld[ArrayIn(ld[:,0], ls)]
    snps = snps[snps[:,snpshead.index("Interval")]==i+1]
    snps = snps[ArrayIn(snps[:,0], ld[:,1])]
    snps = np.c_[snps, [1]*len(snps)]
    snps[ArrayIn(snps[:,0],ls),len(snps[0])-1] = 2

chrom = int(snps[0,2])
xMin = min(snps[:,3])
xMax = max(snps[:,3])

if "or" in snpshead and "se" in snpshead:
    snps = snps[:, [snpshead.index("pos"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("rsID"), snpshead.index("MAF"), snpshead.index("or"), snpshead.index("se")]]
    snpshead = ["pos", "gwasP", "ld", "r2", "rsID", "MAF", "or", "se"]
elif "or" in snpshead:
    snps = snps[:, [snpshead.index("pos"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("rsID"), snpshead.index("MAF"), snpshead.index("or")]]
    snpshead = ["pos", "gwasP", "ld", "r2", "rsID", "MAF", "or"]
elif "se" in snpshead:
    snps = snps[:, [snpshead.index("pos"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("rsID"), snpshead.index("MAF"), snpshead.index("se")]]
    snpshead = ["pos", "gwasP", "ld", "r2", "rsID", "MAF", "se"]
else:
    snps = snps[:, [snpshead.index("pos"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("rsID"), snpshead.index("MAF")]]
    snpshead = ["pos", "gwasP", "ld", "r2", "rsID", "MAF"]

outfile = open(filedir+"locusPlot.txt", 'w')
outfile.write("\t".join(snpshead)+"\n")
for l in snps:
    outfile.write("\t".join(l.astype(str))+"\n")
outfile.close()

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

stop = timeit.default_timer()
print stop - start
