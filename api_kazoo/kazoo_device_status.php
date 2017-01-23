<?php
include 'kazoo_token.php';
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

date_default_timezone_set('UTC');
$now = date("Y-m-d H:i:s");

include "inc_db.php";

echo "<PRE>";
	//Get the accounts that are monitored

	
	$sql = "SELECT * FROM KazooAccounts;";
	mysql_select_db($db);
	$accounts = mysql_query( $sql, $conn );  
	
	while($row = mysql_fetch_array($accounts, MYSQL_ASSOC)) {
		
		$accountId = $row['accountId'];
		
		//Get all device statuses
		$url = "https://api.zswitch.net:8443/v2/accounts/" . $accountId ."/devices/status";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		  array(
				'Accept: application/json',
				'Content-Type: application/json', 
				'X-Auth-Token: ' . $auth_token
			   )
		);   
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$devices_req = curl_exec($ch);
		curl_close($ch);

		$devices = json_decode($devices_req);
		//var_dump($devices);
		
		$device_list = array();
		$device_list = $devices->data;
		
		$arrDevice = array();
		foreach ($device_list as $key => $arrDevice) {
			$cnt++;
			
			$deviceId = $arrDevice->device_id;
			$registered = $arrDevice->registered;
		
			if ($registered) {
				$regCnt++;
				$sql = "UPDATE KazooDevices SET LastRegistered = '{$now}' WHERE deviceId='{$deviceId}';";
			}
			echo $sql;
			mysql_select_db($db);
			$retval1 = mysql_query( $sql, $conn );  
		
		}
			
		//Get registrations
	
		$url = "https://api.zswitch.net:8443/v2/accounts/" . $accountId . "/registrations";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		  array(
				'Accept: application/json',
				'Content-Type: application/json', 
				'X-Auth-Token: ' . $auth_token
			   )
		);   
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$devices_req = curl_exec($ch);
		curl_close($ch);

		$devices = json_decode($devices_req);
		
		$device_list = array();
		$device_list = $devices->data;
		
		$arrDevice = array();
		$cntDevice = 1;
		foreach ($device_list as $key => $arrDevice) {
			
			
			$deviceId = $arrDevice->authorizing_id;
			
			if ($cntDevice==1) {
				$deviceString = "'" . $deviceId . "'";
			} else {
				$deviceString = $deviceString . ",'" . $deviceId . "'";
			}
		
			$contact = $arrDevice->contact;
			$contact_ip = $arrDevice->contact_ip;
			$contact_port = $arrDevice->contact_port;
			$event_timestamp = $arrDevice->event_timestamp;
			$expires = $arrDevice->expires;
			$from_host = $arrDevice->from_host;
			$from_user = $arrDevice->from_user;
			$network_ip = $arrDevice->source_ip;
			$network_port = $arrDevice->source_port;
			$owner_id = $arrDevice->owner_id;
			$proxy_ip = $arrDevice->proxy_ip;
			$proxy_port = $arrDevice->proxy_port;
			$account_name = $arrDevice->account_name;
			$user_agent = $arrDevice->user_agent;
			$proxy_path = $arrDevice->proxy_path;
			$raw_json = json_encode($arrDevice);
			echo $raw_json;
			$sql = "INSERT INTO KazooRegistrations (deviceId, contact, contact_ip, contact_port, event_timestamp, expires, from_host, from_user, network_ip, network_port, owner_id, proxy_ip, proxy_port, account_name, user_agent, proxy_path, raw_json) VALUES ('{$deviceId}','{$contact}','{$contact_ip}','{$contact_port}','{$event_timestamp}','{$expires}','{$from_host}','{$from_user}','{$network_ip}','{$network_port}','{$owner_id}','{$proxy_ip}','{$proxy_port}','{$account_name}','{$user_agent}','{$proxy_path}','{$raw_json}') " .			
					" ON DUPLICATE KEY UPDATE deviceId='{$deviceId}',contact='{$contact}',contact_ip='{$contact_ip}',contact_port='{$contact_port}',event_timestamp='{$event_timestamp}',expires='{$expires}',from_host='{$from_host}',from_user='{$from_user}',network_ip='{$network_ip}',network_port='{$network_port}',owner_id='{$owner_id}',proxy_ip='{$proxy_ip}',proxy_port='{$proxy_port}',account_name='{$account_name}',user_agent='{$user_agent}',proxy_path='{$proxy_path}',raw_json='{$raw_json}'";


			//echo $sql;
			mysql_select_db($db);
			$retval1 = mysql_query( $sql, $conn );  
		
		}	
		
			
	}
	//$unregCnt = $cnt - $regCnt;
	$notes = $cnt . " devices currently registered.";

	if ($cnt > 700) {
		//update the monitor table if there are devices returned. using 700 just because.
		$sql = "UPDATE KazooMonitor SET LastDeviceStatusUpdate = '{$now}' WHERE id=1";
		mysql_select_db($db);
		$retval1 = mysql_query( $sql, $conn );  
	} else {
		$notes = "**ERROR**" . $notes;
		
	}
	//Clean up registration table
	if ($cntDevice > 1000) {
		$sql = "DELETE FROM KazooRegistrations WHERE deviceId NOT IN ({$deviceString});";
		echo $sql;
		mysql_select_db($db);
		$retval1 = mysql_query( $sql, $conn );  
	}

	//update the monitor log
	$sql = "INSERT INTO KazooMonitorLog (updateDate, notes) VALUES ('{$now}', '{$notes}')";
	mysql_select_db($db);
	$retval1 = mysql_query( $sql, $conn );  
	
	//update last public IPs
	$sql = "update KazooDevices d set lastPublicIP = (select network_ip from KazooRegistrations r where r.deviceId=d.deviceId), lastPrivateIP = (select contact_ip from KazooRegistrations rr where rr.deviceId=d.deviceId)";
	mysql_select_db($db);
	$retval1 = mysql_query( $sql, $conn );  
	

echo "STATUS UPDATE COMPLETE. \n Devices: {$cnt}";

	
	

?>