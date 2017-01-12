<?php


//Sends a daily email to project managers with LNP summary, 1 per customer

date_default_timezone_set('America/Chicago');

include "inc_db.php";

	$sql = "SELECT * FROM tblCustomers";  
   mysql_select_db($db);
   $retval1 = mysql_query( $sql, $conn );  
   while($row1 = mysql_fetch_array($retval1, MYSQL_ASSOC))
	{
		$customer = $row1['customer'];
		$customerID = $row1['customerID'];
		$cisPMemail = $row1['cisPMemail'];
		
		$OpenLNP = true;
		$ReadyToPort = true;

	$e = "<H2>SimpleVoIP LNP Summary for {$customer}</H2>";

	$e .= "<table border=1 cellpadding=2 cellspacing=5 class='table table-hover'><thead><tr><th>Customer</th><th>Site</th><th>FOC Date</th><th>DID</th><th>Ported</th><th>LNP Status</th><th>Last Update</th></tr></thead><tbody>";
			
		$now = date("Y-m-d");	
		//$testdate = 
		
		$sql = "SELECT * FROM vwOrders WHERE orderType = 'LNP' AND orderStatus != 'CANCELED' AND (lnpStatus NOT IN ('CLOSED', 'CANCELED') OR (lnpStatus = 'CLOSED' AND lnptestdate > DATE_SUB(now(), INTERVAL 7 DAY)) ) AND customerID={$customerID} ORDER BY lnpstatus";

  
	   mysql_select_db($db);
	   $retval = mysql_query( $sql, $conn );  

		if(mysql_num_rows($retval) == 0)
		{
			$e .= "<TR><TD colspan=10>No open LNP orders.</td></tr>";
			$OpenLNP = false;
		}
		else
		{
			
			while($row = mysql_fetch_array($retval, MYSQL_ASSOC))

			{
				$thisStatus = $row['lnpstatus'];
				if ($thisStatus !== $lastStatus) {
					
					$e .= "<TR><TD colspan=7><BR><B>LNP Status: {$thisStatus}</B></TD></TR>";
				}
					
				
				$e .= "<tr>";
			
				$focdate = "";
				if ($row['focdate']) {
					$focdate = substr($row['focdate'],0,10);
				}
				$lnptestdate = "";
				if ($row['lnptestdate']) {
					$lnptestdate = substr($row['lnptestdate'],0,10);
				}
				
				$e .= "<td nowrap>" . $row['customer'] . "</td>";
				$e .= "<td nowrap>" . $row['siteNumber'] . "</td>";
				$e .= "<td nowrap>" . $focdate . "</td>";
				$e .= "<td nowrap>" . $row['did'] . "</td>";
				$e .= "<td nowrap>" . $lnptestdate . "</td>";				
				$e .= "<td nowrap>" . $row['lnpstatus'] . "</td>";
				$e .= "<td nowrap>" . $row['lnpLastStatusUpdate'] . "</td></tr>";
		
		
			$lastStatus = $thisStatus;
			} 
			
		}
		
		
			$e .= "</tbody></table>";	
		echo $e;

	//Ready to port
	$e2 = "<BR><H2>Activated Sites ready to port, waiting for authorization</H2><table border=1 cellpadding=2 cellspacing=5 class='table table-hover'><thead><tr><th>Customer</th><th>Site</th><th>Activated</th><th>DID</th></tr></thead><tbody>";
			

		
		$sql = "SELECT * FROM vwOrders WHERE (orderType != 'LNP' AND didType='PORT REQUESTED' AND lnpCount=0 AND orderStatus   IN ('ACTIVATED', 'BILLED'))  AND customerID={$customerID} order by activateDate";

  
	   mysql_select_db($db);
	   $retval = mysql_query( $sql, $conn );  

		if(mysql_num_rows($retval) == 0)
		{
			$e2 .= "<TR><TD colspan=10>No sites ready to port.</td></tr>";
			$ReadyToPort = false;
		}
		else
		{
			
			while($row = mysql_fetch_array($retval, MYSQL_ASSOC))

			{
				
				
				$e2 .= "<tr>";
			
							
				$e2 .= "<td nowrap>" . $row['customer'] . "</td>";
				$e2 .= "<td nowrap>" . $row['siteNumber'] . "</td>";
				$e2 .= "<td nowrap>" . $row['activateDate'] . "</td>";
				$e2 .= "<td nowrap>" . $row['did'] . "</td>";					
				

			} 
			
		}
		
		
			$e2 .= "</tbody></table>";	
			
		echo $e2;
		//echo $cisPMemail;
		//$cisPMemail = "jrobs@gecko2.com,josh.robbins@cisvpn.com";
		//send mail if open orders
		if ($OpenLNP OR $ReadyToPort) {
			
		
		
		
		
			$headers = 'From: noreply@simplevoip.us' . "\r\n" .
				'Reply-To: noreply@simplevoip.us' . "\r\n" .
				'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
			$subject = "LNP Report: " . $customer;
			$msg = $e . $e2;
			$msg = wordwrap($msg,70);
			
			
			$to = $cisPMemail;
			//$to = "jrobs@gecko2.com";
			mail($to, $subject, $msg, $headers);
						
			
		}
	} //end customer loop

?>