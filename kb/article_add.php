<?php 
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0); 

include("include/dbcommon.php");
include("include/article_variables.php");


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
$templatefile = "article_add.htm";

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
//	processing Category - start
	if(!$inlineedit)
	{
	$value = postvalue("value_Category");
	$type=postvalue("type_Category");
	if (in_assoc_array("type_Category",$_POST) || in_assoc_array("value_Category",$_POST) || in_assoc_array("value_Category",$_FILES))
	{
		$value=prepare_for_db("Category",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["Category"]=$value;
	}
	}
//	processibng Category - end
//	processing Title - start
	if(!$inlineedit)
	{
	$value = postvalue("value_Title");
	$type=postvalue("type_Title");
	if (in_assoc_array("type_Title",$_POST) || in_assoc_array("value_Title",$_POST) || in_assoc_array("value_Title",$_FILES))
	{
		$value=prepare_for_db("Title",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["Title"]=$value;
	}
	}
//	processibng Title - end
//	processing Problem - start
	if(!$inlineedit)
	{
	$value = postvalue("value_Problem");
	$type=postvalue("type_Problem");
	if (in_assoc_array("type_Problem",$_POST) || in_assoc_array("value_Problem",$_POST) || in_assoc_array("value_Problem",$_FILES))
	{
		$value=prepare_for_db("Problem",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["Problem"]=$value;
	}
	}
//	processibng Problem - end
//	processing screenp - start
	if(!$inlineedit)
	{
	$value = postvalue("value_screenp");
	$type=postvalue("type_screenp");
	if (in_assoc_array("type_screenp",$_POST) || in_assoc_array("value_screenp",$_POST) || in_assoc_array("value_screenp",$_FILES))
	{
		$value=prepare_for_db("screenp",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["screenp"]=$value;
	}
	}
//	processibng screenp - end
//	processing filep - start
	if(!$inlineedit)
	{
	$value = postvalue("value_filep");
	$type=postvalue("type_filep");
	if (in_assoc_array("type_filep",$_POST) || in_assoc_array("value_filep",$_POST) || in_assoc_array("value_filep",$_FILES))
	{
		$value=prepare_for_db("filep",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["filep"]=$value;
	}
	}
//	processibng filep - end
//	processing Solution - start
	if(!$inlineedit)
	{
	$value = postvalue("value_Solution");
	$type=postvalue("type_Solution");
	if (in_assoc_array("type_Solution",$_POST) || in_assoc_array("value_Solution",$_POST) || in_assoc_array("value_Solution",$_FILES))
	{
		$value=prepare_for_db("Solution",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["Solution"]=$value;
	}
	}
//	processibng Solution - end
//	processing screens - start
	if(!$inlineedit)
	{
	$value = postvalue("value_screens");
	$type=postvalue("type_screens");
	if (in_assoc_array("type_screens",$_POST) || in_assoc_array("value_screens",$_POST) || in_assoc_array("value_screens",$_FILES))
	{
		$value=prepare_for_db("screens",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["screens"]=$value;
	}
	}
//	processibng screens - end
//	processing files - start
	if(!$inlineedit)
	{
	$value = postvalue("value_files");
	$type=postvalue("type_files");
	if (in_assoc_array("type_files",$_POST) || in_assoc_array("value_files",$_POST) || in_assoc_array("value_files",$_FILES))
	{
		$value=prepare_for_db("files",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["files"]=$value;
	}
	}
//	processibng files - end
//	processing DateCreated - start
	if(!$inlineedit)
	{
	$value = postvalue("value_DateCreated");
	$type=postvalue("type_DateCreated");
	if (in_assoc_array("type_DateCreated",$_POST) || in_assoc_array("value_DateCreated",$_POST) || in_assoc_array("value_DateCreated",$_FILES))
	{
		$value=prepare_for_db("DateCreated",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$avalues["DateCreated"]=$value;
	}
	}
//	processibng DateCreated - end


//	insert ownerid value if exists
	$avalues["approved"]=prepare_for_db("approved",$_SESSION["_".$strTableName."_OwnerID"]);



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
			$message="<div class=message><<< "."Record was added"." >>></div>";
if($inlineedit || function_exists("AfterAdd"))
{

	$failed_inline_add = false;
						$keys["ArticleID"]=mysql_insert_id($conn);
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
		$copykeys["ArticleID"]=postvalue("copyid1");
	}
	else
	{
		$copykeys["ArticleID"]=postvalue("editid1");
	}
	$strWhere=KeyWhere($copykeys);
	$strWhere=whereAdd($strWhere,SecuritySQL("Search"));
	$strSQL = gSQLWhere($strWhere);

	LogInfo($strSQL);
	$rs=db_query($strSQL,$conn);
	$defvalues=db_fetch_array($rs);
//	clear key fields
	$defvalues["ArticleID"]="";
//call CopyOnLoad event
	if(function_exists("CopyOnLoad"))
		CopyOnLoad($defvalues,$strWhere);
}
else if(!count($defvalues))
{
	$defvalues["DateCreated"]=now();
}
if($readavalues)
{
	$defvalues["Category"]=@$avalues["Category"];
	$defvalues["DateCreated"]=@$avalues["DateCreated"];
	$defvalues["Problem"]=@$avalues["Problem"];
	$defvalues["Solution"]=@$avalues["Solution"];
	$defvalues["Title"]=@$avalues["Title"];
	$defvalues["screenp"]=@$avalues["screenp"];
	$defvalues["screens"]=@$avalues["screens"];
}

foreach($defvalues as $key=>$value)
	$smarty->assign("value_".GoodFieldName($key),$value);

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
			$bodyonload.="define('value_Category','".$validatetype."','Category');";
				$validatetype="";
			$validatetype.="IsRequired";
		if($validatetype)
			$bodyonload.="define('value_DateCreated','".$validatetype."','DateCreated');";
				$validatetype="";
			$validatetype.="IsRequired";
		if($validatetype)
			$bodyonload.="define('value_Problem','".$validatetype."','Problem');";
			  		$validatetype="";
			$validatetype.="IsRequired";
		if($validatetype)
			$bodyonload.="define('value_Title','".$validatetype."','Title');";

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
	$includes.="var AUTOCOMPLETE_TABLE='article_autocomplete.php';\r\n";
	$includes.="var SUGGEST_TABLE='article_searchsuggest.php';\r\n";
	$includes.="var SUGGEST_LOOKUP_TABLE='article_lookupsuggest.php';\r\n";
	}
	$includes.="</script>\r\n";
	if ($useAJAX) {
	$includes.="<div id=\"search_suggest\"></div>\r\n";
	}

		//	include datepicker files
	$includes.="<script language=\"JavaScript\" src=\"include/calendar.js\"></script>\r\n";




	$smarty->assign("includes",$includes);
	$smarty->assign("bodyonload",$bodyonload);
	if(strlen($onsubmit))
		$onsubmit="onSubmit=\"".$onsubmit."\"";
	$smarty->assign("onsubmit",$onsubmit);

$smarty->assign("message",$message);
$smarty->assign("status",$status);

$readonlyfields=array();

//	show readonly fields

$linkdata="";

if ($useAJAX) 
{
	$record_id= postvalue("recordID");

		$linkdata = "<script type=\"text/javascript\">\r\n".
		"$(document).ready(function(){ \r\n".
		$linkdata.
		"});</script>";
} 
else 
{
}

$smarty->assign("linkdata",$linkdata);

	if(function_exists("BeforeShowAdd"))
		BeforeShowAdd($smarty,$templatefile);

	$smarty->display($templatefile);
function add_error_handler($errno, $errstr, $errfile, $errline)
{
	global $readavalues, $message, $status, $inlineedit, $error_happened;
		$message="<div class=message><<< "."Record was NOT added"." >>><br><br>".$errstr."</div>";
	$readavalues=true;
	$error_happened=true;
}
?>
