<?php
   

   
    //enable logging
    $logging = true;
  
   
    if ($logging) {
 
        $req_dump = print_r($_REQUEST, TRUE);
     
        $fp = fopen('select_xfer_cordless.log', 'a');
        fwrite($fp, $req_dump);
       
    }
 
    // set destination number to variable  
    $to = $_REQUEST['To'];
	$from = $_REQUEST['Caller-ID-Number'];
	$userID = $_REQUEST['User-ID'];
   
	
	
   
    if ($logging) {
        //fwrite($fp, '$dest_number = ' . $dest_number."\r\n");
        //fwrite($fp, '$from = ' . $from."\r\n");
        //fclose($fp);
    }
   
    //create response
    header('Content-Type: application/json');

if ($userID) {
?>

{"module":"user"
 ,"data":{
   "id":"<?php echo $userID ?>"
   ,"can_call_self":true
   ,"timeout":20
   ,"delay":0
   ,"strategy":"simultaneous"
 }
}
<?php } ?>