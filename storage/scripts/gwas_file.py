#!/usr/bin/python

import sys
import os
import re
import pandas as pd
import numpy as np
import ConfigParser

cfg = ConfigParser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

if len(sys.argv)<1:
	raise Exception('ERROR: not enough arguments\nUSAGE ./gwas_file.py <filedir>\n')

filedir = sys.argv[1]
if re.match("\/$", filedir) is None:
	filedir += '/'
param = ConfigParser.ConfigParser()
param.read(filedir+'params.config')

gwas = filedir+cfg.get('inputfiles', 'gwas')
outSNPs = filedir+"input.snps"

dbSNPfile = cfg.get('data', 'dbSNP')

GWASin = open(gwas, 'r')
head = GWASin.readline();
while(re.match("^#", head)):
    head = GWASin.readline()
GWASin.close()

chrcol = param.get('inputfiles', 'chrcol')
poscol = param.get('inputfiles', 'poscol')
rsIDcol = param.get('inputfiles', 'rsIDcol')
pcol = param.get('inputfiles', 'pcol')
refcol = param.get('inputfiles', 'refcol')
altcol = param.get('inputfiles', 'altcol')
orcol = param.get('inputfiles', 'orcol')
becol = param.get('inputfiles', 'becol')
secol = param.get('inputfiles', 'secol')
Ncol = param.get('params', 'Ncol')

head = head.strip()
head = head.split()

print head;

for i in range(0,len(head)):
    if head[i].upper==chrcol or re.match("^CHR$|^chromosome$|^chrom$",head[i], re.IGNORECASE):
        chrcol = int(i)
    elif head[i].upper==poscol or re.match("^BP$|^pos$|^position$",head[i], re.IGNORECASE):
        poscol = int(i)
    elif head[i].upper==rsIDcol or re.match("^SNP$|^MarkerName$|^rsID$|^snpid$",head[i], re.IGNORECASE):
        rsIDcol = int(i)
    elif head[i].upper==refcol or re.match("^SNP$|^MarkerName$|^rsID$|^snpid$",head[i], re.IGNORECASE):
        refcol = int(i)
    elif head[i].upper==altcol or re.match("^SNP$|^MarkerName$|^rsID$|^snpid$",head[i], re.IGNORECASE):
        altcol = int(i)

print chrcol;
