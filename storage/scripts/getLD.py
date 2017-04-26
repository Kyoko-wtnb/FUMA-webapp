#!/usr/bin/python
import time
import os
import sys
import re
import pandas as pd
import numpy as np
import tabix
import glob
import ConfigParser
from bisect import bisect_left
# from joblib import Parallel, delayed
# import multiprocessing

starttime = time.time()

# n_cores = multiprocessing.cpu_count()
# print str(n_cores)+" cores detected"

if len(sys.argv)<2:
	sys.exit('ERROR: not enough arguments\nUSAGE ./getLD.py <filedir>')

filedir = sys.argv[1]
if re.match(".+\/$", filedir) is None:
	filedir += '/'

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results

##### return unique element in list #####
def unique(a):
	unique = []
	[unique.append(s) for s in a if s not in unique]
	return unique

###################
# get config files
###################
cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = ConfigParser.ConfigParser()
param.read(filedir+'params.config')

###################
# get parameters
###################
leadSNPs = param.get('inputfiles', 'leadSNPsfile')
if leadSNPs == "NA":
    print "prefedined lead SNPs are not provided"
    leadSNPs = None
else:
    print "predefined lead SNPs are procided"
    leadSNPs = filedir+"input.lead"
addleadSNPs = int(param.get('inputfiles', 'addleadSNPs')) #1 to add, 0 to not add
regions = param.get('inputfiles', 'regionsfile')
if regions == "NA":
    print "predefined genomic regions are not provided"
    regions = None
else:
    print "predefined gwnomic regions are provided"
    regions = filedir+"input.regions"

pop = param.get('params', 'pop')
leadP = float(param.get('params', 'leadP'))
KGSNPs = int(param.get('params', 'Incl1KGSNPs')) #1 to add, 0 to not add
gwasP = float(param.get('params', 'gwasP'))
maf = float(param.get('params', 'MAF'))
r2 = float(param.get('params', 'r2'))
mergeDist = int(param.get('params', 'mergeDist'))*1000
MHC = int(param.get('params', 'exMHC')) # 1 to exclude, 0 to not
extMHC = param.get('params', 'extMHC')
MHCstart = 29614758 # hg19
MHCend = 33170276 # hg19
if extMHC != "NA":
    mhc = extMHC.split("-")
    MHCstart = int(mhc[0])
    MHCend = int(mhc[1])

###################
# input files
###################
gwas = filedir+"input.snps"

###################
# get column index
###################
chrcol = 0
poscol = 1
refcol = 2
altcol = 3
rsIDcol = 4
pcol = 5
orcol = None
becol = None
secol = None

f = open(gwas, 'r')
head = f.readline()
f.close()
head = head.strip().split()
for i in range(0,len(head)):
	if head[i] == "or":
		orcol = i
	elif head[i] == "beta":
		becol = i
	elif head[i] == "se":
		secol = i

###################
# output files
###################
ldout = filedir+"ld.txt"
snpsout = filedir+"snps.txt"
annotout = filedir+"annot.txt"
indsigout = filedir+"IndSigSNPs.txt"
leadout = filedir+"leadSNPs.txt"
glout = filedir+"GenomicRiskLoci.txt"
annovin = filedir+"annov.input"

with open(ldout, 'w') as o:
	o.write("\t".join(["SNP1","SNP2","r2"])+"\n")

ohead = "\t".join(["uniqID", "rsID", "chr", "pos", "ref", "alt", "MAF", "gwasP"])
if orcol:
	ohead += "\tor"
if becol:
	ohead += "\tbeta"
if secol:
	ohead += "\tse"
ohead += "\tr2\tIndSigSNP\tGenomicLocus"
ohead += "\n"
with open(snpsout, 'w') as o:
	o.write(ohead)

ohead = "\t".join(["uniqID", "CADD", "RDB"])
chr15files = cfg.get('data', 'chr15')
chr15files = glob.glob(chr15files+"/*.bed.gz")
chr15files.sort()
for c in chr15files:
	m = re.match(r".+\/(E\d+)_.+", c)
	ohead += "\t"+m.group(1)
ohead += "\n"
with open(annotout, 'w') as o:
	o.write(ohead)

###################
# region file
# 0: chr, 1: start, 2: end
###################
if regions:
	regions = pd.read_table(regions, comment="#", delim_whitespace=True)
	regions = regions.as_matrix()

###################
# lead SNPs file
# 0: rsID, 1: chr, 2: pos
###################
def rsIDup(snps, rsIDi):
	dbSNPfile = cfg.get('data', 'dbSNP')
	#rsIDs = pd.read_table(dbSNPfile+"/RsMerge146.txt", header=None)
	#rsIDs = rsIDs.as_matrix()
	#rsIDs = rsIDs[rsIDs[:,0].argsort()]
	#rsIDset = set(rsIDs[:,0])
	rsID = np.memmap(dbSNPfile+"/RsMerge146.npy", mode='r', dtype='int', shape=(11684784, 3))

	for i in range(0, len(snps)):
		rs = int(snps[i,rsIDi].replace('rs', ''))
		if rs in rsID[:,0]:
			rs = 'rs'+str(rsID[rsID[:,0]==rs,1])
			snps[i, rsIDi] = rs
	return snps

if leadSNPs:
	leadSNPs = pd.read_table(leadSNPs, comment="#", delim_whitespace=True)
	leadSNPs = leadSNPs.as_matrix()
	leadSNPs = rsIDup(leadSNPs, 0)

###################
# get chr row numbers
###################
gwasfile_chr = []
chr_cur = 0
cur_i = 0
row = 0
gwasf = open(gwas, 'r')
gwasf.readline()
for l in gwasf:
	row += 1
	l = re.match(r"^(\d+)\t.+", l)
	chr_tmp = int(l.group(1))
	if chr_tmp == chr_cur:
		gwasfile_chr[cur_i-1][2] += 1
	else:
		chr_cur = chr_tmp
		gwasfile_chr.append([chr_cur, row, 1])
		cur_i += 1
gwasf.close()

gwasfile_chr = np.array(gwasfile_chr)

refgenome = cfg.get('data', 'refgenome')

def chr_process(ichrom):
	chrom = gwasfile_chr[ichrom][0]
	print "Start chromosome "+str(chrom)+" ..."
	regions_tmp = None
	if regions is not None:
		regions_tmp = regions[regions[:,0]==chrom]
		if len(regions_tmp)==0:
			return [], [], [], []

	leadSNPs_tmp = None
	if leadSNPs is not None:
		leadSNPs_tmp = leadSNPs[leadSNPs[:,1]==chrom]
		if len(leadSNPs_tmp) == 0 and addleadSNPs == 0:
			return [], [], [], []

	gwas_in = pd.read_table(gwas, header=None, skiprows=gwasfile_chr[ichrom][1], nrows=gwasfile_chr[ichrom][2])
	gwas_in = gwas_in.as_matrix()

	if chrom == 6 and MHC == 1:
		print "Excluding MHC regions ..."
		gwas_in = gwas_in[(gwas_in[:,poscol].astype(int)<MHCstart) | (gwas_in[:,poscol].astype(int)>MHCend)]

	if regions_tmp:
		gwas_tmp = np.array()
		for l in regions_tmp:
			tmp = gwas_in[(gwas_in[:,poscol].astype(int)>=l[1]) & (gwas_in[:,poscol].astype(int)<=l[2])]
			if len(tmp)>0:
				if len(gwas_tmp)>0:
					gwas_tmp = np.r_[gwas_tmp, tmp]
				else:
					gwas_tmp = tmp
		if len(gwas_tmp) == 0:
			return [], [], [], []
		gwas_in = gwas_tmp

	print str(len(gwas_in))+" SNPs in chromosome "+str(chrom)
	ld = []
	canSNPs = []
	annot = []
	IndSigSNPs = []
	nlead = 0
	pos_set = set(gwas_in[:,poscol])
	posall = gwas_in[:,poscol]

	ldfile = refgenome+"/"+pop+"ld/"+pop+".chr"+str(chrom)+".ld.gz"
	annotfile = refgenome+"/"+pop+"/"+"chr"+str(chrom)+".data.txt.gz"

	rsIDset = set(gwas_in[:, rsIDcol])
	checkeduid = []

	if leadSNPs_tmp is not None:
		for l in leadSNPs_tmp:
			if not l[0] in rsIDset:
				print "Input lead SNP "+l[0]+" does not exists in input gwas file"
				continue

			igwas = np.where(gwas_in[:,rsIDcol]==l[0])[0][0]
			allele = [gwas_in[igwas, refcol], gwas_in[igwas, altcol]]
			allele.sort()
			l_uid = ":".join([str(gwas_in[igwas, chrcol]), str(gwas_in[igwas, poscol])]+allele)
			pos = int(l[2])
			#check uniq ID
			tb = tabix.open(annotfile)
			lead_id = False
			lead_maf = False
			check_id = tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos))
			for m in check_id:
				if m[6] == l_uid:
					lead_id = True
					if float(m[5]) >= maf:
						lead_maf = True
					break
			if not lead_id or not lead_maf:
				continue
			nlead += 1
			tb = tabix.open(ldfile)
			ld_tb = tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos))
			ld_tmp = []
			ld_tmp.append([l[2], l[0], 1])
			for m in ld_tb:
				if m[2] != l[0]:
					continue
				if float(m[6]) >= r2:
					ld_tmp.append([m[4], m[5], m[6]])
			ld_tmp = np.array(ld_tmp)
			minpos = min(ld_tmp[:,0].astype(int))
			maxpos = max(ld_tmp[:,0].astype(int))
			tb = tabix.open(annotfile)
			annot_tb = tb.querys(str(chrom)+":"+str(minpos)+"-"+str(maxpos))
			nonGWASSNPs = 0
			GWASSNPs = 0
			for m in annot_tb:
				if chrom==6 and MHC==1 and int(m[1])>=MHCstart and int(m[1])<=MHCend:
					continue
				if float(m[5]) < maf:
					continue
				if m[4] in ld_tmp[:,1]:
					ild = np.where(ld_tmp[:,1]==m[4])[0][0]
					if int(m[1]) in pos_set:
						# jgwas = np.where(gwas_in[:, poscol]==int(m[1]))[0][0]
						jgwas = bisect_left(posall, int(m[1]))
						if float(gwas_in[jgwas, pcol])>gwasP:
							continue
						allele = [gwas_in[jgwas, refcol], gwas_in[jgwas, altcol]]
						allele.sort()
						uid = ":".join([str(gwas_in[jgwas, chrcol]), str(gwas_in[jgwas, poscol])]+allele)

						if uid != m[6]:
							checkall = False
							jgwas += 1
							while int(m[1]) == gwas_in[jgwas, poscol]:
								allele = [gwas_in[jgwas, refcol], gwas_in[jgwas, altcol]]
								allele.sort()
								uid = ":".join([str(gwas_in[jgwas, chrcol]), str(gwas_in[jgwas, poscol])]+allele)
								if uid == m[6]:
									checkall = True
									break
								jgwas += 1
							if not checkall:
								continue

						ld.append([l_uid, m[6], ld_tmp[ild, 2]])

						if m[6] in checkeduid:
							continue

						checkeduid.append(m[6])
						p = str(gwas_in[jgwas, pcol])
						snp = [m[6], gwas_in[jgwas, rsIDcol], m[0], m[1], gwas_in[jgwas, refcol], gwas_in[jgwas, altcol], m[5], p]
						if orcol:
							snp.append(str(gwas_in[jgwas, orcol]))
						if becol:
							snp.append(str(gwas_in[jgwas, becol]))
						if secol:
							snp.append(str(gwas_in[jgwas, secol]))
						canSNPs.append(snp)
						annot.append([m[6], m[7], m[8]]+m[53:len(m)])
						GWASSNPs += 1
					elif KGSNPs==1:
						ld.append([l_uid, m[6], ld_tmp[ild, 2]])
						if m[6] in checkeduid:
							continue
						checkeduid.append(m[6])
						snp = [m[6], m[4], m[0], m[1], m[2], m[3], m[5], "NA"]
						if orcol:
							snp.append("NA")
						if becol:
							snp.append("NA")
						if secol:
							snp.append("NA")
						canSNPs.append(snp)
						annot.append([m[6], m[7], m[8]]+m[53:len(m)])
						nonGWASSNPs += 1

			IndSigSNPs.append([l_uid, l[0], str(l[1]), str(l[2]), str(gwas_in[igwas, pcol]), str(nonGWASSNPs+GWASSNPs), str(GWASSNPs)])

		if len(gwas_in[gwas_in[:,pcol]<=leadP]) == 0:
			if len(canSNPs)>0:
				ld = np.array(ld)
				canSNPs = np.array(canSNPs)
				annot = np.array(annot)
				IndSigSNPs = np.array(IndSigSNPs)
				IndSigSNPs = IndSigSNPs[IndSigSNPs[:,3].astype(int).argsort()]
				n = canSNPs[:,3].astype(int).argsort()
				canSNPs = canSNPs[n]
				annot = annot[n]
				return ld, canSNPs, annot, IndSigSNPs
			else:
				return [], [], [], []

	if len(gwas_in[gwas_in[:,pcol].astype(float)<=leadP]) == 0:
		if len(canSNPs)>0:
			ld = np.array(ld)
			canSNPs = np.array(canSNPs)
			annot = np.array(annot)
			IndSigSNPs = np.array(IndSigSNPs)
			IndSigSNPs = IndSigSNPs[IndSigSNPs[:,3].astype(int).argsort()]
			n = canSNPs[:,3].astype(int).argsort()
			canSNPs = canSNPs[n]
			annot = annot[n]
			return ld, canSNPs, annot, IndSigSNPs
		else:
			return [], [], [], []

	p_order = gwas_in[:,pcol].argsort()
	if leadSNPs is None or addleadSNPs == 1:
		for pi in p_order:
			l = gwas_in[pi]
			if float(l[pcol])>leadP:
				break
			allele = [l[refcol], l[altcol]]
			allele.sort()
			l_uid = ":".join([str(l[chrcol]), str(l[poscol])]+allele)
			if not l_uid in checkeduid:
				pos = l[poscol]
				#check uniq ID
				tb = tabix.open(annotfile)
				lead_id = False
				lead_maf = False
				check_id = tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos))
				for m in check_id:
					if m[6] == l_uid:
						lead_id = True
						if float(m[5]) >= maf:
							lead_maf = True
						break
				if not lead_id or not lead_maf:
					continue
				nlead += 1
				tb = tabix.open(ldfile)
				ld_tb = tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos))
				ld_tmp = []
				ld_tmp.append([l[poscol], l[rsIDcol], 1])
				for m in ld_tb:
					if m[2] != l[rsIDcol]:
						continue
					if float(m[6]) >= r2:
						ld_tmp.append([m[4], m[5], m[6]])
				ld_tmp = np.array(ld_tmp)
				minpos = min(ld_tmp[:,0].astype(int))
				maxpos = max(ld_tmp[:,0].astype(int))
				tb = tabix.open(annotfile)
				annot_tb = tb.querys(str(chrom)+":"+str(minpos)+"-"+str(maxpos))
				nonGWASSNPs = 0
				GWASSNPs = 0
				for m in annot_tb:
					if chrom==6 and MHC==1 and int(m[1])>=MHCstart and int(m[1])<=MHCend:
						continue
					if float(m[5]) < maf:
						continue
					if m[1] in ld_tmp[:,0]:
						ild = np.where(ld_tmp[:,0]==m[1])[0][0]
						if int(m[1]) in pos_set:
							# jgwas = np.where(gwas_in[:, poscol]==int(m[1]))[0][0]
							jgwas = bisect_left(posall, int(m[1]))
							if float(gwas_in[jgwas, pcol])>gwasP:
								continue
							allele = [gwas_in[jgwas, refcol], gwas_in[jgwas, altcol]]
							allele.sort()
							uid = ":".join([str(gwas_in[jgwas, chrcol]), str(gwas_in[jgwas, poscol])]+allele)
							if uid != m[6]:
								checkall = False
								jgwas += 1
								while int(m[1]) == gwas_in[jgwas, poscol]:
									allele = [gwas_in[jgwas, refcol], gwas_in[jgwas, altcol]]
									allele.sort()
									uid = ":".join([str(gwas_in[jgwas, chrcol]), str(gwas_in[jgwas, poscol])]+allele)
									if uid == m[6]:
										checkall = True
										break
									jgwas += 1
								if not checkall:
									continue
							ld.append([l_uid, m[6], ld_tmp[ild, 2]])
							if m[6] in checkeduid:
								continue
							checkeduid.append(m[6])
							p = str(gwas_in[jgwas, pcol])
							snp = [m[6], gwas_in[jgwas, rsIDcol], m[0], m[1], gwas_in[jgwas, refcol], gwas_in[jgwas, altcol], m[5], p]
							if orcol:
								snp.append(str(gwas_in[jgwas, orcol]))
							if becol:
								snp.append(str(gwas_in[jgwas, becol]))
							if secol:
								snp.append(str(gwas_in[jgwas, secol]))
							canSNPs.append(snp)
							annot.append([m[6], m[7], m[8]]+m[53:len(m)])
							GWASSNPs += 1
						elif KGSNPs==1:
							ld.append([l_uid, m[6], ld_tmp[ild, 2]])
							if m[6] in checkeduid:
								continue
							checkeduid.append(m[6])
							snp = [m[6], m[4], m[0], m[1], m[2], m[3], m[5], "NA"]
							if orcol:
								snp.append("NA")
							if becol:
								snp.append("NA")
							if secol:
								snp.append("NA")
							canSNPs.append(snp)
							annot.append([m[6], m[7], m[8]]+m[53:len(m)])
							nonGWASSNPs += 1
				IndSigSNPs.append([l_uid, l[4], str(l[0]), str(l[1]), str(l[5]), str(nonGWASSNPs+GWASSNPs), str(GWASSNPs)])

	if len(canSNPs)>0:
		ld = np.array(ld)
		canSNPs = np.array(canSNPs)
		annot = np.array(annot)
		IndSigSNPs = np.array(IndSigSNPs)
		IndSigSNPs = IndSigSNPs[IndSigSNPs[:,3].astype(int).argsort()]
		n = canSNPs[:,3].astype(int).argsort()
		canSNPs = canSNPs[n]
		annot = annot[n]
	return ld, canSNPs, annot, IndSigSNPs

ld = []
snps = []
annot = []
IndSigSNPs = []

for i in range(0, len(gwasfile_chr)):
	ld_tmp, snps_tmp, annot_tmp, IndSigSNPs_tmp = chr_process(i)
	if len(snps_tmp)>0:
		if len(snps)>0:
			ld = np.r_[ld, ld_tmp]
			snps = np.r_[snps, snps_tmp]
			annot = np.r_[annot, annot_tmp]
			IndSigSNPs = np.r_[IndSigSNPs, IndSigSNPs_tmp]
		else:
			ld = ld_tmp
			snps = snps_tmp
			annot = annot_tmp
			IndSigSNPs = IndSigSNPs_tmp
# Parallel(n_jobs=n_cores)(delayed(chr_process)(i) for i in range(0, len(gwasfile_chr)))

if len(snps)==0:
	sys.exit("No candidate SNP was identified")

with open(ldout, 'a') as o:
	np.savetxt(o, ld, delimiter="\t", fmt="%s")
# with open(snpsout, 'a') as o:
# 	np.savetxt(o, snps, delimiter="\t", fmt="%s")
with open(annotout, 'a') as o:
	np.savetxt(o, annot, delimiter="\t", fmt="%s")

with open(annovin, 'w') as o:
	for l in snps:
		o.write("\t".join([l[2], l[3], l[3], l[4], l[5]])+"\n")

leadSNPs = []
checked = []

IndSigSNPs = IndSigSNPs[IndSigSNPs[:,4].astype(float).argsort()]
for snp in IndSigSNPs:
	if snp[1] in checked:
		continue
	ldfile = refgenome+'/'+pop+'ld/'+pop+'.chr'+str(snp[2])+'.ld.gz';
	tb = tabix.open(ldfile)
	ld_tmp = tb.querys(snp[2]+":"+snp[3]+"-"+snp[3])
	inSNPs = []
	inSNPs.append(snp[1])
	for l in ld_tmp:
		if float(l[6])<0.1:
			continue
		if l[5] in IndSigSNPs[:,1]:
			checked.append(l[5])
			inSNPs.append(l[5])
	leadSNPs.append([snp[0], snp[1], snp[2], snp[3], snp[4], str(len(inSNPs)), ":".join(inSNPs)])
leadSNPs = np.array(leadSNPs)
leadSNPs = leadSNPs[np.lexsort((leadSNPs[:,3].astype(int), leadSNPs[:,2].astype(int)))]
IndSigSNPs = IndSigSNPs[np.lexsort((IndSigSNPs[:,3].astype(int), IndSigSNPs[:,2].astype(int)))]

loci = []
iloci = 0
chrom = 0
inInd = []
inLead = []
nonGWASSNPs = []
GWASSNPs = []
uid2gl = {}
for i in range(0, len(leadSNPs)):
	if i == 0:
		chrom = int(leadSNPs[i, 2])
		rsIDs = list(leadSNPs[i,6].split(":"))
		uid = list(snps[ArrayIn(snps[:,1], rsIDs),0])
		for s in uid:
			uid2gl[s] = iloci+1
		inInd = rsIDs
		inLead = [leadSNPs[i,1]]
		n = ArrayIn(snps[:,0], ld[ArrayIn(ld[:,0], uid),1])
		snps_tmp = snps[n,]
		nonGWASSNPs += list(snps_tmp[snps_tmp[:,7]=="NA", 0])
		GWASSNPs += list(snps_tmp[snps_tmp[:,7]!="NA", 0])
		start = min(snps[n,3].astype(int))
		end = max(snps[n,3].astype(int))
		loci.append([str(iloci+1)]+list(leadSNPs[i,range(0,5)])+[str(start), str(end), str(len(nonGWASSNPs)+len(GWASSNPs)), str(len(GWASSNPs)), str(len(inInd)), ":".join(inInd), str(len(inLead)), ":".join(inLead)])
	elif chrom==int(leadSNPs[i,2]):
		rsIDs = list(leadSNPs[i,6].split(":"))
		uid = list(snps[ArrayIn(snps[:,1], rsIDs),0])
		for s in uid:
			uid2gl[s] = iloci+1
		inInd += rsIDs
		inInd = unique(inInd)
		inLead += [leadSNPs[i,1]]
		n = ArrayIn(snps[:,0], ld[ArrayIn(ld[:,0], uid),1])
		snps_tmp = snps[n,]
		nonGWASSNPs += list(snps_tmp[snps_tmp[:,7]=="NA", 0])
		GWASSNPs += list(snps_tmp[snps_tmp[:,7]!="NA", 0])
		nonGWASSNPs = unique(nonGWASSNPs)
		GWASSNPs = unique(GWASSNPs)
		start = min(snps[n,3].astype(int))
		end = max(snps[n,3].astype(int))
		if start <= int(loci[iloci][7]) or start-int(loci[iloci][7])<=mergeDist:
			loci[iloci][6] = str(min(start, int(loci[iloci][6])))
			loci[iloci][7] = str(max(end, int(loci[iloci][7])))
			loci[iloci][8] = str(len(nonGWASSNPs)+len(GWASSNPs))
			loci[iloci][9] = str(len(GWASSNPs))
			loci[iloci][10] = str(len(inInd))
			loci[iloci][11] = ":".join(inInd)
			loci[iloci][12] = str(len(inLead))
			loci[iloci][13] = ":".join(inLead)
			if float(leadSNPs[i,4]) < float(loci[iloci][5]):
				loci[iloci][1] = leadSNPs[i,0]
				loci[iloci][2] = leadSNPs[i,1]
				loci[iloci][4] = leadSNPs[i,3]
				loci[iloci][5] = leadSNPs[i,4]
		else:
			iloci += 1
			inInd = []
			inLead = []
			nonGWASSNPs = []
			GWASSNPs = []
			rsIDs = list(leadSNPs[i,6].split(":"))
			uid = list(snps[ArrayIn(snps[:,1], rsIDs),0])
			for s in uid:
				uid2gl[s] = iloci+1
			inInd = rsIDs
			inLead = [leadSNPs[i,1]]
			n = ArrayIn(snps[:,0], ld[ArrayIn(ld[:,0], uid),1])
			snps_tmp = snps[n,]
			nonGWASSNPs += list(snps_tmp[snps_tmp[:,7]=="NA", 0])
			GWASSNPs += list(snps_tmp[snps_tmp[:,7]!="NA", 0])
			start = min(snps[n,3].astype(int))
			end = max(snps[n,3].astype(int))
			loci.append([str(iloci+1)]+list(leadSNPs[i,range(0,5)])+[str(start), str(end), str(len(nonGWASSNPs)+len(GWASSNPs)), str(len(GWASSNPs)), str(len(inInd)), ":".join(inInd), str(len(inLead)), ":".join(inLead)])
	else:
		chrom = int(leadSNPs[i,2])
		iloci += 1
		inInd = []
		inLead = []
		nonGWASSNPs = []
		GWASSNPs = []
		rsIDs = list(leadSNPs[i,6].split(":"))
		uid = list(snps[ArrayIn(snps[:,1], rsIDs),0])
		for s in uid:
			uid2gl[s] = iloci+1
		inInd = rsIDs
		inLead = [leadSNPs[i,1]]
		n = ArrayIn(snps[:,0], ld[ArrayIn(ld[:,0], uid),1])
		snps_tmp = snps[n,]
		nonGWASSNPs += list(snps_tmp[snps_tmp[:,7]=="NA", 0])
		GWASSNPs += list(snps_tmp[snps_tmp[:,7]!="NA", 0])
		start = min(snps[n,3].astype(int))
		end = max(snps[n,3].astype(int))
		loci.append([str(iloci+1)]+list(leadSNPs[i,range(0,5)])+[str(start), str(end), str(len(nonGWASSNPs)+len(GWASSNPs)), str(len(GWASSNPs)), str(len(inInd)), ":".join(inInd), str(len(inLead)), ":".join(inLead)])
loci = np.array(loci)

addcol = []
for i in range(0,len(IndSigSNPs)):
	addcol.append([str(i+1), str(uid2gl[IndSigSNPs[i,0]])])
IndSigSNPs = np.c_[addcol, IndSigSNPs]

addcol = []
for i in range(0,len(leadSNPs)):
	addcol.append([str(i+1), str(uid2gl[leadSNPs[i,0]])])
leadSNPs = np.c_[addcol, leadSNPs]

with open(indsigout, 'w') as o:
	o.write("\t".join(["No", "GenomicLocus", "uniqID", "rsID", "chr", "pos", "p","nSNPs", "nGWASSNPs"])+"\n")
with open(indsigout, 'a') as o:
	np.savetxt(o, IndSigSNPs, delimiter="\t", fmt="%s")

with open(leadout, 'w') as o:
	o.write("\t".join(["No", "GenomicLocus", "uniqID", "rsID", "chr", "pos", "p","nIndSigSNPs", "IndSigSNPs"])+"\n")
with open(leadout, 'a') as o:
	np.savetxt(o, leadSNPs, delimiter="\t", fmt="%s")

with open(glout, 'w') as o:
	o.write("\t".join(["GenomicLocus", "uniqID", "rsID", "chr", "pos", "p", "start", "end", "nSNPs", "nGWASSNPs", "nIndSigSNPs", "IndSigSNPs", "nLeadSNPs", "LeadSNPs"])+"\n")
with open(glout, 'a') as o:
	np.savetxt(o, loci, delimiter="\t", fmt="%s")

###################
# ANNOVAR
###################
annov = cfg.get('annovar', 'annovdir')
humandb = cfg.get('annovar', 'humandb')
annovout = filedir+"annov"
os.system(annov+"/annotate_variation.pl -out "+annovout+" -build hg19 "+annovin+" "+humandb+"/ -dbtype ensGene")
annov1 = filedir+"annov.variant_function"
annov2 = filedir+"annov.txt"
os.system(os.path.dirname(os.path.realpath(__file__))+"/annov_geneSNPs.pl "+annov1+" "+annov2)
os.system("rm "+filedir+"annov.input "+filedir+"annov.*function "+filedir+"annov.log")

###################
# snps annotation
###################
addcol = [] #r2, IndSigSNP, GenomicLocus, nearestGene, dist, func, CADD, RDB, minChrState, commonChrState
for l in snps:
	tmp = ld[ld[:,1]==l[0]]
	tmp = tmp[tmp[:,2]==max(tmp[:,2])][0]
	r2 = tmp[2]
	rsID = snps[snps[:,0]==tmp[0],1][0]
	GenomicLocus = IndSigSNPs[IndSigSNPs[:,2]==tmp[0],1][0]
	addcol.append([r2, rsID, GenomicLocus])
snps = np.c_[snps, addcol]
with open(snpsout, 'a') as o:
	np.savetxt(o, snps, delimiter="\t", fmt="%s")


print time.time() - starttime
