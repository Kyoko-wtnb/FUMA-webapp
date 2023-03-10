#!/usr/bin/python
import sys
import os
import re
import numpy as np
import ConfigParser
import json

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit("ERROR: not enough arguments\nUSAGE ./celltypePlotData.py <filedir>")

	##### get command line arguments #####
	filedir = sys.argv[1]

	##### add '/' to the filedir #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### get Parameters #####
	param = ConfigParser.RawConfigParser()
	param.optionxform = str
	param.read(filedir+'params.config')
	prefix = param.get('params', 'datasets').split(":")
	suffix = ".gsa.out"
	if param.get('version', 'MAGMA')=="v1.06":
		suffix = ".gcov.out"

	##### output variables
	long = {}
	middle = {}
	short = {}

	##### process per file
	for pre in prefix:
		tmp = []
		with open(filedir+"magma_celltype_"+pre+suffix, 'r') as fin:
			line = fin.readline()
			while re.match(r'^#', line): line = fin.readline()
			for l in fin:
				l = l.strip().split()
				tmp.append([l[0], float(l[5])])
		tmp = np.array(tmp)
		tmp = np.c_[tmp, range(len(tmp))]
		tmp = tmp[tmp[:,1].astype(float).argsort()]
		tmp = np.c_[tmp, range(len(tmp))]
		tmp = [list(x) for x in tmp]

		if len(tmp)>25:
			long.update({len(long):{'name':pre, 'n':len(tmp), 'data':tmp}})
		else:
			short.update({len(short):{'name':pre, 'n':len(tmp), 'data':tmp}})

	print json.dumps({"long":long, "short":short})

if __name__ == "__main__": main()
