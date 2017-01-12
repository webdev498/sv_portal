<?php
/*
	Test file to execute the h2o soap interface
*/

include('h2osoap.php');

// first setup the information in the customer object
$customer = new H2OCustomer();
$customer->SetUpCustomer(1700, "test6", "257 Cline Ave", "", "Mansfield", "OH", "44907");
$customer->AddLine("3125551212","2016-07-15","description");
$customer->AddProduct(4632,27.50,2,"SV Managed Extension");
$customer->AddProduct(4585,3.00,2,"SV - 911 Recovery Charge");
$customer->AddProduct(4586,2.00,2,"SV - Regulatory Recovery Fee");

// then call the h2osoap object to execute
$hsoap = new H2OSoap("jrobbins","g3ck0!","cbv");

if($hsoap->AddCustomer($customer))
{
	echo("<br/>Customer Added");
	echo("<br/>  CID: " . $customer->customer_id . "<br/>Line Id: " . $customer->line_id . "<br/>Order ID: " . $customer->order_id);

	// manually set the order to complete
	if (!$hsoap->UpdateOrder($customer,"COMPLETE"))
	{
		echo("<br/>Order Completion Failed: <br/>Error Code: " . $hsoap->error_code .  "<br/>Message: " . $hsoap->error_description);
	}
	else
	{
		echo("<br/>Order Completed");
		// test a transaction if all else is successful
		if($hsoap->Transaction($customer->customer_id,4571,27.50,2,'2016-07-18','2016-07-18'))
		{
			echo("<br/>Transaction Completed");
		}
		else
		{
			echo("<br/>Transaction Failed: <br/>Error Code: " . $hsoap->error_code .  "<br/>Message: " . $hsoap->error_description);
		}
	}
}
else
{
	echo("<br/>Add Failed");
	echo("<br>Err CD: " . $hsoap->error_code . "<br/>Desc: " . $hsoap->error_description);
}
?>