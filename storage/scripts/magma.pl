#!/usr/bin/perl
use strict;
use warnings;

die "ERROR: not enought arguments\nUSAGE./magma.pl <filedir> <sample N> <population>\n" if(@ARGV<3);

my $filedir = $ARGV[0];
my $N = $ARGV[1];
my $pop = $ARGV[2];

my $outSNPs = $filedir."input.snps";
my $magmain = $filedir."mamga.input";
my $ref = "/media/sf_SAMSUNG/MAGMA/g1000_".lc($pop); #local
#webserver my $ref = "/data/MAGMA/g1000_".lc($pop);

system "awk 'NR>=2' $outSNPs | cut -f 5,6 | sort -u -k 1,1 > $magmain";
system "magma --bfile $ref --pval $magmain N=$N --gene-annot /media/sf_Documents/VU/Data/MAGMA/ENSG.w0.genes.annot --out $filedir"."magma"; #local
#webserver system "magma --bfile $ref --pval $outMAGMA N=$N --gene-annot /data/MAGMA/ENSG.w0.genes.annot --out $filedir"."magma"; #local

system "rm $filedir*.raw $filedir*.log";
system "sed 's/ \\+/\\t/g' ".$filedir."magma.genes.out >$filedir"."temp.txt";
system "mv ".$filedir."temp.txt ".$filedir."magma.genes.out";
