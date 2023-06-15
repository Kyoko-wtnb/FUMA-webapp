#!/usr/bin/python
import sys
import os
import re
import numpy as np
import pandas as pd
import ConfigParser
import json

def ArrayIn(a1, a2):
	return np.where(np.in1d(a1, a2))[0]

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit("ERROR: not enough arguments\nUSAGE ./celltype_stepPlotData.py <filedir>")

	##### get command line arguments #####
	filedir = sys.argv[1]

	##### add '/' to the filedir #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	if os.path.exists(filedir+"step1_2_summary.txt"):
		data1 = pd.read_csv(filedir+"step1_2_summary.txt", header=0, sep="\t", usecols=["Dataset", "Cell_type", "P", "step3"])
		data1 = np.array(data1)
	else:
		data1 = pd.read_csv(filedir+"magma_celltype_step1.txt", header=0, sep="\t", usecols=["Dataset", "Cell_type", "P", "P.adj"])
		data1 = np.array(data1)
		data1 = data1[data1[:,3]<0.05,0:3]
		data1 = np.c_[data1, [0]*len(data1)]

	## duplicated cell types
	check = []
	for i in range(len(data1)):
		if data1[i,1] in check:
			data1[i:,1] = [" "+x if x==data1[i,1] else x for x in data1[i:,1]]
		check.append(data1[i,1])

	## order
	data1 = data1[np.argsort(data1[:,2])]
	data1 = np.c_[data1, range(len(data1))]
	data1 = data1[np.lexsort((data1[:,2], data1[:,0]))]
	data1 = np.c_[data1, range(len(data1))]

	data2 = data1[np.where(data1[:,3]==1)]
	data2 = data2[np.argsort(data2[:,2])]
	data2[:,4] = range(len(data2))
	data2 = data2[np.lexsort((data2[:,2], data2[:,0]))]
	data2[:,5] = range(len(data2))
	data2[:,1] = [re.sub(r'^ +', '', x) for x in data2[:,1]]
	## duplicated cell types
	check = []
	for i in range(len(data2)):
		if data2[i,1] in check:
			data2[i:,1] = [" "+x if x==data2[i,1] else x for x in data2[i:,1]]
		check.append(data2[i,1])

	data3 = []
	if os.path.exists(filedir+"magma_celltype_step3.txt"):
		data3 = pd.read_csv(filedir+"magma_celltype_step3.txt", header=0, sep="\t", usecols=["Dataset", "Cell_type", "PS"])
		data3["PS"] = data3["PS"].fillna(-1)
		data3 = np.array(data3)
		data2_label = np.c_[data2[:,1], [":".join([l[0], l[1].replace(" ", "")]) for l in data2[:,0:2]]]
		tmp_label = [":".join(l) for l in data3[:,0:2]]
		data3[:,1] = data2_label[[list(data2_label[:,1]).index(x) for x in tmp_label],0]
		x = ["NA"]*len(data3)
		x[::2] = data3[1::2,1]
		x[1::2] = data3[::2,1]
		data3 = np.c_[data3[:,0], x, data3[:,1:]]

	print json.dumps({"step1":[list(l) for l in data1], "step2": [list(l) for l in data2], "step3":[list(l) for l in data3]})

if __name__ == "__main__": main()
