#!/usr/bin/python
import pandas as pd
import numpy as np
import sys
import os
import re
import scipy.stats as st
import ConfigParser
import time
import tabix
from bisect import bisect_left

def Check_Column(zcol, becol, orcol):
	##### parameter check #####
	if zcol=="NA" and becol=="NA" and orcol=="NA":
		sys.exit("ERROR: Neither Z score nor signed effect size is not given")
	zscore = False
	if zcol != "NA":
		zscore = True
	return zscore

def Get_Zscore(filedir, zscore):
	##### read snps file #####
	snpsfile = filedir+"snps.txt"
	snps = pd.read_table(snpsfile, sep="\t")
	snpshead = list(snps.columns.values)
	snps = snps.as_matrix()
	snps = snps[np.invert(np.isnan(snps[:,7].astype(float)))]

	snps_headi = [snpshead.index("GenomicLocus"), snpshead.index("chr"), snpshead.index("pos"), snpshead.index("rsID"), snpshead.index("ref"), snpshead.index("alt")]
	if zscore:
		snps_headi.append(snpshead.index("z"))
	else:
		if "or" in snpshead:
			snps_headi.append(snpshead.index("or"))
			snps_headi.append(snpshead.index("gwasP"))
			direction = "or"
		elif "beta" in snpshead:
			snps_headi.append(snpshead.index("beta"))
			snps_headi.append(snpshead.index("gwasP"))
			direction = "beta"

	snps = snps[:,snps_headi]

	if not zscore:
		z = [abs(st.norm.ppf(x/2)) for x in snps[:,7]]
		if min(snps[:,6]) < 0:
			d = [1 if x>0 else -1 for x in snps[:,6]]
		else:
			d = [1 if x>1 else -1 for x in snps[:,6]]
		z = [a*b for a,b in zip(z,d)]
		snps = np.c_[snps[:,range(0,6)],z]

	return snps

def Create_LDmatrix(filedir, snps, chrom, locus, refgenome, pop):
	"""
	1. extract subset of vcf in a given Population
	2. check allele and flip z if neccesarry
	3. create ld matrix (r)
	4. output processed snps
	"""
	min_pos = min(snps[:,2].astype(int))
	max_pos = max(snps[:,2].astype(int))
	reffile = refgenome+"/"+pop.upper()+"/"+pop.upper()+".chr"+str(chrom)+".phase3.SNPs.vcf.gz"
	os.system("tabix -h "+reffile+" "+str(chrom)+":"+str(min_pos)+"-"+str(max_pos)+" >"+filedir+"PAINTOR/tmp.vcf")
	tmpvcf = []
	snps_out = []
	pos_set = set(snps[:,2])
	with open(filedir+"PAINTOR/tmp.vcf", 'r') as fin:
		for l in fin:
			if re.match(r'^#.*', l):
				l = l.strip()
				tmpvcf.append(l)
			else:
				line = l.strip().split()
				if "," in line[3] or "," in line[4]:
					continue
				if "rs" not in line[2]:
					continue
				if int(line[1]) in pos_set:
					l = l.strip()
					tmpvcf.append(l)
					j = bisect_left(snps[:,2].astype(int), int(line[1]))
					if snps[j,4] == line[3] and snps[j,5] == line[4]:
						snps_out.append(list(snps[j,1:3])+[line[2]]+list(snps[j,4:7]))
					elif snps[j,4] == line[4] and snps[j,5] == line[3]:
						snps_out.append(list(snps[j,1:3])+[line[2]]+[snps[j,5], snps[j,4]]+[-1*float(snps[j,6])])
	if len(snps_out) <= 1:
		return False

	with open(filedir+"PAINTOR/tmp.vcf", 'w') as fout:
		tmpvcf = np.array(tmpvcf)
		np.savetxt(fout, tmpvcf, fmt='%s', delimiter='')

	fout = filedir+"PAINTOR/input/Locus"+str(locus)
	snps_out = np.array(snps_out)
	snps_out[:,0] = ["chr"+str(x) for x in snps_out[:,0].astype(int)]
	with open(fout, 'w') as o:
		o.write(" ".join(["chr", "pos", "rsid", "ref", "alt", "Zscore"])+"\n")
	with open(fout, 'a') as o:
		np.savetxt(o, snps_out, delimiter=" ", fmt='%s')

	os.system("plink --vcf "+filedir+"PAINTOR/tmp.vcf --r --matrix --out "+filedir+"PAINTOR/input/Locus"+str(locus))
	os.system("rm "+filedir+"PAINTOR/tmp.vcf "+filedir+"PAINTOR/input/*.log "+filedir+"PAINTOR/input/*.nosex")
	return True

def Create_Annotfile(filedir, locus, paintor, paintor_annotdir, paintor_annot):
	if paintor_annot[0] == "NA":
		nsnps = sum(1 for l in open(filedir+"PAINTOR/input/Locus"+str(locus)))
		nsnps -= 1
		with open(filedir+"PAINTOR/input/Locus"+str(locus)+".annotations", 'w') as fout:
			fout.write("fake\n")
			fout.write("\n".join(["0"]*nsnps))
	else:
		if not os.path.isfile(filedir+"PAINTOR/annotation_paths"):
			if paintor_annot[0] == "all":
				with open(paintor_annotdir+"/annotation_paths", 'r') as fin:
					with open(filedir+"PAINTOR/annotation_paths", 'w') as fout:
						for l in fin:
							fout.write(paintor_annotdir+"/"+l)
			else:
				with open(filedir+"PAINTOR/annotation_paths", 'w') as fout:
					for l in paintor_annot:
						fout.write(paintor_annotdir+"/"+l+"\n")
		os.system("python "+paintor+"/PAINTOR_Utilities/AnnotateLocus.py --input "+filedir+"PAINTOR/annotation_paths --locus "+filedir+"PAINTOR/input/Locus"+str(locus)+" --out "+filedir+"PAINTOR/input/Locus"+str(locus)+".annotations --chr chr --pos pos")

def Prepare_Input_Files(filedir, snps, locus, chrom, refgenome, pop, paintor, paintor_annotdir, paintor_annot):
	snps = snps[np.argsort(snps[:,2].astype(int))]

	checkSNPs = Create_LDmatrix(filedir, snps, chrom, locus, refgenome, pop)
	if checkSNPs:
		Create_Annotfile(filedir, locus, paintor, paintor_annotdir, paintor_annot)
		with open(filedir+"PAINTOR/input/input.files", 'a') as fout:
			fout.write("Locus"+str(locus)+"\n")
		return True
	else:
		return False

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

def Run_PAINTOR(filedir, paintor, annot, options):
	command = paintor+"/PAINTOR -input "+filedir+"PAINTOR/input/input.files -in "+filedir+"PAINTOR/input/ -out "+filedir+"PAINTOR/output/ -Zhead Zscore -LDname ld"
	if annot[0] != "NA":
		command += " -annotations "+",".join(annot)
	if options != "NA":
		command += " "+options
	os.system(command)

# def Run_CANVIS(filedir, locus, paintor, annot):
# 	command = "python "+paintor+"/CANVIS/CANVIS.py -l "+filedir+"PAINTOR/output/Locus"+str(locus)+".results -z Zscore -r "+filedir+"PAINTOR/input/Locus"+str(locus)+".ld -a "+filedir+"PAINTOR/input/Locus"+str(locus)+".annotations -s "+" ".join(annot)+" -t 99 -L n -o"+filedir+"PAINTOR/plots/Locus"+str(locus)
# 	os.system(command)
#	#os.remove(filedir+"PAINTOR/plots/*.html")

def main():
	##### check argument #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./paintor.py <filedir>')

	##### start time #####
	start = time.time()

	##### add '/' to the filedir #####
	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### config variables #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

	param = ConfigParser.RawConfigParser()
	param.optionxform = str
	param.read(filedir+'params.config')

	##### get parameters #####
	refgenome = cfg.get("data", "refgenome")
	paintor = cfg.get("paintor", "paintor")
	paintor_annotdir = cfg.get("paintor", "annot")
	paintor_annot = param.get("paintor", "annot")
	if paintor_annot == "NA":
		paintor_annot = ["NA"]
	else:
		paintor_annot = paintor_annot.split(":")
		if "all" in paintor_annot:
			paintor_annot = ["all"]
	paintor_opt = param.get("paintor", "options")
	zcol = param.get("inputfiles", "zcol")
	becol = param.get("inputfiles", "becol")
	orcol = param.get("inputfiles", "orcol")
	pop = param.get("params", "pop")

	##### paintor directory #####
	if not os.path.isdir(filedir+"PAINTOR"):
		os.mkdir(filedir+"PAINTOR")
	if not os.path.isdir(filedir+"PAINTOR/input"):
		os.mkdir(filedir+"PAINTOR/input")
		os.mkdir(filedir+"PAINTOR/output")
		# os.mkdir(filedir+"PAINTOR/plots")

	open(filedir+"PAINTOR/input/input.files", 'w').close()
	zscore = Check_Column(zcol, becol, orcol)
	snps = Get_Zscore(filedir, zscore)
	loci = np.unique(snps[:,0])
	checkedLoci = []
	for i in loci:
		chrom = snps[snps[:,0]==i,1][0]
		checkLoci = Prepare_Input_Files(filedir, snps[snps[:,0]==i], i, chrom, refgenome, pop, paintor, paintor_annotdir, paintor_annot)
		if checkLoci:
			checkedLoci.append(i)

	if len(checkedLoci)==0:
		sys.exit("ERROR: There was no locus containing more than one SNP after filtering. To perform PAINTOR, it require at least more than one locus with at least 2 SNPs.")

	annot = Get_Annot(paintor_annot, paintor_annotdir)
	#Run_PAINTOR(filedir, paintor, annot, paintor_opt)

	# for i in checkedLoci:
	# 	Run_CANVIS(filedir, i, paintor, annot)

	##### zip files #####
	os.system("zip -r "+filedir+"PAINTOR.zip "+filedir+"PAINTOR")

	print time.time() - start

if __name__ == "__main__" : main()
