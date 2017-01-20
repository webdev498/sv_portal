<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0);

include("include/dbcommon.php");

$changed=false;

$message="";

include('libs/Smarty.class.php');
$smarty = new Smarty();

$conn=db_connect();
//	Before Process event
if(function_exists("BeforeProcessChangePwd"))
	BeforeProcessChangePwd($conn);

if (@$_POST["btnSubmit"] == "Submit")
{	
	$rstemp=db_query("select * from `register` where 1=0",$conn);

	$opass = postvalue("opass");
	$newpass = postvalue("newpass");
	$opass = md5($opass);
	$newpass = md5($newpass);
	
	$value = @$_SESSION["UserID"];
	if(FieldNeedQuotes($rstemp,$cUserNameField))
		$value="'".db_addslashes($value)."'";
	else
		$value=(0+$value);
	$passvalue = $newpass;
	if(FieldNeedQuotes($rstemp,$cPasswordField))
		$passvalue="'".db_addslashes($passvalue)."'";
	else
		$passvalue=(0+$passvalue);


//	if($newpass!=$opass)
	{
    	$sWhere = " where ".AddFieldWrappers($cUserNameField)."=".$value;
		$strSQL = "select * from ".AddTableWrappers($cLoginTable).$sWhere;
		$rstemp=db_query($strSQL,$conn);

		if($row=db_fetch_array($rstemp))
		{
			if($opass == $row[$cPasswordField])
			{
				$retval=true;
				if(function_exists("BeforeChangePassword"))
					$retval=BeforeChangePassword($_POST["opass"],$_POST["newpass"]);
				if($retval)
				{
					$strSQL= "update ".AddTableWrappers($cLoginTable)." set ".AddFieldWrappers($cPasswordField)."=".$passvalue.$sWhere;
					db_exec($strSQL,$conn);
					$changed = true;
					if(function_exists("AfterChangePassword"))
						AfterChangePassword($_POST["opass"],$_POST["newpass"]);
					$smarty->assign("backurl",@$_SESSION["BackURL"]);
					$smarty->display("changepwd_success.htm");
					return;
				}
			}
			else
				$message = "Invalid password";
		}
	}
}
else
	$_SESSION["BackURL"] = @$_SERVER["HTTP_REFERER"];

$smarty->assign("backurl",@$_SESSION["BackURL"]);
$smarty->assign("message",$message);

$templatefile="changepwd.htm";
if(function_exists("BeforeShowChangePwd"))
	BeforeShowChangePwd($smarty,$templatefile);

$smarty->display($templatefile);
?>