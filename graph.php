<?php
include ("jpgraph/jpgraph.php");
include ("jpgraph/jpgraph_bar.php");

$ydata = array();
$a = array();

/* Connect SQL server */
$conn_id = mysql_connect("localhost", "user", "password") or die ('Error connecting to mySQL server');

/* Select DB */
mysql_select_db("ca", $conn_id);
 
/* Select the number of articles */
$query = "select pub_year, count(an) as cnt from htc_20160208 where pub_year between 1987 and 2014 group by pub_year order by pub_year asc";
$result = mysql_query($query, $conn_id) or die ($query . ' failed (' . mysql_error() . ')');

/* Set data to array */
while ($row = mysql_fetch_object($result)) {
 array_push($ydata, $row->cnt );
 array_push($a, $row->pub_year );
}
mysql_free_result($result);

/* Disconnect DB */
mysql_close($conn_id);

/* Graph */
$graph = new Graph(700, 500, "auto");
$graph->SetScale("textlin");
$graph->img->SetMargin(40, 30, 20, 40);



$graph->xaxis->SetTickLabels($a);
$graph->xaxis->SetTextLabelInterval(5);
$graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 16);

$graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 16);

$bplot = new BarPlot($ydata);

$graph->Add($bplot);

$graph->Stroke();
?>