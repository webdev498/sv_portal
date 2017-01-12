<?php include 'inc_header.php';
?>
<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
	<!--<![endif]-->

	<head>
		<meta charset="utf-8">
		<title>SimpleVoIP Orders</title>
	
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
					
						<li><i class="fa fa-sign-out pr-10"></i><a href="login.php?action=logout">Logout</a></li>
					</ol>
				</div>
			</div>
			<!-- breadcrumb end -->	
			
			<!-- main-container start -->
			<!-- ================ -->
			<section class="main-container padding-bottom-clear">
				<div class="container">
					<!-- main start -->
					<!-- ================ -->
					<div class="main">
						<h1>SimpleVoIP Orders</h1>
						<!-- page-title start -->
						<!-- ================ -->
						<form action="neworder.php" class="form-horizontal text-left">
							<select name=customer id=customer>
								<option value='Windsor'>Windsor</option>
								<option value='selectcomfort'>Select Comfort</option>
							
							</select>
							<select name=ordertype id=ordertype>
								<option value='add'>Add</option>
								<option value='disconnect'>Disconnect</option>
								<option value='move'>Move</option>
								<option value='change'>Change</option>
							
							</select>
							<button type="submit" class="btn btn-group btn-sm btn-default btn-animated">New Order <i class="fa fa-phone"></i></button>
						</form>
						
						
						<p><?php echo $_SESSION['msg']; $_SESSION['msg']=''; ?></p>
						<div class="separator-2"></div>
						<!-- page-title end -->
						

					</div>
					<!-- main end -->
				</div>
			</section>
			<!-- main-container end -->

			<?php
				

				  
				  
				   $sql = "SELECT * FROM tblOrders ORDER BY orderID";
				  
				   mysql_select_db($db);
				   $retval = mysql_query( $sql, $conn );
				   
				   if(! $retval )
				   {
					  die('Could not get data: ' . mysql_error());
				   }

			   
			?>
			
			
			
			<!-- section start -->
			<!-- ================ -->
			<section>
				<div class="container">
			<div class="row">

				<div class="col-md-6">	
			
				<div class="isotope-container row grid-space-0">
				<table class="table table-hover">
					<thead>
						<tr>
							
							<th>Order ID</th>
							<th>Customer</th>
							<th>Site</th>
							<th>Due</th>
							<th>Status</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
					{
				 
					?>
					<tr>
						<td><a href='orderDetail.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>	
						<td><?php echo $row['customer'] ;?></td>
						<td><?php echo $row['siteNumber'] ;?></td>
						<td><?php echo $row['dueDate'] ;?></td>
						<td><?php echo $row['status'] ;?></td>						
					</tr>
				<?php
					}
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
		<!-- page-wrapper end -->

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
