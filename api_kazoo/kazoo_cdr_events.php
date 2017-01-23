<?php
include "inc_db.php";
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

date_default_timezone_set('UTC');

$now = date("Y-m-d H:i:s");

	$sql = "Select * from KazooMonitor WHERE id=1";
	mysql_select_db($db);
	$retval = mysql_query( $sql, $conn );
	
	if(mysql_num_rows($retval) == 0)
	{
		echo "Not found.";
		
	}
	else
	{
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		$lastProcess = $row['LastCDREventFound'];
		$cnt = 0;

		$dbconn = pg_connect("host=sv-postgres.cilqdskq1dv5.us-east-1.rds.amazonaws.com port=5432 dbname=cdr2db user=cdr2db password=Vl37yZnf5DSg");
		$sql = "select *,(select name from accounts where id=d.account_id) as customer,(select last_name from users u where u.id = d.owner_id) as last_name  from data d where datetime > '{$lastProcess}' and hangup_cause IN ('RECOVERY_ON_TIMER_EXPIRE','UNALLOCATED_NUMBER') AND direction='outbound' AND request like '%simplevoip%' order by datetime asc";
		$retval = pg_query($dbconn, $sql);
		//echo $sql;
		if(pg_num_rows($retval) == 0)
		{
			echo "No calls found.";
		}
		else
		{
			while ($row = pg_fetch_array($retval)) 
			{
				$cnt++;
				$callee_id_name = $row['callee_id_name'];
				$callee_id_number = $row['callee_id_number'];
				$caller_id_name = $row['caller_id_name'];
				$caller_id_number = $row['caller_id_number'];
				$dialed_number = $row['dialed_number'];
				$deviceId = $row['authorizing_id'];
				$account_id = $row['account_id'];
				$datetime = $row['datetime'];
				$userId = $row['owner_id'];
				$hangup_cause = $row['hangup_cause'];
				$id = $row['id'];
				$customer = $row['customer'];
				$site = $row['last_name'];
				$type="INBOUND ERROR: " . $hangup_cause;
				$event = $hangup_cause . " on user " . $callee_id_name . ". From: " . $caller_id_number . " To: " . $callee_id_number . " at " . $datetime;
				
				$cis_process = 0;
				
				if ($type == 'INBOUND ERROR: UNALLOCATED_NUMBER') {
					$event .= "\n\nOur monitoring systems show that you were not able to receive a call on your Panasonic cordless phone. This is normally because a handset is turned off or out of batteries and is very easy to fix. Please see our wiki like below for a quick tutorial on how to turn on your handset. Reminder: please do NOT turn it off at night!";
					$event .= "\n\nhttp://simplevoip.editme.com/Panasonic-TGP600-How-to-Turn-On-Handset";
					//$event .= "\n\nSUPPORT: Please confirm this issue has been resolved before closing the ticket.";
					$cis_process=1;	//temp - need to check if this is chronic
					//SEND EMAIL TO SITE
					
				}
				if ($type == 'INBOUND ERROR: RECOVERY_ON_TIMER_EXPIRE') {
					$cis_process=1;
				}
				if (substr($customer,0,1) != 'X_') {	//do not alert if X_ customer
					$cis_process=1;
				}
				
				
				$sql = "INSERT INTO KazooStatusEvents (accountId, customer,type, deviceId, event, eventDate, site, userId, call_id, email, cis_process) VALUES ('{$account_id}','{$customer}', '{$type}', '{$deviceId}', '{$event}', '{$datetime}', '{$site}', '{$userId}', '{$id}', (Select email from tblCustomerLocations l WHERE l.siteNumber='{$site}'	AND l.customerID=(select c.customerID from tblCustomers c where c.kazooAccountID='{$account_id}')), {$cis_process})";
				echo $sql . "<BR>";
				
				mysql_select_db($db);
				$retval1 = mysql_query( $sql, $conn );
				
			}
		
			

			
		}
		
		//update the monitor table
		if ($cnt > 0) {
			$sql = "UPDATE KazooMonitor SET LastCDREventFound = '{$datetime}' WHERE id=1";
			echo $sql;
			mysql_select_db($db);
			$retval1 = mysql_query( $sql, $conn );  
		}
		$sql = "UPDATE KazooMonitor SET LastCDREventProcess= '{$now}' WHERE id=1";
		echo $sql;
		mysql_select_db($db);
		$retval1 = mysql_query( $sql, $conn );  
		echo "Event Update Complete. \n Created events: {$cnt}.";
	}
?>