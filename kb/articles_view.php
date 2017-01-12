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

$filename="";	
$message="";

//connect database
$conn = db_connect();

//	Before Process event
if(function_exists("BeforeProcessView"))
	BeforeProcessView($conn);


$keys=array();
$keys["ArticleID"]=postvalue("editid1");

//	get current values and show edit controls

$strWhereClause = KeyWhere($keys);


//	select only owned records
$strWhereClause=whereAdd($strWhereClause,SecuritySQL("Search"));

$strSQL=gSQLWhere($strWhereClause);

$strSQLbak = $strSQL;
if(function_exists("BeforeQueryView"))
	BeforeQueryView($strSQL,$strWhereClause);
if($strSQLbak == $strSQL)
	$strSQL=gSQLWhere($strWhereClause);

LogInfo($strSQL);
$rs=db_query($strSQL,$conn);
$data=db_fetch_array($rs);


include('libs/Smarty.class.php');
$smarty = new Smarty();

	$smarty->assign("show_key1", htmlspecialchars(GetData($data,"ArticleID", "")));

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
		$value=ProcessLargeText(GetDataInt($lookupvalue,$data,"Category", ""),"field=Category".$keylink,"",MODE_VIEW);
	}
	else
		$value="";
	$smarty->assign("show_Category",$value);
////////////////////////////////////////////
//	Title - Custom
	$value="";
		$value = GetData($data,"Title", "Custom");
	$smarty->assign("show_Title",$value);
////////////////////////////////////////////
//	Problem - 
	$value="";
		$value = ProcessLargeText(GetData($data,"Problem", ""),"","",MODE_VIEW);
	$smarty->assign("show_Problem",$value);
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
		$value.=" border=0";
		$value.=" src=\"".htmlspecialchars(AddLinkPrefix("screenp",$thumbname))."\"></a>";
	}
	$smarty->assign("show_screenp",$value);
////////////////////////////////////////////
//	filep - Document Download
	$value="";
		$value = GetData($data,"filep", "Document Download");
	$smarty->assign("show_filep",$value);
////////////////////////////////////////////
//	Solution - 
	$value="";
		$value = ProcessLargeText(GetData($data,"Solution", ""),"","",MODE_VIEW);
	$smarty->assign("show_Solution",$value);
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
		$value.=" border=0";
		$value.=" src=\"".htmlspecialchars(AddLinkPrefix("screens",$thumbname))."\"></a>";
	}
	$smarty->assign("show_screens",$value);
////////////////////////////////////////////
//	Views - 
	$value="";
		$value = ProcessLargeText(GetData($data,"Views", ""),"","",MODE_VIEW);
	$smarty->assign("show_Views",$value);
////////////////////////////////////////////
//	files - Document Download
	$value="";
		$value = GetData($data,"files", "Document Download");
	$smarty->assign("show_files",$value);
////////////////////////////////////////////
//	DateCreated - Short Date
	$value="";
		$value = ProcessLargeText(GetData($data,"DateCreated", "Short Date"),"","",MODE_VIEW);
	$smarty->assign("show_DateCreated",$value);
////////////////////////////////////////////
//	ArticleID - 
	$value="";
		$value = ProcessLargeText(GetData($data,"ArticleID", ""),"","",MODE_VIEW);
	$smarty->assign("show_ArticleID",$value);

$templatefile = "articles_view.htm";
if(function_exists("BeforeShowView"))
	BeforeShowView($smarty,$templatefile,$data);

$smarty->display($templatefile);

?>