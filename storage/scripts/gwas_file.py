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
import csv

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

##### detect file delimiter from the header #####
def DetectDelim(header):
	sniffer = csv.Sniffer()
	dialect = sniffer.sniff(header)
	return dialect.delimiter

##### check argument #####
if len(sys.argv)<2:
	sys.exit('ERROR: not enough arguments\nUSAGE ./gwas_file.py <filedir>')

##### start time #####
start = time.time()

##### add '/' to the filedir #####
filedir = sys.argv[1]
if re.match(".+\/$", filedir) is None:
	filedir += '/'

##### config variables #####
cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = ConfigParser.RawConfigParser()
param.optionxform = str
param.read(filedir+'params.config')

##### check format of pre-defined lead SNPS and genomic regions if provided #####
leadfile = param.get('inputfiles', 'leadSNPsfile')
regionfile = param.get('inputfiles', 'regionsfile')
if leadfile != "NA":
	leadfile = filedir+"input.lead"
	tmp = pd.read_table(leadfile, sep=delim)
	tmp = tmp.as_matrix()
	if len(tmp)==0 or len(tmp[0])<3:
		sys.exit("Input lead SNPs file does not have enought columns.")

if regionfile != "NA":
	regionfile = filedir+"input.regions"
	tmp = pd.read_table(regionfile, sep=delim)
	tmp = tmp.as_matrix()
	if len(tmp)==0 or len(tmp[0])<3:
		sys.exit("Input genomic region file does not have enought columns.")

##### prepare parameters #####
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

##### get header of sum stats #####
fin = open(gwas, 'r')
header = fin.readline()
while re.match("^#", header):
    header = fin.readline()
fin.close()
delim = DetectDelim(header)
header = header.strip().split(delim)
nheader = len(header)

##### detect column index #####
# prioritize user defined colum name
# then automatic detection
checkedheader = []
for i in range(0,len(header)):
    if chrcol == header[i].upper():
	chrcol = i
	checkedheader.append(chrcol)
    elif rsIDcol == header[i].upper():
        rsIDcol = i
        checkedheader.append(rsIDcol)
    elif poscol == header[i].upper():
        poscol = i
        checkedheader.append(poscol)
    elif altcol == header[i].upper():
        altcol = i
        checkedheader.append(altcol)
    elif refcol == header[i].upper():
        refcol = i
        checkedheader.append(refcol)
    elif pcol == header[i].upper():
        pcol = i
        checkedheader.append(pcol)
    elif orcol == header[i].upper():
        orcol = i
        checkedheader.append(orcol)
    elif becol == header[i].upper():
        becol = i
        checkedheader.append(becol)
    elif secol == header[i].upper():
        secol = i
        checkedheader.append(becol)
    elif Ncol == header[i].upper():
        Ncol = i
        checkedheader.append(Ncol)
for i in range(0, len(header)):
	if i in checkedheader:
		continue
	if re.match("CHR$|^chromosome$|^chrom$", header[i], re.IGNORECASE):
		chrcol = i
	elif re.match("SNP$|^MarkerName$|^rsID$|^snpid$", header[i], re.IGNORECASE):
		rsIDcol = i
	elif re.match("^BP$|^pos$|^position$", header[i], re.IGNORECASE):
		poscol = i
	elif re.match("^A1$|^Effect_allele$|^alt$|^allele1$|^alleleB$", header[i], re.IGNORECASE):
		altcol = i
	elif re.match("^A2$|^Non_Effect_allele$|^ref$|^allele2$|^alleleA$", header[i], re.IGNORECASE):
		refcol = i
	elif re.match("^P$|^pval$|^pvalue$|^p-value$|^p_value$|^frequentist_add_pvalue$", header[i], re.IGNORECASE):
		pcol = i
	elif re.match("^or$", header[i], re.IGNORECASE):
		orcol = i
	elif re.match("^beta$", header[i], re.IGNORECASE):
		becol = i
	elif re.match("^se$", header[i], re.IGNORECASE):
		secol = i
	elif re.match("^N$", header[i], re.IGNORECASE):
		Ncol = i

user_header = []
if chrcol=="NA":
    chrcol = None
else:
	user_header.append(chrcol)
if rsIDcol=="NA":
    rsIDcol = None
else:
	user_header.append(rsIDcol)
if poscol=="NA":
    poscol = None
else:
	user_header.append(poscol)
if refcol=="NA":
    refcol = None
else:
	user_header.append(refcol)
if altcol=="NA":
    altcol = None
else:
	user_header.append(altcol)
if pcol=="NA":
    pcol = None
else:
	user_header.append(pcol)
if orcol=="NA":
    orcol = None
else:
	user_header.append(orcol)
if secol=="NA":
    secol = None
else:
	user_header.append(secol)
if becol=="NA":
    becol = None
else:
	user_header.append(becol)
if Ncol=="NA":
    Ncol = None
else:
	user_header.append(Ncol)

##### Undetected header #####
# return error only if any of the user input colum names does not exits and not automatically detected
if not all([type(x) is int for x in user_header]):
	bl = [type(x) is not int for x in user_header]
	user_header = ", ".join([user_header[i] for i,x in enumerate(bl) if x])
	sys.exit("The following header(s) was not detected in your input file: "+user_header)

##### allele column check #####
# if only one allele is defined, this has to be alt (effect) allele
if refcol is not None and altcol is None:
    altcol = refcol
    refcol = None

##### Mandatory header check #####
if pcol is None:
    sys.exit("P-value column was not found")
if (chrcol is None or poscol is None) and rsIDcol is None:
    sys.exit("Chromosome, position or rsID column was not found")

##### Rewrite params.config if optional headers were detected #####
if param.get('inputfiles', 'orcol')=="NA" and orcol is not None:
    param.set('inputfiles', 'orcol', 'or')
if param.get('inputfiles', 'becol')=="NA" and becol is not None:
    param.set('inputfiles', 'becol', 'beta')
if param.get('inputfiles', 'secol')=="NA" and secol is not None:
    param.set('inputfiles', 'secol', 'se')

paramout = open(filedir+"params.config", 'w+')
param.write(paramout)
paramout.close()

##### Process input gwas sum stats #####
# when all columns are provided
# In this case, if the rsID columns is wrongly labeled, it will be problem later (not checked here)
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
        l = l.strip().split(delim)
        if len(l) < nheader:
			continue
        if l[pcol]=="":
			continue
        if l[rsIDcol] in rsIDs:
            j = bisect_left(rsID[:,0], l[rsIDcol])
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
    gwasIn.close()
    out.close()
    tempfile = filedir+"temp.txt"
    os.system("sort -k 1n -k 2n "+outSNPs+" > "+tempfile)
    os.system("mv "+tempfile+" "+outSNPs)

# if both chr and pos are provided
elif chrcol is not None and poscol is not None:
	dbSNPfile = cfg.get('data', 'dbSNP')

	##### tabix dbSNP to get rsID nad alleles #####
	def Tabix (chrom, start ,end, snps):
		snps = np.array(snps)

		poss = set(snps[:, poscol].astype(int))
		pos = snps[:, poscol].astype(int)

		tbfile = dbSNPfile+"/dbSNP146.chr"+str(chrom)+".vcf.gz"
		tb = tabix.open(tbfile)
		temp = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))

		out = open(outSNPs, 'a+')

		# when rsID is the only missing column, keep all SNPs in input file
		# assigned rsID for only SNPs that exists in dbSNP
		if refcol is not None and altcol is not None:
			dbSNP = []
			for l in temp:
				dbSNP.append(l)
			dbSNP = np.array(dbSNP)
			poss = set(dbSNP[:,1].astype(int))
			pos = dbSNP[:,1].astype(int)
			for l in snps:
				if int(l[poscol]) in poss:
					j = bisect_left(pos, int(l[poscol]))
					if (l[refcol].upper()==dbSNP[j,3] and l[altcol].upper()==dbSNP[j,4]) or (l[refcol].upper()==dbSNP[j,4] and l[altcol].upper()==dbSNP[j,3]):
						out.write("\t".join([dbSNP[j,0],dbSNP[j,1], l[refcol].upper(), l[altcol].upper(), dbSNP[j,2], l[pcol]]))
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
						a = [l[refcol], l[altcol]]
						a.sort()
						out.write("\t".join([l[chrcol],l[poscol], l[refcol].upper(), l[altcol].upper(), ":".join([l[chrcol], l[poscol]]+a), l[pcol]]))
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
					a = [l[refcol], l[altcol]]
					a.sort()
					out.write("\t".join([l[chrcol],l[poscol], l[refcol].upper(), l[altcol].upper(), ":".join([l[chrcol], l[poscol]]+a), l[pcol]]))
					if orcol is not None:
						out.write("\t"+l[orcol])
					if becol is not None:
						out.write("\t"+l[becol])
					if secol is not None:
						out.write("\t"+l[secol])
					if Ncol is not None:
						out.write("\t"+l[Ncol])
					out.write("\n")

		# when one of the alleles need to be extracted, only SNPs exist in dbSNP will be recoded in the output
		else:
			for l in temp:
				if int(l[1]) in poss:
				    j = bisect_left(pos, int(l[1]))
				    if snps[j,pcol] is None:
						continue
				    if altcol is not None:
				        if snps[j,altcol].upper()==l[3] or snps[j,altcol].upper()==l[4]:
				            a = "NA"
				            if snps[j,altcol]==l[3]:
				                a = l[4]
				            else:
				                a = l[3]
				            out.write("\t".join([l[0],l[1], a, snps[j,altcol].upper(), l[2], snps[j,pcol]]))
				            if orcol is not None:
				                out.write("\t"+snps[j,orcol])
				            if becol is not None:
				                out.write("\t"+snps[j,becol])
				            if secol is not None:
				                out.write("\t"+snps[j,secol])
				            if Ncol is not None:
				                out.write("\t"+snps[j,Ncol])
				            out.write("\n")
				    else:
				        out.write("\t".join([l[0],l[1], l[3], l[4], l[2], snps[j,pcol]]))
				        if orcol is not None:
				            out.write("\t"+snps[j,orcol])
				        if becol is not None:
				            out.write("\t"+snps[j,becol])
				        if secol is not None:
				            out.write("\t"+snps[j,secol])
				        if Ncol is not None:
				            out.write("\t"+snps[j,Ncol])
				        out.write("\n")
		out.close()
		##### end def Tabix() #####

	##### sort input sum stats #####
	# input.gwas will be overwrited
	tmp = pd.read_table(gwas, comment="#", sep=delim, dtype=str)
	head = list(tmp.columns.values)
	tmp = np.array(tmp)
	tmp = tmp[np.lexsort((tmp[:,chrcol].astype(int), tmp[:,poscol].astype(int)))]
	with open(gwas, 'w') as o:
		o.write("\t".join(head)+"\n")
	with open(gwas, 'a+') as o:
		np.savetxt(o, tmp, delimiter='\t', fmt='%s')

	##### init variables #####
	cur_chr = 1
	minpos = 0
	maxpos = 0
	temp = []

	##### write header of input.snps #####
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

	##### read input.gwas line by line #####
	gwasIn = open(gwas, 'r')
	gwasIn.readline()
	for l in gwasIn:
		if re.match("^#", l):
		    next
		l = l.replace("nan", "")
		l = l.strip('\n').split(delim)
		if len(l) < nheader:
			continue
		if l[pcol]=="":
			continue
		l[chrcol] = l[chrcol].replace("chr", "")
		if re.match(r"x", l[chrcol], re.IGNORECASE):
		    l[chrcol] = '23'
		if float(l[pcol]) < 1e-308:
		    l[pcol] = str(1e-308)

		if int(float(l[chrcol])) == cur_chr:
		    if minpos==0:
		        minpos = int(float(l[poscol]))
		    if int(float(l[poscol]))-minpos<=1000000:
		        maxpos = int(float(l[poscol]))
		        temp.append(l)
		    else:
				Tabix(cur_chr, minpos, maxpos, temp)
				minpos = int(float(l[poscol]))
				maxpos = int(float(l[poscol]))
				temp = []
				temp.append(l)
		else:
			if minpos!=0 and maxpos!=0:
				Tabix(cur_chr, minpos, maxpos, temp)
				cur_chr = int(l[chrcol])
				minpos = int(float(l[poscol]))
				maxpos = int(float(l[poscol]))
			temp = []
			temp.append(l)
	Tabix(cur_chr, minpos, maxpos, temp)
# if either chr or pos is not procided, use rsID to extract position
elif chrcol is None or poscol is None:
    print "Either chr or pos is not provided"
	##### read input file #####
    gwas = pd.read_table(gwas, comment="#", sep=delim)
    gwas = gwas.as_matrix()
    gwas = gwas[gwas[:,rsIDcol].argsort()]

	##### write header of input.snps #####
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

	##### update rsID to dbSNP 146 #####
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
    rsID146.close()

	##### sort input snps by rsID for bisect_left #####
    gwas = gwas[gwas[:,rsIDcol].argsort()]
    rsIDs = set(list(gwas[:, rsIDcol]))
    rsID = list(gwas[:, rsIDcol])
    checked = []

	##### process per chromosome #####
    for chrom in range(1,24):
        print "start chr"+str(chrom)
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
    out.close()

##### total time #####
print time.time()-start
