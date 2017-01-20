<?php
//Adds an LNP to billing
session_start();
include '../inc_header.php';
include('h2osoap.php');


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
		
		$msg = "<B>ADDED TO BILLING</B>";
		$billNotes = "\nAUTO added to H2O. CustomerID {$hsoap->customer_id}";
		$now = date("Y-m-d");;
		$newnotes = "\n***Order status updated to BILLED on {$now}***" . $billNotes ;
		
		$sql = "UPDATE tblOrders SET orderStatus = 'BILLED', billed='{$now}', notes = concat(notes,'{$newnotes}') WHERE orderID = {$_REQUEST['orderID']}";
		mysql_select_db($db);
		$retval = mysql_query( $sql, $conn );
	}
	else
	{
		
		$msg = $hsoap->error_code . ": " . $hsoap->error_description;
	}
}
else
{
	$msg = $hsoap->error_code . ": " . $hsoap->error_description;
}

echo $msg;
?>