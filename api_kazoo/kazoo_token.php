<?php


$api_key = 'insert api token here';
$credentials = '56d0bca622435494d9a98b292a4c6d0b';
$account_name = 'simplevoip llc';

// $auth_url = 'https://api.zswitch.net:8443/v2/api_auth';
$auth_url = 'https://api.zswitch.net:8443/v2/user_auth';
// $auth_url = 'https://api.simplevoip.us:8443/v2/user_auth';


//$data = array( 'api_key' => $api_key );
$data = array( 'credentials' => $credentials,
				'account_name' => $account_name );

$envelope = array( 'data' => $data );
$data_string = json_encode($envelope);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $auth_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER,
  array(
		'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
       )
);                                                                                                                           

$result = curl_exec($ch);
curl_close($ch);

$values = json_decode($result);
//echo "<PRE>";
//echo $values->auth_token;
$auth_token = $values->auth_token;


