#!/usr/bin/perl

use strict;
use warnings;

##### resources/views/includes/header.blade.php
=begin
my $in = "resources/views/includes/header.blade.php";
open(IN, "$in") or die "Cannot open $in\n";
open(OUT, ">temp.txt");
my $check = 0;
while(<IN>){
	if(/local_start/){$check=1; next;}
	if(/local_end/){$check=0; next;}
	if($check==1){
		$_ =~ s/href="\//href="{{\$subdir}}\//;
	}
	print OUT $_;
}
close IN;
close OUT;
system "mv temp.txt $in";

##### app/Http/routes.php
$in = "app/Http/routes.php";
open(IN, "$in") or die "Cannot open $in\n";
open(OUT, ">temp.txt");
while(<IN>){
	next if(/#local$/);
	if(/#webserver/){$_ =~ s/#webserver //;}
	
	print OUT $_;
}
close IN;
close OUT;
system "mv temp.txt $in";
=cut

##### Controllers
my $dir = "app/Http/Controllers/";
my @files = qw(JobController.php D3jsController.php JsController.php);

foreach my $f (@files){
	open(IN, $dir.$f);
	open(OUT, ">temp.txt");
	while(<IN>){
		next if(/#local$/);
		if(/#webserver/){$_ =~ s/#webserver //;}
	
		print OUT $_;
	}
	close IN;
	close OUT;
	system "mv temp.txt $dir".$f;
}

##### JS
$dir = "public/js/";
@files = qw(InputParameters.js);
foreach my $f (@files){
	open(IN, $dir.$f);
	open(OUT, ">temp.txt");
	while(<IN>){
		next if(/\/\/local$/);
		if(/\/\/webserver/){$_ =~ s/\/\/webserver //;}
	
		print OUT $_;
	}
	close IN;
	close OUT;
	system "mv temp.txt $dir".$f;
}

##### Scripts
$dir = "storage/scripts/";
@files = qw(gwas_file.pl getLD.pl SNPannot.R getExAC.pl geteQTL.pl geneMap.R annotPlot.R gene2func.R GeneSet.R getChr15.pl getGWAScatalog.pl magma.pl GeneSet.py);

foreach my $f (@files){
	open(IN, $dir.$f);
	open(OUT, ">temp.txt");
	while(<IN>){
		next if(/#local$/);
		if(/#webserver/){$_ =~ s/#webserver //;}
	
		print OUT $_;
	}
	close IN;
	close OUT;
	system "mv temp.txt $dir".$f;
}

system "chmod 755 storage/scripts/*";
