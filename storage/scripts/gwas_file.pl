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

# die "ERROR: not enought arguments\nUSAGE./gwas_file.pl <filedir> <gwas file format>\n" if(@ARGV<2);
die "ERROR: not enought arguments\nUSAGE./gwas_file.pl <filedir>\n" if(@ARGV<1);

my $filedir = $ARGV[0];
# my $gwasfile_format = $ARGV[1]; # removed this option 14-12-2106
#my $N = $ARGV[2];
my $gwas = $filedir."input.gwas";

my $outSNPs = $filedir."input.snps";
my $outMAGMA = $filedir."magma.in";

my $dbSNP = "/media/sf_SAMSUNG/dbSNP/RsMerge146.txt"; #local #local
#webserver my $dbSNP = "/data/dbSNP/RsMerge146.txt";
my %rsID;
open(RS, "$dbSNP");
while(<RS>){
	my @line = split(/\s/, $_);
	$rsID{$line[0]}=$line[1];
}
close RS;

open(GWAS, "$gwas") or die "Cannot open $gwas\n";
my $head = <GWAS>;
my $rsIDcol=undef;
my $chrcol=undef;
my $poscol=undef;
my $pcol=undef;
my $refcol=undef;
my $altcol=undef;

while($head =~ /^#/){
		$head = <GWAS>;
	}
	my @head = split(/\s+/, $head);
	foreach my $i (0..$#head){
		if($head[$i] =~ /^SNP$|^MarkerName$|^rsID$/i){$rsIDcol=$i}
		elsif($head[$i] =~ /^CHR$|^chromosome$|^chrom$/i){$chrcol=$i}
		elsif($head[$i] =~ /^BP$|^pos$|^position$/i){$poscol=$i}
		elsif($head[$i] =~ /^A1$|^Effect_allele$|^alt$|^allele1$/i){$altcol=$i}
		elsif($head[$i] =~ /^A2$|^Non_Effect_allele$|^ref$|^allele2$/i){$refcol=$i}
		elsif($head[$i] =~ /^P$|^pvalue$|^p-value$|^p_value$/i){$pcol=$i}
	}

if(!(defined $pcol)){die "P-value column was not found\n";}
elsif(!(defined $chrcol && defined $poscol && defined $rsIDcol)){die "Chromosome, position or rsID column was not found\n";}

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
		if(defined $rsIDcol){
			$line[$rsIDcol] = $rsID{$line[$rsIDcol]} if(exists $rsID{$line[$rsIDcol]});
			$GWAS{$line[$chrcol]}{$line[$poscol]}{"rsID"}=$line[$rsIDcol];
		}
	}

	unless(defined $refcol && defined $altcol && defined $rsIDcol){
		print "Either ref, alt or rsID is not defined\n";
		foreach my $chr(1..23){
		next unless(exists $GWAS{$chr});
			my $file = "/media/sf_SAMSUNG/1KG/Phase3/EUR/EUR.chr$chr.frq.gz"; #local #local
#webserver 			my $file = "/data/1KG/Phase3/EUR/EUR.chr$chr.frq.gz";
 			my $fin = IO::Zlib->new($file, 'rb');
			while(<$fin>){
				my @line = split(/\s/, $_);
				if(exists $GWAS{$line[0]}{$line[1]}{"p"}){
					$GWAS{$line[0]}{$line[1]}{"rsID"}=$line[2];
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

	open(SNP, ">$outSNPs");
	print SNP "chr\tbp\tref\talt\trsID\tp\n";
	foreach my $chr (sort {$a<=>$b} keys %GWAS){
		foreach my $pos (sort {$a<=>$b} keys %{$GWAS{$chr}}){
			print SNP join("\t", ($chr, $pos, $GWAS{$chr}{$pos}{"ref"}, $GWAS{$chr}{$pos}{"alt"}, $GWAS{$chr}{$pos}{"rsID"}, $GWAS{$chr}{$pos}{"p"})), "\n";
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
	}

	my $dbSNP = "/media/sf_SAMSUNG/dbSNP/snp146_pos_allele.txt"; #local #local
#webserver 	my $dbSNP = "/data/dbSNP/snp146_pos_allele.txt";
 	open(DB, "$dbSNP") or die "Cannot opne $dbSNP\n";
	open(SNP, ">$outSNPs");
	print SNP "chr\tbp\tref\talt\trsID\tp\n";
	while(<DB>){
		my @line = split(/\s/, $_);
		if(exists $GWAS{$line[3]}){
			if(defined $refcol && defined $altcol){
				print SNP "$line[1]\t$line[2]\t$line[4]\t$line[5]\t$line[3]\t", $GWAS{$line[3]}{"p"}, "\n" if(($line[4] eq $GWAS{$line[3]}{"ref"} && $line[5] eq $GWAS{$line[3]}{"alt"}) || ($line[5] eq $GWAS{$line[3]}{"ref"} && $line[4] eq $GWAS{$line[3]}{"alt"}));
			}elsif(defined $refcol){
				print SNP "$line[1]\t$line[2]\t$line[4]\t$line[5]\t$line[3]\t", $GWAS{$line[3]}{"p"}, "\n" if($line[5] eq $GWAS{$line[3]}{"ref"} || $line[4] eq $GWAS{$line[3]}{"ref"});
			}elsif(defined $altcol){
				print SNP "$line[1]\t$line[2]\t$line[4]\t$line[5]\t$line[3]\t", $GWAS{$line[3]}{"p"}, "\n" if($line[5] eq $GWAS{$line[3]}{"alt"} || $line[4] eq $GWAS{$line[3]}{"alt"});
			}else{
				print SNP "$line[1]\t$line[2]\t$line[4]\t$line[5]\t$line[3]\t", $GWAS{$line[3]}{"p"}, "\n";
			}
		}
	}
	close DB;
	close SNP;

}

close GWAS;
delete @rsID{keys %rsID};
delete @GWAS{keys %GWAS};
