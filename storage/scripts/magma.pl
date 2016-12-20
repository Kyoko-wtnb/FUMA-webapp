#!/usr/bin/perl
use strict;
use warnings;
use Config::Simple;
use File::Basename;

die "ERROR: not enought arguments\nUSAGE./magma.pl <filedir> <sample N> <population>\n" if(@ARGV<3);

my $dir = dirname(__FILE__);
my $cfg = new Config::Simple($dir.'/app.config');

my $filedir = $ARGV[0];
$filedir .= '/' unless($filedir =~ /\/$/);
my $N = $ARGV[1];
my $pop = $ARGV[2];
if($pop =~ /\+/){
	#$pop =~ s/\(|\)//g;
	($pop) = split(/\+/, $pop);
}
$pop = lc($pop);

print "Population: $pop\n";

my $outSNPs = $filedir."input.snps";
my $magmain = $filedir."magma.input";
my $magmafiles = $cfg->param('magma.magmafiles');
my $ref = "$magmafiles/g1000_".lc($pop)."_146";
system "awk 'NR>=2' $outSNPs | cut -f 5,6 | sort -u -k 1,1 > $magmain";
my $magma = $cfg->param('magma.magmadir');

system "$magma/magma --bfile $ref --pval $magmain N=$N --gene-annot $magmafiles/ENSG.w0.$pop.genes.annot --out $filedir"."magma";
unless(-e $filedir."magma.genes.out"){
	die "201";
}
system "rm $filedir*.raw $filedir*.log";
system "sed 's/ \\+/\\t/g' ".$filedir."magma.genes.out > $filedir"."temp.txt";
system "mv ".$filedir."temp.txt ".$filedir."magma.genes.out";
