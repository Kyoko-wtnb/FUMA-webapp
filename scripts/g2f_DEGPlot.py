#!/usr/bin/python
import sys
import re
import pandas as pd
import numpy as np
import json
import glob

##### return unique element in list #####
def unique(a):
	unique = []
	[unique.append(s) for s in a if s not in unique]
	return unique

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit("ERROR: not enough arguments\nUSAGE ./g2f_DEGPlot.py <filedir>")

	##### get command line arguments #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	### get files ###
	files = glob.glob(filedir+"/*_DEG.txt")

	out = []
	for f in files:
		dat = pd.read_csv(f, sep="\t", comment="#", dtype=str, usecols=["Category", "GeneSet", "p"])
		dat = np.array(dat)
		c = re.match(r'.*\/(.*)_DEG.txt', f)
		order_p = []
		tmp_out = []
		for t in ["DEG.up", "DEG.down", "DEG.twoside"]:
			tmp = dat[dat[:,0]==t]
			label = list(tmp[:,2].astype(float).argsort())
			order_p.append([list(label).index(x) for x in range(0,len(tmp))])
			tmp = np.c_[tmp, range(0,len(tmp))]
			if len(tmp_out)==0:
				tmp_out = tmp
			else:
				tmp_out = np.r_[tmp_out, tmp]

		tmp_out = np.c_[tmp_out, order_p[0]*3, order_p[1]*3, order_p[2]*3]
		for l in tmp_out:
			out.append([c.group(1), l[0], l[1], float(l[2]), l[3], l[4], l[5], l[6]])

	print json.dumps(out)

if __name__ == "__main__": main()
