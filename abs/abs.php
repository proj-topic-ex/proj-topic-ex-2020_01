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
$base=array();
$base_unique=array();
$col = 0;
$colmax=0;
$max_i = 0;

$colmax =floor(($eyear-($fyear-1))/$step);
$surplus = ($eyear - ($fyear-1)) % $step;
if(($surplus) <> 0) {
    $eyear = $eyear - $surplus;
}

print "<html>\n";
print "<head>\n";
print "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"utf-8\">\n";
print "<link rel=\"stylesheet\" href=\"../../../style1.css\">\n";
print "<title>New Words in Abstract</title>\n";
print "</head>\n";
print "<body>\n";
print "<h2>New Words in Abstract</h2>\n";
print "(From $fyear to $eyear by $step years, Background[$bgyear1-$bgyear2])\n";
print "<table width = \"1500\" border = \"1\" cellspacing=\"0\">\n";


// Connect the SQL Server
$conn_id = mysqli_connect("localhost", "user", "password") or die ('Error connecting to mySQL server');
// DB‚ð‘I‘ð
mysqli_select_db($conn_id, "ca");


// ====================================================================================================
// Define base words

for ($j=$bgyear1; $j<=$bgyear2; $j=$j+1){

//SELECT distinct AN, Abs_word_lc, count(Abs_word_lc)

// If an index term appears in the defined period, the index term is defined as a base word.
$query = <<<EOT
SELECT distinct Abs_word_lc
FROM `htc_abs_20160208_smart`
WHERE Pub_Year = $j
EOT;

/*
// If an index term appears more than one time in the defined period, the index term is defined as a base word.
$query = <<<EOT
SELECT distinct Abs_word_lc, count(Abs_word_lc)
FROM `htc_abs_20160208_smart`
WHERE Pub_Year = $j
group by Abs_word_lc
having (count(Abs_word_lc))>1
EOT;
*/

    $result = mysqli_query($conn_id, $query) or die ($query . ' failed (' . mysqli_error() . ')');

    // Select the number of the result
    $rows = mysqli_num_rows($result);

    // Set data to arrays
    if($rows){
        while ($row = mysqli_fetch_object($result)) {
            $row->Abs_word_lc = str_replace(array("\r\n","\r","\n"), '', $row->Abs_word_lc); 
            $base[]= $row->Abs_word_lc;
        }
    }

    // Memory release
    mysqli_free_result($result);

}

$base_unique = array_unique($base);
$base=array();

// ====================================================================================================

// Select data by step
for ($j=$fyear; $j<=$fyear+($colmax-1)*$step; $j=$j+$step){
    $an = array();
    $seq_low = array();
    $seq_word = array();
    $word = array();
    $word_lc = array();
    $max_key3;
    $data = array();
    $count = array();
    $bigram = "";
    $array_key3 = array();
    $count_sorted = array();
    $bigram_sorted = array();
    $bigram1 =""; $bigram2 = "";
    $color= array();
    $word_for_graph= array();
    $period= array();

    $query = "SELECT * FROM `htc_abs_20160208_smart` where Pub_Year between $j and $j+$step-1";
    $result = mysqli_query($conn_id, $query) or die ($query . ' failed (' . mysqli_error() . ')');

    // Select the number of the result
    $rows = mysqli_num_rows($result);

    // Set data to arrays
    if($rows){
        while ($row = mysqli_fetch_object($result)) {
            $an_ = $row->AN ; $an[] = $row->AN ;
            $seq_low_ = $row->seq_low; $seq_low[$an_] = $row->seq_low;
            $seq_word_ = $row->seq_word; $seq_word[$an_][$seq_low_] = $row->seq_word;
            $word[$an_][$seq_low_][$seq_word_] = $row->Abs_word;
            $row->Abs_word_lc = str_replace(array("\r\n","\r","\n"), '', $row->Abs_word_lc);
            $word_lc[$an_][$seq_low_][$seq_word_] = $row->Abs_word_lc;
            $an_ = ""; $seq_low_ = ""; $seq_word_ = "";
        }

        foreach ($word as $key1 => $value1){
        
            foreach ($value1 as $key2 => $value2){
            
                foreach ($value2 as $key3 => $value3){
                    $value4 = $word_lc[$key1][$key2][$key3];

                    $array_key3[] = $key3;
                    $max_key3 = max($array_key3);
                }
                
                for ($i=1; $i <= $max_key3-1; $i++){
                     $match1 = in_array($word_lc[$key1][$key2][$i], $base_unique);
                     $match2 = in_array($word_lc[$key1][$key2][$i+1], $base_unique);
                     if (!(($match1) and ($match2))){
                         $bigram1 = $word_lc[$key1][$key2][$i];
                         $bigram2 = $word_lc[$key1][$key2][$i+1];
                         if ($match1){ $bigram1 = "[".$bigram1."]" ;}
                         if ($match2){ $bigram2 = "[".$bigram2."]" ;}
                         $bigram = $bigram1." ".$bigram2;
                         if(!isset($count[$bigram])){$count[$bigram] = 0;}
                         if(!isset($flg[$bigram])){$flg[$bigram] = 0;}
                         if($flg[$bigram] == 0){$count[$bigram]++; $flg[$bigram] = 1;}
                          $bigram1 = ""; $bigram2 = ""; $bigram = "";
                     }
                }
                $max_key3 = 0;
                $array_key3 = array();
            }
            $flg= array();
        }
    
        foreach ($count as $key=>$value){
            $data[] = array('bigram' => $key, 'count' => $value);
        }
        
        foreach ($data as $key=>$row){
            $bigram_sorted[$key] = $row['bigram'];
            $count_sorted[$key] = $row['count'];
        }

        array_multisort ($count_sorted, SORT_DESC, $bigram_sorted, SORT_ASC, $data );

        $k = $j+1;

        $i=0;
        foreach ($data as $key1 => $value1){
            // if ($value1['count'] >2){
            $disp_rec[$j][$i]['bigram'] = $value1['bigram'];
            $disp_rec[$j][$i]['count'] = $value1['count'];
            $i++;
            $l = $i-1;
            if ($l >= $max_i){ $max_i = $l ;}
            // }
        }

    }
}


// Memory release
mysqli_free_result($result);
// Disconnect the server
mysqli_close($conn_id);


print "<tr bgcolor=\"#6495ed\" fontcolor=\"#ffffff\">\n";
for($j=0; $j<=($colmax-1)*$step; $j=$j+$step){
    $year1= $j + $fyear;
    $year2= $j + $fyear+($step-1);
    print "<td align=\"center\" colspan=\"2\">$year1 - $year2</td>\n";
    array_push($period, "$year1 - $year2");
    
//    $k = $j+1;
//    print "<td align=\"center\">$j - $k</td><td align=\"center\">CNT</td>\n";
//    array_push($period, "$j - $k");
}
print "</tr>\n";

print "<tr bgcolor=\"#00ffff\">\n";
for($j=0; $j<=$colmax-1; $j++){
    print "<td align=\"center\">Bigrams</td><td align=\"center\">CNT</td>\n";
}
print "</tr>\n";

//  print "$max_i ****\n";

$l =0;
for ($i=0; $i <= $max_i; $i++){
    print "<tr>\n";
    for ($year1=$fyear; $year1 <= $fyear+($colmax-1)*$step; $year1=$year1+$step){
        $year2 = $year1+$step-1;
        if(empty($disp_rec[$year1][$i]['bigram'])){
            $b = "";
            $c = "";
        } else {
            $b = $disp_rec[$year1][$i]['bigram'];
            $c = $disp_rec[$year1][$i]['count'];
            $c_for_graph[$b][$l]= $c;
            if (!empty($disp_rec[$year1][$i]['bigram'])) {
                if (!empty($color[$b])) {
                    $b_disp = preg_replace("/".preg_quote($b,"/")."/i","<strong style=\"background-Color:$color[$b];\">$0</strong>",$b);
                } else {
                    if($c >= 8){
                        $red = rand(150,255);
                        $green = rand(150,255);
                        $blue = rand(150,255);
                        $color16 = change16_color($red, $green, $blue);
                        $color[$b] = $color16;
                        $b_disp = preg_replace("/".preg_quote($b,"/")."/i","<strong style=\"background-Color:$color[$b];\">$0</strong>",$b);
                        array_push($word_for_graph, $b);
                    } else {
                        $b_disp = $b;
                    }
                }
            }
        }


        $to_org = $b." __".$year1." __".$year2;
        $to_org = urlencode($to_org);

        $b ="";
        $c ="";
        $b_disp ="";
    
        $l++;
    
    }
}

$l =0;
for ($i=0; $i <= $max_i; $i++){
    print "<tr>\n";
    for ($year1=$fyear; $year1 <= $fyear+($colmax-1)*$step; $year1=$year1+$step){
        $year2 = $year1+$step-1;
        if(empty($disp_rec[$year1][$i]['bigram'])){
            $b = "";
            $c = "";
        } else {
            $b = $disp_rec[$year1][$i]['bigram'];
            $c = $disp_rec[$year1][$i]['count'];
            $c_for_graph[$b][$l]= $c;
            if (!empty($disp_rec[$year1][$i]['bigram'])) {
                if (!empty($color[$b])) {
                    $b_disp = preg_replace("/".preg_quote($b,"/")."/i","<strong style=\"background-Color:$color[$b];\">$0</strong>",$b);
                } else {
                    $b_disp = $b;
                }
            }
        }

        $to_org = $b." __".$year1." __".$year2;
        $to_org = urlencode($to_org);
        print "<td><a href=\"org_.php?id=".$to_org."\" target=\"_blank\">".$b_disp."</a></td><td align=\"right\">".$c."</td>\n";

        $b ="";
        $c ="";
        $b_disp ="";
    
        $l++;
    
    }
    print "</tr>\n";
    $l=0;
}
print "</table>\n";


print "<form action=\"line_basic.php\" method=\"post\" target=\"_blank\">\n";
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
    $w_for_graph ="";
}
print "<input type=\"submit\" name=\"trans_graph\" value=\"View Transition Graph\" style=\"font-size:large\">\n";
print "</form>\n";

print "Character strings occurred 8 and more times per unit period</hr>\n";
print "<h5>\n";
foreach ($word_for_graph as $value){
    print "$value -> ";
    for ($i=0; $i<=$colmax; $i++) {
        if(empty($c_for_graph[$value][$i])){ $c_for_graph[$value][$i] = 0;}
        print $c_for_graph[$value][$i].',';
    }
    print "<br />\n";
}

print "</h5>\n";


print "</body>\n";
print "</html>\n";



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
