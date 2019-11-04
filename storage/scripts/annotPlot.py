#!/usr/bin/python
import sys
import os
import re
import pandas as pd
import numpy as np
import math
import ConfigParser
import json
import tabix

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	results = np.where(np.in1d(a1, a2))[0]
	return results

def ArrayNotIn(a1, a2):
    tmp = np.where(np.in1d(a1, a2))[0]
    return list(set(range(0,len(a1)))-set(tmp))

##### return unique element in list #####
def unique(a):
	unique = []
	[unique.append(s) for s in a if s not in unique]
	return unique

def getSNPs(filedir, i, Type, eqtlplot, ciplot):
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
		if ";" in loci[i,11]:
			ls = np.array(loci[i,11].split(";"))
		else:
			ls = np.array(loci[i,11].split(":"))
		ls = snps[ArrayIn(snps[:,1], ls), 0]
		ld = ld[ArrayIn(ld[:,0], ls)]
		snps = snps[snps[:,snpshead.index("GenomicLocus")]==i+1]
		snps = np.c_[snps, [1]*len(snps)]
		snps[ArrayIn(snps[:,0], ind[:,2]),len(snps[0])-1] = 2
		snps[ArrayIn(snps[:,0], lead[:,2]),len(snps[0])-1] = 3
		snps[ArrayIn(snps[:,0], loci[:,1]),len(snps[0])-1] = 4

	mapFilt = []
	if eqtlplot==1 and ciplot==1:
		tmp = snps[:,[snpshead.index("posMapFilt"), snpshead.index("eqtlMapFilt"), snpshead.index("ciMapFilt")]]
		mapFilt = [max(l) for l in tmp]
	elif eqtlplot==1 and ciplot==0:
		tmp = snps[:,[snpshead.index("posMapFilt"), snpshead.index("eqtlMapFilt")]]
		mapFilt = [max(l) for l in tmp]
	elif eqtlplot==0 and ciplot==1:
		tmp = snps[:,[snpshead.index("posMapFilt"), snpshead.index("ciMapFilt")]]
		mapFilt = [max(l) for l in tmp]
	else:
		mapFilt = snps[:,snpshead.index("posMapFilt")]

	gl = int(snps[0, snpshead.index("GenomicLocus")])
	snps_headi = [snpshead.index("uniqID"), snpshead.index("chr"), snpshead.index("pos"), snpshead.index("rsID"), snpshead.index("gwasP"), len(snps[0])-1, snpshead.index("r2"), snpshead.index("IndSigSNP"), snpshead.index("MAF"), snpshead.index("CADD"), snpshead.index("RDB"), snpshead.index("nearestGene"), snpshead.index("func")]
	snpshead_tmp = ["uniqID", "chr","pos", "rsID", "gwasP", "ld", "r2", "IndSigSNP", "MAF", "CADD", "RDB", "nearestGene", "func", "MapFilt"]

	snpshead = snpshead_tmp
	snps = snps[:, snps_headi]
	snps = np.c_[snps, mapFilt]

	try:
		snps[:, snpshead.index("RDB")].astype(float)
	except:
		snps[:, snpshead.index("RDB")] = snps[:, snpshead.index("RDB")].astype(str)
		snps[snps[:, snpshead.index("RDB")]=="nan", snpshead.index("RDB")]=["NA"]
	else:
		snps[:, snpshead.index("RDB")] = snps[:, snpshead.index("RDB")].astype(str)
		snps[snps[:, snpshead.index("RDB")]=="nan", snpshead.index("RDB")]=["NA"]
		snps[snps[:, snpshead.index("RDB")]!="NA", snpshead.index("RDB")] = [x.replace(".0", "") for x in snps[snps[:, snpshead.index("RDB")]!="NA", snpshead.index("RDB")]]
	return [snps, gl]

def SortCI(ci):
	# 0:start1, 1:end1, 2:start2, 3:end2, 4-7:else
	for i in range(0, len(ci)):
		l = ci[i]
		if int(l[0]) > int(l[2]):
			ci[i,0:4] = l[[2,3,0,1]]
	ci = np.array(list(i for i in set(map(tuple, ci))))
	return ci

def getCI(filedir, snps, ci, gl):
	min_pos = min(snps[:,2])
	max_pos = max(snps[:,2])
	chrom = int(snps[0,1])
	if ci==1:
		ci = pd.read_csv(filedir+"ci.txt", sep="\t")
		ci = np.array(ci)
		ci = ci[ci[:,0].astype(int)==gl]
		ci = ci[ci[:,7]=="intra"]
		inSNPs = []
		for l in ci[:,8]:
			inSNPs += list(l.split(":"))
		inSNPs = unique(inSNPs)
		ciout = []
		chr1 = [int(x.split(":")[0]) for x in ci[:,1]]
		chr2 = [int(x.split(":")[0]) for x in ci[:,2]]
		ciout = np.c_[chr1, chr2]
		if len(ciout) == 0:
			return [min_pos, max_pos, [], [] ,[] ,[] ,[]]
		ci = ci[np.where((ciout[:,0]==chrom) & (ciout[:,1]==chrom))]
		pos1min = [int(x.split(":")[1].split("-")[0]) for x in ci[:,1]]
		pos1max = [int(x.split(":")[1].split("-")[1]) for x in ci[:,1]]
		pos2min = [int(x.split(":")[1].split("-")[0]) for x in ci[:,2]]
		pos2max = [int(x.split(":")[1].split("-")[1]) for x in ci[:,2]]
		min_pos = min(min_pos, min(min(pos1min), min(pos2min)))
		max_pos = max(max_pos, max(max(pos1max), max(pos2max)))
		ciout = np.c_[pos1min, pos1max, pos2min, pos2max, ci[:,3:7]]
		ciout = SortCI(ciout)
		ciout = ciout[np.lexsort((ciout[:,2], ciout[:,0]))]
		ciout = ciout[np.lexsort((ciout[:,5], ciout[:,6], ciout[:,7]))]
		citypes = []
		ciheight = []
		y = []
		cur_type = ""
		cur_height = 1
		for l in ciout:
			tmp = ":".join([l[5], l[6], l[7]])
			if tmp != cur_type:
				if len(citypes)>0:
					ciheight.append(cur_height-1)
				citypes.append(tmp)
				cur_height = 1
				y.append(cur_height)
				cur_height += 1
				cur_type = tmp
			else:
				y.append(cur_height)
				cur_height += 1
		ciheight.append(cur_height-1)
		ciout = np.c_[ciout, y]
		# citypes = unique([":".join([l[5], l[6], l[7]]) for l in ciout])

		cireg = []
		eid = []
		cienh = pd.read_csv(filedir+"ciSNPs.txt", sep="\t")
		cienh = np.array(cienh)
		if len(cienh)>0:
			cienh = cienh[ArrayIn(cienh[:,1], inSNPs)]
		if len(cienh)>0:
			posmin = [int(x.split(":")[1].split("-")[0]) for x in cienh[:,4]]
			posmax = [int(x.split(":")[1].split("-")[1]) for x in cienh[:,4]]
			cireg = np.c_[posmin, posmax, cienh[:,5:7]]
		ciprom = pd.read_csv(filedir+"ciProm.txt", sep="\t")
		ciprom = np.array(ciprom)
		if len(ciprom)>0:
			ciprom = ciprom[ArrayIn(ciprom[:,0], ci[:,2])]
		if len(ciprom)>0:
			posmin = [int(x.split(":")[1].split("-")[0]) for x in ciprom[:,1]]
			posmax = [int(x.split(":")[1].split("-")[1]) for x in ciprom[:,1]]
			if len(cireg) > 0:
				cireg = np.r_[cireg, np.c_[posmin, posmax, ciprom[:,2:4]]]
		if len(cireg) > 0:
			cireg = np.array(cireg)
			cireg = np.array(list(i for i in set(map(tuple, cireg))))
			eid = unique(cireg[:,3])

		return [min_pos, max_pos, ciout, cireg, citypes, ciheight, eid]
	else:
		return [min_pos, max_pos, [], [], [], [], []]

def getNonCandidateSNPs(filedir, snps, min_pos, max_pos):
	chrom = int(snps[0,1])
	chrcol = 0
	poscol = 1

	tb = tabix.open(filedir+"all.txt.gz")
	tb_snps = tb.querys(str(chrom)+":"+str(max([min_pos-500000,0]))+"-"+str(max_pos+500000))
	tmp = []
	for l in tb_snps:
		tmp.append([int(l[0]), int(l[1]), float(l[2])])
	tmp = np.array(tmp)
	tmp = tmp[ArrayNotIn(tmp[:,poscol], snps[:,3])]

	### filter SNPs if there are too many #####
	if len(tmp)>10000:
		tmp_keep = tmp[tmp[:,2]<0.05]
		tmp = tmp[tmp[:,2]>=0.05]
		step = int(len(tmp)/(10000-len(tmp_keep)))+1
		tmp = tmp[np.arange(0,len(tmp), step)]
		tmp = np.r_[tmp, tmp_keep]

	out = []
	for l in tmp:
		out.append([int(l[0]), int(l[1]), l[2]])
	return out

def getChr15(filedir, snps, Chr15, Chr15cells, chr15dir):
	if int(Chr15)==1:
		annot = pd.read_csv(filedir+"annot.txt", sep="\t")
		annothead = list(annot.columns.values)
		annot = annot.as_matrix()
		annot = annot[ArrayIn(annot[:,0], snps[:,0])]
		if Chr15cells[0]=="all":
		    Chr15cells = list(annothead[3:len(annothead)])
		for c in Chr15cells:
			snps = np.c_[snps, annot[:,annothead.index(c)]]
		Chr15data = []
		chrom = int(snps[0,1])
		start = min(snps[:,2])
		end = max(snps[:,2])
		if end-start == 0:
			end += 500
			start -= 500
		for i in Chr15cells:
			tb = tabix.open(chr15dir+"/"+str(i)+"_core15.bed.gz")
			tmp = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))
			for l in tmp:
				if int(l[1]<start):
					l[1] = str(start)
				if int(l[2] > str(end)):
					l[2] = str(end)
				Chr15data.append([i, int(l[1]), int(l[2]), int(l[3])])
		# Chr15data = np.array(Chr15data)
		return [snps, Chr15data]
	else:
		return [snps, []]

def geteQTLs(filedir, snps, eqtlplot):
	if eqtlplot==1:
		eqtl = pd.read_csv(filedir+"eqtl.txt", sep="\t")
		eqtlhead = list(eqtl.columns.values)
		eqtl = eqtl.as_matrix()
		eqtl = eqtl[ArrayIn(eqtl[:,0], snps[:,0])]
		snps = np.c_[snps, ["NA"]*len(snps)]

		for l in range(0,len(snps)):
			if snps[l,0] in eqtl[:,0]:
				temp = eqtl[eqtl[:,0]==snps[l,0]]
				out = []
				for e in temp:
					out.append(":".join(e.astype(str)[[1,2,10,5,7]]))
				snps[l, len(snps[0])-1] = "</br>".join(out)
		return [snps, eqtl]
	else:
		return [snps, []]

def main():
	##### check argument #####
	if len(sys.argv)<11:
		sys.exit("ERROR: not enough arguments\nUSAGE ./annotPlot.py <filedir> <type> <row index> <GWAS> <CADD> <RDB> <eqtl> <ci> <Chr15> <Chr15cells>")

	##### get command line arguments #####
	filedir = sys.argv[1]
	Type = sys.argv[2]
	rowI = int(sys.argv[3])
	GWAS = int(sys.argv[4])
	CADD = int(sys.argv[5])
	RDB = int(sys.argv[6])
	eqtlplot = int(sys.argv[7])
	ciplot = int(sys.argv[8])
	Chr15 = int(sys.argv[9])
	Chr15cells = sys.argv[10]
	if Chr15cells=="NA":
		Chr15cells = ["NA"]
	else:
		Chr15cells = Chr15cells.split(":")
		if "all" in Chr15cells:
			Chr15cells = ["all"]

	##### add '/' to the filedir #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### get Parameters #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')
	chr15dir = cfg.get('data', 'chr15')

	param = ConfigParser.RawConfigParser()
	param.optionxform = str
	param.read(filedir+'params.config')
	posMapAnnot = param.get('posMap', 'posMapAnnot')
	posMapCADDth = param.get('posMap', 'posMapCADDth')
	posMapRDBth = param.get('posMap', 'posMapRDBth')
	posMapChr15 = param.get('posMap', 'posMapChr15')
	posMapChr15Max = param.get('posMap', 'posMapChr15Max')
	posMapChr15Meth = param.get('posMap', 'posMapChr15Meth')

	eqtlMapCADDth = param.get('eqtlMap', 'eqtlMapCADDth')
	eqtlMapRDBth = param.get('eqtlMap', 'eqtlMapRDBth')
	eqtlMapChr15 = param.get('eqtlMap', 'eqtlMapChr15')
	eqtlMapChr15Max = param.get('eqtlMap', 'eqtlMapChr15Max')
	eqtlMapChr15Meth = param.get('eqtlMap', 'eqtlMapChr15Meth')

	##### get SNPs data #####
	[snps, gl] = getSNPs(filedir, rowI, Type, eqtlplot, ciplot)
	##### get chromatin interaction #####
	[min_pos, max_pos, cidata, cireg, citypes, ciheight, cieid] = getCI(filedir, snps, ciplot, gl)

	##### get non candidate SNPs #####
	osnps = getNonCandidateSNPs(filedir, snps, min_pos, max_pos)

	##### get Chr15 #####
	[snps, Chr15data] = getChr15(filedir, snps, Chr15, Chr15cells, chr15dir)
	##### get eqtl #####
	[snps, eqtldata] = geteQTLs(filedir, snps, eqtlplot)
	if len(eqtldata) > 0:
		eqtlgenes = unique(eqtldata[:,-2])
	else:
		eqtlgenes = ["NA"]

	##### output #####
	out = {}
	out["chrom"] = int(snps[0,1])
	out["snps"] = [list(l) for l in snps]
	out["eqtl"] = [ list(l) for l in eqtldata]
	out["eqtlNgenes"] = len(eqtlgenes)
	out["eqtlgenes"] = ":".join(eqtlgenes)
	out["ci"] = [list(l) for l in cidata]
	out["cireg"] = [list(l) for l in cireg]
	out["citypes"] = citypes
	out["ciheight"] = ciheight
	out["cieid"] = cieid
	out["Chr15"] = [list(l) for l in Chr15data]
	out["osnps"] = [list(l) for l in osnps]
	out["xMin"] = min_pos
	out["xMax"] = max_pos
	out["xMin_init"] = min(snps[:,2])
	out["xMax_init"] = max(snps[:,2])
	print json.dumps(out)

if __name__ == "__main__": main()
