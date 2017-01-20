<?php 
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/articles_variables.php");

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
$includes.="var SUGGEST_TABLE = \"articles_searchsuggest.php\";\r\n".
"var SUGGEST_LOOKUP_TABLE='articles_lookupsuggest.php';\r\n".
"var AUTOCOMPLETE_TABLE=\"articles_autocomplete.php\";\r\n";
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
	document.getElementById('second_Category').style.display =  
		document.forms.editform.elements['asearchopt_Category'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_Title').style.display =  
		document.forms.editform.elements['asearchopt_Title'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_Problem').style.display =  
		document.forms.editform.elements['asearchopt_Problem'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_screenp').style.display =  
		document.forms.editform.elements['asearchopt_screenp'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_filep').style.display =  
		document.forms.editform.elements['asearchopt_filep'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_Solution').style.display =  
		document.forms.editform.elements['asearchopt_Solution'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_screens').style.display =  
		document.forms.editform.elements['asearchopt_screens'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_files').style.display =  
		document.forms.editform.elements['asearchopt_files'].value==\"Between\" ? '' : 'none'; 
	document.getElementById('second_DateCreated').style.display =  
		document.forms.editform.elements['asearchopt_DateCreated'].value==\"Between\" ? '' : 'none'; 
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
	document.forms.editform.value_Title.onkeyup=function(event) {searchSuggest(event,document.forms.editform.value_Title,'advanced')};
	document.forms.editform.value1_Title.onkeyup=function(event) {searchSuggest(event,document.forms.editform.value1_Title,'advanced1')};
	document.forms.editform.value_Title.onkeydown=function(event) {return listenEvent(event,document.forms.editform.value_Title,'advanced')};
	document.forms.editform.value1_Title.onkeydown=function(event) {return listenEvent(event,document.forms.editform.value1_Title,'advanced1')};
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

// Category 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["Category"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["Category"];
	$smarty->assign("value_Category",@$_SESSION[$strTableName."_asearchfor"]["Category"]);
	$smarty->assign("value1_Category",@$_SESSION[$strTableName."_asearchfor2"]["Category"]);
}	
if($not)
	$smarty->assign("not_Category"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_Category\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_Category",$searchtype);
//	edit format
$editformats["Category"]="Lookup wizard";
// Title 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["Title"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["Title"];
	$smarty->assign("value_Title",@$_SESSION[$strTableName."_asearchfor"]["Title"]);
	$smarty->assign("value1_Title",@$_SESSION[$strTableName."_asearchfor2"]["Title"]);
}	
if($not)
	$smarty->assign("not_Title"," checked");
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
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_Title\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_Title",$searchtype);
//	edit format
$editformats["Title"]="Text field";
// Problem 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["Problem"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["Problem"];
	$smarty->assign("value_Problem",@$_SESSION[$strTableName."_asearchfor"]["Problem"]);
	$smarty->assign("value1_Problem",@$_SESSION[$strTableName."_asearchfor2"]["Problem"]);
}	
if($not)
	$smarty->assign("not_Problem"," checked");
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
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_Problem\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_Problem",$searchtype);
//	edit format
$editformats["Problem"]=EDIT_FORMAT_TEXT_FIELD;
// screenp 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["screenp"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["screenp"];
	$smarty->assign("value_screenp",@$_SESSION[$strTableName."_asearchfor"]["screenp"]);
	$smarty->assign("value1_screenp",@$_SESSION[$strTableName."_asearchfor2"]["screenp"]);
}	
if($not)
	$smarty->assign("not_screenp"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_screenp\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_screenp",$searchtype);
//	edit format
$editformats["screenp"]=EDIT_FORMAT_TEXT_FIELD;
// filep 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["filep"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["filep"];
	$smarty->assign("value_filep",@$_SESSION[$strTableName."_asearchfor"]["filep"]);
	$smarty->assign("value1_filep",@$_SESSION[$strTableName."_asearchfor2"]["filep"]);
}	
if($not)
	$smarty->assign("not_filep"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_filep\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_filep",$searchtype);
//	edit format
$editformats["filep"]=EDIT_FORMAT_TEXT_FIELD;
// Solution 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["Solution"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["Solution"];
	$smarty->assign("value_Solution",@$_SESSION[$strTableName."_asearchfor"]["Solution"]);
	$smarty->assign("value1_Solution",@$_SESSION[$strTableName."_asearchfor2"]["Solution"]);
}	
if($not)
	$smarty->assign("not_Solution"," checked");
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
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_Solution\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_Solution",$searchtype);
//	edit format
$editformats["Solution"]=EDIT_FORMAT_TEXT_FIELD;
// screens 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["screens"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["screens"];
	$smarty->assign("value_screens",@$_SESSION[$strTableName."_asearchfor"]["screens"]);
	$smarty->assign("value1_screens",@$_SESSION[$strTableName."_asearchfor2"]["screens"]);
}	
if($not)
	$smarty->assign("not_screens"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_screens\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_screens",$searchtype);
//	edit format
$editformats["screens"]=EDIT_FORMAT_TEXT_FIELD;
// files 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["files"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["files"];
	$smarty->assign("value_files",@$_SESSION[$strTableName."_asearchfor"]["files"]);
	$smarty->assign("value1_files",@$_SESSION[$strTableName."_asearchfor2"]["files"]);
}	
if($not)
	$smarty->assign("not_files"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_files\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_files",$searchtype);
//	edit format
$editformats["files"]=EDIT_FORMAT_TEXT_FIELD;
// DateCreated 
$opt="";
$not=false;
if(@$_SESSION[$strTableName."_search"]==2)
{
	$opt=@$_SESSION[$strTableName."_asearchopt"]["DateCreated"];
	$not=@$_SESSION[$strTableName."_asearchnot"]["DateCreated"];
	$smarty->assign("value_DateCreated",@$_SESSION[$strTableName."_asearchfor"]["DateCreated"]);
	$smarty->assign("value1_DateCreated",@$_SESSION[$strTableName."_asearchfor2"]["DateCreated"]);
}	
if($not)
	$smarty->assign("not_DateCreated"," checked");
//	write search options
$options="";
$options.="<OPTION VALUE=\"Equals\" ".(($opt=="Equals")?"selected":"").">"."Equals"."</option>";
$options.="<OPTION VALUE=\"More than ...\" ".(($opt=="More than ...")?"selected":"").">"."More than ..."."</option>";
$options.="<OPTION VALUE=\"Less than ...\" ".(($opt=="Less than ...")?"selected":"").">"."Less than ..."."</option>";
$options.="<OPTION VALUE=\"Equal or more than ...\" ".(($opt=="Equal or more than ...")?"selected":"").">"."Equal or more than ..."."</option>";
$options.="<OPTION VALUE=\"Equal or less than ...\" ".(($opt=="Equal or less than ...")?"selected":"").">"."Equal or less than ..."."</option>";
$options.="<OPTION VALUE=\"Between\" ".(($opt=="Between")?"selected":"").">"."Between"."</option>";
$options.="<OPTION VALUE=\"Empty\" ".(($opt=="Empty")?"selected":"").">"."Empty"."</option>";
$searchtype = "<SELECT ID=\"SearchOption\" NAME=\"asearchopt_DateCreated\" SIZE=1 onChange=\"return ShowHideControls();\">";
$searchtype .= $options;
$searchtype .= "</SELECT>";
$smarty->assign("searchtype_DateCreated",$searchtype);
//	edit format
$editformats["DateCreated"]="Date";

$linkdata="";

$linkdata .= "<script type=\"text/javascript\">\r\n";

if ($useAJAX) {
}
else
{
}
$linkdata.="</script>\r\n";

$smarty->assign("linkdata",$linkdata);

$templatefile = "articles_search.htm";
if(function_exists("BeforeShowSearch"))
	BeforeShowSearch($smarty,$templatefile);

$smarty->display($templatefile);

?>