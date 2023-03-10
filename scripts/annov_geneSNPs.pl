#!/usr/bin/perl
#refSeq version commented out
#ensemble version
use strict;
use warnings;

die "ERROR: not enough arguments\nUSAGE: ./annov_geneSNPs.pl <input> <output>" if(@ARGV<2);

my $in = $ARGV[0];
my $out = $ARGV[1];

my %exonic;
$in =~ /(.*)\.variant_function/;
my $exonfile = $1.".exonic_variant_function";
open(IN, "$exonfile") or die "Cannot open $exonfile\n";
while(<IN>){
	chomp $_;
	my @line = split(/\t/, $_);
	$line[3] =~ s/x/23/i;
	my $id = join(":", ($line[3], $line[4], sort($line[6], $line[7])));
	# $exonic{$id}{"func"} = $line[1];
	my @g = split(/,/, $line[2]);
	foreach my $i (0..$#g){
		$g[$i] =~ /(ENSG\d+):.+(exon\d+):/;
		my $gene = $1;
		my $exon = $2;
		$exonic{$id}{$gene}{"exon"}=$exon;
		$exonic{$id}{$gene}{"func"}=$line[1]
	}
}
close IN;

open(IN, "$in") or die "Cannot open $in\n";
open(OUT, ">$out") or die "Cannot open $out\n";
print OUT "uniqID\tgene\tannot\tdist\texonic_func\texon\n";
while(<IN>){
	my @line = split(/\s+/, $_);
	$line[2] =~ s/x/23/i;
	my $id = join(":", ($line[2], $line[3], sort($line[5], $line[6])));

	if($line[1] =~ /;/){
		$line[1] =~ s/\(.+\)//g;
		my @genes = split(/;/, $line[1]);
		my @annot = split(/;/, $line[0]);
		foreach my $i (0..$#genes){
			if($genes[$i] =~ /,/){
				my @g = split(/,/, $genes[$i]);
				foreach my $j (@g){
					print OUT "$id\t$j\t$annot[$i]\t0\t";
					if(exists $exonic{$id}{$j}){print OUT $exonic{$id}{$j}{"func"}, "\t", $exonic{$id}{$j}{"exon"}, "\n"}
					else{print OUT "NA\tNA\n"}
				}
			}else{
				print OUT "$id\t$genes[$i]\t$annot[$i]\t0\t";
				if(exists $exonic{$id}{$genes[$i]}){print OUT $exonic{$id}{$genes[$i]}{"func"}, "\t", $exonic{$id}{$genes[$i]}{"exon"}, "\n"}
				else{print OUT "NA\tNA\n"}
			}
		}
	}elsif($line[0] eq "intergenic"){
		my @genes = split(/,/, $line[1]);
		foreach my $g (@genes){
			next if($g =~ /NONE/);
			$g =~ /(.+)\(dist=(\d+)\)/;
			my $gene = $1;
			my $dist = $2;
			print OUT "$id\t$gene\t$line[0]\t$dist\t";
			if(exists $exonic{$id}{$gene}){print OUT $exonic{$id}{$gene}{"func"}, "\t", $exonic{$id}{$gene}{"exon"}, "\n"}
			else{print OUT "NA\tNA\n"}
		}
	}elsif($line[1] =~ /,/){
		$line[1] =~ s/\(.+\)//g;
		my @genes = split(/,/, $line[1]);
		foreach my $g (@genes){
			print OUT "$id\t$g\t$line[0]\t0\t";
			if(exists $exonic{$id}{$g}){print OUT $exonic{$id}{$g}{"func"}, "\t", $exonic{$id}{$g}{"exon"}, "\n"}
			else{print OUT "NA\tNA\n"}
		}
	}else{
		$line[1] =~ s/\(.+\)//;
		print OUT "$id\t$line[1]\t$line[0]\t0\t";
		if(exists $exonic{$id}{$line[1]}){print OUT $exonic{$id}{$line[1]}{"func"}, "\t", $exonic{$id}{$line[1]}{"exon"}, "\n"}
		else{print OUT "NA\tNA\n"}
	}
}
close IN;
close OUT;
