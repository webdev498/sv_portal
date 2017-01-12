<?php

// get your api key of the master top level parent account from 2600hs portal
// if necessary, you can get an auth token for each account individually using the individual
// account api key but that is a design discussion, not functionality which we are providing here
// don't use user auth as it breaks your scripts when passwords need to be reset
$api_key = 'insert api token here';
$credentials = '56d0bca622435494d9a98b292a4c6d0b';
$account_name = 'simplevoip llc';
// this is the url for grabbing an auth token on the main site
// not sure if you can use your own but you can try replacing this and see...
// $auth_url = 'https://api.zswitch.net:8443/v2/api_auth';
$auth_url = 'https://api.zswitch.net:8443/v2/user_auth';
// $auth_url = 'https://portal.simplevoip.us/v2/user_auth';

// we are building our structured data to convert into json
// do youself a favor and don't statically write your own json...
//$data = array( 'api_key' => $api_key );
$data = array( 'credentials' => $credentials,
				'account_name' => $account_name );
// this is the envelope we build around the api key
$envelope = array( 'data' => $data );
// encode our data structure into json
$data_string = json_encode($envelope);

// initialize the cURL library
// requires php5-curl library be installed - fairly basic but may need to install
$ch = curl_init();
// setup all the cURL options...
// the url
curl_setopt($ch, CURLOPT_URL, $auth_url);
// the http method
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
// the json encoded body
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
// security - this complains for self signed certs - you can try 
// setting it to 1 once you get this working without validation then deal
// with the security holes later if it doesn't work then
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// uncomment the next line if you want some verbose messages
curl_setopt($ch, CURLOPT_VERBOSE, 1);
// we want and care about the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// be sure to set the content type to json and the lenght appropriately
// add other headers to the array as required
curl_setopt($ch, CURLOPT_HTTPHEADER,
  array(
		'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
       )
);                                                                                                                           

// the actual execution
$result = curl_exec($ch);
// be sure to actually close the curl request explicitly or you may leak memory
curl_close($ch);

// decode the json string response into a php array
$values = json_decode($result);
echo "<PRE>";
echo $values->auth_token;

$auth_token = $values->auth_token;

// print the array (not the json string) for visibility
// the part you are actually interested in is the 'auth_token'
//print_r($values);
//var_dump($values);



//Move to a different file - testing here with Windsor
$url = "https://api.zswitch.net:8443/v2/accounts/ef18f777f3e17c081d370b3240679ea3/devices/status";

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
echo "<PRE>";
var_dump($devices);



?>
