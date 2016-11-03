#!/usr/bin/python
import sys
import os
import json
import pandas as pd
import numpy as np
import tabix

def skip_commnet(infile, prefix, separator):
    if os.stat(infile).st_size==0:
        raise ValueError("File is empty")
    with open(infile) as f:
        pos = 0
        cur_line = f.readline()
        while cur_line.startswith(prefix):
            pos = f.tell()
            cur_line = f.readline()
        f.seek(pos)
        return pd.read_table(f, sep=separator)

def filter_array(array1, array2):
    results = []
    for i in range(0, len(array1)):
        if array1[i] in array2:
            results.append(i)
    return results

params = json.loads(sys.argv[1])
print params['filedir'], "\n"

def getLD(gwas, chrom, leadP, canSNPs, leadSNPs, intervals, chrcol, poscol, pcol, refcol, altcol):
    lSNP = []
    resiong = []
    start = 0
    end = 0
    for i in range(0, len(gwas)):
        if(gwas[i,pcol]<=leadP):
            lSNP.append(i)
            if end==0:
                end = gwas[i,poscol].astype(int)
            if start==0:
                start = gwas[i,poscol].astype(int)
            if(gwas[i,poscol].astype(int)-start<=1000000):
                end = gwas[i,pcol].astype(int)
            else:
                regions.append(str(chrom)+":"+str(start)+"-"+str(end))

MHCstart = 29624758
MHCend = 33160276
if not params['extMHC']=="NA":
    temp = params['extMHC'].split("-")
    MHCstart = temp[0]
    MHCend = temp[1]

## read gwas input file
GWAS = skip_commnet(params['filedir']+"/input.gwas", "#", "\s+")
header = list(GWAS.columns.values)
print header
rsIDcol = None
chrcol = None
poscol = None
pcol = None
refcol = None
altcal = None
if params['gwasformat']=="Plain":
    for i in range(0, len(header)):
        if header[i] == "SNP":
            rsIDcol = i
        elif header[i] == "CHR":
            chrcol = i
        elif header[i] == "BP":
            poscol = i
        elif header[i] == "A1":
            altcol = i
        elif header[i] == "A2":
            refcol == i
        elif header[i] == "P":
            pcol = i
elif params['gwasformat']=="PLINK":
    for h in header:
        if header[i] == "SNP":
            rsIDcol = i
        elif header[i] == "CHR":
            chrcol = i
        elif header[i] == "BP":
            poscol = i
        elif header[i] == "A1":
            altcol = i
        elif header[i] == "A2":
            refcol == i
        elif header[i] == "P":
            pcol = i
elif params['gwasformat']=="GCTA":
    for h in header:
        if header[i] == "SNP":
            rsIDcol = i
        elif header[i] == "Chr":
            chrcol = i
        elif header[i] == "bp":
            poscol = i
        elif header[i] == "OtherAllele":
            altcol = i
        elif header[i] == "ReferenceAllele":
            refcol == i
        elif header[i] == "p":
            pcol = i
elif params['gwasformat']=="SNPTEST":
    for h in header:
        if header[i] == "rsid":
            rsIDcol = i
        elif header[i] == "chromosome":
            chrcol = i
        elif header[i] == "position":
            poscol = i
        elif header[i] == "alleleB":
            altcol = i
        elif header[i] == "alleleA":
            refcol == i
        elif header[i] == "frequentist_add_pvalue":
            pcol = i

GWAS = GWAS.as_matrix()
canSNPs = []
canSNPs.append(['uniqID', 'chr', 'pos', 'ref', 'alt', 'rsID', 'MAF', 'P', 'annot'])
checked_rsID=[]
leadSNPs = []
leadSNPs.append(['uniqID', 'chr', 'pos', 'rsID', 'P', 'nSNPs'])
intervals = []
intervals.append(['uniqID', 'chr', 'pos', 'ref', 'alt', 'rsID', 'MAF', 'P', 'start', 'end', 'nLeadSNPs', 'leadSNPs', 'nSNPs',])
annot = []

# ldfile = "/media/sf_SAMSUNG/1KG/Phase3/EUR/EUR.chr1.ld.gz"
## 0.chr 1.bp 2.rsID1 3.rsID2 4.r2
# snofile = "/media/sf_SAMSUNG/1KG/Phase3/EUR_annot/chr1.data.txt.gz"
## 0.chr 1.bp 2.ref 3.alt 4.rsID 5.MAF 6.uniqID 7.CADD 8.RDB 9-52.GTEx 53-179.Chr15

ldout = open(params['filedir']+"/ld.txt", 'w')
minP = min(GWAS[:,pcol])
c=0
while minP <= float(params['leadP']):
    c += 1
    p = GWAS[:,pcol]
    i = np.where(p == min(p))

    chrom = GWAS[i,chrcol][0][0]
    pos = GWAS[i,poscol][0][0]
    # print pos
    ldfile = "/media/sf_SAMSUNG/1KG/Phase3/EUR/EUR.chr"+str(chrom)+".ld.gz"
    # print ldfile
    snpfile = "/media/sf_SAMSUNG/1KG/Phase3/EUR_annot/chr"+str(chrom)+".data.txt.gz"
    ldtb = tabix.open(ldfile)
    print "ld tabix "+str(chrom)+":"+str(pos-1)+"-"+str(pos)
    ldquery = ldtb.query(str(chrom), pos-1, pos)
    ld = []
    for l in ldquery:
        ld.append(l)
    del ldquery
    ld = np.array(ld)
    ld = ld[ld[:,1].astype(int)==pos]

    start = pos-1000000
    end = pos+1000000
    snptb = tabix.open(snpfile)
    snpsquery = snptb.query(str(chrom), start, end)
    snps = []
    for l in snpsquery:
        snps.append(l)
    del snpsquery
    snps = np.array(snps)

    if params['KGSNPs']!=1:
        snps = snps[filter_array(snps[:,1].astype(int), GWAS[:,poscol])]
    snps = snps[snps[:,5].astype(float)>=float(params['maf'])]

    if pos not in snps[:,1].astype(int):
        GWAS = np.delete(GWAS, i, 0)
        continue

    count = 0
    leadSNP_i = np.where(snps[:,1].astype(int)==GWAS[i,poscol])
    ldout.write(str(snps[snps[:,4]==ld[0,2],6][0])+"\t"+str(snps[snps[:,4]==ld[0,2],6][0])+"\t1\n")
    if snps[leadSNP_i,4][0] not in checked_rsID:
    	annot = "\t".join(snps[leadSNP_i,7:9][0][0])
    	annot = "\t".join([annot]+list(snps[leadSNP_i,53:][0][0]))
    	canSNPs.append(list(snps[leadSNP_i,[6,0,1,2,3,4,5]][0])+[minP, annot])
    	checked_rsID.append(snps[leadSNP_i,4][0])

    GWAS = np.delete(GWAS, i, 0)

    for l in ld:
        if l[4].astype(float) < float(params['r2']):
            continue
        if l[1].astype(int) != pos:
            print "pos skip"
            continue
        if l[3] not in snps[:,4]:
            print "maf skip"
            continue
        # if params['KGSNPs']!=1:
        #     if l[1] not in GWAS[GWAS[:,0]==chrom,1]:
        #         continue
        count += 1
        ldout.write(str(snps[snps[:,4]==l[2],6][0])+"\t"+str(snps[snps[:,4]==l[3],6][0])+"\t"+str(l[4])+"\n")
        j = np.where(snps[:,4]==l[3])

        if snps[j,4][0] not in checked_rsID:
        	if snps[j, 1].astype(int) in GWAS[GWAS[:,0]==chrom, poscol]:
            		pval = GWAS[GWAS[:,0]==chrom and GWAS[:,1]==snps[snps[:,4]==l[3],1][0],pcol]
            		if pval > float(params['gwasP']):
            	    		np.delete(GWAS, j, 0)
            	    		continue
        	else:
        	    pval = "NA"
        	annot = "\t".join(snps[j,7:9][0][0])
        	annot = "\t".join([annot]+list(snps[j,53:][0][0]))
        	# canSNPs.append([str(snps[j,6][0]), int(snps[j,0][0]), int(snps[j,1][0]), str(snps[j,2][0]), str(snps[j,3][0]), str(snps[j,4][0]), str(snps[j,5][0]), pval, annot])
        	canSNPs.append(list(snps[j,[6,0,1,2,3,4,5]][0])+[pval, annot])
        	GWAS = np.delete(GWAS, j, 0)
        	checked_rsID.append(snps[j,4][0])

    # leadSNPs.append([str(snps[leadSNP_i,6][0]), int(snps[leadSNP_i,0][0]), int(snps[leadSNP_i,1][0]), snps[leadSNP_i,4][0], minP, count])
    leadSNPs.append(list(snps[leadSNP_i, [6,0,1,4]][0])+[minP, count])
    minP = min(GWAS[:,5])
    print len(GWAS)
    if c>10:
        break
del GWAS

snpsout = open(params['filedir']+"/snps.txt", 'w')
snpsout.write("\t".join(canSNPs[0])+"\n")
canSNPs = canSNPs[1:]
# canSNPs = canSNPs.sort(axis=1)
# canSNPs = canSNPs.sort(axis=2)
canSNPs = np.array(canSNPs)
print canSNPs[0:3]
canSNPs = canSNPs[canSNPs[:,1].astype(int).argsort()]
canSNPs = canSNPs[canSNPs[:,2].astype(int).argsort()]
for l in canSNPs:
    snpsout.write("\t".join(l)+"\n")
