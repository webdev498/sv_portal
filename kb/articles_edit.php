<?php 
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/articles_variables.php");


//	check if logged in
if(!@$_SESSION["UserID"] || !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Edit"))
{ 
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: login.php?message=expired"); 
	return;
}

$filename="";	
$status="";
$message="";
$error_happened=false;
$readevalues=false;


$showKeys = array();
$showValues = array();
$showRawValues = array();
$showFields = array();
$showDetailKeys = array();
$IsSaved = false;
$HaveData = true;
$inlineedit = (@$_REQUEST["editType"]=="inline") ? true : false;
$templatefile = ( $inlineedit ) ? "articles_inline_edit.htm" : "articles_edit.htm";

//connect database
$conn = db_connect();

//	Before Process event
if(function_exists("BeforeProcessEdit"))
	BeforeProcessEdit($conn);

$keys=array();
$keys["ArticleID"]=postvalue("editid1");

//	prepare data for saving
if(@$_POST["a"]=="edited")
{
//	read old values
//	$strSQL = $gstrSQL;
	$strWhereClause=KeyWhere($keys);
		//	select only owned records
	$strWhereClause=whereAdd($strWhereClause,SecuritySQL("Edit"));
	$strSQL=gSQLWhere($strWhereClause);
	$rsold=db_query($strSQL,$conn);
	$dataold=db_fetch_array($rsold);
	$strWhereClause=KeyWhere($keys);
	$strSQL = "update ".AddTableWrappers($strOriginalTableName)." set ";
	$evalues=array();
	$efilename_values=array();
	$files_delete=array();
	$files_move=array();
//	processing approved - start
	$value = postvalue("value_approved");
	$type=postvalue("type_approved");
	if (in_assoc_array("type_approved",$_POST) || in_assoc_array("value_approved",$_POST) || in_assoc_array("value_approved",$_FILES))	
	{
		$value=prepare_for_db("approved",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{	
		$evalues["approved"]=$value;
	}


//	processibng approved - end
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
		$evalues["Category"]=$value;
	}


//	processibng Category - end
	}
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
		$evalues["DateCreated"]=$value;
	}


//	processibng DateCreated - end
	}
//	processing Problem - start
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
		$evalues["Problem"]=$value;
	}


//	processibng Problem - end
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
		$evalues["Solution"]=$value;
	}


//	processibng Solution - end
	}
//	processing Title - start
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
		$evalues["Title"]=$value;
	}


//	processibng Title - end
//	processing screenp - start
	if(!$inlineedit)
	{
	$value = postvalue("value_screenp");
	$type=postvalue("type_screenp");
	if (in_assoc_array("type_screenp",$_POST) || in_assoc_array("value_screenp",$_POST) || in_assoc_array("value_screenp",$_FILES))	
	{
		$value=prepare_for_db("screenp",$value,$type,postvalue("filename_screenp"));
	}
	else
		$value=false;
	if(!($value===false))
	{	
		if($value)
		{
				$ext = CheckImageExtension($_FILES["file_screenp"]["name"]);
			$contents = myfile_get_contents($_FILES["file_screenp"]['tmp_name']);
			$thumb = CreateThumbnail($contents,150,$ext);
			$file = GetUploadFolder("screenp")."th".$value;
			if(file_exists($file))
					@unlink($file);
			$th = fopen($file,"w");
			fwrite($th,$thumb);
			fclose($th);
		}
		$evalues["screenp"]=$value;
	}


//	processibng screenp - end
	}
//	processing screens - start
	if(!$inlineedit)
	{
	$value = postvalue("value_screens");
	$type=postvalue("type_screens");
	if (in_assoc_array("type_screens",$_POST) || in_assoc_array("value_screens",$_POST) || in_assoc_array("value_screens",$_FILES))	
	{
		$value=prepare_for_db("screens",$value,$type,postvalue("filename_screens"));
	}
	else
		$value=false;
	if(!($value===false))
	{	
		if($value)
		{
				$ext = CheckImageExtension($_FILES["file_screens"]["name"]);
			$contents = myfile_get_contents($_FILES["file_screens"]['tmp_name']);
			$thumb = CreateThumbnail($contents,150,$ext);
			$file = GetUploadFolder("screens")."th".$value;
			if(file_exists($file))
					@unlink($file);
			$th = fopen($file,"w");
			fwrite($th,$thumb);
			fclose($th);
		}
		$evalues["screens"]=$value;
	}


//	processibng screens - end
	}
//	processing filep - start
	if(!$inlineedit)
	{
	$value = postvalue("value_filep");
	$type=postvalue("type_filep");
	if (in_assoc_array("type_filep",$_POST) || in_assoc_array("value_filep",$_POST) || in_assoc_array("value_filep",$_FILES))	
	{
		$value=prepare_for_db("filep",$value,$type,postvalue("filename_filep"));
	}
	else
		$value=false;
	if(!($value===false))
	{	
		$evalues["filep"]=$value;
	}


//	processibng filep - end
	}
//	processing files - start
	if(!$inlineedit)
	{
	$value = postvalue("value_files");
	$type=postvalue("type_files");
	if (in_assoc_array("type_files",$_POST) || in_assoc_array("value_files",$_POST) || in_assoc_array("value_files",$_FILES))	
	{
		$value=prepare_for_db("files",$value,$type,postvalue("filename_files"));
	}
	else
		$value=false;
	if(!($value===false))
	{	
		$evalues["files"]=$value;
	}


//	processibng files - end
	}

	foreach($efilename_values as $ekey=>$value)
		$evalues[$ekey]=$value;
//	do event
	$retval=true;
	if(function_exists("BeforeEdit"))
		$retval=BeforeEdit($evalues,$strWhereClause,$dataold,$keys,$message,$inlineedit);
	if($retval)
	{		
//	construct SQL string
		foreach($evalues as $ekey=>$value)
		{
			$strSQL.=AddFieldWrappers($ekey)."=".add_db_quotes($ekey,$value).", ";
		}
		if(substr($strSQL,-2)==", ")
			$strSQL=substr($strSQL,0,strlen($strSQL)-2);
		$strSQL.=" where ".$strWhereClause;
		if(SecuritySQL("Edit"))
			$strSQL .= " and (".SecuritySQL("Edit").")";
		set_error_handler("edit_error_handler");
		db_exec($strSQL,$conn);
		set_error_handler("error_handler");
		if(!$error_happened)
		{
//	delete & move files
			foreach ($files_delete as $file)
			{
				if(file_exists($file))
					@unlink($file);
			}
			foreach ($files_move as $file)
			{
				move_uploaded_file($file[0],$file[1]);
				if(strtoupper(substr(PHP_OS,0,3))!="WIN")
					@chmod($file[1],0777);
			}
			if ( $inlineedit ) 
			{
				$status="UPDATED";
				$message=""."Record updated"."";
				$IsSaved = true;
			} 
			else 
				$message="<div class=message><<< "."Record updated"." >>></div>";
//	after edit event
			if(function_exists("AfterEdit"))
				AfterEdit($evalues,KeyWhere($keys),$dataold,$keys,$inlineedit);
		}
	}
	else
		$readevalues=true;
}

//	get current values and show edit controls

//$strSQL = $gstrSQL;

$strWhereClause=KeyWhere($keys);
//	select only owned records
$strWhereClause=whereAdd($strWhereClause,SecuritySQL("Edit"));

$strSQL=gSQLWhere($strWhereClause);

$strSQLbak = $strSQL;
//	Before Query event
if(function_exists("BeforeQueryEdit"))
	BeforeQueryEdit($strSQL,$strWhereClause);

if($strSQLbak == $strSQL)
	$strSQL=gSQLWhere($strWhereClause);
LogInfo($strSQL);
$rs=db_query($strSQL,$conn);
$data=db_fetch_array($rs);

if($readevalues)
{
	$data["approved"]=$evalues["approved"];
	$data["Category"]=$evalues["Category"];
	$data["DateCreated"]=$evalues["DateCreated"];
	$data["Problem"]=$evalues["Problem"];
	$data["Solution"]=$evalues["Solution"];
	$data["Title"]=$evalues["Title"];
}

include('libs/Smarty.class.php');
$smarty = new Smarty();

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
	$includes.="var AUTOCOMPLETE_TABLE='articles_autocomplete.php';\r\n";
	$includes.="var SUGGEST_TABLE='articles_searchsuggest.php';\r\n";
	$includes.="var SUGGEST_LOOKUP_TABLE='articles_lookupsuggest.php';\r\n";
	}
	$includes.="</script>\r\n";
	if ($useAJAX)
		$includes.="<div id=\"search_suggest\"></div>\r\n";

		//	include datepicker files
	$includes.="<script language=\"JavaScript\" src=\"include/calendar.js\"></script>\r\n";




	$smarty->assign("includes",$includes);
	$smarty->assign("bodyonload",$bodyonload);
	if(strlen($onsubmit))
		$onsubmit="onSubmit=\"".$onsubmit."\"";
	$smarty->assign("onsubmit",$onsubmit);
}

$smarty->assign("key1",htmlspecialchars($keys["ArticleID"]));
$showKeys[] = rawurlencode($keys["ArticleID"]);
	$smarty->assign("show_key1", htmlspecialchars(GetData($data,"ArticleID", "")));

$smarty->assign("message",$message);

$readonlyfields=array();

$smarty->assign("value_approved",@$data["approved"]);
$smarty->assign("value_Category",@$data["Category"]);
$smarty->assign("value_DateCreated",@$data["DateCreated"]);
$smarty->assign("value_Problem",@$data["Problem"]);
$smarty->assign("value_Solution",@$data["Solution"]);
$smarty->assign("value_Title",@$data["Title"]);
$smarty->assign("value_screenp",@$data["screenp"]);
$smarty->assign("value_screens",@$data["screens"]);
$smarty->assign("value_filep",@$data["filep"]);
$smarty->assign("value_files",@$data["files"]);


$linkdata="";

if ($useAJAX) 
{
	$record_id= postvalue("recordID");

	if ( $inlineedit ) 
	{
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
	
} else {
}

$smarty->assign("linkdata",$linkdata);

if ($_REQUEST["a"]=="edited" && $inlineedit ) 
{
	if(!$data)
	{
		$data=$evalues;
		$HaveData=false;
	}
	//Preparation   view values

//	detail tables
	$masterquery="mastertable=articles";
	$masterquery.="&masterkey1=".rawurlencode($data["ArticleID"]);
	$showDetailKeys["comments"]=$masterquery;

	$keylink="";
	$keylink.="&key1=".htmlspecialchars(rawurlencode($data["ArticleID"]));

	////////////////////////////////////////////
	//	Category - 
		$value="";
				if(strlen($data["Category"]))
		{
			$strdata = make_db_value("Category",$data["Category"]);
			$LookupSQL="SELECT ";
							$LookupSQL.="`Category`";
			$LookupSQL.=" FROM `categories` WHERE `Category` = " . $strdata;
							LogInfo($LookupSQL);
			$rsLookup = db_query($LookupSQL,$conn);
			$lookupvalue=$data["Category"];
			if($lookuprow=db_fetch_numarray($rsLookup))
				$lookupvalue=$lookuprow[0];
						$value=ProcessLargeText(GetDataInt($lookupvalue,$data,"Category", ""),"field=Category".$keylink,"",MODE_LIST);
		}
		else
			$value="";
		$smarty->assign("show_Category",$value);
		$showValues[] = $value;
		$showFields[] = "Category";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	Title - Custom
		$value="";
				$value = GetData($data,"Title", "Custom");
		$smarty->assign("show_Title",$value);
		$showValues[] = $value;
		$showFields[] = "Title";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	Problem - 
		$value="";
				$value = ProcessLargeText(GetData($data,"Problem", ""),"","",MODE_LIST);
		$smarty->assign("show_Problem",$value);
		$showValues[] = $value;
		$showFields[] = "Problem";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	screenp - File-based Image
		$value="";
				if(CheckImageExtension($data["screenp"])) 
		{
						 	// show thumbnail
			$thumbname="th".$data["screenp"];
			if(substr("screenp/",0,7)!="http://" && !file_exists(GetUploadFolder("screenp").$thumbname))
				$thumbname=$data["screenp"];
			$value="<a target=_blank href=\"".htmlspecialchars(AddLinkPrefix("screenp",$data["screenp"]))."\">";
			$value.="<img";
			if($thumbname==$data["screenp"])
			{
											}
			$value.=" id=\"img_screenp_".$record_id."\" border=0";
			$value.=" src=\"".htmlspecialchars(AddLinkPrefix("screenp",$thumbname))."\"></a>";
		}
		$smarty->assign("show_screenp",$value);
		$showValues[] = $value;
		$showFields[] = "screenp";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	filep - Document Download
		$value="";
				$value = GetData($data,"filep", "Document Download");
		$smarty->assign("show_filep",$value);
		$showValues[] = $value;
		$showFields[] = "filep";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	screens - File-based Image
		$value="";
				if(CheckImageExtension($data["screens"])) 
		{
						 	// show thumbnail
			$thumbname="th".$data["screens"];
			if(substr("screens/",0,7)!="http://" && !file_exists(GetUploadFolder("screens").$thumbname))
				$thumbname=$data["screens"];
			$value="<a target=_blank href=\"".htmlspecialchars(AddLinkPrefix("screens",$data["screens"]))."\">";
			$value.="<img";
			if($thumbname==$data["screens"])
			{
											}
			$value.=" id=\"img_screens_".$record_id."\" border=0";
			$value.=" src=\"".htmlspecialchars(AddLinkPrefix("screens",$thumbname))."\"></a>";
		}
		$smarty->assign("show_screens",$value);
		$showValues[] = $value;
		$showFields[] = "screens";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	files - Document Download
		$value="";
				$value = GetData($data,"files", "Document Download");
		$smarty->assign("show_files",$value);
		$showValues[] = $value;
		$showFields[] = "files";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	Views - 
		$value="";
				$value = ProcessLargeText(GetData($data,"Views", ""),"","",MODE_LIST);
		$smarty->assign("show_Views",$value);
		$showValues[] = $value;
		$showFields[] = "Views";
				$showRawValues[] = "";
	////////////////////////////////////////////
	//	approved - Checkbox
		$value="";
				$value = GetData($data,"approved", "Checkbox");
		$smarty->assign("show_approved",$value);
		$showValues[] = $value;
		$showFields[] = "approved";
				$showRawValues[] = "";
}

if ( $_REQUEST["a"]=="edited" && $inlineedit ) 
{
	echo "<textarea id=\"data\">";
	if($IsSaved)
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
	if(function_exists("BeforeShowEdit"))
		BeforeShowEdit($smarty,$templatefile);
	$smarty->display($templatefile);
}

function edit_error_handler($errno, $errstr, $errfile, $errline)
{
	global $readevalues, $message, $status, $inlineedit, $error_happened;
	if ( $inlineedit ) 
		$message=""."Record was NOT edited".". ".$errstr;
	else  
		$message="<div class=message><<< "."Record was NOT edited"." >>><br><br>".$errstr."</div>";
	$readevalues=true;
	$error_happened=true;
}

?>