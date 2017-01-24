<?php 
include 'inc_email.php';
include "inc_db.php";

session_start();
$user = $_SESSION['user'];
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

	date_default_timezone_set('America/Chicago');
	$now = date("Y-m-d H:i:s");

	$orderID = $_REQUEST["orderID"];
	$fn = $_REQUEST["fn"];
	
	if ($fn == "cnam-complete") {
		$now = date("Y-m-d");
		$sql = "UPDATE tblOrders set CNAMcompleteDate = '{$now}' WHERE orderID={$orderID}";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if ($retval) {
			echo "<B>COMPLETED!</B>";
		} else {
			echo "ERROR!!!!!";
		}
		
	} //end CNAM complete
	if ($fn == "qos-complete") {
		$now = date("Y-m-d");
		$sql = "UPDATE tblOrders set qosCompleteDate = '{$now}' WHERE orderID={$orderID}";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if ($retval) {
			echo "<B>QoS Verified!</B>";
		} else {
			echo "ERROR!!!!!";
		}
		
	} //end QOS complete
	
	
	if ($fn == "lnp-success") {
		$now = date("Y-m-d");
		$cisTicket = $_REQUEST["cisTicket"];
		
		
		$updateNotes = "\nLNP SUCCESS: Test call made on {$now}. Close LNP ticket as completed.";
		$sql = "UPDATE tblOrders set lnpstatus='CLOSED', flagged=NULL, lnptestdate = '{$now}',  notes = concat(notes,'{$updateNotes}') WHERE orderID={$orderID}";
		
			
		if ($cisTicket) {
			$msg = $updateNotes;
			$msg = wordwrap($msg,70);
			$subject = "SUCCESS: LNP TEST {$did} [#{$cisTicket}]";
			$headers = 'From: noreply@simplevoip.us' . "\r\n" .
			'Reply-To: noreply@simplevoip.us' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			
			$mailto = "helpdesk@cisvpn.com";
			
			$mail1 = mail($mailto, $subject, $msg, $headers);
		}
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if ($retval) {
			
			echo "<B>Successful Call</B>";
		} else {
			echo "ERROR!!!!!";
		}
		
	} //end LNP Success
	
	
	if ($fn == "lnp-fail") {
		$now = date("Y-m-d");
		$cisTicket = $_REQUEST["cisTicket"];
		$did = $_REQUEST["did"];
		$site = $_REQUEST["site"];
		$updateNotes = "\nTest call FAILED on {$now}. DID: {$did}, Site: {$site}. Escalating to LNP dept for review.";
		$sql = "UPDATE tblOrders set notes = concat(notes,'{$updateNotes}'), flagged=1, lnpstatus='FAILED' WHERE orderID={$orderID}";
		
		
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if ($retval) {
						
			$headers = 'From: noreply@simplevoip.us' . "\r\n" .
						'Reply-To: noreply@simplevoip.us' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
		
			$msg = $updateNotes;
			$msg = wordwrap($msg,70);
			$subject = "LNP TEST ERROR for {$did} [#{$cisTicket}]";
			$headers = 'From: noreply@simplevoip.us' . "\r\n" .
			'Reply-To: noreply@simplevoip.us' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			
			if ((int)$cisTicket !== 0) {
				$mailto = "helpdesk@cisvpn.com,josh.robbins@cisvpn.com";
			} else {
				$mailto = "josh.robbins@cisvpn.com";
			}
			
			
			$mail1 = mail($mailto, $subject, $msg, $headers);
			
			echo "<B>Failed Call - SV notified</B>";
		} else {
			echo "ERROR!!!!!";
		}
		
	} //end LNP Fail
	
	if ($fn == "lnp-changelnpstatus") {
		$now = date("Y-m-d");
		$newlnpstatus = $_REQUEST["newlnpstatus"];
		$updateNotes = $_REQUEST["notes"];
		$newfoc = $_REQUEST["newfoc"];
		if (!empty($updateNotes)) {
			$updateNotes = "\n" . $updateNotes;
		}
		//echo $newlnpstatus;
		if ($newlnpstatus == 'WAITING_FOR_CUSTOMER') {
			$updateNotes .= "\nLNP Status changed to WAITING_FOR_CUSTOMER on {$now}.";
			$sql = "UPDATE tblOrders set notes = concat(notes,'{$updateNotes}'),   lnpLastStatusUpdate = '{$now}',  lnpstatus='WAITING_FOR_CUSTOMER',focdate=NULL WHERE orderID={$orderID}";
			$msg = "<B>Status changed to WAITING_FOR_CUSTOMER</B>";
		
		}
		
		if ($newlnpstatus == 'RESUBMITTED') {
		
			$updateNotes .= "\nLNP Status changed to RESUBMITTED on {$now}.";
			$sql = "UPDATE tblOrders set notes = concat(notes,'{$updateNotes}'), lnpstatus='RESUBMITTED',  lnpLastStatusUpdate = '{$now}', focdate=NULL, lnptestdate=NULL WHERE orderID={$orderID}";
			$msg = "<B>Status changed to RESUBMITTED</B>";
		}
		if ($newlnpstatus == 'FOC_RECEIVED') {
		
			$updateNotes .= "\nLNP Status changed to FOC_RECEIVED on {$now}. FOC is {$newfoc}.";
			$sql = "UPDATE tblOrders set notes = concat(notes,'{$updateNotes}'), lnpstatus='FOC_RECEIVED',   lnpLastStatusUpdate = '{$now}', focdate='{$newfoc}', lnptestdate=NULL WHERE orderID={$orderID}";
			$msg = "<B>Status changed to FOC_RECEIVED</B>";
		}
		if ($newlnpstatus == 'CLOSED') {
		
			$updateNotes .= "\nLNP Status manually changed to CLOSED on {$now}.";
			$sql = "UPDATE tblOrders set notes = concat(notes,'{$updateNotes}'), lnpstatus='CLOSED', orderStatus='ACTIVATED',  activatedate='{$now}',  lnpLastStatusUpdate = '{$now}', lnptestdate=NULL WHERE orderID={$orderID}";
			$msg = "<B>Status changed to CLOSED</B>";
		}
				
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if ($retval) {
					
					
			
			if ($newlnpstatus == 'WAITING_FOR_CUSTOMER') {
			
				email_order_update_lnp_jep($orderID,$updateNotes );
			
			} else {
				
				email_order_update_single($orderID, false, false, true, $updateNotes );
			}
			echo $msg;
		} else {
			echo "ERROR!!!!!";
		}
		
	} //end new LNP status
	
	if ($fn == "flag") {
		$now = date("Y-m-d");
		
		$flag =  (int)$_REQUEST["flag"];
		$cnt =  $_REQUEST["cnt"];
		if ($flag == 1) {
			$newflag = 0;
		} else {
			$newflag = 1;
		}
		
		
		$sql = "UPDATE tblOrders set flagged = $newflag WHERE orderID={$orderID}";

		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if ($retval) {
			if ($newflag == 0) {
				echo "<button class='btn btn-group btn-sm btn-success btn-animated' onclick='javascript:toggleFlag({$orderID},{$cnt},{$newflag})'>Flag</button>";
			} else {
				echo "<button class='btn btn-group btn-sm btn-danger btn-animated' onclick='javascript:toggleFlag({$orderID},{$cnt},{$newflag})'>Un-Flag</button>";
			}
		} else {
			echo "ERROR!!!!!";
		}
		
	} //end Flag toggle
	
	if ($fn == 'notes') {
		
		
		$email_update = $_REQUEST['email_update'];
		if ($email_update == "true") {
			$email_update = true;
			
		}
		
		$email_customer = $_REQUEST['email_customer'];
		if ($email_customer == "true") {
			$email_customer = true;
			
		}
		
		$email_cis = $_REQUEST['email_cis'];
		
		if ($email_cis == "true") {
			$email_cis = true;
			
			
		}
		$now = date("Y-m-d");
		$notesemail = $_REQUEST['notes'];
		$notes = mysql_escape_string("\n" . $now . ": " . $_REQUEST['notes']);
		
		$sql = "Update tblOrders set  notes = concat(notes,'{$notes}'),lnpLastStatusUpdate = '{$now}' where orderID = {$_REQUEST['orderID']}";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
	
		
		//Send update  email_order_udpate($orderID, $bUpdate, $bCustomer, $bCIS)
		if ($email_update OR $email_customer OR $email_cis) {
			email_order_update_single($orderID, $email_update, $email_customer, $email_cis, $notesemail );
			$em = "Emails Sent.";
		}
		
		echo "<B>Order Updated." . $em . "</B>";
	}

	if ($fn == "get-lnp-status") {
		$now = date("Y-m-d");
		$did = $_REQUEST["did"];
		
		$sql = "SELECT lnpstatus, activateDate,focdate from tblOrders WHERE orderType='LNP' AND orderStatus != 'CANCELED' AND did='{$did}'";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if(mysql_num_rows($retval) == 0)
		{
			echo "<B>NO LNP ORDER</B>";
		}
		else
		{
			$row = mysql_fetch_array($retval, MYSQL_ASSOC);
			if ($row['lnpstatus'] == 'CLOSED') {
				$s = "<B>ACTIVATED " . $row['activateDate'] . "</B>";
			} else if ($row['lnpstatus'] == 'FOC_RECEIVED') {
				$s = "<B>FOC_RECEIVED: " . $row['focdate'] . "</B>";
			}	else {
				$s = "<B>" . $row['lnpstatus'] . "</B>";
			}
			echo $s;
		}
		
	} //end get LNP status

	if ($fn == "get-911") {
		$host = "svconfig.cilqdskq1dv5.us-east-1.rds.amazonaws.com";
		$user = "SVCONFIG";
		$pass = "Simpl3voip";
		$db = "SimpleVoIP";
		$did = $_REQUEST["did"];	
		$link = mssql_connect($host, $user, $pass);
		/*
		if(!$link) {
			echo'Could not connect';
			die('Could not connect: ' . mssql_get_last_message());
		}
		echo'Successful connection';
		*/
		
		mssql_select_db( $db, $link );
		$sql = "Select * from IntradoSubscribers WHERE IntradoSubscriberId='+1{$did}'";		
		$data = mssql_query($sql) or  die('MSSQL error: ' . mssql_get_last_message());

		$row = mssql_fetch_array($data, MSSQL_ASSOC);
		if ($row['Number'] == '') {
			$address = "NOT FOUND!";
		} else {
			$address = $row['Number'] . " " . $row['Street'] . ", " . $row['City'] . ", " . $row['State'] . " " . $row['ZipCode'];
		}
		echo "<B>" . $address . "</B>";
		
		
		mssql_close($link);
		
	} //end get 911

	
	if ($fn == "get-speedtests") {
		
		$siteNumber = $_REQUEST["siteNumber"];
		$customerID = $_REQUEST["customerID"];
		
		$sql = "SELECT * FROM test_capacity where sid='" . $siteNumber . "' AND customerID=" . $customerID . " order by timestamp desc";
		//echo $sql;
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if(mysql_num_rows($retval) == 0)
		{
			echo "No speed tests found.";
		}
		else
		{
			
			$s = "<table class='table table-hover'>" .
					"<thead>" .
						"<tr>" .
							
							"<th>Date</th>" .
							
							"<th>Site</th>" .
							"<th>DL</th>" .
							"<th>UL</th>" .
							"<th>QoS</th>" .
													
						"</tr>" .
					"</thead>" .
					"<tbody>"; 
			while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
					{
				 
					
					$s .= "<tr>" .
						"<td>" . $row['timestamp'] . "</td>" .
						
						"<td>" . $row['sid'] . "</td>" .	
						"<td>" . $row['dcapacity'] . "</td>" .	
						"<td>" . $row['ucapacity'] . "</td>" .	
						"<td>" . $row['qos'] . "</td>" .							
					"</tr>";
				
					}
				
				mysql_close($conn);
			
				$s .= "</tbody></table>";
			
			echo $s;			
					
					
		}
		
	} //end get speed tests	
	
	if ($fn == "get-events") {
		
		$deviceId = $_REQUEST["deviceId"];
		$timezone = $_REQUEST["timezone"];
		
		if ($timezone == 'PST') {
			$tz = "America/Los_Angeles";
		} else if ($timezone == 'MST') {
			$tz = "America/Denver";
		} else if ($timezone == 'CST') {
			$tz = "America/Chicago";
		} else if ($timezone == 'EST') {
			$tz = "America/New_York";
		} else {
			$tz = "America/Chicago";
		}
		
		$sql = "SELECT *,convert_tz(eventDate,'+00:00','{$tz}') as dt FROM KazooStatusEvents where deviceId='" . $deviceId . "' and type in ('UP','DOWN') order by eventDate DESC limit 15";
		//echo $sql;
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if(mysql_num_rows($retval) == 0)
		{
			echo "No monitoring events found for this device.";
		}
		else
		{
			
			$s = "<table class='table table-hover'>" .
					"<thead>" .
						"<tr>" .
							
							"<th>Date</th>" .
							
							"<th>Event</th>" .
							"<th>Message</th>" .
							
													
						"</tr>" .
					"</thead>" .
					"<tbody>"; 
			while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
					{
				 
					
					$s .= "<tr>" .
						"<td>" . $row['dt'] . "</td>" .
						
						"<td>" . $row['type'] . "</td>" .	
						"<td>" . $row['event'] . " (" . $row['call_id'] . ")</td>" .	
												
					"</tr>";
				
					}
				
				mysql_close($conn);
			
				$s .= "</tbody></table>";
			
			echo $s;			
					
					
		}
		
	} //end get monitoring events	
	
	
	if ($fn == "get-cdr") {
		
		$did = $_REQUEST["did"];
		$siteNumber = $_REQUEST["siteNumber"];
		$ownerId = rtrim($_REQUEST["ownerId"], ",") ;
		$ownerIdList = str_replace("^^^", "'", $ownerId);
		$startdate = $_REQUEST["startdate"];
		$enddate = $_REQUEST["enddate"];
		$timezone = $_REQUEST["timezone"];
		
		$dbconn = pg_connect("host=sv-postgres.cilqdskq1dv5.us-east-1.rds.amazonaws.com port=5432 dbname=cdr2db user=cdr2db password=Vl37yZnf5DSg");

		$sql = "select timezone('" . $timezone . "',datetime) as datetime, direction, billing_seconds, callee_id_name, callee_id_number, caller_id_name, caller_id_number from data where owner_id IN ({$ownerIdList}) AND billing_seconds > 0 and timezone('" . $timezone . "',datetime) >= '{$startdate} 00:00:01' and timezone('" . $timezone . "',datetime) <= '{$enddate} 23:59:59' AND ( direction='inbound' or (direction='outbound' and request like '%simplevoip%')) and CHAR_LENGTH(callee_id_number) >=10 and CHAR_LENGTH(caller_id_number)>=10 order by datetime desc";


		$retval = pg_query($dbconn, $sql);
		//echo $sql;
		if(pg_num_rows($retval) == 0)
		{
			echo "No calls found.";
		}
		else
		{
			
			$s = "<table class='table table-hover'>" .
					"<thead>" .
						"<tr>" .
							
							"<th>Date</th>" .
							
							"<th>Direction</th>" .
							"<th>From Name</th>" .
							"<th>From Number</th>" .
							"<th>To Name</th>" .
							"<th>To Number</th>" .
							"<th>Duration</th>" .
													
						"</tr>" .
					"</thead>" .
					"<tbody>"; 
			while ($row = pg_fetch_array($retval)) 
					{
				 
					$dir = $row['direction'];
					if ($dir == 'inbound') {
						$direction = 'Outbound';
						
					} else {
						$direction = 'Inbound';
					}
					
					$caller_id_number =  str_replace("+1","",$row['caller_id_number']);
					$callee_id_number = str_replace("+1","",$row['callee_id_number']);
					
					$duration = gmdate("i:s", $row['billing_seconds']);
					$s .= "<tr>" .
						"<td>" . $row['datetime'] . "</td>" .
						
						"<td>" . $direction . "</td>" .	
						"<td>" . $row['caller_id_name'] . "</td>" .	
						"<td>" . $caller_id_number . "</td>" .	
						"<td>" . $row['callee_id_name'] . "</td>" .	
						"<td>" . $callee_id_number . "</td>" .	
						"<td>" . $duration . "</td>" .							
					"</tr>";
				
					}
				
				pg_close($conn);
			
				$s .= "</tbody></table>";
			
			echo $s;			
					
					
		} 
		
	} //end get CDR	
	
	if ($fn == "newaddress") {
		
		$did = $_REQUEST['did'];
		$siteNumber = $_REQUEST['siteNumber'];
		$newaddress = $_REQUEST['newaddress'];
		
		$subject = "New 911 Address for {$did} at site {$siteNumber}";
		$msg = "Please update the address to {$newaddress}";
		$msg = wordwrap($msg,70);
		
		
		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$mailto = "jrobs@gecko2.com";
		$mail1 = mail($mailto, $subject, $msg, $headers);
		
		echo "<font color=red>Your address will be updated within 24 hours.</font>";
		
		
	}
	if ($fn == "get-userlist") {
		
		$accountId = $_REQUEST['accountId'];
		$last_name = $_REQUEST['last_name'];
		
		$dbconn = pg_connect("host=sv-postgres.cilqdskq1dv5.us-east-1.rds.amazonaws.com port=5432 dbname=cdr2db user=cdr2db password=Vl37yZnf5DSg");

		$sql = "SELECT account_id, id, first_name, last_name FROM users where account_id='" . $accountId . "' AND last_name='" . $last_name . "'";
		$retval = pg_query($dbconn, $sql);
		
		if(pg_num_rows($retval) == 0)
		{
			echo "**ERROR**.";
		}
		else
		{
			$s = "<select multiple name=userlist id=userlist>";
					
			while ($row = pg_fetch_array($retval)) 
			{
				$s .= "<option selected value='^^^" . $row['id'] . "^^^'>" . $row['first_name'] . " " . $row['last_name'] . "</option>";
			}
			$s .= "</select>";
		}
		
		echo $s;
	}
	if ($fn == "get-locations") {
		
		$customerID = $_REQUEST['customerID'];
		
		$sql = "select * from tblCustomerLocations where customerID = {$customerID} order by siteNumber, state, city";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		if(mysql_num_rows($retval) == 0)
		{
			$s = "No devices found.";
		}
		else
		{
			$s = "<select name=locationID id=locationID>";
					
			while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
			{
				$s .= "<option selected value='" . $row['locationID'] . "'>" . $row['siteNumber'] ."-". $row['state'] . "-" . $row['city'] . " - " . $row['street'] . "</option>";
			}
			$s .= "</select>";
		}
		
		echo $s;
	}
	if ($fn == "get-json") {
		
		$deviceId = $_REQUEST['deviceId'];
		
		$sql = "select raw_json from KazooRegistrations where deviceId = '{$deviceId}'";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		if(mysql_num_rows($retval) == 0)
		{
			$s = "No registration found.";
		}
		else
		{
			
			$row = mysql_fetch_array($retval, MYSQL_ASSOC);
			
			$s = "<PRE>".json_encode(json_decode($row['raw_json']), JSON_PRETTY_PRINT);
		}
		
		echo $s;
	}
	if ($fn == "get_location_info") {
		
		$locationID = $_REQUEST['locationID'];
		
		$sql = "select * from tblCustomerLocations where locationID = {$locationID}";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		if(mysql_num_rows($retval) == 0)
		{
			$s = "No location found.";
		}
		else
		{
								
			$row = mysql_fetch_array($retval, MYSQL_ASSOC);			
				$customerID = $row['customerID'];
				$siteNumber = $row['siteNumber'];
				$streetnumber = $row['streetNumber'];
				$street = $row['street'];
				$suite = $row['suite'];
				$city = $row['city'];
				$state = $row['state'];
				$zip = $row['zip'];
				$email = $row['email'];
				
				$arr = array ('email'=> $email,'customerID'=> $customerID,'siteNumber'=>$siteNumber,'streetnumber'=>$streetnumber,'street'=>$street,'suite'=>$suite,'city'=>$city,'state'=>$state,'zip'=>$zip);
		}
		
		echo json_encode($arr);
	}
	if ($fn == "get_config_info") {
		
		$deviceId = $_REQUEST['deviceId'];
		
		$sql = "select * from apCONFIGS where deviceId = '{$deviceId}'";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		if(mysql_num_rows($retval) == 0)
		{
			$arr = array ('result'=> 'NOT FOUND');
		}
		else
		{
								
			$row = mysql_fetch_array($retval, MYSQL_ASSOC);			
			$mac = $row['mac'];
			$phoneModelID = $row['phoneModelID'];
			$baseTemplateID = $row['baseTemplateID'];
			$customerTemplateID = $row['customerTemplateID'];
			$codec = $row['codec'];
			$transport = $row['transport'];
			$proxy = $row['proxy'];
			$line2 = $row['deviceID_line2'];
			$line3 = $row['deviceID_line3'];
						
			$arr = array ('result'=> 'SUCCESS','phoneModelID'=>$phoneModelID,'mac'=> $mac,'baseTemplateID'=> $baseTemplateID,'customerTemplateID'=>$customerTemplateID,'codec'=>$codec,'transport'=>$transport,'proxy'=>$proxy,'line2'=>$line2,'line3'=>$line3);
			 
		}
		echo json_encode($arr);
		
	}
	if ($fn == "check-mac") {
		
		$mac = strtoupper($_REQUEST['mac']);
		$deviceId = $_REQUEST['deviceId'];
		
		$sql = "select c.*, d.name from apCONFIGS c left outer join KazooDevices d on d.deviceId=c.deviceID where c.mac = '{$mac}' and c.deviceId != '{$deviceId}'";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		$s = "MAC OK";
		if(mysql_num_rows($retval) > 0)
		{								
			$row = mysql_fetch_array($retval, MYSQL_ASSOC);			
			$name = $row['name'];
			$s = "<span class='text-danger'>MAC is currently assigned to " . $name . ". Updating will reassign this MAC.</b></span>";		 
		}
		
		echo $s;
		
	}
	if ($fn == "update_config") {
		
		$mac = strtoupper($_REQUEST['mac']);
		$phoneModelID = $_REQUEST['phoneModelID'];
		$baseTemplateID = $_REQUEST['baseTemplateID'];
		$customerTemplateID = $_REQUEST['customerTemplateID'];
		$codec = $_REQUEST['codec'];
		$proxy = $_REQUEST['proxy'];
		$transport = $_REQUEST['transport'];	
		$accountId = $_REQUEST['accountId'];
		$line1 = $_REQUEST['line1'];
		$line2 = $_REQUEST['line2'];
		$line3 = $_REQUEST['line3'];
		
		
		$sql = "DELETE FROM apCONFIGS where mac='{$mac}'";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		$sql = "INSERT INTO apCONFIGS (proxy, mac, phoneModelID, baseTemplateID, customerTemplateID, codec, transport, accountId, deviceID, deviceID_line2, deviceID_line3, lastUpdate) VALUES ('{$proxy}','{$mac}', {$phoneModelID}, {$baseTemplateID}, {$customerTemplateID}, '{$codec}', '{$transport}', '{$accountId}', '{$line1}', '{$line2}', '{$line3}','{$now}') ";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		echo "<B>Config Updated.</B>";
		
	}
	if ($fn == "get_config_options") {
		
		$phoneModelID = $_REQUEST['phoneModelID'];
		$accountId = $_REQUEST['accountId'];
		
		$baseTemplateID = $_REQUEST['baseTemplateID'];
		$customerTemplateID = $_REQUEST['customerTemplateID'];
		$codec = $_REQUEST['codec'];
		$transport = $_REQUEST['transport'];
		$proxy = $_REQUEST['proxy'];
		
		//get account name
		$sql = "select name from KazooAccounts where accountId='{$accountId}'";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		$accountName = $row['name'];
		$acct = substr($accountName,0,2);
		
		$sql = "select * from apPhoneModels order by phoneModel";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		//Get phone models
		$phone = "<select name=phoneModelID id=phoneModelID oninput='javascript:get_config_options(this.options[this.selectedIndex].value,0,0,0,0)' required>";	
		$phone .= "<option value=''>--Please Select--</option>";
		while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		{
			$sel = '';
			if ($phoneModelID == $row['phoneModelID']) {
				$sel = ' selected ';
			}
			$phone .= "<option " . $sel . " value='" . $row['phoneModelID'] . "'>" . $row['phoneModel'] . "</option>";
		}
		$phone .= "</select>";
		
		//Get base templates for this phone model
		$sql = "select * from apTemplates where phoneModelID = {$phoneModelID} and type='BASE'";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		
		$base = "<select name=baseTemplateID id=baseTemplateID required>";	
		while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		{
			$sel = '';
			if ($baseTemplateID == $row['templateID']) {
				$sel = ' selected ';
			}
			$base .= "<option " . $sel . " value='" . $row['templateID'] . "'>" . $row['templateName'] . "</option>";
		}
		$base .= "</select>";
		
		//Get customer templates for this phone model
		
		$sql = "select * from apTemplates where phoneModelID = {$phoneModelID} and type='CUSTOMER' and accountId='{$accountId}'";
		if ($acct=='X_') {
			$sql = "select * from apTemplates where phoneModelID = {$phoneModelID} and type='CUSTOMER'";
		}
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		$customer = "<select name=customerTemplateID id=customerTemplateID required>";	
		while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		{
			$sel = '';
			if ($customerTemplateID == $row['templateID']) {
				$sel = ' selected ';
			}
			if (!$customerTemplateID AND $row['isDefault'] == 1) {
				$sel = ' selected ';
			}
			$customer .= "<option {$sel} value='" . $row['templateID'] . "'>" . $row['templateName'] . "</option>";
		}
		$customer .= "</select>";
		
		

		if ($codec=='G729' or $codec=='') { 
			$c_729 = 'selected';
		}
		if ($codec=='G711') { 
			$c_711 = 'selected';
		}	
		if ($codec=='G722') { 
			$c_722 = 'selected';
		}			
		$codecstr = "<select name=codec id=codec required>";
		$codecstr .= "<option {$c_729} value='G729'>G729</option>";
		$codecstr .= "<option {$c_711} value='G711'>G711</option>";
		$codecstr .= "<option {$c_722} value='G722'>G722</option>";		
		$codecstr .= "</select>";
		
		if ($transport=='TCP') { 
			$c_TCP = 'selected';
		}
		if ($transport=='UDP' or $transport=='') { 
			$c_UDP = 'selected';
		}	
		
		$transportstr = "<select name=transport id=transport required>";
		$transportstr .= "<option {$c_TCP} value='TCP'>TCP</option>";
		$transportstr .= "<option {$c_UDP} value='UDP'>UDP</option>";
		$transportstr .= "</select>";
		
		
		if ($proxy=='AUTO' or $proxy=='') { 
			$p_auto = 'selected';
		}	
		if ($proxy=='WEST') { 
			$p_west = 'selected';
		}
		if ($proxy=='CENTRAL') { 
			$p_central = 'selected';
		}
		if ($proxy=='EAST') { 
			$p_east = 'selected';
		}
		$proxystr = "<select name=proxy id=proxy required>";
		$proxystr .= "<option {$p_auto} value='AUTO'>AUTO</option>";
		$proxystr .= "<option {$p_west} value='WEST'>WEST</option>";
		$proxystr .= "<option {$p_central} value='CENTRAL'>CENTRAL</option>";
		$proxystr .= "<option {$p_east} value='EAST'>EAST</option>";
		$proxystr .= "</select>";
		
		$arr = array ('phone'=> $phone,'base'=> $base,'customer'=> $customer,'codec'=> $codecstr,'transport'=>$transportstr, 'proxy'=>$proxystr);
		echo json_encode($arr);
	}
	if ($fn == "get-devices") {
		
		//Get available devices to assign
		
		$siteNumber = $_REQUEST['siteNumber'];
		$accountId = $_REQUEST['accountId'];
		$deviceId = $_REQUEST['deviceId'];
		$deviceID_line2 = $_REQUEST['line2'];
		$deviceID_line3 = $_REQUEST['line3'];
		
		$sql = "select d.* from KazooDevices d where (select u.last_name from KazooUsers u where u.userId=d.ownerId) = '{$siteNumber}' and d.accountId='{$accountId}'";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		$line1 = "<select name=line1 id=line1 required>";
		$line2 = "<select name=line2 id=line2 required {$deviceID_line2}>";
		$line3 = "<select name=line3 id=line3 required {$deviceID_line3}>";
		
		$line2 .= "<option selected value=''>--NOT USED--</option>";
		$line3 .= "<option selected value=''>--NOT USED--</option>";
		
		while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		{
			$sel = '';
			$sel2 = '';
			$sel3 = '';
			if ($deviceId == $row['deviceId']) {
				$sel = ' selected ';
			}
			if ($deviceID_line2 == $row['deviceId']) {
				$sel2 = ' selected ';
			}
			if ($deviceID_line3 == $row['deviceId']) {
				$sel3 = ' selected ';
			}
			$line1 .= "<option {$sel} value='" . $row['deviceId'] . "'>" . $row['name'] . "</option>";
			$line2 .= "<option {$sel2} value='" . $row['deviceId'] . "'>" . $row['name'] . "</option>";
			$line3 .= "<option {$sel3} value='" . $row['deviceId'] . "'>" . $row['name'] . "</option>";
		}
		$line1 .= "</select>";
		$line2 .= "</select>";
		$line3 .= "</select>";
		
		$arr = array ('line1'=> $line1,'line2'=> $line2,'line3'=> $line3,'sql'=>$sql);
		echo json_encode($arr);
	}
	if ($fn == "new-location") {
		
		$customerID = $_REQUEST['customerID'];
		$siteNumber = $_REQUEST['siteNumber'];
		$streetnumber = $_REQUEST['streetnumber'];
		$street = $_REQUEST['street'];
		$suite = $_REQUEST['suite'];
		$city = $_REQUEST['city'];
		$state = $_REQUEST['state'];
		$zip = $_REQUEST['zip'];
		$email = $_REQUEST['email'];
		$sql = "INSERT INTO tblCustomerLocations (email,customerID, siteNumber, streetnumber, street, suite, city, state, zip) VALUES ('{$email}',{$customerID}, '{$siteNumber}', '{$streetnumber}', '{$street}', '{$suite}', '{$city}', '{$state}', '{$zip}')";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		if ($retval) {
			$s = "New Location added. Please close the window.";
		} else {
			$s = "ERROR!";
		}
		echo $s;
	}	
	if ($fn == "update_location_info") {
		
		$locationID = $_REQUEST['locationID'];
		$siteNumber = $_REQUEST['siteNumber'];
		$streetnumber = $_REQUEST['streetnumber'];
		$street = $_REQUEST['street'];
		$suite = $_REQUEST['suite'];
		$city = $_REQUEST['city'];
		$state = $_REQUEST['state'];
		$zip = $_REQUEST['zip'];
		$email = $_REQUEST['email'];
		
		$sql = "UPDATE tblCustomerLocations SET email = '{$email}', siteNumber = '{$siteNumber}', streetnumber='{$streetnumber}', street='{$street}', suite='{$suite}', city='{$city}', state='{$state}', zip='{$zip}' WHERE locationID={$locationID}";
		
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
		
		if ($retval) {
			$s = "Location updated. Please close the window.";
		} else {
			$s = "ERROR!";
		}
		echo $s;
	}			
	if ($fn == "get-site-devices") {
		
		
		$last_name = $_REQUEST['last_name'];
		$accountId = $_REQUEST['accountId'];
		
		$sql = "SELECT d.*, (select count(0) from KazooStatusEvents where type LIKE '%RECOVERY%' and eventDate > date_sub(current_date, INTERVAL 1 DAY) and deviceId=d.deviceId) as cdrEventsRecovery,  " .  
					" (select count(0) from KazooStatusEvents where type LIKE '%UNALLOCATED%' and eventDate > date_sub(current_date, INTERVAL 1 DAY) and deviceId=d.deviceId) as cdrEventsUnallocated, (select network_ip from KazooRegistrations r where r.deviceId = d.deviceId ) as network_ip, (select user_agent from KazooRegistrations r where r.deviceId = d.deviceId ) as user_agent, (select network_port from KazooRegistrations r where r.deviceId = d.deviceId ) as network_port, (select count(0) from apCONFIGS where deviceID=d.deviceId or deviceID_line2=d.deviceId or deviceID_line3=d.deviceId) as configs FROM KazooDevices d where ownerId IN (select userId from KazooUsers where accountId = '" . $accountId . "' and last_name='" . $last_name . "') order by name  ";
		//echo $sql;
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );  
		if(mysql_num_rows($retval) == 0)
		{
			$s = "No devices found.";
		}
		else
		{
								
			$s = "<table class=table>";
			while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
			{
				$status = "";
				$deviceId = $row['deviceId'];
				$config = $row['configs'];
				$network_ip = $row['network_ip'];
				$network_port = $row['network_port'];
				$user_agent = $row['user_agent'];
				$status = $row['status'];
				$name = $row['name'];
				$username = $row['username'];
				$cdrEventsRecovery = $row['cdrEventsRecovery'];
				$cdrEventsUnallocated = $row['cdrEventsUnallocated'];
				
				if ($status=='DOWN') {
					//not registered
					$icon = "<i class='fa fa-phone-square fa-2x text-danger fa-fw'></i> ";
				} else if ($status=='UP') {
					//registered
					$icon = "<i class='fa fa-phone-square fa-2x  text-success fa-fw'></i> ";
				}  else if (strpos($name, 'Kyocera') !== false) {
					$icon = "<i class='fa fa-mobile fa-2x  text-success fa-fw'></i> ";
				}	else  {
					$icon = "<i class='fa fa-question-circle fa-2x' data-toggle='tooltip' title='Not Yet Installed'></i>";

				}
				
				
				
				$cdrIcon = "";
				$cdrIcon2 = "";	
				$configIcon = "";
				$cfIcon  ="";
				if ($cdrEventsRecovery>0) {
					
					//$cdrIcon = "<a data-toggle='tooltip' title='{$cdrEventsRecovery} recent inbound RECOVERY call events.'><i class='fa fa-exclamation text-danger'></i></a>";
				}
				if ($cdrEventsUnallocated>0) {
					
					//$cdrIcon2 = "<a data-toggle='tooltip' title='{$cdrEventsUnallocated} recent inbound UNALLOCATED call events.'><i class='fa fa-mobile text-danger'></i></a>";
				}
				
				if ($user == 'admin') {
					if ($config > 0) {
						$cfIcon = "<i class='fa fa-cog fa-2x text-success'></i>";
						
					} else {
						$cfIcon = "<i class='fa fa-cog fa-2x text-danger'></i>";
					}
					$configIcon = "<td><a href='#' data-toggle='modal' data-target='#configModal' onclick='javascript:get_config(\"" . $row['deviceId'] . "\",\"" . $name . "\",\"" . $row['ownerId'] . "\",\"" . $last_name . "\")'>" . $cfIcon . "</a></td>";
				}
			$s .= "<tr>" . $configIcon . "<td align=left nowrap><a href='#' data-toggle='modal' data-target='#eventModal' onclick='javascript:get_events(\"" . $row['deviceId'] . "\")'>" . $icon . "{$cdrIcon} {$cdrIcon2}</a></td><TD align=left>"  . $row['name'] . "</td><TD nowrap align=left>&nbsp;&nbsp;&nbsp;&nbsp;"  . $network_ip . ":" . $network_port . "</td><td nowrap>" . substr($user_agent,0,60) . "</td><td><a href='#' data-toggle='modal' data-target='#jsonModal' onclick='javascript:get_json(\"" . $row['deviceId'] . "\")'>JSON</a></td></tr>";
			}
			$s .= "</table>";
		}
		
		echo $s;
	}


?>