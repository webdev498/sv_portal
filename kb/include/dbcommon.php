<?php

session_cache_limiter("none");
session_start();

error_reporting(E_ALL ^ E_NOTICE);

$host="sv-mysql.cilqdskq1dv5.us-east-1.rds.amazonaws.com";
$user="openkbuser";
$pwd="1wr56uter";
$port="";
$sys_dbname="openkb_public";


$cCharset = "Windows-1252";

header("Content-type: text/html; charset=".$cCharset);

$dDebug=false;
$dSQL="";

$tables_data=array();

include("include/locale.php");
include("include/events.php");
include("include/commonfunctions.php");
include("include/dbconnection.php");


define("FORMAT_NONE","");
define("FORMAT_DATE_SHORT","Short Date");
define("FORMAT_DATE_LONG","Long Date");
define("FORMAT_DATE_TIME","Datetime");
define("FORMAT_TIME","Time");
define("FORMAT_CURRENCY","Currency");
define("FORMAT_PERCENT","Percent");
define("FORMAT_HYPERLINK","Hyperlink");
define("FORMAT_EMAILHYPERLINK","Email Hyperlink");
define("FORMAT_FILE_IMAGE","File-based Image");
define("FORMAT_DATABASE_IMAGE","Database Image");
define("FORMAT_DATABASE_FILE","Database File");
define("FORMAT_FILE","Document Download");
define("FORMAT_LOOKUP_WIZARD","Lookup wizard");
define("FORMAT_PHONE_NUMBER","Phone Number");
define("FORMAT_NUMBER","Number");
define("FORMAT_HTML","HTML");
define("FORMAT_CHECKBOX","Checkbox");
define("FORMAT_CUSTOM","Custom");

define("EDIT_FORMAT_NONE","");
define("EDIT_FORMAT_TEXT_FIELD","Text field");
define("EDIT_FORMAT_TEXT_AREA","Text area");
define("EDIT_FORMAT_PASSWORD","Password");
define("EDIT_FORMAT_DATE","Date");
define("EDIT_FORMAT_TIME","Time");
define("EDIT_FORMAT_RADIO","Radio button");
define("EDIT_FORMAT_CHECKBOX","Checkbox");
define("EDIT_FORMAT_DATABASE_IMAGE","Database image");
define("EDIT_FORMAT_DATABASE_FILE","Database file");
define("EDIT_FORMAT_FILE","Document upload");
define("EDIT_FORMAT_LOOKUP_WIZARD","Lookup wizard");
define("EDIT_FORMAT_HIDDEN","Hidden field");
define("EDIT_FORMAT_READONLY","Readonly");

define("EDIT_DATE_SIMPLE",0);
define("EDIT_DATE_SIMPLE_DP",11);
define("EDIT_DATE_DD",12);
define("EDIT_DATE_DD_DP",13);

define("MODE_ADD",0);
define("MODE_EDIT",1);
define("MODE_SEARCH",2);
define("MODE_LIST",3);
define("MODE_PRINT",4);
define("MODE_VIEW",5);
define("MODE_INLINE_ADD",6);
define("MODE_INLINE_EDIT",7);

define("LOGIN_HARDCODED",0);
define("LOGIN_TABLE",1);

define("ADVSECURITY_ALL",0);
define("ADVSECURITY_VIEW_OWN",1);
define("ADVSECURITY_EDIT_OWN",2);
define("ADVSECURITY_NONE",3);

define("ACCESS_LEVEL_ADMIN","Admin");
define("ACCESS_LEVEL_ADMINGROUP","AdminGroup");
define("ACCESS_LEVEL_USER","User");
define("ACCESS_LEVEL_GUEST","Guest");

define("DATABASE_MySQL","0");
define("DATABASE_Oracle","1");
define("DATABASE_MSSQLServer","2");
define("DATABASE_Access","3");
define("DATABASE_PostgreSQL","4");

$strLeftWrapper="`";
$strRightWrapper="`";

$cLoginTable				= "register";
$cUserNameField			= "username";
$cPasswordField			= "password";
$cUserGroupField			= "username";
$cEmailField			= "email";


$cFrom 					= "admin@openkbs.xyz";
if($cFrom)
	ini_set("sendmail_from",$cFrom);

if(!@$_SESSION["UserID"])
{
	$scriptname=$_SERVER["PHP_SELF"];
	$pos=strrpos($scriptname,"/");
	if($pos!==FALSE)
		$scriptname=substr($scriptname,$pos+1);
	if($scriptname!="login.php" && $scriptname!="remind.php" && $scriptname!="register.php")
	{
		$_SESSION["UserID"]="Guest";
		$_SESSION["GroupID"]="<Guest>";
		$_SESSION["AccessLevel"]=ACCESS_LEVEL_GUEST;
		if(function_exists("AfterSuccessfulLogin"))
		{
			$conn=db_connect();
			$dummy=array();
			AfterSuccessfulLogin("","",$dummy);
			db_close($conn);
		}
	}
}

set_error_handler("error_handler");

$useAJAX = true;
$suggestAllContent = true;

function CalcSearchParameters()
{
	global $strTableName, $strSQL;
	$sWhere="";
	if(@$_SESSION[$strTableName."_search"]==2)
//	 advanced search
	{
		foreach(@$_SESSION[$strTableName."_asearchfor"] as $f => $sfor)
		{
			$strSearchFor=trim($sfor);
			$strSearchFor2="";
			$type=@$_SESSION[$strTableName."_asearchfortype"][$f];
			if(array_key_exists($f,@$_SESSION[$strTableName."_asearchfor2"]))
				$strSearchFor2=trim(@$_SESSION[$strTableName."_asearchfor2"][$f]);
			if($strSearchFor!="" || true)
			{
				if (!$sWhere)
				{
					if($_SESSION[$strTableName."_asearchtype"]=="and")
						$sWhere="1=1";
					else
						$sWhere="1=0";
				}
				$strSearchOption=trim($_SESSION[$strTableName."_asearchopt"][$f]);
				if($where=StrWhereAdv($f, $strSearchFor, $strSearchOption, $strSearchFor2,$type))
				{
					if($_SESSION[$strTableName."_asearchnot"][$f])
						$where="not (".$where.")";
					if($_SESSION[$strTableName."_asearchtype"]=="and")
	   					$sWhere .= " and ".$where;
					else
	   					$sWhere .= " or ".$where;
				}
			}
		}
	}
	return $sWhere;
//	$strSQL = AddWhere($strSQL,$sWhere);
}

?>