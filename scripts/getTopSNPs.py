#!/usr/bin/python
import sys
import os
import pandas as pd
import numpy as np
import math
import re

filedir = sys.argv[1]
if re.match(".+\/$", filedir) is None:
	filedir += '/'

snps = pd.read_csv(filedir+"input.snps", delim_whitespace=True)
pcol = 5

head = snps.columns.values

snps = snps.as_matrix()
snps = snps[np.argsort(snps[:,pcol])]
snps = snps[0:10,]

outfile = open(filedir+"topSNPs.txt", 'w')
outfile.write("\t".join(head)+"\n")
for l in snps:
    outfile.write("\t".join(l.astype(str))+"\n")
