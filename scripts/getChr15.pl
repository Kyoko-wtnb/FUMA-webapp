#!/usr/bin/perl
use strict;
use warnings;
use File::Basename;
use Config::Simple;

die "ERROR: Not enough argument.\nUSAGE: ./getChr15.pl <filedir> <ts>\n" if(@ARGV<2);

my $dir = dirname(__FILE__);
my $cfg = new Config::Simple($dir.'/app.config');
my $chr15dir = $cfg->param('data.chr15');

my $filedir = $ARGV[0];
$filedir .= '/' unless($filedir=~/\/$/);
my @cells = split(/:/,$ARGV[1]);
my $in = $filedir."annotPlot.txt";
my $out = $filedir."Chr15.txt";

if($cells[0] eq 'all'){
 @cells = `ls $chr15dir/*.bed.gz`;
  chomp @cells;
  foreach my $i (0..$#cells){
    $cells[$i] =~ s/.*\/(E\d+)_core.*/$1/;
  }
}

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

if($end-$start == 0){
  $end += 500;
  $start -= 500;
}

open(OUT, ">$out") or die "$out";
print OUT "cell\tstart\tend\tstate\n";
foreach my $cell (sort @cells){
  my $file="$chr15dir/$cell\_core15.bed.gz";
  my @line = split(/\n/, `tabix $file $chr:$start-$end`);
  foreach my $l (@line){
    my @epi = split(/\s/, $l);
    $epi[1]=$start if($epi[1]<$start);
    $epi[2]=$end if($epi[2]>$end);
    print OUT "$cell\t$epi[1]\t$epi[2]\t$epi[3]\n";
  }
}
close OUT;
