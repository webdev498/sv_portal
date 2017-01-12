<?php 
	session_start();
	
include "inc_db.php";

//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

date_default_timezone_set('America/Chicago');
$now = date("Y-m-d H:i:s");





?>
<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" >
	<!--<![endif]-->

	<head>
		<meta http-equiv="refresh" content="60;url=sv_monitor.php" />
		<meta charset="utf-8">
		<title>SimpleVoIP Phone Status</title>
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Favicon -->
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
		
		<!-- Web Fonts -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,500,500italic,700,700italic' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Raleway:700,400,300' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=PT+Serif' rel='stylesheet' type='text/css'>

		<!-- Bootstrap core CSS -->
		<link href="bootstrap/css/bootstrap.css" rel="stylesheet">

		<!-- Font Awesome CSS -->
		<link href="fonts/font-awesome/css/font-awesome.css" rel="stylesheet">

		<!-- Fontello CSS -->
		<link href="fonts/fontello/css/fontello.css" rel="stylesheet">

		<!-- Plugins -->
		<link href="plugins/magnific-popup/magnific-popup.css" rel="stylesheet">
		<link href="css/animations.css" rel="stylesheet">
		<link href="plugins/owl-carousel/owl.carousel.css" rel="stylesheet">
		<link href="plugins/owl-carousel/owl.transitions.css" rel="stylesheet">
		<link href="plugins/hover/hover-min.css" rel="stylesheet">		

		<!-- the project core CSS file -->
		<link href="css/style.css" rel="stylesheet" >

		<!-- Color Scheme (In order to change the color scheme, replace the blue.css with the color scheme that you prefer)-->
		<link href="css/skins/light_blue.css" rel="stylesheet">

		<!-- Custom css --> 
		<link href="css/custom.css" rel="stylesheet">
	</head>

	<!-- body classes:  -->
	<!-- "boxed": boxed layout mode e.g. <body class="boxed"> -->
	<!-- "pattern-1 ... pattern-9": background patterns for boxed layout mode e.g. <body class="boxed pattern-1"> -->
	<!-- "transparent-header": makes the header transparent and pulls the banner to top -->
	<!-- "page-loader-1 ... page-loader-6": add a page loader to the page (more info @components-page-loaders.html) -->
	<body class=" transparent-header container-fluid " onload="startTime()">

		<!-- scrollToTop -->
		<!-- ================ 
		<div class="scrollToTop circle"><i class="icon-up-open-big"></i></div>
		-->
		<!-- page wrapper start -->
		<!-- ================ -->
		<div class="page-wrapper ">
			<div class="header-top dark ">
				<div class="container-fluid">
					<div class="row">
						<div class=" col-md-12">
							<!-- header-top-first start -->
							<!-- ================ -->
							<div class="header-top-first clearfix">
								<ul class="list-inline hidden-sm hidden-xs">
									<li><div id="txt"></div></LI>
									<li><i class="fa fa-exclamation pr-5 pl-10"></i>No issues.</li>
								</ul>
							</div>
							<!-- header-top-first end -->
						</div>
					</div>
				</div>
			</div>
			<!-- header-top end -->			

				<div class="container-fluid ">
					<div class="row">
						<div class="col-md-8">	
									
							
							<!-- section start -->
							<!-- ================ -->
							
								<table class="table">
									
										<thead>
											<tr>
										
												<th>Customer</th>
												
												<th align=center >UP</th>
												<th align=center >DOWN</th>
												<th align=center >In Errors</th>
												<th align=center >In 404</th>
												<th align=center >Not Installed</th>
												<th align=center ></th>
												<th align=center >Open Orders</th>
												<th align=center >Open LNP</th>
												<th align=center >Closed LNP</th>
												
												
											</tr>
										</thead>
									<tbody>
							<?php
								
						
							
							
							$sql = "SELECT * FROM vwMonitor where name not like 'X_%' and name not like '%Kyocera%' ORDER BY devicesDown DESC, cdrEventsRecovery DESC, cdrEventsUnallocated DESC;";
							
							mysql_select_db($db);
							$retval = mysql_query( $sql, $conn );  
							
								while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
								{
									
									$cnt++;
									
									$clDown = "success";
									$clUp = "success";
									$clNA = "success";
									$clCDRrecovery = "success";
									$clCDRunallocated = "success";
									
									//get status
									$name = $row['name'];
									$devicesDown = $row['devicesDown'];
									$devicesUp = $row['devicesUp'];
									$devicesNA = $row['devicesNA'];
									$cdrEventsRecovery = $row['cdrEventsRecovery'];
									$cdrEventsUnallocated = $row['cdrEventsUnallocated'];
									
									if ($devicesDown > 0) {
										$clDown = "danger";
									}
									if ($devicesUp > 0) {
										$clUp = "success";
									}
									if ($devicesNA > 0) {
										$clNA = "info";
									}
									if ($cdrEventsRecovery > 0) {
										$clCDRrecovery = "danger";
									}
									if ($cdrEventsUnallocated > 0) {
										$clCDRunallocated = "danger";
									}
									
									
									
									
								?>
									
						
							
								<tr>
									<td><h3><?php echo $row['name'] ;?></td>
									<td align=center class="<?php echo $clUp ?>"><h3><a href="cis_sv_users.php?kazooAccountID=<?php echo $row['accountId']?>&submitonline=true"><?php echo $row['devicesUp'] ;?></a></h3></td>
									<td align=center class="<?php echo $clDown ?>"><h3><a href="cis_sv_users.php?kazooAccountID=<?php echo $row['accountId']?>&submitunregistered=true"><?php echo $row['devicesDown'] ;?></h3></a></td>
									<td align=center class="<?php echo $clCDRrecovery ?>"><h3><a data-toggle='modal' data-target='#errorModal' onclick='javascript:get_errors("RECOVERY","<?php echo $row['accountId'];?>")'><?php echo $row['cdrEventsRecovery'] ;?></a></h3></td>
									<td align=center class="<?php echo $clCDRunallocated ?>"><h3><a data-toggle='modal' data-target='#errorModal' onclick='javascript:get_errors("UNALLOCATED","<?php echo $row['accountId'];?>")'><?php echo $row['cdrEventsUnallocated'] ;?></a></h3></td>

									<td align=center class="<?php echo $clNA ?>"><h3><?php echo $row['devicesNA'] ;?></h3></td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td align=center ><h3><?php echo $row['openOrders'] ;?></h3></td>
									<td align=center ><h3><?php echo $row['openLNP'] ;?></h3></td>
									<td align=center ><h3><?php echo $row['closedLNP'] ;?></h3></td>

								</tr>
							
						<?php	
								
								}
							
							
						
							?>
						
								</tbody>
							</table>							
					
					<!-- section end -->
				</div>
				<div class="col-md-4">
					<?php
						
						//CDR Data
						$dbconn = pg_connect("host=sv-postgres.cilqdskq1dv5.us-east-1.rds.amazonaws.com port=5432 dbname=cdr2db user=cdr2db password=Vl37yZnf5DSg");
						$sql = "select * from stats order by datetime desc limit 1";
						$retval = pg_query($dbconn, $sql);
						
						$row = pg_fetch_array($retval);
						$lastCDRupdate = $row['datetime'];
						$lastCDRcount = $row['imported_cdrs'];
						pg_close($dbconn);
						
						
						
						//Sync Stats
						$sql = "SELECT *, (select count(0) from KazooStatusEvents where type LIKE 'INBOUND ERROR%' and  eventDate >= date_sub(current_time,INTERVAL 1 DAY)) as inboundEvents, " .
							"(select count(0) from KazooStatusEvents where type LIKE 'OUTBOUND ERROR%' and eventDate >= date_sub(current_time,INTERVAL 1 DAY)) as outboundEvents, " .
							"(select count(0) from KazooStatusEvents where type='DOWN' and eventDate >= date_sub(current_time,INTERVAL 1 DAY)) as downEvents, " .
							"(select count(0) from KazooStatusEvents where type='UP' and eventDate >= date_sub(current_time,INTERVAL 1 DAY)) as upEvents, " .  
							"(select count(0) from vwNeedsActivation) as needsActivation   FROM KazooMonitor WHERE id=1";
						//echo $sql;
						mysql_select_db($db);
						$retval = mysql_query( $sql, $conn );
						if(! $retval )
						   {
							  die('Could not get data: ' . mysql_error());
						   }
						$row2 = mysql_fetch_array($retval, MYSQL_ASSOC);
					   
						$LastDeviceUpdate = $row2['LastDeviceUpdate'] ;
						$LastDeviceStatusUpdate = $row2['LastDeviceStatusUpdate'] ;
						$LastEventProcess = $row2['LastEventProcess'] ;
						$userCount = $row2['userCount'] ;
						$deviceCount = $row2['deviceCount'] ;
						$billableDeviceCount =  $row2['billableDeviceCount'];
						$LastCDREventProcess = $row2['LastCDREventProcess'] ;
						$inboundEvents = $row2['inboundEvents'] ;
						$outboundEvents = $row2['outboundEvents'] ;
						$upEvents = $row2['upEvents'] ;
						$downEvents = $row2['downEvents'] ;
						$needsActivation = $row2['needsActivation'] ;
						
						
						
						
					?>
					<table class="table">
					
						<thead>
							<tr>
								<th>Stat</th>
								<th align=center >Value</th>
							</tr>
							<tr>
								<td>Events 24h UP/DN</td>
								<td class="<?php echo $clUp ?>"><?php echo $upEvents . "/" . $downEvents  ?></td>
							</tr>
							<tr>
								<td>Call Events 24h IN/OUT</td>
								<td class="<?php echo $clUp ?>"><?php echo $inboundEvents . "/" . $outboundEvents  ?></td>
							</tr>
							<tr>
								<td>Last CDR Update</td>
								<td class="<?php echo $clUp ?>"><?php echo substr($lastCDRupdate,0,16)  ?></td>
							</tr>
							<tr>
								<td>Last CDR Count</td>
								<td class="<?php echo $clUp ?>"><?php echo $lastCDRcount ?></td>
							</tr>
							<tr>
								<td>Last Device Sync</td>
								<td class="<?php echo $clUp ?>"><?php echo substr($LastDeviceUpdate,0,16) ?></td>
							</tr>
							<tr>
								<td>Last Device Status</td>
								<td class="<?php echo $clUp ?>"><?php echo substr($LastDeviceStatusUpdate,0,16) ?></td>
							</tr>
							<tr>
								<td>Last Event Process</td>
								<td class="<?php echo $clUp ?>"><?php echo substr($LastEventProcess,0,16) ?></td>
							</tr>
							<tr>
								<td>Kazoo Users</td>
								<td class="<?php echo $clUp ?>"><?php echo $userCount ?></td>
							</tr>
							<tr>
								<td>Kazoo Devices</td>
								<td class="<?php echo $clUp ?>"><?php echo $deviceCount ?></td>
							</tr>
							<tr>
								<td>Billable devices</td>
								<td class="<?php echo $clUp ?>"><?php echo $billableDeviceCount ?></td>
							</tr>
							<tr>
								<td>Last CDR Event Process</td>
								<td class="<?php echo $clUp ?>"><?php echo substr($LastCDREventProcess,0,16) ?></td>
							</tr>
							<tr>
								<td>Needs Activation</td>
								<td class="<?php if($needsActivation>0) { echo $clUp;} else {echo $clDown;} ?>"><?php echo $needsActivation ?></td>
							</tr>
						</thead>
						<tbody>
						
						</tbody>
					</table>
	
				</div>	<!--end column-->
					
			</div>
		</div>
	</div>
			
			<?php 
			mysql_close($conn);
			
			?>
			
		<!-- MODAL for ERROR CALLS -->	
		<div class="modal fade" id="errorModal"  tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="errorModalLabel">Error Calls</h4>
					</div>
					<form>
					<div class="modal-body" id="errorBody">
					
					
					
					</div>
					
					<div class="modal-footer">
						
						<button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Close</button>
						
					</div>
					</form>
				</div>
			</div>
		</div>	
			
		</div>
		<!-- page-wrapper end -->
		<script>
		function startTime() {
			var today = new Date();
			var h = today.getHours();
			var m = today.getMinutes();
			var s = today.getSeconds();
			m = checkTime(m);
			s = checkTime(s);
			document.getElementById('txt').innerHTML =
			h + ":" + m + ":" + s + ' CST';
			var t = setTimeout(startTime, 500);
		}
		function checkTime(i) {
			if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
			return i;
		}
		function get_events(type,accountId) {
			thediv = '#errorBody';
			
			
			$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
			
			$.ajax({
				type: "GET",
				url: "ajax_functions.php?fn=get-errors&type="+type+'&accountId='+accountId,
				
				success: function(data){
						
					$(thediv).html(data);
					
				}
			});
			
		}
		</script>
	
		
		<!-- JavaScript files placed at the end of the document so the pages load faster -->
		<!-- ================================================== -->
		<!-- Jquery and Bootstap core js files -->
		<script type="text/javascript" src="plugins/jquery.min.js"></script>
		<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>

		<!-- Modernizr javascript -->
		<script type="text/javascript" src="plugins/modernizr.js"></script>

		<!-- Isotope javascript -->
		<script type="text/javascript" src="plugins/isotope/isotope.pkgd.min.js"></script>
		
		<!-- Magnific Popup javascript -->
		<script type="text/javascript" src="plugins/magnific-popup/jquery.magnific-popup.min.js"></script>
		
		<!-- Appear javascript -->
		<script type="text/javascript" src="plugins/waypoints/jquery.waypoints.min.js"></script>

		<!-- Count To javascript -->
		<script type="text/javascript" src="plugins/jquery.countTo.js"></script>
		
		<!-- Parallax javascript -->
		<script src="plugins/jquery.parallax-1.1.3.js"></script>

		<!-- Contact form -->
		<script src="plugins/jquery.validate.js"></script>

		<!-- Owl carousel javascript -->
		<script type="text/javascript" src="plugins/owl-carousel/owl.carousel.js"></script>
		
		<!-- SmoothScroll javascript -->
		<script type="text/javascript" src="plugins/jquery.browser.js"></script>
		<script type="text/javascript" src="plugins/SmoothScroll.js"></script>

		<!-- Initialization of Plugins -->
		<script type="text/javascript" src="js/template.js"></script>

		<!-- Custom Scripts -->
		<script type="text/javascript" src="js/custom.js"></script>
	
						
	</body>
</html>
