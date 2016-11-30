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

die "ERROR: not enought arguments\nUSAGE./gwas_file.pl <filedir> <gwas file format>\n" if(@ARGV<2);

my $filedir = $ARGV[0];
my $gwasfile_format = $ARGV[1];
#my $N = $ARGV[2];
my $gwas = $filedir."input.gwas";

my $outSNPs = $filedir."input.snps";
my $outMAGMA = $filedir."magma.in";

my $dbSNP = "/media/sf_SAMSUNG/dbSNP/RsMerge146.txt"; #local
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
my $pcol;
my $refcol=undef;
my $altcol=undef;

if($gwasfile_format eq "Plain"){
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
}elsif($gwasfile_format eq "PLINK"){
	my @head = split(/\s+/, $head);
	foreach my $i (0..$#head){
		if($head[$i] eq "SNP"){$rsIDcol=$i}
		elsif($head[$i] eq "CHR"){$chrcol=$i}
		elsif($head[$i] eq "BP"){$poscol=$i}
		elsif($head[$i] eq "A1"){$altcol=$i}
		elsif($head[$i] eq "A2"){$refcol=$i}
		elsif($head[$i] eq "P"){$pcol=$i}
	}
}elsif($gwasfile_format eq "GCTA"){
	my @head = split(/\s+/, $head);
	foreach my $i (0..$#head){
		if($head[$i] eq "SNP"){$rsIDcol=$i}
		elsif($head[$i] eq "Chr"){$chrcol=$i}
		elsif($head[$i] eq "bp"){$poscol=$i}
		elsif($head[$i] eq "ReferenceAllele"){$refcol=$i}
		elsif($head[$i] eq "OtherAllele"){$altcol=$i}
		elsif($head[$i] eq "p"){$pcol=$i}
	}
}elsif($gwasfile_format eq "SNPTEST"){
	while($head =~ /^#/){
		$head = <GWAS>;
	}
	my @head = split(/\s+/, $head);
	foreach my $i (0..$#head){
		if($head[$i] eq "rsid"){$rsIDcol=$i}
		elsif($head[$i] eq "chromosome"){$chrcol=$i}
		elsif($head[$i] eq "position"){$poscol=$i}
		elsif($head[$i] eq "alleleB"){$altcol=$i}
		elsif($head[$i] eq "alleleA"){$refcol=$i}
		elsif($head[$i] eq "frequentist_add_pvalue"){$pcol=$i}
	}
}elsif($gwasfile_format eq "METAL"){
	my @head = split(/\s+/, $head);
	foreach my $i (0..$#head){
		if($head[$i] eq "MarkerName"){$rsIDcol=$i}
		#elsif($head[$i] eq "Chr"){$chrcol=$i}
		#elsif($head[$i] eq "bp"){$poscol=$i}
		elsif($head[$i] eq "Allele1"){$altcol=$i}
		elsif($head[$i] eq "Allele2"){$refcol=$i}
		elsif($head[$i] eq "P-value"){$pcol=$i}
		## need to get chr pos befor start analyses
		## update rsID
		## extract chr and pos from db146 (match rsID + ref/alt)
	}
}

my %GWAS;
print "chr: $chrcol, pos: $poscol, rsID: $rsIDcol, ref: $refcol, alt: $altcol, p: $pcol\n";

if(defined $chrcol && defined $poscol){
	while(<GWAS>){
		next if(/^#/);
		my @line = split(/\s/, $_);
		$line[$chrcol] =~ s/chr//;
		$line[$chrcol]=23 if($line[$chrcol]=~/X|x/);
		$line[$pcol] = 1e-308 if($line[$pcol]<1e-308);
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
			my $file = "/media/sf_SAMSUNG/1KG/Phase3/EUR/EUR.chr$chr.frq.gz"; #local
			#webserver my $file = "/data/1KG/Phase3/EUR/EUR.chr$chr.frq.gz";
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

	my $dbSNP = "/media/sf_SAMSUNG/dbSNP/snp146_pos_allele.txt"; #local
	#webserver my $dbSNP = "/data/dbSNP/snp146_pos_allele.txt";
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

=begin
system "awk 'NR>=2' $outSNPs | cut -f 5,6 | sort -u -k 1,1 > $outMAGMA";
system "magma --bfile /media/sf_SAMSUNG/1KG/Phase3/EUR/1KG.EUR.phase3.SNPs.common --pval $outMAGMA N=$N --gene-annot /media/sf_Documents/VU/Data/MAGMA/ENSG.w0.genes.annot --out $filedir"."magma"; #local
#webserver system "magma --bfile /data/1KG/Phase3/EUR/1KG.EUR.phase3.SNPs.common --pval $outMAGMA N=$N --gene-annot /data/MAGMA/ENSG.w0.genes.annot --out $filedir"."magma"; #local

system "rm $filedir*.raw $filedir*.log";
system "sed 's/ \\+/\\t/g' ".$filedir."magma.genes.out >$filedir"."temp.txt";
system "mv ".$filedir."temp.txt ".$filedir."magma.genes.out";
