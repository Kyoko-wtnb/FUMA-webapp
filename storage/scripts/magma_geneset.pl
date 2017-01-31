#!/usr/bin/perl
use strict;
use warnings;
use Config::Simple;
use File::Basename;

die "ERROR: not enought arguments\nUSAGE./magma._geneset.pl <filedir>\n" if(@ARGV<1);

my $filedir = $ARGV[0];
$filedir .= '/' unless($filedir =~ /\/$/);

my $dir = dirname(__FILE__);
my $cfg = new Config::Simple($dir.'/app.config');
my $magmafiles = $cfg->param('magma.magmafiles');

# MAGMA gene set
system "magma --gene-results $filedir"."magma.genes.raw --set-annot $magmafiles/magma_GS.txt --out $filedir"."magma";
system "rm $filedir"."magma*.log";

system "Rscript $dir/magma_gene.R $filedir";
