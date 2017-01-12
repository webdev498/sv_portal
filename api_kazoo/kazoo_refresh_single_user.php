<?php
include 'kazoo_token.php';
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');


set_time_limit (30);

include "inc_db.php";

$accountId = $_REQUEST['accountId'];
$userId = $_REQUEST['userId'];

if ($accountId and $userId) {
		
	//get the user details
	$url = "https://api.zswitch.net:8443/v2/accounts/" . $accountId ."/users/" . $userId;
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

	$first_name = $user->data->first_name;
	$last_name = $user->data->last_name;
	$email = $user->data->email;
	$timezone = $user->data->timezone;
			
	$callerid = $user->data->caller_id->external->number;

	$sql = "INSERT INTO KazooUsers (userId, first_name, last_name, email, timezone, callerid, accountId) VALUES ('{$userId}','{$first_name}','{$last_name}','{$email}','{$timezone}','{$callerid}','{$accountId}') ON DUPLICATE KEY UPDATE first_name='{$first_name}', last_name='{$last_name}',email='{$email}',timezone='{$timezone}',callerid='{$callerid}', accountId='{$accountId}';";
	//echo $sql . "<BR>";
	mysql_select_db($db);
	$retval1 = mysql_query( $sql, $conn );  
	
	echo "User details updated successfully.";
}	else {
	echo "ERROR: need userId and accountId";
}





?>