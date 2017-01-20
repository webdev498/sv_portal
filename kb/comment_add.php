<?php 
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0); 

include("include/dbcommon.php");
include("include/comment_variables.php");


//	check if logged in
if(!@$_SESSION["UserID"] || !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Add"))
{ 
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: login.php?message=expired"); 
	return;
}

$filename="";
$status="";
$message="";
$error_happened=false;
$readavalues=false;


$showKeys = array();
$showValues = array();
$showRawValues = array();
$showFields = array();
$showDetailKeys = array();
$IsSaved = false;
$HaveData = true;
$inlineedit = (@$_REQUEST["editType"]=="inline") ? true : false;
$keys=array();
$templatefile = ( $inlineedit ) ? "comment_inline_add.htm" : "comment_add.htm";

//connect database
$conn = db_connect();

//	Before Process event
if(function_exists("BeforeProcessAdd"))
	BeforeProcessAdd($conn);

include('libs/Smarty.class.php');
$smarty = new Smarty();

// insert new record if we have to

if(@$_POST["a"]=="added")
{
	$afilename_values=array();
	$avalues=array();
	$files_move=array();
//	processing email - start
	$value = postvalue("value_email");
	$type=postvalue("type_email");
	if (in_assoc_array("type_email",$_POST) || in_assoc_array("value_email",$_POST) || in_assoc_array("value_email",$_FILES))
	{
		$value=prepare_for_db("email",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["email"]=$value;
	}
//	processibng email - end
//	processing comment - start
	$value = postvalue("value_comment");
	$type=postvalue("type_comment");
	if (in_assoc_array("type_comment",$_POST) || in_assoc_array("value_comment",$_POST) || in_assoc_array("value_comment",$_FILES))
	{
		$value=prepare_for_db("comment",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["comment"]=$value;
	}
//	processibng comment - end


//	insert ownerid value if exists
	$avalues["access"]=prepare_for_db("access",$_SESSION["_".$strTableName."_OwnerID"]);


//	insert masterkey value if exists and if not specified
	if(@$_SESSION[$strTableName."_mastertable"]=="article")
	{
		$avalues["ArticleID"]=prepare_for_db("ArticleID",$_SESSION[$strTableName."_masterkey1"]);
	}

//	add filenames to values
	foreach($afilename_values as $akey=>$value)
		$avalues[$akey]=$value;
//	make SQL string
	$strSQL = "insert into ".AddTableWrappers($strOriginalTableName)." ";
	$strFields="(";
	$strValues="(";
	
//	before Add event
	$retval = true;
	if(function_exists("BeforeAdd"))
		$retval=BeforeAdd($avalues,$message,$inlineedit);
	if($retval)
	{
		foreach($avalues as $akey=>$value)
		{
			$strFields.=AddFieldWrappers($akey).", ";
			$strValues.=add_db_quotes($akey,$value).", ";
		}
		if(substr($strFields,-2)==", ")
			$strFields=substr($strFields,0,strlen($strFields)-2);
		if(substr($strValues,-2)==", ")
			$strValues=substr($strValues,0,strlen($strValues)-2);
		$strSQL.=$strFields.") values ".$strValues.")";
		LogInfo($strSQL);
		set_error_handler("add_error_handler");
		db_exec($strSQL,$conn);
		set_error_handler("error_handler");
//	move files
		if(!$error_happened)
		{
			foreach ($files_move as $file)
			{
				move_uploaded_file($file[0],$file[1]);
				if(strtoupper(substr(PHP_OS,0,3))!="WIN")
					@chmod($file[1],0777);
			}
			if ( $inlineedit ) 
			{
				$status="ADDED";
				$message=""."Record was added"."";
				$IsSaved = true;
			} 
			else
				$message="<div class=message><<< "."Record was added"." >>></div>";
if($inlineedit || function_exists("AfterAdd"))
{

	$failed_inline_add = false;
						$keys["CommentID"]=mysql_insert_id($conn);
}	

//	after edit event
			if(function_exists("AfterAdd"))
				AfterAdd($avalues,$keys,$inlineedit);
		}
	}
	else
		$readavalues=true;
}

$defvalues=array();


//	copy record
if(array_key_exists("copyid1",$_REQUEST) || array_key_exists("editid1",$_REQUEST))
{
	$copykeys=array();
	if(array_key_exists("copyid1",$_REQUEST))
	{
		$copykeys["CommentID"]=postvalue("copyid1");
	}
	else
	{
		$copykeys["CommentID"]=postvalue("editid1");
	}
	$strWhere=KeyWhere($copykeys);
	$strWhere=whereAdd($strWhere,SecuritySQL("Search"));
	$strSQL = gSQLWhere($strWhere);

	LogInfo($strSQL);
	$rs=db_query($strSQL,$conn);
	$defvalues=db_fetch_array($rs);
//	clear key fields
	$defvalues["CommentID"]="";
//call CopyOnLoad event
	if(function_exists("CopyOnLoad"))
		CopyOnLoad($defvalues,$strWhere);
}
else if(!count($defvalues))
{
}
if($readavalues)
{
	$defvalues["comment"]=@$avalues["comment"];
	$defvalues["email"]=@$avalues["email"];
}

foreach($defvalues as $key=>$value)
	$smarty->assign("value_".GoodFieldName($key),$value);

if ( !$inlineedit ) {
	//	include files
	$includes="";

	//	validation stuff
	$bodyonload="";
	$onsubmit="";
		$includes.="<script language=\"JavaScript\" src=\"include/validate.js\"></script>\r\n";
	$includes.="<script language=\"JavaScript\">\r\n";
	$includes.="var TEXT_FIELDS_REQUIRED='".addslashes("The Following fields are Required")."';\r\n";
	$includes.="var TEXT_FIELDS_ZIPCODES='".addslashes("The Following fields must be valid Zipcodes")."';\r\n";
	$includes.="var TEXT_FIELDS_EMAILS='".addslashes("The Following fields must be valid Emails")."';\r\n";
	$includes.="var TEXT_FIELDS_NUMBERS='".addslashes("The Following fields must be Numbers")."';\r\n";
	$includes.="var TEXT_FIELDS_CURRENCY='".addslashes("The Following fields must be currency")."';\r\n";
	$includes.="var TEXT_FIELDS_PHONE='".addslashes("The Following fields must be Phone Numbers")."';\r\n";
	$includes.="var TEXT_FIELDS_PASSWORD1='".addslashes("The Following fields must be valid Passwords")."';\r\n";
	$includes.="var TEXT_FIELDS_PASSWORD2='".addslashes("should be at least 4 characters long")."';\r\n";
	$includes.="var TEXT_FIELDS_PASSWORD3='".addslashes("Cannot be 'password'")."';\r\n";
	$includes.="var TEXT_FIELDS_STATE='".addslashes("The Following fields must be State Names")."';\r\n";
	$includes.="var TEXT_FIELDS_SSN='".addslashes("The Following fields must be Social Security Numbers")."';\r\n";
	$includes.="var TEXT_FIELDS_DATE='".addslashes("The Following fields must be valid dates")."';\r\n";
	$includes.="var TEXT_FIELDS_TIME='".addslashes("The Following fields must be valid time in 24-hours format")."';\r\n";
	$includes.="var TEXT_FIELDS_CC='".addslashes("The Following fields must be valid Credit Card Numbers")."';\r\n";
	$includes.="var TEXT_FIELDS_SSN='".addslashes("The Following fields must be Social Security Numbers")."';\r\n";
	$includes.="</script>\r\n";
			$validatetype="";
			$validatetype.="IsRequired";
		if($validatetype)
			$bodyonload.="define('value_comment','".$validatetype."','comment');";
			  		$validatetype="";
			$validatetype.="IsRequired";
		if($validatetype)
			$bodyonload.="define('value_email','".$validatetype."','email');";

	if($bodyonload)
	{
		$onsubmit="return validate();";
		$bodyonload="onload=\"".$bodyonload."\"";
	}

	if ($useAJAX) {
	$includes.="<script language=\"JavaScript\" src=\"include/jquery.js\"></script>\r\n";
	$includes.="<script language=\"JavaScript\" src=\"include/ajaxsuggest.js\"></script>\r\n";
	}
	$includes.="<script language=\"JavaScript\" src=\"include/jsfunctions.js\"></script>\r\n";
	$includes.="<script language=\"JavaScript\">\r\n".
	"var locale_dateformat = ".$locale_info["LOCALE_IDATE"].";\r\n".
	"var locale_datedelimiter = \"".$locale_info["LOCALE_SDATE"]."\";\r\n".
	"var bLoading=false;\r\n".
	"var TEXT_PLEASE_SELECT='".addslashes("Please select")."';\r\n";
	if ($useAJAX) {
	$includes.="var AUTOCOMPLETE_TABLE='comment_autocomplete.php';\r\n";
	$includes.="var SUGGEST_TABLE='comment_searchsuggest.php';\r\n";
	$includes.="var SUGGEST_LOOKUP_TABLE='comment_lookupsuggest.php';\r\n";
	}
	$includes.="</script>\r\n";
	if ($useAJAX) {
	$includes.="<div id=\"search_suggest\"></div>\r\n";
	}





	$smarty->assign("includes",$includes);
	$smarty->assign("bodyonload",$bodyonload);
	if(strlen($onsubmit))
		$onsubmit="onSubmit=\"".$onsubmit."\"";
	$smarty->assign("onsubmit",$onsubmit);
}

$smarty->assign("message",$message);
$smarty->assign("status",$status);

$readonlyfields=array();

//	show readonly fields

$linkdata="";

if ($useAJAX) 
{
	$record_id= postvalue("recordID");

	if ( $inlineedit ) {
		if(@$_REQUEST["browser"]=="ie")
			$smarty->assign("browserie",true);
		$smarty->assign("id",$record_id);
		$linkdata=str_replace(array("&","<",">"),array("&amp;","&lt;","&gt;"),$linkdata);

	} 
	else 
	{
		$linkdata = "<script type=\"text/javascript\">\r\n".
		"$(document).ready(function(){ \r\n".
		$linkdata.
		"});</script>";
	}
} 
else 
{
}

$smarty->assign("linkdata",$linkdata);


if ( @$_POST["a"]=="added" && $inlineedit ) {
	//Preparation   view values

	//	get current values and show edit controls

	$data=0;
	if(count($keys))
	{

		$where=KeyWhere($keys);
		//	select only owned records
		$where=whereAdd($where,SecuritySQL("Search"));
		$strSQL = gSQLWhere($where);

/*
		$strSQL=$gstrSQL;
		$where = KeyWhere($keys);
		$strSQL = AddWhere($strSQL,$where);
		//	select only owned records
		$strSQL = AddWhere($strSQL,SecuritySQL("Search"));
*/
		LogInfo($strSQL);

		$rs=db_query($strSQL,$conn);
		$data=db_fetch_array($rs);
	}
	if(!$data)
	{
		$data=$avalues;
		$HaveData=false;
	}

	//check if correct values added

	
	
	$smarty->assign("key1",htmlspecialchars($keys["CommentID"]));
	$showKeys[] = htmlspecialchars($keys["CommentID"]);

	$keylink="";
	$keylink.="&key1=".htmlspecialchars(rawurlencode($data["CommentID"]));

	////////////////////////////////////////////
	//	email - 
		$value="";
				$value = ProcessLargeText(GetData($data,"email", ""),"","",MODE_LIST);
		$showValues[] = $value;
		$showFields[] = "email";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	comment - 
		$value="";
				$value = ProcessLargeText(GetData($data,"comment", ""),"","",MODE_LIST);
		$showValues[] = $value;
		$showFields[] = "comment";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	ArticleID - 
		$value="";
				$value = ProcessLargeText(GetData($data,"ArticleID", ""),"","",MODE_LIST);
		$showValues[] = $value;
		$showFields[] = "ArticleID";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	access - 
		$value="";
				$value = ProcessLargeText(GetData($data,"access", ""),"","",MODE_LIST);
		$showValues[] = $value;
		$showFields[] = "access";
				$showRawValues[] = "";
}

if ( @$_POST["a"]=="added" && $inlineedit ) 
{
	echo "<textarea id=\"data\">";
	if($IsSaved && count($showValues))
	{
		if($HaveData)
			echo "saved";
		else
			echo "savnd";
		print_inline_array($showKeys);
		echo "\n";
		print_inline_array($showValues);
		echo "\n";
		print_inline_array($showFields);
		echo "\n";
		print_inline_array($showRawValues);
		echo "\n";
		print_inline_array($showDetailKeys,true);
		echo "\n";
		print_inline_array($showDetailKeys);
	}
	else
	{
		echo "error";
		echo str_replace(array("&","<","\\","\r","\n"),array("&amp;","&lt;","\\\\","\\r","\\n"),$message);
	}
	echo "</textarea>";
} 
else 
{
	if(function_exists("BeforeShowAdd"))
		BeforeShowAdd($smarty,$templatefile);

	$smarty->display($templatefile);
}
function add_error_handler($errno, $errstr, $errfile, $errline)
{
	global $readavalues, $message, $status, $inlineedit, $error_happened;
	if ( $inlineedit ) 
		$message=""."Record was NOT added".". ".$errstr;
	else  
		$message="<div class=message><<< "."Record was NOT added"." >>><br><br>".$errstr."</div>";
	$readavalues=true;
	$error_happened=true;
}
?>
