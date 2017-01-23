<?php

//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

date_default_timezone_set('UTC');
$now = date("Y-m-d H:i:s");

include "inc_db.php";


	$sql = "SELECT *, (select LastDeviceStatusUpdate from KazooMonitor where id=1) as LastUpdate, (select last_name from KazooUsers u where u.userId = KazooDevices.ownerId) as last_name,(select name from KazooAccounts where accountId = KazooDevices.accountId) as customer,(select eventDate from KazooStatusEvents where deviceId=KazooDevices.deviceId and type='DOWN' order by eventDate DESC limit 1) as lastDown FROM KazooDevices WHERE name not like 'X_%';";
	mysql_select_db($db);
	$devices = mysql_query( $sql, $conn );  

	
	$eventCount = 0;
	while($row = mysql_fetch_array($devices, MYSQL_ASSOC)) {
		$cnt++;
		$deviceId = $row['deviceId'];
		$userId = $row['ownerId'];
		$LastRegistered = $row['LastRegistered'];
		$LastUpdate = $row['LastUpdate'];
		$lastDown = $row['lastDown'];
		$accountId = $row['accountId'];
		$name = $row['name'];
		$customer = $row['customer'];
		$site = $row['last_name'];
		$cis_process = 0;
		$status = $row['status'];
		$accountId = $row['accountId'];
		//$priorStatus = $row['priorStatus'];
		
		//difference in seconds
		$diff=strtotime($LastUpdate) - strtotime($LastRegistered);	
		
		//SKIP if X_
		if (substr($customer,0,1) != 'X_') {
			
			$type = "";
			$sqlstatus = "";
			$downtime = 0;
			
			if (!$LastRegistered AND $status=='') {
			
			//do nothing, never registered
			//set status to NOT AVAILABLE
			$sqlstatus = "UPDATE KazooDevices SET status='NOT AVAILABLE' WHERE deviceID='{$deviceId}'";
			$eventCount++;
			} 
			else 
			{
			
				if ($diff >= 600 AND $status=='UP') {
					//Expire after 10 minutes
					//copy status to prior status
					//set status to EXPIRED
					//Log a DOWN event
					$type = "DOWN";
					$event = "DEVICE DOWN: " . $name . ". Last online at " . $LastRegistered;
					$sqlstatus = "UPDATE KazooDevices SET status='DOWN' WHERE deviceID='{$deviceId}'";
					$eventCount++;
				
				}
				if ($diff <= 600 AND ($status=='DOWN')) {
					//Back online
					//set status to REGISTERED
					//Log an UP event
					$type = "UP";
					$downtime=round((strtotime($lastDown) - strtotime($LastRegistered))/60);	
					$event = "DEVICE UP: " . $name . ". DOWN TIME: " . $downtime . " minutes.";
					$sqlstatus = "UPDATE KazooDevices SET status='UP' WHERE deviceID='{$deviceId}'";
					$eventCount++;
				}
				if ($diff <= 600 AND ($status=='' OR $status=='NOT AVAILABLE')) {
					//First time online
					//set status to REGISTERED
					//Log an UP event - first time
					$type = "UP";
					$cis_process = 1;
					$event = "DEVICE UP: " . $name . ". **This is the first time this device has been online**";
					$sqlstatus = "UPDATE KazooDevices SET status='UP' WHERE deviceID='{$deviceId}'";
					$eventCount++;
				}
			
			}
			if (substr($customer,0,1) == 'X_') {
				$cis_process = 1;
			}
			if ($type) {
				$sql = "INSERT INTO KazooStatusEvents (userId, accountId, customer,type, deviceId, event, eventDate, site, downtime, cis_process, email) VALUES ('{$userId}','{$accountId}','{$customer}', '{$type}', '{$deviceId}', '{$event}', '{$now}', '{$site}',{$downtime},{$cis_process},COALESCE((select email from tblCustomerLocations where customerID = (select customerID from tblCustomers where customer='{$customer}') and siteNumber = '{$site}'),''))";
				echo $sql . "\n";
				mysql_select_db($db);
				$retval1 = mysql_query( $sql, $conn );  
			}
			if ($sqlstatus) {
				echo $sqlstatus . "\n";
				mysql_select_db($db);
				$retval1 = mysql_query( $sqlstatus, $conn ); 
			}
		
		}	// if not X_
		
	}	// end while
	
	//update the monitor table
	$sql = "UPDATE KazooMonitor SET LastEventProcess = '{$now}' WHERE id=1";
	mysql_select_db($db);
	$retval1 = mysql_query( $sql, $conn );  

	echo "Event Update Complete. \n Events: {$eventCount}, checked {$cnt} devices.";	
			
			




	

?>