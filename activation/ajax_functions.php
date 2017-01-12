<?php include 'inc_header.php';
include 'inc_email.php';

	$orderID = $_REQUEST["orderID"];
	$fn = $_REQUEST["fn"];
	$siteNumber = $_REQUEST["siteNumber"];
	$customer = $_REQUEST["customer"];
	$now = date("Y-m-d H:i:s");
	
	if ($fn == 'completeInstall') {
		
		$notes = "\n**** ON SITE INSTALL COMPLETE on {$now} ****\n****{$customer}-{$siteNumber}****\nInstaller Notes: ";
		$notes .= str_replace( "'","",$_REQUEST["notes"]);
		$sql = "UPDATE tblOrders set notes = concat(notes,'{$notes}') WHERE orderID={$orderID}";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if ($retval) {
			echo "<B>You have completed the installation. Thank you for playing!</B>";
		} else {
			echo "ERROR!!!!!";
		}
		
			
		$subject = "INSTALL COMPLETE: {$customer}-{$siteNumber}";
		$msg = $notes;
		$msg = wordwrap($msg,70);
		
		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$mailto = "desmond.slowe@cisvpn.com,josh.robbins@cisvpn.com";
		$mail1 = mail($mailto, $subject, $msg, $headers);
		
		
	}	//end complete install
	
	if ($fn == 'startInstall') {
		
		$notes = "\n**** ON SITE INSTALL STARTED on {$now} ****";
		$notes .= str_replace( "'","",$_REQUEST["notes"]);
		$sql = "UPDATE tblOrders set notes = concat(notes,'{$notes}') WHERE orderID={$orderID}";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		
		
	}	//end begin install	
	
	if ($fn == 'capacitytest') {
		
		
		$customerID = $_REQUEST['customerID'];
		$siteNumber = $_REQUEST['siteNumber'];
		$detaillink = $_REQUEST['detaillink'];
		$recordid = $_REQUEST['recordid'];
		
		//capacity test
		$dcapacity = $_REQUEST['dcapacity'];
		$ucapacity = $_REQUEST['ucapacity'];
		$qos = $_REQUEST['qos'];
		
		$sql = "INSERT INTO test_capacity (orderID, customerID, sid, detaillink, recordid, dcapacity, ucapacity, qos) VALUES ({$orderID}, {$customerID}, '{$siteNumber}', '{$detaillink}', {$recordid}, {$dcapacity}, {$ucapacity}, {$qos});";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn ); 
			
		
		echo $sql;
		
	}	//end begin install		
	
	if ($fn == 'voiptest') {
		
		
		// voip test
		$customerID = $_REQUEST['customerID'];
		$siteNumber = $_REQUEST['siteNumber'];
		$detaillink = $_REQUEST['detaillink'];
		$recordid = $_REQUEST['recordid'];
		$jitter = $_REQUEST['jitter'];
		$packetloss = $_REQUEST['packetloss'];
		$mos = $_REQUEST['mos'];
		
	
		//$sql = "INSERT INTO test_voip (orderID, customerID, sid, detaillink, recordid, jitter, packetloss, mos) VALUES ({$orderID}, {$customerID}, '{$siteNumber}', '{$detaillink}', {$recordid}, {$jitter}, {$packetloss}, {$mos});";
		$sql = "INSERT INTO test_voip (orderID, customerID, sid, detaillink, recordid) VALUES ({$orderID}, {$customerID}, '{$siteNumber}', '{$detaillink}', {$recordid});";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn ); 
			
		
		echo $sql;
	
		
	}	//end begin install		
	
	?>