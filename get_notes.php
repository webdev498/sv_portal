<?php include 'inc_header.php';

	$orderID = $_REQUEST["orderID"];

	$sql = "SELECT * FROM tblOrders WHERE orderID={$orderID}";
	
	mysql_select_db($db);
  
	$retval = mysql_query( $sql, $conn );  

	if(mysql_num_rows($retval) == 0)
	{
		$notes = "";
	}
	else
	{
		
		$row = mysql_fetch_array($retval, MYSQL_ASSOC);
		$notes = str_replace("\n","<BR>",$row['notes']);
		$updateemail = $row['updateemail'];
	}
	?>
	<form action="orderdetails.php" method=post>
		<input type=hidden name=orderID value="<?php echo $orderID ;?>">
		<input type=hidden name=update value="notes">
			
		<?php echo $notes ?><BR><BR>
		<?php if ($_SESSION['user'] == 'admin') { ?>
		<textarea  class="form-control" id="notes" name="notes" rows=5 ></textarea>
		<input type=checkbox value=true name=email_update id=email_update>Email Internal?
		<input type=checkbox value=true name=email_customer id=email_customer>Email Customer?
		<input type=checkbox value=true name=email_cis id=email_cis checked>Email CIS Ticket?<BR>
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <button type="button" onclick="writeNotes(<?php echo $orderID ;?>)" class="btn btn-success">Update Notes</button>
		
		<BR><BR>LNP STATUS UPDATES:<BR>
		<select name=newlnpstatus id=newlnpstatus>
		<option value="">--Select--</option>
		<option value="RESUBMITTED">RESUBMITTED</option>
		<option value="WAITING_FOR_CUSTOMER">WAITING_FOR_CUSTOMER</option>		
		<option value="FOC_RECEIVED">FOC_RECEIVED</option>
		<option value="CLOSED">CLOSED</option>
		</select>
		<input type=date id=newfoc name=newfoc>
		<button type="button" class="btn btn-group btn-sm btn-info btn-animated" onclick='javascript:updateLNPstatus(<?php echo $orderID ;?>)'>Update LNP Status</button>					
		<?php } ?>
		
	</form>	
	



	
 