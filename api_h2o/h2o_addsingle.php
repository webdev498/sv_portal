<?php

session_start();

include('h2osoap.php');
$ret_str = "../orderdetails.php?orderID=" . $_GET['orderID'] . "&status=";

$hsoap = new H2OSoap(USER_NAME,PASSWORD,CLIENT_CODE);

// customer_lookup stores the customer id in H2OSoap->customer_id
if ($hsoap->CustomerLookup($_GET["tn"]))
{
	
	if($hsoap->Transaction($hsoap->customer_id,
			$_GET['productID'],
			$_GET['amount'],
			$_GET['quantity'],
			$_GET['effectiveDate'],
			$_GET['effectiveDate']))
	{
		$ret_str .= "1&action=billed&customerid=" .$hsoap->customer_id;
	}
	else
	{
		$ret_str .= "0";
		$_SESSION["ErrorCode"] = $hsoap->error_code;
		$_SESSION["ErrorMessage"] = $hsoap->error_description;
	}
}
else
{
	$ret_str .= "0";
	$_SESSION["ErrorCode"] = $hsoap->error_code;
	$_SESSION["ErrorMessage"] = $hsoap->error_description;
}

header('location:' . $ret_str);
?>