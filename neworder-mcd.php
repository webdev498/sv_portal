<?php include 'inc_header.php';
include 'inc_email.php';
	

	if ($_REQUEST['customerID']) //adding order
	{
		
		$conn = mysql_connect($dbhost, $dbuser, $dbpass);

		if(! $conn )
		{
			die('Could not connect: ' . mysql_error());
		}
		
		
		
		$sql = "INSERT INTO tblOrders (orderType, orderStatus, createdate, duedate,  requestedby, customerID, cisTicket,  locationID,  notes,  updateemail, customerEmail, did, billProfile) VALUES ('{$_REQUEST['orderType']}', 'PENDING', now(), DATE_ADD(now(), INTERVAL 3 DAY), '{$_REQUEST['requestedby']}', {$_REQUEST['customerID']}, '{$_REQUEST['cisTicket']}', {$_REQUEST['locationID']},  '{$_REQUEST['notes']}','{$_REQUEST['updateemail']}','{$_REQUEST['customerEmail']}','{$_REQUEST['did']}','--NONE--')";
	
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		if(! $retval )
		{
		  die('Could not add location: ' . mysql_error());
		}	
		
		
		
		//get the new order just added
		$sql = "select * from tblOrders order by orderID DESC limit 1";
		$retval = mysql_query( $sql, $conn );
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		
		$orderID = $row['orderID'];
		$_SESSION['msg'] = "<B>Order ID: {$orderID} created successfully.</B>";
		$orderID = $row['orderID'];
		$orderType = $row['orderType'];
		$updateemail = $row['updateemail'];
		$customerEmail = $row['customerEmail'];
		$customer = $row['customer'];
		$locationID = $row['locationID'];
		
		$notes = $row['notes'];
		$did = $row['did'];
		
		$msg = "***New SimpleVoIP Order ***\nOrder: {$orderID}\nType: {$orderType}\nCustomer: {$customer}\nSite: {$siteNumber}\nNotes: {$notes}\n\n\nhttp://orders.simplevoip.us/orderdetails.php?orderID={$orderID}\n";
		$msg = wordwrap($msg,70);
		$subject = "New SV Order {$_REQUEST['orderID']}: {$customer}";
				
		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		mail('orders@simplevoip.us', $subject, $msg, $headers);
		//mail($updateemail, $subject, $msg, $headers);
		
		email_order_update($orderID, true, true, true ); //update, customer, cis

		
		$url = "orderdetails.php?orderID={$orderID}";
		
		Header("Location: $url");
		exit();		
			
		
	} else if (!$_REQUEST['orderID']) {
		$_SESSION['msg'] = "<font color=red><b>***No OrderID Selected***</b></font>";
		$url = "orders.php";
		
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
		<title>SimpleVoIP - Move/Change/Disconnect</title>
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">


		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Favicon -->
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	

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
		
		<link href="plugins/jquery.countdown/jquery.countdown.css" rel="stylesheet">
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
	<body class="no-trans   ">

		<!-- scrollToTop -->
		<!-- ================ -->
		<div class="scrollToTop circle"><i class="icon-up-open-big"></i></div>
		
		<!-- page wrapper start -->
		<!-- ================ -->
		<div class="page-wrapper">
		
			<!-- breadcrumb start -->
			<!-- ================ -->
			<div class="breadcrumb-container">
				<div class="container">
					<ol class="breadcrumb">		
					<li><img src="images/SimpleVolP125px.jpg"></li>
					<li><i class="fa fa-phone pr-10"></i><a href="orders.php">Orders</a></li>
						<li><i class="fa fa-sign-out pr-10"></i><a href="index.php?action=logout">Logout</a></li>
					</ol>
				</div>
			</div>
			<!-- breadcrumb end -->	

			<!-- banner start -->
			<!-- ================ -->
			<div class="pv-40 light-translucent-bg">
				<div class="container">
					<div class="object-non-visible text-center" data-animation-effect="fadeInDownSmall" data-effect-delay="100">
					
						<div class="form-block center-block p-30 light-gray-bg border-clear">
							<?php
								//get the details from the referring order
								$sql = "select * from vwOrders where orderID={$_REQUEST['orderID']}";
								mysql_select_db($db);
								$retval = mysql_query( $sql, $conn );
								$row = mysql_fetch_array($retval, MYSQL_ASSOC);
							?>		
						
							<h2 class="title text-left">Move/Change/Disconnect</h2>
							<form action="neworder-mcd.php" class="form-horizontal text-left">
							<div class="form-group has-feedback">
									<label for="customerID" class="col-sm-3 control-label">Customer</label>
									<div class="col-sm-8">
										<?php echo $row['customer'] ?>
										<input type=hidden name=customerID value="<?php echo $row['customerID'] ?>">
									</div>
								</div>
							
							<div class="form-group has-feedback">
									<label for="orderType" class="col-sm-3 control-label">Order Type</label>
									<div class="col-sm-8">
										<select name=orderType id=orderType required>
										<option value=''>--Please Select--</option>
											<option value='MOVE'>Move Location</option>
											<option value='CHANGE'>Change Order</option>
											<option value='DISCONNECT'>Disconnect Order</option>
											
										
										</select>
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="locationID" class="col-sm-3 control-label">Location <a class='btn btn-sm  btn-success btn-animated' data-toggle='modal' data-target='#addressModal' onclick="javascript:get_location_info()">Edit Location <i class="fa fa-map-marker"></i></a></label>
									
									<div class="col-sm-8" id=locationDiv>
									<select name=locationID id=locationID required>
									<option value=''>--Please Select--</option>
									<?php
									$sql = "SELECT * FROM tblCustomerLocations where customerID='{$row['customerID']}' ORDER BY siteNumber";
									mysql_select_db($db);
									$retval2 = mysql_query( $sql, $conn );  
									while($row2 = mysql_fetch_array($retval2, MYSQL_ASSOC)) {
									?>	
										
										
											<option value='<?php echo $row2['locationID'] ?>' <?php if (($row['locationID']) == ($row2['locationID'])) {echo " selected";} ?>><?php echo $row2['siteNumber']  . " - " . $row2['streetNumber'] . " " . $row2['street'] . " - " . $row2['suite'] .", ". $row2['city'] . ", " . $row2['state'] . " " . $row2['zip']?></option>
									<?php } ?>	
										
										</select>
									</div>
								</div>
								
								<div class="form-group has-feedback">
									<label for="did" class="col-sm-3 control-label">Phone Number</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="did" name="did" value="<?php echo $row['did'] ?>">
										
									</div>
								</div>
	
								<div class="form-group has-feedback">
									<label for="cisTicket" class="col-sm-3 control-label">CIS Ticket</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="cisTicket" name="cisTicket" >
										
									</div>
								</div>
								
								
							
								
								<div class="form-group has-feedback">
									<label for="requestedby" class="col-sm-3 control-label">Requested By</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="requestedby" name="requestedby"  required>
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="updateemail" class="col-sm-3 control-label">Internal Update Email</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="updateemail" name="updateemail" >
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="customerEmail" class="col-sm-3 control-label">Customer Email</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="customerEmail" name="customerEmail" >
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="notes" class="col-sm-3 control-label">Order Details</label>
									<div class="col-sm-8">
										<textarea  class="form-control" id="notes" name="notes" rows=5 ></textarea>
										
									</div>
								</div>
								
								
								
														
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-8">
									
										<button type="submit" class="btn btn-group btn-default btn-animated">Create Order <i class="fa fa-phone"></i></button>
										<a href="orders.php"  class="btn btn-group btn-warning btn-animated">Cancel <i class="fa fa-phone"></i></a>
										
										
									</div>
								</div>
							</form>
							
						</div>

						
					</div>
				</div>
			</div>
			<!-- banner end -->


			
		</div>
		<!-- page-wrapper end -->

		<!-- JavaScript files placed at the end of the document so the pages load faster -->
		<!-- ================================================== -->
		<!-- Jquery and Bootstap core js files -->
		<script type="text/javascript" src="plugins/jquery.min.js"></script>
		<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>

		<!-- Modernizr javascript -->
		<script type="text/javascript" src="plugins/modernizr.js"></script>

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

		<!-- Count Down javascript -->
		<script type="text/javascript" src="plugins/jquery.countdown/jquery.plugin.js"></script>
		<script type="text/javascript" src="plugins/jquery.countdown/jquery.countdown.js"></script>
		<script type="text/javascript" src="js/coming.soon.config.js"></script>

		<!-- Initialization of Plugins -->
		<script type="text/javascript" src="js/template.js"></script>

		<!-- Custom Scripts -->
		<script type="text/javascript" src="js/custom.js"></script>
	</body>
</html>
