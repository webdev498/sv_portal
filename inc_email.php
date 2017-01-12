<?php	
	
	function email_directory_listing($orderID) {
		
		date_default_timezone_set('America/Chicago');
		$dbhost = 'sv-mysql.cilqdskq1dv5.us-east-1.rds.amazonaws.com';	//'107.180.12.137:3036';
		$dbuser = 'simplevoip';
		$dbpass = '1Bigpimp!';
		$db 	   = 'simplevoip';
		$conn = mysql_connect($dbhost, $dbuser, $dbpass);
		
		$sql = "select * from vwOrders where orderID = {$orderID}";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		if(! $retval )
		   {
			  die('Could not get data: ' . mysql_error());
		   }
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		
		
		$did = 	$row['did'];
		$directory = $row['directory'];
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];
		$zip = $row['zip'];
		

		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$msg = "Peerless: Please enter the following directory listing:\n\nTN: {$did}\nListing Name: {$directory}\nAddress: {$address}, {$city}, {$state} {$zip}\n\nThank you,\nSimpleVoIP";
		$msg = wordwrap($msg,70);
		$subject = "New Directory Listing for {$did}";
		$headers = 'From: lnp@simplevoip.us' . "\r\n" .
		'Reply-To: lnp@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$mailto = "orderentry@peerlessnetwork.com";
		//$mailto = "jrobs@gecko2.com";	//testing
		$mail1 = mail($mailto, $subject, $msg, $headers);
		
		return true;
	}

	function email_order_update($orderID, $bUpdate, $bCustomer, $bCIS) {

		date_default_timezone_set('America/Chicago');
		$dbhost = 'sv-mysql.cilqdskq1dv5.us-east-1.rds.amazonaws.com';	//'107.180.12.137:3036';
		$dbuser = 'simplevoip';
		$dbpass = '1Bigpimp!';
		$db 	   = 'simplevoip';
		$conn = mysql_connect($dbhost, $dbuser, $dbpass);
		
		$sql = "select * from vwOrders where orderID = {$orderID}";
			
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		if(! $retval )
		   {
			  die('Could not get data: ' . mysql_error());
		   }
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
		$cisTicket = $row['cisTicket'];
		
		$subject = "SV Order Update for {$customer} site {$siteNumber}";
		$subjectCIS = "SV Order Update for {$customer} site {$siteNumber} - CIS Ticket [#{$cisTicket}]";
		$msg = "***SimpleVoIP Order Update***\n\nOrder: {$orderID}\nType: {$orderType}\nCustomer: {$customer}\nStatus: {$orderStatus}\nSite: {$siteNumber}\nMain Number: {$did}\nTemp DID: {$tempDID}\nNotes: {$notes}\n";
		$msg = wordwrap($msg,70);
		

		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		//$customerEmail = "josh.robbins@cisvpn.com";
		if ($bCustomer) {
			mail($customerEmail, $subject, $msg, $headers);
		}
		//$updateemail = "jrobs@gecko2.com";
		if ($bUpdate) {
			mail($updateemail, $subject, $msg, $headers);
		}
		$cisEmail = "helpdesk@cisvpn.com";
		//$cisEmail = "jrobsjrobs@gmail.com";
		if (!empty($cisTicket)) {
			
			mail($cisEmail, $subjectCIS, $msg, $headers);
		}
		
		return true;
	}
	
	function email_order_update_lnp_jep($orderID, $notes) {

		date_default_timezone_set('America/Chicago');
		$dbhost = 'sv-mysql.cilqdskq1dv5.us-east-1.rds.amazonaws.com';	//'107.180.12.137:3036';
		$dbuser = 'simplevoip';
		$dbpass = '1Bigpimp!';
		$db 	   = 'simplevoip';
		$conn = mysql_connect($dbhost, $dbuser, $dbpass);
		
		$sql = "select * from vwOrders where orderID = {$orderID}";
			
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		if(! $retval )
		   {
			  die('Could not get data: ' . mysql_error());
		   }
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		
		
		$orderID = $row['orderID'];
		$updateemail = $row['updateemail'];
		$customerEmail = $row['customerEmail'];
		$customer = $row['customer'];	
		$siteNumber = $row['siteNumber'];
		//$notes = $row['notes'];
		$did = $row['did'];
		$tempDID = $row['tempDID'];
		$orderStatus = $row['orderStatus'];
		$orderType = $row['orderType'];
		$cisTicket = $row['cisTicket'];
		
		$subject = "ACTION REQUIRED: LNP {$did}-{$customer} for site {$siteNumber} - CIS Ticket [#{$cisTicket}]";
		
		$msg = "***SimpleVoIP ACTION REQUIRED***\n***THIS ORDER NEEDS ATTENTION BEFORE IT CAN PROCEED\n\nPLEASE SEND REQUESTED INFO TO LNP@SIMPLEVOIP.US\n\nCustomer: {$customer}\nStatus: {$orderStatus}\nSite: {$siteNumber}\nNumber to port: {$did}\nNotes: {$notes}";
		$msg = wordwrap($msg,70);
		

		$headers = 'From: LNP@simplevoip.us' . "\r\n" .
		'Reply-To: LNP@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		

		mail($customerEmail, $subject, $msg, $headers);

		mail($updateemail, $subject, $msg, $headers);

		$cisEmail = "helpdesk@cisvpn.com";
			
		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		if (!empty($cisTicket)) {
			mail($cisEmail, $subject, $msg, $headers);
		}
		
		return true;
	}
	
	
	function email_order_update_single($orderID, $bUpdate, $bCustomer, $bCIS, $notes) {

		date_default_timezone_set('America/Chicago');
		$dbhost = 'sv-mysql.cilqdskq1dv5.us-east-1.rds.amazonaws.com';	//'107.180.12.137:3036';
		$dbuser = 'simplevoip';
		$dbpass = '1Bigpimp!';
		$db 	   = 'simplevoip';
		$conn = mysql_connect($dbhost, $dbuser, $dbpass);
		
		$sql = "select * from vwOrders where orderID = {$orderID}";
			
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		if(! $retval )
		   {
			  die('Could not get data: ' . mysql_error());
		   }
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		
		
		$orderID = $row['orderID'];
		$updateemail = $row['updateemail'];
		$customerEmail = $row['customerEmail'];
		$customer = $row['customer'];	
		$siteNumber = $row['siteNumber'];
		//$notes = $row['notes'];
		$did = $row['did'];
		$tempDID = $row['tempDID'];
		$orderStatus = $row['orderStatus'];
		$orderType = $row['orderType'];
		$cisTicket = $row['cisTicket'];
		
		$subject = "SV Order Update for {$customer} site {$siteNumber}";
		$subjectCIS = "SV Order Update for {$customer} site {$siteNumber} - CIS Ticket [#{$cisTicket}]";
		$msg = "***SimpleVoIP Order Update***\n\nOrder: {$orderID}\nType: {$orderType}\nCustomer: {$customer}\nStatus: {$orderStatus}\nSite: {$siteNumber}\nNumber: {$did}\nTemp DID: {$tempDID}\nUpdate: {$notes}\n";
		$msg = wordwrap($msg,70);
		

		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		//$customerEmail = "josh.robbins@cisvpn.com";
		if ($bCustomer) {
			mail($customerEmail, $subject, $msg, $headers);
		}
		//$updateemail = "jrobs@gecko2.com";
		if ($bUpdate) {
			mail($updateemail, $subject, $msg, $headers);
		}
		$cisEmail = "helpdesk@cisvpn.com";
		//$cisEmail = "jrobsjrobs@gmail.com";
		if (!empty($cisTicket)) {
			
			mail($cisEmail, $subjectCIS, $msg, $headers);
		}
		
		return true;
	}	
?>