#!/usr/bin/python
import sys
import os
import pandas as pd
import numpy as np
import ConfigParser
import re

def ArrayIn(a1, a2):
	results = np.where(np.in1d(a1, a2))[0]
	return results

def main():
	##### check arguments #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./magma_celltype.py <filedir>')

	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### get config files #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

	param_cfg = ConfigParser.ConfigParser()
	param_cfg.read(filedir+'params.config')

	##### Map to ENSG ID #####
	if param_cfg.get('params', 'ensg_id')=="0":
		print "Mapping genes to ENSG ID..."
		os.system("Rscript "+os.path.dirname(os.path.realpath(__file__))+"/magma_raw_ensg.R "+filedir)

	##### MAGMA #####
	magmafiles = cfg.get('magma', 'magmafiles')
	magmadir = cfg.get('magma', 'magmadir')
	datasets = param_cfg.get('params', 'datasets').split(":")

	### Step 1
	for ds in datasets:
		command = magmadir+"/magma --gene-results "+filedir+"magma.genes.raw"
		command += " --gene-covar "+magmafiles+"/celltype/"+ds+".txt"
		command += " --model condition-hide=Average direction=greater"
		command += " --out "+filedir+"magma_celltype_"+ds
		res = os.system(command)
		if res!=0:
			sys.exit("MAGMA ERROR")

	### Step 2 and 3
	if param_cfg.get('params', 'step2')=="1":
		step2 = param_cfg.get('params', 'step2')
		step3 = param_cfg.get('params', 'step3')
		adjPmeth = param_cfg.get('params', 'adhPmeth')

		

if __name__=="__main__": main()
