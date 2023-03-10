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

width = 300 #px
height = 300 #px

obsP = []

f = open(filedir+"input.snps", 'r')
f.readline()
for l in f:
	l = l.rstrip()
	l = l.split("\t")
	if float(l[5]) < 1e-300:
		l[5] = '1e-301'
	obsP.append(float(l[5]))
f.close()
obsP.sort(reverse=True)

N=len(obsP)
step = (1-1/float(N))/float(N)

expP = []
for i in range(0, N):
	expP.append(1-float(i)*step)

obsP = -np.log10(obsP)
expP = -np.log10(expP)
xMax = max(expP)
yMax = max(obsP)
l = xMax/width
h = yMax/height
filtlimit = -math.log10(1e-5)

mat = np.column_stack((expP, obsP))
mat = np.array(mat)
plot = []
plot.append(['exp', 'obs'])
#print mat[0:3]

cur_x = 0
while cur_x < xMax:
	temp1 = mat[mat[:,0]>=cur_x]
	temp1 = temp1[temp1[:,0]<cur_x+l]
	if temp1.shape[0]==0:
		cur_x += l
		continue
	cur_h = min(list(temp1[:,1]))
	max_h = max(list(temp1[:,1]))
	while cur_h < filtlimit:
		if cur_h > max_h:
			break
		temp = temp1[temp1[:,1]>=cur_h]
		temp = temp[temp[:,1]<cur_h+h]
		if temp.shape[0]==0:
			cur_h += h
			continue
		elif temp.shape[0]>2:
			#print temp.shape
			temp = temp[np.random.randint(temp.shape[0], size=2)]
		for i in temp:
			plot.append(i.tolist())
		cur_h += h
	cur_x += l

for i in mat[mat[:,1]>=filtlimit]:
	plot.append(i.tolist())

outfile = open(filedir+"QQSNPs.txt", 'w')
outfile.write("\t".join(plot[0])+"\n")
plot = plot[1:]
for i in plot:
	outfile.write(str(i[0])+"\t"+str(i[1])+"\n")
