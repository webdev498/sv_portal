<?php

$strTableName="main";
$_SESSION["OwnerID"] = $_SESSION["_".$strTableName."_OwnerID"];

$strOriginalTableName="categories";

$gPageSize=20;
$ColumnsCount		= 1;

$gstrOrderBy="ORDER BY `Category` ASC";
if(strlen($gstrOrderBy) && strcasecmp(substr($gstrOrderBy,0,8),"order by"))
	$gstrOrderBy="order by ".$gstrOrderBy;
	
$gsqlHead="select `CategoryID`,  `approved`,   `categories`.`Category`,  `Title`,  `ArticleID`,  `Views`  ";
$gsqlFrom="From `categories` inner join `articles`  on `categories`.`Category`=`articles`.`Category`";
$gsqlWhere="";
$gsqlTail="";
// $gstrSQL = "select `CategoryID`,  `approved`,   `categories`.`Category`,  `Title`,  `ArticleID`,  `Views`  From `categories` inner join `articles`  on `categories`.`Category`=`articles`.`Category`";
$gstrSQL = gSQLWhere("");

include("include/main_settings.php");
include("include/main_events.php");
?>