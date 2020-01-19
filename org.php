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
print "<link rel=\"stylesheet\" href=\"../../style1.css\">\n";
print "<title>$data[0]</title>\n";
print "</head>\n";
print "<body>\n";
print "<h2>IT = \"$data[0]\"<span style=\"margin-left:1em; font-size:50%; font-family:'Meiryo UI';\">PY= $year1-$year2</h2>\n";
print "<table width = \"1500\" border = \"1\" cellspacing=\"0\">\n";


// Connect the SQL Server
$conn_id = mysqli_connect("localhost", "user", "password") or die ('Error connecting to mySQL server');

/* Select DB */
mysqli_select_db($conn_id, "ca");

/* Search original articles by index terms  and publication year */
$query = "select AN, Title, Abstract, Author, Corporate, pub_year, doc_type, JT,IT,DOI from htc_20160208 where AN in (SELECT AN FROM `htc_mh_20160208` WHERE IT = \"$data[0]\" and pub_year between $year1 and $year2 ) order by pub_year, Author, AN";

$result = mysqli_query($conn_id, $query) or die ($query . ' failed (' . mysqli_error() . ')');


// Select the number of the result
$rows = mysqli_num_rows($result);


// Set data to arrays
if($rows){
    while ($row = mysqli_fetch_object($result)) {
        $record[$rec_num][0] = $row->AN ;
        $record[$rec_num][1] = $row->Title ;
        $record[$rec_num][2] = $row->Abstract ;
        $record[$rec_num][3] = $row->Author ;
        $record[$rec_num][4] = $row->Corporate ;
        $record[$rec_num][5] = $row->pub_year ;
        $record[$rec_num][6] = $row->doc_type;
        $record[$rec_num][7] = $row->JT;
        $record[$rec_num][8] = $row->IT;
        $record[$rec_num][9] = $row->DOI;
        $rec_num++;
    }
}

// Memory release
mysqli_free_result($result);


print "<tr bgcolor=\"##ccffcc\"><td>AN</td><td>Title</td><td>Abstract</td><td>Author</td><td>Corporate</td><td>pub_year</td><td>Doc_type</td><td>JT</td><td>IT</td><td>DOI</td></tr>\n";
print "<h2><span style=\"margin-left:1em; font-size:50%; font-family:'Meiryo UI';\">$rows records found.</h2>\n";

for ($i = 0; $i <= $rec_num-1; $i++) {
    $record[$i][9] = str_replace(array("\r\n", "\r", "\n"), '', $record[$i][9]);
    if ( $record[$i][9] <> "") { $record[$i][9] = "<a href = \"https://doi.org/".$record[$i][9]."\" target=\"_blank\">https://doi.org/".$record[$i][9]."</a>"; }
    print "<tr>\n";
    
    print "<td>".$record[$i][0]."</td><td>".$record[$i][1]."</td><td>".$record[$i][2]."</td><td>".$record[$i][3]."</td><td>".$record[$i][4]."</td><td>".$record[$i][5]."</td><td>".$record[$i][6]."</td><td>".$record[$i][7]."</td><td>".$record[$i][8]."</td><td>".$record[$i][9]."</td>\n";
    print "</tr>\n";

}

print "</table>\n";
print "</body>\n";
print "</html>\n";



// Disconnect the server
mysqli_close($conn_id) or die("Failed to disconnect DB");

?>

