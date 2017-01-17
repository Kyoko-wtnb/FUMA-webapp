#!/usr/bin/perl
### input was input.gwas
### modified to input.snps which is already sorted and with fixed colmns
### No need to get input file format here, it's done in gwas_file.pl
### gwas_file.pl has to be executed before this script
### 7 Nov 2016

use strict;
use warnings;
use File::Basename;
use Config::Simple;

#die "ERROR: not enough arguments\nUSAGE: ./getSNPs.pl <file dir> <pop> <leadPth> <KGSNPs> <gwasP> <MAF> <r2> <gwas file format> <leadSNPs> <add leadSNP> <regions> <mergeDist> <exMHC> <extMHC>\n" if (@ARGV < 13);
die "ERROR: not enough arguments\nUSAGE: ./getSNPs.pl <file dir>\n" if (@ARGV < 1);

my $filedir = $ARGV[0];
$filedir .= '/' unless($filedir =~ /\/$/);
my $params = new Config::Simple($filedir.'/params.config');

my $dir = dirname(__FILE__);
my $cfg = new Config::Simple($dir.'/app.config');

my $orcol = $params->param('inputfiles.orcol');
my $secol = $params->param('inputfiles.secol');
$orcol = undef if($orcol eq "NA");
$secol = undef if($secol eq "NA");

my $pop = $params->param('params.pop');
my $leadP = $params->param('params.leadP');
my $KGSNPs = $params->param('params.Incl1KGSNPs'); #1 to add, 0 to not add
my $gwasP = $params->param('params.gwasP');
my $maf = $params->param('params.MAF');
my $r2 = $params->param('params.r2');

my $leadSNPs = $params->param('inputfiles.leadSNPsfile');
if($leadSNPs eq "NA"){$leadSNPs=0}
else{$leadSNPs=1}
my $addleadSNPs = $params->param('inputfiles.addleadSNPs'); #1 to add, 0 to not add
my $regions = $params->param('inputfiles.regionsfile');
if($regions eq "NA"){$regions=0}
else{$regions=1}
my $mergeDist = $params->param('params.mergeDist');
my $MHC = $params->param('params.exMHC'); # 1 to exclude, 0 to not
my $extMHC = $params->param('params.extMHC');
my $MHCstart = 29624758;
my $MHCend = 33160276;
unless($extMHC eq "NA"){
	my @temp = split(/-/, $extMHC);
	$MHCstart = $temp[0];
	$MHCend = $temp[1];
}
# my $Xchr = $ARGV[13];

## Input files
## $leadSNPs and $regions are file name only when file is provided
my $gwas = $filedir."input.snps";
if($leadSNPs){$leadSNPs = $filedir.$cfg->param('inputfiles.leadSNP')}
else{$leadSNPs = 0}
if($regions){$regions = $filedir.$cfg->param('inputfiles.reagions')}
else{$regions = 0}

=begin
## commented out since rsID is update for reference panel and SNPs are matched by chr:pos
## No need to update here since it's done in gwas_file.pl
my $rsIDfile="/media/sf_SAMSUNG/dbSNP/RsMerge146.txt";
my %rsID;
open(RS, "$rsIDfile");
while(<RS>){
	my @line = split(/\s/, $_);
	$rsID{$line[0]}=$line[1];
}
close RS;
=cut
# column matched with the output file from gwas_file.pl
my $rsIDcol=4;
my $chrcol=0;
my $poscol=1;
my $pcol=5;
my $refcol=2;
my $altcol=3;

open(GWAS, "$gwas") or die "Cannot open $gwas\n";
my $head = <GWAS>;
close GWAS;
my @head = split(/\s+/, $head);
foreach my $i (0..$#head){
	if($head[$i] eq "or"){$orcol=$i}
	elsif($head[$i] eq "se"){$secol=$i}
}

my $out1 = $filedir."ld.txt";
my $out2 = $filedir."snps.txt";
my $out3 = $filedir."annot.txt";
my $annovin = $filedir."annov.input";
open(LD, ">$out1") or die "Cannot open $out1\n";
print LD "SNP1\tSNP2\tr2\n";
close LD;
open(OUT, ">$out2");
my $outhead = "uniqID\trsID\tchr\tpos\tref\talt\tMAF\tgwasP";
$outhead .= "\tor" if(defined $orcol);
$outhead .= "\tse" if(defined $secol);
$outhead .= "\n";
print OUT $outhead;
close OUT;
open(ANNOT, ">$out3");
print ANNOT "uniqID\tCADD\tRDB";

my $chr15file = $cfg->param('data.chr15');
my @chr15 = `ls $chr15file/*.bed.gz`;
chomp @chr15;
foreach(@chr15){
	/(E\d+)_/;
	my $c = $1;
	print ANNOT "\t$c";
}
print ANNOT "\n";
close ANNOT;


######
# reagion file
# 1: chr, 2: start, 3: end
######

my %regions;
#$regions{$chr}{$ID}{"start/end"}
if($regions){
	print "Reading regions file...\n";
	open(REG, "$regions") or die "Cannot opne $regions\n";
	<REG>;
	my $rid = 0;
	while(<REG>){
		my @line = split(/\s/, $_);
		$regions{$line[0]}{$rid}{"start"}=$line[1];
		$regions{$line[0]}{$rid}{"end"}=$line[2];
		$rid++;
	}
	close REG;
}

#print "regions : ", scalar(keys %regions), "\n";

my $refgenome = $cfg->param('data.refgenome');

### Process per chr because of memory exeed for big GWAS data
foreach my $chr (1..23){
	#next unless($chr==1);
	# last if($chr==23 && $Xchr==0);
	my %GWAS;
	##$GWAS{$chr}{$pos}{$uniqID}{p/ref/alt/rsID}
	my %plead;
	##$plead{$chr}{$uniqID}=$p
	my $dist = 1000000; #to avoid tabix of long rage

	### skip if regions is defined and there is no predefined regions in the chr
	if($regions){
		next unless(exists $regions{$chr});
	}

	print "Start chr $chr: \n";
	print "Reading GWAS file ...\n";

	### Store SNPs into hash (%GWAS)
	#my $chr = 0;

	## variables for %plead
	my $chr_pre = 0;
	my $pos_pre = 0;
	my $ld = 0;
	## variables for regions
	my $rid_cur=0;
	if($regions){
		my @temp = keys %{$regions{$chr}};
		$rid_cur = $temp[0];
	}

	open(GWAS, "$gwas") or die "Cannot open $gwas\n";
	<GWAS>;
	while(<GWAS>){
		next if(/^#/); ##for SNPTEST file
		chomp $_;
		my @line = split(/\s+/, $_);
		next unless($line[$chrcol]==$chr);

		last if($line[$chrcol] > $chr); ## need to commnet out if the input file is not ordered

		## exclude MHC region id necessally
		next if($MHC==1 && $line[$chrcol]==6 && $line[$poscol]>=$MHCstart && $line[$poscol]<=$MHCend);
		my $id = join(":", ($line[$chrcol], $line[$poscol], sort($line[$refcol], $line[$altcol]))); ## uniqID
		##$line[0] = $rsID{$line[0]} if(exists $rsID{$line[0]});
		my $pos = $line[$poscol];
		$GWAS{$line[$chrcol]}{$line[$poscol]}{$id}{"p"}=$line[$pcol];
		$GWAS{$line[$chrcol]}{$line[$poscol]}{$id}{"ref"}=$line[$refcol];
		$GWAS{$line[$chrcol]}{$line[$poscol]}{$id}{"alt"}=$line[$altcol];
		$GWAS{$line[$chrcol]}{$line[$poscol]}{$id}{"rsID"}=$line[$rsIDcol];
		$GWAS{$line[$chrcol]}{$line[$poscol]}{$id}{"or"}=$line[$orcol] if(defined $orcol);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{$id}{"se"}=$line[$secol] if(defined $secol);

=begin
## Older version with input.gwas as input
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"p"}=$line[$pcol];
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"rsID"}=$line[$rsIDcol] if($getRs==0);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"ref"}=$line[$refcol] if($getRef==0);
		$GWAS{$line[$chrcol]}{$line[$poscol]}{"alt"}=$line[$altcol] if($getAlt==0);
=cut

		# die "P:", $GWAS{$chr}{$line[$poscol]}{"p"} if($line[$chrcol]==1 && $line[$poscol]==197368281);
		#die "Check rs6656401: ".$GWAS{$line[$chrcol]}{$line[$poscol]}{"p"}." pos ".$line[$poscol]."\n" if($line[$rsIDcol]eq"rs6656401");

## commented out 04-11-2016 since it only works for sorted input file
## Uncommented 07-11-2016 since input.snps is sorted and less loop to do this here
## Added regions options 07-11-2016
		if($regions){
			$rid_cur++ if($pos>$regions{$chr}{$rid_cur}{"end"});
			last unless(exists $regions{$chr}{$rid_cur}{"start"});
			next unless($pos>=$regions{$chr}{$rid_cur}{"start"} && $pos<=$regions{$chr}{$rid_cur}{"end"});
			if($line[$pcol] <= $leadP){
				if($chr==$chr_pre && $pos-$pos_pre <= $dist){
					if($chr==6 && $pos>31000000 && $pos<=33764158 && $plead{$chr}{$ld}{"end"}-$plead{$chr}{$ld}{"start"}>=100000){
						$ld++;
						$plead{$chr}{$ld}{"start"}=$pos;
						$plead{$chr}{$ld}{"end"}=$pos;
					}elsif($plead{$chr}{$ld}{"end"}-$plead{$chr}{$ld}{"start"}>=100000){
						$ld++;
						$plead{$chr}{$ld}{"start"}=$pos;
						$plead{$chr}{$ld}{"end"}=$pos;
					}else{$plead{$chr}{$ld}{"end"}=$pos;}
				}else{
					$ld++;
					$plead{$chr}{$ld}{"start"}=$pos;
					$plead{$chr}{$ld}{"end"}=$pos;
				}
				$chr_pre = $chr;
				$pos_pre = $pos;
			}
		}else{
			if($line[$pcol] <= $leadP){
				if($line[$chrcol]==$chr_pre && $line[$poscol]-$pos_pre <= $dist){
					if($line[$chrcol]==6 && $line[$poscol]>31000000 && $line[$poscol]<=33764158 && $plead{$line[$chrcol]}{$ld}{"end"}-$plead{$line[$chrcol]}{$ld}{"start"}>=100000){
						$ld++;
						$plead{$line[$chrcol]}{$ld}{"start"}=$line[$poscol];
						$plead{$line[$chrcol]}{$ld}{"end"}=$line[$poscol];
					}elsif($plead{$line[$chrcol]}{$ld}{"end"}-$plead{$line[$chrcol]}{$ld}{"start"}>=100000){
						$ld++;
						$plead{$line[$chrcol]}{$ld}{"start"}=$line[$poscol];
						$plead{$line[$chrcol]}{$ld}{"end"}=$line[$poscol];
					}else{$plead{$line[$chrcol]}{$ld}{"end"}=$line[$poscol];}
				}else{
					$ld++;
					$plead{$line[$chrcol]}{$ld}{"start"}=$line[$poscol];
					$plead{$line[$chrcol]}{$ld}{"end"}=$line[$poscol];
				}
				$chr_pre = $line[$chrcol];
				$pos_pre = $line[$poscol];
			}
		}

	}
	close GWAS;
	print "GWAS: ", scalar(keys %{$GWAS{$chr}}), "\n";
	print "plead: ", scalar(keys %{$plead{$chr}}), "\n";
	#delete @rsID{keys %rsID};

	######
	# leadSNPs file
	# 1: rsID, 2: chr, 3: pos
	######
	my %leadSNPs;
	#$leadSNPs{$chr}{$rsID}=$pos
	if($leadSNPs){
		print "Reading leadSNPs file...\n";
		open(LOCI, "$leadSNPs") or die "Cannot opne $leadSNPs\n";
		<LOCI>;
		while(<LOCI>){
			my @line = split(/\s/, $_);
			next unless(exists $GWAS{$line[1]});
			next if($MHC==1 && $line[1]==6 && $line[2]>=$MHCstart && $line[2]<=$MHCend);
			$leadSNPs{$line[1]}{$line[0]}=$line[2];
		}
		close LOCI;
	}else{print "There is no pre defined lead SNPs\n"}

	my %riskSNPs;
	#$riskSNPs{$chr}{$pos}{$uniqID}{"MAF/rsID/gwasP"}
	#my %checkedlead;
	#$checkedlead{$uniqID}=1
	# my $out1 = $filedir."ld.txt";
	# my $out2 = $filedir."snps.txt";
	open(LD, ">>$out1") or die "Cannot open $out1\n";
	# print LD "SNP1\tSNP2\tr2\n";
	#if($regions){
	#	foreach my $rid (sort {$a<=>$b} keys %regions){
	#		## write script
	#	}
	#}else{

	my $ldfile = "$refgenome/".$pop."/".$pop.".chr$chr.ld.gz";
	my $maffile = "$refgenome/".$pop."/chr$chr.data.txt.gz";

 	if(exists $leadSNPs{$chr}){
		#print "Checking input lead SNPs\n";
		my $count = 0;
		foreach my $ls (keys %{$leadSNPs{$chr}}){
			# print "$ls\n";
			$count++;
			my $pos=$leadSNPs{$chr}{$ls};
      # print "LD tabix $chr:$pos-$pos\n";

			my $start = $pos - 1000000;
			my $end = $pos + 1000000;
      # print "MAF tabix\n";
			my @MAF = split(/\n/, `tabix $maffile $chr:$start-$end`);
			## 0.chr 1.bp 2.ref 3.alt 4.rsID 5.MAF 6.uniqID 7.CADD 8.RDB 9-52.GTEx 53-179.Chr15

			# print scalar(@MAF), "\n";
			my %maf; #$maf{$rsID}{"uniqID/pos/MAF"}
			foreach my $m (@MAF){
				my @line = split(/\s/, $m);
				next if($MHC==1 && $line[0]==6 && $line[1]>=$MHCstart && $line[1]<=$MHCend);
		    if(exists $GWAS{$line[0]}{$line[1]}{$line[6]}{"p"}){
		      next if($GWAS{$line[0]}{$line[1]}{$line[6]}{"p"}>$gwasP);
		    }
		    next if($line[5]<$maf);
				$maf{$line[4]}{"uniqID"}=$line[6];
				$maf{$line[4]}{"pos"}=$line[1];
				$maf{$line[4]}{"MAF"}=$line[5];
				$maf{$line[4]}{"ref"}=$line[2];
				$maf{$line[4]}{"alt"}=$line[3];
				$maf{$line[4]}{"annot"}=join("\t", (@line[7..8], @line[53..$#line]));
=begin
				if(exists $GWAS{$chr}{$line[1]}{"p"}){
					$GWAS{$line[0]}{$line[1]}{"rsID"} = $line[4] if($getRs);
					$GWAS{$line[0]}{$line[1]}{"ref"} = $line[2] if($getRef);
					$GWAS{$line[0]}{$line[1]}{"alt"} = $line[3] if($getAlt);
					# $GWAS{$line[0]}{$line[1]}{"uniqID"} = $line[3];
				}
=cut

			}
			#print "MAF: ", scalar(keys %maf), "\n";
			delete @MAF[0..$#MAF];
			next unless(exists $maf{$ls}{"pos"});
			unless(exists $riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}){
				#$riskSNPs{$chr}{$maf{$ls}{"uniqID"}}{"pos"}=$maf{$ls}{"pos"};

				$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"MAF"}=$maf{$ls}{"MAF"};
				$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"rsID"}=$ls;
				#$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"uniqID"}=$maf{$ls}{"uniqID"};
				$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"gwasP"}=$GWAS{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"p"};
				$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"ref"}=$GWAS{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"ref"};
				$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"alt"}=$GWAS{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"alt"};
				$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"or"}=$GWAS{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"or"} if(defined $orcol);
				$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"se"}=$GWAS{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"se"} if(defined $secol);
				$riskSNPs{$chr}{$maf{$ls}{"pos"}}{$maf{$ls}{"uniqID"}}{"annot"}=$maf{$ls}{"annot"};
				print LD $maf{$ls}{"uniqID"}, "\t",$maf{$ls}{"uniqID"},"\t1\n";
			}
				#$checkedlead{$maf{$ls}{"uniqID"}}=1;
			my @LD = split(/\n/, `tabix $ldfile $chr:$pos-$pos`);
			foreach my $s (@LD){
				my @line = split(/\s/, $s);
				next unless($line[2] eq $ls);
	      next if($line[4]<$r2);
				if(exists $maf{$line[3]}){
					unless(exists $riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}){
						if(exists $GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}){
							if($GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"} > $leadP){
								#$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"pos"}=$maf{$line[3]}{"pos"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"MAF"}=$maf{$line[3]}{"MAF"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"rsID"}=$line[3];
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"gwasP"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"};
								#$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{"uniqID"}=$maf{$line[3]}{"uniqID"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"ref"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"ref"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"alt"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"alt"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"or"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"or"} if(defined $orcol);
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"se"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"se"} if(defined $secol);
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"annot"}=$maf{$line[3]}{"annot"};
							}
						}else{
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"MAF"}=$maf{$line[3]}{"MAF"};
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"rsID"}=$line[3];
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"gwasP"}="NA";
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"ref"}=$maf{$line[3]}{"ref"};
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"alt"}=$maf{$line[3]}{"alt"};
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"or"}="NA" if(defined $orcol);
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"se"}="NA" if(defined $secol);
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"annot"}=$maf{$line[3]}{"annot"};
						}
					}
					if($KGSNPs==0){
						if(exists $GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}){
							if($GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}<=$gwasP){
								print LD $maf{$ls}{"uniqID"},"\t", $maf{$line[3]}{"uniqID"},"\t$line[4]\n";
							}
						}
					}else{
						if(exists $GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}){
							if($GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}<=$gwasP){
								print LD $maf{$ls}{"uniqID"},"\t", $maf{$line[3]}{"uniqID"},"\t$line[4]\n";
							}
						}else{
							print LD $maf{$ls}{"uniqID"},"\t", $maf{$line[3]}{"uniqID"},"\t$line[4]\n";
						}
					}
				}
			}
		}
		print "Checked $count input lead SNPs\n";
	}
	if($addleadSNPs!=0){
		print "Identifying potential lead SNPs...\n";
		# unless(exists $plead{$chr}){
		# 	print "No additional lead SNP was found\n";
		# 	next;
		# }
### break
		my $count=0;
		foreach my $ld (sort {$a<=>$b} keys %{$plead{$chr}}){
			my $start = $plead{$chr}{$ld}{"start"};
			my $end = $plead{$chr}{$ld}{"end"};

			my $start2 = $start - 1000000;
			$start2 = 0 if($start2<0);
			my $end2 = $end + 1000000;
			my @MAF = split(/\n/, `tabix $maffile $chr:$start2-$end2`);
			# print "$chr:$start2-$end2\n";
			# print scalar @MAF, "\n";
			my %maf; #$maf{$rsID}{"uniqID/pos/MAF"}
			foreach my $m (@MAF){
				my @line = split(/\s/, $m);
				next if($MHC==1 && $line[0]==6 && $line[1]>=$MHCstart && $line[1]<=$MHCend);
				# die "$line[0] $line[1] exists" if($line[2]eq"rs4844600");
				# die "$line[0] $line[1] exists" if(exists $GWAS{1}{207679307}{"p"});

				if(exists $GWAS{$line[0]}{$line[1]}{$line[6]}{"p"}){
					next if($GWAS{$line[0]}{$line[1]}{$line[6]}{"p"}>$gwasP);
				}
				next if($line[5]<$maf);
				$maf{$line[4]}{"uniqID"}=$line[6];
				$maf{$line[4]}{"pos"}=$line[1];
				$maf{$line[4]}{"MAF"}=$line[5];
				$maf{$line[4]}{"ref"}=$line[2];
				$maf{$line[4]}{"alt"}=$line[3];
				$maf{$line[4]}{"annot"}=join("\t", (@line[7..8], @line[53..$#line]));
				# if($line[1]==207850539){print "P\t",$GWAS{$maf{$line[2]}{"pos"}}{"p"}, "\n"; die;}
=begin
				if(exists $GWAS{$line[0]}{$line[1]}){
					$GWAS{$line[0]}{$line[1]}{"rsID"} = $line[4] if($getRs);
					$GWAS{$line[0]}{$line[1]}{"ref"} = $line[2] if($getRef);
					$GWAS{$line[0]}{$line[1]}{"alt"} = $line[3] if($getAlt);
					# $GWAS{$line[0]}{$line[1]}{"uniqID"} = $line[3];
				}
=cut
			}
			delete @MAF[0..$#MAF];
			#print "MAF: ", scalar(keys %maf), "\n";
			my @LD = split(/\n/, `tabix $ldfile $chr:$start-$end`);
			#print "LD: ", scalar(@LD), "\n";
			foreach my $s (@LD){
				my @line = split(/\s/, $s);
				# if($line[1] == 207850539){print $GWAS{$maf{$line[2]}{"pos"}}{"p"}, "\n"}

				# die "$line[0]\t$line[1]\t$line[2]\t$line[3]\n" unless(exists $maf{$line[2]} && $GWAS{$chr}{$line[1]}{"p"}<=$leadP);
				next unless(exists $maf{$line[2]});
				next unless(exists $GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"p"});
				# die "$chr: ", $maf{$line[2]}{"pos"}, ": ", keys %{$GWAS{$chr}{$maf{$line[2]}{"pos"}}},"\n" unless(exists $GWAS{$chr}{$maf{$line[2]}{"pos"}}{"p"});

				next unless($GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"p"}<=$leadP);
				unless(exists $riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}){
					#$riskSNPs{$chr}{$maf{$line[2]}{"uniqID"}}{"pos"}=$maf{$line[2]}{"pos"};
					$riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"MAF"}=$maf{$line[2]}{"MAF"};
					$riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"rsID"}=$line[2];
					$riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"gwasP"}=$GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"p"};
					$riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"ref"}=$GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"ref"};
					$riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"alt"}=$GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"alt"};
					$riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"or"}=$GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"or"} if(defined $orcol);
					$riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"se"}=$GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"se"} if(defined $secol);
					$riskSNPs{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"annot"}=$maf{$line[2]}{"annot"};
					print LD $maf{$line[2]}{"uniqID"}, "\t",$maf{$line[2]}{"uniqID"},"\t1\n";
					$count++;
				}
				next if($line[4]<$r2);
				if(exists $maf{$line[3]}){
					unless(exists $riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}){
						if(exists $GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}){
							if($GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"} > $leadP){
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"MAF"}=$maf{$line[3]}{"MAF"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"rsID"}=$line[3];
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"gwasP"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"};
								#$riskSNPs{$chr}{$maf{$line[3]}{"uniqID"}}{"pos"}=$maf{$line[3]}{"pos"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"ref"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"ref"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"alt"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"alt"};
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"or"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"or"} if(defined $orcol);
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"se"}=$GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"se"} if(defined $secol);
								$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"annot"}=$maf{$line[3]}{"annot"};
							}
						}else{
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"MAF"}=$maf{$line[3]}{"MAF"};
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"rsID"}=$line[3];
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"gwasP"}="NA";
							#$riskSNPs{$chr}{$maf{$line[3]}{"uniqID"}}{"pos"}=$maf{$line[3]}{"pos"};
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"ref"}=$maf{$line[3]}{"ref"};
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"alt"}=$maf{$line[3]}{"alt"};
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"or"}="NA" if(defined $orcol);
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"se"}="NA" if(defined $secol);
							$riskSNPs{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"annot"}=$maf{$line[3]}{"annot"};
						}
					}
					# if($KGSNPs==0){
					# 	if(exists $GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}){
					# 		if($GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}<=$gwasP && $GWAS{$chr}{$maf{$line[3]}{"pos"}}{"p"} >= $GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"p"}){
					# 			print LD $maf{$line[2]}{"uniqID"},"\t", $maf{$line[3]}{"uniqID"},"\t$line[4]\n";
					# 		}
					# 	}
					# }else{
						if(exists $GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}){
							if($GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"}<=$gwasP && $GWAS{$chr}{$maf{$line[3]}{"pos"}}{$maf{$line[3]}{"uniqID"}}{"p"} >= $GWAS{$chr}{$maf{$line[2]}{"pos"}}{$maf{$line[2]}{"uniqID"}}{"p"}){
								print LD $maf{$line[2]}{"uniqID"},"\t", $maf{$line[3]}{"uniqID"},"\t$line[4]\n";
							}
						}else{
							print LD $maf{$line[2]}{"uniqID"},"\t", $maf{$line[3]}{"uniqID"},"\t$line[4]\n";
						}
					# }
				}
			}
		}
		print "Identified $count SNPs\n";
	}

	close LD;

	#commented out annovar input file output
	#this will be created in leadSNP.R
	# my $annovin = $filedir."annov.input";

	##$riskSNPs{$chr}{$uniqID}{"pos/MAF/rsID/gwasP"}
	open(OUT, ">>$out2") or die "Cannot opne $out2\n";
	open(ANNOT, ">>$out3") or die "Cannot open $out3\n";
	# print OUT "uniqID\trsID\tchr\tpos\tMAF\tgwasP\n";
	#open(OUT2, ">$annovin") or die "Cannot opne $annovin\n";
	# foreach my $chr (sort {$a<=>$b} keys %riskSNPs){
	my $rid = 0;
	if($regions){
		my @temp = keys %{$regions{$chr}};
		$rid = $temp[0];
	}
		foreach my $pos (sort {$a<=>$b} keys %{$riskSNPs{$chr}}){
			my ($id) = keys %{$riskSNPs{$chr}{$pos}};
			if($regions){
				last unless(exists $regions{$chr}{$rid});
				$rid++ if($pos>$regions{$chr}{$rid}{"end"});
				last unless(exists $regions{$chr}{$rid}{"start"});
				next unless($pos>=$regions{$chr}{$rid}{"start"} && $pos<=$regions{$chr}{$rid}{"end"});
			}
			if($KGSNPs==0){
				if(exists $GWAS{$chr}{$pos}{$id}{"p"}){
					if($GWAS{$chr}{$pos}{$id}{"p"} <= $gwasP){
						print OUT join("\t", ($id, $riskSNPs{$chr}{$pos}{$id}{"rsID"}, $chr, $pos, $riskSNPs{$chr}{$pos}{$id}{"ref"}, $riskSNPs{$chr}{$pos}{$id}{"alt"}, $riskSNPs{$chr}{$pos}{$id}{"MAF"}, $riskSNPs{$chr}{$pos}{$id}{"gwasP"}));
						print OUT "\t", $riskSNPs{$chr}{$pos}{$id}{"or"} if(defined $orcol);
						print OUT "\t", $riskSNPs{$chr}{$pos}{$id}{"se"} if(defined $secol);
						print OUT "\n";
						print ANNOT join("\t", $id, $riskSNPs{$chr}{$pos}{$id}{"annot"}), "\n";
						#my @a = split(/:/, $id);
						#print OUT2 join("\t", ($chr, $riskSNPs{$chr}{$id}{"pos"},$riskSNPs{$chr}{$id}{"pos"},$a[2],$a[3])), "\n";
					}
				}
			}else{
				if(exists $GWAS{$chr}{$pos}{$id}{"p"}){
					if($GWAS{$chr}{$pos}{$id}{"p"} <= $gwasP){
						print OUT join("\t", ($id, $riskSNPs{$chr}{$pos}{$id}{"rsID"}, $chr, $pos, $riskSNPs{$chr}{$pos}{$id}{"ref"}, $riskSNPs{$chr}{$pos}{$id}{"alt"}, $riskSNPs{$chr}{$pos}{$id}{"MAF"}, $riskSNPs{$chr}{$pos}{$id}{"gwasP"}));
						print OUT "\t", $riskSNPs{$chr}{$pos}{$id}{"or"} if(defined $orcol);
						print OUT "\t", $riskSNPs{$chr}{$pos}{$id}{"se"} if(defined $secol);
						print OUT "\n";
						print ANNOT join("\t", $id, $riskSNPs{$chr}{$pos}{$id}{"annot"}), "\n";
						#my @a = split(/:/, $id);
						#print OUT2 join("\t", ($chr, $riskSNPs{$chr}{$id}{"pos"},$riskSNPs{$chr}{$id}{"pos"},$a[2],$a[3])), "\n";
					}
				}else{
					print OUT join("\t", ($id, $riskSNPs{$chr}{$pos}{$id}{"rsID"}, $chr, $pos, $riskSNPs{$chr}{$pos}{$id}{"ref"}, $riskSNPs{$chr}{$pos}{$id}{"alt"}, $riskSNPs{$chr}{$pos}{$id}{"MAF"}, $riskSNPs{$chr}{$pos}{$id}{"gwasP"}));
					print OUT "\t", $riskSNPs{$chr}{$pos}{$id}{"or"} if(defined $orcol);
					print OUT "\t", $riskSNPs{$chr}{$pos}{$id}{"se"} if(defined $secol);
					print OUT "\n";
					print ANNOT join("\t", $id, $riskSNPs{$chr}{$pos}{$id}{"annot"}), "\n";
					#my @a = split(/:/, $id);
					#print OUT2 join("\t", ($chr, $riskSNPs{$chr}{$id}{"pos"},$riskSNPs{$chr}{$id}{"pos"},$a[2],$a[3])), "\n";
				}
			}
		}
	# }
	close OUT;
	close ANNOT;
	#close OUT2;
}

#system "sort -k 3n -k 4n $out2 > $filedir/temp.txt";
#system "mv $filedir/temp.txt $out2";

### Check output
my $Nsnps = `wc -l $out2`;
($Nsnps) = split(/\s/, $Nsnps);
if($Nsnps<2){
	die "No candidate SNP was identified\n";
}

#interval compute
system "Rscript $dir/leadSNP.R $filedir $r2 $gwasP $leadP $maf $mergeDist $leadSNPs";

#annov
my $annovout = $filedir."annov";
my $annov = $cfg->param('annovar.annovdir');
my $humandb = $cfg->param('annovar.humandb');
system "$annov/annotate_variation.pl -out $annovout -build hg19 $annovin $humandb/ -dbtype ensGene";

my $annov1 = $filedir."annov.variant_function";
my $annov2 = $filedir."annov.txt";
system "$dir/annov_geneSNPs.pl $annov1 $annov2";
system "rm $annovin $annovout\.variant_function $annovout\.exonic_variant_function $filedir"."annov*.log";
