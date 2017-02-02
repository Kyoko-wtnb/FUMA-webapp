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
import tabix

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

if len(sys.argv)<2:
	sys.exit('ERROR: not enough arguments\nUSAGE ./gwas_file.py <filedir>')

start = time.time()

filedir = sys.argv[1]
if re.match("\/$", filedir) is None:
	filedir += '/'

##### config variables #####
cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = ConfigParser.RawConfigParser()
param.optionxform = str
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

if refcol is not None and altcol is None:
    altcol = refcol
    refcol = None

if pcol is None:
    sys.exit("P-value column was not found")
if (chrcol is None or poscol is None) and rsIDcol is None:
    sys.exit("Chromosome, position or rsID column was not found")

if param.get('inputfiles', 'orcol')=="NA" and orcol is not None:
    param.set('inputfiles', 'orcol', 'or')
if param.get('inputfiles', 'becol')=="NA" and becol is not None:
    param.set('inputfiles', 'becol', 'beta')
if param.get('inputfiles', 'secol')=="NA" and secol is not None:
    param.set('inputfiles', 'secol', 'se')

paramout = open(filedir+"params.config", 'w+')
param.write(paramout)
paramout.close()

if chrcol is not None and poscol is not None and rsIDcol is not None and altcol is not None and refcol is not None:
    dbSNPfile = cfg.get('data', 'dbSNP')
    rsID = pd.read_table(dbSNPfile+"/RsMerge146.txt", header=None)
    rsID = np.array(rsID)
    rsIDs = set(rsID[:,0])
    rsID = rsID[rsID[:,0].argsort()]

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

    gwasIn = open(gwas, 'r')
    gwasIn.readline()
    for l in gwasIn:
        if re.match("^#", l):
            next
        l = l.strip()
        l = l.split()
        if l[rsIDcol] in rsIDs:
            j = bisect_left(rsID, l[rsIDcol])
            l[rsIDcol] = rsID[j,1]
        l[chrcol] = l[chrcol].replace("chr", "")
        if re.match("x", l[chrcol], re.IGNORECASE):
            l[chrcol] = '23'
        if float(l[pcol]) < 1e-308:
            l[pcol] = str(1e-308)
        out.write("\t".join([l[chrcol], l[poscol], l[refcol].upper(), l[altcol].upper(), l[rsIDcol], l[pcol]]))
        if orcol is not None:
            out.write("\t"+l[orcol])
        if becol is not None:
            out.write("\t"+l[becol])
        if secol is not None:
            out.write("\t"+l[secol])
        if Ncol is not None:
            out.write("\t"+l[Ncol])
        out.write("\n")
    gwasIn.close
    out.close
    tempfile = filedir+"temp.txt"
    os.system("sort -k 1n -k 2n "+outSNPs+" > "+tempfile)
    os.system("mv "+tempfile+" "+outSNPs)

elif chrcol is not None and poscol is not None:

    dbSNPfile = cfg.get('data', 'dbSNP')

    def Tabix (chrom, start ,end, snps):
        snps = np.array(snps)

        poss = set(snps[:, poscol].astype(int))
        pos = snps[:, poscol].astype(int)

        tbfile = dbSNPfile+"/dbSNP146.chr"+str(chrom)+".vcf.gz"
        tb = tabix.open(tbfile)
        temp = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))

        out = open(outSNPs, 'a+')
        for l in temp:
            if int(l[1]) in poss:
                j = bisect_left(pos, int(l[1]))
                if refcol is not None and altcol is not None:
                    if (snps[j,refcol].upper()==l[3] and snps[j,altcol].upper()==l[4]) or (snps[j,refcol].upper()==l[4] and snps[j,altcol].upper()==l[3]):
                        out.write("\t".join([l[0],l[1], snps[j,refcol].upper(), snps[j,altcol].upper(), l[2], snps[j,pcol]]))
                        if orcol is not None:
                            out.write("\t"+l[orcol])
                        if becol is not None:
                            out.write("\t"+l[becol])
                        if secol is not None:
                            out.write("\t"+l[secol])
                        if Ncol is not None:
                            out.write("\t"+l[Ncol])
                        out.write("\n")
                elif altcol is not None:
                    if snps[j,altcol].upper()==l[3] or snps[j,altcol].upper()==l[4]:
                        a = "NA"
                        if snps[j,altcol]==l[3]:
                            a = l[4]
                        else:
                            a = l[3]
                        out.write("\t".join([l[0],l[1], a, snps[j,altcol].upper(), l[2], snps[j,pcol]]))
                        if orcol is not None:
                            out.write("\t"+l[orcol])
                        if becol is not None:
                            out.write("\t"+l[becol])
                        if secol is not None:
                            out.write("\t"+l[secol])
                        if Ncol is not None:
                            out.write("\t"+l[Ncol])
                        out.write("\n")
                else:
                    out.write("\t".join([l[0],l[1], l[3], l[4], l[2], snps[j,pcol]]))
                    if orcol is not None:
                        out.write("\t"+l[orcol])
                    if becol is not None:
                        out.write("\t"+l[becol])
                    if secol is not None:
                        out.write("\t"+l[secol])
                    if Ncol is not None:
                        out.write("\t"+l[Ncol])
                    out.write("\n")
        out.close

    tempfile = filedir + "temp.txt"
    os.system("sort -k "+str(chrcol+1)+"n -k "+str(poscol+1)+"n "+gwas+" > "+tempfile)
    os.system("mv "+tempfile+" "+gwas)

    cur_chr = 1
    minpos = 0
    maxpos = 0

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
    out.close()

    temp = []

    gwasIn = open(gwas, 'r')
    gwasIn.readline()
    for l in gwasIn:
        if re.match("^#", l):
            next
        l = l.strip()
        l = l.split()
        l[chrcol] = l[chrcol].replace("chr", "")
        if re.match("x", l[chrcol], re.IGNORECASE):
            l[chrcol] = '23'
        if float(l[pcol]) < 1e-308:
            l[pcol] = str(1e-308)

        if int(l[chrcol]) == cur_chr:
            if minpos==0:
                minpos = int(l[poscol])
            if int(l[poscol])-minpos<=1000000:
                maxpos = int(l[poscol])
                temp.append(l)
            else:
				Tabix(cur_chr, minpos, maxpos, temp)
				minpos = int(l[poscol])
				maxpos = int(l[poscol])
				temp = []
				temp.append(l)
        else:
            Tabix(cur_chr, minpos, maxpos, temp)
            cur_chr = int(l[chrcol])
            minpos = int(l[poscol])
            maxpos = int(l[poscol])
            temp = []
            temp.append(l)
    Tabix(cur_chr, minpos, maxpos, temp)

elif chrcol is None or poscol is None:
    print "Either chr or pos is not provided"
    gwas = pd.read_table(gwas, comment="#", delim_whitespace=True)
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
    dbSNPfile = cfg.get('data', 'dbSNP')
    rsID146 = open(dbSNPfile+"/RsMerge146.txt", 'r')
    for l in rsID146:
        l = l.strip()
        l = l.split()
        if l[0] in rsIDs:
            j = bisect_left(rsID, l[0])
            gwas[j,rsIDcol] = l[1]
    rsID146.close

    gwas = gwas[gwas[:,rsIDcol].argsort()]
    rsIDs = set(list(gwas[:, rsIDcol]))
    rsID = list(gwas[:, rsIDcol])
    checked = []

    for chrom in range(1,24):
        print "start chr"+str(chrom)
        # count = 0
        fin = gzip.open(dbSNPfile+"/dbSNP146.chr"+str(chrom)+".vcf.gz", 'rb')
        for l in fin:
			l = l.decode('ascii')
			l = l.strip()
			l = l.split()
			alt = l[4].split(",")

			if l[2] in rsIDs:
				checked.append(l[2])
				j = bisect_left(rsID, l[2])
				if(gwas[j,pcol]<1e-308):
				    gwas[j,pcol]=1e-308
				if altcol is not None and refcol is not None:
				    if (gwas[j,altcol].upper()==l[3] and gwas[j,refcol].upper() in alt) or gwas[j,altcol].upper() in alt and gwas[j,refcol].upper()==l[3]:
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
				    if gwas[j,altcol].upper()==l[3] or gwas[j,altcol].upper() in alt:
						if len(alt)>1 :
							continue

						a = "NA"
						if gwas[j,altcol].upper()==l[3]:
						    a=l[4]
						else:
						    a=l[3]

						out.write("\t".join([str(chrom), str(l[1]), a, gwas[j,altcol].upper(), l[2], str(gwas[j,pcol])]))
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
					if len(alt)>1:
						continue

					out.write("\t".join([str(chrom), str(l[1]), l[3], l[4], l[2], str(gwas[j,pcol])]))
					if orcol is not None:
					    out.write("\t"+str(gwas[j,orcol]))
					if becol is not None:
					    out.write("\t"+str(gwas[j,becol]))
					if secol is not None:
					    out.write("\t"+str(gwas[j,secol]))
					if Ncol is not None:
					    out.write("\t"+str(gwas[j,Ncol]))
					out.write("\n")
        gwas = np.delete(gwas, np.where(np.in1d(gwas[:,rsIDcol], checked)), 0)
        # gwas = np.delete(gwas, ArrayIn(gwas[:,rsIDcol], checked), 0)
        # print time.time() - temptime
        rsID = list(gwas[:, rsIDcol])
        checked = []
        if len(gwas)==0:
            break
    out.close
print time.time()-start
