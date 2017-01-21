<?php 
	session_start();
	
include "inc_db.php";

//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

date_default_timezone_set('America/Chicago');
$now = date("Y-m-d H:i:s");



$kazooAccountID = $_REQUEST['kazooAccountID'];
$user = $_SESSION['user'];



if (!$kazooAccountID and $user !== 'cis' and $user !== 'admin') {
	
	$url = "cis_sv_login.php";
	Header("Location: $url");
	exit();	
	
} else if (!$kazooAccountID and ($user == 'cis'  or $user == 'admin')) {
	$customer = "NO CUSTOMER SELECTED";

} else {
	$sql = "SELECT * FROM tblCustomers WHERE kazooAccountID = '{$kazooAccountID}';";
	mysql_select_db($db);
	$retval = mysql_query( $sql, $conn );  
	$row = mysql_fetch_array($retval, MYSQL_ASSOC);

	$customerID = $row['customerID'];
	$customer = $row['customer'];
}



if ($_REQUEST['newaddress']) {
	
	$did = $_REQUEST['did'];
	$siteNumber = $_REQUEST['siteNumber'];
	$newaddress = $_REQUEST['newaddress'];
	
	$subject = "New 911 Address for {$did} at site {$siteNumber}";
	$msg = "Please update the address to {$newaddress}";
	$msg = wordwrap($msg,70);
	
	
	$headers = 'From: noreply@simplevoip.us' . "\r\n" .
	'Reply-To: noreply@simplevoip.us' . "\r\n" .
	'X-Mailer: PHP/' . phpversion();
	
	$mailto = "jrobs@gecko2.com";
	$mail1 = mail($mailto, $subject, $msg, $headers);
	
	$_SESSION['msg'] = "<font color=red>Your address will be updated within 24 hours.</font>";
	$url = "cis_sv_users.php";
	Header("Location: $url");
	exit();	
}

//Get summary data
$sql = "SELECT * FROM KazooMonitor;";
mysql_select_db($db);
$retval = mysql_query( $sql, $conn );  
$row = mysql_fetch_array($retval, MYSQL_ASSOC);

$LastDeviceStatusUpdate = $row['LastDeviceStatusUpdate'];
$LastDeviceUpdate = $row['LastDeviceUpdate'];





?>
<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
	<!--<![endif]-->

	<head>
		<meta charset="utf-8">
		<title><?php echo $customer ?></title>
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Favicon -->
		
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
		
		 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {
                packages: ['corechart', 'line', 'bar']
            });
        </script>
	</head>

	<!-- body classes:  -->
	<!-- "boxed": boxed layout mode e.g. <body class="boxed"> -->
	<!-- "pattern-1 ... pattern-9": background patterns for boxed layout mode e.g. <body class="boxed pattern-1"> -->
	<!-- "transparent-header": makes the header transparent and pulls the banner to top -->
	<!-- "page-loader-1 ... page-loader-6": add a page loader to the page (more info @components-page-loaders.html) -->
	<body class=" transparent-header container-fluid  ">

		<!-- scrollToTop -->
		<!-- ================ 
		<div class="scrollToTop circle"><i class="icon-up-open-big"></i></div>
		-->
		<!-- page wrapper start -->
		<!-- ================ -->
		<div class="page-wrapper">
				<!-- header-top start -->
				<!-- classes:  -->
				<!-- "dark": dark version of header top e.g. class="header-top dark" -->
				<!-- "colored": colored version of header top e.g. class="header-top colored" -->
				<!-- ================ -->
				
				<?php 
				$sql = "select " .
								"(SELECT count(*)  FROM KazooDevices WHERE accountId = '{$kazooAccountID}' AND status='DOWN') as devicesDown," .
								"(SELECT count(*)  FROM KazooDevices WHERE accountId = '{$kazooAccountID}' AND status='UP') as devicesUp," .
								"(SELECT count(*)  FROM KazooDevices WHERE accountId = '{$kazooAccountID}' AND status = ('NOT AVAILABLE')) as devicesNA";
							mysql_select_db($db);
							$retval = mysql_query( $sql, $conn );  
							$row2 = mysql_fetch_array($retval, MYSQL_ASSOC);
							$up = $row2['devicesUp'];
							$down = $row2['devicesDown'];
							$na = $row2['devicesNA'];
				?>
				<div class="header-top dark ">
					<div class="container">
						<div class="row">
							<div class="col-xs-3 col-sm-6 col-md-9">
								<!-- header-top-first start -->
								<!-- ================ -->
								<div class="header-top-first clearfix">
									
									
									<ul class="list-inline hidden-sm hidden-xs">
									
									<li><i class="fa fa-refresh pr-5 pl-10"></i>Status updated <?php echo $LastDeviceStatusUpdate ?> CST
									</LI>
									<?php if ($user=='cis' or $user=='admin') { ?>
										
									<li>
									<a href="sv_monitor.php" class="btn btn-default btn-sm"><i class="fa fa-eye pr-10"></i> Status Board</a>
									</LI>
									<?php }
									?>
										<li><i class="fa fa-exclamation pr-5 pl-10"></i>No support issues.</li>
										<li><i class="fa fa-phone pr-5 pl-10"></i>312-796-0272</li>
									
									</ul>
								</div>
								<!-- header-top-first end -->
							</div>
							<div class="col-xs-9 col-sm-6 col-md-3">

								<!-- header-top-second start -->
								<!-- ================ -->
								<div id="header-top-second"  class="clearfix">
									
									<!-- header top dropdowns start -->
									<!-- ================ -->
									<div class="header-top-dropdown text-right">
										<div class="btn-group">
											<a href="cis_sv_login.php" class="btn btn-default btn-sm"><i class="fa fa-lock pr-10"></i> CIS Login</a>
										</div>
										
									</div>
									<!--  header top dropdowns end -->
								</div>
								<!-- header-top-second end -->
							</div>
						</div>
					</div>
				</div>
				<!-- header-top end -->			
		
			
			<!-- main-container start -->
			<!-- ================ -->
			<!--<section class="main-container ">-->
				<div class="container-fluid">
					<!-- main start -->
					<!-- ================ -->
					<div class="main">
						<img src="images/SimpleVolP125px.jpg" align=left>
					
						<!-- page-title start -->
						<!-- ================ -->
						
						
					<form class="form-inline" action="cis_sv_users2.php">
						<h1><?php echo $customer ?></h1>				
					
								
						<div class="input-group"> <span class="input-group-addon">Filter</span>
							<input id="filter" type="text" class="form-control" placeholder="Site, city, state, phone...">
						</div>	
						
						
						
						
						<a class="btn btn-sm btn-success" href="#"><?php echo $up ?> Online</a>
						<a class="btn btn-sm btn-danger" href="#"><?php echo $down ?> Offline</a> 
						<a class="btn btn-sm btn-info" href="#"><?php echo $na ?> Not Installed</a> 						
						<!-- <input type=submit value="Events 24 Hours" name=events24hours class="btn btn-sm btn-warning"></i>-->
						<?php if ($user=='cis' or $user=='admin') { ?>	
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<select class="form-control" name=kazooAccountID id=kazooAccountID >
							
							<?php
							
							$sql = "SELECT * FROM KazooAccounts ORDER BY name";
							mysql_select_db($db);
							$retval = mysql_query( $sql, $conn );  
							while($row2 = mysql_fetch_array($retval, MYSQL_ASSOC)) {
							?>	
							<option value='<?php echo $row2['accountId'] ?>' <?php if ($row2['accountId'] == $kazooAccountID)  echo " selected" ?>><?php echo $row2['name'] ?></option>
							<?php } ?>	
										
						</select>			
						<div class="form-group"> 
						<input type=submit value="Switch Customer">
						</div>
						<?php } else {?>
						<input type=hidden name=kazooAccountID value="<?php echo $kazooAccountID ?>">
						<?php } ?>
						</form>
						
											
						<p><?php echo "<B>" . $_SESSION['msg']  . "</B>"; $_SESSION['msg']=''; ?></p>
						
						<!-- page-title end -->
						

					</div>
					<!-- main end -->
				</div>
			<!--</section>
			 main-container end -->

			
			<section>

						<div class="container-fluid">
						<div class="row">

						<div class="col-md-12">	
			<!-- tabs start -->
						<!-- ================ -->
						<!-- Nav tabs -->
						
						
					<!-- section start -->
					<!-- ================ -->
					<section>

						<div class="container-fluid">
						<div class="row">

						<div class="col-md-12">	
					
						<div class="isotope-container row grid-space-0">
						
						<table class="table table-striped table-colored table-hover">
							
								<thead>
									<tr>
								
										<th>Site</th>
										<th></th>
										<th>Status</th>
										<th>Phone</th>
										<th>LNP Status</th>
										<th>Temp #</th>
										<th>Actions</th>
										
									</tr>
								</thead>
							<tbody class="searchable">
							
					<?php
						
				if (!$kazooAccountID) {
					echo "No account selected.";
				} else {
						
					
					
					if ($_REQUEST['events24hours']) {
						$st = " AND LastEvent > DATE_SUB(CURDATE(), INTERVAL 1 DAY) ";
					}
				
					
					
					$sql = "select u.*, " .
							"(select count(0) from KazooDevices where status='UP'" .  
								"and ownerId IN (select userId from KazooUsers  where last_name=u.last_name and accountId='{$kazooAccountID}')) as upDevices," .
							"(select count(0) from KazooDevices where status='DOWN'" .
								"and ownerId IN (select userId from KazooUsers  where last_name=u.last_name and accountId='{$kazooAccountID}')) as downDevices," . 
							"(select count(0) from KazooDevices where status ='NOT AVAILABLE' and name not like '%Kyocera%' "  .
								"and ownerId IN (select userId from KazooUsers  where last_name=u.last_name and accountId='{$kazooAccountID}')) as naDevices, " . 		  
							"(select city from tblOrders where orderType='NEWSITE' and siteNumber=u.last_name and customerID=(select customerID from tblCustomers where KazooAccountID='{$kazooAccountID}') limit 1) as city, " .
							"(select state from tblOrders where orderType='NEWSITE' and siteNumber=u.last_name and customerID=(select customerID from tblCustomers where KazooAccountID='{$kazooAccountID}') limit 1) as state, " . 
							"(select tempDID from tblOrders where orderType='NEWSITE' and siteNumber=u.last_name and customerID=(select customerID from tblCustomers where KazooAccountID='{$kazooAccountID}') limit 1) as tempDID, " . 
							"(select lnpstatus from tblOrders where orderType='LNP' and did=RIGHT(u.callerid,10) and customerID=(select customerID from tblCustomers where KazooAccountID='{$kazooAccountID}') limit 1) as lnpstatus, " .  	
							"(select focdate from tblOrders where orderType='LNP' and did=RIGHT(u.callerid,10) and customerID=(select customerID from tblCustomers where KazooAccountID='{$kazooAccountID}') limit 1) as focdate " .  		  	   		  							
							"from KazooUsers u where accountId='{$kazooAccountID}' and first_name not like 'X_%' group by u.last_name order by downDevices DESC, naDevices DESC, u.last_name, u.first_name";
							
					//echo $sql;
					mysql_select_db($db);
					$retval = mysql_query( $sql, $conn );  
					if(mysql_num_rows($retval) == 0)
					{
						echo "No devices found.";
					}
					else
					{
						while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
						{
							
							$cnt++;
							
							
							$upDevices = $row['upDevices'];
							$downDevices = $row['downDevices'];
							$naDevices = $row['naDevices'];
							
							$lnpstatus = $row['lnpstatus'];
							$focdate = $row['focdate'];
							
							if (!$lnpstatus) {
								$lnpstatus = "NO LNP ORDER";
							}
							if ($lnpstatus == "FOC_RECEIVED") {
								$lnpstatus .= ": <B>" . $focdate . "</B>";
							}
							if ($lnpstatus == "CLOSED") {
								$lnpstatus = "ACTIVATED: " . $focdate;
							}
							
							$timezone = $row['timezone'];
							$callerid = substr($row['callerid'],-10);
							$phone = sprintf("%s-%s-%s",
							  substr($callerid, 0, 3),
							  substr($callerid, 3, 3),
							  substr($callerid, 6));
							

							$city = $row['city'];
							$state = $row['state'];
							$temp = $row['tempDID'];
							$tempDID = sprintf("%s-%s-%s",
							  substr($temp, 0, 3),
							  substr($temp, 3, 3),
							  substr($temp, 6));
							
							$fulladdress =  $city . ", " . $state ;
							
							if ($timezone == 'America/Los Angeles') {
								$timezone = 'PST';
							}
							if ($timezone == 'America/Denver') {
								$timezone = 'MST';
							}
							if ($timezone == 'America/Chicago') {
								$timezone = 'CST';
							}
							if ($timezone == 'America/New York') {
								$timezone = 'EST';
							}
							
							
							
							
							
								
							if ($upDevices>0) {
								//registered
								$icon = "<i class='fa fa-phone-square fa-2x  text-success fa-fw'></i> ";
								
							} 
							if ($downDevices>0) {
								//not registered
								$icon = "<i class='fa fa-phone-square fa-2x text-danger fa-fw'></i> ";
								
							}  
							
							if ($naDevices>0) {
								$icon = "<i class='fa fa-question-circle fa-2x' data-toggle='tooltip' title='Not Yet Installed'></i>";
								
							}
								
							
							
							$thisSite = $row['last_name'];
							
							?>
								
							<TR data-toggle="collapse" data-target="#row_<?php echo $row['last_name'] ?>"  class="table-info" >

								<td colspan=2><B><?php echo $row['last_name'] ;?></B> - <i><?php echo $fulladdress ?></i> </td>
								<td >
								<?php if ($upDevices > 0) { ?>
								<a class="btn btn-sm btn-success" href="#" onclick='javascript:get_site_devices("<?php echo $row['last_name'] ;?>")'><?php echo $upDevices ;?></a>
								<?php } if ($downDevices > 0) { ?>
								<a class="btn btn-sm btn-danger" href="#" onclick='javascript:get_site_devices("<?php echo $row['last_name'] ;?>")'><?php echo $downDevices ;?></a>
								<?php } 
									  if ($naDevices > 0) { ?>
								<a class="btn btn-sm btn-info" href="#" onclick='javascript:get_site_devices("<?php echo $row['last_name'] ;?>")'><?php echo $naDevices ;?></a>
									  <?php } ?>
								</td>
								<td><?php echo $phone ;?></td>
								<td><?php echo $lnpstatus ;?></td>
								<!--<td ><div id="lnp_<?php echo $cnt;?>"><button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:get_lnp_status(<?php echo $callerid;?>,<?php echo $cnt;?>)'>LNP Status<i class="fa fa-phone"></i></button></div></td>-->
								<td><?php echo $tempDID ;?></td>						
								
								<td><a class='btn btn-sm  btn-primary btn-animated' data-toggle='modal' data-target='#addressModal' onclick='javascript:get_911(<?php echo $callerid;?>,<?php echo $thisSite;?>)'>911 Address <i class="fa fa-map-marker"></i></a>&nbsp;<a class='btn btn-sm  btn-primary btn-animated' data-toggle='modal' data-target='#chartModal' onclick='javascript:get_charts1("<?php echo $thisSite;?>","<?php echo $timezone;?>")'>Call Analytics <i class="fa fa-bar-chart"></i></a>&nbsp;<a  type="button" class="btn btn-sm btn-primary btn-animated" data-toggle='modal' data-target='#speedModal'  onclick='javascript:get_speedtests(<?php echo $last_name;?>)'>Speed Tests<i class="fa fa-fighter-jet"></i></a></td>
								<td class="hidden"><?php echo $callerid ;?> <?php echo $temp ;?></td>
							</tr>
							
							<tr>
								<td colspan="2"></td>
								<td colspan="4"><div id="row_<?php echo $row['last_name'] ?>" class="collapse"></div></td>
							</tr>
							
						
				
					
						
					
				<?php	
						$lastSite = $thisSite;	
						
						}
					} 	
					mysql_close($conn);
				} //if account ID	
					?>
				
						
						</tbody>
					</table>	
				</div>
				</div>
				</div>
				</div>
				
				
			</section>
			<!-- section end -->
			
	</div>
</div>
			
			
			
			
		</div>
		<!-- page-wrapper end -->

		<div class="modal fade" id="addressModal"  tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="addressModalLabel">911 Address</h4>
					</div>
					<form>
					<div class="modal-body" id="addressBody">
					
					<h3>Current 911 Registered Address:</h3>
					<div id=911></div><BR>
					<input type=hidden name=did id=did><input type=hidden name=siteNumber id=siteNumber>
					<div id="911msg">
					<BR><BR>To change this address, please enter the new address below and click the Update Address button:<BR>
					</div>
					<textarea cols=50 rows=4 name=newaddress id=newaddress></textarea>
					
					
					</div>
					
					<div class="modal-footer">
						
							<button type=button onclick='javascript:new_address()'  class="btn radius-50 btn-success btn-sm ">Update Address</button> <button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Close</button>
						
					</div>
					</form>
				</div>
			</div>
		</div>
		
		<div class="modal modal-wide fade" id="speedModal"  tabindex="-1" role="dialog" aria-labelledby="speedModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="speedModalLabel">Speed Tests</h4>
					</div>
					
					<div class="modal-body" id="speedBody">
						<div id=speed></div>
					</div>
					
					<div class="modal-footer">
						<button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Close</button>
						
					</div>
				</div>
			</div>
		</div>

		<div class="modal modal-wide fade" id="eventModal"  tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="eventModalLabel">Monitoring Events (last 10)</h4>
					</div>
					
					<div class="modal-body" id="eventBody">
						<div id=events></div>
					</div>
					
					<div class="modal-footer">
						<button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Close</button>
						
					</div>
				</div>
			</div>
		</div>

		<div class="modal modal-wide fade" id="cdrModal"  tabindex="-1" role="dialog" aria-labelledby="cdrModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="cdrModalLabel">Recent Calls</h4>
					</div>
					
					<div class="modal-body" id="cdrBody">
						<div id=cdr></div>
					</div>
					
					<div class="modal-footer">
						<button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Close</button>
						
					</div>
				</div>
			</div>
		</div>
		<div class="modal modal-wide fade" id="chartModal"  tabindex="-1" role="dialog" aria-labelledby="chartModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="chartModalLabel">Call Analytics</h4>
					</div>
					
					<div class="modal-body" id="chartBody">
						<form id=dateform>
						Start Date: <input type=date name=startdate id=startdate> End Date: <input type=date name=enddate id=enddate>
						</form>
						<ul class="nav nav-tabs style-1" role="tablist">
							<li class='active'><a href="#charts1" role="tab" data-toggle="tab"><i class="fa fa-bar-chart pr-10"></i>Charts</a></li>
							<li ><a href="#htab2" role="tab" data-toggle="tab"><i class="fa fa-table pr-10"></i>Call Records</a></li>
								
						</ul>
						<div id="myTabContent" class="tab-content">
							<div class="tab-pane active in" id="charts1">
								
									
								
									<input type=hidden name=site id=site value="">
									<input type=hidden name=timezone id=timezone value="">
									
									<select name=charttype id=charttype>
										<option value="hour">Calls by Hour</option>
									</select>
									
									<button type=button onclick='javascript:get_charts2()'  class="btn radius-50 btn-success btn-sm ">Get Charts</button>
							
								
								<div id="chartmsg"></div>
										
							</div>
						
							<div class="tab-pane fade" id="htab2">
								
									Select Phones
									<div id="selectuserlist"></div>
								
								<a class='btn btn-sm  btn-primary btn-animated'  onclick='javascript:get_cdr_new()'>Get Calls <i class="fa fa-phone"></i></a>							
							
								<div id="cdrdata"></div>
							</div>
						</div>
					</div>
					
					<div class="modal-footer">
						<button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Close</button>
						
					</div>
				</div>
			</div>
		</div>		

		
							
		
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
		<script>
		$(document).ready(function () {

				(function ($) {

					$('#filter').keyup(function () {

						var rex = new RegExp($(this).val(), 'i');
						$('.searchable tr').hide();
						$('.searchable tr').filter(function () {
							return rex.test($(this).text());
						}).show();

					})

				}(jQuery));

			});
		
		
			$('.collapse').on('show.bs.collapse', function () {
				$('.collapse.in').collapse('hide');
			});

			function get_lnp_status(val,cnt) {
				thediv = '#lnp_'+cnt;
				
				$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=get-lnp-status&did="+val,
					data: ''+val,
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}
			function get_911(val,site) {
				thediv = '#911';
				
				$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=get-911&did="+val,
					data: ''+val,
					success: function(data){
							
						
						$(thediv).html(data);
						document.getElementById('did').value = val;
						document.getElementById('siteNumber').value = site;
						
					}
				});
				
			}
			/*function get_911(val) {
				thediv = '#911';
				
				$(thediv).html('Looking up address... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				$.ajax({
					type: "POST",
					url: "http://autoprovision.simplevoip.us/webservice/get_subscriber_information?number="+val,
					//data: ''+val,
					success: function(data){
						
						code = data.Code;
						
						if (code == 'success') {
							msg = data.Data.Number + ' ' + data.Data.Street + ', ' + data.Data.City + ', ' + data.Data.State + '  ' + data.Data.ZipCode;
						} else {
							msg = "NOT FOUND! Please update this address immediately!";
						}
						$(thediv).html('<B>'+msg+'</B>');
						
					}
				});
				
			}*/
			function get_speedtests(siteNumber) {
				thediv = '#speed';
				
				$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=get-speedtests&siteNumber="+siteNumber+"&customerID=<?php echo $customerID ?>",
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}	
			function get_events(deviceId) {
				thediv = '#events';
				//alert(deviceId);
				$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=get-events&deviceId="+deviceId,
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}	
			function get_site_devices(site) {
				thediv = '#row_'+site;
				$(thediv).html('Loading Devices... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=get-site-devices&last_name="+site+"&accountId=<?php echo $kazooAccountID ?>",
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}	
			function get_cdr(did,siteNumber,ownerId) {
				thediv = '#cdr';
				
				$(thediv).html('Searching for calls... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				
				$.ajax({
					type: "GET",
					url: 'ajax_functions.php?fn=get-cdr&did='+did+'&siteNumber='+siteNumber+'&ownerId='+ownerId,
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}
			function get_cdr_new() {
				thediv = '#cdrdata';
				theList = '';
				$('#selectuserlist option:selected').each(function() {
					theList = theList + $(this).val() + ',';
				});
				

				$(thediv).html('Searching for calls... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				
				$.ajax({
					type: "GET",
					url: 'ajax_functions.php?fn=get-cdr&ownerId='+theList+'&startdate='+document.getElementById('startdate').value+'&enddate='+document.getElementById('enddate').value,
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}
			function get_userlist(last_name) {
				thediv = '#selectuserlist';
								
				$.ajax({
					type: "GET",
					url: 'ajax_functions.php?fn=get-userlist&accountId=<?php echo $kazooAccountID ?>&last_name='+last_name,
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}
			function get_charts1(site,timezone) {
				
				$('#chartModalLabel').html('Call Analytics for site '+site);
				$('#chartmsg').html('');
				$('#cdrdata').html('');
				get_userlist(site);
				document.getElementById('site').value = site;
				document.getElementById('timezone').value = timezone;
								
				
			}
			function new_address() {
				thediv = '#911msg';
				
				$(thediv).html('Sending... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=newaddress&did="+document.getElementById('did').value+"&siteNumber="+document.getElementById('siteNumber').value+"&newaddress="+document.getElementById('newaddress').value,
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}
			function get_charts2() {
				thediv = '#chartmsg';
			
				$(thediv).html('Crunching the numbers... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				
				charttype = document.getElementById('charttype').value;
				
				
				$.ajax({
					type: "GET",
					url: "chart-"+charttype+".php?charttype="+document.getElementById('charttype').value+"&site="+document.getElementById('site').value+"&timezone="+document.getElementById('timezone').value+"&startdate="+document.getElementById('startdate').value+"&enddate="+document.getElementById('enddate').value+"&account_id=<?php echo $kazooAccountID ?>",
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}
			
			
			
		</script>
						
	</body>
</html>
