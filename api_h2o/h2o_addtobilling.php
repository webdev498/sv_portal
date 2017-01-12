<?php

session_start();

/** Error reporting */
error_reporting(E_ALL);
ini_set("max_execution_time",0);
ini_set('memory_limit',"-1");
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

include('h2osoap.php');

// check for acceptable parents
$parents = array('1700','1696','1703','2307','2505');
if (!in_array($_GET["parentAccountID"],$parents))
	header('location:../orderdetails.php?orderID=' . $_GET['orderID'] . "&status=");
	

// first setup the information in the customer object
$customer = new H2OCustomer();
$customer->SetUpCustomer($_GET["parentAccountID"], 
	$_GET["companyName"],
	$_GET["address"],
	"",
	$_GET["city"],
	$_GET["state"],
	$_GET["zip"]);

$customer->AddLine($_GET["tn"],$_GET["effectiveDate"],"");


$handsets = $_GET["handsets"];
$billProfile = $_GET["billProfile"];
switch($_GET["parentAccountID"])
{
	
	case '1703'	:	//Select Comfort
		$extQty = 2;
		$customer->AddProduct(4632,25,$extQty,"SV Managed Extension");
		$customer->AddProduct(4585,3.00,$extQty,"SV - 911 Recovery Charge");
		$customer->AddProduct(4586,2.00,$extQty,"SV - Regulatory Recovery Fee");
		break;
		
	case '1696'	:	//Windsor
		$extQty = 1;
		switch($billProfile)
		{
			case 'WIN-1-Standard' :	//standard, 1 phone T23G
				$extQty = 1;
				break;
				
			case 'WIN-1-Cordless' :	//standard site, 1 phone with DECT upgrade
				$extQty = 1;
				$customer->AddProduct(4644,6.00,1,"SV Phone Rental - Panasonic TGP600");
				break;
				
		}
		//Same for all profiles
		$customer->AddProduct(4632,27.50,$extQty,"SV Managed Extension");
		$customer->AddProduct(4585,2.00,$extQty,"SV - 911 Recovery Charge");
		$customer->AddProduct(4586,1.50,$extQty,"SV - Regulatory Recovery Fee");
		break;
		
	case '2307'	:	//Logans Roadhouse
		$extQty = 3;
		$customer->AddProduct(4632,27.5,$extQty,"SV Managed Extension");
		$customer->AddProduct(4673,20,$extQty,"SV Cell Backup Phone");
		$customer->AddProduct(4668,5,1,"SV Phone Rental - Kyocera Duramax");
		$customer->AddProduct(4644,6.00,1,"SV Phone Rental - Panasonic TGP600");
		$customer->AddProduct(4629,3.00,2,"SV Phone Rental - Yealink T27P");
		$customer->AddProduct(4629,3.00,4,"SV Asset Protection Plan NBD");
		$customer->AddProduct(4585,2.00,1,"SV - 911 Recovery Charge");
		$customer->AddProduct(4586,1.50,1,"SV - Regulatory Recovery Fee");
		break;	
	case '2505'	:	//NPC (wendys only now)
		switch($billProfile)
		{
			case 'NPC-WENDYS' :	
				$extQty = 1;
				$customer->AddProduct(4632,27.5,$extQty,"SV Managed Extension");
				$customer->AddProduct(4686,16.00,1,"SV Phone Rental - Panasonic KX-UDT131 Rugged Handset");
				break;
				
			case 'WIN-PIZZAHUT' :	
				$extQty = 1;
				
				break;
				
		}
				
		$customer->AddProduct(4585,2.00,1,"SV - 911 Recovery Charge");
		$customer->AddProduct(4586,1.50,1,"SV - Regulatory Recovery Fee");
		break;		
		
}
// then call the h2osoap object to execute
$hsoap = new H2OSoap(USER_NAME,PASSWORD,CLIENT_CODE);
$ret_str = "../orderdetails.php?orderID=" . $_GET['orderID'] . "&status=";
if($hsoap->AddCustomer($customer))
{
	// manually set the order to complete
	if (!$hsoap->UpdateOrder($customer,"COMPLETE",$_GET["effectiveDate"]))
	{
		$ret_str .= "0&command=SetTN-UpdateOrder";
		$_SESSION["ErrorCode"] 	  = $hsoap->error_code;
		$_SESSION["ErrorMessage"] = $hsoap->error_description;
	}
	else
	{
	
		//Add the setup fees		switch($_GET["parentAccountID"])		{						case '1703'	:	//Select Comfort				$hsoap->Transaction($customer->customer_id,4571,25,$extQty,$_GET["effectiveDate"],$_GET["effectiveDate"]);				
				//Bill for phones
				if ($handsets == 'New Handsets - Ship to CIS' OR $handsets == 'New Handsets - Drop Ship to Customer') {
					$hsoap->Transaction($customer->customer_id,4657,155.72,1,$_GET["effectiveDate"],$_GET["effectiveDate"]);	//Yealink T29G
					$hsoap->Transaction($customer->customer_id,4658,169.15,1,$_GET["effectiveDate"],$_GET["effectiveDate"]);	//Panasonic TGP600
					$hsoap->Transaction($customer->customer_id,4659,92.65,1,$_GET["effectiveDate"],$_GET["effectiveDate"]);	//Panasonic TPA60
					$hsoap->Transaction($customer->customer_id,4639,25,1,$_GET["effectiveDate"],$_GET["effectiveDate"]);	//Shipping & Handling
				}				break;			case '1696'	:	//Windsor				$hsoap->Transaction($customer->customer_id,4571,27.50,$extQty,$_GET["effectiveDate"],$_GET["effectiveDate"]);				break;			
			case '2307'	:	//Logans Roadhouse
				$hsoap->Transaction($customer->customer_id,4571,27.50,$extQty,$_GET["effectiveDate"],$_GET["effectiveDate"]);	//setup
				$hsoap->Transaction($customer->customer_id,4581,25,1,$_GET["effectiveDate"],$_GET["effectiveDate"]);	//AA setup
				$hsoap->Transaction($customer->customer_id,4674,35,1,$_GET["effectiveDate"],$_GET["effectiveDate"]);	//Cell setup

				break;
			case '2505'	:	//NPC
				$hsoap->Transaction($customer->customer_id,4571,27.50,$extQty,$_GET["effectiveDate"],$_GET["effectiveDate"]);

				break;	
		}
				$ret_str .= "1&action=billed&customerid=".$customer->customer_id."&orderid=".$customer->order_id."&lindeid=".$customer->line_id;
	}
}
else
{
	$ret_str .= "0&command=AddCustomer";
	$_SESSION["ErrorCode"] = $hsoap->error_code;
	$_SESSION["ErrorMessage"] = $hsoap->error_description;
}

fwrite($hsoap->log,"\r\n\r\n ret_str: " . $ret_str);
header('location:' . $ret_str);
?>