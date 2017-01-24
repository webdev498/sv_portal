<?php
include 'kazoo_token.php';
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

date_default_timezone_set('America/Chicago');
$now = date("Y-m-d H:i:s");

$fn = $_REQUEST['fn'];

if ($fn == 'get-registration') {
	
	$deviceId = $_REQUEST['deviceId'];
	$accountId = $_REQUEST['accountId'];
	
	$url = "https://api.zswitch.net:8443/v2/accounts/" . $accountId ."/devices/" . $deviceId . "/registration";
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

	$response = json_decode($devices_req);
	$status = $response->status;	
	echo "Reboot Sent. Status: {$status}";
}	
if ($fn == 'enable-device') {
	
}
?>