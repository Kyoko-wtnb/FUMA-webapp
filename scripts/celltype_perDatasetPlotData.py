#!/usr/bin/python
import sys
import os
import re
import numpy as np
import pandas as pd
import ConfigParser
import json

def main():
	##### check argument #####
	if len(sys.argv)<3:
		sys.exit("ERROR: not enough arguments\nUSAGE ./celltype_perDatasetPlotData.py <filedir> <ds>")

	##### get command line arguments #####
	filedir = sys.argv[1]
	ds = sys.argv[2];

	##### add '/' to the filedir #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### get Parameters #####
	param = ConfigParser.RawConfigParser()
	param.optionxform = str
	param.read(filedir+'params.config')
	suffix = ".gsa.out"
	if param.get('version', 'MAGMA')=="v1.06":
		suffix = ".gcov.out"

	##### output variables
	out_data = []

	##### process per file
	for chunk in pd.read_csv(filedir+"magma_celltype_step1.txt", header=0, sep="\t", chunksize=5000):
		chunk = np.array(chunk)
		if len(out_data)>0:
			out_data = np.r_[out_data, chunk[np.where(chunk[:,0]==ds)][:,[1,6,7,8]]]
		else:
			out_data = chunk[np.where(chunk[:,0]==ds)][:,[1,6,7,8]]

	out_data = out_data[np.argsort(out_data[:,0])]
	out_data = np.c_[out_data, range(0,len(out_data))]
	out_data = out_data[np.argsort(out_data[:,1])]
	out_data = np.c_[out_data, range(0, len(out_data))]

	print json.dumps([list(l) for l in out_data])

if __name__ == "__main__": main()
