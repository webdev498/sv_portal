<?php include 'inc_header.php';

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

date_default_timezone_set('America/Chicago');

$accountID = $_REQUEST['accountID'];
$customerID = $_REQUEST['customerID'];

$customerID = 1;
$customer = "Select Comfort";
$accountID = "a9cd96bca19556cd48a1bdca232ea095";



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


	

?>
<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
	<!--<![endif]-->

	<head>
		<meta charset="utf-8">
		<title>SimpleVoIP Phones - <?php echo $customer ?></title>
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Favicon -->
		
		<!-- Web Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,500,500italic,700,700italic' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Raleway:700,400,300' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=PT+Serif' rel='stylesheet' type='text/css'>

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
	<body class="no-trans container-fluid  ">

		<!-- scrollToTop -->
		<!-- ================ -->
		<div class="scrollToTop circle"><i class="icon-up-open-big"></i></div>
		
		<!-- page wrapper start -->
		<!-- ================ -->
		<div class="page-wrapper">
			
		
			
			<!-- main-container start -->
			<!-- ================ -->
			<section class="main-container padding-bottom-clear">
				<div class="container-fluid">
					<!-- main start -->
					<!-- ================ -->
					<div class="main">
						<h1>SimpleVoIP Phones: <?php echo $customer ?></h1>
						<!-- page-title start -->
						<!-- ================ -->
						<?php 
						echo "<B>" . $_SESSION['msg'] . "</B>"; 
						$msg = "";
						?>
						
						
					<form action="cis_sv_users.php">
										
					
								
						Filter by Site, Phone or City: <input type=text name=searchtext id=searchtext>
						<input type=submit value="Search">
						
						</form>
						
												
						<p><?php echo $_SESSION['msg']; $_SESSION['msg']=''; ?></p>
						<div class="separator-2"></div>
						<!-- page-title end -->
						

					</div>
					<!-- main end -->
				</div>
			</section>
			<!-- main-container end -->

			
			<section>

						<div class="container-fluid">
						<div class="row">

						<div class="col-md-12">	
			<!-- tabs start -->
						<!-- ================ -->
						<!-- Nav tabs -->
						
						<!-- Tab panes -->
						<div class="tab-content">
							<div class="tab-pane fade in active" id="htab1">
					<!-- section start -->
					<!-- ================ -->
					<section>

						<div class="container-fluid">
						<div class="row">

						<div class="col-md-12">	
					
						<div class="isotope-container row grid-space-0">
						<table class="table  table-hover">
							<thead>
								<tr>
							
							<th>Site</th>
							<th>Device</th>
							<th>Status</th>
							
							<th>Phone</th>
							<th>LNP Status</th>
							<th>Temp #</th>
							<th>MAC</th>
							<th>Actions</th>
							
							
							
						</tr>
					</thead>
					<tbody>
					<?php
						
						
	
					$sql = "* FROM vwDeviceStatus WHERE accountId = '{$accountID}';";
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
							//get status
							$LastRegistered = $row['LastRegistered'];
							$monitored = $row['monitored'];
							$enabled = $row['enabled'];
							if (!$LastRegistered) 	{
								//never registed
								$icon = "fa fa-question-circle-o ";
								
							} else {
								$icon = "fa fa-signal text-success ";
							}
							
							
							
					?>
						<tr>
							<td><?php echo $row['last_name'] ;?></td>
							<td><?php echo $row['name'] ;?></td>
							<td class="text-center"><i class="<?php echo $icon ?>"></i></td>
							
							<td><?php //echo $row['ExternalCallerId'] ;?></td>
							<td class="text-center"><div id="lnp_1"><button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:get_lnp_status(7034904144,1)'>LNP Status</button></div></td>
							<td><?php// echo $row['ExternalCallerId'] ;?></td>						
							<td><?php //echo $row['MacAddress'] ;?></td>
							<td><a class='btn btn-sm  btn-primary btn-animated' data-toggle='modal' data-target='#addressModal'>911 Address <i class="fa fa-map-marker"></i></a><a href=''  type="button" class="btn btn-sm btn-success btn-animated">Recent Calls<i class="fa fa-phone"></i></a> <a  type="button" class="btn btn-sm btn-info btn-animated" data-toggle='modal' data-target='#speedModal'  onclick='javascript:get_speedtests(11111,1)'>Speed Tests<i class="fa fa-fighter-jet"></i></a></td>
						</tr>
					
					
				
						<?php }
					}
					mysql_close($conn);
					
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
					<form action="cis_sv_users.php" method=get>
					<div class="modal-body" id="addressBody">
					
					<h3>Current 911 Registered Address:</h3>
					<B>123 Main St #123, Chicago, IL 60661</B><BR>
					
					<BR><BR>To change this address, please enter the new address below and click the Update Address button:<BR>
					<textarea cols=50 rows=4 name=newaddress id=newaddress></textarea>
					
					<input type=hidden name=siteNumber id=siteNumber value="123<?php //echo $row['siteNumber'] ?>">
					<input type=hidden name=did id=did value="3125551212<?php //echo $row['did'] ?>">
					
					</div>
					
					<div class="modal-footer">
						<button type=submit  class="btn radius-50 btn-success btn-sm ">Update Address</button> <button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Cancel</button>
						
					</div>
					</form>
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="speedModal"  tabindex="-1" role="dialog" aria-labelledby="speedModalLabel" aria-hidden="true">
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
			function get_speedtests(siteNumber, customerID) {
				thediv = '#speed';
				
				$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=get-speedtests&siteNumber="+siteNumber+"&customerID="+customerID,
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}	
			
		</script>
						
	</body>
</html>
