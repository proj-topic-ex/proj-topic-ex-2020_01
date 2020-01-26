1. Download data from SciFinder in the Quated/tab format.
   Save the data to a text file: <original_file>

2. Prepare the following files:

- Conversion table to CAS Standard abbeviations & acronyms.
  File name: CAS_char_table.txt
  Source:
   CAS. CAS Standard Abbreviations & Acronyms, https://www.cas.org/support/documentation/references/cas-standard-abbreviations
  Format: Tab separated text with two columns. (<a word to be coverted>\t<converted word(s))

- Stopword list
 File name: stoplist.txt
 Format: Text separted by a newline.

Execute
> perl CAS_char_convert.pl <original_file> <file_1>
> perl it_split_mh.pl <file_1> > <file_2>
> perl abs_decomp.pl <file_1> <file_3>


3. Create DB
CREATE TABLE table_main (T CHAR(1), Copyright VARCHAR(100), DB VARCHAR(10), Title VARCHAR(500), AN VARCHAR(20), Abstract TEXT, Author VARCHAR(500), CAN VARCHAR(20), Section_Code VARCHAR(10), Section_Title VARCHAR(200), Cross_ref VARCHAR(20), Corporate VARCHAR(500), URL VARCHAR(500), Doc_Type VARCHAR(100), CODEN CHAR(6), ISSN VARCHAR(13), JT VARCHAR(500), Full_JT VARCHAR(500), Language VARCHAR(50), Volume VARCHAR(50), Issue VARCHAR(500), Page VARCHAR(100), Pub_Year CHAR(4), Pub_Date VARCHAR(8), IT TEXT, IT2 VARCHAR(500), CAS_Reg TEXT, ST VARCHAR(1000), PCT_Des_Stat VARCHAR(1000), PCT_Reg_Des_Stat VARCHAR(500), PCT_Pat_Des_Stat VARCHAR(50), Main_IPC VARCHAR(50), IPC VARCHAR(50), Sec_IPC VARCHAR(50), Add_IPC VARCHAR(50), Index_IPC VARCHAR(50), Inventor VARCHAR(100), NPS VARCHAR(50),Pat_Appli_Cnt VARCHAR(50), Pat_Appli_Date VARCHAR(8), Pat_Appli_Num VARCHAR(50), Pat_Assignee VARCHAR(100), Pat_Country CHAR(2), Pat_Kind_Code VARCHAR(10), Pat_Num VARCHAR(50), Prior_Appli_Cnt CHAR(2), Prior_Appli_Num VARCHAR(50), Prior_Appli_Date VARCHAR(8), Citations TEXT, DOI VARCHAR(500));
CREATE TABLE table_mh (AN VARCHAR(20), Pub_Year CHAR(4), IT VARCHAR(1000));
CREATE TABLE table_abs (AN VARCHAR(20), seq_low INT(3), seq_word INT(3), Abs_word VARCHAR(100), Abs_word_lc VARCHAR(100), Pub_Year char(4));

4. Load data to the DB
load data infile '<file_1>' into table table_main lines terminated by '\r\n';
load data infile '<file_2>' into table table_mh lines terminated by '\r\n';
load data infile '<file_3> into table table_abs lines terminated by '\r\n';
 