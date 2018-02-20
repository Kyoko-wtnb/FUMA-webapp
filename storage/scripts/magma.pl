#!/usr/bin/perl
use strict;
use warnings;
use Config::Simple;
use File::Basename;

##### check arguments #####
die "ERROR: not enought arguments\nUSAGE./magma.pl <filedir>\n" if(@ARGV<1);

my $filedir = $ARGV[0];
$filedir .= '/' unless($filedir =~ /\/$/);

##### get config files #####
my $dir = dirname(__FILE__);
my $cfg = new Config::Simple($dir.'/app.config');
my $params = new Config::Simple($filedir.'params.config');

##### get prams ######
my $N = $params->param('params.N');
my $Ncol = $params->param('params.Ncol');
$N = undef if($N eq "NA");

my $pop = $params->param('params.pop');
if($pop =~ /\+/){
	#$pop =~ s/\(|\)//g;
	($pop) = split(/\+/, $pop);
}
$pop = lc($pop);
print "Population: $pop\n";

my $MHC = $params->param('params.exMHC'); # 1 to exclude, 0 to not
my $MHCopt = $params->param('params.MHCopt');
if($MHC eq "1"){
	if($MHCopt eq "annot"){
		$MHC = "0";
	}
}
my $extMHC = $params->param('params.extMHC');
my $MHCstart = 29614758;
my $MHCend = 33170276;
unless($extMHC eq "NA"){
	my @temp = split(/-/, $extMHC);
	$MHCstart = $temp[0];
	$MHCend = $temp[1];
}

my $outSNPs = $filedir."input.snps";
my $magmain = $filedir."magma.input";
my $magmafiles = $cfg->param('magma.magmafiles');
my $ref = "$magmafiles/g1000_".lc($pop);
my $magma = $cfg->param('magma.magmadir');
my @magma_exp = split(/:/, $params->param('magma.magma_exp'));

if(defined $N){
	if($MHC eq "1"){
		system "awk 'NR>=2' $outSNPs | awk '{if(\$1!=6){print \$0}else if(\$2<$MHCstart || \$2>$MHCend){print \$0}}' | cut -f 5,6 | sort -u -k 1,1 > $magmain"
	}else{
		system "awk 'NR>=2' $outSNPs | cut -f 5,6 | sort -u -k 1,1 > $magmain";
	}
	system "$magma/magma --bfile $ref --pval $magmain N=$N --gene-annot $magmafiles/ENSG.w0.$pop.genes.annot --out $filedir"."magma";
}else{
	open(TMP, $outSNPs);
	my $head = <TMP>;
	close TMP;
	my @head = split(/\s+/, $head);
	$Ncol = $#head+1;
	if($MHC eq "1"){
		system "awk 'NR>=2' $outSNPs | awk '{if(\$1!=6){print \$0}else if(\$2<$MHCstart || \$2>$MHCend){print \$0}}' | cut -f 5,6,$Ncol | sort -u -k 1,1 > $magmain"
	}else{
		system "awk 'NR>=2' $outSNPs | cut -f 5,6,$Ncol | sort -u -k 1,1 > $magmain";
	}
	open(IN, "$magmain");
	my $tmpfile = $filedir."temp.txt";
	open(OUT, ">$tmpfile");
	while(<IN>){
		my @line = split(/\s/, $_);
		$line[2] = int($line[2]);
		print OUT join("\t", @line), "\n";
	}
	close OUT;
	close IN;
	system "mv $tmpfile $magmain";
	system "$magma/magma --bfile $ref --pval $magmain ncol=3 --gene-annot $magmafiles/ENSG.w0.$pop.genes.annot --out $filedir"."magma";
}

unless(-e $filedir."magma.genes.out"){
	die "MAGMA ERROR";
}

system "sed 's/ \\+/\\t/g' ".$filedir."magma.genes.out > $filedir"."temp.txt";
system "mv ".$filedir."temp.txt ".$filedir."magma.genes.out";

# MAGMA gene set
system "$magma/magma --gene-results $filedir"."magma.genes.raw --set-annot $magmafiles/magma_GS.txt --out $filedir"."magma";
# MAGMA gene expression
foreach my $f (@magma_exp){
	my @tmp = split(/\//, $f);
	my $out = $tmp[$#tmp];
	print $out;
	system "$magma/magma --gene-results $filedir"."magma.genes.raw --gene-covar $magmafiles/$f.txt onesided=greater condition=Average --out $filedir"."magma_exp_$out";
}

system "Rscript $dir/magma_gene.R $filedir";
