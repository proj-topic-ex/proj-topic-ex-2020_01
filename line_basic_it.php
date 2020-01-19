<?php
require_once('../../HighRoller/HighRoller.php');
require_once('../../HighRoller/HighRollerSeriesData.php');
require_once('../../HighRoller/HighRollerLineChart.php');

$subtitle = $_POST['condition'];
$count = $_POST['c_for_graph'];
$word = $_POST['word_for_graph'];
$datax = $_POST['period'];

$IT = "";
$ITs = array();
$sorted = array();

$pkey = "";
$x_axis = array();
$num_x_axis = 0;

/* Graph */
foreach ( $count as $key=>$val) {
    foreach ( $val as $key2=>$val2) {

/* decode */
        $IT = "";
        $IT = $key;
        $IT = urldecode($key);
        if ( $key <> $pkey ) { ${"x_".$IT} = array(); ${"y_".$IT} = array(); }

        array_push(${"x_".$IT}, $key2);
        settype($val2, "int");
        array_push(${"y_".$IT}, $val2);
 
        $pkey = $key;
        $x_axis = ${"x_".$IT};
        $num_x_axis = count($x_axis);

    }
    array_push($ITs, $IT);
}

    $linechart = new HighRollerLineChart();
    $linechart->chart->renderTo = 'linechart';
    $linechart->title->text = 'Transition of Index Terms';
    $linechart->subtitle = new stdClass;
    $linechart->subtitle->text = $subtitle;
    $linechart->xAxis = new stdClass;
    $linechart->xAxis->categories = $x_axis;
    $linechart->yAxis = new stdClass;
    $linechart->yAxis->min = new stdClass;
    $linechart->yAxis->min = 0;
    $linechart->yAxis->title = new stdClass;
    $linechart->yAxis->title->text = '# of Index Terms';

foreach ($ITs as $key=>$IT) {

    $x = "x_".$IT;
    $y = "y_".$IT;
    
    ${$IT} = new HighRollerSeriesData();
    ${$IT}->addName($IT)->addData(${$y});
    $linechart->addSeries(${$IT});

}

?>
<html>
<head>
<meta charset="utf-8">
  <title>Transition Graph (IT)</title>
  <link rel="stylesheet" href="../../style.css">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
  <!-- HighRoller: set the location of Highcharts library -->
  <?php echo HighRoller::setHighChartsLocation("../../js/highcharts.js");?>
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
print "<th>New Index Terms</th>";

foreach ($x_axis as $key=>$value) {
    print "<th>$value</th>";
}
print "</tr>\n";

$sorted = sort($ITs, SORT_STRING);

foreach ($ITs as $key=>$IT) {
    print "<tr>";
    print "<colgroup span=\"1\" class=\"col_left\"></colgroup>";
    print "<colgroup span=\"$num_x_axis\" class=\"col_other\"></colgroup>";
    print "<td>$IT</td>";
    foreach (${"y_".$IT} as $key2 => $yy){
        print "<td>$yy</td>";
    }
    print "</tr>\n";
}

?>

</table> 
  
</body>
</html>

