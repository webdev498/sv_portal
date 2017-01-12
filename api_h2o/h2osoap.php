<?phpdate_default_timezone_set('America/Chicago');
/*

	This file contains the h2osoap and h2o customer objects.
	
	The h2ocustomer object allows for the parameters from mutliple calls to be
		consolidated into a single AddCustomer call to h2osoap
	The h2osoap object handles all of the actaul soap interactions with H2O.
	
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

define("DEBUG_LEVEL",4);
define("USER_NAME","jrobbins");
define("PASSWORD","g3ck0!");
define("CLIENT_CODE","cbv");

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

	H2OCustomer object
		SetUpCustomer - add basic elements to customer object
		AddLine - add a line to the NewCustomer_Request
		AddProduct - add a product to the NewCustomer_Request
		AddTransaction - update the values in the Billing portion of the NewCustomer_Request
	
	After the customer object is filled in it is passed to the H2OSoap->AddCustomer method
		
*/
class H2OCustomer
{
	public $customer;		// primary request
	public $address;		// reused address block part of request
	public $billing;		// billing block of request
	public $order;			// the order block of request - contains products
	public $products;		// products as part of request
	
	public $telenum;			//
	
	public $lines;			// holds line ids returned from NewCustomer - used for LinesUpdate
	public $customer_id;	// holds the customer id returned from create
	public $status;			// status - if unsuccessful: 'Failed' else status returned in create (All Prospect Pending Closed Open)
	
	function __constructor()
	{
		$this->completed = array(0,0,0,0);
		$this->address 	 = array();
		$this->address2	 = array();
		$this->billing 	 = array();
		$this->products  = array();
	}

	function SetUpCustomer($parentaccountID, $companyname, $address1, $address2, $city, $state, $zip)
	{
		$this->customer["CustomerClass"]	 = "Business";
		$this->customer["ParentCustomerID"]  = $parentaccountID;
		
		$this->address["CompanyName"] = $companyname;
		$this->address["Address1"] 	  = $address1;
		$this->address["Address2"] 	  = $address2;
		$this->address["City"] 		  = $city;
		$this->address["State"] 	  = $state;
		$this->address["Zip"] 		  = $zip;
		$this->address["Country"] 	  = "USA";
		
		$this->address2["CompanyName"] 	= $companyname;
		$this->address2["Address1"]	   	= $address1;
		$this->address2["Address2"] 	= $address2;
		$this->address2["City"]			= $city;
		$this->address2["State"] 	  	= $state;
		$this->address2["Zip"] 		  	= $zip;
		$this->address2["Country"] 	  	= "USA";
		
		$this->billing["Balance"] 				= 0.0;
		$this->billing["Due"] 					= 0.0;
		$this->billing["OverDue"] 				= 0.0;
		$this->billing["UnbilledUsage"] 		= 0.0;
		$this->billing["UsageBalanceLimit"] 	= 0.0;
		$this->billing["EnableBalanceLimit"] 	= 0;
		$this->billing["PaymentTermsOverride"] 	= 0;
		$this->billing["PaymentTerms"] 			= 0;
		
		$this->customer["PhysicalAddress"] 	 = $this->address;
		$this->customer["MailingAddress"] 	 = $this->address2;
		$this->customer["Billing"]		 	 = $this->billing;
		
		$this->order["ShippingAddress"] 	= $this->address;
		$this->order["MailingAddress"] 		= $this->address2;
	}
	
	function AddLine($tn,$edate,$description)
	{
		$this->order["Description"] 	 = $description;
		$this->customer["ContractStart"] = $edate;
		$this->telenum					 = $tn;
		$this->order["TN"] 	 			 = $tn;
		$this->order["PackageID"] 		 = 1006;
	}
		
	function AddProduct($prod_id,$rate,$quantity,$description)
	{
		$newproduct = array();
		$product = array();
		$product["Product"]  = $product;
		$product["ProductID"] 	= $prod_id;
		$product["Rate"] 		= $rate;
		$product["Quantity"] 	= $quantity;
		$product["Description"] = $description;
		
		$this->products[] = $product;
	}
	
	function AddTransaction($amount)
	{
		$this->billing["Balance"] = $amount;
	}
	
}

/*
	H2OSoap object handles all soap interactions
*/
class H2OSoap
{

	protected $login_parms;	// array holding login prams below
	protected $username;
	protected $password;
	protected $clientid;
	
	protected $soap;		// the soap object
	protected $session;		// the session object if used
	protected $session_key;	// session key if sessions are used
	protected $request;		// the current request
	
	public $log;			// the log file if used
	
	public	$error_code;	// error code and message of last request if returned
	public	$error_description;
	
	function __construct($username,$password,$clientid)
	{
		$this->username = $username;
		$this->password = $password;
		$this->clientid = $clientid;
		$this->session = 0;
		$this->session_key = 0;
		
		// set debuggin based on DEBUG_LEVEL constant
		if (DEBUG_LEVEL > 0)
		{
			$mydate = new DateTime();
			$this->log = fopen("soap_logs/" . $mydate->format("sih") . ".log","w");
		}
		
		$this->login_parms = array("Credentials" => array("Username" => $this->username,
														  "Password" => $this->password,
														  "Client" 	 => $this->clientid));
							
		// create soap client object
		$this->soap = new SoapClient('wsdl.xml',array("soap_version" => SOAP_1_2,
					"trace" => 1,
					"exceptions" => 0,
					"location" => "http://api.myh2o.com/v20/default.asmx"));	//Josh changed to remove HTTPS on 10-24-2016
	}
	
	//	If there will be multiple calls creating a session eliminates the
	//	need to pass and process login parameters with each call.
	function CreateSession()
	{
		$this->session = $soap->Sessions_Create(array("Request" =>$this->login_parms));
		
		$respHdr = $this->session->Sessions_CreateResult->Header;
		$ret = true;
		if ($respHdr->Success == true)
		{
			$this->session_key = $$respHdr->SessionKey;
		}
		else
		{
			$ret = false;
			$this->error_code = $$respHdr->Error_Code;
			$this->error_description = $$respHdr->Error_Description;
		}
		
		return $ret;
	}

	// sets up the credentials based on whether sessions are being used
	function Credentials()
	{
		$this->request = array();		// clear request from any previous information
		if ($this->session_key == 0)
			$this->request["Request"] = $this->login_parms;
		else
			$this->request["Request"] = array("SessionKey"=>$this->session_key);
			
		return;
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
	
	// update order is called to manually set the new customer to COMPLETE
	function UpdateOrder(H2OCustomer $customer, $status, $edate)
	{
		$this->Credentials();		// updates request array with "Request" Element containing credentials
		
		$this->request["Request"]["OrderID"] = $customer->order_id;
		$this->request["Request"]["Status"] = $status;				$this->request["Request"]["CompletionDate"] = $edate;	//added by Josh 7-20-2016
		
		$resp = $this->soap->Orders_Update($this->request);
		
		$this->SoapDebug($resp);
		
		$respHdr = $resp->Orders_UpdateResult->Header;
		$ret = false;
		if ($respHdr->Success == '1')
		{
			$ret = true;
		}
		else
		{
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
	
	// AddTN is called to set the TN since it is not processed as part of the 
	//		NewCustomer_Request
	//		Called from AddCustomer
	function AddTN(H2OCustomer $customer)
	{
		// clear the request array
		$this->Credentials();		// updates request array with "Request" Element containing credentials

		$this->request["Request"]["Line_ID"] = $customer->line_id;
		
		$number = array();
		$number["Updated"] = 1;
		$number["Value"] = $customer->order["TN"];
		
		$this->request["Request"]["Number"] = $number;
		
		$resp = $this->soap->Lines_Update($this->request);
		
		$this->SoapDebug($resp);
		
		$respHdr = $resp->Lines_UpdateResult->Header;
		
		$ret = true;
		if ($respHdr->Success != "1")
			$ret = false;
			
		return $ret;
	}
	
	// Main function
	//	Makes the NewCustomer_Request soap call
	//		calls AddTN to set the TN
	//		Saves customer_id, line_id, order_id in the h2ocustomer Object
	function AddCustomer(H2OCustomer $customer)
	{
		// make sure the array is filled properly
		$customer->order["AdditionalProducts"] 	= $customer->products;

		$this->Credentials();		// updates request array with "Request" Element containing credentials
		$this->request["Request"]["Customer"] = $customer->customer;
		$this->request["Request"]["Order"] = $customer->order;
		
		$resp = $this->soap->NewCustomers_Create($this->request);
		$this->SoapDebug($resp);
		
		$respHdr = $resp->NewCustomers_CreateResult->Header;
		$ret = false;
		if ($respHdr->Success == '1')
		{
			$ret = true;
			$customer->customer_id = $resp->NewCustomers_CreateResult->Customer_ID;
			$customer->line_id = $resp->NewCustomers_CreateResult->Lines->Line;
			$customer->order_id = $resp->NewCustomers_CreateResult->Orders->Order;
			$customer->status = $resp->NewCustomers_CreateResult->Status;
			
			// add the tn separately
			
			$ret = $this->AddTn($customer);
		}
		else
		{
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
	
	// Create a transaction for an account.
	function Transaction($customerId,$productId,$amount,$quantity,$dateStart,$dateEnd)
	{
		$this->Credentials();		// updates request array with "Request" Element containing credentials
		$transaction = array();
		$transaction["CustomerID"]	= $customerId;
		$transaction["ProductID"]	= $productId;
		$transaction["Type"]		= "CHARGE";
		$transaction["Amount"]		= $amount;
		$transaction["Quantity"]	= $quantity;
		$transaction["DateStart"]	= $dateStart;
		$transaction["DateEnd"]		= $dateEnd;
	
		$this->request["Request"]["Transaction"] = $transaction;
		
		$resp = $this->soap->Transactions_Create($this->request);
		$this->SoapDebug($resp);
		
		$respHdr = $resp->Transactions_CreateResult->Header;
		
		$ret = false;
		if ($respHdr->Success == '1')
			$ret = true;
		else
		{
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
	
	function CustomerLookup($tn)
	{
		$this->Credentials();		// updates request array with "Request" Element containing credentials
		$this->request["Request"]["TN"] = $tn;
		
		$resp = $this->soap->Customers_Query($this->request);
		$this->SoapDebug($resp);

		$respHdr = $resp->Customers_QueryResult->Header;
		
		$ret = false;
		if ($respHdr->Success == '1')
		{
			$ret = true;
			$this->customer_id = $resp->Customers_QueryResult->Customer->ID;
		}
		else
		{
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