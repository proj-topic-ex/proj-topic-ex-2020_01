<?php

$fyear = $_POST['fyear'];
$eyear = $_POST['eyear'];
$bgyear1 = $_POST['bgyear1'];
$bgyear2 = $_POST['bgyear2'];
$step = $_POST['step'];

// Define parameters
$word_for_graph=array();
$period=array();
$surplus=0;
$rec_num=0;
$rec_num_max=0;
$col=0;
$colmax=0;
$IT= array();
$count= array();
$c="";

// Select the number of index terms by year

$colmax =floor(($eyear-($fyear-1))/$step);
$surplus = ($eyear - ($fyear-1)) % $step;
if(($surplus) <> 0) {
    $eyear = $eyear - $surplus;
}

print "<html>\n";
print "<head>\n";
print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=\"utf-8\">\n";
print "<link rel=\"stylesheet\" href=\"../../style1.css\">\n";
print "<title>New Index Terms</title>\n";
print "</head>\n";
print "<body>\n";
print "<h3>New Index Terms</h3>\n";
print "(From $fyear to $eyear by $step years)\n";
print "<table width = \"1500\" border = \"1\" cellspacing=\"0\">\n";


// Connect the SQL Server
$conn_id = mysqli_connect("localhost", "user", "password") or die ('Error connecting to mySQL server');

// Select DB
mysqli_select_db($conn_id, "ca");

for ($i=$fyear; $i<=(($fyear-1)+($step*$colmax)); $i=$i+$step){
    $year1 = $i;
    $year2 = $i+($step-1);

    $result="";
    $rows= 0;


// If an index term appears in the defined period, the index term is defined as a base word.
/*
$query = <<<EOT
select IT,count(IT) as cnt
from htc_mh_20160208
where (pub_year between $year1 and $year2)
and IT not in (
    select IT from htc_mh_20160208
    where pub_year between $bgyear1 and $bgyear2
)
group by IT
order by count(IT) desc, IT
EOT;
*/

// If an index term appears more than one time in the defined period, the index term is defined as a base word.
$query = <<<EOT

select IT,count(IT) as cnt
from htc_mh_20160208 as T1
where (pub_year between $year1 and $year2)
and not exists (
    select distinct IT
    from htc_mh_20160208 as T2
    where pub_year between $bgyear1 and $bgyear2
     and T1.IT = T2.IT
    group by Pub_Year, IT
    having count(IT)>1
    order by IT
)
group by IT
order by count(IT) desc, IT

EOT;

$result = mysqli_query($conn_id, $query) or die ($query . ' failed (' . mysqli_error() . ')');

    // Select the number of the result
    $rows = mysqli_num_rows($result);

    // Set data to arrays
    if($rows){
        while ($row = mysqli_fetch_object($result)) {
            $IT[$rec_num][$col] = $row->IT ;
            $count[$rec_num][$col] = $row->cnt ;
            $rec_num++;
        }
    }
    if ($rec_num_max < $rec_num ){ $rec_num_max = $rec_num; }
    $rec_num=0;
    $col++;

    // Memory release
    mysqli_free_result($result);

}

    //print "$msg\n";

print "<tr bgcolor=\"#6495ed\" fontcolor=\"#ffffff\">\n";
for($j=0; $j<=($col-1)*$step; $j=$j+$step){
    $year1= $j + $fyear;
    $year2= $j + $fyear+($step-1);
    print "<td align=\"center\" colspan=\"2\">$year1 - $year2</td>\n";
    array_push($period, "$year1 - $year2");
}
print "</tr>\n";

print "<tr bgcolor=\"#00ffff\">\n";
for($j=0; $j<=$colmax-1; $j++){
    print "<td align=\"center\">IT</td><td align=\"center\">CNT</td>\n";
}
print "</tr>\n";

for ($i=0; $i<=$rec_num_max-1; $i++) {
    for($j=0; $j<=$colmax-1; $j++){
        $year1= $j*$step + $fyear;
        $year2= $year1+($step-1);
        if(empty($IT[$i][$j])){
            $value = "" ;
        } else {
            $value = $IT[$i][$j];
        }
        if(empty($count[$i][$j])){
            $c = "";
        } else {
            $c = $count[$i][$j];
            $c_for_graph[$value][$j]= $c;
        }
        // Highlight results
        if (!empty($color[$value])) {
            $value_disp = preg_replace("/".preg_quote($value,"/")."/i","<strong style=\"background-Color:$color[$value];\">$0</strong>",$value);
        } else {
            if($c >= 15){
                $red = rand(150,255);
                $green = rand(150,255);
                $blue = rand(150,255);
                $color16 = change16_color($red, $green, $blue);
                $color[$value] = $color16;
                $value_disp = preg_replace("/".preg_quote($value,"/")."/i","<strong style=\"background-Color:$color[$value];\">$0</strong>",$value);
                array_push($word_for_graph, $value);
            } else {
                $value_disp = $value;
            }
        }
    }
}
for ($i=0; $i<=$rec_num_max-1; $i++) {
    for($j=0; $j<=$colmax-1; $j++){
        $year1= $j*$step + $fyear;
        $year2= $year1+($step-1);
        if(empty($IT[$i][$j])){
            $value = "" ;
        } else {
            $value = $IT[$i][$j];
        }
        if(empty($count[$i][$j])){
            $c = "";
        } else {
            $c = $count[$i][$j];
            $c_for_graph[$value][$j]= $c;
        }
        // Highlight results
        if (!empty($color[$value])) {
            $value_disp = preg_replace("/".preg_quote($value,"/")."/i","<strong style=\"background-Color:$color[$value];\">$0</strong>",$value);
        } else {
            $value_disp = $value;
        }
        $to_org = $value." __".$year1." __".$year2;
        $to_org = urlencode($to_org);
        print "<td><a href=\"org.php?id=".$to_org."\" target=\"_blank\">".$value_disp."</a></td><td align=\"right\">".$c."</td>\n";
//        print "<td><a href=\"org.php?id=".$value."___".$year1."\" target=\"_blank\">".$value_disp."</a></td><td align=\"right\">".$c."</td>\n";
    }
    print "</tr>\n";
}

print "</table>\n";

print "<form action=\"line_basic_it.php\" method=\"post\" target=\"_blank\">\n";
print "<input type=\"hidden\" name=\"condition\" value=\"From $fyear to $eyear by $step years, Background[$bgyear1-$bgyear2]\">\n";
for ($i=0; $i<=$colmax-1; $i++) {
    print "<input type=\"hidden\" name=\"period[$i]\" value=\"$period[$i]\">\n";
}
foreach ($word_for_graph as $key => $value){
    $w_for_graph = urlencode($value);
    print "<input type=\"hidden\" name=\"word_for_graph[$key]\" value=\"$value\">\n";
    for ($i=0; $i<=$colmax-1; $i++) {
        if(empty($c_for_graph[$value][$i])){ $c_for_graph[$value][$i] = 0;}
        $tmp1 = $c_for_graph[$value][$i];
        $tmp2 = $period[$i];
        print "<input type=\"hidden\" name=\"c_for_graph[$w_for_graph][$tmp2]\" value=\"$tmp1\">\n";
    }
}
print "<input type=\"submit\" name=\"trans_graph\" value=\"View Transition Graph\" style=\"font-size:large\">\n";
print "</form>\n";

print "Index terms occurred 15 and more times per unit period</hr>\n";
print "<h5>\n";
foreach ($word_for_graph as $value){
    print "$value -> ";
    for ($i=0; $i<=$colmax-1; $i++) {
        if(empty($c_for_graph[$value][$i])){ $c_for_graph[$value][$i] = 0;}
        print $c_for_graph[$value][$i].',';
    }
    print "<br />\n";
}

print "</body>\n";
print "</html>\n";


// Disconnect the server
mysqli_close($conn_id);


function change16_color ($red,$green,$blue) {

	$red = dechex($red);
	$green = dechex($green);
	$blue = dechex($blue);

	if (strlen($red) == 1) { $red = $red.$red; }
	if (strlen($green) == 1) { $green = $green.$green; }
	if (strlen($blue) == 1) { $blue = $blue.$blue; }

	$color16 = '#'."$red$green$blue";
	return $color16;
}


?>
