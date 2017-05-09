#!/usr/bin/python
############################################
# chromatin interactions (loops) file format
# 0:chr1 (int), 1:start1 (int), 2:end1 (int), 3:chr2 (int), 4:start2 (int), 5:end2 (int), 6:FDR (float)
# total 7 columns
############################################
import pandas as pd
import numpy as np
import sys
import os
import re
import scipy.stats as st
import ConfigParser
import time
import tabix
import glob
from bisect import bisect_left

##### return unique element in list #####
def unique(a):
	unique = []
	[unique.append(s) for s in a if s not in unique]
	return unique

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results

def getSNPs(filedir):
	snps = pd.read_table(filedir+"snps.txt", sep="\t")
	snpshead = list(snps.columns.values)
	snps = np.array(snps)
	snps = snps[:,[0,1,2,3,snpshead.index("GenomicLocus")]]
	snps = snps[np.lexsort((snps[:,3].astype(int), snps[:,2].astype(int)))]
	return snps

def getRow(dat, chrom, pos):
	n = np.where((dat[:,0].astype(int)==chrom) & (dat[:,1].astype(int)<=pos) & (dat[:,2].astype(int)>=pos))[0]
	return n

def getGenes(genes, start, end):
	'''
	input genes need to be filtered by the chromosome
	'''
	n = np.where(((genes[:,3].astype(int)>=start) & (genes[:,3].astype(int)<=end)) | ((genes[:,4].astype(int)>=start) & (genes[:,4].astype(int)<=end)) | ((genes[:,3].astype(int)<=start) & (genes[:,4].astype(int)>=end)))[0]
	if len(n) > 0:
		genes = genes[n]
		# dist = [str(min(abs(x-start), abs(x-end))) for x in genes[:,2].astype(int)]
		# return [":".join(genes[:,0]), ":".join(dist)]
		return ":".join(genes[:,0])
	else:
		# dist = [min(abs(x-start), abs(x-end)) for x in genes[:,2].astype(int)]
		# i = dist.index(min(dist))
		return "NA"

def mapToCI(snps, f, ciMapFDR, dt, DB, ts, genes):
	mapdat = pd.read_table(f, comment="#", delim_whitespace=True)
	mapdat = np.array(mapdat)
	if len(mapdat) < 7:
		sys.exit("ERROR: a uploaded file of chromatin interactions does not contain enought columns.")
	if not isinstance(mapdat[0,1], (int, long, float, complex)) or not isinstance(mapdat[0,2], (int, long, float, complex)) or not isinstance(mapdat[0,4], (int, long, float, complex)) or not isinstance(mapdat[0,5], (int, long, float, complex)) or not isinstance(mapdat[0,6], (int, long, float, complex)):
		sys.exit("ERROR: the format of a uploaded file of chromatin interactions is wrong.")
	mapdat = mapdat[mapdat[:,6].astype(float)<ciMapFDR]
	if len(mapdat) == 0:
		print "No interaction left after filtering for "+f
		return []

	print "Significant interactions: "+str(len(mapdat))

	if mapdat[0,0].dtype is np.str:
		mapdat[:,0] = [int(re.sub(r'X|x', '23', x.replace("chr",""))) for x in mapdat[:,0]]
	if mapdat[0,3].dtype is np.str:
		mapdat[:,3] = [int(re.sub(r'X|x', '23', x.replace("chr",""))) for x in mapdat[:,0]]

	chrdat1 = {}
	chrdat2 = {}
	for i in range(1,24):
		chrdat1[i] = mapdat[mapdat[:,0]==i]
		chrdat2[i] = mapdat[mapdat[:,3]==i]

	mapdat = None

	out = []
	cur_chr = 0
	cur_max = 0
	last_n = []
	interaction = ""
	for i in range(0,len(snps)):
		if int(snps[i,2]) == cur_chr and int(snps[i,3]) <= cur_max:
			for j in last_n:
				out[j][8] = ":".join([str(out[j][8]), str(snps[i,1])])
		else:
			cur_chr = int(snps[i,2])
			last_n=[]
			tmpdat = chrdat1[int(snps[i,2])]
			n = getRow(tmpdat[:,0:3], int(snps[i,2]), int(snps[i,3]))
			if len(n) > 0:
				for j in n:
					cur_max = int(tmpdat[j,2])
					r1 = str(int(tmpdat[j,0]))+":"+str(int(tmpdat[j,1]))+"-"+str(int(tmpdat[j,2]))
					r2 = str(int(tmpdat[j,3]))+":"+str(int(tmpdat[j,4]))+"-"+str(int(tmpdat[j,5]))
					if int(tmpdat[j,0]) == int(tmpdat[j,3]):
						interaction = "intra"
					else:
						interaction = "inter"
					out.append([snps[i,4], r1, r2, tmpdat[j,6], dt, DB, ts, interaction, snps[i,1]])
					last_n.append(len(out)-1)

			tmpdat = chrdat2[int(snps[i,2])]
			n = getRow(tmpdat[:,3:6], int(snps[i,2]), int(snps[i,3]))
			if len(n) > 0:
				for j in n:
					cur_max = int(tmpdat[j,5])
					r1 = str(int(tmpdat[j,3]))+":"+str(int(tmpdat[j,4]))+"-"+str(int(tmpdat[j,5]))
					r2 = str(int(tmpdat[j,0]))+":"+str(int(tmpdat[j,1]))+"-"+str(int(tmpdat[j,2]))
					if int(tmpdat[j,0]) == int(tmpdat[j,3]):
						interaction = "intra"
					else:
						interaction = "inter"
					out.append([snps[i,4], r1, r2, tmpdat[j,6], dt, DB, ts, interaction, snps[i,1]])
					last_n.append(len(out)-1)
	mappedGenes = []
	for l in out:
		c = re.match(r'(\d+):(\d+)-(\d+)', l[2])
		chrom = int(c.group(1))
		min_pos = int(c.group(2))
		max_pos = int(c.group(3))
		tmp = getGenes(genes[genes[:,1].astype(int)==chrom], min_pos, max_pos)
		mappedGenes.append(tmp)
	out = np.array(out)
	out = np.c_[out, mappedGenes]
	return out

def mapToRegElements(snps, f, dt, ts):
	mapdat = pd.read_table(f, comment="#", delim_whitespace=True, header=None)
	mapdat = np.array(mapdat)
	mapdat = mapdat[:,0:3]
	mapdat[:,0] = [int(re.sub(r'X|x', '23', x.replace("chr",""))) for x in mapdat[:,0]]
	mapdat[:,1] = [int(x)+1 for x in mapdat[:,1]]
	mapdat[:,2] = [int(x)+1 for x in mapdat[:,2]]
	print len(mapdat)

	out = []
	chrdat = {}
	for i in range(1,24):
		chrdat[i] = mapdat[mapdat[:,0].astype(int)==i]
	mapdat = None

	for i in range(0, len(snps)):
		tmpdat = chrdat[int(snps[i,2])]
		if len(tmpdat)==0:
			continue
		n = getRow(tmpdat, int(snps[i,2]), int(snps[i,3]))
		for j in n:
			r = str(int(tmpdat[j,0]))+":"+str(int(tmpdat[j,1]))+"-"+str(int(tmpdat[j,2]))
			out.append(list(snps[i, 0:4])+[r, dt, ts])
	return np.array(out)

def GeneToPromoter(genes, promoter):
	"""
	return [ensg, chr, TSS, min, max]
	"""
	out = []
	for l in genes:
		if int(l[4]) == 1:
			out.append([l[0], l[1], l[2], l[2]-promoter[0], l[2]+promoter[1]])
		else:
			out.append([l[0], l[1], l[3], l[3]-promoter[1], l[3]+promoter[0]])
	return np.array(out)

def getciprom(dat, chrom, min_pos, max_pos, genes):
	if len(dat) == 0:
		return []
	tmpdat = dat[np.where(((dat[:,1].astype(int)>=min_pos) & (dat[:,1].astype(int)<=max_pos)) | ((dat[:,2].astype(int)>=min_pos) & (dat[:,2].astype(int)<=max_pos)) | ((dat[:,1].astype(int)<=min_pos) & (dat[:,2].astype(int)>=max_pos)))]
	if len(tmpdat) == 0:
		return []
	out = []
	for l in tmpdat:
		r = str(l[0])+":"+str(l[1])+"-"+str(l[2])
		g = getGenes(genes, int(l[1]), int(l[2]))
		out.append([r, g])
	return np.array(out)

def RegionToGenes(regions, f, dt, ts, genes):
	mapdat = pd.read_table(f, comment="#", delim_whitespace=True, header=None)
	mapdat = np.array(mapdat)
	mapdat = mapdat[:,0:3]
	mapdat[:,0] = [int(re.sub(r'X|x', '23', x.replace("chr",""))) for x in mapdat[:,0]]
	mapdat[:,1] = [int(x)+1 for x in mapdat[:,1]]
	mapdat[:,2] = [int(x)+1 for x in mapdat[:,2]]
	print len(mapdat)

	out = []
	chrdat = {}
	for i in range(1,24):
		chrdat[i] = mapdat[mapdat[:,0].astype(int)==i]
	mapdat = None

	for i in range(0, len(regions)):
		c = re.match(r'(\d+):(\d+)-(\d+)', regions[i])
		chrom = int(c.group(1))
		min_pos = int(c.group(2))
		max_pos = int(c.group(3))
		tmpdat = chrdat[chrom]
		tmp_out = getciprom(tmpdat, chrom, min_pos, max_pos, genes[genes[:,1].astype(int)==chrom])
		if len(tmp_out) > 0:
			tmp_out = np.c_[[regions[i]]*len(tmp_out), tmp_out[:,0], [dt]*len(tmp_out), [ts]*len(tmp_out), tmp_out[:,1]]
			if len(out) == 0:
				out = tmp_out
			else:
				out = np.r_[out, tmp_out]
	return out

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./getCI.py <filedir>')

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

	##### get parameters #####
	ENSG = cfg.get("data", "ENSG")
	datadir = cfg.get("CI", "CIdata")
	reg_datadir = cfg.get("CI", "open_chromatin")
	ciMapBuildin = param.get("ciMap", "ciMapBuildin")
	if ciMapBuildin=="NA":
		ciMapBuildin = ["NA"]
	else:
		ciMapBuildin = ciMapBuildin.split(":")

	if "all" in ciMapBuildin:
		tmp = glob.glob(datadir+"/HiC/GSE87112/*.txt.gz")
		ciMapBuildin = [x.replace(datadir+"/", "") for x in tmp]
	ciMapFileN = int(param.get("ciMap", "ciMapFileN"))
	if ciMapFileN > 0:
		ciMapFiles = param.get("ciMap", "ciMapFiles")
		ciMapFiles = ciMapFiles.split(":")
	ciMapFDR = float(param.get("ciMap", 'ciMapFDR'))
	ciMapRoadmap = param.get("ciMap", "ciMapRoadmap")
	ciMapRoadmap = ciMapRoadmap.split(":")
	if "all" in ciMapRoadmap:
		tmp = glob.glob(reg_datadir+"/enh/*.bed.gz")
		# ciMapRoadmap = [x.replace(reg_datadir+"/", "") for x in tmp]
		ciMapRoadmap = [re.match(r'.+regions_enh_(E\d+)\.bed.gz', x).group(1) for x in tmp]
	promoter = param.get("ciMap", "ciMapPromWindow")
	promoter = [int(x) for x in promoter.split("-")]
	genetype = param.get("params", "genetype")
	genetype = list(genetype.split(":"))

	snps = getSNPs(filedir)

	##### get genes #####
	genes = pd.read_table(ENSG+"/ENSG.all.genes.txt", sep="\t", header=None)
	genes = np.array(genes)
	genes = genes[:, [1,3,4,5,6,8]]
	genes[:,1] = [int(x.replace("X", "23")) for x in genes[:,1]]
	genes = genes[ArrayIn(genes[:,5], genetype)]
	genes = GeneToPromoter(genes, promoter)

	##### outputs #####
	ci = []
	cisnps = []
	ciprom = []
	outci = filedir+"ci.txt"
	outsnps = filedir+"ciSNPs.txt"
	outgenes = filedir+"ciProm.txt"

	##### Map SNPs to 3DC data #####
	if ciMapFileN > 0:
		for f in ciMapFiles:
			print f
			c = re.match(r"(.+?)\/(.+?)\/(.+?)\.txt.gz", f)
			tmp_ci = mapToCI(snps, filedir+c.group(3)+".txt.gz", ciMapFDR, c.group(1), c.group(2), c.group(3), genes)
			if len(tmp_ci)>0:
				if len(ci) == 0:
					ci = tmp_ci
				else:
					ci = np.r_[ci, tmp_ci]

	if ciMapBuildin[0] != "NA":
		for f in ciMapBuildin:
			print f
			c = re.match(r"(.+?)\/(.+?)\/(.+?)\.txt.gz", f)
			tmp_ci = mapToCI(snps, datadir+"/"+f, ciMapFDR, c.group(1), c.group(2), c.group(3), genes)
			if len(tmp_ci)>0:
				if len(ci) == 0:
					ci = tmp_ci
				else:
					ci = np.r_[ci, tmp_ci]

	insnps = []
	for x in ci[:,8]:
		insnps += x.split(":")
	insnps = unique(insnps)
	snps = snps[ArrayIn(snps[:,1], insnps)]
	print len(snps)

	##### Map SNPs to reguratory elements #####
	if ciMapRoadmap[0] != "NA":
		for eid in ciMapRoadmap:
			f = "enh/regions_enh_"+eid+".bed.gz"
			print f
			tmp_cisnps = mapToRegElements(snps, reg_datadir+"/"+f, "enh", eid)
			if len(tmp_cisnps) > 0:
				if len(cisnps) == 0:
					cisnps = tmp_cisnps
				else:
					cisnps = np.r_[cisnps, tmp_cisnps]
			f = "dyadic/regions_dyadic_"+eid+".bed.gz"
			print f
			tmp_cisnps = mapToRegElements(snps, reg_datadir+"/"+f, "dyadic", eid)
			if len(tmp_cisnps) > 0:
				if len(cisnps) == 0:
					cisnps = tmp_cisnps
				else:
					cisnps = np.r_[cisnps, tmp_cisnps]

	##### Map CI regions to reguratory elements #####
	regions = unique(ci[:,1])
	if ciMapRoadmap[0] != "NA":
		for eid in ciMapRoadmap:
			f = "prom/regions_prom_"+eid+".bed.gz"
			print f
			tmp_ciprom = RegionToGenes(regions, reg_datadir+"/"+f, "prom", eid, genes)
			if len(tmp_ciprom) > 0:
				if len(ciprom) == 0:
					ciprom = tmp_ciprom
				else:
					ciprom = np.r_[ciprom, tmp_ciprom]
			f = "dyadic/regions_dyadic_"+eid+".bed.gz"
			print f
			tmp_ciprom = RegionToGenes(regions, reg_datadir+"/"+f, "dyadic", eid, genes)
			if len(tmp_ciprom) > 0:
				if len(ciprom) == 0:
					ciprom = tmp_ciprom
				else:
					ciprom = np.r_[ciprom, tmp_ciprom]

	##### write outputs #####
	with open(outci, 'w') as o:
		o.write("\t".join(["GenomicLocus", "region1", "region2", "FDR", "type", "DB", "tissue/cell", "inter/intra", "SNPs", "genes"])+"\n")
	with open(outci, 'a') as o:
		np.savetxt(o, ci, delimiter="\t", fmt="%s")

	with open(outsnps, 'w') as o:
		o.write("\t".join(["uniqID", "rsID", "chr", "pos", "reg_region", "type", "tissue/cell"])+"\n")
	with open(outsnps, 'a') as o:
		np.savetxt(o, cisnps, delimiter="\t", fmt="%s")

	with open(outgenes, 'w') as o:
		o.write("\t".join(["region2", "reg_region", "type", "tissue/cell", "genes"])+"\n")
	with open(outgenes, 'a') as o:
		np.savetxt(o, ciprom, delimiter="\t", fmt="%s")

	print time.time() - start


if __name__ == "__main__": main()
