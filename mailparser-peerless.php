<?php 

include 'inc_email.php';

include "inc_db.php";

	if ($_REQUEST['status']) {
		
		//echo "Status is found<BR>";
		
		$did = trim($_REQUEST['did_0']);
		$type = $_REQUEST['type'];
		$peerlessID = $_REQUEST['orderid'];
		$PON = $_REQUEST['PON'];
		$status = trim($_REQUEST['status']);
		$foc = $_REQUEST['foc'];
		echo "FOC RECD: " . $foc;
		//format of FOC looks like this:
		//Mon Aug 01 09:00:00 CDT 2016
		
		$foc_month = strtoupper(substr($foc, 4, 3));
		echo "MO:" .$foc_month;
		switch ($foc_month) {
			
			case "JAN":
				$foc_month_num = "01";
				break;
			case "FEB":
				$foc_month_num = "02";
				break;	
			case "MAR":
				$foc_month_num = "03";
				break;
			case "APR":
				$foc_month_num = "04";
				break;
			case "MAY":
				$foc_month_num = "05";
				break;
			case "JUN":
				$foc_month_num = "06";
				break;
			case "JUL":
				$foc_month_num = "07";
				break;	
			case "AUG":
				$foc_month_num = "08";
				break;
			case "SEP":
				$foc_month_num = "09";
				break;
			case "OCT":
				$foc_month_num = "10";
				break;
			case "NOV":
				$foc_month_num = "11";
				break;
			case "DEC":
				$foc_month_num = "12";
				break;	
		}

		$foc_day = substr($foc, 8, 2);
		$foc_year = substr($foc,-4);
		$focdate = $foc_year . "-" . $foc_month_num . "-" . $foc_day;
		
		
		
		
		/*STATUS GUIDE
		
			PENDING_CSR
			PENDING_LSR
			FOC_PENDING
			FOC_RECEIVED
			PROVISIONING_IN_PROCESS
			NPAC_PENDING
			TNS_ACTIVATED
			CLOSED
			
			***BAD STATUSES
			REJECTED
			PARTIAL_FAILURE
			CLARIFICATION_REQUESTED
			CANCELED
			PENDING_RESPONSE
			
			
		*/
		if ($type == 'PORT_IN') {
			//Look up any OPEN orders for this DID
			$sql = "select * from vwOrders where orderType='LNP' AND did = '{$did}' ";
			mysql_select_db($db);
			$retval = mysql_query( $sql, $conn );
			$row = mysql_fetch_array($retval, MYSQL_ASSOC);
			
			$now = date("Y-m-d");
			
			$orderStatus = $row['orderStatus'];
			$updateemail = $row['updateemail'];
			$customerEmail = $row['customerEmail'];
			$cisTicket = $row['cisTicket'];
			$customer = $row['customer'];
			$siteNumber = $row['siteNumber'];
			$cnam = $row['cnam'];
			$directory = $row['directory'];
			$address = $row['address'];
			$city = $row['city'];
			$state = $row['state'];
			$zip = $row['zip'];
			$directoryCompleteDate = $row['directoryCompleteDate'];
			//echo "count: {mysql_num_rows($retval)}";
			if(mysql_num_rows($retval) == 0)
			{
				//No order found for this DID
				
				echo "no order found. Creating a pending order.\n";
				
				//Create an order for this DID in PENDING status, CUSTOMER=XXX
				$newnotes = "AUTO CREATED - No Order found for this LNP order.\nLNP Update on {$now} for {$did}. Order ID: {$peerlessID}, status is {$status}, PON {$PON}, FOC is {$foc}.\n";	
				$sql = "INSERT INTO tblOrders (orderType, orderStatus, lnpstatus, createdate, lnpLastStatusUpdate, duedate,  customerID, siteNumber,  notes,  updateemail, did) VALUES ('LNP', 'PENDING', 'CLARIFICATION_REQUESTED', now(), now(), DATE_ADD(now(), INTERVAL 10 DAY), 0, 'XXX',  '{$newnotes}','pm@cisvpn.com','{$did}')";
				echo $sql;
				mysql_select_db($db);
				$retval = mysql_query( $sql, $conn );
				
				//Send email to Josh - TEMP
				$subject = "Peerless Update/No Order Found for {$did}";
				$msg = "No order found for DID {$did}. Order created, please update and assign to a customer.";
				$msg = wordwrap($msg,70);
				$headers = 'From: noreply@simplevoip.us' . "\r\n" .
				'Reply-To: noreply@simplevoip.us' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
				
				$mailto = "lnp@simplevoip.us,jrobs@simplevoip.us";
				$mail1 = mail($mailto, $subject, $msg, $headers);
			}
			else //Open Order found
			{
				echo "Order Found<BR>";
				//update to IN PROGRESS if in PENDING
				if ($orderStatus == 'PENDING') {
					$strUpdate = ", orderStatus = 'IN PROGRESS' ";
				}
				$orderID = $row['orderID'];
				$newnotes = "\nLNP Update on {$now} for {$did}. Order ID: {$peerlessID}.\n";
				$updateNotes = $newnotes . ", status is {$status}, PON {$PON}, FOC is {$foc}.\n";
				
				//Update the order notes for any Peerless update
				$sql = "UPDATE tblOrders SET lnpstatus = '{$status}', lnpLastStatusUpdate = '{$now}', lnpOrderID = '{$peerlessID}', notes = concat(notes,'{$updateNotes}') {$strUpdate} WHERE orderID = {$orderID} and lnpstatus != 'CLOSED'";
				mysql_select_db($db);
				$retval = mysql_query( $sql, $conn );
				
				//send to CIS Kayako if CIS Ticket ID exists
				if ($cisTicket) {
					
					//only send on certain statuses
					if ($status !== "PROVISIONING_IN_PROCESS" AND $status !== "NPAC_PENDING" AND $status !== "TNS_ACTIVATED") {
						echo "Sending to CIS for ticket ($cisTicket}<BR>";
						$msg = "***SimpleVoIP LNP UPDATE***\n\nStatus: {$status} \nCustomer: {$customer}\nSite: {$siteNumber}\nMain Number: {$did}\nFOC: {$foc}\nNotes: {$newnotes}\n";
						$msg = wordwrap($msg,70);
						$subject = "SV LNP UPDATE for {$did} - {$customer} [#{$cisTicket}]";
						$headers = 'From: noreply@simplevoip.us' . "\r\n" .
						'Reply-To: noreply@simplevoip.us' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
						$mailto = "helpdesk@cisvpn.com";
						$mail1 = mail($mailto, $subject, $msg, $headers);
					}
				}
				//Send to customer certain statuses
				if (trim($status) == "FOC_RECEIVED") {
					echo "FOC Received<BR>";
					$msg = "***SimpleVoIP FOC RECEIVED***\n\nStatus: {$status} \nCustomer: {$customer}\nSite: {$siteNumber}\nMain Number: {$did}\nFOC: {$foc}\nNotes: {$newnotes}\n";
					$msg = wordwrap($msg,70);
					$subject = "SV FOC RECEIVED for {$did} - {$customer} on {$focdate} [#{$cisTicket}]";
					
					
					$headers = 'From: noreply@simplevoip.us' . "\r\n" .
					'Reply-To: noreply@simplevoip.us' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
					
					//Temp - send to Josh
					//$mailto = "jrobs@gecko2.com";
					//$mail1 = mail($mailto, $subject, $msg, $headers);

				
					
					$mail1 = mail($customerEmail, $subject, $msg, $headers);
					
					if (strlen($focdate) == 10) {
						$sql2 = "UPDATE tblOrders SET focdate='{$focdate}', notes = concat(notes,'{$newnotes}'), orderStatus='PROVISIONED' WHERE (orderID = {$orderID} AND orderStatus != 'ACTIVATED' )";
					} else {
						$sql2 = "UPDATE tblOrders SET notes = concat(notes,'{$newnotes}'), orderStatus='PROVISIONED' WHERE (orderID = {$orderID} AND orderStatus != 'ACTIVATED' )";
						
					}
					
					//Update to PROVISIONED if IN PROGRESS or PENDING
					$newnotes = "\nAuto update status to PROVISIONED (FOC RECEIVED)";
					$retval = mysql_query( $sql2, $conn );
					echo $sql2;
				}
				if (trim($status) == "CLOSED") {
					
					echo "Closed order <BR>";
					//Send mail to Customer email and order update email
					$msg = "***SimpleVoIP PORT IN COMPLETE***\n\nStatus: {$status} \nCustomer: {$customer}\nSite: {$siteNumber}\nMain Number: {$did}\n!!**PLEASE REMEMBER TO CALL YOUR OLD CARRIER AND CANCEL SERVICE**!!\nNotes: {$newnotes}\n";
					$msg = wordwrap($msg,70);
					$subject = "SV PORT IN COMPLETE for {$did} - {$customer} [#{$cisTicket}]";
					
					
					$headers = 'From: noreply@simplevoip.us' . "\r\n" .
					'Reply-To: noreply@simplevoip.us' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
					
					

					//send to update and customer emails
					$mail1 = mail($updateemail, $subject, $msg, $headers);
					$mail1 = mail($customerEmail, $subject, $msg, $headers);
				
					//SELECT COMFORT ONLY
					if ($customer == "Select Comfort") {
						//$selectNotify = "brendano@renodis.com";
						//$mail1 = mail($selectNotify, $subject, $msg, $headers);
					}
					
					//Send email to Peerless for CNAM
					echo "Directory complete date is {$directoryCompleteDate}";
					if (is_null($directoryCompleteDate) ) {
						email_directory_listing($orderID);
					}
					
					//Update to ACTIVATED if in PROVISIONED status
					$newnotes = "\nAuto update status to ACTIVATED (CLOSED). Sent directory listing registration.";
					$sql2 = "UPDATE tblOrders SET notes = concat(notes,'{$newnotes}'),orderStatus='ACTIVATED', activatedate='{$now}', directoryCompleteDate='{$now}' WHERE orderStatus='PROVISIONED' AND orderID = {$orderID}";
					$retval = mysql_query( $sql2, $conn );

				}
					
				if ($status == "REJECTED" OR $status == "PARTIAL_FAILURE" OR $status == "CLARIFICATION_REQUESTED" OR $status == "CANCELED" OR $status == "PENDING_RESPONSE") {
					echo "Error status, sending email<BR>";	
					//Send mail to order team
					$msg = "***SV PORT IN ISSUE***\n\nStatus: {$status} \nCustomer: {$customer}\nCIS Ticket: [#{$cisTicket}]\nSite: {$siteNumber}\nMain Number: {$did}\nNotes: {$newnotes}\n";
					$msg = wordwrap($msg,70);
					$subject = "ACTION REQUIRED: PORT IN for {$did} - {$customer}";
					
					
					$headers = 'From: noreply@simplevoip.us' . "\r\n" .
					'Reply-To: noreply@simplevoip.us' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
					
					//Temp - send to Josh
					$mailto = "lnp@simplevoip.us";
					$mail1 = mail($mailto, $subject, $msg, $headers);

					//send to update emails
					$mail1 = mail($updateemail, $subject, $msg, $headers);
					
					// Add flag for review 
					
					$sql2 = "UPDATE tblOrders SET flagged=1 WHERE orderID = {$orderID}";
					$retval = mysql_query( $sql2, $conn );
					
					// Cancel LNP order if port is canceled
					if ($status == "CANCELED") {
						$sql2 = "UPDATE tblOrders SET orderStatus='CANCELED' WHERE orderID = {$orderID}";
						$retval = mysql_query( $sql2, $conn );
						
					}
					
				}
				
								
			} //End Order Found
			
		} //End type=PORT IN

		
		

	} else {
		
		
		echo "nothing submitted";
	}


	
?>
