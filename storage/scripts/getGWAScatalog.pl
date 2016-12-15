#!/usr/bin/perl
use strict;
use warnings;

die "Error: not enought arguments\nUSAGE: ./getGWAScatalog.pl <filedir>" if(@ARGV<1);
my $filedir = $ARGV[0];
my $gwascat = "/media/sf_Documents/VU/Data/GWAScatalog/gwas_catalog_e85_2016-09-27.txt.gz"; #local
my $gwascat = "/data/GWAScatalog/gwas_catalog_e85_2016-09-27.txt.gz";
#webserver  #webservermy $in = $filedir."snps.txt";
my $out = $filedir."gwascatalog.txt";

#my $head = `gzip -cd $gwascat | head -1`;
#chomp $head;
my @head = qw(chr bp snp DateAddedToCatalog PMID FirstAuth Date Journal Link Study Trait InitialN ReplicationN	Region ReportedGene MappedGene UpGene DownGene SNP_Gene_ID UpGeneDist DownGeneDist Strongest SNPs marged SNP_ID_cur Context intergenic RiskAF P Pmlog Ptext OrBeta 95CI Platform CNV);
open(OUT, ">$out");
print OUT join("\t", ("Interval", "leadSNP", @head)), "\n";
open(IN, "$in") or die "Cannot open $in\n";
my $chr = 0;
my $start = 0;
my $end = 0;
my %SNP;
<IN>;
while(<IN>){
	my @line = split(/\s/, $_);
	$SNP{$line[1]}{"interval"}=$line[10];
	$SNP{$line[1]}{"leadSNP"}=$line[9];
	$start = $line[3] if($start == 0);
	if($line[2]==$chr){
		if($end-$start<1000000){
			$end = $line[3]
		}else{
			my @temp = split(/\n/, `tabix $gwascat $chr:$start-$end`);
			foreach my $l (@temp){
				my @l = split(/\t/, $l);
				if(exists $SNP{$l[2]}{"interval"}){
					print OUT join("\t", ($SNP{$l[2]}{"interval"}, $SNP{$l[2]}{"leadSNP"}, @l)), "\n";
				}
			}
			$start = $end;
		}
	}else{
		$chr=$line[2];
		$start = 0;
		$end = 0;
	}
}
close IN;
close OUT;
