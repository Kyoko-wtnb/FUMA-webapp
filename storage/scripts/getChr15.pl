#!/usr/bin/perl
use strict;
use warnings;

my $filedir = $ARGV[0];
my @cells = split(/:/,$ARGV[1]);
my $in = $filedir."annotPlot.txt";
my $out = $filedir."Chr15.txt";

my $start = 0;
my $end = 0;
my $chr = 0;
open(IN, "$in") or die "Cannot open $in\n";
<IN>;
while(<IN>){
  my @line = split(/\s/, $_);
  if($start==0){
    $start = $line[2];
    $chr = $line[1];
  }
  if($end < $line[2]){
    $end = $line[2];
  }
}
close IN;

open(OUT, ">$out") or die "$out";
print OUT "cell\tstart\tend\tstate\n";
foreach my $cell (sort @cells){
  my $file="/media/sf_Documents/VU/Data/Chr15States/States/$cell\_core15.bed.gz";
  my @line = split(/\n/, `tabix $file $chr:$start-$end`);
  foreach my $l (@line){
    my @epi = split(/\s/, $l);
    $epi[1]=$start if($epi[1]<$start);
    $epi[2]=$end if($epi[2]>$end);
    print OUT "$cell\t$epi[1]\t$epi[2]\t$epi[3]\n";
  }
}
close OUT;
