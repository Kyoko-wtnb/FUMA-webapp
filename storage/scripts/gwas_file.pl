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
use DBI;

# die "ERROR: not enought arguments\nUSAGE./gwas_file.pl <filedir> <gwas file format>\n" if(@ARGV<2);
die "ERROR: not enought arguments\nUSAGE./gwas_file.pl <filedir>\n" if(@ARGV<1);

my $dir = dirname(__FILE__);
my $cfg = new Config::Simple($dir.'/app.config');

my $start = time;

my $filedir = $ARGV[0];
$filedir .='/' unless($filedir =~ /\/$/);
# my $gwasfile_format = $ARGV[1]; # removed this option 14-12-2106
#my $N = $ARGV[2];

my $gwas = $filedir.$cfg->param('inputfiles.gwas');

my $params = new Config::Simple($filedir.'params.config');

my $outSNPs = $filedir."input.snps";
my $outMAGMA = $filedir."magma.in";

## update column name options 17-01-2017
my $chrcol=$params->param('inputfiles.chrcol');
my $poscol=$params->param('inputfiles.poscol');
my $rsIDcol=$params->param('inputfiles.rsIDcol');
my $pcol=$params->param('inputfiles.pcol');
my $refcol=$params->param('inputfiles.refcol');
my $altcol=$params->param('inputfiles.altcol');
my $orcol=$params->param('inputfiles.orcol');
my $becol=$params->param('inputfiles.becol');
my $secol=$params->param('inputfiles.secol');
my $Ncol=$params->param('params.Ncol');

open(GWAS, "$gwas") or die "Cannot open $gwas\n";
my $head = <GWAS>;

while($head =~ /^#/){
		$head = <GWAS>;
}
close GWAS;
my @head = split(/\s+/, $head);
foreach my $i (0..$#head){
	if(uc($head[$i]) eq uc($rsIDcol) || $head[$i] =~ /^SNP$|^MarkerName$|^rsID$|^snpid$/i){$rsIDcol=$i}
	elsif(uc($head[$i]) eq uc($chrcol) ||$head[$i] =~ /^CHR$|^chromosome$|^chrom$/i){$chrcol=$i}
	elsif(uc($head[$i]) eq uc($poscol) ||$head[$i] =~ /^BP$|^pos$|^position$/i){$poscol=$i}
	elsif(uc($head[$i]) eq uc($altcol) ||$head[$i] =~ /^A1$|^Effect_allele$|^alt$|^allele1$|^alleleB$/i){$altcol=$i}
	elsif(uc($head[$i]) eq uc($refcol) ||$head[$i] =~ /^A2$|^Non_Effect_allele$|^ref$|^allele2$|^alleleA$/i){$refcol=$i}
	elsif(uc($head[$i]) eq uc($pcol) ||$head[$i] =~ /^P$|^pval$|^pvalue$|^p-value$|^p_value$|^frequentist_add_pvalue$/i){$pcol=$i}
	elsif(uc($head[$i]) eq uc($orcol) ||$head[$i] =~ /^OR$/i){$orcol=$i}
	elsif(uc($head[$i]) eq uc($becol) ||$head[$i] =~ /^beta$/i){$becol=$i}
	elsif(uc($head[$i]) eq uc($secol) ||$head[$i] =~ /^SE$/i){$secol=$i}
	# elsif(uc($head[$i]) eq uc($mafcol)){$mafcol=$i}
	elsif(uc($head[$i]) eq uc($Ncol) ||$head[$i] =~ /^N$/i){$Ncol=$i}
}

$chrcol = undef if($chrcol eq "NA");
$poscol = undef if($poscol eq "NA");
$rsIDcol = undef if($rsIDcol eq "NA");
$pcol = undef if($pcol eq "NA");
$refcol = undef if($refcol eq "NA");
$altcol = undef if($altcol eq "NA");
$orcol = undef if($orcol eq "NA");
$becol = undef if($becol eq "NA");
$secol = undef if($secol eq "NA");
$Ncol = undef if($Ncol eq "NA");
if(defined $Ncol && $Ncol eq $params->param('params.Ncol')){
	die "N column name was not found";
}

if(defined $refcol && !defined $altcol){
	$altcol = $refcol;
	$refcol = undef;
}

# print "chrcol:$chrcol\nposcol:$poscol\nrsIDcol:$rsIDcol\npcol:$pcol\nrefcol:$refcol\naltcol:$altcol\norcol:$orcol\nsecol:$secol\n";
# unless(defined $mafcol){
# 	print "MAF columns is not defined\n";
# }

if(!(defined $pcol)){die "P-value column was not found\n";}
elsif(!(defined $chrcol && defined $poscol) && !(defined $rsIDcol)){die "Chromosome, position or rsID column was not found\n";}

## modify params.config orcol, becol and secol
if($params->param("inputfiles.orcol") eq "NA" && defined $orcol){$params->param("inputfiles.orcol", "or")}
if($params->param("inputfiles.becol") eq "NA" && defined $becol){$params->param("inputfiles.becol", "beta")}
if($params->param("inputfiles.secol") eq "NA" && defined $secol){$params->param("inputfiles.secol", "se")}
$params->save();

my %GWAS;
#print "chr: $chrcol, pos: $poscol, rsID: $rsIDcol, ref: $refcol, alt: $altcol, p: $pcol\n";
if(defined $chrcol && defined $poscol && defined $rsIDcol && defined $altcol && defined $refcol){
	my $dbSNPfile = $cfg->param('data.dbSNP');
	my %rsID;
	open(RS, "$dbSNPfile/RsMerge146.txt");
	while(<RS>){
		my @line = split(/\s/, $_);
		$rsID{$line[0]}=$line[1];
	}
	close RS;

	my $outhead = "chr\tbp\tref\talt\trsID\tp";
	$outhead .= "\tor" if(defined $orcol);
	$outhead .= "\tbeta" if(defined $becol);
	$outhead .= "\tse" if(defined $secol);
	$outhead .= "\tN" if(defined $Ncol);
	$outhead .= "\n";
	open(SNP, ">$outSNPs");
	print SNP $outhead;
	open(GWAS, "$gwas") or die "Cannot open $gwas\n";
	<GWAS>;
	while($head =~ /^#/){
			$head = <GWAS>;
	}
	while(<GWAS>){
		next if(/^#/);
		my @line = split(/\s/, $_);
		$line[$rsIDcol] = $rsID{$line[$rsIDcol]} if(exists $rsID{$line[$rsIDcol]});
		$line[$chrcol] =~ s/chr//;
		$line[$chrcol] = 23 if($line[$chrcol]=~/x/i);
		$line[$refcol] = uc($line[$refcol]);
		$line[$altcol] = uc($line[$altcol]);
		print SNP join("\t", ($line[$chrcol], $line[$poscol], $line[$refcol], $line[$altcol], $line[$rsIDcol], $line[$pcol]));
		print SNP "\t", $line[$orcol] if(defined $orcol);
		print SNP "\t", $line[$becol] if(defined $becol);
		print SNP "\t", $line[$secol] if(defined $secol);
		print SNP "\t", $line[$Ncol] if(defined $Ncol);
		print SNP "\n";
	}
	close GWAS;
	close SNP;
	my $temp = $filedir."temp.txt";
	system "sort -k 1n -k 2n $outSNPs > $temp";
	system "mv $temp $outSNPs";
}elsif(defined $chrcol && defined $poscol){
	my $outhead = "chr\tbp\tref\talt\trsID\tp";
	$outhead .= "\tor" if(defined $orcol);
	$outhead .= "\tbeta" if(defined $becol);
	$outhead .= "\tse" if(defined $secol);
	# $outhead .= "\tmaf" if(defined $mafcol);
	$outhead .= "\tN" if(defined $Ncol);
	$outhead .= "\n";
	open(SNP, ">$outSNPs");
	print SNP $outhead;
	close SNP;
	# $chrcol--;
	# $poscol--;
	#system "sort -k $chrcol"."n -k $poscol"."n $filedir"."input.gwas > $filedir"."temp.txt";
	#system "mv $filedir"."temp.txt $filedir"."input.gwas";
	# my $database = "hg19";
	# my $hostname = "genome-mysql.cse.ucsc.edu";
	# my $user="genomep";
	# my $password = "password";
	# my $dsn = "DBI:mysql:database=$database;host=$hostname";
	# my $db = DBI->connect($dsn, $user, $password);
	my $dbSNPfile = $cfg->param('data.dbSNP');
	my $count = 0;
	open(GWAS, "$gwas") or die "Cannot open $gwas\n";
	<GWAS>;
	while($head =~ /^#/){
			$head = <GWAS>;
	}

	while(<GWAS>){
		next if(/^#/);

		my @line = split(/\s/, $_);
		$line[$chrcol] =~ s/chr//;
		$line[$chrcol]=23 if($line[$chrcol]=~/X|x/);
		$line[$pcol] = 1e-308 if($line[$pcol]<1e-308); #avoid NA or inf in R

		$count++;

		$GWAS{$line[$chrcol]}{$line[$poscol]}{"p"}=$line[$pcol];
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"ref"}=uc($line[$refcol]) if(defined $refcol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"alt"}=uc($line[$altcol]) if(defined $altcol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"or"}=$line[$orcol] if(defined $orcol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"be"}=$line[$becol] if(defined $becol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"se"}=$line[$secol] if(defined $secol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"N"}=$line[$Ncol] if(defined $Ncol);
		# if(defined $mafcol){
		# 	$line[$mafcol] = 1-$line[$mafcol] if($line[$mafcol]>0.5);
		# 	$GWAS{$line[$chrcol]}{$line[$poscol]}{"maf"}=$line[$mafcol];
		# }
		# if(defined $rsIDcol){
		# 	# $line[$rsIDcol] = $rsID{$line[$rsIDcol]} if(exists $rsID{$line[$rsIDcol]});
		# 	# $GWAS{$line[$chrcol]}{$line[$poscol]}{"rsID"}=$line[$rsIDcol];
		# }
		if($count>=1000000){
			open(SNP, ">>$outSNPs");
			foreach my $chr (sort {$a<=>$b} keys %GWAS){
				my $min = 0;
				my $max = 0;
				foreach my $pos (sort {$a<=>$b} keys %{$GWAS{$chr}}){
					$min = $pos if($min==0);
					$max = $pos if($max==0);
					if($pos-$min>5000000){
						&Tabix($chr, $min, $max);
						$min=$pos;
						$max=$pos;
					}else{
						$max=$pos;
					}
				}
				&Tabix($chr, $min, $max);
			}
			close SNP;

			delete @GWAS{keys %GWAS};
			$count = 0;
			# foreach my $key (keys %GWAS){
			# 	delete $GWAS{$key};
			# }
		}
	}
	close GWAS;
	open(SNP, ">>$outSNPs");
	foreach my $chr (sort {$a<=>$b} keys %GWAS){
		my $min = 0;
		my $max = 0;
		foreach my $pos (sort {$a<=>$b} keys %{$GWAS{$chr}}){
			$min = $pos if($min==0);
			$max = $pos if($max==0);
			if($pos-$min>5000000){
				&Tabix($chr, $min, $max);
				$min=$pos;
				$max=$pos;
			}else{
				$max=$pos;
			}
		}
		&Tabix($chr, $min, $max);
	}
	close SNP;

	sub Tabix{
		my $chr = $_[0];
		my $min = $_[1];
		my $max = $_[2];

		# my $sth = $db->prepare("SELECT name,chrom,chromEnd,refNCBI,alleles FROM snp146 WHERE chrom='chr".$chr."' AND chromEnd>=$min AND chromEnd<=$max");
		# $sth->execute();

		my $file = $dbSNPfile.'/dbSNP146.chr'.$chr.'.vcf.gz';
		my @tabix = split(/\n/, `tabix $file $chr:$min-$max`);
		#######################
		# 0:chr 1:pos 2:rsID 3:ref 4:alt
		#######################
		# while(my @row = $sth->fetchrow_array()){
		foreach my $t (@tabix){
			my @row = split(/\s/, $t);
			$row[4] =~ s/,$//;
			my @alleles = ($row[3], $row[4]);

			if(exists $GWAS{$chr}{$row[1]}{"p"}){
				if(defined $refcol && defined $altcol){
					if(In($GWAS{$chr}{$row[1]}{"ref"}, \@alleles)==0 && In($GWAS{$chr}{$row[1]}{"alt"}, \@alleles)==0){
						print SNP join("\t", ($chr, $row[1], $GWAS{$chr}{$row[1]}{"ref"}, $GWAS{$chr}{$row[1]}{"alt"}, $row[2], $GWAS{$chr}{$row[1]}{"p"}));
						print SNP "\t", $GWAS{$chr}{$row[1]}{"or"} if(defined $orcol);
						print SNP "\t", $GWAS{$chr}{$row[1]}{"be"} if(defined $becol);
						print SNP "\t", $GWAS{$chr}{$row[1]}{"se"} if(defined $secol);
						print SNP "\t", $GWAS{$chr}{$row[1]}{"N"} if(defined $Ncol);
						print SNP "\n";
					}
				}elsif(defined $altcol){
					if(In($GWAS{$chr}{$row[1]}{"alt"}, \@alleles)==0){
						my $a;
						if($row[3] eq $GWAS{$chr}{$row[1]}{"alt"}){$a=$row[4]}
						else{$a=$row[3]}
						print SNP join("\t", ($chr, $row[1], $a, $GWAS{$chr}{$row[1]}{"alt"}, $row[2], $GWAS{$chr}{$row[1]}{"p"}));
						print SNP "\t", $GWAS{$chr}{$row[1]}{"or"} if(defined $orcol);
						print SNP "\t", $GWAS{$chr}{$row[1]}{"be"} if(defined $becol);
						print SNP "\t", $GWAS{$chr}{$row[1]}{"se"} if(defined $secol);
						print SNP "\t", $GWAS{$chr}{$row[1]}{"N"} if(defined $Ncol);
						print SNP "\n";
					}
				}else{
					print SNP join("\t", ($chr, $row[1], $row[3], $row[4], $row[2], $GWAS{$chr}{$row[1]}{"p"}));
					print SNP "\t", $GWAS{$chr}{$row[1]}{"or"} if(defined $orcol);
					print SNP "\t", $GWAS{$chr}{$row[1]}{"be"} if(defined $becol);
					print SNP "\t", $GWAS{$chr}{$row[1]}{"se"} if(defined $secol);
					print SNP "\t", $GWAS{$chr}{$row[1]}{"N"} if(defined $Ncol);
					print SNP "\n";
				}
			}
		}
	}

	my $temp = $filedir."temp.txt";
	system "sort -k 1n -k 2n $outSNPs > $temp";
	system "mv $temp $outSNPs";

}else{
	print "Either chr or pos is not defined\n";
	system "$dir/gwas_file.py $filedir";
}

# close GWAS;
# delete @rsID{keys %rsID};
# delete @GWAS{keys %GWAS};

sub In{
	my $e = $_[0];
	my $a = $_[1];
	my @a = @$a;
	my $check = 1;
	foreach (@a){
		next unless(defined $_);
		if($e eq $_){
			$check = 0;
			last;
		}
	}
	return $check;
}

print "Execution time: ", time-$start, "\n";
