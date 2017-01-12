<?php

$strTableName="users";
$_SESSION["OwnerID"] = $_SESSION["_".$strTableName."_OwnerID"];

$strOriginalTableName="categories";

$gPageSize=20;
$ColumnsCount		= 1;

$gstrOrderBy="ORDER BY `categories`.`Category` ASC";
if(strlen($gstrOrderBy) && strcasecmp(substr($gstrOrderBy,0,8),"order by"))
	$gstrOrderBy="order by ".$gstrOrderBy;
	
$gsqlHead="select `CategoryID`,   `categories`.`Category`,  `Title`,  `ArticleID`,  `Views`  ";
$gsqlFrom="From `categories` inner join `articles`  on `categories`.`Category`=`articles`.`Category`  ";
$gsqlWhere="`approved`='1'";
$gsqlTail="";
// $gstrSQL = "select `CategoryID`,   `categories`.`Category`,  `Title`,  `ArticleID`,  `Views`  From `categories` inner join `articles`  on `categories`.`Category`=`articles`.`Category`  where `approved`='1'";
$gstrSQL = gSQLWhere("");

include("include/users_settings.php");
include("include/users_events.php");
?>