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
import subprocess

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results
def ArrayNotIn(a1, a2):
    tmp = np.where(np.in1d(a1, a2))[0]
    return list(set(range(0,len(a1)))-set(tmp))

##### detect file delimiter from the header #####
def DetectDelim(header):
	if re.match(r'.*\s\s.*', header) is not None:
		return '\s+'
	sniffer = csv.Sniffer()
	dialect = sniffer.sniff(header)
	return dialect.delimiter

##### check float #####
def is_float(s):
	try:
		float(s)
		return True
	except ValueError:
		return False

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
	tmp = pd.read_csv(leadfile, delim_whitespace=True)
	tmp = tmp.as_matrix()
	if len(tmp)==0 or len(tmp[0])<3:
		sys.exit("Input lead SNPs file does not have enought columns.")

if regionfile != "NA":
	regionfile = filedir+"input.regions"
	tmp = pd.read_csv(regionfile, delim_whitespace=True)
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
neacol = param.get('inputfiles', 'neacol').upper()
eacol = param.get('inputfiles', 'eacol').upper()
orcol = param.get('inputfiles', 'orcol').upper()
becol = param.get('inputfiles', 'becol').upper()
secol = param.get('inputfiles', 'secol').upper()
Ncol = param.get('params', 'Ncol').upper()
N = param.get('params', 'N')

##### get header of sum stats #####
fin = open(gwas, 'r')
header = fin.readline()
while re.match("^#", header):
    header = fin.readline()
fin.close()
delim = DetectDelim(header)
header = re.split(delim, header.strip())
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
	elif eacol == header[i].upper():
		eacol = i
		checkedheader.append(eacol)
	elif neacol == header[i].upper():
		neacol = i
		checkedheader.append(neacol)
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
		checkedheader.append(secol)
	elif Ncol == header[i].upper():
		Ncol = i
		checkedheader.append(Ncol)
for i in range(0, len(header)):
	if i in checkedheader:
		continue
	if chrcol == "NA" and re.match("CHR$|^chromosome$|^chrom$", header[i], re.IGNORECASE):
		chrcol = i
	elif rsIDcol == "NA" and re.match("SNP$|^MarkerName$|^rsID$|^snpid$", header[i], re.IGNORECASE):
		rsIDcol = i
	elif poscol == "NA" and re.match("^BP$|^pos$|^position$", header[i], re.IGNORECASE):
		poscol = i
	elif eacol == "NA" and re.match("^A1$|^Effect_allele$|^allele1$|^alleleB$", header[i], re.IGNORECASE):
		eacol = i
	elif neacol == "NA" and re.match("^A2$|^Non_Effect_allele$|^allele2$|^alleleA$", header[i], re.IGNORECASE):
		neacol = i
	elif pcol == "NA" and re.match("^P$|^pval$|^pvalue$|^p-value$|^p_value$", header[i], re.IGNORECASE):
		pcol = i
	elif orcol == "NA" and re.match("^or$", header[i], re.IGNORECASE):
		orcol = i
	elif becol == "NA" and re.match("^beta$", header[i], re.IGNORECASE):
		becol = i
	elif secol == "NA" and re.match("^se$", header[i], re.IGNORECASE):
		secol = i
	elif Ncol == "NA" and N=="NA" and re.match("^N$", header[i], re.IGNORECASE):
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
if neacol=="NA":
    neacol = None
else:
	user_header.append(neacol)
if eacol=="NA":
    eacol = None
else:
	user_header.append(eacol)
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
if neacol is not None and eacol is None:
    eacol = neacol
    neacol = None

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
if chrcol is not None and poscol is not None and rsIDcol is not None and eacol is not None and neacol is not None:
	# dbSNPfile = cfg.get('data', 'dbSNP')
	# rsID = pd.read_csv(dbSNPfile+"/RsMerge146.txt", header=None)
	# rsID = np.array(rsID)
	# rsIDs = set(rsID[:,0])
	# rsID = rsID[rsID[:,0].argsort()]

	out = open(outSNPs, 'w')
	out.write("chr\tbp\tnon_effect_allele\teffect_allele\trsID\tp")
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
		l = re.split(delim, l.strip())
		if len(l) < nheader:
			continue
		if not is_float(l[pcol]):
			continue
		if float(l[pcol])<0 or float(l[pcol])>1:
			continue
		if float(l[pcol])==0 and re.match("^0", l[pcol]):
			continue
		# if l[rsIDcol] in rsIDs:
		# 	j = bisect_left(rsID[:,0], l[rsIDcol])
		# 	l[rsIDcol] = rsID[j,1]
		l[chrcol] = l[chrcol].replace("chr", "").replace("CHR", "")
		if re.match("x", l[chrcol], re.IGNORECASE):
			l[chrcol] = '23'
		if not l[chrcol].isdigit():
			continue
		if int(l[chrcol]) not in range(1,24):
			continue
        # if float(l[pcol]) < 1e-308:
        #     l[pcol] = str(1e-308)
		out.write("\t".join([l[chrcol], l[poscol], l[neacol].upper(), l[eacol].upper(), l[rsIDcol], l[pcol]]))
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
	refpanel = cfg.get('data', 'refgenome')+"/"+param.get('params', 'refpanel')
	pop = param.get('params', 'pop')

	##### tabix refpanel to get rsID and alleles #####
	def Tabix (chrom, start ,end, snps):
		snps = np.array(snps)

		poss = set(snps[:, poscol].astype(int))
		pos = snps[:, poscol].astype(int)

		out = open(outSNPs, 'a+')

		# when rsID is the only missing column, keep all SNPs in input file
		# assigned rsID from the selected reference panel
		# if rsID is not available, replace with uniqID
		if neacol is not None and eacol is not None:
			tbfile = refpanel+"/"+pop+"/"+pop+".chr"+str(chrom)+".rsID.gz"
			tb = tabix.open(tbfile)
			refSNP = []
			for l in tb.querys(str(chrom)+":"+str(start)+"-"+str(end)):
				refSNP.append(l)
			if len(refSNP)>0:
				refSNP = np.array(refSNP)
				poss = set(refSNP[:,1].astype(int))
				pos = refSNP[:,1].astype(int)
				for l in snps:
					uid = ":".join([l[chrcol], l[poscol]]+sorted([l[neacol].upper(), l[eacol].upper()]))
					if int(l[poscol]) in poss:
						j = bisect_left(pos, int(l[poscol]))
						while refSNP[j,1] == int(l[poscol]):
							if uid == refSNP[j,2]: break
							j += 1
						if uid == refSNP[j,2]:
							out.write("\t".join([refSNP[j,0], refSNP[j,1], l[neacol].upper(), l[eacol].upper(), refSNP[j,3], l[pcol]]))
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
							out.write("\t".join([l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]))
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
						out.write("\t".join([l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]))
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
				for l in snps:
					uid = ":".join([l[chrcol], l[poscol]]+sorted([l[neacol].upper(), l[eacol].upper()]))
					out.write("\t".join([l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]))
					if orcol is not None:
						out.write("\t"+l[orcol])
					if becol is not None:
						out.write("\t"+l[becol])
					if secol is not None:
						out.write("\t"+l[secol])
					if Ncol is not None:
						out.write("\t"+l[Ncol])
					out.write("\n")

		# when one of the alleles need to be extracted, get from the selected population
		else:
			tbfile = refpanel+"/"+pop+"/"+pop+".chr"+str(chrom)+".frq.gz"
			tb = tabix.open(tbfile)
			temp = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))
			for l in temp:
				if int(l[1]) in poss:
				    j = bisect_left(pos, int(l[1]))
				    if snps[j,pcol] is None:
						continue
				    if eacol is not None:
						if snps[j,eacol].upper()==l[3] or snps[j,eacol].upper()==l[4]:
							a = "NA"
							if snps[j,eacol]==l[3]:
								a = l[4]
							else:
								a = l[3]
							if rsIDcol is None:
								out.write("\t".join([l[0],l[1], a, snps[j,eacol].upper(), l[2], snps[j,pcol]]))
							else:
								out.write("\t".join([l[0],l[1], a, snps[j,eacol].upper(), snps[j,rsIDcol], snps[j,pcol]]))
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
						if rsIDcol is None:
							out.write("\t".join([l[0],l[1], l[3], l[4], l[2], snps[j,pcol]]))
						else:
							out.write("\t".join([l[0],l[1], l[3], l[4], snps[j,rsIDcol], snps[j,pcol]]))
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
		return
		##### end def Tabix() #####

	##### sort input sum stats #####
	# input.gwas will be overwrited
	tmp = pd.read_csv(gwas, comment="#", sep=delim, dtype=str)
	head = list(tmp.columns.values)
	tmp = np.array(tmp)
	tmp[:,chrcol] = [x.replace("chr", "").replace("CHR", "") for x in tmp[:,chrcol]]
	tmp[:,chrcol] = [x.replace("x", "23").replace("X", "23") for x in tmp[:,chrcol]]
	tmp = tmp[np.lexsort((tmp[:,poscol].astype(int), tmp[:,chrcol].astype(int)))]
	with open(gwas, 'w') as o:
		o.write(" ".join(head)+"\n")
	with open(gwas, 'a+') as o:
		np.savetxt(o, tmp, delimiter=' ', fmt='%s')

	##### init variables #####
	cur_chr = 1
	minpos = 0
	maxpos = 0
	temp = []

	##### write header of input.snps #####
	out = open(outSNPs, 'w')
	out.write("chr\tbp\tnon_effect_allele\teffect_allele\trsID\tp")
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
		l = l.strip('\n').split(' ')
		if len(l) < nheader:
			continue
		if not is_float(l[pcol]):
			continue
		if float(l[pcol])<0 or float(l[pcol])>1:
			continue
		if float(l[pcol])==0 and re.match("^0", l[pcol]):
			continue
		l[chrcol] = l[chrcol].replace("chr", "").replace("CHR", "")
		if re.match(r"x", l[chrcol], re.IGNORECASE):
		    l[chrcol] = '23'
		if not l[chrcol].isdigit():
			continue
		if int(l[chrcol]) not in range(1,24):
			continue
		# if float(l[pcol]) < 1e-308:
		#     l[pcol] = str(1e-308)

		if int(float(l[chrcol])) == cur_chr:
		    if minpos==0:
		        minpos = int(float(l[poscol]))
		    if int(float(l[poscol]))-minpos<=1000000:
		        maxpos = int(float(l[poscol]))
		        temp.append(l)
		    else:
				if str(cur_chr) in [str(x) for x in range(1,24)]:
					Tabix(cur_chr, minpos, maxpos, temp)
				minpos = int(float(l[poscol]))
				maxpos = int(float(l[poscol]))
				temp = []
				temp.append(l)
		else:
			if minpos!=0 and maxpos!=0:
				if str(cur_chr) in [str(x) for x in range(1,24)]:
					Tabix(cur_chr, minpos, maxpos, temp)
			cur_chr = int(l[chrcol])
			minpos = int(float(l[poscol]))
			maxpos = int(float(l[poscol]))
			temp = []
			temp.append(l)
	if str(cur_chr) in [str(x) for x in range(1,24)]:
		Tabix(cur_chr, minpos, maxpos, temp)
# if either chr or pos is not procided, use rsID to extract position
elif chrcol is None or poscol is None:
	print "Either chr or pos is not provided"
	##### read input file #####
	gwas = pd.read_csv(gwas, comment="#", sep=delim, dtype=str)
	gwas = gwas.as_matrix()
	gwas = gwas[gwas[:,rsIDcol].argsort()]

	##### write header of input.snps #####
	out = open(outSNPs, 'w')
	out.write("chr\tbp\tnon_effect_allele\teffect_allele\trsID\tp")
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
		l = l.strip().split()
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
		for chunk in pd.read_csv(dbSNPfile+"/dbSNP146.chr"+str(chrom)+".vcf.gz", header=None, sep="\t", dtype=str, chunksize=10000):
			chunk = np.array(chunk)
			for l in chunk:
				alt = l[4].split(",")
				if l[2] in rsIDs:
					checked.append(l[2])
					j = bisect_left(rsID, l[2])
					if not is_float(gwas[j,pcol]):
						continue
					if float(gwas[j,pcol])<0 or float(gwas[j,pcol])>1:
						continue
					if float(gwas[j,pcol])==0 and re.match("^0", gwas[j,pcol]):
						continue
					# if(gwas[j,pcol]<1e-308):
					#     gwas[j,pcol]=1e-308
					if eacol is not None and neacol is not None:
						if (gwas[j,eacol].upper()==l[3] and gwas[j,neacol].upper() in alt) or gwas[j,eacol].upper() in alt and gwas[j,neacol].upper()==l[3]:
							out.write("\t".join([str(chrom), str(l[1]), gwas[j,neacol].upper(), gwas[j,eacol].upper(), l[2], str(gwas[j,pcol])]))
							if orcol is not None:
								out.write("\t"+str(gwas[j,orcol]))
							if becol is not None:
								out.write("\t"+str(gwas[j,becol]))
							if secol is not None:
								out.write("\t"+str(gwas[j,secol]))
							if Ncol is not None:
								out.write("\t"+str(gwas[j,Ncol]))
							out.write("\n")
					elif eacol is not None:
						if gwas[j,eacol].upper()==l[3] or gwas[j,eacol].upper() in alt:
							if len(alt)>1 :
								continue
							a = "NA"
							if gwas[j,eacol].upper()==l[3]:
								a=l[4]
							else:
								a=l[3]

							out.write("\t".join([str(chrom), str(l[1]), a, gwas[j,eacol].upper(), l[2], str(gwas[j,pcol])]))
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

		if len(gwas)==len(checked):
			break
	out.close()

##### check output file #####
wc = int(subprocess.check_output("wc -l "+filedir+"input.snps", shell=True).split()[0])
if wc < 2:
	sys.exit("There was no SNPs remained after formatting the input summary statistics.")

##### total time #####
print time.time()-start
