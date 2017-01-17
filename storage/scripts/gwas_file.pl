#!/usr/bin/perl

# input : input.gwas
# output : input.snps
# update rsID
# min P-val is 1e-308
# P-val less than this will be replaced by 1e-308
# otherwise user need to modify this beforehand

use strict;
use warnings;
use IO::Zlib;
use Config::Simple;
use File::Basename;

my $dir = dirname(__FILE__);
my $cfg = new Config::Simple($dir.'/app.config');

# die "ERROR: not enought arguments\nUSAGE./gwas_file.pl <filedir> <gwas file format>\n" if(@ARGV<2);
die "ERROR: not enought arguments\nUSAGE./gwas_file.pl <filedir>\n" if(@ARGV<1);

my $filedir = $ARGV[0];
$filedir .='/' unless($filedir =~ /\/$/);
# my $gwasfile_format = $ARGV[1]; # removed this option 14-12-2106
#my $N = $ARGV[2];

my $gwas = $filedir.$cfg->param('inputfiles.gwas');

my $params = new Config::Simple($filedir.'params.config');

my $outSNPs = $filedir."input.snps";
my $outMAGMA = $filedir."magma.in";

my $dbSNPfile = $cfg->param('data.dbSNP');
my %rsID;
open(RS, "$dbSNPfile/RsMerge146.txt");
while(<RS>){
	my @line = split(/\s/, $_);
	$rsID{$line[0]}=$line[1];
}
close RS;

open(GWAS, "$gwas") or die "Cannot open $gwas\n";
my $head = <GWAS>;

## update column name options 17-01-2017
my $chrcol=$params->param('inputfiles.chrcol');
my $poscol=$params->param('inputfiles.poscol');
my $rsIDcol=$params->param('inputfiles.rsIDcol');
my $pcol=$params->param('inputfiles.pcol');
my $refcol=$params->param('inputfiles.refcol');
my $altcol=$params->param('inputfiles.altcol');
my $orcol=$params->param('inputfiles.orcol');
my $secol=$params->param('inputfiles.secol');
# my $mafcol=$params->param('inputfiles.mafcol');
# $mafcol = undef if($mafcol eq "NA");
my $Ncol=$params->param('params.Ncol');
$Ncol = undef if($Ncol eq "NA");

while($head =~ /^#/){
		$head = <GWAS>;
}
my @head = split(/\s+/, $head);
foreach my $i (0..$#head){
	if(uc($head[$i]) eq uc($rsIDcol) || $head[$i] =~ /^SNP$|^MarkerName$|^rsID$|^snpid$/i){$rsIDcol=$i}
	elsif(uc($head[$i]) eq uc($chrcol) ||$head[$i] =~ /^CHR$|^chromosome$|^chrom$/i){$chrcol=$i}
	elsif(uc($head[$i]) eq uc($poscol) ||$head[$i] =~ /^BP$|^pos$|^position$/i){$poscol=$i}
	elsif(uc($head[$i]) eq uc($altcol) ||$head[$i] =~ /^A1$|^Effect_allele$|^alt$|^allele1$|^alleleB$/i){$altcol=$i}
	elsif(uc($head[$i]) eq uc($refcol) ||$head[$i] =~ /^A2$|^Non_Effect_allele$|^ref$|^allele2$|^alleleA$/i){$refcol=$i}
	elsif(uc($head[$i]) eq uc($pcol) ||$head[$i] =~ /^P$|^pval$|^pvalue$|^p-value$|^p_value$|^frequentist_add_pvalue$/i){$pcol=$i}
	elsif(uc($head[$i]) eq uc($orcol) ||$head[$i] =~ /^OR$/i){$orcol=$i}
	elsif(uc($head[$i]) eq uc($secol) ||$head[$i] =~ /^SE$/i){$secol=$i}
	# elsif(uc($head[$i]) eq uc($mafcol)){$mafcol=$i}
	elsif(uc($head[$i]) eq uc($Ncol) ||$head[$i] =~ /^SE$/i){$Ncol=$i}
}

$chrcol = undef if($chrcol eq "NA");
$poscol = undef if($poscol eq "NA");
$rsIDcol = undef if($rsIDcol eq "NA");
$pcol = undef if($pcol eq "NA");
$refcol = undef if($refcol eq "NA");
$altcol = undef if($altcol eq "NA");
$orcol = undef if($orcol eq "NA");
$secol = undef if($secol eq "NA");
if($Ncol eq $params->param('params.Ncol')){
	die "N column name was not found";
}

# print "chrcol:$chrcol\nposcol:$poscol\nrsIDcol:$rsIDcol\npcol:$pcol\nrefcol:$refcol\naltcol:$altcol\norcol:$orcol\nsecol:$secol\n";
# unless(defined $mafcol){
# 	print "MAF columns is not defined\n";
# }

if(!(defined $pcol)){die "P-value column was not found\n";}
elsif(!(defined $chrcol && defined $poscol) && !(defined $rsIDcol)){die "Chromosome, position or rsID column was not found\n";}

## modify params.config orcol and secol
if($params->param("inputfiles.orcol") eq "NA" && defined $orcol){$params->param("inputfiles.orcol", "or")}
if($params->param("inputfiles.secol") eq "NA" && defined $secol){$params->param("inputfiles.secol", "se")}
$params->save();

my %GWAS;
#print "chr: $chrcol, pos: $poscol, rsID: $rsIDcol, ref: $refcol, alt: $altcol, p: $pcol\n";

if(defined $chrcol && defined $poscol){
	while(<GWAS>){
		next if(/^#/);
		my @line = split(/\s/, $_);
		$line[$chrcol] =~ s/chr//;
		$line[$chrcol]=23 if($line[$chrcol]=~/X|x/);
		$line[$pcol] = 1e-308 if($line[$pcol]<1e-308); #avoid NA or inf in R
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"p"}=$line[$pcol];
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"ref"}=uc($line[$refcol]) if(defined $refcol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"alt"}=uc($line[$altcol]) if(defined $altcol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"or"}=$line[$orcol] if(defined $orcol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"se"}=$line[$secol] if(defined $secol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"N"}=$line[$Ncol] if(defined $Ncol);
		# if(defined $mafcol){
		# 	$line[$mafcol] = 1-$line[$mafcol] if($line[$mafcol]>0.5);
		# 	$GWAS{$line[$chrcol]}{$line[$poscol]}{"maf"}=$line[$mafcol];
		# }
		if(defined $rsIDcol){
			$line[$rsIDcol] = $rsID{$line[$rsIDcol]} if(exists $rsID{$line[$rsIDcol]});
			$GWAS{$line[$chrcol]}{$line[$poscol]}{"rsID"}=$line[$rsIDcol];
		}
	}

	unless(defined $refcol && defined $altcol && defined $rsIDcol){
		print "Either ref, alt or rsID is not defined\n";
		foreach my $chr(1..23){
			next unless(exists $GWAS{$chr});
			my $refgenome = $cfg->param('data.refgenome');
			my $file = "$refgenome/ALL/ALL.chr$chr.frq.gz";
 			my $fin = IO::Zlib->new($file, 'rb');
			while(<$fin>){
				my @line = split(/\s/, $_);
				if(exists $GWAS{$line[0]}{$line[1]}{"p"}){
					$GWAS{$line[0]}{$line[1]}{"rsID"}=$line[2] unless(defined $rsIDcol);
					$GWAS{$line[0]}{$line[1]}{"rsID"}=$rsID{$GWAS{$line[0]}{$line[1]}{"rsID"}} if(exists $rsID{$GWAS{$line[0]}{$line[1]}{"rsID"}});
					next if(defined $refcol && defined $altcol);
					if(defined $refcol){
						if($GWAS{$line[0]}{$line[1]}{"ref"} eq $line[4]){$GWAS{$line[0]}{$line[1]}{"alt"}=$line[5]}
						else{$GWAS{$line[0]}{$line[1]}{"alt"}=$line[4]}
					}elsif(defined $altcol){
						if($GWAS{$line[0]}{$line[1]}{"alt"} eq $line[4]){$GWAS{$line[0]}{$line[1]}{"ref"}=$line[5]}
						else{$GWAS{$line[0]}{$line[1]}{"ref"}=$line[4]}
					}else{
						$GWAS{$line[0]}{$line[1]}{"ref"}=$line[5];
						$GWAS{$line[0]}{$line[1]}{"alt"}=$line[4];
					}
				}
			}
		}
	}

	my $outhead = "chr\tbp\tref\talt\trsID\tp";
	$outhead .= "\tor" if(defined $orcol);
	$outhead .= "\tse" if(defined $secol);
	# $outhead .= "\tmaf" if(defined $mafcol);
	$outhead .= "\tN" if(defined $Ncol);
	$outhead .= "\n";
	open(SNP, ">$outSNPs");
	print SNP $outhead;
	foreach my $chr (sort {$a<=>$b} keys %GWAS){
		foreach my $pos (sort {$a<=>$b} keys %{$GWAS{$chr}}){
			print SNP join("\t", ($chr, $pos, $GWAS{$chr}{$pos}{"ref"}, $GWAS{$chr}{$pos}{"alt"}, $GWAS{$chr}{$pos}{"rsID"}, $GWAS{$chr}{$pos}{"p"}));
			print SNP "\t", $GWAS{$chr}{$pos}{"or"} if(defined $orcol);
			print SNP "\t", $GWAS{$chr}{$pos}{"se"} if(defined $secol);
			# print SNP "\t", $GWAS{$chr}{$pos}{"maf"} if(defined $mafcol);
			print SNP "\t", $GWAS{$chr}{$pos}{"N"} if(defined $Ncol);
			print SNP "\n";
		}
	}
	close SNP;
}else{
	print "Either chr or pos is not defined\n";
	while(<GWAS>){
		my @line = split(/\s/, $_);
		$line[$rsIDcol] = $rsID{$line[$rsIDcol]} if(exists $rsID{$line[$rsIDcol]});
		$GWAS{$line[$rsIDcol]}{"p"}=$line[$pcol];
		$GWAS{$line[$rsIDcol]}{"ref"}=uc($line[$refcol]) if(defined $refcol);
		$GWAS{$line[$rsIDcol]}{"alt"}=uc($line[$altcol]) if(defined $altcol);
		$GWAS{$line[$rsIDcol]}{"or"}=$line[$orcol] if(defined $orcol);
		$GWAS{$line[$rsIDcol]}{"se"}=$line[$secol] if(defined $secol);
		$GWAS{$line[$rsIDcol]}{"N"}=$line[$secol] if(defined $Ncol);
		# if(defined $mafcol){
		# 	$line[$mafcol] = 1-$line[$mafcol] if($line[$mafcol]>0.5);
		# 	$GWAS{$line[$rsIDcol]}{"maf"}=$line[$mafcol];
		# }
	}

	my $dbSNP = "$dbSNPfile/snp146_pos_allele.txt";
 	open(DB, "$dbSNP") or die "Cannot opne $dbSNP\n";
	open(SNP, ">$outSNPs");
	my $outhead = "chr\tbp\tref\talt\trsID\tp";
	$outhead .= "\tor" if(defined $orcol);
	$outhead .= "\tse" if(defined $secol);
	$outhead .= "\tN" if(defined $Ncol);
	# $outhead .= "\tmaf" if(defined $mafcol);
	$outhead .= "\n";
	print SNP $outhead;
	while(<DB>){
		my @line = split(/\s/, $_);
		$line[1] = 23 if($line[1] =~ /x/i);
		if(exists $GWAS{$line[3]}){
			if(defined $refcol && defined $altcol){
				if(($line[4] eq $GWAS{$line[3]}{"ref"} && $line[5] eq $GWAS{$line[3]}{"alt"}) || ($line[5] eq $GWAS{$line[3]}{"ref"} && $line[4] eq $GWAS{$line[3]}{"alt"})){
					print SNP "$line[1]\t$line[2]\t$line[4]\t$line[5]\t$line[3]\t", $GWAS{$line[3]}{"p"};
					print SNP "\t", $GWAS{$line[3]}{"or"} if(defined $orcol);
					print SNP "\t", $GWAS{$line[3]}{"se"} if(defined $secol);
					# print SNP "\t", $GWAS{$line[3]}{"maf"} if(defined $mafcol);
					print SNP "\t", $GWAS{$line[3]}{"N"} if(defined $Ncol);
					print SNP "\n";
				}
			}elsif(defined $refcol){
				if($line[5] eq $GWAS{$line[3]}{"ref"} || $line[4] eq $GWAS{$line[3]}{"ref"}){
					print SNP "$line[1]\t$line[2]\t$line[4]\t$line[5]\t$line[3]\t", $GWAS{$line[3]}{"p"};
					print SNP "\t", $GWAS{$line[3]}{"or"} if(defined $orcol);
					print SNP "\t", $GWAS{$line[3]}{"se"} if(defined $secol);
					# print SNP "\t", $GWAS{$line[3]}{"maf"} if(defined $mafcol);
					print SNP "\t", $GWAS{$line[3]}{"N"} if(defined $Ncol);
					print SNP "\n";
				}
			}elsif(defined $altcol){
				if($line[5] eq $GWAS{$line[3]}{"alt"} || $line[4] eq $GWAS{$line[3]}{"alt"}){
					print SNP "$line[1]\t$line[2]\t$line[4]\t$line[5]\t$line[3]\t", $GWAS{$line[3]}{"p"};
					print SNP "\t", $GWAS{$line[3]}{"or"} if(defined $orcol);
					print SNP "\t", $GWAS{$line[3]}{"se"} if(defined $secol);
					# print SNP "\t", $GWAS{$line[3]}{"maf"} if(defined $mafcol);
					print SNP "\t", $GWAS{$line[3]}{"N"} if(defined $Ncol);
					print SNP "\n";
				}
			}else{
				print SNP "$line[1]\t$line[2]\t$line[4]\t$line[5]\t$line[3]\t", $GWAS{$line[3]}{"p"};
				print SNP "\t", $GWAS{$line[3]}{"or"} if(defined $orcol);
				print SNP "\t", $GWAS{$line[3]}{"se"} if(defined $secol);
				# print SNP "\t", $GWAS{$line[3]}{"maf"} if(defined $mafcol);
				print SNP "\t", $GWAS{$line[3]}{"N"} if(defined $Ncol);
				print SNP "\n";
			}
		}
	}
	close DB;
	close SNP;
}

close GWAS;
delete @rsID{keys %rsID};
delete @GWAS{keys %GWAS};
