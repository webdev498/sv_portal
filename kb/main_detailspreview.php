<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); 
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/main_variables.php");

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
			echo ( $rowcount > 10 ) ? ". Displaying first: <strong>10</strong>.<br /><br />" : "<br /><br />";
	echo "<table cellpadding=1 cellspacing=1 border=0 align=left class=\"detailtable\"><tr>";
	echo "<td><strong>Category</strong></td>";
	echo "<td><strong>Title</strong></td>";
	echo "<td><strong>ArticleID</strong></td>";
	echo "<td><strong>Views</strong></td>";
	echo "<td><strong>approved</strong></td>";
	echo "</tr>";
	while ($data = db_fetch_array($rs)) {
		$recordsCounter++;
					if ( $recordsCounter > 10 ) { break; }
		echo "<tr>";
		$keylink="";
		$keylink.="&key1=".htmlspecialchars(rawurlencode($data["CategoryID"]));

	//	Category - 
		    $value="";
				$value = ProcessLargeText(GetData($data,"Category", ""),"field=Category".$keylink,"",MODE_PRINT);
			echo "<td>".$value."</td>";
	//	Title - 
		    $value="";
				$value = ProcessLargeText(GetData($data,"Title", ""),"field=Title".$keylink,"",MODE_PRINT);
			echo "<td>".$value."</td>";
	//	ArticleID - 
		    $value="";
				$value = ProcessLargeText(GetData($data,"ArticleID", ""),"field=ArticleID".$keylink,"",MODE_PRINT);
			echo "<td>".$value."</td>";
	//	Views - Custom
		    $value="";
				$value = GetData($data,"Views", "Custom");
			echo "<td>".$value."</td>";
	//	approved - Checkbox
		    $value="";
				$value = GetData($data,"approved", "Checkbox");
			echo "<td>".$value."</td>";
		echo "</tr>";
	}
	echo "</table>";
} else {
	echo "Details found".": <strong>".$rowcount."</strong>";
}

echo "counterSeparator".postvalue("counter");
?>