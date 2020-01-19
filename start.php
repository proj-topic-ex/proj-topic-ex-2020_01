<?php
require_once('../../HighRoller/HighRoller.php');
require_once('../../HighRoller/HighRollerSeriesData.php');
require_once('../../HighRoller/HighRollerColumnChart.php');

$pnum = 0;
$ydata = array();
$a = array();

/* Connect SQL server */
$conn_id = mysqli_connect("localhost", "user", "password") or die ('Error connecting to mySQL server');

/* Select DB */
mysqli_select_db( $conn_id, 'ca');
 
/* Select the number of articles */
$query = "select pub_year, count(an) as cnt from htc_20160208 where pub_year between 1987 and 2015 group by pub_year order by pub_year asc";
$result = mysqli_query($conn_id, $query) or die ($query . ' failed (' . mysql_error() . ')');

/* Set data to array */
while ($row = mysqli_fetch_object($result)) {
 $pnum = $row->cnt;
 settype($pnum, "int");
 array_push($ydata, $pnum );
 array_push($a, $row->pub_year );
}
mysqli_free_result($result);

/* Disconnect DB */
mysqli_close($conn_id);

/* Graph */
    $linechart = new HighRollerColumnChart();
    $linechart->chart->renderTo = 'linechart';
    $linechart->title->text = 'No. of publications related to "High Temperature Superconductor" and "phonon"';

    $linechart->xAxis = new stdClass;
    $linechart->xAxis->categories = $a;

    $linechart->yAxis = new stdClass;
    $linechart->yAxis->categories = $ydata;
    $linechart->yAxis->min = new stdClass;
    $linechart->yAxis->min = 0;
    $linechart->yAxis->tickInterval = 10;
    $linechart->yAxis->title = new stdClass;
    $linechart->yAxis->title->text = '# of Publications';
  $series = new HighRollerSeriesData();
  $series->addName('publications')->addData($ydata);
  $linechart->addSeries($series);

?>

<html>
<head>
<meta charset="utf-8">
  <title>Welcome</title>
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

<h3>Index Terms:</h3>
<h2>
<form action = "it.php" method="post" target="_blank">
    Period:    <input type="text" name="fyear" value = "2001"> - <input type="text" name="eyear" value = "2014"><br>
    Background:     <input type="text" name="bgyear1" value = "1995"> -  <input type="text" name="bgyear2" value = "2000"><br>
    Step Value:     <input type="text" name="step" value = "2"><br>
<input type="submit" name="exec" value="View Trend" style="font-size:large">
</form>
</h2>
<hr>
<h3>Abstract:</h3>
<h2>
<form action = "abs/abs.php" method="post" target="_blank">
    Period:    <input type="text" name="fyear" value = "2001"> - <input type="text" name="eyear" value = "2014"><br>
    Background:     <input type="text" name="bgyear1" value = "1995"> -  <input type="text" name="bgyear2" value = "2000"><br>
    Step Value:     <input type="text" name="step" value = "2"><br>
<input type="submit" name="exec" value="View Trend of words in Absracts" style="font-size:large">
</h2>
</body>
</html>
