<?php
include("include/articles_settings.php");

function DisplayMasterTableInfo($detailtable, $keys)
{
	global $conn,$strTableName,$smarty;
	
	$oldTableName=$strTableName;
	$strTableName="articles";

//$strSQL = "select `approved`,   `ArticleID`,   `Category`,   `DateCreated`,   `Problem`,   `Solution`,   `Title`,   `Views`,   `screenp`,   `screens`,   `filep`,   `files`  From `articles`";

$sqlHead="select `approved`,   `ArticleID`,   `Category`,   `DateCreated`,   `Problem`,   `Solution`,   `Title`,   `Views`,   `screenp`,   `screens`,   `filep`,   `files`  ";
$sqlFrom="From `articles`";
$sqlWhere="";
$sqlTail="";

$where="";

if($detailtable=="comments")
{
		$where.= GetFullFieldName("ArticleID")."=".make_db_value("ArticleID",$keys[1-1]);
}
if(!$where)
{
	$strTableName=$oldTableName;
	return;
}
	$str = SecuritySQL("Export");
	if(strlen($str))
		$where.=" and ".$str;
	
	$strWhere=whereAdd($sqlWhere,$where);
	if(strlen($strWhere))
		$strWhere=" where ".$strWhere." ";
	$strSQL= $sqlHead.$sqlFrom.$strWhere.$sqlTail;

//	$strSQL=AddWhere($strSQL,$where);

	LogInfo($strSQL);
	$rs=db_query($strSQL,$conn);
	$data=db_fetch_array($rs);
	$keylink="";
	$keylink.="&key1=".htmlspecialchars(rawurlencode($data["ArticleID"]));
	

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
				$value=ProcessLargeText(GetDataInt($lookupvalue,$data,"Category", ""),"field=Category".$keylink,"",MODE_PRINT);
			}
			$smarty->assign("showmaster_Category",$value);

//	Title - Custom
			$value="";
				$value = GetData($data,"Title", "Custom");
			$smarty->assign("showmaster_Title",$value);

//	Problem - 
			$value="";
				$value = ProcessLargeText(GetData($data,"Problem", ""),"field=Problem".$keylink,"",MODE_PRINT);
			$smarty->assign("showmaster_Problem",$value);

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
			$smarty->assign("showmaster_screenp",$value);

//	filep - Document Download
			$value="";
				$value = GetData($data,"filep", "Document Download");
			$smarty->assign("showmaster_filep",$value);

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
			$smarty->assign("showmaster_screens",$value);

//	files - Document Download
			$value="";
				$value = GetData($data,"files", "Document Download");
			$smarty->assign("showmaster_files",$value);

//	Views - 
			$value="";
				$value = ProcessLargeText(GetData($data,"Views", ""),"field=Views".$keylink,"",MODE_PRINT);
			$smarty->assign("showmaster_Views",$value);

//	approved - Checkbox
			$value="";
				$value = GetData($data,"approved", "Checkbox");
			$smarty->assign("showmaster_approved",$value);
	$strTableName=$oldTableName;
}

// events

?>