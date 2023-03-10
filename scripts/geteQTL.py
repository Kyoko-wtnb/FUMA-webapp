#!/usr/bin/python

###########################################################
# eQTL file has to follow the following structure and tabixable
# chr	pos 	A1	A2	tested-allele	statistics	P	FDR/corrected P
#
# A1/A2 are in arbitrary order
# If alleles are provided but tested allele is not specified,
# teste-allele column is NA
# If alleles are not provided at all, only chr and pos are matched
###########################################################

import sys
import os
import pandas as pd
import numpy as np
import ConfigParser
import tabix
import re

##### eQTL tabix #####
def eqtl_tabix(region, tb):
	eqtls = []
	try:
		tmp = tb.querys(region)
	except:
		print "Tabix failed for region "+region
	else:
		for l in tmp:
			eqtls.append(l[0:9])
	return eqtls

##### check argument #####
if len(sys.argv) < 2:
	print "ERROR: not enough arguments\nUSAGE: ./geteQTL.py <filedir>"
	sys.exit()

##### add '/' to the filedir #####
filedir = sys.argv[1]
if re.match(".+\/$", filedir) is None:
	filedir += '/'

##### get config files #####
cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param_cfg = ConfigParser.ConfigParser()
param_cfg.read(filedir+'params.config')

##### get parameters #####
qtldir = cfg.get('data', 'QTL')
eqtlds = param_cfg.get('eqtlMap', 'eqtlMaptss').split(":")
sigonly = int(param_cfg.get('eqtlMap', 'eqtlMapSig'))
eqtlP = float(param_cfg.get('eqtlMap', 'eqtlMapP'))

##### files #####
fsnps = filedir+"snps.txt"
floci = filedir+"GenomicRiskLoci.txt"
fout = filedir+"eqtl.txt"

##### write header for output file #####
with open(fout, 'w+') as fo:
	fo.write("uniqID\tdb\ttissue\tgene\ttestedAllele\tp\tsigned_stats\tFDR\n")

##### Process per locus #####
loci = pd.read_csv(floci, sep="\t", usecols=[0,3,6,7], header=0)
snps = pd.read_csv(fsnps, sep="\t", usecols=[0,2,3], header=0)

for feqtl in eqtlds:
	reg = re.match(r'(.+)\/(.+).txt.gz', feqtl)
	db = reg.group(1)
	ts = reg.group(2)
	tb = tabix.open(qtldir+"/"+feqtl)
	for li in range(len(loci)):
		chrom = loci.iloc[li,1]
		start = loci.iloc[li,2]
		end = loci.iloc[li,3]
		eqtls = eqtl_tabix(str(chrom)+":"+str(start)+"-"+str(end), tb)
		eqtls = pd.DataFrame(eqtls, columns=['chr', 'pos', 'a1', 'a2', 'ta', 'gene', 'stats', 'p', 'fdr'])

		### filter on eQTLs based on position
		eqtls = eqtls[eqtls.iloc[:,1].astype('int').isin(snps[snps.iloc[:,1]==chrom].iloc[:,2])]
		if len(eqtls)==0: continue

		### filter by P/FDR
		#eqtls.iloc[:,6:] = eqtls.iloc[:,6:].apply(pd.to_numeric, errors='coerce', axis=1)
		if sigonly == 1:
			eqtls = eqtls[pd.to_numeric(eqtls.iloc[:,8], errors='coerce')<0.05]
		else:
			eqtls = eqtls[pd.to_numeric(eqtls.iloc[:,7], errors='coerce')<eqtlP]
		if len(eqtls)==0: continue

		### assign uniqID
		## if eQTLs do not have alleles, take uniqID from snps
		## For multi allelic SNPs, duplicated eQTLs (for later use in gene mapping)
		if eqtls.iloc[0,2]=="NA" and eqtls.iloc[0,3]=="NA":
			eqtls.iloc[:,1] = eqtls.iloc[:,1].astype('int')
			eqtls = eqtls.merge(snps[snps.iloc[:,1]==chrom].iloc[:,[0,2]], on="pos", how="left")
			eqtls = eqtls[eqtls.uniqID.isin(snps.uniqID)]
		elif eqtls.iloc[0,2]=="NA" or eqtls.iloc[0,3]=="NA":
			snps['uniqID1'] = snps.uniqID.apply(lambda x: ":".join(x.split(":")[0:3]))
			snps['uniqID2'] = snps.uniqID.apply(lambda x: ":".join(np.delete(x.split(":"),2)))
			if eqtls.iloc[0,2]=="NA":
				eqtls['uniqID1'] = eqtls.iloc[:,0].astype('str')+":"+eqtls.iloc[:,1].astype('str')+":"+eqtls.iloc[:,3]
			else:
				eqtls['uniqID1'] = eqtls.iloc[:,0].astype('str')+":"+eqtls.iloc[:,1].astype('str')+":"+eqtls.iloc[:,2]
			tmp_eqtls = eqtls[eqtls.uniqID1.isin(snps.uniqID1)]
			tmp_eqtls = tmp_eqtls.merge(snps[snps.uniqID1.isin(tmp_eqtls.uniqID1)].loc[:,["uniqID1", "uniqID"]], on="uniqID1", how="left")
			tmp_eqtls = tmp_eqtls.drop(columns="uniqID1")
			eqtls = eqtls[eqtls.uniqID1.isin(snps.uniqID2)]
			eqtls = eqtls.merge(snps[snps.uniqID2.isin(eqtls.uniqID1)].loc[:,["uniqID2", "uniqID"]], left_on="uniqID1", right_on="uniqID2", how="left")
			eqtls = eqtls.drop(columns=["uniqID1", "uniqID2"])
			eqtls = eqtls.append(tmp_eqtls, ignore_index=True)
			del tmp_eqtls
		else:
			eqtls.iloc[:,2:4] = eqtls.iloc[:,2:4].apply(np.sort, axis=1, result_type='broadcast')
			eqtls['uniqID'] = eqtls.iloc[:,0].astype('str')+":"+eqtls.iloc[:,1].astype('str')+":"+eqtls.iloc[:,2]+":"+eqtls.iloc[:,3]
			eqtls = eqtls[eqtls.uniqID.isin(snps.uniqID)]

		eqtls['db'] = db
		eqtls['tissue'] = ts
		eqtls = eqtls[["uniqID", "db", "tissue", "gene", "ta", "p", "stats", "fdr"]]
		eqtls.to_csv(fout, header=False, index=False, mode='a', na_rep="NA", sep="\t", float_format="%.5f")

os.system("Rscript "+os.path.dirname(os.path.realpath(__file__))+"/align_eqtl.R "+filedir)
