#!/usr/bin/python
import sys
import os
import re
import pandas as pd
import numpy as np
import math
import json
import glob

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

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit("ERROR: not enough arguments\nUSAGE ./magma_expPlot.py <filedir>")

	##### get command line arguments #####
	filedir = sys.argv[1]

	### get files ###
	files = glob.glob(filedir+"/magma_exp*.out")

	out = []
	for f in files:
		dat = pd.read_table(f, delim_whitespace=True, comment="#", dtype=str, usecols=["COVAR", "P"])
		dat = np.array(dat)
		dat = dat[dat[:,1].astype(float).argsort()]
		dat = np.c_[dat, range(0,len(dat))]
		dat = dat[dat[:,0].argsort()]
		dat = np.c_[dat, range(0,len(dat))]
		c = re.match(r'.*magma_exp_(.*)\.gcov.out', f)
		for l in dat:
			out.append([c.group(1), l[0], float(l[1]), l[2], l[3]])

	print json.dumps(out)
if __name__ == "__main__": main()
