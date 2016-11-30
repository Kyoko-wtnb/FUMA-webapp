#!/usr/bin/python

import sys
import glob
import pandas as pd
import numpy as np
import scipy.stats as stats
import timeit
import statsmodels.sandbox.stats.multicomp as multicomp
import re

start = timeit.default_timer()

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
#	if type(a1) is np.ndarray:
#		a1 = a1.tolist()
#	if type(a2) is np.ndarray:
#		a2 = a2.tolist()
	#if len(a1)<=len(a2):
	results = [i for i, x in enumerate(a1) if x in a2]
	#else:
	#	results = []
	#	for i in a2:
	#		results = results+list(np.where(a1==i)[0])
	return results
	

if len(sys.argv)<7:
	raise Exception('ERROR: not enough arguments\nUSAGE ./gene2func.py <filedir> <gtype> <gval> <bkgtype> <bkgval> <X chrom> <MHC> <adjPmeth> <adjPcutoff> <minOverlap> <testCategory>\n')

filedir = sys.argv[1]
gtype = sys.argv[2]
gval = sys.argv[3]
bkgtype = sys.argv[4]
bkgval = sys.argv[5]
Xchr = int(sys.argv[6]) #1 for exclude
MHC = int(sys.argv[7]) #1 for exclude
adjPmeth = sys.argv[8]
adjPcut = float(sys.argv[9])
minOverlap = int(sys.argv[10])
#testCategory = sys.argv[11]

if gtype == "text":
	genes = gval.split(":")
else:
	lines = pd.read_table(filedir+gval, header=None, sep="\s+")
	lines = np.array(lines)
	genes = list(lines[:,0])
#Ning = len(genes)
#print "Input genes: "+str(Ning)

#ENSG = pd.read_table("ENSG.all.genes.txt", header=None, sep="\t")
ENSG = pd.read_table("/media/sf_Documents/VU/Data/ENSG.all.genes.txt", header=None, sep="\t") #local
#webserver ENSG = pd.read_table("/data/ENSG/ENSG.all.genes.txt", header=None, sep="\t")
ENSG = np.array(ENSG)

if bkgtype == "select":
	bkgval = bkgval.split(":")
	ENSG = ENSG[ArrayIn(ENSG[:,8], bkgval),]
	bkgenes = list(ENSG[:,9])
elif bkgtype == "text":
	bkgenes = bkgval.split(":")
else:
	lines = pd.read_table(filedir+bkgval, sep="\s+")
	lines = np.array(lines)
	bkgenes = lsit(lines[:,0])
#Ninbkg = len(bkgenes)
#print "Input bkg: "+str(Ninbkg)
	
if Xchr == 1:
	ENSG = ENSG[ENSG[:,3]!=23,]
if MHC == 1:
	MHC = False
else:
	MHC = True

## genes ID
Type = 0
if len(ArrayIn(genes, ENSG[:,2]))>0:
	Type = 0
	genes = list(ENSG[ArrayIn(ENSG[:,2], genes),9])
elif len(ArrayIn(genes, ENSG[:,1]))>0:
	Type = 1
	genes = list(ENSG[ArrayIn(ENSG[:,1], genes),9])
elif len(ArrayIn(genes, ENSG[:,9]))>0:
	Type = 2
	genes = list(ENSG[ArrayIn(ENSG[:,9], genes),9])
genes = np.array(genes)
genes = np.unique(genes)
#print "type: "+str(Type)
#Ng = len(genes)

## bkgenes ID
if bkgtype != "select":
	if len(ArrayIn(bkgenes, ENSG[:,2]))>0 :
		bkgenes = list(ENSG[ArrayIn(ENSG[:,2], bkgenes),9])
	elif len(ArrayIn(bkgenes, ENSG[:,1]))>0 :
		bkgenes = list(ENSG[ArrayIn(ENSG[:,1], bkgenes),9])
	elif len(ArrayIn(bkgenes, ENSG[:,9]))>0:
		bkgenes = list(ENSG[ArrayIn(ENSG[:,9], bkgenes),9])
bkgenes = np.array(bkgenes)
bkgenes = np.unique(bkgenes)
#Nbkg = len(bkgenes)
#print "Total gene: "+str(Ng)
#print "Total bkgene: "+str(Nbkg)

genes = genes[ArrayIn(genes, bkgenes)]
#entrez2symbol = ENSG[ArrayIn(ENSG[:,9], genes)][:,[2,9]]

#def GeneSetTest(genes, bkgenes, adjPmethod, adjPcutoff, MHC, minOverlap):

files = glob.glob('/media/sf_Documents/VU/Data/GeneSet/*.txt') #local
#webserver files = glob.glob('/data/GeneSet/*.txt')

#if(testCategory!="all"):
#	testCategory = testCategory.split(":")
#	testCategory = np.array(testCategory)
#	files = files[InArray(files, testCategory)]

#genes = np.array(genes)
#bkgenes = np.array(bkgenes)
results = []
results.append(["Category", "GeneSet", "N_genes", "N_overlap", "p", "FDR", "genes", "logP", "logFDR"])
N = len(bkgenes)
m = len(genes)
#print "N: "+str(N)
#print "m: "+str(m)
#print bkgenes[0]
#print "genes: "+" ".join(genes)
out = open(filedir+"GS.txt", 'w')
out.write("\t".join(["Category", "GeneSet", "N_genes", "N_overlap", "p", "FDR", "genes", "logP", "logFDR"])+"\n")
for f in files:
	if "Human_Adult_Brain" in f:
		continue
	print f
	c = re.match(".*GeneSet/(\w+)\.txt", f)
	gs = pd.read_table(f)
	gs = np.array(gs)
	#print gs[0:3]
	
	tmp = []
	for l in gs:
		g = l[2].split(":")
		g = np.array(g).astype(int)
		#print len(g)
		g = g[ArrayIn(g, bkgenes)]
		#g = g[[0,2,4,6]]
		#print len(g)
		n = len(g)
		#print ArrayIn(g, bkgenes)
		gin = genes[ArrayIn(genes, g)]
		x = len(gin)
		#print l[0]+": "+str(n)+": "+str(x)
		if x>0:
			p = stats.hypergeom.sf(x, N ,n, m)
			tmp.append([c.group(1), l[0], n, x, p, 1.0, ":".join(gin.astype(str)), 0.0, 0.0])
		else:
			p=1
			tmp.append([c.group(1), l[0], n, x, p, 1.0, "", 0.0, 0.0])
		#if(x>0):
		#	tmp.append([c.group(1), l[0], n, x, p, 1.0, ":".join(gin.astype(str))])
		#else:
		#	tmp.append([c.group(1), l[0], n, x, p, 1.0, ""])
			#if(p<1e-5):
			#	print l[0]+" x:"+str(x)+" n:"+str(n)+" p:"+str(p)
		
	tmp = np.array(tmp)
	padj = multicomp.multipletests(list(tmp[:,4].astype(float)), alpha=0.05, method=adjPmeth, is_sorted=False, returnsorted=False)
	tmp[:, 5] = padj[1]
	tmp = tmp[tmp[:,5].astype(float)<adjPcut]
	tmp = tmp[tmp[:,3].astype(int)>=minOverlap]
	tmp = tmp[tmp[:,4].astype(float).argsort()]
	tmp[:,7] = -np.log10(tmp[:,4].astype(float))
	tmp[:,8] = -np.log10(tmp[:,5].astype(float))
	for i in tmp:
		g = i[6].split(":")
		g = [int(x) for x in g]
		g = ":".join(list(ENSG[ArrayIn(ENSG[:,9], g),2]))
		#g = ":".join(list(entrez2symbol[ArrayIn(entrez2symbol[:,1], g),0]))
		i[6]=g
		out.write("\t".join(i)+"\n")
		#results.append(list(i))	

#GeneSetTest(genes, bkgenes, "BH", 0.05, True, 2)

#out = open(filedir+"GS.txt", 'w')
#for l in results:
#	out.write("\t".join(list(l))+"\n")

stop = timeit.default_timer()

print stop - start 
