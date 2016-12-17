#!/usr/bin/perl

###########################################################
# eQTL file has to follow the following structure and tabixable
# chr	pos 	ref	alt(tested)	gene	t/z	p	FDR(if applicable)
###########################################################

use strict;
use warnings;

die "ERROR: not enough arguments\nUSAGE: ./geteQTL.pl <filedir> <tissues> <sigonly> <eqtlP>\n" if(@ARGV <4);

#get input arguments
my $filedir = $ARGV[0];
my $tsall = $ARGV[1];
my $sigonly = $ARGV[2];
my $eqtlP = $ARGV[3];

#files
my $snpfile = $filedir."snps.txt";
my $out = $filedir."eqtl.txt";

#tissues
my @ts;
if($tsall eq "all"){
#local 	my @temp = `ls /media/sf_SAMSUNG/GTEx/Tabix/*.txt.gz`;
<<<<<<< HEAD
	my @temp = `ls /data/QTL/GTEx/*.sig.txt.gz`; #webserver
	chomp @temp;
	foreach my $f (@temp){
#local 		$f =~ /Tabix\/(.+)\.txt\.gz/;
		$f =~ /GTEx\/(.+)\.sig\.txt\.gz/; #webserver
		push @ts, "GTEx_".$1;
=======
	my @temp = `ls /data/QTL/GTEx/*.sig.txt.gz`;
 #webserver	chomp @temp; #webserver
	foreach my $f (@temp){
#local 		$f =~ /Tabix\/(.+)\.txt\.gz/;
		$f =~ /GTEx\/(.+)\.sig\.txt\.gz/;
 #webserver		push @ts, "GTEx_".$1; #webserver
>>>>>>> parent of bd609f4... minor bug fixed
	}
	push @ts, "BloodeQTL_BloodeQTL";
	push @ts, "BIOSQTL_BIOS_eQTL_geneLevel";
}else{
	@ts = split(/:/, $tsall);
}
my %db;
foreach (@ts){
	/(^.+?)_(.+)/;
	my $s = $1;
	my $f = $2.".txt.gz";
	if(exists $db{$s}){
			$db{$s} .=":".$f;
	}else{
		$db{$s} = $f;
	}
}

my $dist=500000;

my %SNPs;
my %Loci;
my $lid = 0;
open(IN, "$snpfile") or die "Cannot opne $snpfile\n";
<IN>;
while(<IN>){
	my @line = split(/\s/, $_);
	if($lid == 0){
		$lid++;
		$Loci{$lid}{"chr"}=$line[2];
		$Loci{$lid}{"start"}=$line[3];
		$Loci{$lid}{"end"}=$line[3];
		$SNPs{$lid}{$line[0]}{"pos"}=$line[3];
	}elsif($Loci{$lid}{"chr"}==$line[2] && $line[3]-$Loci{$lid}{"end"} <= $dist){
		$Loci{$lid}{"end"}=$line[3];
		$SNPs{$lid}{$line[0]}{"pos"}=$line[3];
	}else{
		$lid++;
		$Loci{$lid}{"chr"}=$line[2];
		$Loci{$lid}{"start"}=$line[3];
		$Loci{$lid}{"end"}=$line[3];
		$SNPs{$lid}{$line[0]}{"pos"}=$line[3];
	}
}
close IN;
open(OUT, ">$out") or die "Cannot open $out\n";
print OUT "uniqID\tdb\ttissue\tgene\ttestedAllel\tp\ttz\tFDR\n";

foreach my $s (keys %db){
	my @files = split(/:/, $db{$s});
	if($s eq "GTEx"){
		foreach my $f (@files){
#local 			my $file = "/media/sf_SAMSUNG/GTEx/Tabix/".$f;
			my $file = "/data/QTL/GTEx/".$f;
 #webserver			$f =~ /(.+)\.txt.gz/; #webserver
			my $ts = $1;
			my $f2 = $ts.".sig.txt.gz";
#local 			my $file2 = "/media/sf_SAMSUNG/GTEx/TabixSig/".$f2;
			my $file2 = "/data/QTL/GTEx/".$f2;
 #webserver			foreach my $lid (sort {$a<=>$b} keys %Loci){ #webserver
				my $chr = $Loci{$lid}{"chr"};
				my $start = $Loci{$lid}{"start"};
				my $end = $Loci{$lid}{"end"};
				if($sigonly){
					my @eqtlsig = split(/\n/, `tabix $file2 $chr:$start-$end`);
					my %sig;
					foreach my $l (@eqtlsig){
						my @line = split(/\s/, $l);
						my $id = join(":", ($line[0], $line[1], sort($line[2], $line[3])));
						print OUT "$id\t$s\t$ts\t$line[4]\t$line[3]\t$line[6]\t$line[5]\t$line[7]\n" if(exists $SNPs{$lid}{$id});
					}
				}else{
					my @eqtlsig = split(/\n/, `tabix $file2 $chr:$start-$end`);
					my %sig;
					foreach my $l (@eqtlsig){
						my @line = split(/\s/, $l);
						my $id = join(":", ($line[0], $line[1], sort($line[2], $line[3])));
						$sig{$id}{$line[4]}=$line[7] if(exists $SNPs{$lid}{$id});
					}
					my @eqtl = split(/\n/, `tabix $file $chr:$start-$end`);
					foreach my $l (@eqtl){
						my @line = split(/\s/, $l);
						next if($line[6]>$eqtlP);
						my $id = join(":", ($line[0], $line[1], sort($line[2], $line[3])));
						if(exists $SNPs{$lid}{$id}){
							$line[4] =~ s/(ENSG\d+).\d+/$1/;
							print OUT "$id\t$s\t$ts\t$line[4]\t$line[3]\t$line[6]\t$line[5]\t";
							if(exists $sig{$id}{$line[4]}){print OUT $sig{$id}{$line[4]}, "\n"}
							else{print OUT "NA\n"}
						}
					}
				}
			}
		}
	}else{
		foreach my $f (@files){
			$f =~ /(.+)\.txt.gz/;
			my $ts = $1;
#local 			my $file = "/media/sf_SAMSUNG/".$s."/".$f;
			my $file = "/data/QTL/".$s."/".$f;
 #webserver			foreach my $lid (sort {$a<=>$b} keys %Loci){ #webserver
				my $chr = $Loci{$lid}{"chr"};
				my $start = $Loci{$lid}{"start"};
				my $end = $Loci{$lid}{"end"};
				my @eqtl = split(/\n/, `tabix $file $chr:$start-$end`);
				foreach my $l (@eqtl){
					my @line = split(/\s/, $l);
					if($sigonly){next if($line[7]>0.05)}
					else{next if($line[6]>$eqtlP)}
					my $id = join(":", ($line[0], $line[1], sort($line[2], $line[3])));
					if(exists $SNPs{$lid}{$id}){
						print OUT join("\t", ($id, $s, $ts, $line[4], $line[3], $line[6], $line[5], $line[7])), "\n";
					}
				}
			}
		}
	}

}
close OUT;
