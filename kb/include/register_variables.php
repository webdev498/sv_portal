<?php

$strTableName="register";
$_SESSION["OwnerID"] = $_SESSION["_".$strTableName."_OwnerID"];

$strOriginalTableName="register";

$gPageSize=20;
$ColumnsCount		= 1;

$gstrOrderBy="";
if(strlen($gstrOrderBy) && strcasecmp(substr($gstrOrderBy,0,8),"order by"))
	$gstrOrderBy="order by ".$gstrOrderBy;
	
$gsqlHead="select `email`,   `fullname`,   `password`,   `username`  ";
$gsqlFrom="From `register`";
$gsqlWhere="";
$gsqlTail="";
// $gstrSQL = "select `email`,   `fullname`,   `password`,   `username`  From `register`";
$gstrSQL = gSQLWhere("");

include("include/register_settings.php");
include("include/register_events.php");
?>