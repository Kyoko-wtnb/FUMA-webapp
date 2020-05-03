#!/usr/bin/python
import sys
import os
import re
import pandas as pd
import numpy as np
import math
import json
from scipy.cluster.hierarchy import linkage, leaves_list

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	results = np.where(np.in1d(a1, a2))[0]
	return results

def ArrayNotIn(a1, a2):
    tmp = np.where(np.in1d(a1, a2))[0]
    return list(set(range(0,len(a1)))-set(tmp))

def main():
	##### check argument #####
	if len(sys.argv)<3:
		sys.exit("ERROR: not enough arguments\nUSAGE ./magma_expPlot.py <filedir>")

	##### get command line arguments #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'
	dataset = sys.argv[2]

	##### output #####
	out = {"data":[], "order_gene":{"alph":[], "clst":{"log2":[], "norm":[]}}, "order_label":{"alph":[], "clst":{"log2":[], "norm":[]}}}

	##### log2 average #####
	exp = pd.read_csv(filedir+dataset+"_exp.txt", header=0, sep="\t")
	label = list(exp.columns.values)[2:]
	genes = np.array(exp.symbol)
	exp = np.array(exp)[genes.argsort(),2:]
	genes = genes[genes.argsort()]

	gene_order_alph = range(0,len(genes))
	gene_order_clst_log2 = [int(x) for x in leaves_list(linkage(exp, "average")).argsort()]
	label_order_alph = range(0, len(label))
	label_order_clst_log2 = [int(x) for x in leaves_list(linkage(exp.transpose(), "average")).argsort()]
	exp_table = np.c_[np.repeat(genes, len(label)), label*len(genes), np.reshape(exp, (1,len(genes)*len(label)))[0]]

	##### norm average #####
	exp = pd.read_csv(filedir+dataset.replace("log2", "norm").replace("_MA", "_normMA")+"_exp.txt", header=0, sep="\t")
	genes = np.array(exp.symbol)
	exp = np.array(exp)[genes.argsort(),2:]
	genes = genes[genes.argsort()]
	gene_order_clst_norm = [int(x) for x in leaves_list(linkage(exp, "average")).argsort()]
	label_order_clst_norm = [int(x) for x in leaves_list(linkage(exp.transpose(), "average")).argsort()]
	exp_table = np.c_[exp_table, np.reshape(exp, (1,len(genes)*len(label)))[0]]

	gene_order = np.c_[gene_order_alph, gene_order_clst_log2, gene_order_clst_norm]
	label_order = np.c_[label_order_alph, label_order_clst_log2, label_order_clst_norm]

	print json.dumps({"data":[list(l) for l in exp_table], "gene":list(genes), "label":list(label), "order_gene":[list(l) for l in gene_order], "order_label":[list(l) for l in label_order]})

if __name__ == "__main__": main()
