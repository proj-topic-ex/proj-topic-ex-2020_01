#!/Perl/bin/perl


# usage: perl CAS_char_convert.pl <input file>  <output file>

use utf8;
binmode STDOUT, ":utf8";
binmode STDERR, ":utf8";
binmode STDIN,  ":utf8";
use strict;
use warnings;



my $dummy;

my $inputfile = $ARGV[0];
my $outputfile = $ARGV[1];
my $table = "CAS_char_table.txt";

my %casword=();
my $key="";
my @word=();
my @conv_word=();

open(IN, "$table") or die("Can't open conv_table: $table\n");

while(<IN>){
    if ($_ !~ /^#/){
        chomp; $_ =~ s/[\r\n]$//;
        ($key, my $value ) = split(/\t/, $_);
        $casword{$key} = $value;
        if($key eq lc($key)){
            $key = ucfirst($key);
            $casword{$key} = $value;
        }
    }
}

close(IN);

open(IN, "$inputfile") or die("Can't open inputfile: $inputfile\n");
open(OUT, ">$outputfile") or die("Can't open outputfile: $outputfile\n");

while(<IN>){
if ($_ !~ /^T/){
#   chomp; $_ =~ s/[\r\n]$//;
    ($dummy, $dummy, $dummy, $dummy, my $an, my $abs, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, $dummy, my $pyear) = split(/\t/, $_);
#    chomp; $_ =~ s/^ //; $_ =~ s/[\r\n]$//;
#    (my $an, my $py, my $abs) = split(/\t/);
    $abs =~ s/\[machine translation of descriptors\]//i;
    $abs =~ s/\[on SciFinder\(R\)\]//i;
    $abs =~ s/\[on SciFinder \(R\)\]//i;
    $abs =~ s/\(c\) [0-9]{4} American Institute of Physics\.//i;
    print OUT "$an\t$pyear\t";
    my @abs = split(/\.  /, $abs);
    foreach my $sentence (@abs) {
#        @word = split(/[ \-]/, $sentence);
        @word = split(/[( +)\-]/, $sentence);
        foreach my $k (@word) {
            $k =~ s/ +$//; $k =~ s/^ +//; $k =~s/[\,;]$//; 
            if ($k =~ /\(.*\)/) {
                $k =~ s/^\((.*)\)$/$1/;
            } else {
                $k =~ s/^\(//;
                $k =~ s/\)$//;
            }
            if ($k =~ /\[.*\]/) {
                $k =~ s/^\[(.*)\]$/$1/;
            } else {
                $k =~ s/^\[//;
                $k =~ s/\]$//;
            }
            if ($k =~ /\".*\"/) {
                $k =~ s/^\"(.*)\"$/$1/;
            } else {
                $k =~ s/^\"//;
                $k =~ s/\"$//;
            }
            if (exists $casword{$k}){
                $k = $casword{$k};
            }
            push(@conv_word, $k);
        }
        $, = " ";
        my $for_print =  join(" ", @conv_word);
        $for_print = $for_print."\.  ";
        $for_print =~ s/\.\.  $/\.  /;
        print OUT $for_print;
        @conv_word=(); $for_print="";
    }
    print OUT "\n";
}
}
close(IN);
close(OUT);
