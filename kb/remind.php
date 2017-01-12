<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0);

include("include/dbcommon.php");


$cEmailField = "email";
$reminded=false;
$strSearchBy="username";

include('libs/Smarty.class.php');
$smarty = new Smarty();

$strUsername="";
$strEmail="";
$strMessage="";

$conn=db_connect();
//	Before Process event
if(function_exists("BeforeProcessRemindPwd"))
	BeforeProcessRemindPwd($conn);


if (@$_POST["btnSubmit"] == "Remind")
{
	$strSearchBy=$_POST["searchby"];
	$strUsername=refine(@$_POST["username"]);
	$strEmail=refine(@$_POST["email"]);

	$rstemp=db_query("select * from `register` where 1=0",$conn);

	$tosearch=false;
	if($strSearchBy!="email")
	{
		$value=$strUsername;
		if((string)$value!="")
			$tosearch=true;
		if(FieldNeedQuotes($rstemp,$cUserNameField))
			$value="'".db_addslashes($value)."'";
		else
			$value=(0+$value);
		$sWhere=AddFieldWrappers($cUserNameField)."=".$value;
	}
	else
	{
		$value=$strEmail;
		if((string)$value!="")
			$tosearch=true;
		if(FieldNeedQuotes($rstemp,$cEmailField))
			$value="'".db_addslashes($value)."'";
		else
			$value=(0+$value);
		$sWhere=AddFieldWrappers($cEmailField)."=".$value;
	}
	
	if($tosearch && function_exists("BeforeRemindPassword"))
		$tosearch = BeforeRemindPassword($strUsername,$strEmail);
	
	if($tosearch)
	{
		$strSQL="select ".AddFieldWrappers($cUserNameField).",".AddFieldWrappers($cPasswordField).",".AddFieldWrappers($cEmailField)." from `register` where ".$sWhere;
		$rs=db_query($strSQL,$conn);
		if($data=db_fetch_numarray($rs))
		{
			$password=$data[1];
//	generate 6 chars length password
			$password="";
			for($i=0;$i<6;$i++)
			{
				$j=rand(0,35);
				if($j<26)
					$password.=chr(ord('a')+$j);
				else
					$password.=chr(ord('0')-26+$j);
			}
			db_exec("update `register` set ".AddFieldWrappers($cPasswordField)."='".md5($password)."' where ".$sWhere,$conn);
			$message="Password reminder\r\n";
			$message.="You asked to remind your username and password at http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]."\r\n";
			$message.="User Name: ".$data[0]."\r\n";
			$message.="Password: ".$password."\r\n";
			mail($data[2],"Password retrieval",$message);
			$reminded=true;
			if(function_exists("AfterRemindPassword"))
				AfterRemindPassword($strUsername,$strEmail);
			if($strSearchBy!="email")
				$smarty->assign("params","username=".rawurlencode($strUsername));
			$smarty->display("remind_success.htm");
			return;
		}
	}
	if(!$reminded)
	{
		if($strSearchBy!="email")
			$strMessage="User"." <i>".$strUsername."</i> "."is not registered.";
		else
			$strMessage="This email don't exist in our database";
	}
}
$smarty->assign("strSearchBy","value=\"".$strSearchBy."\"");
if($strSearchBy=="username")
{
	$smarty->assign("checked_username","checked");
	$smarty->assign("checked_email","");
	$smarty->assign("searchby_disabled","email");
}
else
{
	$smarty->assign("checked_username","");
	$smarty->assign("checked_email","checked");
	$smarty->assign("searchby_disabled","username");
}
$smarty->assign("strUsername","value=\"".htmlspecialchars($strUsername)."\"");
$smarty->assign("strEmail","value=\"".htmlspecialchars($strEmail)."\"");
$smarty->assign("message",@$strMessage);


$templatefile="remind.htm";
if(function_exists("BeforeShowRemindPwd"))
	BeforeShowRemindPwd($smarty,$templatefile);

$smarty->display($templatefile);
