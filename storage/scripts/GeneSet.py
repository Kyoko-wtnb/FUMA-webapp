#!/usr/bin/python

import sys
import os;
import glob
import pandas as pd
import numpy as np
import scipy.stats as stats
import time
import statsmodels.sandbox.stats.multicomp as multicomp
import re
from joblib import Parallel, delayed
import multiprocessing
import ConfigParser

n_cores = multiprocessing.cpu_count()

start = time.time()

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	results = np.where(np.in1d(a1, a2))[0]
	return results

##### read gmt file #####
def read_gmt(file):
	genesets = []
	with open(file, 'r') as fin:
		for l in fin:
			genesets.append(l.strip().split("\t"))
	return genesets

##### hypergeometric test #####
def hypTest(l, c):
	g = l[2:]
	g = np.array(g)
	g = g[ArrayIn(g, bkgenes)]
	n = len(g)
	gin = genes[ArrayIn(genes, g)]
	x = len(gin)
	if x>0:
		p = stats.hypergeom.sf(x-1, N, n, m)
		gin = ENSG[ArrayIn(ENSG[:,ENSGheads.index("entrezID")], gin),ENSGheads.index("external_gene_name")]
		if len(l)>3:
			return([c, l[0], n, x, p, 1.0, ":".join(gin.astype(str)), l[1]])
		else:
			return([c, l[0], n, x, p, 1.0, ":".join(gin.astype(str)), ""])
	else:
		p=1
		if len(l)>3:
			return([c, l[0], n, x, p, 1.0, "", l[1]])
		else:
			return([c, l[0], n, x, p, 1.0, "", ""])

#### geneset test #####
def GeneSetTest(f):
	print f
	c = f.replace(".gmt", "").split("/")
	c = c[len(c)-1]
	gs = read_gmt(f)
	gs = np.array(gs)
	tmp = []
	for l in gs:
		if len(l) < 3:
			continue
		tmp.append(hypTest(l, c))
	tmp = np.array(tmp)
	padj = multicomp.multipletests(list(tmp[:,4].astype(float)), alpha=0.05, method=adjPmeth, is_sorted=False, returnsorted=False)
	tmp[:, 5] = padj[1]
	tmp = tmp[tmp[:,5].astype(float)<adjPcut]
	tmp = tmp[tmp[:,3].astype(int)>=minOverlap]
	tmp = tmp[tmp[:,4].astype(float).argsort()]
	return tmp;


##### config variables #####
cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')
ensgdir = cfg.get('data', 'ENSG')
ensgfile = cfg.get('data', 'ENSGfile')
gsdir = cfg.get('data', 'GeneSet')

if len(sys.argv)<1:
	raise Exception('ERROR: not enough arguments\nUSAGE ./GeneSet.py <filedir>\n')

filedir = sys.argv[1]
if re.match(".+\/$", filedir) is None:
	filedir += '/'
param = ConfigParser.ConfigParser()
param.read(filedir+'params.config')

gtype = param.get('params', 'gtype')
gval = param.get('params', 'gval')
bkgtype = param.get('params', 'bkgtype')
bkgval = param.get('params', 'bkgval')
ensg_v = param.get('params', 'ensembl')
nFiles = int(param.get('params', 'gsFileN'))
gsFiles = param.get('params', 'gsFiles')
MHC = int(param.get('params', 'MHC')) #1 for exclude
adjPmeth = param.get('params', 'adjPmeth')
adjPcut = float(param.get('params', 'adjPcut'))
minOverlap = int(param.get('params', 'minOverlap'))

if gtype == "text":
	genes = gval.split(":")
else:
	lines = pd.read_csv(filedir+gval, header=None, delim_whitespace=True, dtype=str)
	lines = np.array(lines)
	genes = list(lines[:,0].astype(str))
genes = [s.upper() for s in genes]

ENSG = pd.read_csv(ensgdir+"/"+ensg_v+"/"+ensgfile, sep="\t", dtype=str)
ENSGheads = list(ENSG.columns.values)
ENSG = np.array(ENSG)
ENSG = ENSG[ENSG[:,ENSGheads.index("entrezID")]!="NA"]

if bkgtype == "select":
	bkgval = list(bkgval.split(":"))
	if "all" not in bkgval:
		ENSG = ENSG[ArrayIn(ENSG[:,ENSGheads.index("gene_biotype")], bkgval),]
	bkgenes = list(ENSG[:,ENSGheads.index("entrezID")])
elif bkgtype == "text":
	bkgenes = bkgval.split(":")
	bkgenes = [s.upper() for s in bkgenes]
else:
	lines = pd.read_csv(filedir+bkgval, header=None, delim_whitespace=True, dtype=str)
	lines = np.array(lines)[:,0]
	bkgenes = list([str(s) for s in lines])
	bkgenes = [s.upper() for s in bkgenes]

if MHC == 1:
	MHC = False
else:
	MHC = True

### remove MHC region
if not MHC:
	print "Excluding genes in MHC regions"
	mhc_start = int(ENSG[ENSG[:,ENSGheads.index("external_gene_name")]=="MOG",ENSGheads.index("start_position")][0])
	mhc_end = int(ENSG[ENSG[:,ENSGheads.index("external_gene_name")]=="COL11A2",ENSGheads.index("start_position")][0])
	ENSG = ENSG[np.where((ENSG[:,ENSGheads.index("chromosome_name")]!="6")|(ENSG[:,ENSGheads.index("end_position")].astype(int)<=mhc_start)|(ENSG[:,ENSGheads.index("start_position")].astype(int)>=mhc_end))]

## genes ID
Type = 0
if len(ArrayIn(genes, ENSG[:,ENSGheads.index("external_gene_name")]))>0:
	Type = 0
	genes = list(ENSG[ArrayIn(ENSG[:,ENSGheads.index("external_gene_name")], genes),ENSGheads.index("entrezID")])
elif len(ArrayIn(genes, ENSG[:,ENSGheads.index("ensembl_gene_id")]))>0:
	Type = 1
	genes = list(ENSG[ArrayIn(ENSG[:,ENSGheads.index("ensembl_gene_id")], genes),ENSGheads.index("entrezID")])
elif len(ArrayIn(genes, ENSG[:,ENSGheads.index("entrezID")]))>0:
	Type = 2
	genes = list(ENSG[ArrayIn(ENSG[:,ENSGheads.index("entrezID")], genes),ENSGheads.index("entrezID")])
genes = np.array(genes)
genes = np.unique(genes)

## bkgenes ID
if bkgtype != "select":
	if len(ArrayIn(bkgenes, ENSG[:,ENSGheads.index("external_gene_name")]))>0 :
		bkgenes = list(ENSG[ArrayIn(ENSG[:,ENSGheads.index("external_gene_name")], bkgenes),ENSGheads.index("entrezID")])
	elif len(ArrayIn(bkgenes, ENSG[:,ENSGheads.index("ensembl_gene_id")]))>0 :
		bkgenes = list(ENSG[ArrayIn(ENSG[:,ENSGheads.index("ensembl_gene_id")], bkgenes),ENSGheads.index("entrezID")])
	elif len(ArrayIn(bkgenes, ENSG[:,ENSGheads.index("entrezID")]))>0:
		bkgenes = list(ENSG[ArrayIn(ENSG[:,ENSGheads.index("entrezID")], bkgenes),ENSGheads.index("entrezID")])
bkgenes = np.array(bkgenes)
bkgenes = np.unique(bkgenes)
bkgenes = bkgenes[ArrayIn(bkgenes, ENSG[:,ENSGheads.index("entrezID")])]
genes = genes[ArrayIn(genes, bkgenes)]

if len(genes)==0:
	sys.exit("No input genes matched with DB")
if len(genes)==1:
	sys.exit("Only one gene remained")

ENSG = ENSG[ArrayIn(ENSG[:,ENSGheads.index("entrezID")], genes)]

files = glob.glob(gsdir+'/*.gmt')
if nFiles>0:
	files += [filedir+x for x in gsFiles.split(":")]
print files

N = len(bkgenes)
m = len(genes)

gs = Parallel(n_jobs=n_cores)(delayed(GeneSetTest)(f) for f in files)

with open(filedir+"GS.txt", 'w') as out:
	out.write("\t".join(["Category", "GeneSet", "N_genes", "N_overlap", "p", "adjP", "genes", "link"])+"\n")
with open(filedir+"GS.txt", 'a') as out:
	for gs_tmp in gs:
		if len(gs_tmp)>0:
			out.write("\n".join(["\t".join(l) for l in gs_tmp])+"\n")

print time.time() - start
