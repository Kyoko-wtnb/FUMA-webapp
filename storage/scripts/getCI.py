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
	results = np.where(np.in1d(a1, a2))[0]
	return results

def getSNPs(filedir):
	snps = pd.read_table(filedir+"snps.txt", sep="\t")
	snpshead = list(snps.columns.values)
	snps = np.array(snps)
	snps = snps[:,[0,1,2,3,snpshead.index("GenomicLocus")]]
	snps = snps[np.lexsort((snps[:,3].astype(int), snps[:,2].astype(int)))]
	### stor snps per chr
	snps_chr = {}
	chrom = unique(snps[:,2].astype(int))
	for i in chrom:
		snps_chr[i] = snps[snps[:,2]==i]
	### genomic risk loci
	gl = pd.read_table(filedir+"GenomicRiskLoci.txt", sep="\t")
	gl = np.array(gl)
	gl = gl[:,[0,3,6,7]]

	return snps_chr, gl

def getGenes(genes, start, end):
	'''
	input genes need to be filtered by the chromosome
	'''
	n = np.where(((genes[:,3].astype(int)>=start) & (genes[:,3].astype(int)<=end)) | ((genes[:,4].astype(int)>=start) & (genes[:,4].astype(int)<=end)) | ((genes[:,3].astype(int)<=start) & (genes[:,4].astype(int)>=end)))[0]
	if len(n) > 0:
		genes = genes[n]
		return ":".join(genes[:,0])
	else:
		return "NA"

def mapToCI(snps, gl, f, ciMapFDR, dt, DB, ts, genes):
	s = time.time()
	chunks = pd.read_table(f, comment="#", delim_whitespace=True, chunksize=10000)
	mapdat = []
	for tmp in chunks:
		tmp = np.array(tmp)
		tmp = tmp[tmp[:,6].astype(float)<ciMapFDR]
		if len(tmp)>0:
			if len(mapdat)==0:
				mapdat = tmp
			else:
				mapdat = np.r_[mapdat, tmp]
	if len(mapdat) == 0:
		print "No interaction left after filtering for "+f
		return []

	print "Significant interactions: "+str(len(mapdat))

	if isinstance(mapdat[0,0], str):
		mapdat[:,0] = [int(re.sub(r'X|x', '23', x.replace("chr",""))) for x in mapdat[:,0]]
	if isinstance(mapdat[0,3], str):
		mapdat[:,3] = [int(re.sub(r'X|x', '23', x.replace("chr",""))) for x in mapdat[:,3]]

	### filter interaction based on risk loci
	chrdat1 = {}
	chrdat2 = {}
	for l in gl:
		n1 = list(np.where((mapdat[:,0]==l[1]) & (((mapdat[:,1]>=l[2]) & (mapdat[:,1]<=l[3])) | ((mapdat[:,2]>=l[2]) & (mapdat[:,2]<=l[3])) | ((mapdat[:,1]<l[2]) & (mapdat[:,2]>l[3]))))[0])
		n2 = list(np.where((mapdat[:,3]==l[1]) & (((mapdat[:,4]>=l[2]) & (mapdat[:,4]<=l[3])) | ((mapdat[:,5]>=l[2]) & (mapdat[:,5]<=l[3])) | ((mapdat[:,4]<l[2]) & (mapdat[:,5]>l[3]))))[0])
		if len(n1) > 0:
			n1 = unique(n1)
			if l[1] in chrdat1 and len(chrdat1[l[1]])>0:
				chrdat1[l[1]] = np.r_[chrdat1[l[1]], mapdat[n1]]
			else:
				chrdat1[l[1]] = mapdat[n1]
		if len(n2) > 0:
			n2 = unique(n2)
			if l[1] in chrdat2 and len(chrdat2[l[1]])>0:
				chrdat2[l[1]] = np.r_[chrdat2[l[1]], mapdat[n2]]
			else:
				chrdat2[l[1]] = mapdat[n2]

	mapdat = None
	out = []
	for chrom in range(1,24):
		if chrom not in snps:
			continue
		tmp_snps = snps[chrom]
		if chrom in chrdat1:
			tmp = chrdat1[chrom]
			tmp = tmp[np.lexsort((tmp[:,1], tmp[:,0]))]
			cur_region = ""
			cur_snps = ""
			cur_gl = 0
			for l in tmp:
				r1 = str(int(l[0]))+":"+str(int(l[1]))+"-"+str(int(l[2]))
				if r1 == cur_region:
					r2 = str(int(l[3]))+":"+str(int(l[4]))+"-"+str(int(l[5]))
					if l[0]==l[3]:
						interaction = "intra"
					else:
						interaction = "inter"
					out.append([cur_gl, r1, r2, l[6], dt, DB, ts, interaction, cur_snps])
				else:
					n = np.where((tmp_snps[:,3]>=l[1]) & (tmp_snps[:,3]<=l[2]))[0]
					if len(n) > 0:
						cur_region = r1
						r2 = str(int(l[3]))+":"+str(int(l[4]))+"-"+str(int(l[5]))
						gl = ":".join(str(x) for x in unique(tmp_snps[n,4]))
						cur_gl = gl
						cur_snps = ";".join(unique(tmp_snps[n,1]))
						if l[0]==l[3]:
							interaction = "intra"
						else:
							interaction = "inter"
						out.append([cur_gl, r1, r2, l[6], dt, DB, ts, interaction, cur_snps])
		if chrom in chrdat2:
			tmp = chrdat2[chrom]
			tmp = tmp[np.lexsort((tmp[:,4], tmp[:,3]))]
			cur_region = ""
			cur_snps = ""
			cur_gl = 0
			for l in tmp:
				r1 = str(int(l[3]))+":"+str(int(l[4]))+"-"+str(int(l[5]))
				if r1 == cur_region:
					r2 = str(int(l[0]))+":"+str(int(l[1]))+"-"+str(int(l[2]))
					if l[0]==l[3]:
						interaction = "intra"
					else:
						interaction = "inter"
					out.append([cur_gl, r1, r2, l[6], dt, DB, ts, interaction, cur_snps])
				else:
					n = np.where((tmp_snps[:,3]>=l[4]) & (tmp_snps[:,3]<=l[5]))[0]
					if len(n) > 0:
						cur_region = r1
						r2 = str(int(l[0]))+":"+str(int(l[1]))+"-"+str(int(l[2]))
						gl = ":".join(str(x) for x in unique(tmp_snps[n,4]))
						cur_gl = gl
						cur_snps = ";".join(unique(tmp_snps[n,1]))
						if l[0]==l[3]:
							interaction = "intra"
						else:
							interaction = "inter"
						out.append([cur_gl, r1, r2, l[6], dt, DB, ts, interaction, cur_snps])

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

def mapSNPsToRegElements(snps, gl, reg_datadir, ts):
	out = []
	for i in range(0, len(gl)):
		chrom = gl[i,1]
		if chrom not in snps:
			continue
		tmp = snps[chrom]
		tmp = tmp[tmp[:,4]==gl[i,0]]
		if len(tmp)==0:
			continue
		start = min(tmp[:,3])
		end = max(tmp[:,3])
		tb = tabix.open(reg_datadir+"/enh/enh.bed.gz")
		enh = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))
		for l in enh:
			if "all" in ts or l[3] in ts:
				r = str(chrom)+":"+str(int(l[1])+1)+"-"+str(int(l[2])+1)
				tmp_snps = tmp[np.where((tmp[:,3]>int(l[1])) & (tmp[:,3]<int(l[2])+1))]
				for m in tmp_snps:
					out.append(list(m[0:4])+[r, "enh", l[3]])
		tb = tabix.open(reg_datadir+"/dyadic/dyadic.bed.gz")
		dyadic = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))
		for l in dyadic:
			if "all" in ts or l[3] in ts:
				r = str(chrom)+":"+str(int(l[1])+1)+"-"+str(int(l[2])+1)
				tmp_snps = tmp[np.where((tmp[:,3]>int(l[1])) & (tmp[:,3]<int(l[2])+1))]
				for m in tmp_snps:
					out.append(list(m[0:4])+[r, "dyadic", l[3]])
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
	out = []
	for l in dat:
		r = str(l[0])+":"+str(int(l[1])+1)+"-"+str(int(l[2])+1)
		g = getGenes(genes, int(l[1]), int(l[2]))
		out.append([r, l[4], l[3], g])
	return np.array(out)

def mapRegionToGenes(regions, reg_datadir, ts, genes):
	out = []
	for reg in regions:
		c = re.match(r'(\d+):(\d+)-(\d+)', reg)
		chrom = int(c.group(1))
		start = int(c.group(2))
		end = int(c.group(3))
		mapdat = []

		tb = tabix.open(reg_datadir+"/prom/prom.bed.gz")
		prom = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))
		for l in prom:
			if "all" in ts or l[3] in ts:
				l = l+["prom"]
				mapdat.append(l)
		tb = tabix.open(reg_datadir+"/dyadic/dyadic.bed.gz")
		dyadic = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))
		for l in dyadic:
			if "all" in ts or l[3] in ts:
				l = l+["dyadic"]
				mapdat.append(l)
		mapdat = np.array(mapdat)

		tmp_out = getciprom(mapdat, chrom, start, end, genes[genes[:,1].astype(int)==chrom])
		if len(tmp_out) > 0:
			tmp_out = np.c_[[reg]*len(tmp_out),tmp_out]
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
	start_time = time.time()

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
	reg_datadir = cfg.get("CI", "RoadmapData")
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
		ciMapRoadmap = ["all"]
	promoter = param.get("ciMap", "ciMapPromWindow")
	promoter = [int(x) for x in promoter.split("-")]
	genetype = param.get("params", "genetype")
	genetype = list(genetype.split(":"))

	snps, gl = getSNPs(filedir)

	##### get genes #####
	genes = pd.read_table(ENSG, sep="\t")
	genes = np.array(genes)
	genes = genes[:, [0,2,3,4,5,7]]
	genes[:,1] = [int(str(x).replace("X", "23")) for x in genes[:,1]]
	if "all" not in genetype:
		genes = genes[ArrayIn(genes[:,5], genetype)]
	genes = GeneToPromoter(genes, promoter)

	##### outputs #####
	cisnps = []
	ciprom = []
	outci = filedir+"ci.txt"
	outsnps = filedir+"ciSNPs.txt"
	outgenes = filedir+"ciProm.txt"

	##### write headers #####
	with open(outci, 'w') as o:
		o.write("\t".join(["GenomicLocus", "region1", "region2", "FDR", "type", "DB", "tissue/cell", "inter/intra", "SNPs", "genes"])+"\n")
	with open(outsnps, 'w') as o:
		o.write("\t".join(["uniqID", "rsID", "chr", "pos", "reg_region", "type", "tissue/cell"])+"\n")
	with open(outgenes, 'w') as o:
		o.write("\t".join(["region2", "reg_region", "type", "tissue/cell", "genes"])+"\n")

	##### Map SNPs to 3DC data #####
	insnps = []
	regions = []
	if ciMapFileN > 0:
		for f in ciMapFiles:
			print f
			c = re.match(r"(.+?)\/(.+?)\/(.+?)\.txt.gz", f)
			tmp_ci = mapToCI(snps, gl, filedir+c.group(3)+".txt.gz", ciMapFDR, c.group(1), c.group(2), c.group(3), genes)
			if len(tmp_ci)>0:
				with open(outci, 'a') as o:
					np.savetxt(o, tmp_ci, delimiter="\t", fmt="%s")
				for x in tmp_ci[:,8]:
					insnps += x.split(";")
				regions = regions+unique(tmp_ci[:,1])

	if ciMapBuildin[0] != "NA":
		for f in ciMapBuildin:
			print f
			c = re.match(r"(.+?)\/(.+?)\/(.+?)\.txt.gz", f)
			tmp_ci = mapToCI(snps, gl, datadir+"/"+f, ciMapFDR, c.group(1), c.group(2), c.group(3), genes)
			if len(tmp_ci)>0:
				with open(outci, 'a') as o:
					np.savetxt(o, tmp_ci, delimiter="\t", fmt="%s")
				for x in tmp_ci[:,8]:
					insnps += x.split(";")
				regions = regions+unique(tmp_ci[:,1])

	insnps = np.unique(insnps)
	for i in range(1,24):
		if i in snps:
			snps[i] = snps[i][ArrayIn(snps[i][:,1], insnps)]

	##### Map SNPs to reguratory elements #####
	if ciMapRoadmap[0] != "NA" and len(snps) > 0:
		cisnps = mapSNPsToRegElements(snps, gl, reg_datadir, ciMapRoadmap)

	##### Map CI regions to reguratory elements #####
	if ciMapRoadmap[0] != "NA" and len(regions) > 0:
		regions = unique(regions)
		ciprom = mapRegionToGenes(regions, reg_datadir, ciMapRoadmap, genes)

	##### write outputs #####
	with open(outsnps, 'a') as o:
		np.savetxt(o, cisnps, delimiter="\t", fmt="%s")

	with open(outgenes, 'a') as o:
		np.savetxt(o, ciprom, delimiter="\t", fmt="%s")

	print time.time() - start_time


if __name__ == "__main__": main()
