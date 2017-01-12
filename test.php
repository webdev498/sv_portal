<?php include 'inc_header.php';

header("Content-Type: text/plain");

	$mac = 'AABBCC112233';

	$sql = "CALL sp_getConfig('{$mac}')";
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
		$deviceName = $row['name'];
		$ownerId = $row['ownerId'];
		$userName = $row['first_name'] . " " . $row['last_name'];
		$timezone = $row['timezone'];
		$callerid = $row['callerid'];
		$lastUpdate = $row['lastUpdate'];
		$username = $row['username'];
		$password = $row['password'];
		$realm = $row['realm'];

		//Random port
		$random = mt_rand(10100,64000);
		$random_port = $random & ~1;	//make it an even number
		
		//Build the config
				
		$header .= "########################################################\n" .
		"## CUSTOMER: 	{$customer}\n" . 						
		"## USER: 	{$userName}\n" . 						
		"## DEVICE: 	{$deviceName}\n" .					
		"## MAC: 	{$mac}\n" .	
		"## CODEC: 	{$codec}\n" .
		"## TRANSPORT: 	{$transport}\n" .	
		"## PROXY: 	{$proxy}\n" .			
		"## UPDATED:	{$lastUpdate}\n" .					
		"########################################################\n";
		
		
		//TCP check
		if ($transport == 'TCP') {
			$tcp = '-tcp';
		}
		
		//Time Zone
		switch ($timezone) {
			case "America/New_York":
				$timezoneOffset = -5;
				$timezoneName = "United States-Eastern Time";
				$primaryHeadend = "us-east{$tcp}.simplevoip.us";
				break;
			case "America/Los_Angeles":
				$timezoneOffset = -8;
				$timezoneName = "United States-Pacific Time";
				$primaryHeadend = "us-west{$tcp}.simplevoip.us";
				break;
			case "America/Chicago":
				$timezoneOffset = -6;
				$timezoneName = "United States-Central Time";
				$primaryHeadend = "us-central{$tcp}.simplevoip.us";
				break;
			case "America/Denver":
				$timezoneOffset = -7;
				$timezoneName = "United States-Mountain Time";
				$primaryHeadend = "us-west{$tcp}.simplevoip.us";
				break;
			default:
				// Default to pacific if not specified
				$timezoneOffset = -8;
				$timezoneName = "United States-Pacific Time";
				$primaryHeadend = "us-west{$tcp}.simplevoip.us";
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
		
		
		$baseConfig = str_replace('{{A1-DISPLAY-NAME}}', $userName, $baseConfig);
		$baseConfig = str_replace('{{A1-LABEL}}', $userName, $baseConfig);
		$baseConfig = str_replace('{{REALM}}', $realm, $baseConfig);
		
		$baseConfig = str_replace('{{A1-USER-NAME}}', $username, $baseConfig);
		$baseConfig = str_replace('{{A1-AUTH-NAME}}', $username, $baseConfig);
		$baseConfig = str_replace('{{A1-PASSWORD}}', $password, $baseConfig);
		
		$baseConfig = str_replace('{{RANDOM_PORT}}', $random_port, $baseConfig);
		$baseConfig = str_replace('{{MAC}}', $mac, $baseConfig);
		
		$baseConfig = str_replace('{{DEVICENAME}}', $deviceName, $baseConfig);
		
		
		echo $baseConfig . "\n";
		echo "\n################################################################################################\n END BASE CONFIG: {$baseTemplateName} \n################################################################################################\n\n";
		
		echo "\n################################################################################################\n BEGIN CUSTOMER CONFIG: {$customer}/{$customerTemplateName} \n################################################################################################\n\n";
		echo $customerConfig . "\n";
		echo "\n################################################################################################\n END CUSTOMER CONFIG: {$customer}/{$customerTemplateName} \n################################################################################################\n\n";
		
		echo "\n################################################################################################\n BEGIN CODEC CONFIG: {$codecTemplateName} \n################################################################################################\n\n";
		echo $codecConfig . "\n";
		echo "\n################################################################################################\n END CODEC CONFIG: {$codecTemplateName} \n################################################################################################\n\n";
		
		
		
		
	}

?>