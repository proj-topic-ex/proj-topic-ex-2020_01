<?php

$id = $_GET['id'];
$id = urldecode($id);
$data = explode(" __",$id);
$word = explode(" ",$data[0]);
$year1 = $data[1];
$year2 = $data[2];
$record=array();
$rec_num=0;
$query="";
$query2="";

print "<html>\n";
print "<head>\n";
print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=\"utf-8\">\n";
print "<link rel=\"stylesheet\" href=\"../../../style1.css\">\n";
print "<title>$id</title>\n";
print "</head>\n";
print "<body>\n";
print "<h2>$word[0] $word[1]<span style=\"margin-left:1em; font-size:50%; font-family:'Meiryo UI';\">PY= $year1-$year2</h2>\n";
print "<table class=\"tb1\">";
print "</p>\n";

$word[0] =  preg_replace("/^\[(.+)\]$/", "$1", $word[0]);
$word[1] =  preg_replace("/^\[(.+)\]$/", "$1", $word[1]);


// Connect the SQL Server
$conn_id = mysqli_connect("localhost", "user", "password") or die ('Error connecting to mySQL server');

// Select DB
mysqli_select_db($conn_id, "ca");

// Search original articles by words and publication year


$query = "SELECT A.AN FROM htc_abs_20160208_smart A, (SELECT AN, seq_low, seq_word, Abs_word FROM htc_abs_20160208_smart) B WHERE A.AN = B.AN AND A.seq_low = B.seq_low and A.seq_word+1=B.seq_word and A.Abs_word=\"$word[0]\" and B.Abs_word=\"$word[1]\" and Pub_Year BETWEEN $year1 and $year2";

$result = mysqli_query($conn_id, $query) or die ($query . ' failed (' . mysqli_error() . ')');


// Select the number of the result
$rows = mysqli_num_rows($result);

//print "<h2><span style=\"margin-left:1em; font-size:50%; font-family:'Meiryo UI';\">$rows records found </h2>\n";

$query2 = "SELECT AN, Abstract, Author, Corporate, Pub_Year, Doc_Type, JT, IT FROM htc_20160208 WHERE AN IN (";


// Set data to arrays

if($rows){
    while ($row = mysqli_fetch_object($result)) {
        $record[$rec_num][0] = $row->AN ;
        $query2 = $query2."\"".$row->AN."\", ";
        $rec_num++;
    }
}

$rec_num = 0;

$query2 = preg_replace("/\, $/", "", $query2);
$query2 = $query2.") AND Pub_Year BETWEEN $year1 AND $year2";
// print "$query2</p>\n";

$result = mysqli_query($conn_id, $query2) or die ($query2 . ' failed (' . mysqli_error() . ')');

// Set data to arrays
$rows = mysqli_num_rows($result);

//print "<h2><span style=\"margin-left:1em; font-size:50%; font-family:'Meiryo UI';\">$rows records found (duplicates removed)</h2>\n";
print "<h2><span style=\"margin-left:1em; font-size:50%; font-family:'Meiryo UI';\">$rows records found.</h2>\n";

// Set data to arrays
if($rows){
    while ($row = mysqli_fetch_object($result)) {
        $record[$rec_num][0] = $row->AN ;
        $record[$rec_num][1] = $row->Abstract ;
        $record[$rec_num][2] = $row->Author ;
        $record[$rec_num][3] = $row->Corporate ;
        $record[$rec_num][4] = $row->Pub_Year ;
        $record[$rec_num][5] = $row->Doc_Type ;
        $record[$rec_num][6] = $row->JT ;
        $record[$rec_num][7] = $row->IT ;
        $rec_num++;
    }
}


// Memory release
mysqli_free_result($result);

print "<tr bgcolor=\"##ccffcc\"><td>AN</td><td>Abstract</td><td>Author</td><td>Corporate</td><td>Pub_Year</td><td>Doc_type</td><td>JT</td><td>IT</td></tr>\n";

for ($i = 0; $i <= $rec_num-1; $i++) {
    $record[$i][1] = replace_search_result($word[0], $record[$i][1]);
    $record[$i][1] = replace_search_result($word[1], $record[$i][1]);
    print "<tr>\n";
    print "<td>".$record[$i][0]."</td><td>".$record[$i][1]."</td><td>".$record[$i][2]."</td><td>".$record[$i][3]."</td><td>".$record[$i][4]."</td><td>".$record[$i][5]."</td><td>".$record[$i][6]."</td><td>".$record[$i][7]."</td>\n";
    print "</tr>\n";

}

print "</table>\n";
print "</body>\n";
print "</html>\n";



// Disconnect the server
mysqli_close($conn_id) or die("Failed to disconnect DB");


// Highlight results
function replace_search_result($query, $str)
{
    $query  = str_replace('Å@', ' ', $query);
    $q = preg_split("'[\\s,]+'", $query, -1, PREG_SPLIT_NO_EMPTY);
    $qq = array();
    foreach ($q as $val) {
        $qq[] = "'(".preg_quote($val).")'i";
    }
    return preg_replace($qq, "<strong>$1</strong>", $str);
}


?>

