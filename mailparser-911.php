<?php 
date_default_timezone_set('America/Chicago');
set_time_limit (360);
include "inc_db.php";
	
	
	if ($_REQUEST['did'] ) {
		
		$did = $_REQUEST['did'];
		$unregistered = $_REQUEST['unregistered'];
		$streetnumber = $_REQUEST['streetnumber'];
		$street = $_REQUEST['street'];
		$location = $_REQUEST['location'];
		$city = $_REQUEST['city'];
		$state = $_REQUEST['state'];
		$zip = $_REQUEST['zip'];	
		
		
		//Look up any orders for this DID
		$sql = "select * from tblOrders where did = '{$did}' AND orderType != 'LNP' AND orderStatus != 'CANCELED' ";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		
		$now = date("Y-m-d");
		if ($unregistered == 'unregistered') {
			
			
			$newnotes = "\n-!!-(FAIL) 911 Test Call completed on {$now} shows UNREGISTERED. Please register this address ASAP-!!-\n";
			
			$subject = "711 Test Call UNREGISTERED {$siteNumber}";
			$msg = "Unregistered DID {$did}\n\n{$newnotes}";
			$msg = wordwrap($msg,70);
			
			$headers = 'From: noreply@simplevoip.us' . "\r\n" .
			'Reply-To: noreply@simplevoip.us' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			
			$mailto = "jrobs@gecko2.com";
			$mail1 = mail($mailto, $subject, $msg, $headers);
		} elseif ($streetnumber) {
			$newnotes = "\n---(SUCCESS) 911 Test Call completed on {$now}, registered to {$streetnumber} {$street} {$location}, {$city} {$state} . ---\n";
		}
		
		
		if(mysql_num_rows($retval) == 0)
		{
			//No order found for this DID
			//Open a ticket? Send an email
			echo "no order found";
			$subject = "711 Test Call NO ORDER for {$did}";
			$msg = "No order found for DID {$did}";
			$msg = wordwrap($msg,70);
			
			$headers = 'From: noreply@simplevoip.us' . "\r\n" .
			'Reply-To: noreply@simplevoip.us' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			
			$mailto = "jrobs@gecko2.com";
			$mail1 = mail($mailto, $subject, $msg, $headers);
		}
		else
		{
			//Open Order found
			
			$orderID = $row['orderID'];
			$tempDID = $row['tempDID'];
			$actNotes = "\nAUTO ACTIVATED on {$now} based on 711 call.\n";
			
			$sql = "UPDATE tblOrders SET notes = concat(notes,'{$newnotes}') WHERE orderID = {$orderID}";
			$sql2 = "UPDATE tblOrders SET notes = concat(notes,'{$actNotes}'), orderStatus='ACTIVATED', activatedate='{$now}' WHERE orderStatus='PROVISIONED' AND orderID = {$orderID}";
			mysql_select_db($db);
			$retval = mysql_query( $sql, $conn );
			$retval = mysql_query( $sql2, $conn );
			
			if ($tempDID) {
				//Now update the callflow in Kazoo
				$url = "http://autoprovision.simplevoip.us/webservice/update_permanent_callflow?tn=" . $tempDID;

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				$json_response = curl_exec($curl);

				$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

				curl_close($curl);

				$response = json_decode($json_response, true);
				var_dump($response);
				$Code = $response['Code'];
				$Message = $response['Message'];
				$newnotes = "\n711 Call Success: Kazoo callflow updated using tempDID " .$tempDID . ": " . $Code . ": " . $Message . "\n";
				$sql = "UPDATE tblOrders SET notes = concat(notes,'{$newnotes}') WHERE orderID = {$orderID}";
				mysql_select_db($db);
				$retval = mysql_query( $sql, $conn );
			}
			
		}
		

		
		
		//Send order updates
		
		$subject = "711 Test Call SUCCESS for {$siteNumber}";
		$msg = "***SimpleVoIP Order Update***\n\nOrder: {$orderID}\nType: {$orderType}\nCustomer: {$customer}\nStatus: {$orderStatus}\nSite: {$siteNumber}\nMain Number: {$did}\nTemp DID: {$tempDID}\nNotes: {$notes}\n\n\nhttp://orders.simplevoip.us/orderdetails.php?orderID={$orderID}\n";
		$msg = wordwrap($msg,70);
		$subject = "SV Order {$_REQUEST['orderID']}: {$customer}";
		
		
		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$mailto = "lnp@simplevoip.us";
		//$mail1 = mail($mailto, $subject, $msg, $headers);
		//$mail2 = mail($updateemail, $subject, $msg, $headers);
		

	} else {
		
		
		echo "nothing found";
	}


	
?>
