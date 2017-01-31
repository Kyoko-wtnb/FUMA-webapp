#!/usr/bin/python
import sys
import os
import re
import gzip
import pandas as pd
import numpy as np
import ConfigParser
import time
from bisect import bisect_left

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
if len(sys.argv)<1:
	raise Exception('ERROR: not enough arguments\nUSAGE ./gwas_file.py <filedir>\n')

start = time.time()

filedir = sys.argv[1]
if re.match("\/$", filedir) is None:
	filedir += '/'

##### config variables #####
cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = ConfigParser.ConfigParser()
param.read(filedir+'params.config')

gwas = filedir+cfg.get('inputfiles', 'gwas')
outSNPs = filedir+"input.snps"
outMAGMA = filedir+"magma.in"

chrcol = param.get('inputfiles', 'chrcol').upper()
poscol = param.get('inputfiles', 'poscol').upper()
rsIDcol = param.get('inputfiles', 'rsIDcol').upper()
pcol = param.get('inputfiles', 'pcol').upper()
refcol = param.get('inputfiles', 'refcol').upper()
altcol = param.get('inputfiles', 'altcol').upper()
orcol = param.get('inputfiles', 'orcol').upper()
becol = param.get('inputfiles', 'becol').upper()
secol = param.get('inputfiles', 'secol').upper()
Ncol = param.get('params', 'Ncol').upper()

fin = open(gwas, 'r')
header = fin.readline()
while re.match("^#", header):
    header = fin.readline()
fin.close
header = header.strip()
header = header.split()

for i in range(0,len(header)):
    if chrcol == header[i].upper() or re.match("CHR$|^chromosome$|^chrom$", header[i], re.IGNORECASE):
        chrcol = i
    elif rsIDcol == header[i].upper() or re.match("SNP$|^MarkerName$|^rsID$|^snpid$", header[i], re.IGNORECASE):
        rsIDcol = i
    elif poscol == header[i].upper() or re.match("^BP$|^pos$|^position$", header[i], re.IGNORECASE):
        poscol = i
    elif altcol == header[i].upper() or re.match("^A1$|^Effect_allele$|^alt$|^allele1$|^alleleB$", header[i], re.IGNORECASE):
        altcol = i
    elif refcol == header[i].upper() or re.match("^A2$|^Non_Effect_allele$|^ref$|^allele2$|^alleleA$", header[i], re.IGNORECASE):
        refcol = i
    elif pcol == header[i].upper() or re.match("^P$|^pval$|^pvalue$|^p-value$|^p_value$|^frequentist_add_pvalue$", header[i], re.IGNORECASE):
        pcol = i
    elif orcol == header[i].upper() or re.match("^or$", header[i], re.IGNORECASE):
        orcol = i
    elif becol == header[i].upper() or re.match("^beta$", header[i], re.IGNORECASE):
        becol = i
    elif secol == header[i].upper() or re.match("^se$", header[i], re.IGNORECASE):
        secol = i
    elif Ncol == header[i].upper() or re.match("^N$", header[i], re.IGNORECASE):
        Ncol = i

if chrcol=="NA":
    chrcol = None
if rsIDcol=="NA":
    rsIDcol = None
if poscol=="NA":
    poscol = None
if refcol=="NA":
    refcol = None
if altcol=="NA":
    altcol = None
if pcol=="NA":
    pcol = None
if orcol=="NA":
    orcol = None
if secol=="NA":
    secol = None
if becol=="NA":
    becol = None
if Ncol=="NA":
    Ncol = None

if chrcol is None or poscol is None:
    print "Either chr or pos is not provided"
    gwas = pd.read_table(gwas, delim_whitespace=True)
    gwas = gwas.as_matrix()
    gwas = gwas[gwas[:,rsIDcol].argsort()]
    # gwas = gwas[0:1000000]

    out = open(outSNPs, 'w')
    out.write("chr\tbp\tref\talt\trsID\tp")
    if orcol is not None:
        out.write("\tor")
    if becol is not None:
        out.write("\tbeta")
    if secol is not None:
        out.write("\tse")
    if Ncol is not None:
        out.write("\tN")
    out.write("\n")

    rsIDs = set(list(gwas[:, rsIDcol]))
    rsID = list(gwas[:, rsIDcol])
    checked = []
    dbSNPfile = cfg.get('data', 'dbSNP')
    rsID146 = open(dbSNPfile+"/RsMerge146.txt", 'r')
    for l in rsID146:
        l = l.strip()
        l = l.split()
        if l[0] in rsIDs:
            j = bisect_left(rsID, l[0])
            gwas[j,rsIDcol] = l[1]
    rsID146.close

    for chrom in range(1,24):
        print "start chr"+str(chrom)
        # count = 0
        fin = gzip.open(dbSNPfile+"/dbSNP146.chr"+str(chrom)+".vcf.gz", 'rb')
        for l in fin:
            l = l.decode('ascii')
            l = l.strip()
            l = l.split()

            if l[2] in rsIDs:
                # count += 1
                checked.append(l[2])
                # temptime = time.time()
                # j = rsID.index(l[2])
                j = bisect_left(rsID, l[2])
                # print time.time()-temptime
                if altcol is not None and refcol is not None:
                    if (gwas[j,altcol].upper()==l[3] and gwas[j,refcol].upper()==l[4]) or gwas[j,altcol].upper()==l[4] and gwas[j,refcol].upper()==l[3]:
                        out.write("\t".join([str(chrom), str(l[1]), gwas[j,refcol].upper(), gwas[j,altcol].upper(), l[2], str(gwas[j,pcol])]))
                        if orcol is not None:
                            out.write("\t"+str(gwas[j,orcol]))
                        if becol is not None:
                            out.write("\t"+str(gwas[j,becol]))
                        if secol is not None:
                            out.write("\t"+str(gwas[j,secol]))
                        if Ncol is not None:
                            out.write("\t"+str(gwas[j,Ncol]))
                        out.write("\n")
                elif altcol is not None:
                    if gwas[j,altcol]==l[3] or gwas[j,altcol]==l[4]:
                        out.write("\t".join([str(chrom), str(l[1]), gwas[j,refcol].upper(), gwas[j,altcol].upper(), l[2], str(gwas[j,pcol])]))
                        if orcol is not None:
                            out.write("\t"+str(gwas[j,orcol]))
                        if becol is not None:
                            out.write("\t"+str(gwas[j,becol]))
                        if secol is not None:
                            out.write("\t"+str(gwas[j,secol]))
                        if Ncol is not None:
                            out.write("\t"+str(gwas[j,Ncol]))
                        out.write("\n")
                else:
                    out.write("\t".join([str(chrom), str(l[1]), gwas[j,refcol].upper(), gwas[j,altcol].upper(), l[2], str(gwas[j,pcol])]))
                    if orcol is not None:
                        out.write("\t"+str(gwas[j,orcol]))
                    if becol is not None:
                        out.write("\t"+str(gwas[j,becol]))
                    if secol is not None:
                        out.write("\t"+str(gwas[j,secol]))
                    if Ncol is not None:
                        out.write("\t"+str(gwas[j,Ncol]))
                    out.write("\n")
                # gwas = np.delete(gwas, (j), axis=0)
                # rsID = list(gwas[:, rsIDcol])
                # if count%100000==0 and count>0:
                #     print count
                    # gwas = gwas[ArrayNotIn(gwas[:,rsIDcol], checked)]
                    # temptime = time.time()
        gwas = np.delete(gwas, np.where(np.in1d(gwas[:,rsIDcol], checked)), 0)
        # gwas = np.delete(gwas, ArrayIn(gwas[:,rsIDcol], checked), 0)
        # print time.time() - temptime
        rsID = list(gwas[:, rsIDcol])
        checked = []
        if len(gwas)==0:
            break
out.close
print time.time()-start
