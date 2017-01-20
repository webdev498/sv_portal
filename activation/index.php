<?php
$msg = $_REQUEST['msg'];
include 'inc_header.php';

$siteNumber = $_REQUEST["siteNumber"];
$customercode = $_REQUEST["customercode"];

if ($_REQUEST['customercode'])
{

	$sql = "SELECT * FROM vwOrders WHERE customercode={$customercode} AND siteNumber='{$siteNumber}' and orderType='NEWSITE'";

	mysql_select_db($db);

	$retval = mysql_query( $sql, $conn );  

	if(mysql_num_rows($retval) == 0)
	{
		$msg = "<font color=red>Site or customer code not found. Please try again.</font>";
	}
	else
	{
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		$orderID = $row['orderID'];
		
		$url = $_REQUEST['customercode'] . ".php?orderID=" . $orderID;
		Header("Location: $url");
		exit();	
		
		
	
		
	}
	

}

?>
<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
	<!--<![endif]-->

	<head>
		<meta charset="utf-8">
		<title>SimpleVoIP Activation</title>
		<meta name="description" content="Login">
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Favicon -->
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="'images/favicon.ico">

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
	<body class="no-trans   ">

		<!-- scrollToTop -->
		<!-- ================ -->
		<div class="scrollToTop circle"><i class="icon-up-open-big"></i></div>
		
		<!-- page wrapper start -->
		<!-- ================ -->
		<div class="page-wrapper">
		
			<!-- background image -->
			<div class="fullscreen-bg"></div>

			<!-- banner start -->
			<!-- ================ -->
			<div class="pv-40 light-translucent-bg">
				<div class="container">
					<div class="object-non-visible text-center" data-animation-effect="fadeInDownSmall" data-effect-delay="100">
						
						<div class="form-block center-block p-30 light-gray-bg border-clear">
							
							<h2 class="title text-left">SimpleVoIP Phone Activation</h2>
							<h4 > <font color=red>STOP! Please make sure you are using FIREFOX or INTERNET EXPLORER and you have installed the latest version of Java. Get it here: <a target=new href="http://www.java.com">www.java.com</a></font></h4>
							<P>SimpleVoIP Installation Support hotline: 312-796-0272</P>
							<form action="index.php" class="form-horizontal text-left" method=get>
								
								<div class="form-group has-feedback">
									<label for="customercode" class="col-sm-3 control-label">Customer Code</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="customercode" name="customercode" placeholder="Customer Code" required>
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="siteNumber" class="col-sm-3 control-label">Site Number</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="siteNumber" name="siteNumber" placeholder="Site Number" required>
										
									</div>
								</div>
							
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-8">
										<strong><?php echo $msg; ?></strong>
										<button type="submit" class="btn btn-group btn-default btn-animated">Continue <i class="fa fa-user"></i></button>
										
										<BR><BR><img src="images/SimpleVolP125px.jpg">
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
