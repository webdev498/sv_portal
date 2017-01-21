<?php
//test

$timezone=$_REQUEST["timezone"];
$last_name=$_REQUEST["site"];
$start_date=$_REQUEST["startdate"];
$end_date=$_REQUEST["enddate"];
$account_id=$_REQUEST["account_id"];

$db = new PDO('pgsql:dbname=cdr2db;host=sv-postgres.cilqdskq1dv5.us-east-1.rds.amazonaws.com;user=cdr2db;password=Vl37yZnf5DSg');
$query = $db->prepare("SELECT * FROM call_report3('$timezone','$last_name','$start_date','$end_date','$account_id')");
$query->execute();
$total_calls=$query->fetch(PDO::FETCH_ASSOC);
$avg_calls=$query->fetch(PDO::FETCH_ASSOC);
$inbound=$query->fetch(PDO::FETCH_ASSOC);
$outbound=$query->fetch(PDO::FETCH_ASSOC);
$chart1="";
$chart2="";
$chart3="";
$chart4="";
$dow=array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
for($x=0;$x<=6;$x++)
{
	$y=strtoupper($dow[$x]);
	$line1="['".$dow[$x]."',".(float)$avg_calls[$y].",".(float)$inbound[$y].",".(float)$outbound[$y]."],";
	$chart1.=$line1;
	$line2="['".$dow[$x]."',".(float)$total_calls[$y].",".(float)$avg_calls[$y]."],";
	$chart2.=$line2;
	$line3="['".$dow[$x]."',".(float)$total_calls[$y]."],";
	$chart3.=$line3;
	$line4="['".$dow[$x]."',".(float)$avg_calls[$y]."],";
	$chart4.=$line4;
}
?>
  
        <div style="font:bold 20px arial"><?php echo date("d M Y",strtotime($start_date));?> - <?php echo date("d M Y",strtotime($end_date));?> </div>
        <div id="container1" style="width: 50%; height: 300px; margin: 0 auto;float:left"></div>
        <div id="container2" style="width: 50%; height: 300px; margin: 0 auto;float:left"></div><hr>
        <div id="container3" style="width: 50%; height: 300px; margin: 0 auto;float:left;clear:both"></div>
        <div id="container4" style="width: 50%; height: 300px; margin: 0 auto;float:left"></div>
        <script language="JavaScript">
            function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Day of Weeks');
                data.addColumn('number', 'Avg Calls');
                data.addColumn('number', 'Outbound');
                data.addColumn('number', 'Inbound');
                data.addRows([
                    <?php echo $chart1;?>
                ]);
                var options = {
                    'title': 'Inbound/Outbound Calls by Day of Week',
                    hAxis: {
                        title: 'Day of Week'
                    },
                    'width': 550,
                    'height': 300,
                };
                var chart = new google.visualization.LineChart(document.getElementById('container1'));
                chart.draw(data, options);
            }
            function drawChart2() {
                var data = google.visualization.arrayToDataTable([
                    ['Day of Week', 'Total Calls', 'Avg Calls'],
                    <?php echo $chart2;?>
                ]);
                var options = {
                    chart: {
                        title: 'Calls by Day of Week',
                    },
                    bars: 'vertical',
                    hAxis: {
                        title: 'Day of Week'
                    },
                    vAxis: {
                        title: 'Calls'
                    },
                    'width': 550,
                    'height': 300,
                    colors: ['#1b9e77', '#d95f02']
                };
                var chart = new google.charts.Bar(document.getElementById('container2'));
                chart.draw(data, options);
            }
            function drawChart3() {
                var data = google.visualization.arrayToDataTable([
                    ['Day of Week', 'Total Calls'],
                    <?php echo $chart3;?>
                ]);
                var options = {
                    chart: {
                        title: 'Total Calls by Day of Week',
                    },
                    bars: 'vertical',
                    hAxis: {
                        title: 'Day of Week'
                    },
                    vAxis: {
                        title: 'Calls'
                    },
                    'width': 550,
                    'height': 300,
                    colors: ['#1b9e77']
                };
                var chart = new google.charts.Bar(document.getElementById('container3'));
                chart.draw(data, options);
            }
            function drawChart4() {
                var data = google.visualization.arrayToDataTable([
                    ['Day of Week', 'Avg Calls'],
                    <?php echo $chart4;?>
                ]);
                var options = {
                    chart: {
                        title: 'Avg Calls by Day of Week',
                    },
                    bars: 'vertical',
                    hAxis: {
                        title: 'Day of Week'
                    },
                    vAxis: {
                        title: 'Calls'
                    },
                    'width': 550,
                    'height': 300,
                    colors: ['#7570b3', ],
                    bar: {
                        groupWidth: "75%"
                    }
                };
                var chart = new google.charts.Bar(document.getElementById('container4'));
                chart.draw(data, options);
            }
            google.charts.setOnLoadCallback(drawChart);
            google.charts.setOnLoadCallback(drawChart2);
            google.charts.setOnLoadCallback(drawChart3);
            google.charts.setOnLoadCallback(drawChart4);
        </script>
