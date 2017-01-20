<?php
$timezone="CST";
$last_name="301";
$start_date="2016-11-10";
$end_date="2016-11-20";
$account_id="5899e36e47b8db450bf2ccd384dd259f";

$timezone=$_REQUEST["timezone"];
$last_name=$_REQUEST["site"];
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
        <div style="font:bold 20px arial"><?php echo date("d M Y",strtotime($start_date));?> - <?php echo date("d M Y",strtotime($end_date));?> </div>
        <div id="container1" style="width: 50%; height: 300px; margin: 0 auto;float:left"></div>
        <div id="container2" style="width: 50%; height: 300px; margin: 0 auto;float:left"></div><hr>
        <div id="container3" style="width: 50%; height: 300px; margin: 0 auto;float:left;clear:both"></div>
        <div id="container4" style="width: 50%; height: 300px; margin: 0 auto;float:left"></div>
        <script language="JavaScript">
            function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Hours');
                data.addColumn('number', 'Avg Calls');
                data.addColumn('number', 'Outbound');
                data.addColumn('number', 'Inbound');
                data.addRows([
                    <?php echo $chart1;?>
                ]);
                var options = {
                    'title': 'Inbound/Outbound Calls by Hour',
                    hAxis: {
                        title: 'Hour'
                    },
                    'width': 550,
                    'height': 300,
                };
                var chart = new google.visualization.LineChart(document.getElementById('container1'));
                chart.draw(data, options);
            }
            function drawChart2() {
                var data = google.visualization.arrayToDataTable([
                    ['Hour', 'Total Calls', 'Avg Calls'],
                    <?php echo $chart2;?>
                ]);
                var options = {
                    chart: {
                        title: 'Calls by Hour',
                    },
                    bars: 'vertical',
                    hAxis: {
                        title: 'Hour'
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
                    ['Hour', 'Total Calls'],
                    <?php echo $chart3;?>
                ]);
                var options = {
                    chart: {
                        title: 'Total Calls by Hour',
                    },
                    bars: 'vertical',
                    hAxis: {
                        title: 'Hour'
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
                    ['Hour', 'Avg Calls'],
                    <?php echo $chart4;?>
                ]);
                var options = {
                    chart: {
                        title: 'Avg Calls by Hour',
                    },
                    bars: 'vertical',
                    hAxis: {
                        title: 'Hour'
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
  