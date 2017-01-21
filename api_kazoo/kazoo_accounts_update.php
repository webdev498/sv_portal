<?php
include 'kazoo_token.php';
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

date_default_timezone_set('America/Chicago');
$now = date("Y-m-d H:i:s");

set_time_limit (480);

include "inc_db.php";


$url = "https://api.zswitch.net:8443/v2/accounts/cf7cc9ec9543536dc8299d87945fc7b7/children";

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
$accounts_req = curl_exec($ch);
curl_close($ch);

$accounts = json_decode($accounts_req);
echo "<PRE>";
//var_dump($accounts);

$account_list = array();
$account_list = $accounts->data;
//echo "ACCOUNTS<BR>";
//var_dump($account_list);
$arr = array();


foreach ($account_list as $key => $arr) {
	$cntAccount++;
	echo "<BR><BR>********<BR><BR>ADDING ACCOUNT******<BR><BR>";
	$id = $arr->id;
	$name = $arr->name;
	$realm = $arr->realm;
	$descendants_count = $arr->descendants_count;
	
	//Update the table
	$sql = "INSERT INTO KazooAccounts (accountId, name, realm,descendants_count) VALUES ('{$id}','{$name}','{$realm}',{$descendants_count}) ON DUPLICATE KEY UPDATE name='{$name}', realm='{$realm}',descendants_count={$descendants_count}";
	mysql_select_db($db);
	$retval1 = mysql_query( $sql, $conn );  
    echo $sql . "<BR>";
	
	//get Child accounts
	if ($descendants_count > 0) {
		echo "<BR><BR>********<BR><BR>ADDING CHILD ACCOUNTS******<BR><BR>";
		$url = "https://api.zswitch.net:8443/v2/accounts/{$id}/children";
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
		$accounts_req2 = curl_exec($ch);
		curl_close($ch);

		$accounts2 = json_decode($accounts_req2);
		echo "<PRE>";

		$account_list2 = array();
		$account_list2 = $accounts2->data;
		
		$arr2 = array();
		foreach ($account_list2 as $key2 => $arr2) {
			$cntAccount2++;
			
			$id2 = $arr2->id;
			$name2 = $arr2->name;
			$realm2 = $arr2->realm;
			$parentAccountId = $id;
			
			//Update the table
			$sql = "INSERT INTO KazooAccounts (accountId, name, realm,parentAccountId) VALUES ('{$id2}','{$name2}','{$realm2}','{$id}') ON DUPLICATE KEY UPDATE name='{$name2}', realm='{$realm2}'";
			mysql_select_db($db);
			$retval1 = mysql_query( $sql, $conn );  
			echo $sql . "<BR>";
		}
	}
	
	

}

//update the monitor table
$sql = "UPDATE KazooMonitor SET LastAccountUpdate = '{$now}' WHERE id=1";
mysql_select_db($db);
$retval1 = mysql_query( $sql, $conn );  




?>