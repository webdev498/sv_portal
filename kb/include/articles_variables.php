<?php

$strTableName="articles";
$_SESSION["OwnerID"] = $_SESSION["_".$strTableName."_OwnerID"];

$strOriginalTableName="articles";

$gPageSize=20;
$ColumnsCount		= 1;

$gstrOrderBy="";
if(strlen($gstrOrderBy) && strcasecmp(substr($gstrOrderBy,0,8),"order by"))
	$gstrOrderBy="order by ".$gstrOrderBy;
	
$gsqlHead="select `approved`,   `ArticleID`,   `Category`,   `DateCreated`,   `Problem`,   `Solution`,   `Title`,   `Views`,   `screenp`,   `screens`,   `filep`,   `files`  ";
$gsqlFrom="From `articles`";
$gsqlWhere="";
$gsqlTail="";
// $gstrSQL = "select `approved`,   `ArticleID`,   `Category`,   `DateCreated`,   `Problem`,   `Solution`,   `Title`,   `Views`,   `screenp`,   `screens`,   `filep`,   `files`  From `articles`";
$gstrSQL = gSQLWhere("");

include("include/articles_settings.php");
include("include/articles_events.php");
?>