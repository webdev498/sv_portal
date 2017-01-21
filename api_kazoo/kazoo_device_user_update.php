<?php
include 'kazoo_token.php';
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

date_default_timezone_set('America/Chicago');
$now = date("Y-m-d H:i:s");

set_time_limit (980);

include "inc_db.php";

//Get accounts
$sql = "SELECT * from KazooAccounts";
mysql_select_db($db);
$accounts = mysql_query( $sql, $conn );  


while($row = mysql_fetch_array($accounts, MYSQL_ASSOC)) {
	$id = $row['accountId'];
	
	//Get all the devices for this account
	$url = "https://api.zswitch.net:8443/v2/accounts/" . $id ."/devices";
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
	
	
	echo "<BR><BR>********<BR><BR>ADDING DEVICES******<BR><BR>";
	foreach ($device_list as $key => $arrDevice) {
		$cntDevice++;
		
		$deviceId = $arrDevice->id;
		if ($cntDevice==1) {
			$deviceString = "'" . $deviceId . "'";
		} else {
			$deviceString = $deviceString . ",'" . $deviceId . "'";
		}
		$accountId = $id;
		$name = $arrDevice->name;
		$username = $arrDevice->username;
		$ownerId = $arrDevice->owner_id;
		$enabled = $arrDevice->enabled;
		$deviceType = $arrDevice->device_type;
		
		if ($enabled) {
			$enabled = 1;
		} else {
			$enabled = 0;
		}
	
		if (substr($name,0,2) == 'X_' OR $enabled ==0) {
			$monitored = 0;
			$sql = "INSERT INTO KazooDevices (deviceId, accountId, name, ownerId, enabled,monitored,username) VALUES ('{$deviceId}','{$accountId}','{$name}','{$ownerId}',{$enabled},{$monitored},'{$username}') ON DUPLICATE KEY UPDATE name='{$name}', ownerId='{$ownerId}', enabled={$enabled}, monitored={$monitored}, username='{$username}'";
			
		} else {
			$monitored = 1;
			$sql = "INSERT INTO KazooDevices (deviceId, accountId, name, ownerId, enabled,monitored,username) VALUES ('{$deviceId}','{$accountId}','{$name}','{$ownerId}',{$enabled},{$monitored},'{$username}') ON DUPLICATE KEY UPDATE name='{$name}', ownerId='{$ownerId}', enabled={$enabled}, username='{$username}'";
			$billableDevices++;
		}
		echo $sql . "<BR>";
		mysql_select_db($db);
		$retval1 = mysql_query( $sql, $conn );  
	
	}
	
	
	
	//Get all the users for this account
	$url = "https://api.zswitch.net:8443/v2/accounts/" . $id ."/users";
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
	$users_req = curl_exec($ch);
	curl_close($ch);

	$users = json_decode($users_req);
    //var_dump($users);
	
	$users_list = array();
	$users_list = $users->data;
	
	$arrUsers = array();
	
	echo "<BR><BR>********<BR><BR>ADDING USERS******<BR><BR>";
	foreach ($users_list as $key => $arrUsers) {
		$cntUser++;
		
		$userId = $arrUsers->id;
		$first_name = $arrUsers->first_name;
		$last_name = $arrUsers->last_name;
		$email = $arrUsers->email;
		$timezone = $arrUsers->timezone;
		
		
		//Now get the user details
		$url = "https://api.zswitch.net:8443/v2/accounts/" . $id ."/users/" . $userId;
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
		$user_req = curl_exec($ch);
		curl_close($ch);

		$user = json_decode($user_req);
		$user_data = array();
		$user_data = $user->data;
	
		$callerid = $user->data->caller_id->external->number;
		
		$sql = "INSERT INTO KazooUsers (userId, first_name, last_name, email, timezone, callerid, accountId) VALUES ('{$userId}','{$first_name}','{$last_name}','{$email}','{$timezone}','{$callerid}','{$accountId}') ON DUPLICATE KEY UPDATE first_name='{$first_name}', last_name='{$last_name}',email='{$email}',timezone='{$timezone}',callerid='{$callerid}', accountId='{$accountId}';";
		echo $sql . "<BR>";
		mysql_select_db($db);
		$retval1 = mysql_query( $sql, $conn );  
	
	}	

}

//update the monitor table
$sql = "UPDATE KazooMonitor SET LastUserUpdate = '{$now}',LastDeviceUpdate = '{$now}', userCount={$cntUser},deviceCount={$cntDevice}, billableDeviceCount={$billableDevices} WHERE id=1";
mysql_select_db($db);
$retval1 = mysql_query( $sql, $conn );  

//Clean up devices table
if ($cntDevice > 1000) {
	$sql = "DELETE FROM KazooDevices WHERE deviceId NOT IN ({$deviceString});";
	echo $sql;
	mysql_select_db($db);
	$retval1 = mysql_query( $sql, $conn );  
}

$emaillog =  "UPDATE COMPLETE at {$now}. \n  Users: {$cntUser}, Devices: {$cntDevice}, Billable Devices: {$billableDevices}";
echo $emaillog;
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
mail("jrobs@simplevoip.us", "Kazoo Sync Complete", $emaillog, $headers);	

?>