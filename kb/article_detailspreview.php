<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); 
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/article_variables.php");

if(!@$_SESSION["UserID"])
{ 
	return;
}
if(!CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search"))
{
	return;
}

$conn=db_connect(); 
$recordsCounter = 0;


//$strSQL = $gstrSQL;



$str = SecuritySQL("Search");
if(strlen($str))
	$where.=" and ".$str;
$strSQL = gSQLWhere($where);
//$strSQL = AddWhere($strSQL,$where);

$strSQL.=" ".$gstrOrderBy;

$rowcount=gSQLRowCount($where);


if ( $rowcount ) {
	$rs=db_query($strSQL,$conn);
	echo "Details found".": <strong>".$rowcount."</strong>";
			echo ( $rowcount > 5 ) ? ". Displaying first: <strong>5</strong>.<br /><br />" : "<br /><br />";
	echo "<table cellpadding=1 cellspacing=1 border=0 align=left class=\"detailtable\"><tr>";
	echo "<td><strong>Category</strong></td>";
	echo "<td><strong>Title</strong></td>";
	echo "<td><strong>Problem</strong></td>";
	echo "<td><strong>screenp</strong></td>";
	echo "<td><strong>filep</strong></td>";
	echo "<td><strong>screens</strong></td>";
	echo "<td><strong>files</strong></td>";
	echo "<td><strong>Views</strong></td>";
	echo "</tr>";
	while ($data = db_fetch_array($rs)) {
		$recordsCounter++;
					if ( $recordsCounter > 5 ) { break; }
		echo "<tr>";
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
			else
				$value="";
			echo "<td>".$value."</td>";
	//	Title - 
		    $value="";
				$value = ProcessLargeText(GetData($data,"Title", ""),"field=Title".$keylink,"",MODE_PRINT);
			echo "<td>".$value."</td>";
	//	Problem - 
		    $value="";
				$value = ProcessLargeText(GetData($data,"Problem", ""),"field=Problem".$keylink,"",MODE_PRINT);
			echo "<td>".$value."</td>";
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
							$value.=" width=100";
							$value.=" height=100";
			}
			$value.=" border=0";
			$value.=" src=\"".htmlspecialchars(AddLinkPrefix("screenp",$thumbname))."\"></a>";
		}
			echo "<td>".$value."</td>";
	//	filep - Document Download
		    $value="";
				$value = GetData($data,"filep", "Document Download");
			echo "<td>".$value."</td>";
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
							$value.=" width=100";
							$value.=" height=100";
			}
			$value.=" border=0";
			$value.=" src=\"".htmlspecialchars(AddLinkPrefix("screens",$thumbname))."\"></a>";
		}
			echo "<td>".$value."</td>";
	//	files - Document Download
		    $value="";
				$value = GetData($data,"files", "Document Download");
			echo "<td>".$value."</td>";
	//	Views - 
		    $value="";
				$value = ProcessLargeText(GetData($data,"Views", ""),"field=Views".$keylink,"",MODE_PRINT);
			echo "<td>".$value."</td>";
		echo "</tr>";
	}
	echo "</table>";
} else {
	echo "Details found".": <strong>".$rowcount."</strong>";
}

echo "counterSeparator".postvalue("counter");
?>