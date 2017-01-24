<?php

$strTableName="comment";
$_SESSION["OwnerID"] = $_SESSION["_".$strTableName."_OwnerID"];

$strOriginalTableName="comments";

$gPageSize=20;
$ColumnsCount		= 1;

$gstrOrderBy="";
if(strlen($gstrOrderBy) && strcasecmp(substr($gstrOrderBy,0,8),"order by"))
	$gstrOrderBy="order by ".$gstrOrderBy;
	
$gsqlHead="select `access`,   `ArticleID`,   `comment`,   `CommentID`,   `email`  ";
$gsqlFrom="From `comments`";
$gsqlWhere="";
$gsqlTail="";
// $gstrSQL = "select `access`,   `ArticleID`,   `comment`,   `CommentID`,   `email`  From `comments`";
$gstrSQL = gSQLWhere("");

include("include/comment_settings.php");
include("include/comment_events.php");
?>