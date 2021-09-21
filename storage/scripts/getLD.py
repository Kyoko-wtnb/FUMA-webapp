#!/usr/bin/python
import time
import os
import subprocess
import sys
import re
import pandas as pd
import numpy as np
import tabix
import glob
import ConfigParser
from bisect import bisect_left

##### initialize parameters #####
class getParams:
	def __init__(self, filedir, cfg, param_cfg):
		leadSNPs = param_cfg.get('inputfiles', 'leadSNPsfile')
		if leadSNPs == "NA":
		    print "prefedined lead SNPs are not provided"
		    leadSNPs = None
		else:
		    print "predefined lead SNPs are provided"
		    leadSNPs = filedir+cfg.get('inputfiles', 'leadSNPs')
		addleadSNPs = int(param_cfg.get('inputfiles', 'addleadSNPs')) #1 to add, 0 to not add
		regions = param_cfg.get('inputfiles', 'regionsfile')
		if regions == "NA":
		    print "predefined genomic regions are not provided"
		    regions = None
		else:
		    print "predefined genomic regions are provided"
		    regions = filedir+cfg.get('inputfiles', 'regions')

		refpanel = param_cfg.get('params', 'refpanel')
		pop = param_cfg.get('params', 'pop')
		leadP = float(param_cfg.get('params', 'leadP'))
		refSNPs = int(param_cfg.get('params', 'refSNPs')) #1 to add, 0 to not add
		gwasP = float(param_cfg.get('params', 'gwasP'))
		maf = float(param_cfg.get('params', 'MAF'))
		r2 = float(param_cfg.get('params', 'r2'))
		if param_cfg.has_option('params', 'r2_2'):
			r2_2 = float(param_cfg.get('params', 'r2_2'))
		else:
			r2_2 = 0.1
		mergeDist = int(param_cfg.get('params', 'mergeDist'))*1000
		MHC = int(param_cfg.get('params', 'exMHC')) # 1 to exclude, 0 to not
		extMHC = param_cfg.get('params', 'extMHC')
		mhcopt = param_cfg.get('params', 'MHCopt')
		if MHC==1 and mhcopt=="magma":
			MHC = 0
		MHCstart = 29614758 # hg19
		MHCend = 33170276 # hg19
		if extMHC != "NA":
		    mhc = extMHC.split("-")
		    MHCstart = int(mhc[0])
		    MHCend = int(mhc[1])

		###### input files #####
		gwas = filedir+cfg.get('inputfiles', 'snps')
		refgenome_dir = cfg.get('data', 'refgenome')
		annot_dir = refgenome_dir+"/"+refpanel+"/annot"

		##### get column index ######
		chrcol = 0
		poscol = 1
		neacol = 2
		eacol = 3
		rsIDcol = 4
		pcol = 5
		orcol = None
		becol = None
		secol = None

		f = open(gwas, 'r')
		head = f.readline()
		f.close()
		head = head.strip().split()
		for i in range(0,len(head)):
			if head[i] == "or":
				orcol = i
			elif head[i] == "beta":
				becol = i
			elif head[i] == "se":
				secol = i

		##### dbSNP file #####
		dbSNPfile = cfg.get('data', 'dbSNP')+"/RsMerge146.npy"

		##### set aprams #####
		self.leadSNPs = leadSNPs
		self.addleadSNPs = addleadSNPs
		self.regions = regions
		self.refpanel = refpanel
		self.pop = pop
		self.leadP = leadP
		self.refSNPs = refSNPs #1 to add, 0 to not add
		self.gwasP = gwasP
		self.maf = maf
		self.r2 = r2
		self.r2_2 = r2_2
		self.mergeDist = mergeDist
		self.MHC = MHC # 1 to exclude, 0 to not
		self.extMHC = extMHC
		self.MHCstart = MHCstart
		self.MHCend = MHCend
		self.gwas = gwas
		self.annot_dir = annot_dir
		self.refgenome_dir = refgenome_dir
		self.chrcol = chrcol
		self.poscol = poscol
		self.neacol = neacol
		self.eacol = eacol
		self.rsIDcol = rsIDcol
		self.pcol = pcol
		self.orcol = orcol
		self.becol = becol
		self.secol = secol
		self.dbSNPfile = dbSNPfile

##### Return index of a1 which exists in a2 #####
def ArrayIn(a1, a2):
	results = np.where(np.in1d(a1, a2))[0]
	return results

##### return unique element in list #####
def unique(a):
	unique = []
	[unique.append(s) for s in a if s not in unique]
	return unique

##### update rsID #####
# need to optimize
def rsIDup(snps, rsIDi, dbSNPfile):
	rsID = np.memmap(dbSNPfile, mode='r', dtype='int', shape=(11684784, 3))

	for i in range(0, len(snps)):
		rs = int(snps[i,rsIDi].replace('rs', ''))
		if rs in rsID[:,0]:
			rs = 'rs'+str(rsID[rsID[:,0]==rs,1])
			snps[i, rsIDi] = rs
	return snps

##### separate GWAS file by chromosome #####
def separateGwasByChr(gwas):
	gwasfile_chr = []
	chr_cur = 0
	cur_i = 0
	row = 0
	gwasf = open(gwas, 'r')
	gwasf.readline()
	for l in gwasf:
		row += 1
		l = re.match(r"^(\d+)\t.+", l)
		chr_tmp = int(l.group(1))
		if chr_tmp == chr_cur:
			gwasfile_chr[cur_i-1][2] += 1
		else:
			chr_cur = chr_tmp
			gwasfile_chr.append([chr_cur, row, 1])
			cur_i += 1
	gwasf.close()

	gwasfile_chr = np.array(gwasfile_chr)
	return gwasfile_chr

##### get LD scructure and MAF per chromosome #####
def chr_process(ichrom, gwasfile_chr, regions, leadSNPs, params):
	### Parameters
	addleadSNPs = params.addleadSNPs
	refpanel = params.refpanel
	pop = params.pop
	leadP = params.leadP
	refSNPs = params.refSNPs
	gwasP = params.gwasP
	maf = params.maf
	r2 = params.r2
	MHC = params.MHC # 1 to exclude, 0 to not
	extMHC = params.extMHC
	MHCstart = params.MHCstart
	MHCend = params.MHCend
	annot_dir = params.annot_dir
	refgenome_dir = params.refgenome_dir
	chrcol = params.chrcol
	poscol = params.poscol
	neacol = params.neacol
	eacol = params.eacol
	rsIDcol = params.rsIDcol
	pcol = params.pcol
	orcol = params.orcol
	becol = params.becol
	secol = params.secol

	chrom = int(gwasfile_chr[ichrom][0])
	print "Start chromosome "+str(chrom)+" ..."

	### check pre-defined regions
	regions_tmp = None
	if regions is not None:
		regions_tmp = regions[regions[:,0]==chrom]
		if len(regions_tmp)==0:
			return [], [], []
		regions_tmp = regions_tmp[regions_tmp[:,1].argsort()]

	### check pre-defined lead SNPs
	leadSNPs_tmp = None
	if leadSNPs is not None:
		leadSNPs_tmp = leadSNPs[leadSNPs[:,1]==chrom]
		if len(leadSNPs_tmp) == 0 and addleadSNPs == 0:
			return [], [], []

	### read gwas file for the current chromsome
	gwas_in = pd.read_csv(params.gwas, header=None, sep="\t", skiprows=gwasfile_chr[ichrom][1], nrows=gwasfile_chr[ichrom][2])
	gwas_in = np.array(gwas_in)

	### exclude MHC region
	if chrom == 6 and MHC == 1:
		print "Excluding MHC regions ..."
		gwas_in = gwas_in[(gwas_in[:,poscol].astype(int)<MHCstart) | (gwas_in[:,poscol].astype(int)>MHCend)]

	### filter SNPs for pre-defined regions (if provided)
	if regions_tmp is not None:
		gwas_tmp = []
		for l in regions_tmp:
			tmp = gwas_in[(gwas_in[:,poscol].astype(int)>=l[1]) & (gwas_in[:,poscol].astype(int)<=l[2])]
			if len(tmp)>0:
				if len(gwas_tmp)>0:
					gwas_tmp = np.r_[gwas_tmp, tmp]
				else:
					gwas_tmp = tmp
		if len(gwas_tmp) == 0:
			return [], [], []
		gwas_in = gwas_tmp
	gwas_in = gwas_in[np.lexsort((gwas_in[:,pcol], gwas_in[:,poscol]))]

	print str(len(gwas_in))+" SNPs in chromosome "+str(chrom)

	### init variables
	ld = []
	canSNPs = []
	IndSigSNPs = []
	nlead = 0
	pos_set = set(gwas_in[:,poscol])
	posall = gwas_in[:,poscol]

	ldfile = refgenome_dir+"/"+refpanel+'/'+pop+"/"+pop+".chr"+str(chrom)+".ld.gz"
	maffile = refgenome_dir+"/"+refpanel+'/'+pop+"/"+pop+".chr"+str(chrom)+".frq.gz"
	if not os.path.isfile(maffile) or not os.path.isfile(ldfile):
		print "Reference file does not exist for chr: "+str(chrom)
		return [], [], []

	rsIDset = set(gwas_in[:, rsIDcol])
	checkeduid = set()

	### process pre-defined lead SNPs
	if leadSNPs_tmp is not None:
		for l in leadSNPs_tmp:
			if not l[0] in rsIDset:
				print "Input lead SNP "+l[0]+" does not exists in input gwas file"
				continue # rsID of lead SNPs needs to be matched with the one in GWAS file

			igwas = np.where(gwas_in[:,rsIDcol]==l[0])[0][0]
			l_uid = ":".join([str(gwas_in[igwas, chrcol]), str(gwas_in[igwas, poscol])]+sorted([gwas_in[igwas, neacol], gwas_in[igwas, eacol]]))
			pos = int(l[2])

			### check if the lead SNP meat other condition
			tb = tabix.open(maffile)
			lead_id = False
			lead_maf = False
			check_id = tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos))
			for m in check_id:
				tmp_uid = ":".join([m[0], m[1]]+sorted([m[3], m[4]]))
				if tmp_uid == l_uid:
					lead_id = True
					if float(m[5]) >= maf:
						lead_maf = True
					break
			if not lead_id or not lead_maf:
				continue

			nlead += 1
			### get SNPs in LD
			tb = tabix.open(ldfile)
			ld_tb = tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos))
			ld_tmp = []
			ld_tmp.append([l[2], l[0], 1])
			for m in ld_tb:
				if int(m[1]) != pos:
					continue
				if float(m[6]) >= r2:
					ld_tmp.append([m[4], m[5], m[6]])
			ld_tmp = np.array(ld_tmp)

			### check MAF and add to array
			minpos = min(ld_tmp[:,0].astype(int))
			maxpos = max(ld_tmp[:,0].astype(int))
			tb = tabix.open(maffile)
			maf_tb = tb.querys(str(chrom)+":"+str(minpos)+"-"+str(maxpos))
			nonGWASSNPs = 0
			GWASSNPs = 0
			for m in maf_tb:
				## skip SNPs in MHC if exMHC==1
				if chrom==6 and MHC==1 and int(m[1])>=MHCstart and int(m[1])<=MHCend:
					continue
				## MAF filtering
				if float(m[5]) < maf:
					continue
				if m[1] in ld_tmp[:,0]:
					ild = np.where(ld_tmp[:,0]==m[1])[0][0]
					## process SNP if exists in input GWAS
					if int(m[1]) in pos_set:
						jgwas = bisect_left(posall, int(m[1]))

						uid = ":".join([str(gwas_in[jgwas, chrcol]), str(gwas_in[jgwas, poscol])]+sorted([gwas_in[jgwas, neacol], gwas_in[jgwas, eacol]]))
						tmp_uid = ":".join([m[0], m[1]]+sorted([m[3], m[4]]))
						if uid != tmp_uid:
							checkall = False
							jgwas += 1
							while jgwas < len(gwas_in) and int(m[1]) == gwas_in[jgwas, poscol]:
								uid = ":".join([str(gwas_in[jgwas, chrcol]), str(gwas_in[jgwas, poscol])]+sorted([gwas_in[jgwas, neacol], gwas_in[jgwas, eacol]]))
								if uid == tmp_uid:
									checkall = True
									break
								jgwas += 1
							if not checkall:
								continue

						### do not filter on P-value for provided lead SNPs
						if not uid==l_uid and float(gwas_in[jgwas, pcol])>=gwasP:
							continue

						ld.append([l_uid, uid, ld_tmp[ild, 2]])

						if uid in checkeduid:
							continue

						checkeduid.add(uid)
						p = str(gwas_in[jgwas, pcol])
						snp = [uid, gwas_in[jgwas, rsIDcol], m[0], m[1], gwas_in[jgwas, neacol], gwas_in[jgwas, eacol], m[5], p]
						if orcol:
							snp.append(str(gwas_in[jgwas, orcol]))
						if becol:
							snp.append(str(gwas_in[jgwas, becol]))
						if secol:
							snp.append(str(gwas_in[jgwas, secol]))
						canSNPs.append(snp)
						GWASSNPs += 1
					## process SNPs which do not exist in input file
					elif refSNPs==1:
						tmp_uid = ":".join([m[0], m[1]]+sorted([m[3], m[4]]))
						ld.append([l_uid, tmp_uid, ld_tmp[ild, 2]])
						if tmp_uid in checkeduid:
							continue
						checkeduid.add(tmp_uid)
						snp = [tmp_uid, m[2], m[0], m[1], m[4], m[3], m[5], "NA"]
						if orcol:
							snp.append("NA")
						if becol:
							snp.append("NA")
						if secol:
							snp.append("NA")
						canSNPs.append(snp)
						nonGWASSNPs += 1

			IndSigSNPs.append([l_uid, l[0], str(l[1]), str(l[2]), str(gwas_in[igwas, pcol]), str(nonGWASSNPs+GWASSNPs), str(GWASSNPs)])

		if len(gwas_in[gwas_in[:,pcol]<leadP]) == 0:
			if len(canSNPs)>0:
				ld = np.array(ld)
				canSNPs = np.array(canSNPs)
				IndSigSNPs = np.array(IndSigSNPs)
				IndSigSNPs = IndSigSNPs[IndSigSNPs[:,3].astype(int).argsort()]
				n = canSNPs[:,3].astype(int).argsort()
				canSNPs = canSNPs[n]
				return ld, canSNPs, IndSigSNPs
			else:
				return [], [], []

	### check if there are still sig SNPs
	if len(gwas_in[gwas_in[:,pcol].astype(float)<leadP]) == 0:
		if len(canSNPs)>0:
			ld = np.array(ld)
			canSNPs = np.array(canSNPs)
			IndSigSNPs = np.array(IndSigSNPs)
			IndSigSNPs = IndSigSNPs[IndSigSNPs[:,3].astype(int).argsort()]
			n = canSNPs[:,3].astype(int).argsort()
			canSNPs = canSNPs[n]
			return ld, canSNPs, IndSigSNPs
		else:
			return [], [], []

	### identifies sig SNPs
	p_order = gwas_in[:,pcol].argsort()
	if leadSNPs is None or addleadSNPs == 1:
		for pi in p_order:
			l = gwas_in[pi]
			if float(l[pcol])>=leadP:
				break
			l_uid = ":".join([str(l[chrcol]), str(l[poscol])]+sorted([l[neacol], l[eacol]]))
			if not l_uid in checkeduid:
				pos = l[poscol]
				### check if the SNP meat other condition
				tb = tabix.open(maffile)
				lead_id = False
				lead_maf = False
				check_id = tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos))
				for m in check_id:
					tmp_uid = ":".join([m[0], m[1]]+sorted([m[3], m[4]]))
					if tmp_uid == l_uid:
						lead_id = True
						if float(m[5]) >= maf:
							lead_maf = True
						break
				if not lead_id or not lead_maf:
					continue
				nlead += 1

				### get SNPs in LD
				tb = tabix.open(ldfile)
				# ld_tb = tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos))
				ld_tmp = []
				ld_tmp.append([l[poscol], l[rsIDcol], 1])
				for m in tb.querys(str(chrom)+":"+str(pos)+"-"+str(pos)):
					if int(m[1]) != pos:
						continue
					if float(m[6]) >= r2:
						ld_tmp.append([m[4], m[5], m[6]])
				ld_tmp = np.array(ld_tmp)

				### get MAF
				minpos = min(ld_tmp[:,0].astype(int))
				maxpos = max(ld_tmp[:,0].astype(int))
				tb = tabix.open(maffile)
				maf_tb = tb.querys(str(chrom)+":"+str(minpos)+"-"+str(maxpos))
				nonGWASSNPs = 0
				GWASSNPs = 0
				for m in maf_tb:
					## skip SNPs in MHC if exMHC==1
					if chrom==6 and MHC==1 and int(m[1])>=MHCstart and int(m[1])<=MHCend:
						continue
					if float(m[5]) < maf:
						continue
					if int(m[1]) in ld_tmp[:,0].astype(int):
						ild = np.where(ld_tmp[:,0].astype(int)==int(m[1]))[0][0]
						## process SNPs exist in input file
						if int(m[1]) in pos_set:
							jgwas = bisect_left(posall, int(m[1]))
							uid = ":".join([str(gwas_in[jgwas, chrcol]), str(gwas_in[jgwas, poscol])]+sorted([gwas_in[jgwas, neacol], gwas_in[jgwas, eacol]]))
							tmp_uid = ":".join([m[0], m[1]]+sorted([m[3], m[4]]))
							if uid != tmp_uid:
								checkall = False
								jgwas += 1
								while jgwas < len(gwas_in) and int(m[1]) == gwas_in[jgwas, poscol]:
									uid = ":".join([str(gwas_in[jgwas, chrcol]), str(gwas_in[jgwas, poscol])]+sorted([gwas_in[jgwas, neacol], gwas_in[jgwas, eacol]]))
									if uid == tmp_uid:
										checkall = True
										break
									jgwas += 1
								if not checkall:
									continue

							if float(gwas_in[jgwas, pcol])>=gwasP:
								continue

							ld.append([l_uid, tmp_uid, ld_tmp[ild, 2]])
							if tmp_uid in checkeduid:
								continue
							checkeduid.add(tmp_uid)
							p = str(gwas_in[jgwas, pcol])
							snp = [tmp_uid, gwas_in[jgwas, rsIDcol], m[0], m[1], gwas_in[jgwas, neacol], gwas_in[jgwas, eacol], m[5], p]
							if orcol:
								snp.append(str(gwas_in[jgwas, orcol]))
							if becol:
								snp.append(str(gwas_in[jgwas, becol]))
							if secol:
								snp.append(str(gwas_in[jgwas, secol]))
							canSNPs.append(snp)
							GWASSNPs += 1
						## process SNPs do not exist in input file
						elif refSNPs==1:
							tmp_uid = ":".join([m[0], m[1]]+sorted([m[3], m[4]]))
							ld.append([l_uid, tmp_uid, ld_tmp[ild, 2]])
							if tmp_uid in checkeduid:
								continue
							checkeduid.add(tmp_uid)
							snp = [tmp_uid, m[2], m[0], m[1], m[4], m[3], m[5], "NA"]
							if orcol:
								snp.append("NA")
							if becol:
								snp.append("NA")
							if secol:
								snp.append("NA")
							canSNPs.append(snp)
							nonGWASSNPs += 1
				IndSigSNPs.append([l_uid, l[4], str(l[0]), str(l[1]), str(l[5]), str(nonGWASSNPs+GWASSNPs), str(GWASSNPs)])

	if len(canSNPs)>0:
		ld = np.array(ld)
		canSNPs = np.array(canSNPs)
		IndSigSNPs = np.array(IndSigSNPs)
		IndSigSNPs = IndSigSNPs[IndSigSNPs[:,3].astype(int).argsort()]
		n = canSNPs[:,3].astype(int).argsort()
		canSNPs = canSNPs[n]
	return ld, canSNPs, IndSigSNPs

##### get annotations for candidate SNPs #####
def getAnnot(snps, annot_dir):
	chroms = unique(snps[:,2].astype(int))
	out = []
	### process per chromosome
	for chrom in chroms:
		annotfile = annot_dir+"/chr"+str(chrom)+".annot.gz"

		tmp = snps[snps[:,2].astype(int)==chrom]
		if len(tmp)==0:
			continue

		## split snps into chunks
		ranges = []
		start = min(tmp[:,3].astype(int))
		end = min(tmp[:,3].astype(int))
		cur_start = start
		cur_end = end
		for l in tmp:
			if int(l[3])-cur_start < 1000000:
				cur_end = int(l[3])
			else:
				ranges.append([cur_start, cur_end])
				cur_start = int(l[3])
				cur_end = int(l[3])
		ranges.append([cur_start, cur_end])

		tmp = tmp[tmp[:,0].argsort()]
		suid = set(tmp[:,0])

		## get annotations
		tmp_out = []
		for i in range(0, len(ranges)):
			tb = tabix.open(annotfile)
			annot_tb = tb.querys(str(chrom)+":"+str(ranges[i][0])+"-"+str(ranges[i][1]))
			for l in annot_tb:
				uid = ":".join([l[0], l[1]]+sorted([l[2], l[3]]))
				if uid in suid:
					j = bisect_left(tmp[:,0], uid)
					tmp_out.append([tmp[j,2], tmp[j,3], uid]+l[4:])
		tmp_out = np.array(tmp_out)
		tmp_out = tmp_out[np.lexsort((tmp_out[:,0], tmp_out[:,1])), 2:]

		if len(out)==0:
			out = tmp_out
		else:
			out = np.r_[out, tmp_out]
	return out

##### defined lead SNPs from ind. sig. SNPs
def getLeadSNPs(chrom, snps, IndSigSNPs, params):
	leadSNPs = []
	checked = []
	IndSigSNPs = IndSigSNPs[IndSigSNPs[:,4].astype(float).argsort()]
	for snp in IndSigSNPs:
		if snp[1] in checked:
			continue
		ldfile = params.refgenome_dir+'/'+params.refpanel+'/'+params.pop+'/'+params.pop+'.chr'+str(snp[2])+'.ld.gz';
		tb = tabix.open(ldfile)
		ld_tmp = tb.querys(snp[2]+":"+snp[3]+"-"+snp[3])
		inSNPs = []
		inSNPs.append(snp[1])

		for l in ld_tmp:
			if float(l[6])<params.r2_2:
				continue
			if int(l[1]) != int(snp[3]):
				continue
			if int(l[4]) in IndSigSNPs[:,3].astype(int):
				rsID = IndSigSNPs[IndSigSNPs[:,3].astype(int)==int(l[4]),1][0]
				checked.append(rsID)
				inSNPs.append(rsID)
		leadSNPs.append([snp[0], snp[1], snp[2], snp[3], snp[4], str(len(inSNPs)), ";".join(inSNPs)])
	leadSNPs = np.array(leadSNPs)
	leadSNPs = leadSNPs[leadSNPs[:,3].astype(int).argsort()]

	return leadSNPs

##### Merge lead SNPs into genomic risk loci
def getGenomicRiskLoci(gidx, chrom, snps, ld, IndSigSNPs, leadSNPs, params):
	loci = []
	iloci = 0
	chrom = 0
	inInd = []
	inLead = []
	nonGWASSNPs = []
	GWASSNPs = []
	uid2gl = {}
	for i in range(0, len(leadSNPs)):
		if i == 0:
			chrom = int(leadSNPs[i, 2])
			rsIDs = list(leadSNPs[i,6].split(";"))
			uid = list(snps[ArrayIn(snps[:,1], rsIDs),0])
			for s in uid:
				uid2gl[s] = gidx+1
			inInd = rsIDs
			inLead = [leadSNPs[i,1]]
			n = ArrayIn(snps[:,0], ld[ArrayIn(ld[:,0], uid),1])
			snps_tmp = snps[n]
			nonGWASSNPs += list(snps_tmp[snps_tmp[:,7]=="NA", 0])
			GWASSNPs += list(snps_tmp[snps_tmp[:,7]!="NA", 0])
			start = min(snps[n,3].astype(int))
			end = max(snps[n,3].astype(int))
			loci.append([str(gidx+1)]+list(leadSNPs[i,range(0,5)])+[str(start), str(end), str(len(nonGWASSNPs)+len(GWASSNPs)), str(len(GWASSNPs)), str(len(inInd)), ";".join(inInd), str(len(inLead)), ";".join(inLead)])
		else:
			rsIDs = list(leadSNPs[i,6].split(";"))
			uid = list(snps[ArrayIn(snps[:,1], rsIDs),0])
			for s in uid:
				uid2gl[s] = gidx+1
			inInd += rsIDs
			inInd = unique(inInd)
			inLead += [leadSNPs[i,1]]
			n = ArrayIn(snps[:,0], ld[ArrayIn(ld[:,0], uid),1])
			snps_tmp = snps[n]
			nonGWASSNPs += list(snps_tmp[snps_tmp[:,7]=="NA", 0])
			GWASSNPs += list(snps_tmp[snps_tmp[:,7]!="NA", 0])
			nonGWASSNPs = unique(nonGWASSNPs)
			GWASSNPs = unique(GWASSNPs)
			start = min(snps_tmp[:,3].astype(int))
			end = max(snps_tmp[:,3].astype(int))
			if start <= int(loci[iloci][7]) or start-int(loci[iloci][7])<params.mergeDist:
				loci[iloci][6] = str(min(start, int(loci[iloci][6])))
				loci[iloci][7] = str(max(end, int(loci[iloci][7])))
				loci[iloci][8] = str(len(nonGWASSNPs)+len(GWASSNPs))
				loci[iloci][9] = str(len(GWASSNPs))
				loci[iloci][10] = str(len(inInd))
				loci[iloci][11] = ";".join(inInd)
				loci[iloci][12] = str(len(inLead))
				loci[iloci][13] = ";".join(inLead)
				if float(leadSNPs[i,4]) < float(loci[iloci][5]):
					loci[iloci][1] = leadSNPs[i,0]
					loci[iloci][2] = leadSNPs[i,1]
					loci[iloci][4] = leadSNPs[i,3]
					loci[iloci][5] = leadSNPs[i,4]
				if iloci > 0 and int(loci[iloci][6])-int(loci[iloci-1][7])<params.mergeDist:
					iloci -= 1
					loci[iloci][6] = str(min(loci[iloci][6], loci[iloci+1][6]))
					loci[iloci][7] = str(max(loci[iloci][7], loci[iloci+1][7]))
					loci[iloci][11] = ";".join(unique(loci[iloci][11].split(";")+loci[iloci+1][11].split(";")))
					loci[iloci][10] = len(loci[iloci][11].split(";"))
					loci[iloci][13] = ";".join(unique(loci[iloci][13].split(";")+loci[iloci+1][13].split(";")))
					loci[iloci][12] = len(loci[iloci][13].split(";"))
					n = ArrayIn(snps[:,0], ld[ArrayIn(ld[:,0], snps[ArrayIn(snps[:,1], loci[iloci][11].split(";")),0]),1])
					loci[iloci][8] = len(n)
					loci[iloci][9] = len(np.where(snps[n,7]!="NA")[0])
					if float(loci[iloci+1][5]) < float(loci[iloci][5]):
						loci[iloci][1] = loci[iloci+1][1]
						loci[iloci][2] = loci[iloci+1][2]
						loci[iloci][4] = loci[iloci+1][4]
						loci[iloci][5] = loci[iloci+1][5]
					tmp_loci = []
					for i in range(0, iloci+1):
						tmp_loci.append(loci[i])
					loci = tmp_loci
					for key in uid2gl:
						if uid2gl[key]==gidx+1:
							uid2gl[key] -= 1
					gidx -= 1
			else:
				gidx += 1
				iloci += 1
				inInd = []
				inLead = []
				nonGWASSNPs = []
				GWASSNPs = []
				rsIDs = list(leadSNPs[i,6].split(";"))
				uid = list(snps[ArrayIn(snps[:,1], rsIDs),0])
				for s in uid:
					uid2gl[s] = gidx+1
				inInd = rsIDs
				inLead = [leadSNPs[i,1]]
				n = ArrayIn(snps[:,0], ld[ArrayIn(ld[:,0], uid),1])
				snps_tmp = snps[n,]
				nonGWASSNPs += list(snps_tmp[snps_tmp[:,7]=="NA", 0])
				GWASSNPs += list(snps_tmp[snps_tmp[:,7]!="NA", 0])
				start = min(snps[n,3].astype(int))
				end = max(snps[n,3].astype(int))
				loci.append([str(gidx+1)]+list(leadSNPs[i,range(0,5)])+[str(start), str(end), str(len(nonGWASSNPs)+len(GWASSNPs)), str(len(GWASSNPs)), str(len(inInd)), ";".join(inInd), str(len(inLead)), ";".join(inLead)])
	loci = np.array(loci)
	gidx += 1
	return loci, uid2gl, gidx

def main():
	starttime = time.time()

	##### check arguments #####
	if len(sys.argv)<2:
		sys.exit('ERROR: not enough arguments\nUSAGE ./getLD.py <filedir>')

	filedir = sys.argv[1]
	if re.match(".+\/$", filedir) is None:
		filedir += '/'

	##### get config files #####
	cfg = ConfigParser.ConfigParser()
	cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

	param_cfg = ConfigParser.ConfigParser()
	param_cfg.read(filedir+'params.config')

	##### get parameters #####
	params = getParams(filedir, cfg, param_cfg)

	##### output files #####
	ldout = filedir+"ld.txt"
	snpsout = filedir+"snps.txt"
	annotout = filedir+"annot.txt"
	indsigout = filedir+"IndSigSNPs.txt"
	leadout = filedir+"leadSNPs.txt"
	glout = filedir+"GenomicRiskLoci.txt"

	##### write headers #####
	with open(ldout, 'w') as o:
		o.write("\t".join(["SNP1","SNP2","r2"])+"\n")

	ohead = "\t".join(["uniqID", "rsID", "chr", "pos", "non_effect_allele", "effect_allele", "MAF", "gwasP"])
	if params.orcol:
		ohead += "\tor"
	if params.becol:
		ohead += "\tbeta"
	if params.secol:
		ohead += "\tse"
	ohead += "\tr2\tIndSigSNP\tGenomicLocus"
	ohead += "\n"
	with open(snpsout, 'w') as o:
		o.write(ohead)

	tmp = subprocess.check_output('gzip -cd '+params.annot_dir+'/chr1.annot.gz | head -1', shell=True)
	tmp = tmp.strip().split()

	ohead = "\t".join(["uniqID"]+tmp[4:])
	ohead += "\n"
	with open(annotout, 'w') as o:
		o.write(ohead)

	with open(indsigout, 'w') as o:
		o.write("\t".join(["No", "GenomicLocus", "uniqID", "rsID", "chr", "pos", "p","nSNPs", "nGWASSNPs"])+"\n")

	with open(leadout, 'w') as o:
		o.write("\t".join(["No", "GenomicLocus", "uniqID", "rsID", "chr", "pos", "p","nIndSigSNPs", "IndSigSNPs"])+"\n")

	with open(glout, 'w') as o:
		o.write("\t".join(["GenomicLocus", "uniqID", "rsID", "chr", "pos", "p", "start", "end", "nSNPs", "nGWASSNPs", "nIndSigSNPs", "IndSigSNPs", "nLeadSNPs", "LeadSNPs"])+"\n")

	##### region file #####
	# 0: chr, 1: start, 2: end
	regions = None
	if params.regions:
		regions = pd.read_csv(params.regions, comment="#", delim_whitespace=True, dtype='str')
		regions.iloc[:,0] = regions.iloc[:,0].apply(lambda x: re.sub('x','23',re.sub('chr', '', x, flags=re.IGNORECASE), flags=re.IGNORECASE))
		regions.iloc[:,0] = pd.to_numeric(regions.iloc[:,0], downcast='integer', errors='coerce')
		regions.iloc[:,1] = pd.to_numeric(regions.iloc[:,1], downcast='integer', errors='coerce')
		regions.iloc[:,2] = pd.to_numeric(regions.iloc[:,2], downcast='integer', errors='coerce')
		regions = np.array(regions)

	##### lead SNPs file #####
	# 0: rsID, 1: chr, 2: pos
	inleadSNPs = None
	if params.leadSNPs:
		inleadSNPs = pd.read_csv(params.leadSNPs, comment="#", delim_whitespace=True, dtype='str')
		inleadSNPs.iloc[:,1] = inleadSNPs.iloc[:,1].apply(lambda x: re.sub('x','23',re.sub('chr', '', x, flags=re.IGNORECASE), flags=re.IGNORECASE))
		inleadSNPs.iloc[:,1] = pd.to_numeric(inleadSNPs.iloc[:,1], downcast='integer', errors='coerce')
		inleadSNPs.iloc[:,2] = pd.to_numeric(inleadSNPs.iloc[:,2], downcast='integer', errors='coerce')
		inleadSNPs = np.array(inleadSNPs)
		#inleadSNPs = rsIDup(inleadSNPs, 0, params.dbSNPfile)

	##### get row index for each chromosome #####
	# input file needs to be sorted by chr and position
	gwasfile_chr = separateGwasByChr(params.gwas)

	##### process per chromosome #####
	nSNPs = 0
	gidx = 0 #risk loci index
	IndSigIdx = 0
	leadIdx = 0
	for i in range(0, len(gwasfile_chr)):
		chrom = chrom = gwasfile_chr[i][0]
		ld, snps, IndSigSNPs = chr_process(i, gwasfile_chr, regions, inleadSNPs, params)
		if len(snps)>0:
			nSNPs += len(IndSigSNPs)
			### get annot
			annot = getAnnot(snps, params.annot_dir)
			tmp_uids = list(annot[:,0])
			#overlap snps in reference with snps with annotation. Error occurs if you don't when using chr23 on ukb 10k EUR reference
			snpsi = np.array([a[0] in tmp_uids for a in snps])
			snps=snps[snpsi]
			annot = annot[[tmp_uids.index(x) for x in snps[:,0]]]
			### get lead SNPs
			leadSNPs = getLeadSNPs(chrom, snps, IndSigSNPs, params)
			### get Genomic risk loci
			loci, uid2gl, gidx = getGenomicRiskLoci(gidx, chrom, snps, ld, IndSigSNPs, leadSNPs, params)

			### add columns for sig SNPs
			addcol = []
			for i in range(0,len(IndSigSNPs)):
				addcol.append([str(IndSigIdx+i+1), str(uid2gl[IndSigSNPs[i,0]])])
			IndSigSNPs = np.c_[addcol, IndSigSNPs]
			IndSigIdx += len(IndSigSNPs)

			addcol = []
			for i in range(0,len(leadSNPs)):
				addcol.append([str(leadIdx+i+1), str(uid2gl[leadSNPs[i,0]])])
			leadSNPs = np.c_[addcol, leadSNPs]
			leadIdx += len(leadSNPs)

			### snps add columns
			pd_ld = pd.DataFrame(ld)
			pd_ld[[2]] = pd_ld[[2]].astype(float)
			idx = pd_ld.groupby(1)[2].transform(max) == pd_ld[2]
			uid1 = np.array(pd_ld[0][idx].tolist())
			uid2 = np.array(pd_ld[1][idx].tolist())
			r2 = np.array(pd_ld[2][idx].tolist())
			tmp = list(snps[:,0])
			uid2 = list(uid2)
			idx = [uid2.index(x) for x in tmp]
			uid1 = uid1[idx]
			r2 = r2[idx]
			rsIDs = snps[[tmp.index(x) for x in uid1],1]
			tmp = list(IndSigSNPs[:,2])
			gl = IndSigSNPs[[tmp.index(x) for x in uid1],1]
			snps = np.c_[snps, r2, rsIDs, gl]

			### write outputs
			with open(snpsout, 'a') as o:
				np.savetxt(o, snps, delimiter="\t", fmt="%s")

			with open(ldout, 'a') as o:
				np.savetxt(o, ld, delimiter="\t", fmt="%s")

			with open(annotout, 'a') as o:
				np.savetxt(o, annot, delimiter="\t", fmt="%s")

			with open(indsigout, 'a') as o:
				np.savetxt(o, IndSigSNPs, delimiter="\t", fmt="%s")

			with open(leadout, 'a') as o:
				np.savetxt(o, leadSNPs, delimiter="\t", fmt="%s")

			with open(glout, 'a') as o:
				np.savetxt(o, loci, delimiter="\t", fmt="%s")

	##### exit if there is no SNPs with P<=leadP
	if nSNPs==0:
		sys.exit("No candidate SNP was identified")

	##### ANNOVAR #####
	os.system("python "+os.path.dirname(os.path.realpath(__file__))+"/annovar.py "+filedir)

	print "getLD.py run time: "+str(time.time()-starttime)

if __name__ == "__main__": main()
