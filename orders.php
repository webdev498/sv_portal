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
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Favicon -->
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
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
	<body class="no-trans container-fluid  ">

		<!-- scrollToTop -->
		<!-- ================ -->
		<div class="scrollToTop circle"><i class="icon-up-open-big"></i></div>
		
		<!-- page wrapper start -->
		<!-- ================ -->
		<div class="page-wrapper">
			
			<!-- breadcrumb start -->
			<!-- ================ -->
			<div class="breadcrumb-container">
				<div class="container-fluid">
				
					<ol class="breadcrumb">		
					<li><img src="images/SimpleVolP125px.jpg"></li>
					<li><i class="fa fa-phone pr-10"></i><a href="orderSummary.php">Summary</a></li>
						<li><i class="fa fa-sign-out pr-10"></i><a href="index.php?action=logout">Logout</a></li>
						<li><a href='cis_sv_users.php' target=_new type="button" class="btn btn-group btn-sm btn-info btn-animated">Phone Status <i class="fa fa-phone"></i></a></li>
						<li><a href='sv_monitor.php' target=_new type="button" class="btn btn-group btn-sm btn-success btn-animated">Monitor Board <i class="fa fa-exclamation"></i></a></li>
					</ol>
					
				</div>
			</div>
			<!-- breadcrumb end -->	
			
			<!-- main-container start -->
			<!-- ================ -->
			<section class="main-container padding-bottom-clear">
				<div class="container-fluid">
					<!-- main start -->
					<!-- ================ -->
					<div class="main">
						<h1>SimpleVoIP Orders</h1>
						<!-- page-title start -->
						<!-- ================ -->
						
						
						
					<form action="orders.php">
							<a href='neworder-add.php'  type="button" class="btn btn-group btn-sm btn-success btn-animated">New Site Order <i class="fa fa-phone"></i></a>
							
						&nbsp;
						
						<?php
							$st = ",,";						
						foreach ($_REQUEST['status'] as $selectedOption)
							
							$st .= $selectedOption.",";
							
							//echo $st;
							$siteNumber = $_REQUEST['siteNumber'];
							if ($_REQUEST['submit'] == 'reset') {
									$siteNumber = "";
							}
						?>
					
						Site/Phone Number: <input type=text id=siteNumber name=siteNumber value="<?php echo $siteNumber?>" >
						
						<select name=customerID id=customerID >
							<option value=''>--Please Select--</option>
							<?php
							$sql = "SELECT * FROM tblCustomers ORDER BY customer";
							mysql_select_db($db);
							$retval = mysql_query( $sql, $conn );  
							while($row2 = mysql_fetch_array($retval, MYSQL_ASSOC)) {
							?>	
							<option value='<?php echo $row2['customerID'] ?>' <?php if ($row2['customerID'] == $_REQUEST['customerID'])  echo " selected" ?>><?php echo $row2['customer'] ?></option>
							<?php } ?>	
										
						</select>				
						<input type=submit value="Filter"> <input type=submit value="reset" name='submit'>
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
				
				
				
				
				$siteNumber = $_REQUEST['siteNumber'];
				$customerID = $_REQUEST['customerID'];
				
				if ($siteNumber) {				
					$s = " WHERE siteNumber='{$siteNumber}' OR did='{$siteNumber}' ";
				}
				else {
					if ($customerID) {
						$s = " WHERE customerID='{$customerID}' ";
					}	
					
						
				}
				if ($_REQUEST['submit'] == 'reset') {
					$s = "";
				}
			//echo $s;
			   
				  

			   
			?>
			<section>

						<div class="container-fluid">
						<div class="row">

						<div class="col-md-12">	
			<!-- tabs start -->
						<!-- ================ -->
						<!-- Nav tabs -->
						<ul class="nav nav-tabs style-1" role="tablist">
							<li class='active'><a href="#htab1" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>ALL</a></li>
							<li ><a href="#htab2" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>PENDING</a></li>
							<li><a href="#htab3" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>IN PROGRESS</a></li>
							<li ><a href="#htab4" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>PROVISIONED</a></li>
							<li><a href="#htab5" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>ACTIVATED</a></li>
							<li><a href="#htab6" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>BILLED</a></li>
							
							<li><a href="#htab8" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>CNAM TO DO</a></li>
							<li><a href="#htab10" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>FLAGGED</a></li>
							
							<li><a href="#htab15" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>TO PORT</a></li>
							<li><a href="#htab7" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>LNP ALL</a></li>
							<li><a href="#htab11" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>LNP ISSUES</a></li>
							<li><a href="#htab12" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>LNP-PENDING</a></li>
							<li><a href="#htab13" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>LNP-IN PROGRESS</a></li>
							<li><a href="#htab14" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>LNP-FOC</a></li>
							<li><a href="#htab9" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>LNP TESTS</a></li>
							
							<li><a href="#htab16" role="tab" data-toggle="tab"><i class="fa fa-phone pr-10"></i>QoS Needed</a></li>
							
						</ul>
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
						<table class="table table-hover">
							<thead>
								<tr>
							
							<th>Order ID</th>
							<th>Notes</th>
							<th>CIS Ticket</th>
							<th>Type</th>
							<th>Customer</th>
							<th>Site</th>
							<th>Created</th>
							<th>Due</th>
							<th>DID</th>
							<th>Temp DID</th>
							<th>Status</th>
							<th>LNP Status</th>
							<th>FOC</th>
							<th>Actions</th>
							<th></th>
							<th></th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				
				
				$sql = "SELECT * FROM vwOrders {$s} ORDER BY duedate asc LIMIT 40";
				//echo $sql;
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  
			   if(! $retval )
			   {
				  die('Could not get data: ' . mysql_error());
			   }
				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					//$cnt = 0;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						$cnt++;
						
						$voiptest_count =  $row['voiptest_count'];
						$capacitytest_count =  $row['capacitytest_count'];
						
							
							
					?>
					<tr <?php echo $rowclass ?>>
						
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
						<td nowrap><?php echo $row['cisTicket'] ;?> 	</td>
						<td nowrap><?php echo $row['orderType'] ;?> 	</td>
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><?php echo $row['siteNumber'] ;?></td>
						<td nowrap><?php echo  substr($row['createdate'],0,10) ;?></td>
						<td nowrap><?php echo substr($row['duedate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
						<td nowrap><?php echo $row['tempDID'] ;?> </td>	
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
						<td nowrap><?php echo $row['lnpstatus'] ;?> </td>
						<td nowrap><?php echo substr($row['focdate'],0,10) ;?></td>
						<td nowrap>
						<?php 
						 if ($_SESSION['user'] == 'admin') { 
						
							if ($row['orderType'] !== "LNP" and $row['didType'] == "PORT REQUESTED" AND (int)$row['lnpCount'] == 0  AND ($row['orderStatus'] == "ACTIVATED" OR $row['orderStatus'] == "BILLED")) {?>
							<a href='neworder-lnp1.php?orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-info ">Port In<i class="fa fa-phone"></i></a>					
						<?php } ?>
							<div id="actions<?php echo $cnt ?>">
								<?php if ((int)$row['flagged'] == 0 ) { ?>
									<button class="btn btn-group btn-sm btn-success " onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Flag</button>					
								<?php
								} else { ?>
									<button class="btn btn-group btn-sm btn-danger " onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Un-Flag</button>					
								<?php
								}
								?>
								
								
							</div>	

							<?php
						 } //end admin ?>
				

						</td>
						<td nowrap>
							<?php  if ($_SESSION['user'] == 'admin') { ?>
							<a href='neworder-mcd.php?orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-info ">MACD</a>					
							<?php } ?>
						</td>	
						<td nowrap>
							<?php  if ($voiptest_count>0 or $capacitytest_count>0 ) { ?>
							<a href='speedtests.php?orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-danger ">Tests</a>					
							<?php } ?>
						</td>						
											
					</tr>
					
				
					
				<?php
						
					}
				}
				
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
							<div class="tab-pane fade" id="htab2">
								<h3>PENDING</h3>
								<!-- section start -->
					<!-- ================ -->
					<section>

						<div class="container">
						<div class="row">

						<div class="col-md-12">	
					
						<div class="isotope-container row grid-space-0" id="data2">
						
				
				</div>
				</div>
				</div>
				</div>
				
				
			</section>
			<!-- section end -->
							</div>
							<div class="tab-pane fade" id="htab3">
								<h3>IN PROGRESS</h3>
								<!-- section start -->
					<!-- ================ -->
					<section>

						<div class="container-fluid">
						<div class="row">

						<div class="col-md-12">	
					
						<div class="isotope-container row grid-space-0" id='data3'>
					
				
				</div>
				</div>
				</div>
				</div>
				
				
			</section>
			<!-- section end -->
						</div>
			
			<div class="tab-pane fade"  id="htab4">
								<h3>PROVISIONED</h3>
								<!-- section start -->
					<!-- ================ -->
					<section>

						<div class="container-fluid">
						<div class="row">

						<div class="col-md-12">	
					
						<div class="isotope-container row grid-space-0" id="data4">
						
				
				</div>
				</div>
				</div>
				</div>
				
				
			</section>
			<!-- section end -->
						</div>
			<div class="tab-pane fade" id="htab5">
								<h3>ACTIVATED</h3>
								<!-- section start -->
					<!-- ================ -->
					<section>

						<div class="container-fluid">
						<div class="row">
						<div class="col-md-12">	
						<div class="isotope-container row grid-space-0" id="data5">
						</div>
						</div>
						</div>
						</div>
				
				
			</section>
			<!-- section end -->
						</div>
					<div class="tab-pane fade" id="htab6">
					<h3>BILLED</h3>
					<!-- section start -->
					<!-- ================ -->
					<section>

						<div class="container-fluid">
							<div class="row">
								<div class="col-md-12">	
									<div class="isotope-container row grid-space-0" id="data6">
									</div>
								</div>
							</div>
						</div>
					</section>
					<!-- section end -->
					
						</div>
			<div class="tab-pane fade" id="htab7">
				<h3>LNP ORDERS</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data7">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
						
			<div class="tab-pane fade" id="htab8">
				<h3>CNAM TO REGISTER</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data8">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			<div class="tab-pane fade" id="htab9">
				<h3>LNP TESTS</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data9">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			<div class="tab-pane fade" id="htab10">
				<h3>Flagged</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data10">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			<div class="tab-pane fade" id="htab11">
				<h3>LNP ISSUES</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data11">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			<div class="tab-pane fade" id="htab12">
				<h3>LNP PENDING</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data12">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			<div class="tab-pane fade" id="htab13">
				<h3>LNP IN PROCESS</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data13">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			<div class="tab-pane fade" id="htab14">
				<h3>LNP FOC</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data14">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			<div class="tab-pane fade" id="htab15">
				<h3>TO PORT</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data15">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			
			<div class="tab-pane fade" id="htab16">
				<h3>QoS Needd</h3>
				<!-- section start -->
				<!-- ================ -->
				<section>

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">	
								<div class="isotope-container row grid-space-0" id="data16">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- section end -->
				
			</div>
			


			<!-- tabs end -->
		</div>
	</div>
</div>
			
			
			
			
		</div>
		<!-- page-wrapper end -->

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
			
			$(document).on( 'shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
			   var ActiveTab = $(e.target).attr("href"); // activated tab
			   getData(ActiveTab);
			   
			   console.log(ActiveTab); // activated tab
			   
			})
			
			$(window).on('shown.bs.modal', function() { 
				//$('#notesModal').modal('show');
				//alert('shown');
			});
			
			
			function getData(val) {
				$(val).html('Updating... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				$.ajax({
				type: "POST",
				url: "get_tab_data.php",
				data:'tab='+val+'&customerID='+document.getElementById('customerID').value+'&siteNumber='+document.getElementById('siteNumber').value,
				success: function(data){
					
					$(val).html(data);
					
				}
				});
			}
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
			function updateCnam(val,cnt) {
				$.ajax({
				type: "POST",
				url: "ajax_functions.php",
				data:'fn=cnam-complete&orderID='+val,
				success: function(data){
					
					thediv = '#cnam'+cnt;
					
					$(thediv).html(data);
					
				}
				});
			}
			function updateQOS(val,cnt) {
				$.ajax({
				type: "POST",
				url: "ajax_functions.php",
				data:'fn=qos-complete&orderID='+val,
				success: function(data){
					
					thediv = '#qos'+cnt;
					
					$(thediv).html(data);
					
				}
				});
			}
			function toggleFlag(val,cnt,flag) {
				$.ajax({
				type: "POST",
				url: "ajax_functions.php",
				data:'fn=flag&flag='+flag+'&orderID='+val+'&cnt='+cnt,
				success: function(data){
					
					thediv = '#actions'+cnt;
					
					$(thediv).html(data);
					//getData('#htab9');
					console.log(thediv);
					
					
				}
				});
			}
			function updateLNPsuccess(val,cnt,cisTicket) {
				if (confirm('SUCCESS: Please confirm you have made a test call to this number and it was successful...')) {
					$.ajax({
						type: "POST",
						url: "ajax_functions.php",
						data:'fn=lnp-success&orderID='+val+'&cisTicket='+cisTicket,
						success: function(data){
							
							thediv = '#lnptest'+cnt;
							
							$(thediv).html(data);
							
						}
					});
				}
			}
			function updateLNPfail(val,cnt,cisTicket,did,site) {
				if (confirm('FAIL: Please confirm you have made a test call to this number and it FAILED...')) {
					$.ajax({
						type: "POST",
						url: "ajax_functions.php",
						data:'fn=lnp-fail&orderID='+val+'&cisTicket='+cisTicket+'&did='+did+'&site='+site,
						success: function(data){
							
							thediv = '#lnptest'+cnt;
							
							$(thediv).html(data);
							
						}
					});
				}
			}
			function waitCustomer(val,cnt) {
				if (confirm('Click OK to change the LNP status to WAITING_FOR_CUSTOMER. This will also email the customer.')) {
					$.ajax({
						type: "POST",
						url: "ajax_functions.php",
						data:'fn=lnp-waitforcustomer&orderID='+val,
						success: function(data){
							
							thediv = '#lnpstatus'+cnt;
							
							$(thediv).html(data);
							
						}
					});
				}
			}
			function updateLNPstatus(val) {
				  
				 if (document.getElementById('notes').value == '' && document.getElementById('newlnpstatus').value == 'WAITING_FOR_CUSTOMER') {
					 
					 alert('Notes required for WAITING_FOR_CUSTOMER');
				 } else  if (document.getElementById('newfoc').value == '' && document.getElementById('newlnpstatus').value == 'FOC_RECEIVED') {
					 
					 alert('Date required for FOC_RECEIVED');
				 } else
				 
						{
					 
				
				
					if (confirm('Click OK to change the LNP status to '+document.getElementById('newlnpstatus').value+'.')) {
						$.ajax({
							type: "POST",
							url: "ajax_functions.php",
							data:'fn=lnp-changelnpstatus&orderID='+val+'&newlnpstatus='+document.getElementById('newlnpstatus').value+'&notes='+document.getElementById('notes').value+'&newfoc='+document.getElementById('newfoc').value,
							success: function(data){
															
								$('#notesupdate').html(data);
								getNotes(val);
							}
						});
					}
				 }
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
			function addToBilling(val,cnt) {
				thediv = '#billing'+cnt;
				
				$(thediv).html('Working... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
				$.ajax({
					type: "GET",
					url: "api_h2o/h2o_add_lnp.php",
					data: ''+val,
					success: function(data){
							
						
						$(thediv).html(data);
						
					}
				});
				
			}
			
		</script>
						
	</body>
</html>
