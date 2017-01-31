#!/usr/bin/python

import os
import sys
import re
import pandas as pd
import numpy as np
import ConfigParser
import time

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results

if len(sys.argv)<1:
	raise Exception('ERROR: not enough arguments\nUSAGE ./geneDist.py <filedir>\n')

start = time.time()

filedir = sys.argv[1]
if re.match("\/$", filedir) is None:
	filedir += '/'

cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = ConfigParser.ConfigParser()
param.read(filedir+'params.config')

dist = int(param.get('posMap', 'posMapwindowSize'))*1000

snps = pd.read_table(filedir+"snps.txt", sep="\t")
snps = snps.as_matrix()

ENSG = pd.read_table(cfg.get('data', 'ENSG')+"/ENSG.all.genes.txt", header=None)
ENSG = np.array(ENSG)

geneDist = []

for ensg in ENSG:
    if ensg[3]=="X":
        ensg[3]=23
    temp = snps[snps[:,2].astype(int)==int(ensg[3])]
    temp = temp[temp[:,3].astype(int)>=int(ensg[4])-dist]
    temp = temp[temp[:,3].astype(int)<=int(ensg[5])+dist]
    if len(temp)==0 :
        continue
    for l in temp:
        geneDist.append([l[0], ensg[1]])

fout = open(filedir+"geneDist.txt", 'w')
fout.write("uniqID\tensg\n")
for l in geneDist:
    fout.write("\t".join(l)+"\n")

print time.time() - start
