<?php
//This include file contains all the email functions

class database_helper {

    private $db_host = "107.180.12.37";
    private $db_user = "simplevoip";
    private $db_pass = "1Bigpimp!";
    private $db_database = "simplevoip";
    private $mysqli;
	

    function __construct() {
        $this->mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_database);
        if ($this->mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
        }
        //echo $this->mysqli->host_info . "\n";
        set_time_limit (360);
    }

	public function get_orders(){
		
		
	}
	public function email_directory_listing($orderID) {
				
		$sql = "select * from vwOrders where orderID = {$orderID}";
		$result = $this->mysqli->query($sql);
		
		
		$did = $result->fetch_assoc()['did'];
		$directory = $result->fetch_assoc()['directory'];
		$address = $result->fetch_assoc()['address'];
		$city = $result->fetch_assoc()['city'];
		$state = $result->fetch_assoc()['state'];
		$zip = $result->fetch_assoc()['zip'];
		

		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$msg = "Peerless: Please enter the following directory listing:\n\nTN: {$did}\nListing Name: {$directory}\nAddress: {$address}, {$city}, {$state} {$zip}\n\nThank you,\nSimpleVoIP";
		$msg = wordwrap($msg,70);
		$subject = "New Directory Listing for {$did}";
		$headers = 'From: jrobs@simplevoip.us' . "\r\n" .
		'Reply-To: jrobs@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$mailto = "orderentry@peerlessnetwork.com";
		$mailto = "jrobs@gecko2.com";	//testing
		$mail1 = mail($mailto, $subject, $msg, $headers);
		
		return true;
	}

	public function email_order_udpate($orderID, $bUpdate, $bCustomer, $bCIS) {

		$sql = "select * from vwOrders where orderID = {$orderID}";
		$result = $this->mysqli->query($sql);
		
		$orderID = $result->fetch_assoc()['orderID'];
		$updateemail = $result->fetch_assoc()['updateemail'];
		$customerEmail = $result->fetch_assoc()['customerEmail'];
		$customer = $result->fetch_assoc()['customer'];	
		$siteNumber = $result->fetch_assoc()['siteNumber'];
		$notes = $result->fetch_assoc()['notes'];
		$did = $result->fetch_assoc()['did'];
		$tempDID = $result->fetch_assoc()['tempDID'];
		$orderStatus = $result->fetch_assoc()['orderStatus'];
		$orderType = $result->fetch_assoc()['orderType'];
		$cisTicket = $result->fetch_assoc()['cisTicket'];
		
		$subject = "SV Order Update for {$customer} site {$siteNumber}";
		$subjectCIS = "SV Order Update for {$customer} site {$siteNumber} - CIS Ticket [#{$cisTicket}]";
		$msg = "***SimpleVoIP Order Update***\n\nOrder: {$orderID}\nType: {$orderType}\nCustomer: {$customer}\nStatus: {$orderStatus}\nSite: {$siteNumber}\nMain Number: {$did}\nTemp DID: {$tempDID}\nNotes: {$notes}\n\n\nhttp://orders.simplevoip.us/orderdetails.php?orderID={$orderID}\n";
		$msg = wordwrap($msg,70);
		

		$headers = 'From: noreply@simplevoip.us' . "\r\n" .
		'Reply-To: noreply@simplevoip.us' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		$customerEmail = "josh.robbins@cisvpn.com";
		if ($bCustomer) {
			mail($customerEmail, $subject, $msg, $headers);
		}
		$updateemail = "jrobs@gecko2.com";
		if ($bUpdate) {
			mail($updateemail, $subject, $msg, $headers);
		}
		$cisEmail = "jrobs@gecko2.com";
		if ($cisTicket) {
			$cisEmail = "helpdesk@cisvpn.com";
			mail($cisEmail, $subjectCIS, $msg, $headers);
		}
		
		
	}
}
















?>