<?php include 'inc_header.php';
include 'inc_email.php';

	$orderID = $_REQUEST['orderID'];

	$mailto = "josh.robbins@cisvpn.com";
	
	
	if ($_REQUEST['update'] == 'true') {
	
		$notes = mysql_escape_string($_REQUEST['notes']);
		$sql = "UPDATE tblOrders SET duedate = '{$_REQUEST['duedate']}', billProfile = '{$_REQUEST['billProfile']}', requestedby = '{$_REQUEST['requestedby']}', customerID = {$_REQUEST['customerID']}, siteNumber = '{$_REQUEST['siteNumber']}', customerEmail = '{$_REQUEST['customerEmail']}', locationID = {$_REQUEST['locationID']}, orderStatus = '{$_REQUEST['orderStatus']}', cisTicket = '{$_REQUEST['cisTicket']}', did = '{$_REQUEST['did']}', tempDID = '{$_REQUEST['tempDID']}', didType = '{$_REQUEST['didType']}', handsets = '{$_REQUEST['handsets']}', updateemail = '{$_REQUEST['updateemail']}' WHERE orderID = {$_REQUEST['orderID']}";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		$_SESSION['msg'] = "<B>Order ID {$_REQUEST['orderID']} updated successfully.</B>";
		
		//Send update  email_order_udpate($orderID, $bUpdate, $bCustomer, $bCIS)
		if ($_REQUEST['email_update'] == 'true') {
			email_order_update($orderID, true, false, true ); 
		}
		
		$url = "orderdetails.php?orderID={$orderID}";
		Header("Location: $url");
		exit();	
	}
	if ($_REQUEST['action'] == 'directory') {
		email_directory_listing($orderID);
	}
	if ($_REQUEST['action'] == 'provisioned') {
	
		$now = date("Y-m-d");
		$newnotes = "\n***Order status updated to PROVISIONED on {$now}***";
		
		$sql = "UPDATE tblOrders SET orderStatus = 'PROVISIONED', notes = concat(notes,'{$newnotes}') WHERE orderID = {$_REQUEST['orderID']}";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );

		$_SESSION['msg'] = "<B>Order ID {$_REQUEST['orderID']} updated successfully to status=PROVISIONED.</B>";
		
		//Send update  email_order_udpate($orderID, $bUpdate, $bCustomer, $bCIS)
		//$test = email_order_update($orderID, true, false, true );
		
		$url = "orderdetails.php?orderID={$orderID}";
		Header("Location: $url");
		exit();	
	}	
	if ($_REQUEST['action'] == 'cancel') {
	
		$now = date("Y-m-d");
		$newnotes = "\n***Order status updated to CANCELED on {$now}***";
		
		$sql = "UPDATE tblOrders SET orderStatus = 'CANCELED', lnpstatus = 'CANCELED', notes = concat(notes,'{$newnotes}') WHERE orderID = {$_REQUEST['orderID']}";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );

		$msg = "<B>Order ID {$_REQUEST['orderID']} updated successfully to status=CANCELED.</B>";
		$_SESSION['msg'] = $msg;
		//Send update  email_order_udpate($orderID, $bUpdate, $bCustomer, $bCIS)
		$test = email_order_update_single($orderID, true, false, true, $msg );
		
		$url = "orderdetails.php?orderID={$orderID}";
		Header("Location: $url");
		exit();	
	}		
	if ($_REQUEST['action'] == 'activate') {
	
		$now = date("Y-m-d");
		
		$activationDate = $_REQUEST['activationDate'];
		
		
		$newnotes = "\n***Order status updated to ACTIVATED on {$activationDate}***";
		
		$sql = "UPDATE tblOrders SET orderStatus = 'ACTIVATED', activateDate = '{$activationDate}', directoryCompleteDate = '{$now}', notes = concat(notes,'{$newnotes}') WHERE orderID = {$_REQUEST['orderID']}";
		//echo $sql;
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		$msg = "<B>Order ID {$_REQUEST['orderID']} updated successfully to status=ACTIVATED.</B>";
		$_SESSION['msg'] = $msg;
		//Send update  email_order_udpate($orderID, $bUpdate, $bCustomer, $bCIS)
		$test = email_order_update_single($orderID, true, false, true, $msg );
		
		if (is_null($directoryCompleteDate) ) {
			email_directory_listing($orderID);
		}
		
		$url = "orderdetails.php?orderID={$orderID}";
		Header("Location: $url");
		exit();	
	}
	if ($_REQUEST['action'] == 'billed') {
	
		//get h2o auto billing info
		if ($_REQUEST['status'] == '1') {
			$billNotes = "\nAUTO added to H2O. CustomerID {$_REQUEST['customerid']}";
		}
	
		$now = date("Y-m-d");;
		$newnotes = "\n***Order status updated to BILLED on {$now}***" . $billNotes ;
		
		$sql = "UPDATE tblOrders SET orderStatus = 'BILLED', billed='{$now}', notes = concat(notes,'{$newnotes}') WHERE orderID = {$_REQUEST['orderID']}";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		$_SESSION['msg'] = "<B>Order ID {$_REQUEST['orderID']} updated successfully to status=BILLED.</B>";
		
				
		$url = "orders.php";
		Header("Location: $url");
		exit();	
	}		
	
	if ($_REQUEST['action'] == 'approvalemail') {
	
		
		
		//Send approval email
		$sql = "select * from vwOrders where orderID = {$_REQUEST['orderID']}";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		
		$orderID = $row['orderID'];
		$updateemail = $row['updateemail'];
		$customerEmail = $row['customerEmail'];
		$customer = $row['customer'];
		$siteNumber = $row['siteNumber'];
		$notes = $row['notes'];
		$did = $row['did'];
		$tempDID = $row['tempDID'];
		$orderStatus = $row['orderStatus'];
		$orderType = $row['orderType'];
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];
		$zip = $row['zip'];
		$now = date("Y-m-d");;
		$activationDate = $row['activateDate'];
		
		$subject = "SimpleVoIP LNP Approval Needed";
		$msg = "Dear customer:\n\nWe have received a request to port the following phone number to our service. This process takes approximately 10 business days from the time you approve the order by clicking the link below. You will be updated by our system when the status changes and once the number is successfully ported you should call your old provider and cancel service to avoid being billed for inactive services.\n\n";
		$msg .= "Customer: {$customer}\nPhone Number: {$did}\nSite: {$siteNumber}\nAddress: {$address}, {$city}, {$state} {$zip}\n\n";
		$msg .= "To approve this port request, please click this link or paste it into your browser: http://orders.simplevoip.us/lnp-approval.php?orderID={$orderID}&approval=true\n\nIf you have any questions, please call or email your project manager.";
			
		$_SESSION['msg'] = "Approval email sent to {$customerEmail}";
		
		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		
		mail($customerEmail, $subject, $msg, $headers);
		
		$newnotes = "\nApproval email sent to customer ({$customerEmail}) on {$now}.\n";
		
		$sql = "UPDATE tblOrders SET notes = concat(notes,'{$newnotes}') WHERE orderID = {$_REQUEST['orderID']}";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		$url = "orders.php";
		Header("Location: $url");
		exit();	
	}



	

	$sql = "select * from vwOrders where orderID = {$_REQUEST['orderID']}";
	mysql_select_db($db);
	$retval = mysql_query( $sql, $conn );
	$row = mysql_fetch_array($retval, MYSQL_ASSOC);
	
	$orderID = $row['orderID'];
	$updateemail = $row['updateemail'];
	$customerEmail = $row['customerEmail'];
	$customer = $row['customer'];
	$customerID = $row['customerID'];
	
	$notes = $row['notes'];
	$did = $row['did'];
	$tempDID = $row['tempDID'];
	$orderStatus = $row['orderStatus'];
	$orderType = $row['orderType'];
	
	$now = date("Y-m-d");
	$activateDate = $row['activateDate'];
	$locationID = $row['locationID'];
	$siteNumber = $row['siteNumber'];
	$address = $row['address'];
	$city = $row['city'];
	$state = $row['state'];
	$zip = $row['zip'];
?>
<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
	<!--<![endif]-->

	<head>
		<meta charset="utf-8">
		<title>SimpleVoIP Order Detail</title>
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
						

									
							<h2 class="title text-left">Order <?php echo $row['orderID'] ?></h2>
							
							<?php echo $_SESSION['msg'] ;
								$_SESSION['msg'] = '';
								echo $_SESSION["ErrorMessage"];
								echo $_SESSION["ErrorCode"];
								
								$_SESSION["ErrorMessage"] = "";
								$_SESSION["ErrorCode"] = "";
								?>
							<form action="orderdetails.php" class="form-horizontal text-left">
							<input type=hidden value=true name=update>
							<input type=hidden value=<?php echo $row['orderID'] ?> name=orderID>
							<div class="form-group has-feedback">
									<label for="customerID" class="col-sm-3 control-label">Customer</label>
									<div class="col-sm-8">
									<select name=customerID id=customerID required>
									<?php
										$sql = "SELECT * FROM tblCustomers ORDER BY customer";
										mysql_select_db($db);
										$retval = mysql_query( $sql, $conn );  
										while($row2 = mysql_fetch_array($retval, MYSQL_ASSOC)) {
									?>	
									
										
										
											<option value='<?php echo $row2['customerID'] ?>' <?php if ($row2['customerID'] == $row['customerID'])  echo " selected" ?>><?php echo $row2['customer'] ?></option>
									<?php } ?>	
										<option value='' <?php if ($row['customerID'] == 0)  echo " selected" ?>>--Please Select--</option>
										</select>
									</div>
								</div>
							
							<div class="form-group has-feedback">
									<label for="orderStatus" class="col-sm-3 control-label">Status</label>
									<div class="col-sm-8">
										<select name=orderStatus id=orderStatus required>
										
											<option selected  value="<?php echo $row['orderStatus'] ?>"><?php echo $row['orderStatus'] ?></option>
										<?php if ($_SESSION['user'] == 'admin') { ?>										
											<option value='IN PROGRESS'>IN PROGRESS</option>
										<?php } ?>
											
										</select>
									<?php if ($_SESSION['user'] == 'admin') { ?>			
										<?php if ($row['orderStatus'] == 'IN PROGRESS' ) { ?>
										<button type=button class='btn radius-10 btn-info btn-sm' data-toggle='modal' data-target='#provisioned'>Provisioned</button>
										<?php } ?>
										<?php if ($row['orderStatus'] == 'PROVISIONED') { ?>
										<button type=button class='btn radius-10 btn-info btn-sm' data-toggle='modal' data-target='#activate'>Activate</button>
										<?php } ?>
										<?php if ($row['orderStatus'] == 'ACTIVATED' ) { ?>
										<button type=button class='btn radius-10 btn-info btn-sm' data-toggle='modal' data-target='#billed'>Billed</button>
										<?php } ?>
										
									<?php } ?>
									</div>
								</div>
								<?php if ($row['orderType'] == 'LNP') { 
								
								//Set all the variables to send to Peerless
								$pon = $customer . $siteNumber;
								$lnporderType = "PI";
								$tn = $did;
								$routeLabel = "SIMPLEVOIP_01";
								$routeLabel = "PROV_TEST";
								$mou = "1000";
								$accountNumber = $pon;
								$atn = $did;
								$authDate = date("Y-m-d");
								$authnm = $row['authname'];
								$desiredDueDate = "";
								$earliestPossible = "1";
								$endCustomerName = $customer;
								$mi = "C";
								$npdi = "D";
								$sanoHouseNumber = substr($address,0,strpos($address," "));
								$sasnStrName  = substr($address,strpos($address," "));
								$sasdStrDir = "";
								$state = $state;
								$city = $city;
								$telephoneNumber = "3129482996";
								$zipCode = $zip;
								
								$lnpURL = "orderID=" . $orderID . "&pon=" . $pon . "&orderType=" . $lnporderType . "&tn=" . $tn . 
									"&routeLabel=" . $routeLabel . "&mou=" . $mou . "&accountNumber=" . $accountNumber . "&atn=" . $atn . "&authDate=" . $authDate .
									"&authnm=" . $authnm . "&desiredDueDate=" . $desiredDueDate . "&earliestPossible=" . $earliestPossible . "&endCustomerName=" . $endCustomerName . "&mi=" . $mi .
									"&npdi=" . $npdi . "&city=" . $city . "&sanoHouseNumber=" . $sanoHouseNumber . "&sasnStrName=" . $sasnStrName . "&sasdStrDir=" . $sasdStrDir .
									"&state=" . $state . "&telephoneNumber=" . $telephoneNumber . "&zipCode=" . $zipCode;
							
								
								?>
							<script>
								function sendOrder() {
									
									$("#lnpstatus").html('Sending Order... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
									$.ajax({
									type: "POST",
									url: "api_peerless/peerless_submitorder.php",
									data:'<?php echo $lnpURL ;?>',
									success: function(data){
										
										$("#lnpstatus").html(data);
									}
									});
								}
								
								
							</script>
								<div class="form-group has-feedback">
									<label for="lnpstatus" class="col-sm-3 control-label">LNP Status</label>
									<div class="col-sm-8" id="lnpstatus">
										<B><?php echo $row['lnpstatus'] ?></B>
										<?php if (empty($row['lnpstatus']) OR $row['lnpstatus'] == 'PENDING') { ?>					
										<button type=button class='btn radius-10 btn-info btn-sm' onclick="sendOrder()">Submit LNP</button>
										<?php } ?>
										
									</div>
								</div>
								<?php } 
								
								
								?>	
								
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
									<label for="cisTicket" class="col-sm-3 control-label">CIS Ticket</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="cisTicket" name="cisTicket"  value="<?php echo $row['cisTicket'] ?>">
										
									</div>
								</div>
								
								
							
								
								<div class="form-group has-feedback">
									<label for="requestedby" class="col-sm-3 control-label">Requested By</label>
									
										
									<div class="col-sm-8">
									<input type="text" class="form-control" id="requestedby" name="requestedby" value="<?php echo $row['requestedby'] ?>">
									
										
									</div>
									
								</div>
								<div class="form-group has-feedback">
									<label for="duedate" class="col-sm-3 control-label">Due Date</label>
									<div class="col-sm-8">
									<input type="date" class="form-control" id="duedate" name="duedate" value="<?php echo $row['duedate'] ?>">
									
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="updateemail" class="col-sm-3 control-label">Update Email</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="updateemail" name="updateemail"  value="<?php echo $row['updateemail'] ?>" >
										
									</div>
								</div>
								<?php if ($row['orderType'] !== "LNP") {?>
								<div class="form-group has-feedback">
									<label for="handsets" class="col-sm-3 control-label">Handsets</label>
									<div class="col-sm-8">
										<select name=handsets id=handsets <?php if($row['orderType'] == 'NEWSITE') { echo 'required';} ?>>
										
											<option selected  value="<?php echo $row['handsets'] ?>"><?php echo $row['handsets'] ?></option>
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
										<select name=didType id=didType <?php if($row['orderType'] == 'NEWSITE') { echo 'required';} ?>>
										<option selected value="<?php echo $row['didType'] ?>"><?php echo $row['didType'] ?></option>
											<option value='PORT REQUESTED'>PORT REQUESTED</option>
											<option value='NEW NUMBER'>NEW NUMBER</option>
										
										</select>
										
									</div>
								</div>
								<?php } else {?>
								<input type=hidden name=handsets value="<?php echo $row['handsets'] ?>">
								<input type=hidden name=didType value="<?php echo $row['didType'] ?>">
								<?php } ?>
								
								<div class="form-group has-feedback">
									<label for="did" class="col-sm-3 control-label">Main Phone Number
									
									
									</label>
																		
																		
									<div class="col-sm-8">
										<input type="number" class="form-control" id="did" name="did" value="<?php echo $row['did']; ?>">
										<?php if ($_SESSION['user'] == 'admin') { ?>
									<a href='orderdetails.php?action=directory&orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-info btn-animated">Send Directory Listing<i class="fa fa-phone"></i></a>					
									
									<?php if ($row['orderType'] !== "LNP"   ) {?>
										<a href='neworder-lnp1.php?orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-info btn-animated">Port In<i class="fa fa-phone"></i></a>					
										<?php } } ?>
									
									</div>
								</div>
								<script>
								function updateCF(val) {
									$('#test').html('Updating CF... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
									$.ajax({
									type: "POST",
									url: "http://autoprovision.simplevoip.us/webservice/update_permanent_callflow?tn="+val,
									//data:'tn='+val,
									success: function(data){
										//var obj = JSON.parse(data);
										
										$('#test').html(data.Code + " " + data.Message);
										
									}
									});
								}
								</script>
								<div class="form-group has-feedback">
									<label for="tempDID" class="col-sm-3 control-label">Temporary DID</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="tempDID" name="tempDID" value="<?php echo $row['tempDID']; ?>">
										<button type=button class='btn radius-10 btn-info btn-sm' onclick="updateCF(<?php echo $row['tempDID']; ?>)">Update Call Flow</button>
									</div>
									
								</div>
								<div id="test"></div>
								<div class="form-group has-feedback">
									<label for="customerEmail" class="col-sm-3 control-label">Customer Email</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="customerEmail" name="customerEmail"  value="<?php echo $row['customerEmail']; ?>">
										
									</div>
								</div>
								<!--
								<div class="form-group has-feedback">
									<label for="did2" class="col-sm-3 control-label">DID 2</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="did2" name="did2"  value="<?php echo $row['did2'] ?>">
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="did3" class="col-sm-3 control-label">DID 3</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="did3" name="did3"  value="<?php echo $row['did3'] ?>">
										
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="did4" class="col-sm-3 control-label">DID 4</label>
									<div class="col-sm-8">
										<input type="number" class="form-control" id="did4" name="did4"  value="<?php echo $row['did4'] ?>">
										
									</div>
								</div>
								-->
								<?php if ($orderType !== "LNP") { ?>
								<div class="form-group has-feedback">
									<label for="billProfile" class="col-sm-3 control-label">Bill Profile</label>
									<div class="col-sm-8">
										<select name=billProfile id=billProfile required>
											
											<option selected  value="<?php echo $row['billProfile'] ?>"><?php echo $row['billProfile'] ?></option>
											<option value="--NONE--">--NONE--</option>
											<option value="SELECT-1-Standard">SELECT-1-Standard</option>
											<option value="WIN-1-Standard">WIN-1-Standard</option>
											<option value="WIN-2-Cordless">WIN-2-Cordless</option>
											<option value="LRH-1-Standard">LRH-1-Standard</option>
											<option value="NPC-WENDYS">NPC-WENDYS</option>
																					
										</select>
									</div>
								</div>
								<?php } ?>
								<div class="form-group has-feedback">
									<label for="notes" class="col-sm-3 control-label">Notes/Special Requests</label>
									<div class="col-sm-8">
										<?php echo $row['notes']; 
										if ($_SESSION['user'] == 'admin') {?>
										<a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a>
										<?php } ?>
									</div>
								</div>
								
								
								
														
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-8">
									
										<input type=checkbox value=true name=email_update> Send update?
										<button type="submit" class="btn btn-group btn-default btn-animated">Update Order <i class="fa fa-phone"></i></button>
									<?php if ($_SESSION['user'] == 'admin') { ?>
									<button type=button class='btn radius-10 btn-danger btn-sm' data-toggle='modal' data-target='#cancel'>Cancel</button>
										
									
									<?php } ?>	
										
									</div>
								</div>
							</form>
							
						</div>

						
					</div>
				</div>
				
				<div class="modal fade" id="notesModal"  tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
					<div class="modal-dialog" >
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title" id="notesModalLabel">Notes</h4>
							</div>
							
							<div class="modal-body" id="notesBody">
														
							</div>
							
							<div class="modal-footer">
								<div  id="notesupdate">
														
							</div>
								
							</div>
						</div>
					</div>
				</div>
			
			
			</div>
			<!-- banner end -->
			<div class="modal fade" id="provisioned" tabindex="1" role="dialog" aria-labelledby="provisionedLabel" aria-hidden="true">
				<div class="modal-dialog" >
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="provisionedLabel">Provisioned</h4>
						</div>
						<div class="modal-body">
						
						<P>You are marking this order as PROVISIONED by SimpleVoIP and ready for activation.</P>
							
							
							<a href="orderdetails.php?action=provisioned&orderID=<?php echo $row['orderID'] ?>" class="btn radius-50 btn-success btn-sm ">Mark Provisioned</a>

						</div>
						<div class="modal-footer">
							
							
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="cancel" tabindex="1" role="dialog" aria-labelledby="cancelLabel" aria-hidden="true">
				<div class="modal-dialog" >
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="provisionedLabel">Cancel Order</h4>
						</div>
						<div class="modal-body">
						
						<P>You are CANCELING this order.</P>
							
							
							<a href="orderdetails.php?action=cancel&orderID=<?php echo $row['orderID'] ?>" class="btn radius-50 btn-success btn-sm ">CANCEL</a>

						</div>
						<div class="modal-footer">
							
							
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="activate" tabindex="1" role="dialog" aria-labelledby="activateLabel" aria-hidden="true">
				<div class="modal-dialog" >
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="activateLabel">Activate Order</h4>
						</div>
						<div class="modal-body">
						<form action="orderdetails.php">
						<input type=hidden name=orderID value="<?php echo $row['orderID'] ?>">
						<input type=hidden name=action value="activate">
						<P>You are marking this order as ACTIVATED and ready to start billing on the following date:
						<input type=date id="activationDate" name="activationDate">
						<!--
						<select class="form-control" id="activationDate" name="activationDate">
						<?php
							$dueDate = date("Y-m-d");
							$newdate = $dueDate;
							for ($x = -30; $x <= -1; $x++) {
								$thisdate = date("Y-m-d",strtotime("{$x} days",strtotime($newdate)));
								?>
								<option value="<?php echo $thisdate; ?>"><?php echo $thisdate; ?></option>
								<?php
								
							}
							?>
							<option value="<?php echo $dueDate; ?>" selected><?php echo $dueDate; ?></option>
							<?php
							for ($x = 1; $x <= 5; $x++) {
								$thisdate = date("Y-m-d",strtotime("+{$x} days",strtotime($newdate)));
								?>
								<option value="<?php echo $thisdate; ?>"><?php echo $thisdate; ?></option>
								<?php
								
							}
						
						
						
						?>
					</select>--></P>
							<button type=submit class="btn radius-50 btn-success btn-sm ">Mark as ACTIVATED</button>

					
						<div class="modal-footer">
							
							
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="billed" tabindex="1" role="dialog" aria-labelledby="billedLabel" aria-hidden="true">
				<div class="modal-dialog" >
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="billedLabel">Add to Billing</h4>
						</div>
						<div class="modal-body">
						
						<P>You are marking this order as BILLED.</P>
							<?php
							if ($customerID == 1) {
								$parentAccountID = 1703;	//SELECT
							}
							if ($customerID == 2) {
								$parentAccountID = 1696;	//WINDSOR
							}
							if ($customerID == 5) {
								$parentAccountID = 2307;	//LOGANS
							}
							if ($customerID == 7) {
								$parentAccountID = 2505;	//NPC - Wendys
							}
							$companyName = "SV - " . $customer . " " . $siteNumber;
							
							$billProfile = $row['billProfile'];
							$handsets = $row["handsets"];
							
							if ($orderType == "LNP") {
								$billingURL = "api_h2o/h2o_addsingle.php?orderID=" . $orderID . "&productID=4577&amount=15&quantity=1&effectiveDate=" . $activateDate . "&tn=" . $did ;
								$label = "Add LNP to H2O";
							} else {
								$billingURL = "api_h2o/h2o_addtobilling.php?orderID=" . $orderID . "&parentAccountID=" . $parentAccountID . "&billProfile=" . $billProfile . "&companyName=" . $companyName . "&address=" . $address . "&city=" . $city . "&state=" . $state . "&zip=" . $zip . "&tn=" . $did . "&effectiveDate=" . $activateDate . "&handsets=" . $handsets ;
								$label = "Add Order to H2O";
							}
							
							if ( $row['billProfile']!=='--NONE--' AND ( $row['orderType'] == 'NEWSITE' OR $row['orderType'] == 'LNP')) {
							?>
							<a href="<?php echo $billingURL ?>" class="btn radius-50 btn-info btn-sm "><?php echo $label ?></a>
							<?php } ?>
							<a href="orderdetails.php?action=billed&orderID=<?php echo $row['orderID'] ?>" class="btn radius-50 btn-success btn-sm ">Mark as Billed (JOSH ONLY)</a>

						</div>
						<div class="modal-footer">
							
							
						</div>
					</div>
				</div>
			</div>
			

			
		</div>
		<!-- page-wrapper end -->

		<!-- JavaScript files placed at the end of the document so the pages load faster -->
		<!-- ================================================== -->
		<!--begin modal-->
		<div class="modal fade" id="addressModal"  tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="addressModalLabel">Edit Location</h4>
					</div>
					
					<div class="modal-body" id="addressBody">
						
					  <form class="form-horizontal text-left">
						<div class="form-group has-feedback">
							<label for="siteNumber" class="col-sm-3 control-label">Site Number</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="siteNumber" name="siteNumber"  required >
								
							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="streetnumber" class="col-sm-3 control-label">Street Number</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="streetnumber" name="streetnumber"  required >
								
							</div>
						</div>
						<div class="form-group has-feedback">
							<label for="street" class="col-sm-3 control-label">Street</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="street" name="street"  required >
								
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
								<input type="text" class="form-control" id="city" name="city"  required >
								
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
						<div id=locationDiv></div>
							<button type=button onclick='javascript:update_location_info()'  class="btn radius-50 btn-success btn-sm ">Update Location</button> 
							
							<button type=button class='btn radius-10 btn-danger btn-sm ' data-dismiss='modal' >Close</button>					
					</div>
					
				</div>
			</div>
		</div>
		<!--end modal-->
		
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
		
		<script>
		
		
			$('body').on('click', '.odom-submit', function (e) {
				$(this.form).submit();
				$('#lnp-submit').modal('hide');
			});
		
			function getNotes(val) {
				
				$.ajax({
				type: "POST",
				url: "get_notes.php",
				data:'orderID='+val,
				success: function(data){
					
					$("#notesBody").html(data);
					
				}
				});
			}
			function writeNotes(val) {
				
				$.ajax({
					type: "POST",
					url: "ajax_functions.php",
					data:'fn=notes&orderID='+val+'&notes='+document.getElementById('notes').value+'&email_update='+document.getElementById('email_update').checked+'&email_customer='+document.getElementById('email_customer').checked+'&email_cis='+document.getElementById('email_cis').checked,
					success: function(data){						
						$('#notesupdate').html(data);
						getNotes(val);
					}
				});
				
				
			}
		function get_location_info() {
				
				locationID = document.getElementById('locationID').value;
						
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=get_location_info&locationID="+locationID,
					dataType:'json',
					success: function(data){
						
						document.getElementById('siteNumber').value = data['siteNumber'];
						document.getElementById('streetnumber').value = data['streetnumber'];
						document.getElementById('street').value = data['street'];
						document.getElementById('suite').value = data['suite'];
						document.getElementById('city').value = data['city'];
						document.getElementById('state').value = data['state'];
						document.getElementById('zip').value = data['zip'];
						document.getElementById('email').value = data['email'];
						
					}
				});
				
			}
		function update_location_info() {
				
				thediv = '#locationDiv';
				locationID = document.getElementById('locationID').value;
				siteNumber = document.getElementById('siteNumber').value;
				streetnumber = document.getElementById('streetnumber').value;
				street = document.getElementById('street').value;
				suite = document.getElementById('suite').value;
				city = document.getElementById('city').value;
				state = document.getElementById('state').value;
				zip = document.getElementById('zip').value;	
				email = document.getElementById('email').value;	
				$(thediv).html('Updating... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				$.ajax({
					type: "GET",
					url: "ajax_functions.php?fn=update_location_info&email="+email+"&locationID="+locationID+"&siteNumber="+siteNumber+"&streetnumber="+streetnumber+"&street="+street+"&suite="+suite+"&city="+city+"&state="+state+"&zip="+zip,
					
					success: function(data){
						$(thediv).html(data);	
						
					}
				});
				
			}				
		</script>
		
	</body>
</html>
