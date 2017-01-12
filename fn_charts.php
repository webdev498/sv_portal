<?php

$timezone=$_REQUEST["timezone"];
$last_name=$_REQUEST["sitelist"];
$start_date=$_REQUEST["startdate"];
$end_date=$_REQUEST["enddate"];
$account_id=$_REQUEST["account_id"];


$db = new PDO('pgsql:dbname=cdr2db;host=sv-postgres.cilqdskq1dv5.us-east-1.rds.amazonaws.com;user=cdr2db;password=Vl37yZnf5DSg');
$query = $db->prepare("SELECT * FROM call_report2('$timezone','$last_name','$start_date','$end_date','$account_id')");
$query->execute();
$total_calls=$query->fetch(PDO::FETCH_ASSOC);
$avg_calls=$query->fetch(PDO::FETCH_ASSOC);
$inbound=$query->fetch(PDO::FETCH_ASSOC);
$outbound=$query->fetch(PDO::FETCH_ASSOC);
$chart1="";
$chart2="";
$chart3="";
$chart4="";
for($x=0;$x<=23;$x++)
{
	$line1="['$x',".(float)$avg_calls[$x].",".(float)$inbound[$x].",".(float)$outbound[$x]."],";
	$chart1.=$line1;
	$line2="['$x',".(float)$total_calls[$x].",".(float)$avg_calls[$x]."],";
	$chart2.=$line2;
	$line3="['$x',".(float)$total_calls[$x]."],";
	$chart3.=$line3;
	$line4="['$x',".(float)$avg_calls[$x]."],";
	$chart4.=$line4;
}


?>