<?php 
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
session_cache_limiter("none");
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/articles_variables.php");

if(!@$_SESSION["UserID"])
{ 
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: login.php?message=expired"); 
	return;
}
if(!CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Export"))
{
	echo "<p>"."You don't have permissions to access this table"."<a href=\"login.php\">"."Back to login page"."</a></p>";
	return;
}

$conn=db_connect();
//	Before Process event
if(function_exists("BeforeProcessExport"))
	BeforeProcessExport($conn);

$strWhereClause="";

$options = "1";
if (@$_REQUEST["a"]!="") 
{
	$options = "";
	
	$sWhere = "1=0";	

//	process selection
	$selected_recs=array();
	if (@$_REQUEST["mdelete"])
	{
		foreach(@$_REQUEST["mdelete"] as $ind)
		{
			$keys=array();
			$keys["ArticleID"]=refine($_REQUEST["mdelete1"][$ind-1]);
			$selected_recs[]=$keys;
		}
	}
	elseif(@$_REQUEST["selection"])
	{
		foreach(@$_REQUEST["selection"] as $keyblock)
		{
			$arr=split("&",refine($keyblock));
			if(count($arr)<1)
				continue;
			$keys=array();
			$keys["ArticleID"]=urldecode($arr[0]);
			$selected_recs[]=$keys;
		}
	}

	foreach($selected_recs as $keys)
	{
		$sWhere = $sWhere . " or ";
		$sWhere.=KeyWhere($keys);
	}


	$strSQL = gSQLWhere($sWhere);
	$strWhereClause=$sWhere;
	
	$_SESSION[$strTableName."_SelectedSQL"] = $strSQL;
	$_SESSION[$strTableName."_SelectedWhere"] = $sWhere;
}

if ($_SESSION[$strTableName."_SelectedSQL"]!="" && @$_REQUEST["records"]=="") 
{
	$strSQL = $_SESSION[$strTableName."_SelectedSQL"];
	$strWhereClause=@$_SESSION[$strTableName."_SelectedWhere"];
}
else
{
	$strWhereClause=@$_SESSION[$strTableName."_where"];
	$strSQL=gSQLWhere($strWhereClause);
}


$mypage=1;
if(@$_REQUEST["type"])
{
//	order by
	$strOrderBy=$_SESSION[$strTableName."_order"];
	if(!$strOrderBy)
		$strOrderBy=$gstrOrderBy;
	$strSQL.=" ".trim($strOrderBy);

	$strSQLbak = $strSQL;
	if(function_exists("BeforeQueryExport"))
		BeforeQueryExport($strSQL,$strWhereClause,$strOrderBy);
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

	$nPageSize=0;
	if(@$_REQUEST["records"]=="page" && $numrows)
	{
		$mypage=(integer)@$_SESSION[$strTableName."_pagenumber"];
		$nPageSize=(integer)@$_SESSION[$strTableName."_pagesize"];
		if($numrows<=($mypage-1)*$nPageSize)
			$mypage=ceil($numrows/$nPageSize);
		if(!$nPageSize)
			$nPageSize=$gPageSize;
		if(!$mypage)
			$mypage=1;

		$strSQL.=" limit ".(($mypage-1)*$nPageSize).",".$nPageSize;
	}
	$rs=db_query($strSQL,$conn);

	if(!ini_get("safe_mode"))
		set_time_limit(300);
	
	if(@$_REQUEST["type"]=="excel")
		ExportToExcel();
	else if(@$_REQUEST["type"]=="word")
		ExportToWord();
	else if(@$_REQUEST["type"]=="xml")
		ExportToXML();
	else if(@$_REQUEST["type"]=="csv")
		ExportToCSV();
	else if(@$_REQUEST["type"]=="pdf")
		ExportToPDF();

	db_close($conn);
	return;
}

header("Expires: Thu, 01 Jan 1970 00:00:01 GMT"); 

include('libs/Smarty.class.php');
$smarty = new Smarty();
$smarty->assign("options",$options);
$smarty->display("articles_export.htm");


function ExportToExcel()
{
	global $cCharset;
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;Filename=articles.xls");

	echo "<html>";
	echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
	
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$cCharset."\">";
	echo "<body>";
	echo "<table border=1>";

	WriteTableData();

	echo "</table>";
	echo "</body>";
	echo "</html>";
}

function ExportToWord()
{
	global $cCharset;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=articles.doc");

	echo "<html>";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$cCharset."\">";
	echo "<body>";
	echo "<table border=1>";

	WriteTableData();

	echo "</table>";
	echo "</body>";
	echo "</html>";
}

function ExportToXML()
{
	global $nPageSize,$rs,$strTableName,$conn;
	header("Content-type: text/xml");
	header("Content-Disposition: attachment;Filename=articles.xml");
	if(!($row=db_fetch_array($rs)))
		return;
	global $cCharset;
	echo "<?xml version=\"1.0\" encoding=\"".$cCharset."\" standalone=\"yes\"?>\r\n";
	echo "<table>\r\n";
	$i=0;
	while((!$nPageSize || $i<$nPageSize) && $row)
	{
		echo "<row>\r\n";
		$field=htmlspecialchars(XMLNameEncode("Category"));
		echo "<".$field.">";
		if(strlen($row["Category"]))
		{
			$strdata = make_db_value("Category",$row["Category"]);
			$LookupSQL="SELECT ";
					$LookupSQL.="`Category`";
			$LookupSQL.=" FROM `categories` WHERE `Category` = " . $strdata;
					LogInfo($LookupSQL);
			$rsLookup = db_query($LookupSQL,$conn);
			$lookupvalue=$row["Category"];
			if($lookuprow=db_fetch_numarray($rsLookup))
				$lookupvalue=$lookuprow[0];
			echo htmlspecialchars(GetDataInt($lookupvalue,$row,"Category", ""));
		}
		echo "</".$field.">\r\n";
		$field=htmlspecialchars(XMLNameEncode("Title"));
		echo "<".$field.">";
		echo htmlspecialchars(GetData($row,"Title",""));
		echo "</".$field.">\r\n";
		$field=htmlspecialchars(XMLNameEncode("Problem"));
		echo "<".$field.">";
		echo htmlspecialchars(GetData($row,"Problem",""));
		echo "</".$field.">\r\n";
		$field=htmlspecialchars(XMLNameEncode("Solution"));
		echo "<".$field.">";
		echo htmlspecialchars(GetData($row,"Solution",""));
		echo "</".$field.">\r\n";
		$field=htmlspecialchars(XMLNameEncode("Views"));
		echo "<".$field.">";
		echo htmlspecialchars(GetData($row,"Views",""));
		echo "</".$field.">\r\n";
		$field=htmlspecialchars(XMLNameEncode("DateCreated"));
		echo "<".$field.">";
		echo htmlspecialchars(GetData($row,"DateCreated",""));
		echo "</".$field.">\r\n";
		echo "</row>\r\n";
		$i++;
		$row=db_fetch_array($rs);
	}
	echo "</table>\r\n";
}

function ExportToCSV()
{
	global $rs,$nPageSize,$strTableName,$conn;
	header("Content-type: application/csv");
	header("Content-Disposition: attachment;Filename=articles.csv");

	if(!($row=db_fetch_array($rs)))
		return;

	$totals=array();

	
// write header
	$outstr="";
	if($outstr!="")
		$outstr.=",";
	$outstr.= "\"Category\"";
	if($outstr!="")
		$outstr.=",";
	$outstr.= "\"Title\"";
	if($outstr!="")
		$outstr.=",";
	$outstr.= "\"Problem\"";
	if($outstr!="")
		$outstr.=",";
	$outstr.= "\"Solution\"";
	if($outstr!="")
		$outstr.=",";
	$outstr.= "\"Views\"";
	if($outstr!="")
		$outstr.=",";
	$outstr.= "\"DateCreated\"";
	echo $outstr;
	echo "\r\n";

// write data rows
	$iNumberOfRows = 0;
	while((!$nPageSize || $iNumberOfRows<$nPageSize) && $row)
	{
		$outstr="";
		if($outstr!="")
			$outstr.=",";
		if(strlen($row["Category"]))
		{
			$strdata = make_db_value("Category",$row["Category"]);
			$LookupSQL="SELECT ";
					$LookupSQL.="`Category`";
			$LookupSQL.=" FROM `categories` WHERE `Category` = " . $strdata;
					LogInfo($LookupSQL);
			$rsLookup = db_query($LookupSQL,$conn);

			$lookupvalue=$row["Category"];
			if($lookuprow=db_fetch_numarray($rsLookup))
				$lookupvalue=$lookuprow[0];
			$outstr.='"'.htmlspecialchars(GetDataInt($lookupvalue,$row,"Category", "")).'"';
		}
		if($outstr!="")
			$outstr.=",";
			$format="Custom";
		$outstr.='"'.htmlspecialchars(GetData($row,"Title",$format)).'"';
		if($outstr!="")
			$outstr.=",";
			$format="";
		$outstr.='"'.htmlspecialchars(GetData($row,"Problem",$format)).'"';
		if($outstr!="")
			$outstr.=",";
			$format="";
		$outstr.='"'.htmlspecialchars(GetData($row,"Solution",$format)).'"';
		if($outstr!="")
			$outstr.=",";
			$format="";
		$outstr.='"'.htmlspecialchars(GetData($row,"Views",$format)).'"';
		if($outstr!="")
			$outstr.=",";
			$format="Short Date";
		$outstr.='"'.htmlspecialchars(GetData($row,"DateCreated",$format)).'"';
		echo $outstr;
		echo "\r\n";
		$iNumberOfRows++;
		$row=db_fetch_array($rs);
	}

//	display totals
	$first=true;

}


function WriteTableData()
{
	global $rs,$nPageSize,$strTableName,$conn;
	if(!($row=db_fetch_array($rs)))
		return;
// write header
	echo "<tr>";
	if($_REQUEST["type"]=="excel")
	{
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Category").'</td>';
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Title").'</td>';
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Problem").'</td>';
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Solution").'</td>';
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Views").'</td>';
		echo '<td style="width: 100" x:str>'.PrepareForExcel("DateCreated").'</td>';
	}
	else
	{
		echo "<td>Category</td>";
		echo "<td>Title</td>";
		echo "<td>Problem</td>";
		echo "<td>Solution</td>";
		echo "<td>Views</td>";
		echo "<td>DateCreated</td>";
	}
	echo "</tr>";

	$totals=array();
// write data rows
	$iNumberOfRows = 0;
	while((!$nPageSize || $iNumberOfRows<$nPageSize) && $row)
	{
		echo "<tr>";
	if($_REQUEST["type"]=="excel")
		echo '<td x:str>';
	else
		echo '<td>';
		if(strlen($row["Category"]))
		{
			$strdata = make_db_value("Category",$row["Category"]);
			$LookupSQL="SELECT ";
					$LookupSQL.="`Category`";
			$LookupSQL.=" FROM `categories` WHERE `Category` = " . $strdata;
					LogInfo($LookupSQL);
			$rsLookup = db_query($LookupSQL,$conn);
			$lookupvalue=$row["Category"];
			if($lookuprow=db_fetch_numarray($rsLookup))
				$lookupvalue=$lookuprow[0];

			$strValue=GetDataInt($lookupvalue,$row,"Category", "");
						if($_REQUEST["type"]=="excel")
				echo PrepareForExcel($strValue);
			else
				echo htmlspecialchars($strValue);

		}
	echo '</td>';
	if($_REQUEST["type"]=="excel")
		echo '<td x:str>';
	else
		echo '<td>';

		$format="Custom";
			echo GetData($row,"Title",$format);
	echo '</td>';
	if($_REQUEST["type"]=="excel")
		echo '<td x:str>';
	else
		echo '<td>';

		$format="";
			if($_REQUEST["type"]=="excel")
			echo PrepareForExcel(GetData($row,"Problem",$format));
		else
			echo htmlspecialchars(GetData($row,"Problem",$format));
	echo '</td>';
	if($_REQUEST["type"]=="excel")
		echo '<td x:str>';
	else
		echo '<td>';

		$format="";
			if($_REQUEST["type"]=="excel")
			echo PrepareForExcel(GetData($row,"Solution",$format));
		else
			echo htmlspecialchars(GetData($row,"Solution",$format));
	echo '</td>';
	echo '<td>';

		$format="";
			echo htmlspecialchars(GetData($row,"Views",$format));
	echo '</td>';
	echo '<td>';

		$format="Short Date";
			if($_REQUEST["type"]=="excel")
			echo PrepareForExcel(GetData($row,"DateCreated",$format));
		else
			echo htmlspecialchars(GetData($row,"DateCreated",$format));
	echo '</td>';
		echo "</tr>";
		$iNumberOfRows++;
		$row=db_fetch_array($rs);
	}

}

function XMLNameEncode($strValue)
{	
	$search=array(" ","#","'","/","\\","(",")",",","[","]","+","\"","-","_","|","}","{","=");
	return str_replace($search,"",$strValue);
}

function PrepareForExcel($str)
{
	$ret = htmlspecialchars($str);
	if (substr($ret,0,1)== "=") 
		$ret = "&#61;".substr($ret,1);
	return $ret;

}




function ExportToPDF()
{
	global $nPageSize,$rs,$strTableName,$conn;
		global $colwidth,$leftmargin;
	if(!($row=db_fetch_array($rs)))
		return;


	include("libs/fpdf.php");

	class PDF extends FPDF
	{
	//Current column
		var $col=0;
	//Ordinate of column start
		var $y0;
		var $maxheight;

	function AcceptPageBreak()
	{
		global $colwidth,$leftmargin;
		if($this->y0+$this->rowheight>$this->PageBreakTrigger)
			return true;
		$x=$leftmargin;
		if($this->maxheight<$this->PageBreakTrigger-$this->y0)
			$this->maxheight=$this->PageBreakTrigger-$this->y0;
		$this->Rect($x,$this->y0,$colwidth["Category"],$this->maxheight);
		$x+=$colwidth["Category"];
		$this->Rect($x,$this->y0,$colwidth["Title"],$this->maxheight);
		$x+=$colwidth["Title"];
		$this->Rect($x,$this->y0,$colwidth["Problem"],$this->maxheight);
		$x+=$colwidth["Problem"];
		$this->Rect($x,$this->y0,$colwidth["Solution"],$this->maxheight);
		$x+=$colwidth["Solution"];
		$this->Rect($x,$this->y0,$colwidth["Views"],$this->maxheight);
		$x+=$colwidth["Views"];
		$this->Rect($x,$this->y0,$colwidth["DateCreated"],$this->maxheight);
		$x+=$colwidth["DateCreated"];
		$this->maxheight = $this->rowheight;
//	draw frame	
		return true;
	}

	function Header()
	{
		global $colwidth,$leftmargin;
	    //Page header
		$this->SetFillColor(192);
		$this->SetX($leftmargin);
		$this->Cell($colwidth["Category"],$this->rowheight,"Category",1,0,'C',1);
		$this->Cell($colwidth["Title"],$this->rowheight,"Title",1,0,'C',1);
		$this->Cell($colwidth["Problem"],$this->rowheight,"Problem",1,0,'C',1);
		$this->Cell($colwidth["Solution"],$this->rowheight,"Solution",1,0,'C',1);
		$this->Cell($colwidth["Views"],$this->rowheight,"Views",1,0,'C',1);
		$this->Cell($colwidth["DateCreated"],$this->rowheight,"DateCreated",1,0,'C',1);
		$this->Ln($this->rowheight);
		$this->y0=$this->GetY();
	}

	}

	$pdf=new PDF();

	$leftmargin=5;
	$pagewidth=200;
	$pageheight=290;
	$rowheight=5;


	$defwidth=$pagewidth/6;
	$colwidth=array();
    $colwidth["Category"]=$defwidth;
    $colwidth["Title"]=$defwidth;
    $colwidth["Problem"]=$defwidth;
    $colwidth["Solution"]=$defwidth;
    $colwidth["Views"]=$defwidth;
    $colwidth["DateCreated"]=$defwidth;
	
	$pdf->AddFont('CourierNewPSMT','','courcp1252.php');
	$pdf->rowheight=$rowheight;
	
	$pdf->SetFont('CourierNewPSMT','',8);
	$pdf->AddPage();
	

	$i=0;
	while((!$nPageSize || $i<$nPageSize) && $row)
	{
		$pdf->maxheight=$rowheight;
		$x=$leftmargin;
		$pdf->SetY($pdf->y0);
		$pdf->SetX($x);
		if(strlen($row["Category"]))
		{
			$strdata = make_db_value("Category",$row["Category"]);
			$LookupSQL="SELECT ";
					$LookupSQL.="`Category`";
			$LookupSQL.=" FROM `categories` WHERE `Category` = " . $strdata;
					LogInfo($LookupSQL);
			$rsLookup = db_query($LookupSQL,$conn);
			$lookupvalue=$row["Category"];
			if($lookuprow=db_fetch_numarray($rsLookup))
				$lookupvalue=$lookuprow[0];
			$pdf->Cell($colwidth["Category"],$rowheight,GetDataInt($lookupvalue,$row,"Category", ""));
		}
		$x+=$colwidth["Category"];
		if($pdf->GetY()-$pdf->y0>$pdf->maxheight)
			$pdf->maxheight=$pdf->GetY()-$pdf->y0;
		$pdf->SetY($pdf->y0);
		$pdf->SetX($x);
		$pdf->MultiCell($colwidth["Title"],$rowheight,GetData($row,"Title","Custom"));
		$x+=$colwidth["Title"];
		if($pdf->GetY()-$pdf->y0>$pdf->maxheight)
			$pdf->maxheight=$pdf->GetY()-$pdf->y0;
		$pdf->SetY($pdf->y0);
		$pdf->SetX($x);
		$pdf->MultiCell($colwidth["Problem"],$rowheight,GetData($row,"Problem",""));
		$x+=$colwidth["Problem"];
		if($pdf->GetY()-$pdf->y0>$pdf->maxheight)
			$pdf->maxheight=$pdf->GetY()-$pdf->y0;
		$pdf->SetY($pdf->y0);
		$pdf->SetX($x);
		$pdf->MultiCell($colwidth["Solution"],$rowheight,GetData($row,"Solution",""));
		$x+=$colwidth["Solution"];
		if($pdf->GetY()-$pdf->y0>$pdf->maxheight)
			$pdf->maxheight=$pdf->GetY()-$pdf->y0;
		$pdf->SetY($pdf->y0);
		$pdf->SetX($x);
		$pdf->MultiCell($colwidth["Views"],$rowheight,GetData($row,"Views",""));
		$x+=$colwidth["Views"];
		if($pdf->GetY()-$pdf->y0>$pdf->maxheight)
			$pdf->maxheight=$pdf->GetY()-$pdf->y0;
		$pdf->SetY($pdf->y0);
		$pdf->SetX($x);
		$pdf->MultiCell($colwidth["DateCreated"],$rowheight,GetData($row,"DateCreated","Short Date"));
		$x+=$colwidth["DateCreated"];
		if($pdf->GetY()-$pdf->y0>$pdf->maxheight)
			$pdf->maxheight=$pdf->GetY()-$pdf->y0;
//	draw fames
		$x=$leftmargin;
		$pdf->Rect($x,$pdf->y0,$colwidth["Category"],$pdf->maxheight);
		$x+=$colwidth["Category"];
		$pdf->Rect($x,$pdf->y0,$colwidth["Title"],$pdf->maxheight);
		$x+=$colwidth["Title"];
		$pdf->Rect($x,$pdf->y0,$colwidth["Problem"],$pdf->maxheight);
		$x+=$colwidth["Problem"];
		$pdf->Rect($x,$pdf->y0,$colwidth["Solution"],$pdf->maxheight);
		$x+=$colwidth["Solution"];
		$pdf->Rect($x,$pdf->y0,$colwidth["Views"],$pdf->maxheight);
		$x+=$colwidth["Views"];
		$pdf->Rect($x,$pdf->y0,$colwidth["DateCreated"],$pdf->maxheight);
		$x+=$colwidth["DateCreated"];
		$pdf->y0+=$pdf->maxheight;
		$i++;
		$row=db_fetch_array($rs);
	}
	$pdf->Output();
}

?>