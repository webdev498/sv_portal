<?php include 'inc_header.php';


	if ($_REQUEST['customerID']) //adding order
	{
		
		$conn = mysql_connect($dbhost, $dbuser, $dbpass);

		if(! $conn )
		{
			die('Could not connect: ' . mysql_error());
		}
		
		$cisTicket = $_REQUEST['cisTicket'];
		
		$sql = "INSERT INTO tblOrders (orderType, orderStatus, createdate, duedate, locationID, requestedby, customerID, cisTicket, did, didType, notes, handsets, updateemail, customeremail,billProfile) VALUES ('{$_REQUEST['orderType']}', 'PENDING', now(), '{$_REQUEST['duedate']}', {$_REQUEST['locationID']}, '{$_REQUEST['requestedby']}', {$_REQUEST['customerID']}, '{$cisTicket}', '{$_REQUEST['did']}',  '{$_REQUEST['didType']}',  '{$_REQUEST['notes']}', '{$_REQUEST['handsets']}', '{$_REQUEST['updateemail']}', '{$_REQUEST['customeremail']}',  '{$_REQUEST['billProfile']}')";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		if(! $retval )
		{
		  die('Could not add location: ' . mysql_error());
		}	
		
		
		
		//get the new order just added
		$sql = "select * from vwOrders order by orderID DESC limit 1";
		$retval = mysql_query( $sql, $conn );
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		
		$orderID = $row['orderID'];
		$_SESSION['msg'] = "<B>Order ID: {$orderID} created successfully. Target complete date is {$row['duedate']}.</B>";
		
		$orderID = $row['orderID'];
		$updateemail = $row['updateemail'];
		$customer = $row['customer'];
		$siteNumber = $row['siteNumber'];
		$notes = $row['notes'];
		
		$msg = "***New SimpleVoIP Order ***\n\nOrder: {$orderID}\nCustomer: {$customer}\nSite: {$siteNumber}\nNotes: {$notes}\n\n\nhttp://orders.simplevoip.us/orderdetails.php?orderID={$orderID}\n";
		$msg = wordwrap($msg,70);
		$subject = "New SV Order {$_REQUEST['orderID']}: {$customer}";
				
		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		mail('orders@simplevoip.us', $subject, $msg, $headers);
		mail($updateemail, $subject, $msg, $headers);
		
		
		$url = "orderdetails.php?orderID=" . $orderID;
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
		<title>SimpleVoIP - Add Order</title>
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
							
							<h2 class="title text-left">Add Site</h2>
							<form action="neworder-add.php" class="form-horizontal text-left">
							
							<?php if ($_SESSION['user'] == 'admin') { ?>
								<div class="form-group has-feedback">
									<label for="orderType" class="col-sm-3 control-label">Order Type</label>
									<div class="col-sm-8">
										<select name=orderType id=orderType required>
										<option value=''>--Please Select--</option>
											
											<option value='NEWSITE'>NEWSITE</option>
											
											
										
										</select>
									</div>
								</div>
							
							<?php } else { ?>
							<input type=hidden name=orderType value="NEWSITE">
							<?php } ?>
							<div class="form-group has-feedback">
									<label for="customerID" class="col-sm-3 control-label">Customer</label>
									<div class="col-sm-8">
									<select name=customerID id=customerID onchange='javascript:get_locations()' required>
									<option value=''>--Please Select--</option>
									<?php
									$sql = "SELECT * FROM tblCustomers ORDER BY customer";
									mysql_select_db($db);
									$retval = mysql_query( $sql, $conn );  
									while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
									?>	
										
										
											<option value='<?php echo $row['customerID'] ?>'><?php echo $row['customer'] ?></option>
									<?php } ?>	
										
										</select>
									</div>
								</div>
							
							
								
								<div class="form-group has-feedback">
									<label for="locationID" class="col-sm-3 control-label">Location <a class='btn btn-sm  btn-success btn-animated' data-toggle='modal' data-target='#addressModal'>New Location <i class="fa fa-map-marker"></i></a></label>
									
									<div class="col-sm-8" id=locationDiv>
									<select name=locationID id=locationID required>
										<option value=''>--Please Select--</option>
									</select>
									</div>
								</div>
								
								<div class="form-group has-feedback">
									<label for="cisTicket" class="col-sm-3 control-label">CIS Ticket</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="cisTicket" name="cisTicket" >
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="duedate" class="col-sm-3 control-label">Due Date</label>
									<div class="col-sm-8">
									<input type="date" class="form-control" id="duedate" name="duedate" ">
									
										
									</div>
								</div>
								
							
								
								<div class="form-group has-feedback">
									<label for="requestedby" class="col-sm-3 control-label">Requested By</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="requestedby" name="requestedby"  >
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="updateemail" class="col-sm-3 control-label">Update Email</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="updateemail" name="updateemail"  >
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="handsets" class="col-sm-3 control-label">Handsets</label>
									<div class="col-sm-8">
										<select name=handsets id=handsets required>
											<option value=''>--Please Select--</option>
											<option value='Not Needed'>Not Needed</option>
											<option value='In Stock'>In Stock at CIS</option>
											<option value='New Handsets - Ship to CIS'>New Handsets - Ship to CIS</option>
											<option value='New Handsets - Drop Ship to Customer'>New Handsets - Drop Ship to Customer</option>	
										</select>
									</div>
								</div>
								
								<div class="form-group has-feedback">
									<label for="didType" class="col-sm-3 control-label">DID Type</label>
									<div class="col-sm-8">
										<select name=didType id=didType required>
										<option value=''>--Please Select--</option>
										<option value='PORT REQUESTED'>PORT REQUESTED</option>
											<option value='NEW NUMBER'>NEW NUMBER</option>
										
										</select>
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="did" class="col-sm-3 control-label">Phone Number</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="did" name="did" >
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="customerEmail" class="col-sm-3 control-label">Customer Email</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="customerEmail" name="customerEmail" >
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="billProfile" class="col-sm-3 control-label">Bill Profile</label>
									<div class="col-sm-8">
										<select name=billProfile id=billProfile required>
											
											
											<option value="--NONE--">--NONE--</option>
											<option value="SELECT-1-Standard">SELECT-1-Standard</option>
											<option value="WIN-1-Standard">WIN-1-Standard</option>
											<option value="WIN-2-Cordless">WIN-2-Cordless</option>
											<option value="LRH-1-Standard">LRH-1-Standard</option>	
											<option value="NPC-WENDYS">NPC-WENDYS</option>											
										</select>
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="notes" class="col-sm-3 control-label">Notes/Special Requests</label>
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
		<div class="modal fade" id="addressModal"  tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="addressModalLabel">New Location</h4>
					</div>
					
					<div class="modal-body" id="addressBody">
						
					  <form class="form-horizontal text-left">
						<div class="form-group has-feedback">
							<label for="siteNumber" class="col-sm-3 control-label">Site Number</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="siteNumber" name="siteNumber" required >
								
							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="streetnumber" class="col-sm-3 control-label">Street Number</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="streetnumber" name="streetnumber" required >
								
							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="street" class="col-sm-3 control-label">Street</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="street" name="street" required >
								
							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="suite" class="col-sm-3 control-label">Suite</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="suite" name="suite"  >
								
							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="city" class="col-sm-3 control-label">City</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="city" name="city" required >
								
							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="state" class="col-sm-3 control-label">State</label>
							<div class="col-sm-8">
								
								<select name="state" id=state>
								
									
									<option value="AL">AL</option>
									<option value="AK">AK</option>
									<option value="AZ">AZ</option>
									<option value="AR">AR</option>
									<option value="CA">CA</option>
									<option value="CO">CO</option>
									<option value="CT">CT</option>
									<option value="DE">DE</option>
									<option value="DC">DC</option>
									<option value="FL">FL</option>
									<option value="GA">GA</option>
									<option value="HI">HI</option>
									<option value="ID">ID</option>
									<option value="IL">IL</option>
									<option value="IN">IN</option>
									<option value="IA">IA</option>
									<option value="KS">KS</option>
									<option value="KY">KY</option>
									<option value="LA">LA</option>
									<option value="ME">ME</option>
									<option value="MD">MD</option>
									<option value="MA">MA</option>
									<option value="MI">MI</option>
									<option value="MN">MN</option>
									<option value="MS">MS</option>
									<option value="MO">MO</option>
									<option value="MT">MT</option>
									<option value="NE">NE</option>
									<option value="NV">NV</option>
									<option value="NH">NH</option>
									<option value="NJ">NJ</option>
									<option value="NM">NM</option>
									<option value="NY">NY</option>
									<option value="NC">NC</option>
									<option value="ND">ND</option>
									<option value="OH">OH</option>
									<option value="OK">OK</option>
									<option value="OR">OR</option>
									<option value="PA">PA</option>
									<option value="RI">RI</option>
									<option value="SC">SC</option>
									<option value="SD">SD</option>
									<option value="TN">TN</option>
									<option value="TX">TX</option>
									<option value="UT">UT</option>
									<option value="VT">VT</option>
									<option value="VA">VA</option>
									<option value="WA">WA</option>
									<option value="WV">WV</option>
									<option value="WI">WI</option>
									<option value="WY">WY</option>
								</select>

							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="zip" class="col-sm-3 control-label">Zip</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="zip" name="zip" required >
								
							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="email" class="col-sm-3 control-label">Site Email</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="email" name="email" required >
								
							</div>
						</div>
					  </form>
										
					</div>
					
					<div class="modal-footer">
						<div id=addbutton>
							<button type=button onclick='javascript:add_location()'  class="btn radius-50 btn-success btn-sm ">Add Location</button> 
							</div>
							<button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Close</button>
						
					</div>
					</form>
				</div>
			</div>
		</div>
		<!-- JavaScript files placed at the end of the document so the pages load faster -->
		<!-- ================================================== -->
		<script>
		function add_location() {
				
				customerID = document.getElementById('customerID').value;
				
				if (!customerID) {
					alert("Please select a customer first!");
					return;
				}
				thediv = '#addbutton';
				siteNumber = document.getElementById('siteNumber').value;
				streetnumber = document.getElementById('streetnumber').value;
				street = document.getElementById('street').value;
				suite = document.getElementById('suite').value;
				city = document.getElementById('city').value;
				state = document.getElementById('state').value;
				zip = document.getElementById('zip').value;
				email = document.getElementById('email').value;
				
				$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=new-location&email="+email+"&customerID="+customerID+"&siteNumber="+siteNumber+"&streetnumber="+streetnumber+"&street="+street+"&suite="+suite+"&city="+city+"&state="+state+"&zip="+zip,
					
					success: function(data){
							
						
						$(thediv).html(data);
						
						get_locations();
						
					}
				});
				
			}
			function get_locations() {
				thediv = '#locationDiv';
				
				
				$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=get-locations&customerID="+document.getElementById('customerID').value,
					
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}
		</script>
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
