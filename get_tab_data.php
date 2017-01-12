<?php include 'inc_header.php';

	/*STATUS GUIDE
			
	PENDING_CSR - ip
	PENDING_LSR - ip
	FOC_PENDING - ip
	FOC_RECEIVED
	PROVISIONING_IN_PROCESS
	NPAC_PENDING
	TNS_ACTIVATED
	CLOSED

	***BAD STATUSES
	REJECTED - issues
	PARTIAL_FAILURE - issues
	CLARIFICATION_REQUESTED - issues
	CANCELED
	PENDING_RESPONSE - issues
	*/			
	function number_of_working_days($from, $to) {
		$workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
		$holidayDays = ['*-12-25', '*-01-01', '2013-12-23']; # variable and fixed holidays

		$from = new DateTime($from);
		$to = new DateTime($to);
		$to->modify('+1 day');
		$interval = new DateInterval('P1D');
		$periods = new DatePeriod($from, $interval, $to);

		$days = 0;
		foreach ($periods as $period) {
			if (!in_array($period->format('N'), $workingDays)) continue;
			if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
			if (in_array($period->format('*-m-d'), $holidayDays)) continue;
			$days++;
		}
		return $days;
	}
	
	$tab = $_REQUEST["tab"];
	$siteNumber = $_REQUEST["siteNumber"];
	$customerID = $_REQUEST["customerID"];
	
	
	if ($siteNumber) {				
		$s = " WHERE (siteNumber='{$siteNumber}' OR did='{$siteNumber}'  OR tempDID='{$siteNumber}' )";
	}
	else {
		if ($customerID) {
			$s = " WHERE customerID='{$customerID}' ";
		}	
	}
	if ($_REQUEST['submit'] == 'reset') {
		$s = "";
	}

	if ($tab == '#htab2') { //PENDING
	?>
	<table class="table table-hover">
			<thead>
				<tr>
			
			<th>Order ID</th>
			<th>Notes</th>
			<th>CIS Ticket</th>
			<th>Type</th>
			<th>Customer</th>
			<th>Site</th>
			<th>Created</th>
			<th>Due</th>
			<th>DID</th>
			<th>Temp DID</th>
			<th>Status</th>
			<th>Actions</th>
			
			
		</tr>
	</thead>
	<tbody>
	
<?php
//mysql_data_seek( $retval, 0 );
if ($s) {
	$s2 = $s . " AND orderStatus = 'PENDING' ";
} else {
	$s2 = $s . " WHERE orderStatus = 'PENDING' ";
}
$sql = "SELECT * FROM vwOrders {$s2} ORDER BY duedate asc LIMIT 40";


mysql_select_db($db);
$retval = mysql_query( $sql, $conn );  

//mysql_data_seek( $retval, 0 );
if(! $retval )
{
  die('Could not get data: ' . mysql_error());
}
if(mysql_num_rows($retval) == 0)
{
	echo "<TR><TD colspan=10>You have no orders!</td></tr>";
}
else
{
	$cnt = 9000;
	while($row = mysql_fetch_array($retval, MYSQL_ASSOC))

	{
		
		if ($row['orderStatus'] == 'PENDING') {
			$cnt++;
			
			$today = date("Y-m-d");						
			$rowclass = "";
			
			
			if ($row['duedate'] < $today) {
				$rowclass = "bgcolor='red'";
			}
	?>
	<tr <?php echo $rowclass ?>>
		
		<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>
		<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>		<td nowrap><?php echo $row['cisTicket'] ;?> 	</td>
		<td nowrap><?php echo $row['orderType'] ;?> 	</td>
		<td nowrap><?php echo $row['customer'] ;?></td>
		<td nowrap><?php echo $row['siteNumber'] ;?></td>
		<td nowrap><?php echo  substr($row['createdate'],0,10) ;?></td>
		<td nowrap><?php echo substr($row['duedate'],0,10) ;?></td>
		<td nowrap><?php echo $row['did'] ;?> </td>
		<td nowrap><?php echo $row['tempDID'] ;?> </td>	
		<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
		
		<td nowrap>
		<?php if ($_SESSION['user'] == 'admin') { ?>
		<?php if ($row['orderType'] !== "LNP" AND ($row['orderStatus'] == "ACTIVATED" OR $row['orderStatus'] == "BILLED")) {?>									
		<a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>&action=approvalemail'  type="button" class="btn btn-group btn-sm btn-success btn-animated">Send Approval Email<i class="fa fa-email"></i></a>					
		<?php } ?>
		<?php if ($row['orderType'] !== "LNP" and $row['didType'] == "PORT REQUESTED" AND (int)$row['lnpCount'] == 0  AND ($row['orderStatus'] == "ACTIVATED" OR $row['orderStatus'] == "BILLED")) {?>
			<a href='neworder-lnp1.php?orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-info btn-animated">Port In<i class="fa fa-phone"></i></a>					
		<?php } }?>
		</td>				
							
	</tr>
	
<?php
		} 
	}
}

?>
	</tbody>
	</table>
	<?PHP
	} //End IN PENDING	
	

	if ($tab == '#htab3') { //IN PROGRESS
	?>
	<table class="table table-hover">
							<thead>
								<tr>
							
							<th>Order ID</th>
							<th>Notes</th>
							<th>CIS Ticket</th>
							<th>Type</th>
							<th>Customer</th>
							<th>Site</th>
							<th>Created</th>
							<th>Due</th>
							<th>DID</th>
							<th>Temp DID</th>
							<th>Status</th>
							<th>LNP Status</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s3 = $s . " AND orderStatus = 'IN PROGRESS' and orderType != 'LNP' ";
				} else {
					$s3 = $s . " WHERE orderStatus = 'IN PROGRESS' and orderType != 'LNP' ";
				}
				$sql = "SELECT * FROM vwOrders {$s3} ORDER BY duedate asc LIMIT 40";
	
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 8000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						if ($row['orderStatus'] == 'IN PROGRESS') {
							$cnt++;
							$today = date("Y-m-d");						
						$rowclass = "";
						if ($row['duedate'] < $today) {
							$rowclass = "bgcolor='red'";
						}
					?>
					<tr <?php echo $rowclass ?>>
						
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>						<td nowrap><?php echo $row['cisTicket'] ;?> 	</td>
						<td nowrap><?php echo $row['orderType'] ;?> 	</td>
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><?php echo $row['siteNumber'] ;?></td>
						<td nowrap><?php echo  substr($row['createdate'],0,10) ;?></td>
						<td nowrap><?php echo substr($row['duedate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
						<td nowrap><?php echo $row['tempDID'] ;?> </td>	
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
						<td nowrap><?php echo $row['lnpstatus'] ;?> </td>	
						<td nowrap>
						
						</td>				
											
					</tr>
					
				<?php
						} 
					}
				}
				
				?>
					</tbody>
					</table>	
	<?PHP
	} //End IN PROGRESS
	
	if ($tab == '#htab4') { //PROVISIONED
	?>
	<table class="table table-hover">
			<thead>
				<tr>
			
			<th>Order ID</th>
			<th>Notes</th>
			<th>CIS Ticket</th>
			<th>Type</th>
			<th>Customer</th>
			<th>Site</th>
			<th>Created</th>
			<th>Due</th>
			<th>DID</th>
			<th>Temp DID</th>
			<th>Status</th>
			<th>LNP Status</th>
			<th>Actions</th>
			
			
		</tr>
	</thead>
	<tbody>
	
<?php
if ($s) {
	$s4 = $s . " AND orderStatus = 'PROVISIONED' and orderType != 'LNP'";
} else {
	$s4 = $s . " WHERE orderStatus = 'PROVISIONED' and orderType != 'LNP'";
}
$sql = "SELECT * FROM vwOrders {$s4} ORDER BY duedate asc LIMIT 40";


mysql_select_db($db);
$retval = mysql_query( $sql, $conn );  

if(mysql_num_rows($retval) == 0)
{
	echo "<TR><TD colspan=10>You have no orders!</td></tr>";
}
else
{
	$cnt = 7000;
	while($row = mysql_fetch_array($retval, MYSQL_ASSOC))

	{
		
		if ($row['orderStatus'] == 'PROVISIONED') {
			$cnt++;
			$today = date("Y-m-d");						
		$rowclass = "";
		if ($row['duedate'] < $today) {
			$rowclass = "bgcolor='red'";
		}
	?>
	<tr <?php echo $rowclass ?>>
		
		<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>
		<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>		<td nowrap><?php echo $row['cisTicket'] ;?> 	</td>
		<td nowrap><?php echo $row['orderType'] ;?> 	</td>
		<td nowrap><?php echo $row['customer'] ;?></td>
		<td nowrap><?php echo $row['siteNumber'] ;?></td>
		<td nowrap><?php echo  substr($row['createdate'],0,10) ;?></td>
		<td nowrap><?php echo substr($row['duedate'],0,10) ;?></td>
		<td nowrap><?php echo $row['did'] ;?> </td>
		<td nowrap><?php echo $row['tempDID'] ;?> </td>	
		<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
		<td nowrap><?php echo $row['lnpstatus'] ;?> </td>	
		<td nowrap>
		
		</td>				
							
	</tr>
	
<?php
		} 
	}
}

?>
	</tbody>
	</table>	
	
	
	<?php
	} //end PROVISIONED
	
	if ($tab == '#htab5') { //ACTIVATED
	?>
		<table class="table table-hover">
				<thead>
					<tr>
				
				<th>Order ID</th>
				<th>Notes</th>
				<th>CIS Ticket</th>
				<th>Type</th>
				<th>Customer</th>
				<th>Site</th>
				<th>Created</th>
				<th>Due</th>
				<th>DID</th>
				<th>Temp DID</th>
				<th>Status</th>
				<th>Activated</th>
				<th>Actions</th>
				
				
			</tr>
		</thead>
		<tbody>
		
	<?php
	if ($s) {
		$s5 = $s . " AND orderStatus = 'ACTIVATED' ";
	} else {
		$s5 = $s . " WHERE orderStatus = 'ACTIVATED' ";
	}
	$sql = "SELECT * FROM vwOrders {$s5} ORDER BY duedate asc LIMIT 40";


   mysql_select_db($db);
   $retval = mysql_query( $sql, $conn );  

	if(mysql_num_rows($retval) == 0)
	{
		echo "<TR><TD colspan=10>You have no orders!</td></tr>";
	}
	else
	{
		$cnt = 6000;
		while($row = mysql_fetch_array($retval, MYSQL_ASSOC))

		{
			
			if ($row['orderStatus'] == 'ACTIVATED') {
				$cnt++;
				
		?>
		<tr>
			
			<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>
			<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
			<td nowrap><?php echo $row['cisTicket'] ;?> 	</td>
			<td nowrap><?php echo $row['orderType'] ;?> 	</td>
			<td nowrap><?php echo $row['customer'] ;?></td>
			<td nowrap><?php echo $row['siteNumber'] ;?></td>
			<td nowrap><?php echo  substr($row['createdate'],0,10) ;?></td>
			<td nowrap><?php echo substr($row['duedate'],0,10) ;?></td>
			<td nowrap><?php echo $row['did'] ;?> </td>
			<td nowrap><?php echo $row['tempDID'] ;?> </td>	
			<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
			<td nowrap><?php echo  substr($row['activateDate'],0,10) ;?></td>
			<td nowrap>
						<?php if ($_SESSION['user'] == 'admin' AND $row['orderType'] !== "LNP" and $row['didType'] == "PORT REQUESTED" AND (int)$row['lnpCount'] == 0  AND ($row['orderStatus'] == "ACTIVATED" OR $row['orderStatus'] == "BILLED")) {?>
				<a href='neworder-lnp1.php?orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-info btn-animated">Port In<i class="fa fa-phone"></i></a>					
			<?php } ?>
			
			<?php 
				if ($customerID == 1) {
					$parentAccountID = 1703;	//SELECT
				}
				if ($customerID == 2) {
					$parentAccountID = 1696;	//WINDSOR
				}
				$companyName = "SV - " . $customer . " " . $siteNumber;	
				
				$billingURL = "orderID=" . $row['orderID'] . "&productID=4577&amount=15&quantity=1&effectiveDate=" . substr($row['activateDate'],0,10) . "&tn=" . $row['did'] ;
				if ($row['orderType'] == "LNP") { ?>
				<div id="billing<?php echo $cnt ?>">
					<button class="btn btn-group btn-sm btn-success btn-animated" onclick="javascript:addToBilling('<?php echo $billingURL ;?>',<?php echo $cnt ;?>)">Add to Billing</button>					
				</div>	
					
				<?php
				}
				
				
				
				?>
			
			</td>				
								
		</tr>
		
	<?php
			} 
		}
	}
	
	?>
		</tbody>
		</table>	


	
	<?php
	} //end ACTIVATED
	
	if ($tab == '#htab6') {	//BILLING
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							<th>Order ID</th>
							<th>Notes</th>
							<th>CIS Ticket</th>
							<th>Type</th>
							<th>Customer</th>
							<th>Site</th>
							<th>Created</th>
							<th>Due</th>
							<th>DID</th>
							<th>Temp DID</th>
							<th>Status</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND orderStatus = 'BILLED' ";
				} else {
					$s6 = $s . " WHERE orderStatus = 'BILLED' ";
				}
				$sql = "SELECT * FROM vwOrders {$s6} ORDER BY duedate asc LIMIT 40";
	
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 5000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						if ($row['orderStatus'] == 'BILLED') {
							$cnt++;
					?>
					<tr>
						
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>						<td nowrap><?php echo $row['cisTicket'] ;?> 	</td>
						<td nowrap><?php echo $row['orderType'] ;?> 	</td>
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><?php echo $row['siteNumber'] ;?></td>
						<td nowrap><?php echo  substr($row['createdate'],0,10) ;?></td>
						<td nowrap><?php echo substr($row['duedate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
						<td nowrap><?php echo $row['tempDID'] ;?> </td>	
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
						<td nowrap>
						<?php if ($_SESSION['user'] == 'admin' AND $row['orderType'] !== "LNP" and $row['didType'] == "PORT REQUESTED" AND (int)$row['lnpCount'] == 0  AND ($row['orderStatus'] == "ACTIVATED" OR $row['orderStatus'] == "BILLED")) {?>
							<a href='neworder-lnp1.php?orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-info btn-animated">Port In<i class="fa fa-phone"></i></a>					
						<?php } ?>
						</td>				
											
					</tr>
				
				<?php
						} 
					}
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab 6 BILLING
	
	if ($tab == '#htab7') {	//LNP
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							
							<th>Notes</th>
							
							
							<th>Customer</th>
							<th>Site</th>
							<th>CIS Ticket</th>
							<th>Created</th>
							<th>FOC Date</th>
							<th>DID</th>
							
							<th>LNP Status</th>
							<th>Ported</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND (orderType = 'LNP' AND orderStatus NOT IN ('CANCELED'))";
				} else {
					$s6 = $s . " WHERE (orderType = 'LNP' AND orderStatus NOT IN ('CANCELED'))";
				}
				$sql = "SELECT * FROM vwOrders {$s6} ORDER BY focdate desc";
	//echo $sql;
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 4000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						
							$cnt++;
					$rowclass = "";
					if ($row['lnpstatus'] == 'REJECTED') {
						$rowclass = "bgcolor='red'";
					}
					if ($row['lnpstatus'] == 'CLARIFICATION_REQUESTED') {
						$rowclass = "bgcolor='orange'";
					}
				?>
				<tr <?php echo $rowclass ?>>
					
						
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
					
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['siteNumber'] ;?></a></td>
						<td nowrap><?php echo $row['cisTicket'] ;?></td>
						<td nowrap><?php echo $row['createdate'] ;?></td>
						<td nowrap><?php echo substr($row['focdate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
							
						
						<td nowrap><?php echo $row['lnpstatus'] ;?> </td>
						<td nowrap><?php echo $row['lnptestdate'] ;?> </td>
						<td nowrap>
						<div id="actions<?php echo $cnt ?>">
								<?php if ((int)$row['flagged'] == 0 ) { ?>
									<button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Flag</button>					
								<?php
								} else { ?>
									<button class="btn btn-group btn-sm btn-danger btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Un-Flag</button>					
								<?php
								}
								?>
								
								
							</div>
						</td>				
											
					</tr>
				
				<?php
						} 
					
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab 7 LNP
		
		if ($tab == '#htab8') {	//CNAM
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							<th>Type</th>
							<th>Customer</th>
							<th>Site</th>
							<th>DID</th>
							<th>DID Type</th>
							<th>Status</th>
							<th>LNP Status</th>
							<th>CNAM</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				
				$sql = "SELECT * FROM vwCNAMopen";

		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have nothing to do!</td></tr>";
				}
				else
				{
					$cnt = 3000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						
							$cnt++;
					
				?>
				<tr >
					
						
						<td nowrap><?php echo $row['orderType'] ;?></td>
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['siteNumber'] ;?></a></td>
						
						<td nowrap><?php echo $row['did'] ;?> </td>
						<td nowrap><?php echo $row['didType'] ;?> </td>
							
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
						<td nowrap><?php echo $row['lnpstatus'] ;?> </td>
						<td nowrap><?php echo $row['cnam'] ;?> </td>
						<td nowrap>
							<div id="cnam<?php echo $cnt ?>">
								<button class="btn btn-group btn-sm btn-info btn-animated" onclick='javascript:updateCnam(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>)'>Complete</button>					
							</div>

						</td>
									
											
					</tr>
				
				<?php
						} 
					
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab 8 CNAM
		
	if ($tab == '#htab9') {	//LNP TESTS TO DO
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							
							<th>Notes</th>
							
							
							<th>Customer</th>
							<th>Site</th>
							<th>CIS Ticket</th>
							<th>FOC Date</th>
							<th>DID</th>
							
							<th>LNP Status</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				$now = date("Y-m-d");
				if ($s) {
					$s6 = $s . " AND  (orderType = 'LNP' AND ( focdate = '{$now}' OR ( lnpstatus IN ('CLOSED','PROVISIONING_IN_PROCESS','TNS_ACTIVATED','NPAC_PENDING','FAILED') ) ) AND lnptestdate IS NULL)";
				} else {
					$s6 = $s . " WHERE (orderType = 'LNP' AND ( focdate = '{$now}' OR ( lnpstatus IN ('CLOSED','PROVISIONING_IN_PROCESS','TNS_ACTIVATED','NPAC_PENDING','FAILED') ) ) AND lnptestdate IS NULL)";
				}
				$sql = "SELECT * FROM vwOrders {$s6} ORDER BY focdate asc";
				//echo $sql;
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 2000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						
							$cnt++;
					$rowclass = "";
					
				?>
				<tr <?php echo $rowclass ?>>
					
						
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
					
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['siteNumber'] ;?></a></td>
						<td nowrap><?php echo $row['cisTicket'] ;?></td>
						<td nowrap><?php echo substr($row['focdate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
							
						
						<td nowrap><?php echo $row['lnpstatus'] ;?> </td>
						<td nowrap>
						<div id="lnptest<?php echo $cnt ?>">
							<?php if ($row['lnpstatus'] == 'CLOSED' Or $row['lnpstatus'] == 'FAILED') { 
							$cisTicket = $row['cisTicket'];
							if (empty($cisTicket)) {
								$cisTicket = 0;
							}
							
							?>
								<button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:updateLNPsuccess(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $cisTicket ;?>)'>TEST OK</button>					
							<?php if ($row['lnpstatus'] !== 'FAILED') { ?>
							<button class="btn btn-group btn-sm btn-danger btn-animated" onclick='javascript:updateLNPfail(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $cisTicket ;?>,<?php echo $row['did'] ;?>,<?php echo $row['siteNumber'] ;?>)'>FAIL</button>
							<?php } }?>
							</div>
						</td>				
											
					</tr>
				
				<?php
						} 
					
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab 9 LNP TESTING
		if ($tab == '#htab10') {	//FLAGGED
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							
							<th>Notes</th>
							
							
							<th>Type</th>
							<th>Customer</th>
							<th>Site</th>
							<th>CIS Ticket</th>
							<th>FOC Date</th>
							<th>DID</th>
							
							<th>LNP Status</th>
							<th>Last Update</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND flagged = 1";
				} else {
					$s6 = $s . " WHERE flagged = 1";
				}
				$sql = "SELECT * FROM vwOrders {$s6} ";
	
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 1000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						
							$cnt++;
					$rowclass = "";
					
				?>
				<tr <?php echo $rowclass ?>>
					
						
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
					<td nowrap><?php echo $row['orderType'] ;?></td>
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['siteNumber'] ;?></a></td>
						<td nowrap><?php echo $row['cisTicket'] ;?></td>
						<td nowrap><?php echo substr($row['focdate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
							
						
						<td nowrap><div id="lnpstatus<?php echo $cnt ?>"><?php echo $row['lnpstatus'] ;?></div></td>
						<td nowrap><?php echo $row['lnpLastStatusUpdate'] ;?> </td>
						<td nowrap>
						<?php if ($_SESSION['user'] == 'admin') { ?> 
						<div id="actions<?php echo $cnt ?>">
								<?php if ((int)$row['flagged'] == 0 ) { ?>
									<button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Flag</button>					
								<?php
								} else { ?>
									<button class="btn btn-group btn-sm btn-danger btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Un-Flag</button>					
								<?php
								}
								?>

								
							</div>
						<?php } ?>
						</td>				
											
					</tr>
				
				<?php
						} 
					
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab 10 FLAGGED
		
	if ($tab == '#htab11') {	//LNP ISSUES - WAITING_FOR_CUSTOMER
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							
							<th>Notes</th>
							
							
							<th>Customer</th>
							<th>Site</th>
							<th>CIS Ticket</th>
							<th>FOC Date</th>
							<th>DID</th>
							<th>Status</th>
							<th>LNP Status</th>
							<th>Update Days</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND (orderType = 'LNP' AND orderStatus != 'CANCELED' AND lnpstatus  IN ('CLARIFICATION_REQUESTED','REJECTED', 'WAITING_FOR_CUSTOMER','PARTIAL_FAILURE','PENDING_RESPONSE'))";
				} else {
					$s6 = $s . " WHERE (orderType = 'LNP' AND orderStatus != 'CANCELED'  AND lnpstatus IN ('CLARIFICATION_REQUESTED','REJECTED', 'WAITING_FOR_CUSTOMER','PARTIAL_FAILURE','PENDING_RESPONSE'))";
				}
				$sql = "SELECT * FROM vwOrders {$s6} ";
	
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 4000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						
							$cnt++;
					$rowclass = "";
					if ($row['lnpstatus'] == 'REJECTED') {
						$rowclass = "bgcolor='red'";
					}
					if ($row['lnpstatus'] == 'CLARIFICATION_REQUESTED') {
						$rowclass = "bgcolor='orange'";
					}
					
					
					$now = date("Y-m-d");
					$daysOld = number_of_working_days($row['lnpLastStatusUpdate'],$now);
				  
				   
				   
				?>
				<tr <?php echo $rowclass ?>>
					
						
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
					
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['siteNumber'] ;?></a></td>
						<td nowrap><?php echo $row['cisTicket'] ;?></td>
						<td nowrap><?php echo substr($row['focdate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
							
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>
						<td nowrap><div id="lnpstatus<?php echo $cnt ?>"><?php echo $row['lnpstatus'] ;?></div></td>
						<td nowrap><?php echo $daysOld; ?></td>
						<td nowrap>
						<div id="actions<?php echo $cnt ?>">
								<?php if ((int)$row['flagged'] == 0 ) { ?>
									<button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Flag</button>					
								<?php
								} else { ?>
									<button class="btn btn-group btn-sm btn-danger btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Un-Flag</button>					
								<?php
								}
								?>
						</td>				
											
					</tr>
				
				<?php
						} 
					
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab 11 LNP ISSUES
		
	if ($tab == '#htab12') {	//LNP PENDING
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							
							<th>Notes</th>
							
							
							<th>Customer</th>
							<th>Site</th>
							<th>CIS Ticket</th>
							<th>FOC Date</th>
							<th>DID</th>
							<th>Status</th>
							<th>LNP Status</th>
							<th>Update Days</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND (orderType = 'LNP' AND orderStatus != 'CANCELED' AND lnpstatus  IN ('PENDING'))";
				} else {
					$s6 = $s . " WHERE (orderType = 'LNP' AND orderStatus != 'CANCELED'  AND lnpstatus IN ('PENDING'))";
				}
				$sql = "SELECT * FROM vwOrders {$s6} ";
	
				
	  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 4000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
					
					$now = date("Y-m-d");
											
					$daysOld = number_of_working_days($row['lnpLastStatusUpdate'],$now);	
						
					
					
					$cnt++;
					$rowclass = "";
					if ($row['lnpstatus'] == 'REJECTED' OR $daysOld > 5) {
						$rowclass = "bgcolor='red'";
					}
					if ($row['lnpstatus'] == 'CLARIFICATION_REQUESTED') {
						$rowclass = "bgcolor='orange'";
					}
					
				   
				?>
				<tr <?php echo $rowclass ?>>
					
						
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
					
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['siteNumber'] ;?></a></td>
						<td nowrap><?php echo $row['cisTicket'] ;?></td>
						<td nowrap><?php echo substr($row['focdate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
							
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>
						<td nowrap><div id="lnpstatus<?php echo $cnt ?>"><?php echo $row['lnpstatus'] ;?></div></td>
						<td nowrap><?php echo $daysOld; ?></td>
						<td nowrap>
						<div id="actions<?php echo $cnt ?>">
								<?php if ((int)$row['flagged'] == 0 ) { ?>
									<button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Flag</button>					
								<?php
								} else { ?>
									<button class="btn btn-group btn-sm btn-danger btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Un-Flag</button>					
								<?php
								}
								?>
						</td>				
											
					</tr>
				
				<?php
						} 
					
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab  LNP PENDING	\

	if ($tab == '#htab13') {	//LNP IN PROCESS
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							
							<th>Notes</th>
							
							
							<th>Customer</th>
							<th>Site</th>
							<th>CIS Ticket</th>
							<th>FOC Date</th>
							<th>DID</th>
							<th>Status</th>
							<th>LNP Status</th>
							<th>Update Days</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND (orderType = 'LNP' AND orderStatus = 'IN PROGRESS' AND lnpstatus  IN ('PENDING_CSR','PENDING_LSR', 'FOC_PENDING','CLOSED','RESUBMITTED') ) order by lnpLastStatusUpdate ";

					} else {
					$s6 = $s . " WHERE  (orderType = 'LNP' AND orderStatus = 'IN PROGRESS' AND lnpstatus  IN ('PENDING_CSR','PENDING_LSR', 'FOC_PENDING','CLOSED','RESUBMITTED') ) order by lnpLastStatusUpdate ";
				}
				$sql = "SELECT * FROM vwOrders {$s6} ";
	
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 4000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
					$now = date("Y-m-d");
					$daysOld = number_of_working_days($row['lnpLastStatusUpdate'],$now);
				   
							$cnt++;
					$rowclass = "";
					if ($row['lnpstatus'] == 'REJECTED' OR $daysOld > 5) {
						$rowclass = "bgcolor='red'";
					}
					if ($row['lnpstatus'] == 'CLARIFICATION_REQUESTED') {
						$rowclass = "bgcolor='orange'";
					}
					
				?>
				<tr <?php echo $rowclass ?>>
					
						
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
					
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['siteNumber'] ;?></a></td>
						<td nowrap><?php echo $row['cisTicket'] ;?></td>
						<td nowrap><?php echo substr($row['focdate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
							
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>
						<td nowrap><div id="lnpstatus<?php echo $cnt ?>"><?php echo $row['lnpstatus'] ;?></div></td>
						<td nowrap><?php echo $daysOld; ?></td>
						<td nowrap>
						<div id="actions<?php echo $cnt ?>">
								<?php if ((int)$row['flagged'] == 0 ) { ?>
									<button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Flag</button>					
								<?php
								} else { ?>
									<button class="btn btn-group btn-sm btn-danger btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Un-Flag</button>					
								<?php
								}
								?>
						</td>				
											
					</tr>
				
				<?php
						} 
					
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab  LNP IN PROCESS		
	if ($tab == '#htab14') {	//LNP FOC
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							
							<th>Notes</th>
							
							
							<th>Customer</th>
							<th>Site</th>
							<th>CIS Ticket</th>
							<th>FOC Date</th>
							<th>DID</th>
							<th>Status</th>
							<th>LNP Status</th>
							
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND (orderType = 'LNP' AND orderStatus != 'CANCELED' AND lnpstatus   IN ('FOC_RECEIVED'))";
				} else {
					$s6 = $s . " WHERE (orderType = 'LNP' AND orderStatus != 'CANCELED'  AND lnpstatus  IN ('FOC_RECEIVED'))";
				}
				$sql = "SELECT * FROM vwOrders {$s6} ";
	
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 4000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						
							$cnt++;
					$rowclass = "";
					if ($row['lnpstatus'] == 'REJECTED') {
						$rowclass = "bgcolor='red'";
					}
					if ($row['lnpstatus'] == 'CLARIFICATION_REQUESTED') {
						$rowclass = "bgcolor='orange'";
					}
				?>
				<tr <?php echo $rowclass ?>>
					
						
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>
					
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['siteNumber'] ;?></a></td>
						<td nowrap><?php echo $row['cisTicket'] ;?></td>
						<td nowrap><?php echo substr($row['focdate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
							
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>
						<td nowrap><div id="lnpstatus<?php echo $cnt ?>"><?php echo $row['lnpstatus'] ;?></div></td>
						
						<td nowrap>
						<div id="actions<?php echo $cnt ?>">
								<?php if ((int)$row['flagged'] == 0 ) { ?>
									<button class="btn btn-group btn-sm btn-success btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Flag</button>					
								<?php
								} else { ?>
									<button class="btn btn-group btn-sm btn-danger btn-animated" onclick='javascript:toggleFlag(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>,<?php echo $row['flagged'] ?>)'>Un-Flag</button>					
								<?php
								}
								?>
						</td>				
											
					</tr>
				
				<?php
						} 
					
				}
				
				?>
					</tbody>
					</table>	
	<?php 
	}  //Tab FOC		
		
	if ($tab == '#htab15') {	//TO PORT
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							<th>Order ID</th>
							<th>Notes</th>
							<th>CIS Ticket</th>
							<th>Type</th>
							<th>Customer</th>
							<th>Site</th>
							<th>Created</th>
							
							<th>DID</th>
							
							<th>Status</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND (orderType != 'LNP' AND didType='PORT REQUESTED' AND lnpCount=0 AND orderStatus   IN ('ACTIVATED', 'BILLED')) ORDER BY siteNumber";
				} else {
					$s6 = $s . " WHERE (orderType != 'LNP' AND didType='PORT REQUESTED' AND lnpCount=0 AND orderStatus   IN ('ACTIVATED', 'BILLED')) ORDER BY siteNumber ";
				}
				$sql = "SELECT * FROM vwOrders {$s6}";
	
		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 5000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						
							$cnt++;
					?>
					<tr>
						
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>						<td nowrap><?php echo $row['cisTicket'] ;?> 	</td>
						<td nowrap><?php echo $row['orderType'] ;?> 	</td>
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><?php echo $row['siteNumber'] ;?></td>
						
						<td nowrap><?php echo substr($row['duedate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
						
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
						<td nowrap>
							<?php if ($_SESSION['user'] == 'admin') { ?> 
							<a href='neworder-lnp1.php?orderID=<?php echo $row['orderID'] ;?>'  type="button" class="btn btn-group btn-sm btn-info btn-animated">Port In<i class="fa fa-phone"></i></a>					
							<?php } ?>
						</td>				
											
					</tr>
				
				<?php
						
					}
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab 15 TO PORT
		
	if ($tab == '#htab16') {	//QOS
		 
	?>	
	
	
				<table class="table table-hover">
							<thead>
								<tr>
							
							<th>Order ID</th>
							<th>Notes</th>
							
							<th>Type</th>
							<th>Customer</th>
							<th>Site</th>
							<th>Activated</th>
							
							<th>DID</th>
							
							<th>Status</th>
							<th>Actions</th>
							
							
						</tr>
					</thead>
					<tbody>
					
				<?php
				if ($s) {
					$s6 = $s . " AND (orderType = 'NEWSITE' AND qosCompleteDate IS NULL AND orderStatus   IN ('ACTIVATED', 'BILLED')) ORDER BY siteNumber";
				} else {
					$s6 = $s . " WHERE (orderType = 'NEWSITE' AND qosCompleteDate IS NULL AND orderStatus   IN ('ACTIVATED', 'BILLED')) ORDER BY siteNumber";
				}
				$sql = "SELECT * FROM vwOrders {$s6}";

		  
			   mysql_select_db($db);
			   $retval = mysql_query( $sql, $conn );  

				if(mysql_num_rows($retval) == 0)
				{
					echo "<TR><TD colspan=10>You have no orders!</td></tr>";
				}
				else
				{
					$cnt = 5000;
					while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
		
					{
						
						
							$cnt++;
					?>
					<tr>
						
						<td nowrap><a href='orderdetails.php?orderID=<?php echo $row['orderID'] ;?>'><?php echo $row['orderID'] ;?></a></td>
						<td nowrap><a class='fa fa-edit' data-toggle='modal' data-target='#notesModal' onclick='javascript:getNotes(<?php echo $row['orderID'] ;?>)' ></a></td>						
						<td nowrap><?php echo $row['orderType'] ;?> 	</td>
						<td nowrap><?php echo $row['customer'] ;?></td>
						<td nowrap><?php echo $row['siteNumber'] ;?></td>
						
						<td nowrap><?php echo substr($row['activateDate'],0,10) ;?></td>
						<td nowrap><?php echo $row['did'] ;?> </td>
						
						<td nowrap><?php echo $row['orderStatus'] ;?> </td>	
						<td nowrap>
							<?php if ($_SESSION['user'] == 'admin') { ?> 
							<div id="qos<?php echo $cnt ?>">
								<button class="btn btn-group btn-sm btn-info btn-animated" onclick='javascript:updateQOS(<?php echo $row['orderID'] ;?>,<?php echo $cnt ?>)'>QoS Verified</button>					
							</div>							<?php } ?>
						</td>				
											
					</tr>
				
				<?php
						
					}
				}
				
				?>
					</tbody>
					</table>	
	<?php 
		}  //Tab 16 QOS


		?>
 