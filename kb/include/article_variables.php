<?php

$strTableName="article";
$_SESSION["OwnerID"] = $_SESSION["_".$strTableName."_OwnerID"];

$strOriginalTableName="articles";

$gPageSize=20;
$ColumnsCount		= 1;

$gstrOrderBy="";
if(strlen($gstrOrderBy) && strcasecmp(substr($gstrOrderBy,0,8),"order by"))
	$gstrOrderBy="order by ".$gstrOrderBy;
	
$gsqlHead="select `approved`,   `ArticleID`,  `Category`,  `DateCreated`,  `Problem`,  `Solution`,  `Title`,  `Views`,  `screenp`,  `screens`,  `filep`,  `files`  ";
$gsqlFrom="From `articles`  ";
$gsqlWhere="`approved`='1'";
$gsqlTail="";
// $gstrSQL = "select `approved`,   `ArticleID`,  `Category`,  `DateCreated`,  `Problem`,  `Solution`,  `Title`,  `Views`,  `screenp`,  `screens`,  `filep`,  `files`  From `articles`  where `approved`='1'";
$gstrSQL = gSQLWhere("");

include("include/article_settings.php");
include("include/article_events.php");
?>