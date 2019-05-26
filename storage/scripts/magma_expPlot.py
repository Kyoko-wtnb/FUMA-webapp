#!/usr/bin/python
import sys
import os
import re
import pandas as pd
import numpy as np
import math
import json
import glob
import ConfigParser

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit("ERROR: not enough arguments\nUSAGE ./magma_expPlot.py <filedir>")

	##### get command line arguments #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### check MAGMA version #####
	param_cfg = ConfigParser.ConfigParser()
	param_cfg.read(filedir+'params.config')

	### get files ###
	files = glob.glob(filedir+"/magma_exp*.gsa.out")
	suffix = "gsa"
	if len(files)==0:
		files = glob.glob(filedir+"/magma_exp*.gcov.out")
		suffix = "gcov"

	out = []
	for f in files:
		if suffix=="gsa":
			header = pd.read_csv(f, delim_whitespace=True, comment="#", dtype=str, header=0, nrows=1)
			header = list(header.columns.values)
			if "FULL_NAME" in header:
				header = ["P", "FULL_NAME"]
			else:
				header = ["VARIABLE", "P"]
		else:
			header = ["COVAR", "P"]

		dat = pd.read_csv(f, delim_whitespace=True, comment="#", dtype=str, usecols=header)
		dat = np.array(dat)
		if "FULL_NAME" in header:
			dat = dat[:,::-1]
		dat = dat[dat[:,1].astype(float).argsort()]
		dat = np.c_[dat, range(0,len(dat))]
		dat = dat[dat[:,0].argsort()]
		dat = np.c_[dat, range(0,len(dat))]
		c = re.match(r'.*magma_exp_(.*)\.'+suffix+'.out', f)
		for l in dat:
			out.append([c.group(1), l[0], float(l[1]), l[2], l[3]])

	print json.dumps(out)
if __name__ == "__main__": main()
