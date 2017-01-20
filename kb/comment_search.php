<?php 
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/comment_variables.php");

//	check if logged in
if(!@$_SESSION["UserID"] || !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search"))
{ 
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: login.php?message=expired"); 
	return;
}

//connect database
$conn = db_connect();

include('libs/Smarty.class.php');
$smarty = new Smarty();

//	Before Process event
if(function_exists("BeforeProcessSearch"))
	BeforeProcessSearch($conn);


$includes=
"<STYLE>
	.vis1	{ visibility:\"visible\" }
	.vis2	{ visibility:\"hidden\" }
</STYLE>
<script language=\"JavaScript\" src=\"include/calendar.js\"></script>
<script language=\"JavaScript\" src=\"include/jsfunctions.js\"></script>\r\n";
if ($useAJAX) {
$includes.="<script language=\"JavaScript\" src=\"include/jquery.js\"></script>
<script language=\"JavaScript\" src=\"include/ajaxsuggest.js\"></script>\r\n";
}
$includes.="<script language=\"JavaScript\" type=\"text/javascript\">\r\n".
"var locale_dateformat = ".$locale_info["LOCALE_IDATE"].";\r\n".
"var locale_datedelimiter = \"".$locale_info["LOCALE_SDATE"]."\";\r\n".
"var bLoading=false;\r\n".
"var TEXT_PLEASE_SELECT='".addslashes("Please select")."';\r\n";
if ($useAJAX) {
$includes.="var SUGGEST_TABLE = \"comment_searchsuggest.php\";\r\n".
"var SUGGEST_LOOKUP_TABLE='comment_lookupsuggest.php';\r\n".
"var AUTOCOMPLETE_TABLE=\"comment_autocomplete.php\";\r\n";
}
$includes.="var detect = navigator.userAgent.toLowerCase();

function checkIt(string)
{
	place = detect.indexOf(string) + 1;
	thestring = string;
	return place;
}


function ShowHideControls()
{
	document.getElementById('second_email').style.display =  
		document.forms.editform.elements['asearchopt_email'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_comment').style.display =  
		document.forms.editform.elements['asearchopt_comment'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_access').style.display =  
		document.forms.editform.elements['asearchopt_access'].value==\"Between\" ? '' : 'none'; 
	return false;
}
function ResetControls()
{
	var i;
	e = document.forms[0].elements; 
	for (i=0;i<e.length;i++) 
	{
		if (e[i].name!='type' && e[i].className!='button' && e[i].type!='hidden')
		{
			if(e[i].type=='select-one')
				e[i].selectedIndex=0;
			else if(e[i].type=='select-multiple')
			{
				var j;
				for(j=0;j<e[i].options.length;j++)
					e[i].options[j].selected=false;
			}
			else if(e[i].type=='checkbox' || e[i].type=='radio')
				e[i].checked=false;
			else 
				e[i].value = ''; 
		}
		else if(e[i].name.substr(0,6)=='value_' && e[i].type=='hidden')
			e[i].value = ''; 
	}
	ShowHideControls();	
	return false;
}";

if ($useAJAX) {
$includes.="
$(document).ready(function() {
	document.forms.editform.value_email.onkeyup=function(event) {searchSuggest(event,document.forms.editform.value_email,'advanced')};
	document.forms.editform.value1_email.onkeyup=function(event) {searchSuggest(event,document.forms.editform.value1_email,'advanced1')};
	document.forms.editform.value_email.onkeydown=function(event) {return listenEvent(event,document.forms.editform.value_email,'advanced')};
	document.forms.editform.value1_email.onkeydown=function(event) {return listenEvent(event,document.forms.editform.value1_email,'advanced1')};
});
</script>
<div id=\"search_suggest\"></div>
";
} else {
$includes.="
function OnKeyDown(e)
{ if(!e) e = window.event; 
if (e.keyCode == 13){ e.cancel = true; document.forms[0].submit();} }

</script>";
}

$smarty->assign("includes",$includes);
$smarty->assign("noAJAX",!$useAJAX);

$onload="onLoad=\"javascript: ShowHideControls();\"";
$smarty->assign("onload",$onload);

if(@$_SESSION[$strTableName."_asearchtype"]=="or")
{
	$smarty->assign("any_checked"," checked");
	$smarty->assign("all_checked","");
}
else
{
	$smarty->assign("any_checked","");
	$smarty->assign("all_checked"," checked");
}

$editformats=array();

// email 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["email"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["email"];
	$smarty->assign("value_email",@$_SESSION[$strTableName."_asearchfor"]["email"]);
	$smarty->assign("value1_email",@$_SESSION[$strTableName."_asearchfor2"]["email"]);
}	
if($not)
	$smarty->assign("not_email"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Contains\" ".(($opt=="Contains")?"selected":"").">"."Contains"."</option>";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$options.="<OPTION VALUE=\"Starts with ...\" ".(($opt=="Starts with ...")?"selected":"").">"."Starts with ..."."</option>";
$options.="<OPTION VALUE=\"More than ...\" ".(($opt=="More than ...")?"selected":"").">"."More than ..."."</option>";
$options.="<OPTION VALUE=\"Less than ...\" ".(($opt=="Less than ...")?"selected":"").">"."Less than ..."."</option>";
$options.="<OPTION VALUE=\"Equal or more than ...\" ".(($opt=="Equal or more than ...")?"selected":"").">"."Equal or more than ..."."</option>";
$options.="<OPTION VALUE=\"Equal or less than ...\" ".(($opt=="Equal or less than ...")?"selected":"").">"."Equal or less than ..."."</option>";
$options.="<OPTION VALUE=\"Between\" ".(($opt=="Between")?"selected":"").">"."Between"."</option>";
$options.="<OPTION VALUE=\"Empty\" ".(($opt=="Empty")?"selected":"").">"."Empty"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_email\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_email",$searchtype);
//	edit format
$editformats["email"]="Text field";
// comment 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["comment"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["comment"];
	$smarty->assign("value_comment",@$_SESSION[$strTableName."_asearchfor"]["comment"]);
	$smarty->assign("value1_comment",@$_SESSION[$strTableName."_asearchfor2"]["comment"]);
}	
if($not)
	$smarty->assign("not_comment"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Contains\" ".(($opt=="Contains")?"selected":"").">"."Contains"."</option>";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$options.="<OPTION VALUE=\"Starts with ...\" ".(($opt=="Starts with ...")?"selected":"").">"."Starts with ..."."</option>";
$options.="<OPTION VALUE=\"More than ...\" ".(($opt=="More than ...")?"selected":"").">"."More than ..."."</option>";
$options.="<OPTION VALUE=\"Less than ...\" ".(($opt=="Less than ...")?"selected":"").">"."Less than ..."."</option>";
$options.="<OPTION VALUE=\"Equal or more than ...\" ".(($opt=="Equal or more than ...")?"selected":"").">"."Equal or more than ..."."</option>";
$options.="<OPTION VALUE=\"Equal or less than ...\" ".(($opt=="Equal or less than ...")?"selected":"").">"."Equal or less than ..."."</option>";
$options.="<OPTION VALUE=\"Between\" ".(($opt=="Between")?"selected":"").">"."Between"."</option>";
$options.="<OPTION VALUE=\"Empty\" ".(($opt=="Empty")?"selected":"").">"."Empty"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_comment\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_comment",$searchtype);
//	edit format
$editformats["comment"]=EDIT_FORMAT_TEXT_FIELD;
// access 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["access"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["access"];
	$smarty->assign("value_access",@$_SESSION[$strTableName."_asearchfor"]["access"]);
	$smarty->assign("value1_access",@$_SESSION[$strTableName."_asearchfor2"]["access"]);
}	
if($not)
	$smarty->assign("not_access"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_access\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_access",$searchtype);
//	edit format
$editformats["access"]="Radio button";

$linkdata="";

$linkdata .= "<script type=\"text/javascript\">\r\n";

if ($useAJAX) {
}
else
{
}
$linkdata.="</script>\r\n";

$smarty->assign("linkdata",$linkdata);

$templatefile = "comment_search.htm";
if(function_exists("BeforeShowSearch"))
	BeforeShowSearch($smarty,$templatefile);

$smarty->display($templatefile);

?>