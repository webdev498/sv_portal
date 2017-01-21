<?php
$msg = $_REQUEST['msg'];
include 'inc_header.php';

$orderID = $_REQUEST["orderID"];


if ($_REQUEST['orderID'])
{

	$sql = "SELECT * FROM vwOrders WHERE orderID={$orderID}";

	mysql_select_db($db);

	$retval = mysql_query( $sql, $conn );  

	
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		$orderID = $row['orderID'];
		$siteNumber = $row['siteNumber'];
		$customer = $row['customer'];
		$did = $row['did'];
		$tempDID = $row['tempDID'];
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];
		$zip = $row['zip'];
		$customerID = $row['customerID'];

		$fulladdress = $address . ", " . $city . ", " . $state . " " . $zip;

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
		<link rel="shortcut icon" href="images/favicon.ico">

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
		
		
		
		
			<!-- background image -->
			<div class="fullscreen-bg"></div>

			<!-- main-container start -->
			<!-- ================ -->
			<section class="main-container">

				<div class="container">
					<div class="row">

						<!-- main start -->
						<!-- ================ -->
						<div class="main col-md-12">

							<!-- page-title start -->
							<!-- ================ -->
							<h1 class="page-title">SELECT COMFORT: Required Activation Steps
							<img src="images/SimpleVolP125px.jpg" hspace=10 vspace=10 align=right></h1>
							<div class="separator-2"></div>
							
							<!-- page-title end -->
							<b><font color=red>**Please complete all steps IN ORDER before leaving. Buttons will become enabled as you complete steps.</font> If you have any problems please work with your turn-up tech or call SimpleVoIP Installation Support at 312-796-0272.**</b><BR>
							<BR><B>NOTE: Please leave the old phones plugged in and operational. Both sets of handsets will remain at this site.</b>
								
							<h2>Step 1: Verify Site Information</h2>
							
							Customer: <B><?php echo $customer; ?></B><BR>
							Site: <B><?php echo $siteNumber; ?></B><BR>
							Address: <B><?php echo $fulladdress; ?></B><BR>
							Main #: <B><?php echo $did; ?></B><BR>
							Temp #: <B><?php echo $tempDID; ?></B><BR>
							<BR>Please verify the suite number is accurate for this site - this is important for 911 calls to properly route. If anything is incorrect, add to the notes box below.<BR>
							<div id="verifyInfo"><button type=button  id=verifybutton class='btn radius-10 btn-info btn-sm' onclick="verifyInfo()">Information Verified</button></div>

						<h2>Step 2: Broadband Testing</h2>
							<div id=testmessage>Performing Broadband Tests...THIS WILL TAKE UP TO 5 MINUTES... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i></div><BR> 
							 <div id="id_applet_custom_text"></div>
								<applet mayscript name="mcs" code="myspeedserver/applet/myspeed.class" archive="/myspeed/myspeed_s_8.jar,/myspeed/plugins_s_8.jar" codebase="http://speedtest.simplevoip.us/myspeed" width=600 height=400>

									<param name="permissions" value="all-permissions"></param>
									<param name="config" value="capacity">
									<param name="testspecid" value="4">
									
									<param name="autostart" value="yes">
									<param name="SID" value="*<?php echo $customer . ": " . $siteNumber; ?>"> 
									<h3 ><font color=red>Java is required to view this applet and Chrome is NOT supported. Please use Firefox or IE.</font></h3><BR>Please go to www.java.com and install the latest version before completing this activation.
								</applet>
								
								<applet mayscript name="mcs" code="myspeedserver/applet/myspeed.class" archive="/myspeed/myspeed_s_8.jar,/myspeed/plugins_s_8.jar" codebase="http://speedtest.simplevoip.us/myspeed" width=600 height=400>

									<param name="permissions" value="all-permissions"></param>
									<param name="config" value="voip">
									<param name="testspecid" value="5">
									<param name="voip_lines" value="2">
									<param name="autostart" value="yes">
									<param name="SID" value="*<?php echo $customer . ": " . $siteNumber; ?>"> 
									<h3 ><font color=red>Java is required to view this applet and Chrome is NOT supported. Please use Firefox or IE.</font></h3><BR>Please go to www.java.com and install the latest version before completing this activation.
								</applet>
							
							<BR>
							<p>When the test is complete, provide the upload and download values to your turn up tech to continue.</p>
							<div id="speedmessage"></div><BR>
							<div id="testComplete"><button type=button disabled id=testbutton class='btn radius-10 btn-info btn-sm' onclick="testComplete()">Speed Test Complete - Info Provided to Tech</button></div>

							
							<h2>Step 3: Activate Phones</h2>
							Click the button below to activate the site's phones and phone numbers in our system. <font color=red><b>PHONES WILL NOT FUNCTION WITHOUT THIS STEP COMPLETED!!</B></FONT><BR><BR>
							<div id="did"></div>
							<div id="enable">
								<button type=button disabled id=activate class='btn radius-10 btn-info btn-sm' onclick="updateCF(<?php echo $tempDID; ?>)">Activate DIDs & Devices</button>
							</div>
																	
							<script>
							function startInstall(val) {
								
								$.ajax({
								type: "POST",
								url: "ajax_functions.php?fn=startInstall&orderID="+val,
								//data:'tn='+val,
								success: function(data){
									
									//test_complete(12345,'test',44678576, 54778678, 1.3, 65.545434, 75.46778, 4.3 );
									
								}
								});
								
							}
							
							function round(value, decimals) {
							  return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
							}
							function capacity_test_complete( MSSID, DETAILLINK, DCAPACITY, UCAPACITY, QOS  ) {
								
								DCAPACITY = round(DCAPACITY*.000001,2);
								UCAPACITY = round((UCAPACITY*.000001),2);
							
								
								note = '<B><font color=blue>Please tell your turn-up technician the following values and then continue the activation process: Download: ' + DCAPACITY + 'Mbps, Upload: '+ UCAPACITY + 'Mbps</font></b>';
								alert('Bandwidth test complete. Please tell your turn up technician the values below to continue.');

								$('#speedmessage').html(note);
								
								document.getElementById('testbutton').disabled = false;
								
								//$('#completeInstall').html('Finalizing Installation... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
								$.ajax({
								type: "POST",
								url: "ajax_functions.php?fn=capacitytest&orderID=<?php echo $orderID ?>&siteNumber=<?php echo $siteNumber ?>&customerID=<?php echo $customerID ?>&detaillink="+DETAILLINK+"&dcapacity="+DCAPACITY+"&ucapacity="+UCAPACITY+"&qos="+QOS+"&recordid="+MSSID,
								
								success: function(data){
									
									$('#testmessage').html('TESTS COMPLETE');
									
								}
								});
								
								
							}
							function voip_test_complete_XXX( MSSID, DETAILLINK, JITTER, PACKETLOSS, MOS  ) {
								
							
								//alert(JITTER);
								
								//$('#completeInstall').html('Finalizing Installation... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
								$.ajax({
								type: "POST",
								url: "ajax_functions.php?fn=voiptest&orderID=<?php echo $orderID ?>&siteNumber=<?php echo $siteNumber ?>&customerID=<?php echo $customerID ?>&detaillink="+DETAILLINK+"&jitter="+JITTER+"&packetloss="+PACKETLOSS+"&mos="+MOS+"&recordid="+MSSID,
								
								success: function(data){
									
									//alert(data);
									
								}
								});
								
								
							}
							function voip_test_complete( MSSID, DETAILLINK  ) {
								
							
								//alert(DETAILLINK);
								
								//$('#completeInstall').html('Finalizing Installation... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
								$.ajax({
								type: "POST",
								url: "ajax_functions.php?fn=voiptest&orderID=<?php echo $orderID ?>&siteNumber=<?php echo $siteNumber ?>&customerID=<?php echo $customerID ?>&detaillink="+DETAILLINK+"&recordid="+MSSID,
								
								success: function(data){
									
									//alert(data);
									
								}
								});
								
								
							}
							function updateCF(val) {
								$('#did').html('Activating... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
								$.ajax({
								type: "POST",
								url: "http://autoprovision.simplevoip.us/webservice/update_permanent_callflow?tn="+val,
								//data:'tn='+val,
								success: function(data){
									
									if (data.Code == 'success') {
										msg = "<i class='fa fa-check' style='font-size:24px,color=green'></i><font color=green><B>Call Flow Activation Successful.</B></font><BR>";
										
									} else {
										msg = "<i class='fa fa-check' style='font-size:24px,color=green'></i><font color=green><B>##" + data.Message + "##.</B></font><BR>";
									}
									$('#did').html(msg);
									//document.getElementById('installbutton').disabled = false;
									enableDevices(val);
									
									//temp until I fix the enable
									//$('#enable').html(msg);
									//start_testcalls();
								}
								});
								
							}
							function enableDevices(val) {
								$('#enable').html('Enabling Devices... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
								$.ajax({
								type: "POST",
								url: "http://autoprovision.simplevoip.us/webservice/enable_site_devices?tn="+val,
								//data:'tn='+val,
								success: function(data){
									
									if (data.Code == 'success') {
										msg = "<i class='fa fa-check' style='font-size:24px,color=green'></i><font color=green><B>Device Enable Successful. Please move on to install the devices.</B></font><BR>";
										
									} else {
										msg = "<i class='fa fa-check' style='font-size:24px,color=green'></i><font color=green><B>##" + data.Message + "##.  Please move on to install the devices.</B></font><BR>";
									}
									$('#enable').html(msg);
									start_testcalls();
									
								}
								});
							}
							
							function start_testcalls(){
								document.getElementById('testcall1_inbound').disabled = false;
								document.getElementById('testcall1_outbound').disabled = false;
								document.getElementById('testcall1_711').disabled = false;
								document.getElementById('testcall2_inbound').disabled = false;
								document.getElementById('testcall2_outbound').disabled = false;
								document.getElementById('testcall2_711').disabled = false;
								document.getElementById('testcall3_inbound').disabled = false;
								document.getElementById('testcall3_outbound').disabled = false;
								document.getElementById('testcall3_711').disabled = false;
								
							}
							
							
							function verifyInfo() {
								$('#verifyInfo').html('<i class="fa fa-check" style="font-size:24px,color=green"></i><font color=green><b>Site Info Verified</b></font>');
								startInstall(<?php echo $orderID ?>);
								//start_testcalls();
								
								
							}
							function testComplete() {
								$('#testComplete').html('<i class="fa fa-check" style="font-size:24px,color=green"></i><font color=green><b>Bandwidth Test Complete</b></font>');
								document.getElementById('activate').disabled = false;
								
								
								
							}
							function completeInstall() {
								$('#completeInstall').html('<i class="fa fa-check" style="font-size:24px"></i>Installation Complete');
								document.getElementById('outboundbutton').disabled = false;
								
								
							}
							
							function completeTestCall(val) {
								$('#testcall_'+val).html('<i class="fa fa-check" style="font-size:24px"></i>Test Complete');
						
								if (document.getElementById('testcall1_inbound') == null && 
								document.getElementById('testcall1_outbound') == null &&
								document.getElementById('testcall1_711') == null &&
								document.getElementById('testcall2_inbound') == null &&
								document.getElementById('testcall2_outbound') == null &&
								document.getElementById('testcall2_711') == null &&
								document.getElementById('testcall3_inbound') == null &&
								document.getElementById('testcall3_outbound') == null &&
								document.getElementById('testcall3_711') == null) 
								{
									alert('Congratulations! Please complete the final testing section, add notes and complete the job.');
									document.getElementById('final1_button').disabled = false;
									document.getElementById('final2_button').disabled = false;
									document.getElementById('final3_button').disabled = false;
								}
						
						
							}
							function completeFinal(val) {
								$('#final_'+val).html('<i class="fa fa-check" style="font-size:24px"></i>Test Complete');
						
								if (document.getElementById('final1_button') == null && 
								document.getElementById('final2_button') == null &&
								document.getElementById('final3_button') == null ) 
								{
									alert('Congratulations! Please add any installation notes and complete the job.');
									document.getElementById('completebutton').disabled = false;
								}
						
						
							}
							
							function completeInstall(val) {
								$('#completeInstall').html('Finalizing Installation... <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>'); 
								$.ajax({
								type: "POST",
								url: "ajax_functions.php?fn=completeInstall&notes="+document.getElementById('notes').value+"&orderID="+val+"&customer=<?php echo $customer?>&siteNumber=<?php echo $siteNumber?>",
								//data:'tn='+val,
								success: function(data){
									
									$('#completeInstall').html(data);
									
								}
								});
								
							}
							</script>
							
							<h2>Step 4: Install Handsets</h2>
							<p>Please follow these instructions to install the handsets.  <font color=red><b>Please DO NOT REMOVE the old handsets.</B></FONT> They will continue to receive inbound calls until we port the phone number.</p>
							
							<BR>
							
							<div class="image-box team-member style-3-b">
								<div class="row">
									<div class="col-sm-6 col-md-4 col-lg-3">
										<div class="overlay-container overlay-visible">
											<img src="images/t29g.jpg" alt="">
											<div class="overlay-bottom hidden-xs">
												<div class="text">
													<p class="small margin-clear"><em>Yealink T29G</em></p>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-md-4 col-lg-6">
										<div class="body">
											<h3 class="title margin-clear">Yealink T29G</h3>
											<div class="separator-2 mt-10"></div>
											<p>Unbox the Yealink T29G desk phone, assemble and connect the INTERNET port on the phone to the port labeled <b>2 Phone</b> on the Ethernet wall plate. Place the Yealink T29G next to the existing phone and wait for it to power on and download its config completely. The phone should boot up, download its config and show the company logo along with green phone icons for the 2 line buttons 1 and 2.</p>
											
											<h4>Complete Test Calls:</h4>
											
											<OL>
												<LI><B>Inbound Test:</B> Calling <b>1+<?php echo $tempDID ?></b> rings BOTH phones and you can hear the caller.<BR><div id="testcall_1"><button id=testcall1_inbound disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(1)">Inbound Test Passed</button></div></LI>
												<LI><B>Outbound Test:</B> Dial <B>1-800-241-6522</B> and verify United answers. <b>Repeat on 2nd handset.</b><BR><div id="testcall_2"><button id=testcall1_outbound disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(2)">Outbound Test Passed</button></div></LI>
												<LI><B>Emergency Test:</B> Dial <B>7-1-1</B> and verify the recording plays. <b>Repeat on 2nd handset.</b><BR><div id="testcall_3"><button id=testcall1_711 disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(3)">711 Test Passed</button></div></LI>
											</OL>
											
											
											
										</div>
									</div>
									<div class="col-sm-12 col-md-4 col-lg-3">
										<div class="overlay-container overlay-visible">
											<img src="customerimages/t29g-complete-select.jpg" alt="">
											<div class="overlay-bottom hidden-xs">
												<div class="text">
													<p class="small margin-clear"><em>Successful Install</em></p>
												</div>
											</div>
										</div>
										
									</div>
								</div>
							</div>
							
							
							<div class="image-box team-member style-3-b">
								<div class="row">
									<div class="col-sm-6 col-md-4 col-lg-3">
										<div class="overlay-container overlay-visible">
											<img src="images/tgp600.jpg" alt="">
											<div class="overlay-bottom hidden-xs">
												<div class="text">
													<p class="small margin-clear"><em>Panasonic TGP-600</em></p>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 col-md-4 col-lg-6">
										<div class="body">
											<h3 class="title margin-clear">Panasonic TGP-600 BASE + 2 Handsets</h3>
											<div class="separator-2 mt-10"></div>
											<OL>
												<LI>Plug the Panasonic TGP-600 base station Ethernet into the surface mount box labeled <B>3 Phone</B> and place the Panasonic base unit in a safe location, <b>ideally at the cash wrap</b>. Do not use the included power adapter - this device is PoE. Do not place this inside a cabinet or near any computers or PC monitors. <b>After 3-5 minutes you should see a solid green light. A flashing orange light indicates an IP issue (no internet generally). </b><BR></LI>		
												<LI>Plug the Panasonic charging cradle in at the cash wrap and place the handset in the charger. A red light should appear and the phone should beep to indicate it is charging. To turn the handset on, press and hold the red power button.<BR></LI>
												
												<LI>Check the cordless phone signal strength (top left of screen). If the signal indicator is red or only 1 bar, you need to relocate the base station to a new location or away from other electronics. If a cable run is available, move it to the cash wrap for optimal coverage.<BR><BR></LI>
											</OL>
											
											<h4>Complete Test Calls (Handset 1):</h4>
											
											<OL>
												<LI><B>Inbound Test:</B> Calling <b>1+<?php echo $tempDID ?></b> rings BOTH phones and you can hear the caller.<BR><div id="testcall_4"><button id=testcall2_inbound disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(4)">Inbound Test Passed</button></div></LI>
												<LI><B>Outbound Test:</B> Dial <B>1-800-241-6522</B> and verify United answers. <b>Repeat on 2nd handset.</b><BR>							<div id="testcall_5"><button id=testcall2_outbound disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(5)">Outbound Test Passed</button></div></LI>
												<LI><B>Emergency Test:</B> Dial <B>7-1-1</B> and verify the recording plays. <b>Repeat on 2nd handset.</b><BR><div id="testcall_6"><button id=testcall2_711 disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(6)">711 Test Passed</button></div></LI>
											</OL>
											<h4>Pair Handset 2</h4>
											<OL>
												<LI>Plug the 2nd Panasonic charging cradle in at the cash wrap and place the handset in the charger. A red light should appear and the phone should beep to indicate it is charging. To turn the handset on, press and hold the red power button. The screen should say "Register Handset".<BR></LI>
												<LI>To pair this handset, press and hold the button on the Panasonic base station for 5 seconds until the red light flashes. On the handset, press and hold the OK button to register this handset to the base station. After 3 seconds it will produce a long beep and the screen should come up with the time like the other handset and say <b>Handset 2</b>.<BR></LI>
												<LI>Check the cordless phone signal strength indicators (top left of screen). If the signal indicator is red or only 1 bar, you need to relocate the base station to a new location or away from other electronics. If a cable run is available, move it to the cash wrap for optimal coverage.<BR></LI>
												
												<LI>Leave the printed user guide and the leave behind documents next to the phones in plain sight.<BR></LI>
											</OL>
											
											<h4>Complete Test Calls (Handset 2):</h4>
											
											<OL>
												<LI><B>Inbound Test:</B> Calling <b>1+<?php echo $tempDID ?></b> rings BOTH phones and you can hear the caller.<BR><div id="testcall_7"><button id=testcall3_inbound disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(7)">Inbound Test Passed</button></div></LI>
												<LI><B>Outbound Test:</B> Dial <B>1-800-241-6522</B> and verify United answers. <b>Repeat on 2nd handset.</b><BR>							<div id="testcall_8"><button id=testcall3_outbound disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(8)">Outbound Test Passed</button></div></LI>
												<LI><B>Emergency Test:</B> Dial <B>7-1-1</B> and verify the recording plays. <b>Repeat on 2nd handset.</b><BR><div id="testcall_9"><button id=testcall3_711 disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeTestCall(9)">711 Test Passed</button></div></LI>
											</OL>										</div>
									</div>
									<div class="col-sm-12 col-md-4 col-lg-3">
										<div class="overlay-container overlay-visible">
											<img src="images/tpa60-complete.jpg" alt="">
											<div class="overlay-bottom hidden-xs">
												<div class="text">
													<p class="small margin-clear"><em>Successful Install</em></p>
												</div>
											</div>
										</div>
										
									</div>
								</div>
							</div>
							
							
								
									<div class="col-sm-6 col-md-8 col-lg-6">
										<div class="body">
											<h3 class="title margin-clear">Final Testing</h3>
											<div class="separator-2 mt-10"></div>
												<p>Before you leave this site, please verify the following and check the boxes to indicate you have completed them. <font color=red><B>DO NOT LEAVE WITHOUT PHONES WORKING!!!</B></font></p>
										
												<OL>
													<LI><B>Old phones are still installed: </B><BR><div id="final_1"><button id=final1_button disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeFinal(1)">Phones Still Exist</button></div></LI>
													<LI><B>Published Number still works on old phones:</B> Dial <B>1+<?php echo $did ?></B> and verify the old phones ring. <BR>	<div id="final_2"><button id=final2_button disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeFinal(2)">Phone Number Works</button></div></LI>
													<LI><B>Guide Left:</B> The phone guide and leave-behind document has been left next to handsets. <BR><div id="final_3"><button id=final3_button disabled type=button class='btn radius-10 btn-success btn-sm' onclick="completeFinal(3)">Guide Left</button></div></LI>
												</OL>										
										</div>
									</div>
									
									
							
							
	
							
							
							
							
							
							Enter any notes or problems here:<BR>
							<textarea rows="6" cols="80" name=notes id=notes></textarea><BR>
							<div id="completeInstall"><button type=button disabled  id=completebutton class='btn radius-10 btn-success btn-sm' onclick="completeInstall(<?php echo $orderID ?>)">Installation Completed</button></div>
							
							
							
							

						</div>
						<!-- main end -->


					</div>
				</div>
			</section>
			<!-- main-container end -->


			
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
