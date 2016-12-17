#!/usr/bin/perl
use strict;
use warnings;
use Vcf;

die "ERROR: not enough arguments\nUSAGE: ./exacext.pl <filedir>\n" if(@ARGV < 1);

my $filedir = $ARGV[0];
#local my $file = "/media/sf_Documents/VU/Data/ExAC0.3/ExAC.r0.3.sites.vep.vcf.gz"; #local
my $file = "/data/ExAC/ExAC.r0.3.sites.vep.vcf.gz";
my $in = $filedir."intervals.txt"; #webserver #webserver
my $out = $filedir."ExAC.txt";

open(IN, "$in") or die "Cannot open $in\n";
my %Loci;
<IN>;
while(<IN>){
	my @line = split(/\s/, $_);
	$Loci{$line[0]}{"chr"} = $line[3];
	$Loci{$line[0]}{"start"} = $line[7];
	$Loci{$line[0]}{"end"} = $line[8];
}
close IN;

my $vcf = Vcf -> new(file=>"$file");
$vcf -> parse_header();

open(OUT, ">$out");
print OUT "Interval\tuniqID\tchr\tpos\tref\talt\tannot\tgene\tMAF\tMAF_FIN\tMAF_NFE\tMAF_AMR\tMAF_AFR\tMAF_EAS\tMAF_SAS\tMAF_OTH\n";

my @pop = qw(FIN NFE AMR AFR EAS SAS OTH);
foreach my $lid (sort {$a<=>$b} keys %Loci){
	my $region = $Loci{$lid}{"chr"}.":".$Loci{$lid}{"start"}."-".$Loci{$lid}{"end"};
	$vcf -> open(region=>"$region");
	while(my $line = $vcf->next_line()){
		my @line = split(/\s/, $line);
		next unless($line[6] eq "PASS");
		my $uniqID = join(":", ($line[0], $line[1], sort($line[3], $line[4])));
		my @vep = split(/\|/, $vcf -> get_info_field($line[7], 'CSQ'));
		$line[2] =~ s/^\.$/NA/;
		$line[7] =~ /;AF=(.+?);/;
		my $AF = $1;
		print OUT join("\t", ($lid, $uniqID, $line[0], $line[1], $line[3], $line[4], $vep[4], $vep[1]));
		printf OUT "\t%.2e", $AF;

		foreach my $p (@pop){
			$line[7] =~/.+AC_$p=(\d+).+AN_$p=(\d+)/;
			my $AC = $1;
			my $AN = $2;
			my $pAF = 0;
			$pAF = $AC/$AN unless($AN==0);
			if($pAF==0){print OUT "\t$pAF"}
			else{printf OUT "\t%.2e", $pAF}
		}
		print OUT "\n";
	}
}
close OUT;
