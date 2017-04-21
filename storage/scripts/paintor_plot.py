#!/usr/bin/python
import pandas as pd
import numpy as np
import sys
import os
import re
import ConfigParser
from bisect import bisect_left
import json

def ArrayIn(a1, a2):
	# results = [i for i, x in enumerate(a1) if x in a2]
	results = np.where(np.in1d(a1, a2))[0]
	return results

def Get_Annot(paintor_annot, paintor_annotdir):
	if paintor_annot[0] == "NA":
		return ["NA"]
	elif paintor_annot[0] == "all":
		annot = []
		with open(paintor_annotdir+"/annotation_paths", 'r') as fin:
			for l in fin:
				annot.append(re.match(r'.*\/(.+)', l).group(1))
		return annot
	else:
		annot = [re.match(r'.*\/(.+)', x).group(1) for x in paintor_annot]
		return annot

def Get_Annot_Bed(chrom, min_pos, max_pos, paintor_annot, paintor_annotdir):
	annot_bed = []
	for f in paintor_annot:
		bedfile = paintor_annotdir+"/"+f
		bed = pd.read_table(bedfile, header=None, delim_whitespace=True)
		bed = np.array(bed)
		bed = bed[:,[0,1,2]]
		bed = bed[bed[:,0]=="chr"+str(chrom)]
		bed = bed[:,[1,2]]
		if len(bed)==0:
			annot_bed.append([])
			continue
		bed[:,0] = bed[:,0].astype(int)
		bed[:,1] = bed[:,1].astype(int)

		bed = bed[bed[:,1].astype(int) >= min_pos-1]
		if len(bed)==0:
			annot_bed.append([])
			continue
		bed = bed[bed[:,0].astype(int) <= max_pos-1]
		if len(bed)==0:
			annot_bed.append([])
			continue
		for i in range(0, len(bed)):
			if bed[i,0] < min_pos-1:
				bed[i,0] = min_pos-1
			if bed[i,1] > max_pos-1:
				bed[i,1] = max_pos-1
		bed[:,0] = [x+1 for x in bed[:,0]]
		bed[:,1] = [x+1 for x in bed[:,1]]
		annot_bed.append([list(x) for x in bed])
	return annot_bed

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./paintor_plot.py <filedir> <locus name>')

	##### get parameters #####
	filedir = sys.argv[1]
	locus = sys.argv[2]

	##### add '/' to the filedir #####
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### config variables #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')
	paintor_annotdir = cfg.get('paintor', 'annot')
	param = ConfigParser.RawConfigParser()
	param.optionxform = str
	param.read(filedir+'params.config')
	paintor_annot = param.get('paintor', 'annot')
	if paintor_annot == "NA":
		paintor_annot = ["NA"]
	else:
		paintor_annot = paintor_annot.split(":")
		if "all" in paintor_annot:
			paintor_annot = ["all"]

	##### snps data #####
	snps = pd.read_table(filedir+"PAINTOR/output/"+locus+".results", sep=" ")
	snps = np.array(snps)
	snps[:,0] = [int(x.replace("chr", "")) for x in snps[:,0]]
	chrom = int(snps[0,0])
	snps = snps[:,[1,2,6]]
	snpsall = pd.read_table(filedir+"snps.txt", sep="\t")
	snpsall_head = list(snpsall.columns.values)
	snpsall = np.array(snpsall)
	snpsall = snpsall[:,[snpsall_head.index("rsID"), snpsall_head.index("gwasP"), snpsall_head.index("r2")]]
	snpsall = snpsall[ArrayIn(snpsall[:,0], snps[:,1])]
	snps = np.c_[snps, snpsall[:,[1,2]]]

	IndSigSNPs = pd.read_table(filedir+"IndSigSNPs.txt", sep="\t")
	IndSigSNPs = np.array(IndSigSNPs)
	IndSigSNPs = IndSigSNPs[:,3]
	snps = np.c_[snps, [0]*len(snps)]
	snps[ArrayIn(snps[:,1], IndSigSNPs),5] = 1
	snps = snps[:, [0,2,3,4,5]]
	min_pos = min(snps[:,0].astype(int))
	max_pos = max(snps[:,0].astype(int))
	snps_out = []
	for l in snps:
		snps_out.append(list(l))

	# annot = Get_Annot(paintor_annot, paintor_annotdir)
	annot = paintor_annot
	annot_bed = []
	if annot[0] != "NA":
		annot_bed = Get_Annot_Bed(chrom, min_pos, max_pos, paintor_annot, paintor_annotdir)
	else:
		annot = []

	data = {'data':snps_out, 'chr':chrom, 'annot':annot, 'annot_bed':annot_bed}
	print json.dumps(data)

if __name__ == '__main__' : main()
