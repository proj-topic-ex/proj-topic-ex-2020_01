<?php
require_once('../../../HighRoller/HighRoller.php');
require_once('../../../HighRoller/HighRollerSeriesData.php');
require_once('../../../HighRoller/HighRollerLineChart.php');

$subtitle = $_POST['condition'];
$count = $_POST['c_for_graph'];
$word = $_POST['word_for_graph'];
$datax = $_POST['period'];

$bigram = "";
$bigrams = array();
$sorted = array();

$pkey = "";
$x_axis = array();
$num_x_axis = 0;

/* Graph */
foreach ( $count as $key=>$val) {
    foreach ( $val as $key2=>$val2) {

/* decode */
        $bigram = "";
        $bigram = $key;
        $bigram = urldecode($key);
        if ( $key <> $pkey ) { ${"x_".$bigram} = array(); ${"y_".$bigram} = array(); }

        array_push(${"x_".$bigram}, $key2);
        settype($val2, "int");
        array_push(${"y_".$bigram}, $val2);
 
        $pkey = $key;
        $x_axis = ${"x_".$bigram};
        $num_x_axis = count($x_axis);

    }
    array_push($bigrams, $bigram);
}

    $linechart = new HighRollerLineChart();
    $linechart->chart->renderTo = 'linechart';
    $linechart->title->text = 'Transition of Word Frequencies';
    $linechart->subtitle = new stdClass;
    $linechart->subtitle->text = $subtitle;
    $linechart->xAxis = new stdClass;
    $linechart->xAxis->categories = $x_axis;
    $linechart->yAxis = new stdClass;
    $linechart->yAxis->min = new stdClass;
    $linechart->yAxis->min = 0;
    $linechart->yAxis->allowDecimals = 'false';
    $linechart->yAxis->title = new stdClass;
    $linechart->yAxis->title->text = '# of Bigrams';
    $linechart->yAxis->tickInterval = 5;

#foreach ($word as $key=>$val) {
foreach ($bigrams as $key=>$bigram) {

    $x = "x_".$bigram;
    $y = "y_".$bigram;
    
    ${$bigram} = new HighRollerSeriesData();
    ${$bigram}->addName($bigram)->addData(${$y});
    $linechart->addSeries(${$bigram});

}

?>
<html>
<head>
<meta charset="utf-8">
  <title>Transition Graph (word)</title>
  <link rel="stylesheet" href="../../../style.css">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
  <!-- HighRoller: set the location of Highcharts library -->
  <?php echo HighRoller::setHighChartsLocation("../../../js/highcharts.js");?>
</head>

<body>

  <div id="linechart"></div>
  <script type="text/javascript">
    <?php echo $linechart->renderChart();?>
  </script>

<hr>

<table class="tb2">

<?php

print "<tr>";
print "<th>Bigrams including new words</th>";

foreach ($x_axis as $key=>$value) {
    print "<th>$value</th>";
}
print "</tr>\n";

$sorted = sort($bigrams, SORT_STRING);

foreach ($bigrams as $key=>$bigram) {
    print "<tr>";
    print "<colgroup span=\"1\" class=\"col_left\"></colgroup>";
    print "<colgroup span=\"$num_x_axis\" class=\"col_other\"></colgroup>";
    print "<td>$bigram</td>";
    foreach (${"y_".$bigram} as $key2 => $yy){
        print "<td>$yy</td>";
    }
    print "</tr>\n";
}

?>

</table> 
  
</body>
</html>

