#!/usr/bin/python
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
	snps = np.array(snps)
	snps = snps[:,0:4]
	snps = snps[np.lexsort((snps[:,3].astype(int), snps[:,2].astype(int)))]
	return snps

def getRow(dat, chrom, pos):
	n = np.where((dat[:,0].astype(int)==chrom) & (dat[:,1].astype(int)<=pos) & (dat[:,2].astype(int)>=pos))[0]
	return n

def mapTo3DC(snps, f, minScore, dt, DB, name):
	mapdat = pd.read_table(f, comment="#", delim_whitespace=True)
	mapdat = np.array(mapdat)
	mapdat = mapdat[mapdat[:,6].astype(float)<minScore]
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
				out[j][7] = ":".join([str(out[j][7]), str(snps[i,1])])
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
						interaction = "inter"
					else:
						interaction = "intra"
					out.append([r1, r2, tmpdat[j,6], dt, DB, name, interaction, snps[i,1]])
					last_n.append(len(out)-1)

			tmpdat = chrdat2[int(snps[i,2])]
			n = getRow(tmpdat[:,3:6], int(snps[i,2]), int(snps[i,3]))
			if len(n) > 0:
				for j in n:
					cur_max = int(tmpdat[j,5])
					r1 = str(int(tmpdat[j,3]))+":"+str(int(tmpdat[j,4]))+"-"+str(int(tmpdat[j,5]))
					r2 = str(int(tmpdat[j,0]))+":"+str(int(tmpdat[j,1]))+"-"+str(int(tmpdat[j,2]))
					if int(tmpdat[j,0]) == int(tmpdat[j,3]):
						interaction = "inter"
					else:
						interaction = "intra"
					out.append([r1, r2, tmpdat[j,6], dt, DB, name, interaction, snps[i,1]])
					last_n.append(len(out)-1)
	return np.array(out)

def mapToRegElements(snps, f, dt, name):
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
			out.append(list(snps[i])+[r, dt, name])
	return np.array(out)

def GeneToPromoter(genes, promoter):
	out = []
	for l in genes:
		if int(l[4]) == 1:
			out.append([l[0], l[1], l[2], l[2]-promoter[0], l[2]+promoter[1]])
		else:
			out.append([l[0], l[1], l[3], l[3]-promoter[1], l[3]+promoter[0]])
	return np.array(out)

def getGenes(genes, start, end):
	n = np.where(((genes[:,3].astype(int)>=start) & (genes[:,3].astype(int)<=end)) | ((genes[:,4].astype(int)>=start) & (genes[:,4].astype(int)<=end)) | ((genes[:,3].astype(int)<=start) & (genes[:,4].astype(int)>=end)))[0]
	if len(n) > 0:
		genes = genes[n]
		dist = [str(min(abs(x-start), abs(x-end))) for x in genes[:,2].astype(int)]
		return [":".join(genes[:,0]), ":".join(dist)]
	else:
		dist = [min(abs(x-start), abs(x-end)) for x in genes[:,2].astype(int)]
		i = dist.index(min(dist))
		return [genes[i,0], str(dist[i])]

def getRegGenes(dat, chrom, min_pos, max_pos, promoter, genes):
	if len(dat) == 0:
		return []
	tmpdat = dat[np.where(((dat[:,1].astype(int)>=min_pos) & (dat[:,1].astype(int)<=max_pos)) | ((dat[:,2].astype(int)>=min_pos) & (dat[:,2].astype(int)<=max_pos)) | ((dat[:,1].astype(int)<=min_pos) & (dat[:,2].astype(int)>=max_pos)))]
	if len(tmpdat) == 0:
		return []
	out = []
	for l in tmpdat:
		r = str(l[0])+":"+str(l[1])+"-"+str(l[2])
		g = getGenes(genes, int(l[1]), int(l[2]))
		out.append([r]+g)
	return np.array(out)

def RegionToGenes(regions, f, dt, name, promoter, genes):
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
		tmp_out = getRegGenes(tmpdat, chrom, min_pos, max_pos, promoter, genes[genes[:,1].astype(int)==chrom])
		if len(tmp_out) > 0:
			tmp_out = np.c_[[regions[i]]*len(tmp_out), tmp_out[:,0], [dt]*len(tmp_out), [name]*len(tmp_out), tmp_out[:,1], tmp_out[:,2]]
			if len(out) == 0:
				out = tmp_out
			else:
				out = np.r_[out, tmp_out]
	return out

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./paintor.py <filedir>')

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
	datadir = cfg.get("3DC", "3DCdata")
	reg_datadir = cfg.get("3DC", "reg_elements")
	buildin_data = param.get("ciMap", "buildin_data")
	buildin_data = buildin_data.split(":")
	if "all" in buildin_data:
		tmp = glob.glob(datadir+"/HiC/GSE87112/*.txt.gz")
		buildin_data = [x.replace(datadir+"/", "") for x in tmp]
	reg_elements = param.get("ciMap", "reg_elements")
	reg_elements = reg_elements.split(":")
	if "all" in reg_elements:
		tmp = glob.glob(reg_datadir+"/*/*.bed.gz")
		reg_elements = [x.replace(reg_datadir+"/", "") for x in tmp]
	minScore = float(param.get("ciMap", 'minScore'))
	promoter = param.get("ciMap", "promoter")
	promoter = [int(x) for x in promoter.split("-")]
	genetype = param.get("params", "genetype")
	genetype = list(genetype.split(":"))

	snps = getSNPs(filedir)

	##### outputs #####
	snps3dc = []
	snpsreg = []
	reggenes = []
	out3dc = filedir+"3dc.txt"
	outsnps = filedir+"snps_reg.txt"
	outgenes = filedir+"3dc_genes.txt"

	##### Map SNPs to 3DC data #####
	for f in buildin_data:
		print f
		c = re.match(r"(.+?)\/(.+?)\/(.+?)\.txt.gz", f)
		tmp_snps3DC = mapTo3DC(snps, datadir+"/"+f, minScore, c.group(1), c.group(2), c.group(3))
		if len(tmp_snps3DC)>0:
			if len(snps3dc) == 0:
				snps3dc = tmp_snps3DC
			else:
				snps3dc = np.r_[snps3dc, tmp_snps3DC]
	insnps = []
	for x in snps3dc[:,7]:
		insnps += x.split(":")
	insnps = unique(insnps)
	snps = snps[ArrayIn(snps[:,1], insnps)]
	print len(snps)

	##### Map SNPs to reguratory elements #####
	for f in reg_elements:
		print f
		c = re.match(r"(.+?)\/.+_(E\d{3})\.bed\.gz", f)
		tmp_snpsreg = mapToRegElements(snps, reg_datadir+"/"+f, c.group(1), c.group(2))
		if len(tmp_snpsreg) > 0:
			if len(snpsreg) == 0:
				snpsreg = tmp_snpsreg
			else:
				snpsreg = np.r_[snpsreg, tmp_snpsreg]

	##### get genes #####
	genes = pd.read_table(ENSG+"/ENSG.all.genes.txt", sep="\t", header=None)
	genes = np.array(genes)
	genes = genes[:, [1,3,4,5,6,8]]
	genes[:,1] = [int(x.replace("X", "23")) for x in genes[:,1]]
	genes = genes[ArrayIn(genes[:,5], genetype)]
	genes = GeneToPromoter(genes, promoter)

	##### Map 3DC regions to reguratory elements #####
	regions = unique(snps3dc[:,1])
	for f in reg_elements:
		c = re.match(r"(.+?)\/.+_(E\d{3})\.bed\.gz", f)
		if c.group(1) == "enh":
			continue
		print f
		tmp_reggenes = RegionToGenes(regions, reg_datadir+"/"+f, c.group(1), c.group(2), promoter, genes)
		print len(tmp_reggenes)
		print tmp_reggenes[0:3]
		if len(tmp_reggenes) > 0:
			if len(reggenes) == 0:
				reggenes = tmp_reggenes
			else:
				reggenes = np.r_[reggenes, tmp_reggenes]

	##### write outputs #####
	with open(out3dc, 'w') as o:
		o.write("\t".join(["region1", "region2", "FDR", "type", "DB", "name", "SNPs"])+"\n")
	with open(out3dc, 'a') as o:
		np.savetxt(o, snps3dc, delimiter="\t", fmt="%s")

	with open(outsnps, 'w') as o:
		o.write("\t".join(["uniqID", "rsID", "chr", "pos", "reg_region", "type", "name"])+"\n")
	with open(outsnps, 'a') as o:
		np.savetxt(o, snpsreg, delimiter="\t", fmt="%s")

	with open(outgenes, 'w') as o:
		o.write("\t".join(["region", "reg_region", "type", "name", "genes", "distance"])+"\n")
	with open(outgenes, 'a') as o:
		np.savetxt(o, reggenes, delimiter="\t", fmt="%s")

	print time.time() - start


if __name__ == "__main__": main()
