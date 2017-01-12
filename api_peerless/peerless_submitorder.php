<?php

include("peer_soap.php");

$psoap = new PeerSoap(USER_NAME,PASSWORD,CUSTOMER);

if ($psoap->placeOrder($_GET["orderID"],$_GET["pon"],$_GET["orderType"],$_GET["tn"],$_GET["routeLabel"],
						$_GET["mou"],$_GET["accountNumber"],$_GET["atn"],$_GET["authDate"],$_GET["authnm"],
						$_GET["desiredDueDate"],$_GET["earliestPossible"],$_GET["endCustomerName"],$_GET["mi"],
						$_GET["npdi"],$_GET["sanoHouseNumber"],$_GET["sasnStrName"],
						$_GET["sasdStrDir"],$_GET["city"],$_GET["state"],$_GET["telephoneNumber"],$_GET["zipCode"]))
{
	echo("Status=1&orderID=" . $_GET["orderID"]);
}
else
{
	echo("Status=0&msg=" . $psoap->error_description);
	
}
?>