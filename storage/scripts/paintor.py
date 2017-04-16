#!/usr/bin/python
import pandas as pd
import numpy as np
import sys
import os
import re
import scipy.stats as st
import ConfigParser
import time

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
refgenome = cfg.get("data", "refgenome")
paintor = cfg.get("paintor", "paintor")
paintor_annot = cfg.get("paintor", "annot")
zcol = param.get("inputfiles", "zcol")
becol = param.get("inputfiles", "becol")
orcol = param.get("inputfiles", "orcol")
pop = param.get("params", "pop")

##### parameter check #####
if zcol=="NA" and becol=="NA" and orcol=="NA":
	sys.exit("Neither Z score nor signed effect size is not given")
zscore = False
direction = False
if zcol != "NA":
	zscore = True

##### paintor directory #####
if not os.path.isdir(filedir+"PAINTOR"):
	os.mkdir(filedir+"PAINTOR")

##### read snps file #####
snpsfile = filedir+"snps.txt"
snps = pd.read_table(snpsfile, sep="\t")
snpshead = list(snps.columns.values)
snps = snps.as_matrix()
snps = snps[np.invert(np.isnan(snps[:,7].astype(float)))]

snps_headi = [snpshead.index("GenomicLocus"), snpshead.index("chr"), snpshead.index("pos"), snpshead.index("rsID"), snpshead.index("ref"), snpshead.index("alt")]
if zscore:
	snps_headi.append(snpshead.index("z"))
else:
	if "or" in snpshead:
		snps_headi.append(snpshead.index("or"))
		snps_headi.append(snpshead.index("gwasP"))
		direction = "or"
	elif "beta" in snpshead:
		snps_headi.append(snpshead.index("beta"))
		snps_headi.append(snpshead.index("gwasP"))
		direction = "beta"

snps = snps[:,snps_headi]

if not zscore:
	z = [abs(st.norm.ppf(x/2)) for x in snps[:,7]]
	if min(snps[:,6]) < 0:
		d = [1 if x>0 else -1 for x in snps[:,6]]
	else:
		d = [1 if x>1 else -1 for x in snps[:,6]]
	z = [a*b for a,b in zip(z,d)]
	snps = np.c_[snps[:,range(0,6)],z]

loci = np.unique(snps[:,0])
chrom = []
for i in loci:
	chrom.append(snps[snps[:,0]==i,1][0])
	fout = filedir+"PAINTOR/Locus"+str(i)
	with open(fout, 'w') as o:
		o.write("\t".join(["chr", "pos", "rsid", "ref", "alt", "Zscore"])+"\n")
	with open(fout, 'a') as o:
		np.savetxt(o, snps[snps[:,0]==i,1:7], delimiter="\t", fmt='%s')

##### prepare ld file #####
#for i in range(0,len(loci)):
#	reffile = "ALL.chr"+str(chrm[i])+".phaes3_shapeit2_mvncall_integrated_v5a.20130502.genotypes.vcf.gz"
#	panelfile = "integrated_all_samples_v3.20130502.ALL.panel"
#	os.system("python "+paintor+"/PAINTOR_Utilities/CalcLD_1KG_VCF.py --locus "+filedir+"PAINTOR/Locus"+str(loci[i])+" --reference "+refgenome+"/"+reffile+" --map "+refgenome+"/"+panelfile+"--effec_allele alt --alt_allele ref --population "+pop+" --Zhead Zscore --out_name "+filedir+"PAINTOR/Locis"+str(loci[i])+".ld --position pos")

print time.time() - start
