<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); 
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/comment_variables.php");

if(!@$_SESSION["UserID"])
{ 
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: login.php?message=expired"); 
	return;
}
if(!CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search") && !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Add"))
{
	echo "<p>"."You don't have permissions to access this table"." <a href=\"login.php\">"."Back to login page"."</a></p>";
	return;
}


include('libs/Smarty.class.php');
$smarty = new Smarty();



$conn=db_connect();


//	process reqest data, fill session variables

if(!count($_POST) && !count($_GET))
{
	$sess_unset = array();
	foreach($_SESSION as $key=>$value)
		if(substr($key,0,strlen($strTableName)+1)==$strTableName."_" &&
			strpos(substr($key,strlen($strTableName)+1),"_")===false)
			$sess_unset[] = $key;
	foreach($sess_unset as $key)
		unset($_SESSION[$key]);
}

//	Before Process event
if(function_exists("BeforeProcessList"))
	BeforeProcessList($conn);

if(@$_REQUEST["a"]=="showall")
	$_SESSION[$strTableName."_search"]=0;
else if(@$_REQUEST["a"]=="search")
{
	$_SESSION[$strTableName."_searchfield"]=postvalue("SearchField");
	$_SESSION[$strTableName."_searchoption"]=postvalue("SearchOption");
	$_SESSION[$strTableName."_searchfor"]=postvalue("SearchFor");
	if(postvalue("SearchFor")!="" || postvalue("SearchOption")=='Empty')
		$_SESSION[$strTableName."_search"]=1;
	else
		$_SESSION[$strTableName."_search"]=0;
	$_SESSION[$strTableName."_pagenumber"]=1;
}
else if(@$_REQUEST["a"]=="advsearch")
{
	$_SESSION[$strTableName."_asearchnot"]=array();
	$_SESSION[$strTableName."_asearchopt"]=array();
	$_SESSION[$strTableName."_asearchfor"]=array();
	$_SESSION[$strTableName."_asearchfor2"]=array();
	$tosearch=0;
	$asearchfield = postvalue("asearchfield");
	$_SESSION[$strTableName."_asearchtype"] = postvalue("type");
	if(!$_SESSION[$strTableName."_asearchtype"])
		$_SESSION[$strTableName."_asearchtype"]="and";
	foreach($asearchfield as $field)
	{
		$gfield=GoodFieldName($field);
		$asopt=postvalue("asearchopt_".$gfield);
		$value1=postvalue("value_".$gfield);
		$type=postvalue("type_".$gfield);
		$value2=postvalue("value1_".$gfield);
		$not=postvalue("not_".$gfield);
		if($value1 || $asopt=='Empty')
		{
			$tosearch=1;
			$_SESSION[$strTableName."_asearchopt"][$field]=$asopt;
			if(!is_array($value1))
				$_SESSION[$strTableName."_asearchfor"][$field]=$value1;
			else
				$_SESSION[$strTableName."_asearchfor"][$field]=combinevalues($value1);
			$_SESSION[$strTableName."_asearchfortype"][$field]=$type;
			if($value2)
				$_SESSION[$strTableName."_asearchfor2"][$field]=$value2;
			$_SESSION[$strTableName."_asearchnot"][$field]=($not=="on");
		}
	}
	if($tosearch)
		$_SESSION[$strTableName."_search"]=2;
	else
		$_SESSION[$strTableName."_search"]=0;
	$_SESSION[$strTableName."_pagenumber"]=1;
}

//	process masterkey value
$mastertable=postvalue("mastertable");
if($mastertable!="")
{
	$_SESSION[$strTableName."_mastertable"]=$mastertable;
//	copy keys to session
	$i=1;
	while(isset($_REQUEST["masterkey".$i]))
	{
		$_SESSION[$strTableName."_masterkey".$i]=$_REQUEST["masterkey".$i];
		$i++;
	}
	if(isset($_SESSION[$strTableName."_masterkey".$i]))
		unset($_SESSION[$strTableName."_masterkey".$i]);
//	reset search and page number
	$_SESSION[$strTableName."_search"]=0;
	$_SESSION[$strTableName."_pagenumber"]=1;
}
else
	$mastertable=$_SESSION[$strTableName."_mastertable"];

$smarty->assign("mastertable",$mastertable);
$smarty->assign("mastertable_short",GetTableURL($mastertable));


if(@$_REQUEST["orderby"])
	$_SESSION[$strTableName."_orderby"]=@$_REQUEST["orderby"];

if(@$_REQUEST["pagesize"])
{
	$_SESSION[$strTableName."_pagesize"]=@$_REQUEST["pagesize"];
	$_SESSION[$strTableName."_pagenumber"]=1;
}

if(@$_REQUEST["goto"])
	$_SESSION[$strTableName."_pagenumber"]=@$_REQUEST["goto"];


//	process reqest data - end

$includes="";

if ($useAJAX) {
	$includes.="<script type=\"text/javascript\" src=\"include/jquery.js\"></script>\r\n";
	$includes.="<script type=\"text/javascript\" src=\"include/ajaxsuggest.js\"></script>\r\n";
//	validation stuff
	$editValidateTypes = array();
	$editValidateFields = array();
	$addValidateTypes = array();
	$addValidateFields = array();

	$includes.="<script type=\"text/javascript\" src=\"include/inlineedit.js\"></script>\r\n";
			$editValidateTypes[] = "";
		$editValidateFields[] = "access";
						$validatetype="";
					$validatetype.="IsRequired";
			$editValidateTypes[] = $validatetype;
			$editValidateFields[] = "comment";
										$validatetype="";
					$validatetype.="IsRequired";
			$editValidateTypes[] = $validatetype;
			$editValidateFields[] = "email";
	
						$validatetype="";
					$validatetype.="IsRequired";
			$addValidateTypes[] = $validatetype;
			$addValidateFields[] = "comment";
										$validatetype="";
					$validatetype.="IsRequired";
			$addValidateTypes[] = $validatetype;
			$addValidateFields[] = "email";


		$includes.="<script type=\"text/javascript\">\r\n";
	$includes.="var TEXT_INLINE_FIELD_REQUIRED='".jsreplace("Required field")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_ZIPCODE='".jsreplace("Field should be a valid zipcode")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_EMAIL='".jsreplace("Field should be a valid email address")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_NUMBER='".jsreplace("Field should be a valid number")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_CURRENCY='".jsreplace("Field should be a valid currency")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_PHONE='".jsreplace("Field should be a valid phone number")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_PASSWORD1='".jsreplace("Field can not be 'password'")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_PASSWORD2='".jsreplace("Field should be at least 4 characters long")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_STATE='".jsreplace("Field should be a valid US state name")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_SSN='".jsreplace("Field should be a valid Social Security Number")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_DATE='".jsreplace("Field should be a valid date")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_TIME='".jsreplace("Field should be a valid time in 24-hour format")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_CC='".jsreplace("Field should be a valid credit card number")."';\r\n";
	$includes.="var TEXT_INLINE_FIELD_SSN='".jsreplace("Field should be a valid Social Security Number")."';\r\n";
	$includes.= "</script>\r\n";
		
			$types_separated = implode(",", $editValidateTypes);
		$fields_separated = implode(",", $editValidateFields);
		$includes.="<script type=\"text/javascript\">\r\n";
		$includes.= "var editValidateTypes = String('".$types_separated."').split(',');"."\r\n";
		$includes.= "var editValidateFields = String('".$fields_separated."').split(',');"."\r\n";
		$includes.= "</script>\r\n";
		
			$types_separated = implode(",", $addValidateTypes);
		$fields_separated = implode(",", $addValidateFields);
		$includes.="<script type=\"text/javascript\">\r\n";
		$includes.= "var addValidateTypes = String('".$types_separated."').split(',');"."\r\n";
		$includes.= "var addValidateFields = String('".$fields_separated."').split(',');"."\r\n";
		$includes.= "</script>\r\n";
			

/*	


*/



}
$includes.="<script type=\"text/javascript\" src=\"include/jsfunctions.js\">".
"</script>\n".
"<script type=\"text/javascript\">".
"\nvar bSelected=false;".
"\nvar TEXT_FIRST = \""."First"."\";".
"\nvar TEXT_PREVIOUS = \""."Previous"."\";".
"\nvar TEXT_NEXT = \""."Next"."\";".
"\nvar TEXT_LAST = \""."Last"."\";".
"\nvar TEXT_PLEASE_SELECT='".jsreplace("Please select")."';".
"\nvar TEXT_SAVE='".jsreplace("Save")."';".
"\nvar TEXT_CANCEL='".jsreplace("Cancel")."';".
"\nvar TEXT_INLINE_ERROR='".jsreplace("Error occurred")."';".
"\nvar locale_dateformat = ".$locale_info["LOCALE_IDATE"].";".
"\nvar locale_datedelimiter = \"".$locale_info["LOCALE_SDATE"]."\";".
"\nvar bLoading=false;\r\n";

if ($useAJAX) {
	$includes.="var AUTOCOMPLETE_TABLE='comment_autocomplete.php';\r\n";
	$includes.="var INLINE_EDIT_TABLE='comment_edit.php';\r\n";
	$includes.="var INLINE_ADD_TABLE='comment_add.php';\r\n";
	$includes.="var INLINE_VIEW_TABLE='comment_view.php';\r\n";
	$includes.="var SUGGEST_TABLE='comment_searchsuggest.php';\r\n";
	$includes.="var MASTER_PREVIEW_TABLE='comment_masterpreview.php';\r\n";
	$includes.="var SUGGEST_LOOKUP_TABLE='comment_lookupsuggest.php';";
}
$includes.="\n</script>\n";
if ($useAJAX) {
$includes.="<div id=\"search_suggest\"></div>";
$includes.="<div id=\"master_details\" onmouseover=\"RollDetailsLink.showPopup();\" onmouseout=\"RollDetailsLink.hidePopup();\"></div>";
$includes.="<div id=\"inline_error\"></div>";
}

$smarty->assign("includes",$includes);
$smarty->assign("useAJAX",$useAJAX);


//	process session variables
//	order by
$strOrderBy="";
$order_ind=-1;

$smarty->assign("order_dir_access","a");
$smarty->assign("order_dir_ArticleID","a");
$smarty->assign("order_dir_comment","a");
$smarty->assign("order_dir_email","a");

if(@$_SESSION[$strTableName."_orderby"])
{
	$order_field=substr($_SESSION[$strTableName."_orderby"],1);
	$order_dir=substr($_SESSION[$strTableName."_orderby"],0,1);
	$order_ind=GetFieldIndex($order_field);

	$smarty->assign("order_dir_access","a");
	if($order_field=="access")
	{
		if($order_dir=="a")
		{
			$smarty->assign("order_dir_access","d");
			$img="up";
		}
		else
			$img="down";
		$smarty->assign("order_image_access","<img src=\"images/".$img.".gif\" border=0>");
	}
	$smarty->assign("order_dir_ArticleID","a");
	if($order_field=="ArticleID")
	{
		if($order_dir=="a")
		{
			$smarty->assign("order_dir_ArticleID","d");
			$img="up";
		}
		else
			$img="down";
		$smarty->assign("order_image_ArticleID","<img src=\"images/".$img.".gif\" border=0>");
	}
	$smarty->assign("order_dir_comment","a");
	if($order_field=="comment")
	{
		if($order_dir=="a")
		{
			$smarty->assign("order_dir_comment","d");
			$img="up";
		}
		else
			$img="down";
		$smarty->assign("order_image_comment","<img src=\"images/".$img.".gif\" border=0>");
	}
	$smarty->assign("order_dir_email","a");
	if($order_field=="email")
	{
		if($order_dir=="a")
		{
			$smarty->assign("order_dir_email","d");
			$img="up";
		}
		else
			$img="down";
		$smarty->assign("order_image_email","<img src=\"images/".$img.".gif\" border=0>");
	}

	if($order_ind)
	{
		if($order_dir=="a")
			$strOrderBy="order by ".($order_ind)." asc";
		else 
			$strOrderBy="order by ".($order_ind)." desc";
	}
}
if(!$strOrderBy)
	$strOrderBy=$gstrOrderBy;

//	page number
$mypage=(integer)$_SESSION[$strTableName."_pagenumber"];
if(!$mypage)
	$mypage=1;

//	page size
$PageSize=(integer)$_SESSION[$strTableName."_pagesize"];
if(!$PageSize)
	$PageSize=$gPageSize;

	$smarty->assign("rpp10_selected",($PageSize==10)?"selected":"");
	$smarty->assign("rpp20_selected",($PageSize==20)?"selected":"");
	$smarty->assign("rpp30_selected",($PageSize==30)?"selected":"");
	$smarty->assign("rpp50_selected",($PageSize==50)?"selected":"");
	$smarty->assign("rpp100_selected",($PageSize==100)?"selected":"");
	$smarty->assign("rpp500_selected",($PageSize==500)?"selected":"");

// delete record
$selected_recs=array();
if (@$_REQUEST["mdelete"])
{
	foreach(@$_REQUEST["mdelete"] as $ind)
	{
		$keys=array();
		$keys["CommentID"]=refine($_REQUEST["mdelete1"][$ind-1]);
		$selected_recs[]=$keys;
	}
}
elseif(@$_REQUEST["selection"])
{
	foreach(@$_REQUEST["selection"] as $keyblock)
	{
		$arr=explode("&",refine($keyblock));
		if(count($arr)<1)
			continue;
		$keys=array();
		$keys["CommentID"]=urldecode(@$arr[0]);
		$selected_recs[]=$keys;
	}
}

$records_deleted=0;
foreach($selected_recs as $keys)
{
	$where = KeyWhere($keys);
//	delete only owned records
	$where = whereAdd($where,SecuritySQL("Delete"));

	$strSQL="delete from ".AddTableWrappers($strOriginalTableName)." where ".$where;
	$retval=true;
	if(function_exists("AfterDelete") || function_exists("BeforeDelete"))
	{
		$deletedrs = db_query(gSQLWhere($where),$conn);
		$deleted_values = db_fetch_array($deletedrs);
	}
	if(function_exists("BeforeDelete"))
		$retval = BeforeDelete($where,$deleted_values);
	if($retval)
	{
		$records_deleted++;
				LogInfo($strSQL);
		db_exec($strSQL,$conn);
		if(function_exists("AfterDelete"))
			AfterDelete($where,$deleted_values);
	}
}

if(count($selected_recs))
{
	if(function_exists("AfterMassDelete"))
		AfterMassDelete($records_deleted);
}

//	make sql "select" string

//$strSQL = $gstrSQL;
$strWhereClause="";

//	add search params

if(@$_SESSION[$strTableName."_search"]==1)
//	 regular search
{  
	$strSearchFor=trim($_SESSION[$strTableName."_searchfor"]);
	$strSearchOption=trim($_SESSION[$strTableName."_searchoption"]);
	if(@$_SESSION[$strTableName."_searchfield"])
	{
		$strSearchField = $_SESSION[$strTableName."_searchfield"];
		if($where = StrWhere($strSearchField, $strSearchFor, $strSearchOption, ""))
			$strWhereClause = whereAdd($strWhereClause,$where);
//			$strSQL = AddWhere($strSQL,$where);
		else
			$strWhereClause = whereAdd($strWhereClause,"1=0");
//			$strSQL = AddWhere($strSQL,"1=0");
	}
	else
	{
		$strWhere = "1=0";
		if($where=StrWhere("access", $strSearchFor, $strSearchOption, ""))
			$strWhere .= " or ".$where;
		if($where=StrWhere("comment", $strSearchFor, $strSearchOption, ""))
			$strWhere .= " or ".$where;
		if($where=StrWhere("email", $strSearchFor, $strSearchOption, ""))
			$strWhere .= " or ".$where;
		$strWhereClause = whereAdd($strWhereClause,$strWhere);
//		$strSQL = AddWhere($strSQL,$strWhere);
	}
}
else if(@$_SESSION[$strTableName."_search"]==2)
//	 advanced search
{
	$sWhere="";
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
		$strWhereClause = whereAdd($strWhereClause,$sWhere);
//		$strSQL = AddWhere($strSQL,$sWhere);
	}




if($mastertable=="article")
{
	$where ="";
		$where.= GetFullFieldName("ArticleID")."=".make_db_value("ArticleID",$_SESSION[$strTableName."_masterkey1"]);
	$strWhereClause = whereAdd($strWhereClause,$where);
//	$strSQL = AddWhere($strSQL,$where);
}

$strSQL = gSQLWhere($strWhereClause);

//	order by
$strSQL.=" ".trim($strOrderBy);

//	save SQL for use in "Export" and "Printer-friendly" pages

$_SESSION[$strTableName."_sql"] = $strSQL;
$_SESSION[$strTableName."_where"] = $strWhereClause;
$_SESSION[$strTableName."_order"] = $strOrderBy;

//	select and display records
if(CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search"))
{
	$strSQLbak = $strSQL;
	if(function_exists("BeforeQueryList"))
		BeforeQueryList($strSQL,$strWhereClause,$strOrderBy);
//	Rebuild SQL if needed
	if($strSQL!=$strSQLbak)
	{
//	changed $strSQL - old style	
		$numrows=GetRowCount($strSQL);
	}
	else
	{
		$strSQL = gSQLWhere($strWhereClause);
		$strSQL.=" ".trim($strOrderBy);
		$numrows=gSQLRowCount($strWhereClause);
	}
	LogInfo($strSQL);

//	 Pagination:
	if(!$numrows)
	{
		$smarty->assign("rowsfound",false);
		$message="No records found";
				$message = "<span name=\"notfound_message\">".$message."</span>";
		$smarty->assign("message",$message);
	}
	else
	{
		$smarty->assign("rowsfound",true);
		$smarty->assign("records_found",$numrows);
		$maxRecords = $numrows;
		$maxpages=ceil($maxRecords/$PageSize);
		if($mypage > $maxpages)
			$mypage = $maxpages;
		if($mypage<1) 
			$mypage=1;
		$maxrecs=$PageSize;
		$smarty->assign("page",$mypage);
		$smarty->assign("maxpages",$maxpages);

//	write pagination
$smarty->assign("pagination","<script language=\"JavaScript\">WritePagination(".$mypage.",".$maxpages.");
		function GotoPage(nPageNumber)
		{
			window.location='comment_list.php?goto='+nPageNumber;
		}
</script>");
		
		$strSQL.=" limit ".(($mypage-1)*$PageSize).",".$PageSize;
	}
	$rs=db_query($strSQL,$conn);

//	hide colunm headers if needed
	$recordsonpage=$numrows-($mypage-1)*$PageSize;
	if($recordsonpage>$PageSize)
	$recordsonpage=$PageSize;
	if($recordsonpage>=1)
		$smarty->assign("column1show",true);
	else
		$smarty->assign("column1show",false);
	$smarty->assign("column1show",true);


//	fill $rowinfo array
	$rowinfo = array();
	$shade=false;
	$recno=1;
	$editlink="";
	$copylink="";

	while($data=db_fetch_array($rs))
	{
		if(function_exists("BeforeProcessRowList"))
		{
			if(!BeforeProcessRowList($data))
				continue;
		}
		break;
	}

	while($data && $recno<=$PageSize)
	{
		$row=array();
		if(!$shade)
		{
			$row["shadeclass"]='class="shade"';
			$row["shadeclassname"]="shade";
			$shade=true;
		}
		else
		{
			$row["shadeclass"]="";
			$row["shadeclassname"]="";
			$shade=false;
		}
		for($col=1;$data && $recno<=$PageSize && $col<=1;$col++)
		{


			$row[$col."editable"]=CheckSecurity($data["access"],"Edit");
//	key fields
			$keyblock="";
			$row[$col."id1"]=htmlspecialchars($data["CommentID"]);
			$keyblock.= rawurlencode($data["CommentID"]);
			$row[$col."keyblock"]=htmlspecialchars($keyblock);
			$row[$col."recno"] = $recno;
//	detail tables
//	edit page link
			$editlink="";
			$editlink.="editid1=".htmlspecialchars(rawurlencode($data["CommentID"]));
			$row[$col."editlink"]=$editlink;

			$copylink="";
			$copylink.="copyid1=".htmlspecialchars(rawurlencode($data["CommentID"]));
			$row[$col."copylink"]=$copylink;
			$keylink="";
			$keylink.="&key1=".htmlspecialchars(rawurlencode($data["CommentID"]));


//	email - 
			$value="";
				$value = ProcessLargeText(GetData($data,"email", ""),"field=email".$keylink,"",MODE_LIST);
			$row[$col."email_value"]=$value;

//	comment - 
			$value="";
				$value = ProcessLargeText(GetData($data,"comment", ""),"field=comment".$keylink,"",MODE_LIST);
			$row[$col."comment_value"]=$value;

//	ArticleID - 
			$value="";
				$value = ProcessLargeText(GetData($data,"ArticleID", ""),"field=ArticleID".$keylink,"",MODE_LIST);
			$row[$col."ArticleID_value"]=$value;

//	access - 
			$value="";
				$value = ProcessLargeText(GetData($data,"access", ""),"field=access".$keylink,"",MODE_LIST);
			$row[$col."access_value"]=$value;
			$row[$col."show"]=true;
			if(function_exists("BeforeMoveNextList"))
				BeforeMoveNextList($data,$row,$col);
			$span="<span ";
			$span.="id=\"edit".$recno."_email\" ";
					$span.=">";
			$row[$col."email_value"] = $span.$row[$col."email_value"]."</span>";
			$span="<span ";
			$span.="id=\"edit".$recno."_comment\" ";
					$span.=">";
			$row[$col."comment_value"] = $span.$row[$col."comment_value"]."</span>";
			$span="<span ";
			$span.="id=\"edit".$recno."_ArticleID\" ";
					$span.=">";
			$row[$col."ArticleID_value"] = $span.$row[$col."ArticleID_value"]."</span>";
			$span="<span ";
			$span.="id=\"edit".$recno."_access\" ";
					$span.=">";
			$row[$col."access_value"] = $span.$row[$col."access_value"]."</span>";
				
			while($data=db_fetch_array($rs))
			{
				if(function_exists("BeforeProcessRowList"))
				{
					if(!BeforeProcessRowList($data))
						continue;
				}
				break;
			}
			$recno++;
			
		}
		$rowinfo[]=$row;
	}
	$smarty->assign("rowinfo",$rowinfo);

}


if(CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search"))
{
	if($_SESSION[$strTableName."_search"]==1)
	{
		$onload = "onLoad=\"if(document.getElementById('SearchFor')) document.getElementById('ctlSearchFor').focus();\"";
		$smarty->assign("onload",$onload);
//	fill in search variables
	//	field selection
		if(@$_SESSION[$strTableName."_searchfield"]=="access")
			$smarty->assign("search_access","selected");
		if(@$_SESSION[$strTableName."_searchfield"]=="comment")
			$smarty->assign("search_comment","selected");
		if(@$_SESSION[$strTableName."_searchfield"]=="email")
			$smarty->assign("search_email","selected");
	// search type selection
		if(@$_SESSION[$strTableName."_searchoption"]=="Contains")
			$smarty->assign("search_contains_option_selected","selected");		
		if(@$_SESSION[$strTableName."_searchoption"]=="Equals")
			$smarty->assign("search_equals_option_selected","selected");		
		if(@$_SESSION[$strTableName."_searchoption"]=="Starts with ...")
			$smarty->assign("search_startswith_option_selected","selected");		
		if(@$_SESSION[$strTableName."_searchoption"]=="More than ...")
			$smarty->assign("search_more_option_selected","selected");		
		if(@$_SESSION[$strTableName."_searchoption"]=="Less than ...")
			$smarty->assign("search_less_option_selected","selected");		
		if(@$_SESSION[$strTableName."_searchoption"]=="Equal or more than ...")
			$smarty->assign("search_equalormore_option_selected","selected");		
		if(@$_SESSION[$strTableName."_searchoption"]=="Equal or less than ...")
			$smarty->assign("search_equalorless_option_selected","selected");		
		if(@$_SESSION[$strTableName."_searchoption"]=="Empty")
			$smarty->assign("search_empty_option_selected","selected");		

		$smarty->assign("search_searchfor","value=\"".htmlspecialchars(@$_SESSION[$strTableName."_searchfor"])."\"");
	}
}

$smarty->assign("userid",htmlspecialchars($_SESSION["UserID"]));


//	table selector
$strPerm = GetUserPermissions("main");
$smarty->assign("allow_main",!(strpos($strPerm, "A")===false && strpos($strPerm, "S")===false));

$smarty->assign("displayheader","<script language=\"JavaScript\">DisplayHeader();</script>");

	$smarty->assign("allow_delete",CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Delete"));
	$smarty->assign("allow_add",CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Add"));
	$smarty->assign("allow_edit",CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Edit"));
	$smarty->assign("allow_export",CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Export"));
	$smarty->assign("allow_search",CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search"));
	$smarty->assign("allow_deleteorexport",CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Delete") || CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Export") );


//	display master table info
$masterkeys=array();
$smarty->assign("showmasterfile","empty.htm");
if($mastertable=="article")
{
//	include proper masterlist.php code
	include("include/article_masterlist.php");
	$masterkeys[]=@$_SESSION[$strTableName."_masterkey1"];
	DisplayMasterTableInfo("comment", $masterkeys);
	$smarty->assign("showmasterfile","article_masterlist.htm");
}

$linkdata="";

if ($useAJAX) {
	$linkdata .= "<script type=\"text/javascript\">\r\n";
	$linkdata .= "

function SetDate(fldName, recordID)
{
	if ( $('select#month'+fldName+'_'+recordID).get(0).value!='' && $('select#day'+fldName+'_'+recordID).get(0).value!='' && $('select#year'+fldName+'_'+recordID).get(0).value!='') {
		$('input#'+fldName+'_'+recordID).get(0).value = '' + 
			$('select#year'+fldName+'_'+recordID).get(0).value + '-' + 
			$('select#month'+fldName+'_'+recordID).get(0).value + '-' + 
			$('select#day'+fldName+'_'+recordID).get(0).value;
		if ( $('input#ts'+fldName+'_'+recordID)[0] )
			$('input#ts'+fldName+'_'+recordID).get(0).value = '' + 
				$('select#day'+fldName+'_'+recordID).get(0).value + '-' + 
				$('select#month'+fldName+'_'+recordID).get(0).value + '-' + 
				$('select#year'+fldName+'_'+recordID).get(0).value;
	} else {
		if ( $('input#ts'+fldName+'_'+recordID)[0] )
			$('input#ts'+fldName+'_'+recordID).get(0).value= '10-6-2007';
		if ( $('input#'+fldName+'_'+recordID)[0] )
			$('input#'+fldName+'_'+recordID).get(0).value= '';
	}
}


function update(fldName, recordID, newDate, showTime)
{
	var dt_datetime;
	var curdate = new Date();
	dt_datetime = newDate;
	
	if ( $('select#day'+fldName+'_'+recordID)[0] ) {
		$('input#'+fldName+'_'+recordID).get(0).value = dt_datetime.getFullYear() + '-' 
			+ (dt_datetime.getMonth()+1) + '-' + dt_datetime.getDate();
		$('select#day'+fldName+'_'+recordID).get(0).selectedIndex = dt_datetime.getDate();
		$('select#month'+fldName+'_'+recordID).get(0).selectedIndex = dt_datetime.getMonth() + 1;
		for ( i=0; i<$('select#year'+fldName+'_'+recordID).get(0).options.length; i++ ) {
			if ( $('select#year'+fldName+'_'+recordID).get(0).options[i].value == dt_datetime.getFullYear() ) { 
				$('select#year'+fldName+'_'+recordID).get(0).selectedIndex = i; 
				break; 
			} 
			$('input#ts'+fldName+'_'+recordID).get(0).value = dt_datetime.getDate() + '-' + 
				( dt_datetime.getMonth() + 1 ) + '-' + dt_datetime.getFullYear();
		}
	} else {
		$('input#'+fldName+'_'+ recordID).get(0).value = print_datetime(newDate,".$locale_info["LOCALE_IDATE"].",showTime);
		$('input#ts'+fldName+'_'+ recordID).get(0).value = print_datetime(newDate,-1,showTime);
	}
}";
	$linkdata.="\$(\".addarea\").each(function(i) { \$(this).hide();});\r\n";
	$linkdata.="var newrecord_id=".($recno+1).";\r\n";
	$linkdata.="var newrecord_tempid=0;\r\n";
	if(!$numrows)
	{
		$linkdata .= "$('[@name=record_controls]').hide();
			$('[@name=maintable]').hide();";
	}
	$linkdata .= "</script>\r\n";

$linkdata .= "<style>
#inline_error {
	font-family: Verdana, Arial, Helvetica, sans serif;
	font-size: 11px;
	position: absolute;
	background-color: white;
	border: 1px solid red;
	padding: 10px;
	background-repeat: no-repeat;
	display: none;
	}
</style>";
}

if ($useAJAX) {
$linkdata.="<script>
if(!$('[@disptype=control1]').length && $('[@disptype=controltable1]').length)
	$('[@disptype=controltable1]').hide();
</script>";
}
$smarty->assign("linkdata",$linkdata);

$strSQL=$_SESSION[$strTableName."_sql"];
$smarty->assign("guest",$_SESSION["AccessLevel"] == ACCESS_LEVEL_GUEST);

$templatefile = "comment_list.htm";
if(function_exists("BeforeShowList"))
	BeforeShowList($smarty,$templatefile);

$smarty->display($templatefile);

