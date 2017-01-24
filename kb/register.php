<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/register_variables.php");


$registered=false;
$onload=' ';
$strMessage="";
$strUsername="";
$strPassword="";
$strEmail="";
$values=array();

include('libs/Smarty.class.php');
$smarty = new Smarty();

$conn=db_connect();

//	Before Process event
if(function_exists("BeforeProcessRegister"))
	BeforeProcessRegister($conn);

if(@$_POST["btnSubmit"] == "Register")
{

	$filename_values=array();
	$files_move=array();

//	processing username - start

	$value = postvalue("value_username");
	$type=postvalue("type_username");
	if (in_assoc_array("type_username",$_POST) || in_assoc_array("value_username",$_POST) || in_assoc_array("value_username",$_FILES))
	{
		$value=prepare_for_db("username",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$values["username"]=$value;
	}

//	processibng username - end

//	processing password - start

	$value = postvalue("value_password");
	$type=postvalue("type_password");
	if (in_assoc_array("type_password",$_POST) || in_assoc_array("value_password",$_POST) || in_assoc_array("value_password",$_FILES))
	{
		$value=prepare_for_db("password",$value,$type);
	}
	else
		$value=false;
	if(!($value===false))
	{
		$values["password"]=$value;
	}

//	processibng password - end

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
		$values["email"]=$value;
	}

//	processibng email - end


	$strUsername = $values["username"];
	$strPassword = $values["password"];
	$strEmail = $values["email"];

//	add filenames to values
	foreach($filename_values as $key=>$value)
		$values[$key]=$value;

	$strMessage="";
//	check if entered username already exists
	if(!strlen($strUsername))
		$strMessage="Username can not be empty.";
	else
	{
		$strSQL="select count(*) from `register` where `username`=".add_db_quotes("username",$strUsername);
	   	$rs=db_query($strSQL,$conn);
		$data=db_fetch_numarray($rs);
		if($data[0]>0)
			$strMessage="Username"." <i>".$strUsername."</i> "."already exists. Choose another username.";
	}

//	check if entered email already exists
	
	if(!strlen($strEmail))
		$strMessage="Please enter valid email address.";
	else
	{
		$strSQL="select count(*) from `register` where `email`=".add_db_quotes("email",$strEmail);
	   	$rs=db_query($strSQL,$conn);
		$data=db_fetch_numarray($rs);
		if($data[0]>0)
		{
			if($strMessage)
				$strMessage.="<br>";
			$strMessage.="Email"." <i>".$strEmail."</i> "."already registered. If you forgot your username or password use the password reminder form.";
		}
	}

	$retval=true;
	if(!$strMessage)
	{
		if(function_exists("BeforeRegister"))
			$retval = BeforeRegister($values);
	}

	if(!$strMessage && $retval)
	{

	//	encrypt password
	$originalpassword=$values["password"];
	$values["password"]=md5($values["password"]);
//	make SQL string
		$strSQL = "insert into `register` ";
		$strFields="(";
		$strValues="(";
		foreach($values as $akey=>$value)
		{
			$strFields.=AddFieldWrappers($akey).", ";
			$strValues.=add_db_quotes($akey,$value).", ";
		}
		if(substr($strFields,-2)==", ")
			$strFields=substr($strFields,0,strlen($strFields)-2);
		if(substr($strValues,-2)==", ")
			$strValues=substr($strValues,0,strlen($strValues)-2);
		$strSQL.=$strFields.") values ".$strValues.")";
//	insert new user
		LogInfo($strSQL);
		db_exec($strSQL,$conn);

		foreach ($files_move as $file)
			move_uploaded_file($file[0],$file[1]);

	$values["password"]=$originalpassword;

		if(function_exists("AfterSuccessfulRegistration"))
			AfterSuccessfulRegistration($values);

//	send email to user
		$message="You have registered as user at http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]."\r\n";
		$label = "username";
		$message.=$label.": ".$values["username"]."\r\n";
		$label = "password";
		$message.=$label.": ".$values["password"]."\r\n";
		$label = "email";
		$message.=$label.": ".$values["email"]."\r\n";
		mail($strEmail,"Notification on registering",$message);

//	send letter to admin
		$message="User registered at http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]."\r\n";
		$label = "username";
		$message.=$label.": ".$values["username"]."\r\n";
		$label = "password";
		$message.=$label.": ".$values["password"]."\r\n";
		$label = "email";
		$message.=$label.": ".$values["email"]."\r\n";
		mail("admin@openkbs.xyz","Notification on registering",$message);
//	show Registartion successful message
		$smarty->assign("username",htmlspecialchars($strUsername));
		$smarty->assign("password",htmlspecialchars($strPassword));
		$smarty->display("register_success.htm");
		return;
	}
	else
	{
		if(function_exists("AfterUnsuccessfulRegistration"))
			AfterUnsuccessfulRegistration($values);
	}
	$smarty->assign("message",$strMessage);
}

//	validation stuff
$bodyonload="";
$onsubmit="";
$includes="";


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
	$includes.="var AUTOCOMPLETE_TABLE='register_autocomplete.php';\r\n";
	$includes.="var SUGGEST_LOOKUP_TABLE='register_lookupsuggest.php';\r\n";
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

//	assign values to the controls

if(!count($values))
{
	$values["email"]=@$_SESSION["_" . $strTableName."_OwnerID"];
}

$smarty->assign("value_username",@$values["username"]);
$smarty->assign("value_password",@$values["password"]);
$smarty->assign("value_email",@$values["email"]);


$readonlyfields=array();

//	show readonly fields

$linkdata="";


$linkdata .= "<script type=\"text/javascript\">\r\n";

if ($useAJAX) {
}
else
{
}
$linkdata.="</script>\r\n";


/*
if ($useAJAX) {
	$linkdata .= "<script type=\"text/javascript\">\r\n";
	$linkdata .= "$(document).ready(function(){ \r\n";
	$linkdata .= "
function loadSelectContent(txt, selectControl, selectValue) 
{
	$('#'+selectControl).get(0).options[0]=new Option(TEXT_PLEASE_SELECT,'');
	var str = txt.split('\\n');
	var index = 0;
	for(i=0,j=0; i < str.length - 1; i=i+2, j++) {
		$('#'+selectControl).get(0).options[j+1]=new Option(unescape(str[i+1]),unescape(str[i]));
		if ( unescape(str[i]) == selectValue ) {index = j+1;}
	}
	$('#'+selectControl).get(0).selectedIndex = index;
	if(index==0 && j==1)
		$('#'+selectControl).get(0).selectedIndex = 1;
}"."\r\n";
	$linkdata .= "});\r\n";
	$linkdata .= "</script>\r\n";
} else {
}
*/
$smarty->assign("linkdata",$linkdata);

$smarty->assign("submitonclick","onclick=\"javascript: if (document.forms.editform.value1_password==undefined) return true; if(document.forms.editform.value_password.value != document.forms.editform.value1_password.value) {alert('"."Passwords do not match. Re-enter password"."');document.forms.editform.value1_password.value='';return false;} return true;\"");

$templatefile="register.htm";
if(function_exists("BeforeShowRegister"))
	BeforeShowRegister($smarty,$templatefile);

$smarty->display($templatefile);

?>