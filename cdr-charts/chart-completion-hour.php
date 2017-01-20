<?php
$timezone=$_REQUEST["timezone"];
$last_name=$_REQUEST["site"];
$start_date=$_REQUEST["startdate"];
$end_date=$_REQUEST["enddate"];
$account_id=$_REQUEST["account_id"];

$db = new PDO('pgsql:dbname=cdr2db;host=sv-postgres.cilqdskq1dv5.us-east-1.rds.amazonaws.com;user=cdr2db;password=Vl37yZnf5DSg');
$query = $db->prepare("SELECT * FROM call_report4('$timezone','$last_name','$start_date','$end_date','$account_id')");
$query->execute();
$missed_calls=$query->fetch(PDO::FETCH_ASSOC);
$answered_calls=$query->fetch(PDO::FETCH_ASSOC);
$answer_speed=$query->fetch(PDO::FETCH_ASSOC);
$talk_time=$query->fetch(PDO::FETCH_ASSOC);
$chart1="";
$chart2="";$max_talk_time=$talk_time;
unset($max_talk_time['hours']);
unset($max_talk_time['accid']);
$max=max($max_talk_time);
for($x=0;$x<=23;$x++)
{
	$line1="['$x',new Date(new Date('Jan 01 2000').getTime() + ".round($answer_speed[$x])* 1000 ."),new Date(new Date('Jan 01 2000').getTime() + ".round($talk_time[$x])* 1000 .")],";
	$chart1.=$line1;
	$line2="['$x',".(float)$missed_calls[$x].",".(float)$answered_calls[$x]."],";
	$chart2.=$line2;
}
?>
    
        <div style="font:bold 20px arial"><?php echo date("d M Y",strtotime($start_date));?> - <?php echo date("d M Y",strtotime($end_date));?> </div>
        <div id="container1" style="width: 100%; height: 300px; margin: 0;"></div>
        <div id="container2" style="width: 75%; height: 300px; margin: 0 auto;"></div>
        <script language="JavaScript">
            function drawChart() {
                var data = new google.visualization.DataTable();
				var dt=new google.visualization.DateFormat({pattern: "mm:ss"});
				var dth=new google.visualization.DateFormat({pattern: "hh:mm:ss"});
                data.addColumn('string', 'Hours');
                data.addColumn('datetime', 'Answer Speed');
                data.addColumn('datetime', 'Talk Time');
                data.addRows([
                    <?php echo $chart1;?>
                ]);
				dt.format(data,1);
				var max=<?php echo $max; ?>;
				if(parseInt(max)<3600)
					dt.format(data,2);
				else
					dth.format(data,2);
                var options = {
                    'title': 'Answer Speed, Talk Time by Hour(Average)',
                    hAxis: {
                        title: 'Hour'
                    },
                    vAxis: {
                        title: parseInt(max)<3600?'Min:Sec':'Hour:Min:Sec',
						format: parseInt(max)<3600?'mm:ss':'hh:mm:ss'
                    },
                    'height': 300,
                };
                var chart = new google.visualization.LineChart(document.getElementById('container1'));
                chart.draw(data, options);
            }
           function drawChart2() {
                var data = google.visualization.arrayToDataTable([
                    ['Hour', 'Missed Calls', 'Answered Calls'],
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
                    'height': 300,
                    colors: ['#d95f02', '#1b9e77']
                };
                var chart = new google.charts.Bar(document.getElementById('container2'));
                chart.draw(data, options);
            }
            google.charts.setOnLoadCallback(drawChart);
            google.charts.setOnLoadCallback(drawChart2);
        </script>
  