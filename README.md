# proj-topic-ex-2020_01
====

Overview  
Tools to obtain overviews of a scientific field for nonexperts by capturing research trends simply.

## Description
The system first displays a graph of the number of publications. Parameters can be set to display a ranking of the frequency of the bigrams. The parameters are “Period of new word extraction (year),” “Period for defining the base words (year),” and “Unit period of extraction (year.”).” In the execution of a search after the parameters are set, a ranking table displayed the frequency of occurrence of bigrams. Each term in the table has a link to original article records so that the user could confirm abstracts and other bibliographic items. Furthermore, if the number of terms that had certain occurrences per unit period (year) surpassed a threshold, it could be graphically displayed. Use data downloaded from SciFinder.

## Demo
[Demo](https://github.com/proj-topic-ex/proj-topic-ex-2020_01/blob/master/demo/demo_proj-topic-ex-2020_01.gif)

## Requirement
Apache/2.4  
MariaDB/10.1  
PHP/7.2  
Highcharts2.3  

## Setup
Copy all files under the document root and install HighRoller to a directory two levels up.

## Preprocessing
1. Download data from SciFinder in the Quated/tab format.
   Save the data to a text file: <original_file>

2. Prepare the following files:

- Conversion table to conver CAS Standard abbeviations & acronyms  
  File name: CAS_char_table.txt  
  Source:  
   CAS. CAS Standard Abbreviations & Acronyms,  
   https://www.cas.org/support/documentation/references/cas-standard-abbreviations  
  Format: Tab separated text with two columns. (<a word to be coverted>\t<converted word(s))

- Stopword list  
 File name: stoplist.txt  
 Format: Text separted by a newline.  

Execute the following per scripts in the "preprocessing" directory.
> perl CAS_char_convert.pl <original_file> <file_1>  
> perl it_split_mh.pl <file_1> > <file_2>  
> perl abs_decomp.pl <file_1> <file_3>  

3. Create DB
Create tables as in "preprocessing/create_tables.txt".

4. Load data to the DB
> load data infile '<file_1>' into table table_main lines terminated by '\r\n';  
> load data infile '<file_2>' into table table_mh lines terminated by '\r\n';  
> load data infile '<file_3> into table table_abs lines terminated by '\r\n';  


## Usage
Execute "start.php".

## Licence
These codes are licensed under CC0.
[![CC0](http://i.creativecommons.org/p/zero/1.0/88x31.png "CC0")](http://creativecommons.org/publicdomain/zero/1.0/deed.en)


