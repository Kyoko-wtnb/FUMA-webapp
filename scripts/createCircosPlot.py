#!/usr/bin/python
import sys
import os
from shutil import copyfile
import re
import pandas as pd
import numpy as np
import math
import ConfigParser
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

def createConfig(c, filedir, circos_config, loci, ci, snps, genes):
	regions = []
	breaks = ""
	loci = loci[loci[:,3].argsort()]

	loci = np.c_[loci, loci[:,4], loci[:,5]]
	for l in ci:
		if min(l[2], l[5]) < loci[loci[:,0]==l[0],6]:
			loci[loci[:,0]==l[0],6] = min(l[2], l[5])
		if max(l[3], l[6]) > loci[loci[:,0]==l[0],7]:
			loci[loci[:,0]==l[0],7] = max(l[3], l[6])
	for l in genes:
		if l[1] < loci[loci[:,0]==int(l[4]),6]:
			loci[loci[:,0]==int(l[4]),6] = l[1]
		if l[2] > loci[loci[:,0]==int(l[4]),7]:
			loci[loci[:,0]==int(l[4]),7] = l[2]
	cur_pos = 0
	tmp_start = []
	tmp_end = []
	for l in loci:
		if cur_pos == 0:
			if int((l[6]-1000)/1000000)<=0:
				tmp_start.append(0)
			else:
				breaks = "-hs"+str(c)+":0-"+str(int((l[6]-1000)/1000000)-1)
				tmp_start.append((int((l[6]-1000)/1000000)-1)*1000000)
			cur_pos = l[7]
		elif (int((l[6]-1000)/1000000)-1)-(int((cur_pos+1000)/1000000)+1) <= 1:
			cur_pos = max(cur_pos, l[7])
		else:
			if len(breaks) > 0:
				breaks += ";"
			breaks += "-hs"+str(c)+":"+str(int((cur_pos+1000)/1000000)+1)+"-"+str(int((l[6]-1000)/1000000)-1)
			tmp_end.append((int((cur_pos+1000)/1000000)+1)*1000000)
			tmp_start.append((int((l[6]-1000)/1000000)-1)*1000000)
			cur_pos = l[7]
	breaks += ";-hs"+str(c)+":"+str(int((cur_pos+1000)/1000000)+1)+"-)"
	tmp_end.append((int((cur_pos+1000)/1000000)+1)*1000000)
	regions = np.c_[tmp_start, tmp_end]

	tb = tabix.open(filedir+"all.txt.gz")
	tmp_snps = []
	for l in regions:
		tb_snps = tb.querys(str(c)+":"+str(l[0])+"-"+str(l[1]))
		tmp = []
		for l in tb_snps:
			tmp.append(l)
		if len(tmp_snps)==0:
			tmp_snps = np.array(tmp)
		else:
			tmp_snps = np.r_[tmp_snps, np.array(tmp)]
	tmp_snps = np.c_[tmp_snps, [0]*len(tmp_snps)]
	tmp_snps = tmp_snps[ArrayNotIn(tmp_snps[:,1].astype(int), snps[:,1].astype(int))]
	snps = np.r_[snps, tmp_snps]
	snps[snps[:,2].astype(float)==0,2] = "1e-300"
	snps[:,2] = [float(-1*x) for x in np.log10(snps[:,2].astype(float))]

	##### take top 150000 SNPs per chromosome #####
	if len(snps) > 50000:
		snps = snps[snps[:,2].argsort()[::-1]]
		snps = snps[0:50000]
		snps = snps[snps[:,1].argsort()]

	maxlogP = int(max(snps[:,2]))+1
	minlogP = 0
	snps[:,0] = ["hs"+str(x) for x in snps[:,0]]
	snps = np.c_[snps[:,0:2], [x+1 for x in snps[:,1].astype(int)], snps[:,2:]]
	for l in snps:
		if float(l[4]) >= 0.8:
			l[4] = "id=1"
		elif float(l[4]) >= 0.6:
			l[4] = "id=2"
		elif float(l[4]) >= 0.4:
			l[4] = "id=3"
		elif float(l[4]) >= 0.2:
			l[4] = "id=4"
		else:
			l[4] = "id=5"
	with open(circos_config+"/base.conf", 'r') as fin:
		cfg = fin.read();
	if c==23:
		c="X"
		breaks = breaks.replace("hs23", "hsX")
	cfg = cfg.replace("[chr]", str(c))
	cfg = cfg.replace("[breaks]", breaks)
	cfg = cfg.replace("[maxlogP]", str(maxlogP))
	cfg = cfg.replace("[minlogP]", "0")

	if c=="X":
		c = 23

	with open(filedir+"circos/circos_chr"+str(c)+".conf", 'w') as o:
		o.write(cfg)

	regions = np.c_[[c]*len(regions), regions]
	return [snps, regions];

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit("ERROR: not enough arguments\nUSAGE ./createCircosConf.py <filedir>")

	##### get command line arguments #####
	filedir = sys.argv[1]

	##### add '/' to the filedir #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### get Parameters #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')
	circos_config = cfg.get('data', 'circos_config')
	circos_path = cfg.get('data', 'circos_path')
	param = ConfigParser.RawConfigParser()
	param.optionxform = str
	param.read(filedir+'params.config')
	ciMap = int(param.get('ciMap', 'ciMap'))
	eqtlMap = param.get('eqtlMap', 'eqtlMap')
	if ciMap!=1:
		sys.exit("ERROR: circos plot is only available when chromatin interaction mapping is performed.")

	##### prepare directory #####
	if not os.path.isdir(filedir+"circos"):
		os.makedirs(filedir+"circos")
	copyfile(circos_config+"/housekeeping.conf", filedir+"circos/housekeeping.conf")
	copyfile(circos_config+"/ideogram.conf", filedir+"circos/ideogram.conf")
	copyfile(circos_config+"/ticks.conf", filedir+"circos/ticks.conf")

	##### risk loci #####
	loci = pd.read_csv(filedir+"GenomicRiskLoci.txt", sep="\t", header=0)
	loci = np.array(loci)
	loci = loci[:,[0,2,3,4,6,7]] #loci,rsID,chr,pos,start,end

	##### snps #####
	snps = pd.read_csv(filedir+"snps.txt", sep="\t", header=0)
	snpshead = list(snps.columns.values)
	snps = np.array(snps)
	snps = snps[:,[2,3,7,snpshead.index("r2")]]
	snps = snps[np.where(np.isfinite(snps[:,2].astype(float)))]

	##### 3D genome  #####
	ci = pd.read_csv(filedir+"ci.txt", sep="\t", header=0)
	ci = np.array(ci)
	if len(ci)>0:
		ci = ci[ci[:,10]==1,]
		ci = ci[ci[:,0].argsort()]
		ci = ci[ci[:,7]=="intra"]
		chr1 = [int(x.split(":")[0]) for x in ci[:,1]]
		chr2 = [int(x.split(":")[0]) for x in ci[:,2]]
		pos1min = [int(x.split(":")[1].split("-")[0]) for x in ci[:,1]]
		pos1max = [int(x.split(":")[1].split("-")[1]) for x in ci[:,1]]
		pos2min = [int(x.split(":")[1].split("-")[0]) for x in ci[:,2]]
		pos2max = [int(x.split(":")[1].split("-")[1]) for x in ci[:,2]]
		ci = np.c_[ci[:,0], chr1, pos1min, pos1max, chr2, pos2min, pos2max]
		ci = np.array(ci, dtype=str)
		if len(ci)>1:
			ci = np.vstack({tuple(row) for row in ci})
		### take top 100000 links per chromosome
		ci_chrom = unique(ci[:,1])
		ci_tmp = []
		for c in ci_chrom:
			tmp = ci[ci[:,1]==c]
			if len(tmp)>10000:
				tmp = tmp[tmp[:,0].astype(float).argsort()]
				tmp = tmp[0:10000]
			if len(ci_tmp)==0:
				ci_tmp = tmp
			else:
				ci_tmp = np.r_[ci_tmp, tmp]
		ci = ci_tmp

	##### mapped genes #####
	genes = pd.read_csv(filedir+"genes.txt", sep="\t")
	geneshead = list(genes.columns.values)
	genes = np.array(genes)

	##### eqtl #####
	eqtl = []
	### eQTL
	# 0:ensg, 1: P-value, 2: chr, 3: pos
	# later exclude p-value column
	if os.path.isfile(filedir+"eqtl.txt"):
		eqtl = pd.read_csv(filedir+"eqtl.txt", sep="\t", header=0)
		eqtl = np.array(eqtl)
		if len(eqtl)>0:
			eqtl = eqtl[eqtl[:,13]==1]
			eqtl = eqtl[ArrayIn(eqtl[:,3], genes[:,0])]
		eqtl = eqtl[:,[3,5,10,11]]

	### take top 100000 links per chromosome (include all genes)
	if len(eqtl)>0:
		e_chrom = unique(eqtl[:,2])
		eqtl_tmp = []
		for c in e_chrom:
			tmp = eqtl[eqtl[:,2]==c]
			tmp = tmp[tmp[:,1].astype(float).argsort()][:,[0,2,3]]
			tmp = np.vstack({tuple(row) for row in tmp})
			if len(tmp)>10000:
				tmp = tmp[0:10000]
			if len(eqtl_tmp)==0:
				eqtl_tmp = tmp
			else:
				eqtl_tmp = np.r_[eqtl_tmp, tmp]
		eqtl = eqtl_tmp
	##### process per chromosome #####
	chrom = unique(loci[:,2])
	snpsout = []
	regions = []
	for c in chrom:
		if len(genes)>0:
			tmp_genes = genes[genes[:,2]==c]
			tmp_genes = tmp_genes[:,[geneshead.index("chr"),geneshead.index("start"),geneshead.index("end"),geneshead.index("symbol"),geneshead.index("GenomicLocus")]]
			tmp_genes[:,4] = [int(x.split(":")[-1]) for x in tmp_genes[:,4].astype(str)]
		else:
			tmp_genes = []
		if len(ci) >0 :
			tmp_ci = ci[np.where((ci[:,1]==str(c)) & (ci[:,4]==str(c)))]
		else:
			tmp_ci = []
		[tmp_snps, tmp_regions] = createConfig(c, filedir, circos_config, loci[loci[:,2].astype(int)==c], tmp_ci, snps[snps[:,0]==c], tmp_genes)
		if len(snpsout)==0:
			snpsout = tmp_snps
			regions = tmp_regions
		else:
			snpsout = np.r_[snpsout, tmp_snps]
			regions = np.r_[regions, tmp_regions]

	##### write SNPs #####
	snpsout[snpsout[:,0]=="hs23",0] = "hsX"
	with open(filedir+"circos/circos_snps.txt", 'w') as o:
		np.savetxt(o, snpsout, delimiter=" ", fmt="%s")

	##### write regions #####
	regions[:,0] = regions[:,0].astype(str)
	c = ["hs"+str(x) for x in regions[:,0].astype(int)]
	regions = np.c_[c, regions[:,[1,2]]]
	regions[regions[:,0]=="hs23", 0] = "hsX"
	with open(filedir+"circos/circos_regions.txt", "w") as o:
		np.savetxt(o, regions, delimiter=" ", fmt="%s")

	##### write 3d genome links #####
	if len(ci) > 0:
		ci[:,1] = ["hs"+str(x) for x in ci[:,1]]
		ci[:,4] = ["hs"+str(x) for x in ci[:,4]]
		ci[ci[:,1]=="hs23", 1] = "hsX"
		ci[ci[:,4]=="hs23", 4] = "hsX"
		ci = ci[:,1:7]
	with open(filedir+"circos/ci_links.txt", "w") as o:
		np.savetxt(o, ci, delimiter=" ", fmt="%s")

	##### eqtl write out #####
	if len(eqtl) >0 :
		c = ["hs"+str(x) for x in eqtl[:,1]]
		pos = eqtl[:,2].astype(int)
		gstart = list(map(lambda x: genes[genes[:,0]==x,3], eqtl[:,0]))
		gend = list(map(lambda x: genes[genes[:,0]==x,4], eqtl[:,0]))
		eqtl = np.c_[c, pos, [x+1 for x in pos], c, gstart, gend]
		eqtl[eqtl[:,0]=="hs23", 0] = "hsX"
		eqtl[eqtl[:,3]=="hs23", 3] = "hsX"
	with open(filedir+"circos/eqtl_links.txt", "w") as o:
		np.savetxt(o, eqtl, delimiter=" ", fmt="%s")

	### write genes
	if "eqtlMapSNPs" in geneshead:
		genes = genes[np.where((genes[:,geneshead.index("ciMap")]=="Yes") | (genes[:,geneshead.index("eqtlMapSNPs")]>0))]
	elif "ciMap" in geneshead:
		genes = genes[genes[:, geneshead.index("ciMap")]=="Yes"]
	gid = []
	if len(genes) > 0:
		gid = np.array(["id=0"]*len(genes))
		gid[np.where(genes[:, geneshead.index("ciMap")]=="Yes")] = "id=1"
		if "eqtlMapSNPs" in geneshead:
			gid[np.where((genes[:,geneshead.index("ciMap")]=="Yes") & (genes[:,geneshead.index("eqtlMapSNPs")]>0))] = "id=2"
		genes = np.c_[genes[:,[2,3,4,1]], gid]
		genes[:,0] = ["hs"+str(x) for x in genes[:,0]]
		genes[genes[:,0]=="hs23",0] = "hsX"
	with open(filedir+"circos/circos_genes.txt", "w") as o:
		np.savetxt(o, genes, delimiter=" ", fmt="%s")

	### write loci and rsID
	tmp = np.c_[["hs"+str(x) for x in loci[:,2]], loci[:,[4,5]]]
	tmp[tmp[:,0]=="hs23",0] = "hsX"
	with open(filedir+"circos/highlights.txt", "w") as o:
		np.savetxt(o, tmp, delimiter=" ", fmt="%s")
	tmp = np.c_[["hs"+str(x) for x in loci[:,2]], loci[:,3], [x+1 for x in loci[:,3]], loci[:,1]]
	tmp[tmp[:,0]=="hs23",0] = "hsX"
	with open(filedir+"circos/circos_rsID.txt", "w") as o:
		np.savetxt(o, tmp, delimiter=" ", fmt="%s")

	### execute circos ###
	for i in chrom:
		os.system(circos_path+"/circos -conf "+filedir+"circos/circos_chr"+str(i)+".conf -noparanoid -outputdir "+filedir+"circos -outputfile circos_chr"+str(i))

if __name__=="__main__": main()
