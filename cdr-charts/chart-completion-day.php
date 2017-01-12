<?php
$timezone=$_REQUEST["timezone"];
$last_name=$_REQUEST["site"];
$start_date=$_REQUEST["startdate"];
$end_date=$_REQUEST["enddate"];
$account_id=$_REQUEST["account_id"];

$db = new PDO('pgsql:dbname=cdr2db;host=sv-postgres.cilqdskq1dv5.us-east-1.rds.amazonaws.com;user=cdr2db;password=Vl37yZnf5DSg');
$query = $db->prepare("SELECT * FROM call_report5('$timezone','$last_name','$start_date','$end_date','$account_id')");
$query->execute();
$missed_calls=$query->fetch(PDO::FETCH_ASSOC);
$answered_calls=$query->fetch(PDO::FETCH_ASSOC);
$answer_speed=$query->fetch(PDO::FETCH_ASSOC);
$talk_time=$query->fetch(PDO::FETCH_ASSOC);
$chart1="";
$chart1_1="";
$chart2="";
$dow=array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
$max_talk_time=$talk_time;
unset($max_talk_time['hours']);
unset($max_talk_time['accid']);
$max=max($max_talk_time);
for($x=0;$x<=6;$x++)
{
	$y=strtoupper($dow[$x]);
	$line1="['".$dow[$x]."',new Date(new Date('Jan 01 2000').getTime() + ".round($answer_speed[$y],3)* 1000 .")],";
	$chart1.=$line1;
	$line1_1="['".$dow[$x]."',new Date(new Date('Jan 01 2000').getTime() + ".round($talk_time[$y],3) * 1000 .")],";
	$chart1_1.=$line1_1;
	$line2="['".$dow[$x]."',".(float)$missed_calls[$y].",".(float)$answered_calls[$y]."],";
	$chart2.=$line2;
}
?>
   
        <div style="font:bold 20px arial"><?php echo date("d M Y",strtotime($start_date));?> - <?php echo date("d M Y",strtotime($end_date));?> </div>
        <div id="container1" style="width: 50%; height: 300px; margin: 0;float:left"></div>
        <div id="container1_1" style="width: 50%; height: 300px; margin: 0;float:left"></div>
        <div id="container2" style="width: 75%; height: 300px; margin: 0 auto;clear:both"></div>
        <script language="JavaScript">
            function drawChart() {
				var dt=new google.visualization.DateFormat({pattern: "ss.SS"});
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Day');
                data.addColumn('datetime', 'Answer Speed');
                data.addRows([
                    <?php echo $chart1;?>
                ]);
				dt.format(data,1);
                var options = {
                    'title': 'Answer Speed by Day(Average)',
                    hAxis: {
                        title: 'Day '
                    },
                    vAxis: {
                        title: 'Sec.MS',
						format: 'ss.SS'
                    },
                    'height': 300,
                };
                var chart = new google.visualization.LineChart(document.getElementById('container1'));
                chart.draw(data, options);
            }
            function drawChart1() {
				var dt=new google.visualization.DateFormat({pattern: "mm:ss"});
				var dth=new google.visualization.DateFormat({pattern: "hh:mm:ss"});
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Days of Week');
                data.addColumn('datetime', 'Talk Time');
                data.addRows([
                    <?php echo $chart1_1;?>
                ]);
				var max=<?php echo $max; ?>;
				if(parseInt(max)<3600)
					dt.format(data,1);
				else
					dth.format(data,1);
                var options = {
                    title: 'Talk Time by Day(Average)',
                    hAxis: {
                        title: 'Day'
                    },
                    vAxis: {
                        title: parseInt(max)<3600?'Min:Sec':'Hour:Min:Sec',
						format: parseInt(max)<3600?'mm:ss':'hh:mm:ss'
                    },
                    height: 300,
                    colors: ['#1b9e77']
                };
                var chart = new google.visualization.LineChart(document.getElementById('container1_1'));
                chart.draw(data, options);
            }
           function drawChart2() {
                var data = google.visualization.arrayToDataTable([
                    ['Day of Week', 'Missed Calls', 'Answered Calls'],
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
                    height: 300,
                    colors: ['#d95f02', '#1b9e77']
                };
                var chart = new google.charts.Bar(document.getElementById('container2'));
                chart.draw(data, options);
            }
            google.charts.setOnLoadCallback(drawChart);
            google.charts.setOnLoadCallback(drawChart1);
            google.charts.setOnLoadCallback(drawChart2);
        </script>
    