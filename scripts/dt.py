#!/usr/bin/python

### arguments
# 1. filedir
# 2. file name
# 3. headers (":" separated)
# 4. sort col
# 5. sort col dir
# 6. start
# 7. length
###

import os
import sys
import re
import pandas as pd
import numpy as np

if len(sys.argv) < 8:
	sys.exit("ERROR: not enought arguments\nUSAGE: ./dt.py <filedir> <file> <draw> <header> <sort column> <asc/desc> <start> <length> [<search>]")

filedir = sys.argv[1]
f = sys.argv[2]
draw = sys.argv[3]
cols = sys.argv[4]
sort_col = int(sys.argv[5])
sort_dir = sys.argv[6]
start = int(sys.argv[7])
length = int(sys.argv[8])
if len(sys.argv)>=10:
	search = sys.argv[9]
else:
	search = None

if re.match(".+\/$", filedir) is None:
	filedir += "/"

cols = cols.split(":")

fin = pd.read_csv(filedir+f, sep="\t")
header = list(fin.columns.values)
fin = np.array(fin)

if len(fin)==0:
	print '{"data":[]}'
	sys.exit()

hind = []
for i in range(0, len(cols)):
	hind.append(header.index(cols[i]))

fin = fin[:,hind]
total = len(fin)

if search:
	n = []
	for i in range(0, len(hind)):
		tmp = [j for j, item in enumerate(fin[:,i].astype(str)) if re.search(search, item, re.IGNORECASE)]
		n += tmp
	n = np.unique(n)
	if len(n)==0:
		fin = []
	else:
		fin = fin[n]

filt = len(fin)

if filt>0:
	if sort_dir =="asc":
		fin = fin[fin[:, sort_col].argsort()]
	else:
		fin = fin[fin[:, sort_col].argsort()[::-1]]
	fin = fin[start:start+length-1]

out = '{"draw":'+draw+',"recordsTotal":'+str(total)+',"recordsFiltered":'+str(filt)+',"data":['

for l in fin:
	out += "["
	out += ','.join('"{0}"'.format(w) for w in l.astype(str))
	out += "],"
out = re.sub(r'],$', ']', out)
out += ']}'
print out
