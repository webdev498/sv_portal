<?php
/*

	
	The DEBUG_LEVEL constant controls the amount of information dumped to the soap_logs. The
	settings are cumulative: i.e. each higher number also logs the elements from the lower
	numbers.
	
		0 - No logging
		1 - Log Request Object
		2 - Log Request XML
		3 - Log Response Object
		4 - Log Response XML

*/

/** Error reporting */
error_reporting(E_ALL);
ini_set("max_execution_time",0);
ini_set('memory_limit',"-1");
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

date_default_timezone_set("America/Chicago");

define("DEBUG_LEVEL",4);

define("USER_NAME","SimpleVoIP");
define("PASSWORD","D52z3qeK2");
define("CUSTOMER","jrobs@simplevoip.us");

// make print_r usable on a web page
function fixpr($istr)
{
	return "<pre>" .  print_r($istr,1) ."</pre>";
}

// a simple function to make xml readable on web page
function dumpxml($istr)
{
	$begidx = 0;
	$ostr = 0;
	
	$accum = 0;
	while(1)
	{
		$idx = strpos($istr,"<",$begidx);
		if ($idx === FALSE)
			break;
			
		$ostr .= substr($istr,$begidx,($idx - $begidx)) . "\r\n" . str_repeat("\t",$accum) . "<";
		$begidx = $idx + 1;
		if ($istr[$idx + 1] == "/")
			$accum--;
		else
			$accum++;
	}
	
	$ostr .= substr($istr,$begidx);
	
	return "<xmp>" . $ostr . "</xmp>";
}

/*
	H2OSoap object handles all soap interactions
*/
class PeerSoap
{

	protected $username;
	protected $password;
	protected $customer;
	
	protected $soap;		// the soap object
	protected $request;		// the current request
	
	public $log;			// the log file if used
	
	public	$error_code;	// error code and message of last request if returned
	public	$error_description;
	
	function __construct($username,$password,$customer)
	{
		$this->username = $username;
		$this->password = $password;
		$this->customer = $customer;
		
		// set debuggin based on DEBUG_LEVEL constant
		if (DEBUG_LEVEL > 0)
		{
			$this->log = fopen("soap_logs/peer_" . date("sih") . ".log","w");
		}
		
		$this->request["Authentication"] = array("userId" 	 => $this->username,												 "passCode"  => $this->password,												 "customer"  => $this->customer);
		// create soap client object
		$this->soap = new SoapClient('APIService.wsdl',array("soap_version" => SOAP_1_2,					"trace" => 1,					"exceptions" => 0,					"Authentication"=> $this->request["Authentication"]));
	}
	
	// writes to the soap_logs folder based on DEBUG_LEVEL constant
	function SoapDebug($resp)
	{
		switch(DEBUG_LEVEL)
		{
			case 0	:
				return;
				break;
			case 4	:
				fwrite($this->log,"Last Response XML: " . dumpxml($this->soap->__getlastResponse()));
			case 3	:
				fwrite($this->log,"Last Response: " . fixpr($resp));
			case 2	:
				fwrite($this->log,"Last Resquest XML: " . dumpxml($this->soap->__getlastRequest()));
			case 1	:
				fwrite($this->log,"Last Resquest: " . fixpr($this->request));
			break;
				
		}
	}
	
	// Main function
	function PlaceOrder($orderID,$pon,$orderType,$tn,$routeLabel,$mou,$accountNumber,$atn,$authDate,$authnm,
						$desiredDueDate,$earliestPossible,$endCustomerName,$mi,$npdi,$sanoHouseNumber,
						$sasnStrName,$sasdStrDir,$city,$state,$telephoneNumber,$zipCode)
	{
		$orderNumbers = array();
		
		$numbers = array();
		$number["tn"] 			= $tn;
		$number["routeLabel"] 	= $routeLabel;
		$number["mou"] 			= $mou;
		$number["sms"]			= 0;
		$number["e911"] 		= 0;
		
		$orderDetails = array();
		$orderDetails["accountNumber"]	  = $accountNumber;
		$orderDetails["atn"] 			  = $atn;
		$orderDetails["authDate"] 		  = $authDate;
		$orderDetails["authnm"] 		  = $authnm;
		$orderDetails["desiredDueDate"]   = $desiredDueDate;
		$orderDetails["earliestPossible"] = $earliestPossible;
		$orderDetails["endCustomerName"]  = $endCustomerName;
		$orderDetails["mi"] 			  = $mi;
		$orderDetails["npdi"] 			  = $npdi;
		$orderDetails["sanoHouseNumber"]  = $sanoHouseNumber;
		$orderDetails["sasdStrDir"] 	  = $sasdStrDir;
		$orderDetails["sasnStrName"] 	  = $sasnStrName;
		$orderDetails["city"] 			  = $city;
		$orderDetails["state"] 			  = $state;
		$orderDetails["telephoneNumbe"]   = $telephoneNumber;
		$orderDetails["zipCode"] 		  = $zipCode;
		
		$orderNumbers["number"] = $number;
		
		$order["pon"] 		   = $pon;
		$order["orderType"]    = $orderType;
		$order["orderNumbers"] = $orderNumbers;
		$order["orderDetails"] = $orderDetails;
		
		$this->request["order"] = $order;
		
		$resp = $this->soap->placeOrder($this->request);
		$this->SoapDebug($resp);
		
		$respHdr = $resp->placeOrderResult->Header;
		if ($respHdr->Success == '1')
		{
			$ret = true;
		}
		else
		{
			$ret = false;
			$customer->status = 'Failed';
			if (isset($respHdr->Error_Code))
			{
				$this->error_code = $respHdr->Error_Code;
				$this->error_description = $respHdr->Error_Description;
			}
			else
			{
				$this->error_code = -1;
				$this->error_description = $respHdr->Message;
			}
		}
			
		
		return $ret;
	}
}	
?>