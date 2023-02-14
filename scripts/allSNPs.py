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

	for snps in pd.read_csv(filedir+"input.snps", dtype=str, sep="\t", usecols = ['chr', 'bp', 'p'], chunksize=50000):
		snps = snps.dropna()
		snps.loc[:,'chr'] = snps.chr.astype(int)
		snps = snps[(snps.bp.apply(lambda x: x.isdigit())) & (snps.p.apply(is_float))]
		snps.to_csv(filedir+'all.txt', header=False, index=False, sep="\t", mode="a")

	os.system("bgzip "+filedir+"all.txt")
	os.system("tabix -p vcf "+filedir+"all.txt.gz")

if __name__ == "__main__": main()
