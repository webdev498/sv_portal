<?php include "inc_db.php";

$filename = $_SERVER['REQUEST_URI'];

header("Content-Type: text/plain");
date_default_timezone_set('America/Chicago');
$now = date("Y-m-d H:i:s");
		
		
function get_random_port() {
	$random = mt_rand(10100,64000);
	$random_port = $random & ~1;	//make it an even number
	return $random_port;
}

$mac = substr($filename,1,12);

	//Update the last accessed field
	$sql = "UPDATE apCONFIGS SET lastAccess = '{$now}' WHERE mac='{$mac}'";
	mysql_select_db($db);
	$retval2 = mysql_query( $sql, $conn );  
	

	$sql = "CALL sp_getConfig('{$mac}');";
	mysql_select_db($db);
	$retval = mysql_query( $sql, $conn );  
	
	if(mysql_num_rows($retval) == 0)
	{
		echo "MAC {$mac} not found.";
	}
	else
	{
		$row = mysql_fetch_array($retval, MYSQL_ASSOC); 
		
		$deviceID = $row['deviceID'];
		$accountId = $row['accountId'];
		$baseTemplateName = $row['baseTemplateName'];
		$customerTemplateName = $row['customerTemplateName'];
		$codecTemplateName = $row['codecTemplateName'];
		$baseConfig = $row['baseConfig'];
		$customerConfig = $row['customerConfig'];
		$codec = $row['codec'];
		$codecConfig = $row['codecConfig'];
		$transport = $row['transport'];
		$proxy = $row['proxy'];
		$customer = $row['customer'];
		
		$ownerId = $row['ownerId'];
		$userName = $row['first_name'] . " " . $row['last_name'];
		$timezone = $row['timezone'];
		$callerid = $row['callerid'];
		$lastUpdate = $row['lastUpdate'];
		$realm = $row['realm'];
		
		$deviceName = $row['name'];
		$username = $row['username'];
		$password = $row['password'];
		
		$deviceName2 = $row['a2_name'];
		$username2 = $row['a2_username'];
		$password2 = $row['a2_password'];
		
		$deviceName3 = $row['a3_name'];
		$username3 = $row['a3_username'];
		$password3 = $row['a3_password'];

		//TEMP for SELECT COMFORT
		$ext_slc = substr($username,-5);
		
		$loc = strpos($username,'_')+1;
		$extension = substr($username,-(strlen($username)-$loc));
		
		$loc2 = strpos($username2,'_')+1;
		$extension2 = substr($username2,-(strlen($username2)-$loc2));
		
		$loc3 = strpos($username3,'_')+1;
		$extension3 = substr($username3,-(strlen($username3)-$loc3));
		
		//Build the config
				
		$header .= "################################################################################################\n" .
		"## CUSTOMER: 	{$customer}\n" . 						
		"## USER: 	{$userName}\n" . 						
		"## DEVICE: 	{$deviceName}\n" .					
		"## MAC: 	{$mac}\n" .	
		"## CODEC: 	{$codec}\n" .
		"## TRANSPORT: 	{$transport}\n" .	
		"## PROXY: 	{$proxy}\n" .			
		"## UPDATED:	{$lastUpdate}\n##\n" .	
		"##               _____            __   _   __     _______ \n" .
		"##              / __(_)_ _  ___  / /__| | / /__  /  _/ _ \ \n" .
		"##             _\ \/ /  ' \/ _ \/ / -_) |/ / _ \_/ // ___/ \n" .
		"##            /___/_/_/_/_/ .__/_/\__/|___/\___/___/_/    \n" .
		"##                       /_/    \n".	   
		"################################################################################################\n" . 
		"\n################################################################################################\n## BEGIN BASE CONFIG: {$customer}/{$customerTemplateName} \n################################################################################################\n";

		//Default UDP Transport
		$transport_type = '0';
		
		//TCP check
		if ($transport == 'TCP') {
	
			$transport_type = '1';

		}
		
		//Time Zone
		switch ($timezone) {
			case "America/New_York":
				$timezoneOffset = -5;
				$timezoneName = "United States-Eastern Time";
				$primaryHeadend = "us-east.simplevoip.us";
				break;
			case "America/Los_Angeles":
				$timezoneOffset = -8;
				$timezoneName = "United States-Pacific Time";
				$primaryHeadend = "us-west.simplevoip.us";
				break;
			case "America/Chicago":
				$timezoneOffset = -6;
				$timezoneName = "United States-Central Time";
				$primaryHeadend = "us-central.simplevoip.us";
				break;
			case "America/Denver":
				$timezoneOffset = -7;
				$timezoneName = "United States-Mountain Time";
				$primaryHeadend = "us-west.simplevoip.us";
				break;
			default:
				// Default to pacific if not specified
				$timezoneOffset = -8;
				$timezoneName = "United States-Pacific Time";
				$primaryHeadend = "us-west.simplevoip.us";
				break;
		}
		
		//Override Proxy
		switch ($proxy) {
			case "WEST":
				$primaryHeadend = "us-west{$tcp}.simplevoip.us";
				break;
			case "EAST":
				$primaryHeadend = "us-east{$tcp}.simplevoip.us";
				break;
			case "CENTRAL":
				$primaryHeadend = "us-central{$tcp}.simplevoip.us";
				break;
			default:
				break;
		}
		 
		$baseConfig = str_replace('{{HEADER}}', $header, $baseConfig);
		$baseConfig = str_replace('{{TIMEZONE-OFFSET}}', $timezoneOffset, $baseConfig);
		$baseConfig = str_replace('{{TIMEZONE-NAME}}', $timezoneName, $baseConfig);
		$baseConfig = str_replace('{{PRIMARY-HEADEND}}', $primaryHeadend, $baseConfig);
		$baseConfig = str_replace('{{REALM}}', $realm, $baseConfig);
		
		$customerConfig = str_replace('{{A1-EXTENSION}}', $extension, $customerConfig);
		$baseConfig = str_replace('{{A1-DISPLAY-NAME}}', $deviceName, $baseConfig);
		$baseConfig = str_replace('{{A1-LABEL}}', $deviceName, $baseConfig);
		$baseConfig = str_replace('{{A1-USER-NAME}}', $username, $baseConfig);
		$baseConfig = str_replace('{{A1-AUTH-NAME}}', $username, $baseConfig);
		$baseConfig = str_replace('{{A1-PASSWORD}}', $password, $baseConfig);
		
		$a2_enable='0';
		$a3_enable='0';
		if ($username2) {
			$a2_enable='1';
		}
		if ($username3) {
			$a3_enable='1';
		}
		$customerConfig = str_replace('{{A2-EXTENSION}}', $extension2, $customerConfig);
		$baseConfig = str_replace('{{A2-ENABLE}}', $a2_enable, $baseConfig);
		$baseConfig = str_replace('{{A2-DISPLAY-NAME}}', $deviceName2, $baseConfig);
		$baseConfig = str_replace('{{A2-LABEL}}', $deviceName2, $baseConfig);
		$baseConfig = str_replace('{{A2-USER-NAME}}', $username2, $baseConfig);
		$baseConfig = str_replace('{{A2-AUTH-NAME}}', $username2, $baseConfig);
		$baseConfig = str_replace('{{A2-PASSWORD}}', $password2, $baseConfig);
		
		$customerConfig = str_replace('{{A3-EXTENSION}}', $extension3, $customerConfig);
		$baseConfig = str_replace('{{A3-ENABLE}}', $a3_enable, $baseConfig);
		$baseConfig = str_replace('{{A3-DISPLAY-NAME}}', $deviceName3, $baseConfig);
		$baseConfig = str_replace('{{A3-LABEL}}', $deviceName3, $baseConfig);
		$baseConfig = str_replace('{{A3-USER-NAME}}', $username3, $baseConfig);
		$baseConfig = str_replace('{{A3-AUTH-NAME}}', $username3, $baseConfig);
		$baseConfig = str_replace('{{A3-PASSWORD}}', $password3, $baseConfig);
		
		$baseConfig = str_replace('{{RANDOM_PORT}}', get_random_port(), $baseConfig);
		$baseConfig = str_replace('{{RANDOM_PORT2}}', get_random_port(), $baseConfig);
		$baseConfig = str_replace('{{RANDOM_PORT3}}', get_random_port(), $baseConfig);
		$baseConfig = str_replace('{{RANDOM_PORT4}}', get_random_port(), $baseConfig);
		$baseConfig = str_replace('{{RANDOM_PORT5}}', get_random_port(), $baseConfig);
		
		$baseConfig = str_replace('{{MAC}}', $mac, $baseConfig);
		
		$baseConfig = str_replace('{{DEVICENAME}}', $deviceName, $baseConfig);
		$baseConfig = str_replace('{{TRANSPORT-TYPE}}', $transport_type, $baseConfig);
		
		
		echo $baseConfig . "\n";
		echo "\n################################################################################################\n## END BASE CONFIG: {$baseTemplateName} \n################################################################################################\n\n";
		
		echo "\n################################################################################################\n## BEGIN CUSTOMER CONFIG: {$customer}/{$customerTemplateName} \n################################################################################################\n\n";
		echo $customerConfig . "\n";
		echo "\n################################################################################################\n## END CUSTOMER CONFIG: {$customer}/{$customerTemplateName} \n################################################################################################\n\n";
		
		echo "\n################################################################################################\n## BEGIN CODEC CONFIG: {$codecTemplateName} \n################################################################################################\n\n";
		echo $codecConfig . "\n";
		echo "\n################################################################################################\n## END CODEC CONFIG: {$codecTemplateName} \n################################################################################################\n\n";
		
		
		
		
	
		
		
	}
	mysql_close($conn);
?>