<?php include "inc_db.php";
?>
<!DOCTYPE html>

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
	<!--<![endif]-->

	<head>
		<meta charset="utf-8">
		<title>SimpleVoIP - LNP Order Approval</title>
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
		
			
			<!-- banner start -->
			<!-- ================ -->
			<div class="pv-40 light-translucent-bg">
				<div class="container">
					<div class="object-non-visible text-center" data-animation-effect="fadeInDownSmall" data-effect-delay="100">
					
						<div class="form-block center-block p-30 light-gray-bg border-clear">
							<center><img src="images/SimpleVolP125px.jpg"></center>
							<h2 class="title text-left">New Port In - Customer Approval</h2>
							
							<?php  


								if ($_REQUEST['orderID'] AND $_REQUEST['approval'] == 'true') //approving LNP order
								{
									
									
									$now = date("Y-m-d");	
									$notes = "\nLNP Order APPROVED by email click on {$now}.\n";
									
									$sql = "UPDATE tblOrders SET  notes = concat(notes,'{$notes}'), orderStatus='IN PROGRESS' WHERE orderID={$_REQUEST['orderID']}";
								
									mysql_select_db($db);
									$retval = mysql_query( $sql, $conn );
									if(! $retval )
									{
									  die('FAILURE: ' . mysql_error());
									}	
									
									
									
									//get the new order just added
									$sql = "select * from tblOrders WHERE orderID = {$_REQUEST['orderID']}";
									$retval = mysql_query( $sql, $conn );
									$row = mysql_fetch_array($retval, MYSQL_ASSOC);
									
									$orderID = $row['orderID'];
									$orderType = $row['orderType'];
									$updateemail = $row['updateemail'];
									$customerEmail = $row['customerEmail'];
									$customer = $row['customer'];
									$siteNumber = $row['siteNumber'];
									$notes = $row['notes'];
									$did = $row['did'];
									$cisTicket = $row['cisTicket'];
									
									
									$msg = "***LNP ORDER APPROVED BY CUSTOMER***\nOrder: {$orderID}\nCIS Ticket: [#{$cisTicket}]\nDID: {$did}\nCustomer: {$customer}\nSite: {$siteNumber}\nNotes: {$notes}\n\n\nhttp://orders.simplevoip.us/orderdetails.php?orderID={$orderID}\n";
									$msg = wordwrap($msg,70);
									$subject = "LNP ORDER APPROVAL: {$customer}-{$did}";
											
									$headers = 'From: noreply@simplevoip.us' . "\r\n" .
									'Reply-To: noreply@simplevoip.us' . "\r\n" .
									'X-Mailer: PHP/' . phpversion();
									
									mail("jrobs@gecko2.com", $subject, $msg, $headers);
									mail($updateemail, $subject, $msg, $headers);
									
									//to CIS
									$cisEmail = "helpdesk@cisvpn.com";
									mail($cisEmail, $subject, $msg, $headers);
										
									echo "Thank you. Your order to port {$did} will be processed shortly. Please contact your project manager with questions.<BR><BR>";
									
								} else
								{
									echo "Oops! Please try again.";
								}

							?>
							
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
