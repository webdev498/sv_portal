<?php
include "inc_db.php";
session_start();
//echo "LOGGED: {$_SESSION['loggedin']} created {$_SESSION['LAST_ACTIVITY']} type: {$_SESSION['user']}";

date_default_timezone_set('America/Chicago');

//if (session_status() !== PHP_SESSION_ACTIVE) 
if ($_SESSION['loggedin'] !== 'YES')
{
	$url = "index.php?msg=Please log in to access the SimpleVoIP Orders System.";
	Header("Location: $url");
	exit();
}





?>
