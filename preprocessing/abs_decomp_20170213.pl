#!/Perl/bin/perl

# usage: perl abs_decomp.pl <input_file> <output_file>
#
# <stop-file> is stoword list
#
#2013/5/29
#@abs = split(/.  /, $abs);  Å®@abs = split(/\.  /, $abs); 


use utf8;
binmode STDOUT, ":utf8";
binmode STDERR, ":utf8";
binmode STDIN,  ":utf8";
use strict;
use warnings;


my %Stop;
my $an= "";
my $abs= "";
my @abs=();
my @word=();
my $lnum= 0;
my $wnum= 0;
my $pyear= "";

my $inputfile = $ARGV[0];
my $outputfile = $ARGV[1];
my $stoplist = "smart-stop-list.txt";
#my $stoplist = "stoplist20140810.txt";

$, = "\t";

open(IN, "$stoplist") or die("Can't open stoplist: $stoplist\n");

while(<IN>){
    if ($_ !~ /^#/){
        chomp; $_ =~ s/[\r\n]$//;
        $Stop{$_} = $_;
    }
}

close(IN);

open(IN, "$inputfile") or die("Can't open inputfile: $inputfile\n");
open(OUT, ">$outputfile") or die("Can't open outputfile: $outputfile\n");

while(<IN>){
    s/\. *$//i;
    chomp;
    ($an, $pyear, $abs) = split(/\t/);
    @abs = split(/\.  /, $abs);
    foreach my $m (@abs) {
        $lnum++;
        @word = split(/[ \-]/, $m);
        foreach my $l (@word) {
            $l =~ s/ +$//; $l =~ s/^ +//; $l =~s/[\,;]$//; 
            if ($l =~ /\(.*\)/) {
                $l =~ s/^\((.*)\)$/$1/;
            } else {
                $l =~ s/^\(//;
                $l =~ s/\)$//;
            }
            if ($l =~ /\".*\"/) {
                $l =~ s/^\"(.*)\"$/$1/;
            } else {
                $l =~ s/^\"//;
                $l =~ s/\"$//;
            }
            if (!exists $Stop{lc($l)} && $l ne ""){
                $wnum++;
                print OUT "$an\t$lnum\t$wnum\t$l\t";
                my $lc = lc($l);
                print OUT "$lc\t$pyear\n";
            }
        }
        $wnum=0;
        @word=();
    }
    $lnum=0;
    @abs=();
    $an=""; $pyear=""; $abs="";
}
close(IN);
close(OUT);
