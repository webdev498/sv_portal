<?php
include 'kazoo_token.php';
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

date_default_timezone_set('UTC');
$now = date("Y-m-d H:i:s");

set_time_limit (980);

include "inc_db.php";

//Get accounts
$sql = "SELECT * from KazooDevices where password=''";
mysql_select_db($db);
$accounts = mysql_query( $sql, $conn );  


while($row = mysql_fetch_array($accounts, MYSQL_ASSOC)) {
	$deviceId = $row['deviceId'];
	$accountId = $row['accountId'];
	
	
	$url = "https://api.zswitch.net:8443/v2/accounts/" . $accountId ."/devices/" . $deviceId;
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
    
	var_dump($devices);
	
	$device_list = array();
	$device_list = $devices->data;
	
	
	
	

		
	$password = $device_list->sip->password;
	$username = $device_list->sip->username;
	$codeclist = array();
	$codecs = implode(",",$device_list->media->audio->codecs);
	

	
	$sql = "UPDATE KazooDevices SET password='{$password}', username='{$username}', codecs='{$codecs}' WHERE deviceId='{$deviceId}'";
	
	echo "<BR><BR>".$sql . "<BR><BR>";
	mysql_select_db($db);
	$retval1 = mysql_query( $sql, $conn );  

	
	

}

//update the monitor table
$sql = "UPDATE KazooMonitor SET LastSIPupdate = '{$now}' WHERE id=1";
mysql_select_db($db);
$retval1 = mysql_query( $sql, $conn );  



?>