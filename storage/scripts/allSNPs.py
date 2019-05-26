#!/usr/bin/python
import pandas as pd
import numpy as np
import sys
import os
import re

def is_float(s):
	try:
		return float(s)
	except ValueError:
		return False

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./allSNPs.py <filedir>')

	##### add '/' to the filedir #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	snps = pd.read_csv(filedir+"input.snps", dtype=str, sep="\t")
	snps = snps.dropna()
	snps = np.array(snps)

	snps = snps[:,[0,1,5]]
	out = []
	for l in snps:
		if l[0].isdigit() and l[1].isdigit() and is_float(l[2]):
			l[0] = str(int(l[0])) #in case there is 0 in fromt of chromosome index
			out.append(l)
	out = np.array(out)
	out = out[np.lexsort((out[:,1].astype(int), out[:,0].astype(int)))]
	with open(filedir+"all.txt", 'w') as o:
		np.savetxt(o, out, fmt="%s", delimiter="\t")

	os.system("bgzip "+filedir+"all.txt")
	os.system("tabix -p vcf "+filedir+"all.txt.gz")

if __name__ == "__main__": main()
