<?php 

////////////////////////////////////////////////////////////////////////////////
// table and field info functions
////////////////////////////////////////////////////////////////////////////////


function GetTableData($table,$key,$default)
{
	global $strTableName,$tables_data;
	if(!$table) 
		$table = $strTableName;
	if(!array_key_exists($table,$tables_data))
		return $default;
	if(!array_key_exists($key,$tables_data[$table]))
		return $default;
	return $tables_data[$table][$key];
}

function GetFieldData($table,$field,$key,$default)
{
	global $strTableName,$tables_data;
	if(!$table) 
		$table = $strTableName;
	if(!array_key_exists($table,$tables_data))
		return $default;
	if(!array_key_exists($field,$tables_data[$table]))
		return $default;
	if(!array_key_exists($key,$tables_data[$table][$field]))
		return $default;
	return $tables_data[$table][$field][$key];
}


// return field label
function Label($field,$table="")
{
	return GetFieldData($table,$field,"Label",$field);
}

// return filename field if any
function GetFilenameField($field,$table="")
{
	return GetFieldData($table,$field,"Filename","");
}

//	return hyperlink prefix
function GetLinkPrefix($field,$table="")
{
	return GetFieldData($table,$field,"LinkPrefix","");
}

//	return database field type
//	using ADO DataTypeEnum constants
//	the full list available at:
//	http://msdn.microsoft.com/library/default.asp?url=/library/en-us/ado270/htm/mdcstdatatypeenum.asp
function GetFieldType($field,$table="")
{
	return GetFieldData($table,$field,"FieldType","");
}

//	return Edit format
function GetEditFormat($field,$table="")
{
	return GetFieldData($table,$field,"EditFormat","");
}

//	return View format
function Format($field,$table="")
{
	return GetFieldData($table,$field,"ViewFormat","");
}

//	show time in datepicker or not
function DateEditShowTime($field,$table="")
{
	return GetFieldData($table,$field,"ShowTime",false);
}

//	use FastType Lookup wizard or not
function FastType($field,$table="")
{
	return GetFieldData($table,$field,"FastType",false);
}

//	is Lookup wizard dependent or not
function UseCategory($field,$table="")
{
	return GetFieldData($table,$field,"UseCategory",false);
}

//	Category Control field for dependent dropdowns
function CategoryControl($field,$table="")
{
	return GetFieldData($table,$field,"CategoryControl","");
}

//	create Thumbnail or not
function GetCreateThumbnail($field,$table="")
{
	return GetFieldData($table,$field,"CreateThumbnail",false);
}

//	return Thumbnail prefix
function GetThumbnailPrefix($field,$table="")
{
	return GetFieldData($table,$field,"ThumbnailPrefix","");
}


//	return field name
function GetFieldByGoodFieldName($field,$table="")
{
	global $strTableName,$tables_data;
	if(!$table)
		$table=$strTableName;
	if(!array_key_exists($table,$tables_data))
		return "";

	foreach($tables_data[$table] as $key=>$value)
	{
		if(count($value)>1 && $value["GoodName"]==$field)
			return $key;
	}
	return "";
}

//	return the full database field original name
function GetFullFieldName($field,$table="")
{
	return GetFieldData($table,$field,"FullName",$field);
}

//     return height of text area
function GetNRows($field,$table="")
{
	return GetFieldData($table,$field,"nRows",$field);
}

//     return width of text area
function GetNCols($field,$table="")
{
	return GetFieldData($table,$field,"nCols",$field);
}


//	return number of chars to show before More... link
function GetNumberOfChars($table="")
{
	return GetTableData($table,".NumberOfChars",0);
}

//	return table short name
function GetTableURL($table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
	if("comments"==$table) 
		return "comments";
	if("main"==$table) 
		return "main";
	if("articles"==$table) 
		return "articles";
	if("article"==$table) 
		return "article";
	if("users"==$table) 
		return "users";
	if("comment"==$table) 
		return "comment";
	if("register"==$table) 
		return "register";
	if("_register"==$table) 
		return "_register";
}

//	return table Owner ID field
function GetTableOwnerID($table="")
{
	return GetTableData($table,".OwnerID",0);
}

//	is field marked as required
function IsRequired($field,$table="")
{
	return GetFieldData($table,$field,"IsRequired",false);
}

//	use Rich Text Editor or not
function UseRTE($field,$table="")
{
	return GetFieldData($table,$field,"UseRTE",false);
}

//	add timestamp to filename when uploading files or not
function UseTimestamp($field,$table="")
{
	return GetFieldData($table,$field,"UseTimestamp",false);
}

function GetUploadFolder($field, $table="")
{
	$path = GetFieldData($table,$field,"UploadFolder","");
	if(strlen($path) && substr($path,strlen($path)-1) != "/")
		$path.="/";
	return $path;
}

function GetFieldIndex($field, $table="")
{
	return GetFieldData($table,$field,"Index",0);
}

//	return Date field edit type
function DateEditType($field,$table="")
{
	return GetFieldData($table,$field,"DateEditType",0);
}

// returns text edit parameters
function GetEditParams($field, $table="")
{
	return GetFieldData($table,$field,"EditParams","");
}

// returns Chart type
function GetChartType($shorttable)
{
	return "";
}

////////////////////////////////////////////////////////////////////////////////
// data output functions
////////////////////////////////////////////////////////////////////////////////

//	format field value for output
function GetData($data,$field, $format)
{
	return GetDataInt($data[$field],$data,$field, $format);
}

//	GetData Internal
function GetDataInt($value,$data,$field, $format)
{
	global $strTableName;
	$ret="";
// long binary data?
	if(IsBinaryType(GetFieldType($field)))
	{
		$ret="LONG BINARY DATA - CANNOT BE DISPLAYED";
	} else
		$ret = $value;
	if($ret===false)
		return "";
	
	if($format == FORMAT_DATE_SHORT) 
		$ret = format_shortdate(db2time($value));
	else if($format == FORMAT_DATE_LONG) 
		$ret = format_longdate(db2time($value));
	else if($format == FORMAT_DATE_TIME) 
		$ret = format_datetime(db2time($value));
	else if($format == FORMAT_TIME) 
	{
		if(IsDateFieldType(GetFieldType($field)))
			$ret = format_time(db2time($value));
		else
		{
			$numbers=parsenumbers($value);
			if(!count($numbers))
				return "";
			while(count($numbers)<3)
				$numbers[]=0;
			$ret = format_time(array(0,0,0,$numbers[0],$numbers[1],$numbers[2]));
		}
	}
	else if($format == FORMAT_NUMBER) 
		$ret = format_number($value);
	else if($format == FORMAT_CURRENCY) 
		$ret = format_currency($value);
	else if($format == FORMAT_CHECKBOX) 
	{
		$ret="<img src=\"images/check_";
		if($value && $value!=0)
			$ret.="yes";
		else
			$ret.="no";
		$ret.=".gif\" border=0>";
	}
	else if($format == FORMAT_PERCENT) 
	{
		if($value!="")
			$ret = ($value*100)."%";
	}
	else if($format == FORMAT_PHONE_NUMBER)
	{
		if(strlen($ret)==7)
			$ret=substr($ret,0,3)."-".substr($ret,3);
		else if(strlen($ret)==10)
			$ret="(".substr($ret,0,3).") ".substr($ret,3,3)."-".substr($ret,6);
	}
	else if($format == FORMAT_FILE_IMAGE)
	{
		if(!CheckImageExtension($ret))
			return "";
			
		$thumbnailed=false;
		$thumbprefix="";
		if($strTableName=="articles" && $field=="screenp")
		{
			$thumbnailed=true;
			$thumbprefix="th";
		}
		if($strTableName=="articles" && $field=="screens")
		{
			$thumbnailed=true;
			$thumbprefix="th";
		}
		if($strTableName=="article" && $field=="screenp")
		{
			$thumbnailed=true;
			$thumbprefix="th";
		}
		if($strTableName=="article" && $field=="screens")
		{
			$thumbnailed=true;
			$thumbprefix="th";
		}
		if($thumbnailed)
		{
		 	// show thumbnail
			$thumbname=$thumbprefix.$ret;
			if(substr(GetLinkPrefix($field),0,7)!="http://" && !file_exists(GetUploadFolder($field).$thumbname))
				$thumbname=$ret;
			$ret="<a target=_blank href=\"".htmlspecialchars(AddLinkPrefix($field,$ret))."\">";
			$ret.="<img";
			$ret.=" border=0";
			$ret.=" src=\"".htmlspecialchars(AddLinkPrefix($field,$thumbname))."\"></a>";
		}
		else
			$ret='<img src="'.AddLinkPrefix($field,$ret).'" border=0>';
	}
	else if($format == FORMAT_HYPERLINK)
	{
		$ret=GetHyperlink($ret,$field,$data);
	}
	else if($format==FORMAT_EMAILHYPERLINK)
	{
		$link=$ret;
		$title=$ret;
		if(substr($ret,0,7)=="mailto:")
			$title=substr($ret,8);
		else
			$link="mailto:".$link;
		$ret='<a href="'.$link.'">'.$title.'</a>';
	}
	else if($format==FORMAT_FILE)
	{
		$iquery="field=".rawurlencode($field);
		if($strTableName=="comments")
		{
			$iquery.="&key1=".rawurlencode($data["CommentID"]);
		}
		if($strTableName=="main")
		{
			$iquery.="&key1=".rawurlencode($data["CategoryID"]);
		}
		if($strTableName=="articles")
		{
			$iquery.="&key1=".rawurlencode($data["ArticleID"]);
		}
		if($strTableName=="article")
		{
			$iquery.="&key1=".rawurlencode($data["ArticleID"]);
		}
		if($strTableName=="users")
		{
			$iquery.="&key1=".rawurlencode($data["CategoryID"]);
		}
		if($strTableName=="comment")
		{
			$iquery.="&key1=".rawurlencode($data["CommentID"]);
		}
		if($strTableName=="register")
		{
			$iquery.="&key1=".rawurlencode($data["email"]);
		}
		if($strTableName=="_register")
		{
			$iquery.="&key1=".rawurlencode($data["email"]);
		}
		return 	'<a href="'.GetTableURL($strTableName).'_download.php?'.$iquery.'".>'.htmlspecialchars($ret).'</a>';
	}
	else if(GetEditFormat($field)==EDIT_FORMAT_CHECKBOX && $format==FORMAT_NONE)
	{
		if($ret && $ret!=0)
			$ret="Yes";
		else
			$ret="No";
	}
	else if($format == FORMAT_CUSTOM) 
		$ret = CustomExpression($value,$data,$field);
	return $ret;
}

//	return custom expression
function CustomExpression($value,$data,$field,$table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
	if($table=="main" && $field=="Views") 
	{
		if ($value==NULL) $value="0";
	}
	if($table=="articles" && $field=="Title") 
	{
		global $data;
$value="<a href=\"articles_view.php?editid1=".$data["ArticleID"]."\" class=kb10><b>".$data["Title"]."</b></a>";

	}
	return $value;
}


function ProcessLargeText($strValue,$iquery="",$table="", $mode=MODE_LIST)
{
	global $strTableName;

	$cNumberOfChars = GetNumberOfChars($table);
	if(substr($strValue,0,8)=="<a href=")
		return $strValue;
	if(substr($strValue,0,23)=="<img src=\"images/check_")
		return $strValue;
	if($cNumberOfChars>0 && strlen($strValue)>$cNumberOfChars && (strlen($strValue)<200 || !strlen($iquery)) && $mode==MODE_LIST)
	{
		$ret = substr($strValue,0,$cNumberOfChars );
		$ret=htmlspecialchars($ret);
		$ret.=" <a href=\"#\" onClick=\"javascript: pwin = window.open('',null,'height=300,width=400,status=yes,resizable=yes,toolbar=no,menubar=no,location=no,left=150,top=200,scrollbars=yes'); ";
		$ind = 1;
		$ret.="pwin.document.write('" . htmlspecialchars(jsreplace(nl2br(substr($strValue,0, 801)))) ."');";
//		$ret.="pwin.document.write('" . db_addslashes(str_replace("\r\n","<br>",htmlspecialchars(substr($strValue,0, 801)))) ."');";
		$ret.="pwin.document.write('<br><hr size=1 noshade><a href=# onClick=\\'window.close();return false;\\'>"."Close window"."</a>');";
		$ret.="return false;\">"."More"." ...</a>";
	}
	else if($cNumberOfChars>0 && strlen($strValue)>$cNumberOfChars && $mode==MODE_LIST)
	{
		$table = GetTableURL($table);
		$ret = substr($strValue,0,$cNumberOfChars );
		$ret=htmlspecialchars($ret);
		$ret.=" <a href=#  onClick=\"javascript: pwin = window.open('',null,'height=300,width=400,status=yes,resizable=yes,toolbar=no,menubar=no,location=no,left=150,top=200,scrollbars=yes');";
		$ret.=" pwin.location='".$table."_fulltext.php?".$iquery."'; return false;\">"."More"." ...</a>";
	}
	else if($cNumberOfChars>0 && strlen($strValue)>$cNumberOfChars && $mode==MODE_PRINT)
	{
		$ret = substr($strValue,0,$cNumberOfChars );
		$ret=htmlspecialchars($ret);
		if(strlen($strValue)>$cNumberOfChars)
			$ret.=" ...";
	}
	else
		$ret= htmlspecialchars($strValue);

/*
//	highlight search results
	if ($mode==MODE_LIST && $_SESSION[$strTableName."_search"]==1)
	{
		$ind = 0;
		$searchopt=$_SESSION[$strTableName."_searchoption"];
		$searchfor=$_SESSION[$strTableName."_searchfor"];
//		highlight Contains search
		if($searchopt=="Contains")
		{
			while ( ($ind = my_stripos($ret, $searchfor, $ind)) !== false )
			{
				$ret = substr($ret, 0, $ind) . "<span class=highlight>". substr($ret, $ind, strlen($searchfor)) ."</span>" . substr($ret, $ind + strlen($searchfor));
				$ind+= strlen("<span class=highlight>") + strlen($searchfor) + strlen("</span>");
			}
		}
//		highlight Starts with search
		elseif($searchopt=="Starts with ...")
		{
			if( !strncasecmp($ret, $searchfor,strlen($searchfor)) )
				$ret = "<span class=highlight>". substr($ret, 0, strlen($searchfor)) ."</span>" . substr($ret, strlen($searchfor));
		}
		elseif($searchopt=="Equals")
		{
			if( !strcasecmp($ret, $searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
		elseif($searchopt=="More than ...")
		{
			if( strtoupper($ret)>strtoupper($searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
		elseif($searchopt=="Less than ...")
		{
			if( strtoupper($ret)<strtoupper($searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
		elseif($searchopt=="Equal or more than ...")
		{
			if( strtoupper($ret)>=strtoupper($searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
		elseif($searchopt=="Equal or less than ...")
		{
			if( strtoupper($ret)<=strtoupper($searchfor) )
				$ret = "<span class=highlight>". $ret ."</span>";
		}
	}
*/
	return nl2br($ret);
}

//	construct hyperlink
function GetHyperlink($str, $field,$data,$table="")
{
	global $strTableName;
	if(!strlen($table))
		$table=$strTableName;
	if(!strlen($str))
		return "";
	$ret=$str;
	$title=$ret;
	$link=$ret;
	if(substr($ret,strlen($ret)-1)=='#')
	{
		$i=strpos($ret,'#');
		$title=substr($ret,0,$i);
		$link=substr($ret,$i+1,strlen($ret)-$i-2);
		if(!$title)
			$title=$link;
	}
	$target="";
	
	if(strpos($link,"://")===false && substr($link,0,7)!="mailto:")
		$link=$prefix.$link;
	$ret='<a href="'.$link.'"'.$target.'>'.$title.'</a>';
	return $ret;
}

//	add prefix to the URL
function AddLinkPrefix($field,$link,$table="")
{
	if(strpos($link,"://")===false && substr($link,0,7)!="mailto:")
		return GetLinkPrefix($field,$table).$link;
	return $link;
}

//	return Totals string
function GetTotals($field,$value, $stype, $iNumberOfRows,$sFormat)
{
	if($stype=="AVERAGE")
	{
		if($iNumberOfRows)
			$value=round($value/$iNumberOfRows,2);
		else
			return "";
	}
	$sValue="";
	$data=array($field=>$value);
	if($sFormat == FORMAT_CURRENCY)
	 	$sValue = format_currency($value);
	else if($sFormat == FORMAT_PERCENT)
		$sValue = format_number($value*100)."%"; 
	else if($sFormat == FORMAT_NUMBER)
 		$sValue = format_number($value);
	else if($sFormat == FORMAT_CUSTOM && $stype!="COUNT")
 		$sValue = GetData($data,$field,$sFormat);
	else 
 		$sValue = $value;

	if($stype=="COUNT") 
		return $value;
	if($stype=="TOTAL") 
		return $sValue;
	if($stype=="AVERAGE") 
		return $sValue;
	return "";
}


////////////////////////////////////////////////////////////////////////////////
// miscellaneous functions
////////////////////////////////////////////////////////////////////////////////


//	refine value passed by POST or GET method
function refine($str)
{
	$ret=$str;
	if(get_magic_quotes_gpc())
		$ret=stripslashes($str);
	return html_special_decode($ret);
}

//	return refined POST or GET value - single value or array
function postvalue($name)
{
	if(array_key_exists($name,$_POST))
		$value=$_POST[$name];
	else if(array_key_exists($name,$_GET))
		$value=$_GET[$name];
	else
		return "";
	if(!is_array($value))
		return refine($value);
	$ret=array();
	foreach($value as $key=>$val)
		$ret[$key]=refine($val);
	return $ret;
}

//	analog of strrpos function
function my_strrpos($haystack, $needle) {
   $index = strpos(strrev($haystack), strrev($needle));
   if($index === false) {
       return false;
   }
   $index = strlen($haystack) - strlen($needle) - $index;
   return $index;
}

//	utf-8 analog of strlen function
function strlen_utf8($str)
{
	$len=0;
	$i=0;
	$olen=strlen($str);
	while($i<$olen)
	{
		$c=ord($str[$i]);
		if($c<128)
			$i++;
		else if($i<$olen-1 && $c>=192 && $c<=223)
			$i+=2;
		else if($i<$olen-2 && $c>=224 && $c<=239)
			$i+=3;
		else if($i<$olen-3 && $c>=240)
			$i+=4;
		else
			break;
		$len++;
	}
	return $len;
}

//	utf-8 analog of substr function
function substr_utf8($str,$index,$strlen)
{
	if($strlen<=0)
		return "";
	$len=0;
	$i=0;
	$olen=strlen($str);
	$oindex=-1;
	while($i<$olen)
	{
		if($len==$index)
			$oindex=$i;
		
		$c=ord($str[$i]);
		if($c<128)
			$i++;
		else if($i<$olen-1 && $c>=192 && $c<=223)
			$i+=2;
		else if($i<$olen-2 && $c>=224 && $c<=239)
			$i+=3;
		else if($i<$olen-3 && $c>=240)
			$i+=4;
		else
			break;
		$len++;
		if($oindex>=0 && $len==$index+$strlen)
			return substr($str,$oindex,$i-$oindex);
	}
	if($oindex>0)
		return substr($str,$oindex,$olen-$oindex);
	return "";
}

//	read the whole file and return contents
function myfile_get_contents($filename)
{
	if(!file_exists($filename))
		return false;
	$handle = fopen($filename, "rb");
	if(!$handle)
		return false;
	fseek($handle, 0 , SEEK_END);
	$fsize = ftell($handle);
	fseek($handle, 0 , SEEK_SET);
	
	if($fsize)
		$contents = fread($handle, $fsize);
	else
		$contents="";
	fclose($handle);
	return $contents;
}

//	construct "good" field name
function GoodFieldName($field)
{
	$field=(string)$field;	
	for($i=0;$i<strlen($field);$i++)
	{
		$t=ord($field[$i]);
		if(($t<ord('a') || $t>ord('z')) && ($t<ord('A') || $t>ord('Z')) && ($t<ord('0') || $t>ord('9')))
			$field[$i]='_';
	}
	return $field;
}

//	prepare string for JavaScript. Replace ' with \' and linebreaks with \r\n
function jsreplace($str)
{
	return str_replace(array("\\","'","\r","\n"),array("\\\\","\\'","\\r","\\n"),$str);
}

//	display error message
function error_handler($errno, $errstr, $errfile, $errline)
{
	global $strSQL;

	if ($errno==2048)
		return 0;	

	if($errno==2 && strpos($errstr,"has been disabled for security reasons"))
		return 0;
	if($errno==8 && !strncmp($errstr,"Undefined index",15))
		return 0;
	if(strpos($errstr,"It is not safe to rely on the system's timezone settings."))
		return 0;
//////////////////////////////////////////////////////////////////////////////////////////////
	class XMLParser 
	{
		var $filename;
		var $xml;
		var $data;
   
		function XMLParser($xml_file)
		{
			$this->filename = $xml_file;
			$this->xml = xml_parser_create();
			xml_set_object($this->xml, $this);
			xml_set_element_handler($this->xml, 'startHandler', 'endHandler');
			xml_set_character_data_handler($this->xml, 'dataHandler');
			$this->parse($xml_file);
		}
   
		function parse($xml_file)
		{
			if (!($fp = fopen($xml_file, 'r'))) 
			{
				die('Cannot open XML data file: '.$xml_file);
            return false;
			}

			$bytes_to_parse = 512;

			while ($data = fread($fp, $bytes_to_parse)) 
			{
				$parse = xml_parse($this->xml, $data, feof($fp));
           
				if (!$parse) 
				{
					die(sprintf("XML error: %s at line %d",
					xml_error_string(xml_get_error_code($this->xml)),
                       xml_get_current_line_number($this->xml)));
                    xml_parser_free($this->xml);
				}
			}

			return true;
		}
   
		function startHandler($parser, $name, $attributes)
		{
			$data['name'] = $name;
			if ($attributes) 
			{
				$data['attributes'] = $attributes; 
			}
			$this->data[] = $data;
		}

		function dataHandler($parser, $data)
		{
			if ($data = trim($data)) 
			{
				$index = count($this->data) - 1;
				if(isset($this->data[$index]['content'])) 
				$this->data[$index]['content'] .= $data;
				else $this->data[$index]['content'] = $data;
			}
		}

		function endHandler($parser, $name)
		{
			if (count($this->data) > 1) 
			{
				$data = array_pop($this->data);
				$index = count($this->data) - 1; 
				$this->data[$index]['child'][] = $data;
			}
		}
	}
	//////////////////////////////////////////////////////////////////////////////

	$solution = "";
	$i = 0;

	$path_to_file = "include/errors.xml";
	$myFile = new XMLParser($path_to_file);
	$size = sizeof($myFile->data[0]['child'])-1;
	for ($i=0; $i<=$size; $i++)
	{
		$keywords = $myFile->data[0]['child'][$i]['child'][1]['content'];
		
		$keys = explode(" ",$keywords);
		
		for ($j=0; $j<sizeof($keys)-1; $j++)
		$pos[$j] = strpos(strtoupper($errstr), strtoupper($keys[$j]));
		$nullfound=false;
		foreach($pos as $val)
			if($val===false)
			{
				$nullfound=true;	
				break;
			}
		if(!$nullfound)
			{ 
				$solution = $myFile->data[0]['child'][$i]['child'][5]['content'];
			}

	}
?>
</form>
<p align=center><font size=+2>PHP <?php echo "error happened";?></font></p>
<table border="0" cellpadding="3" cellspacing="1" width="700" bgcolor="#000000" align="center">
<tr><td bgcolor="#ccccff" colspan=2 align=middle><font size=+1><b><?php echo "Technical information";?></b></font></td></tr>
<tr bgcolor="#cccccc"><td bgcolor="#ccccff"><b><?php echo "Error type";?></b></td><td align="left"><?php echo $errno; ?></td></tr>
<tr bgcolor="#cccccc"><td bgcolor="#ccccff"><b><?php echo "Error description";?></b></td><td align="left"><font color=#cc3300><?php echo $errstr?></font></td></tr>
<tr bgcolor="#cccccc"><td bgcolor="#ccccff"><b>URL</b></td><td align="left"><?php echo $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]; if(array_key_exists("QUERY_STRING",$_SERVER)) echo "?".$_SERVER["QUERY_STRING"];?> </td></tr>
<tr bgcolor="#cccccc"><td bgcolor="#ccccff"><b><?php echo "Error file";?></b></td><td align="left"><?php echo $errfile;?></td></tr>
<tr bgcolor="#cccccc"><td bgcolor="#ccccff"><b><?php echo "Error line";?></b></td><td align="left"><?php echo $errline;?></td></tr>
<tr bgcolor="#cccccc"><td bgcolor="#ccccff" ><b><?php echo "SQL query";?></b></td><td align="left"><?php if(isset($strSQL)) echo substr($strSQL,0,1024);?></td></tr>
<?php if ($solution) 
{?>
<tr bgcolor="#cccccc"><td bgcolor="#ccccff"><b>Solution</b></td><td align="left"><font color=#cc3300><?php echo $solution?></font></td></tr>
<?php } ?>
</table>
<?php
  exit(0);
}

function LogInfo($SQL)
{
	global $dSQL,$dDebug;
	$dSQL=$SQL;
	if($dDebug)
	{
		echo $dSQL;
		echo "<br>";
	}
}

//	suggest image type by extension
function SupposeImageType($file)
{
	if(strlen($file)>1 && $file[0]=='B' && $file[1]=='M')
		return "image/bmp";
	if(strlen($file)>2 &&  $file[0]=='G' && $file[1]=='I' && $file[2]=='F')
		return "image/gif";
	if(strlen($file)>3 &&  ord($file[0])==0xff && ord($file[1])==0xd8 && ord($file[2])==0xff)
		return "image/jpeg";
	if(strlen($file)>8 &&  ord($file[0])==0x89 && ord($file[1])==0x50 && ord($file[2])==0x4e && ord($file[3])==0x47
					   &&  ord($file[4])==0x0d && ord($file[5])==0x0a && ord($file[6])==0x1a && ord($file[7])==0x0a)
		return "image/png";
}

//	check if file extension is image extension
function CheckImageExtension($filename)
{
	if(strlen($filename)<4)
		return false;
	$ext=strtoupper(substr($filename,strlen($filename)-4));
	if($ext==".GIF" || $ext==".JPG" || $ext=="JPEG" || $ext==".PNG" || $ext==".BMP")
		return $ext;
	return false;
} 


function CreateThumbnail($value, $size, $ext)
{
	if(!function_exists("imagecreatefromstring"))
		return $value;
	$img = imagecreatefromstring($value);
	if(!$img)
		return $value;
	$sx = imagesx($img);
	$sy = imagesy($img);
	if($sx>$size || $sy>$size)
	{
		if($sx>=$sy)
		{
			$nsy=(integer)($sy*$size/$sx);
			$nsx=$size;
		}
		else
		{
			$nsx=(integer)($sx*$size/$sy);
			$nsy=$size;
		}
		$thumb = imagecreatetruecolor($nsx,$nsy);
		imagecopyresized($thumb,$img,0,0,0,0,$nsx,$nsy,$sx,$sy);
		ob_start();
		if($ext==".JPG" || $ext=="JPEG")
			imagejpeg($thumb);
		elseif($ext==".PNG")
			imagepng($thumb);
		else
			imagegif($thumb);
		$ret=ob_get_contents();
		ob_end_clean();
		imagedestroy($img);
		imagedestroy($thumb);
		return $ret;
	}
	imagedestroy($img);
	return $value;
}

function RTESafe($strText)
{
//	returns safe code for preloading in the RTE
	$tmpString="";
	
	$tmpString = trim($strText);
	if(!$tmpString) return "";
	
//	convert all types of single quotes
	$tmpString = str_replace( chr(145), chr(39),$tmpString);
	$tmpString = str_replace( chr(146), chr(39),$tmpString);
	$tmpString = str_replace("'", "&#39;",$tmpString);
	
//	convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34),$tmpString);
	$tmpString = str_replace(chr(148), chr(34),$tmpString);
	
//	replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ",$tmpString);
	$tmpString = str_replace(chr(13), " ",$tmpString);
	
	return $tmpString;
}


function now()
{
	return strftime("%Y-%m-%d %H:%M:%S");
}

function html_special_decode($str)
{
	$ret=$str;
	$ret=str_replace("&gt;",">",$ret);
	$ret=str_replace("&lt;","<",$ret);
	$ret=str_replace("&quot;","\"",$ret);
	$ret=str_replace("&#039;","'",$ret);
	$ret=str_replace("&amp;","&",$ret);
	return $ret;
}

////////////////////////////////////////////////////////////////////////////////
// database and SQL related functions
////////////////////////////////////////////////////////////////////////////////

//	add WHERE condition to gstrSQL
function gSQLWhere($where)
{
	global $gsqlHead,$gsqlFrom,$gsqlWhere,$gsqlTail;
	$strWhere=whereAdd($gsqlWhere,$where);
	if(strlen($strWhere))
		$strWhere=" where ".$strWhere." ";
	
	return $gsqlHead.$gsqlFrom.$strWhere.$gsqlTail;
}
//	add clause to WHERE expression
function whereAdd($where,$clause)
{
	if(!strlen($clause))
		return $where;
	if(!strlen($where))
		return $clause;
	return "(".$where.") and (".$clause.")";
}

//	add WHERE clause to SQL string
function AddWhere($sql,$where)
{
	if(!strlen($where))
		return $sql;
	$sql=str_replace(array("\r\n","\n","\t")," ",$sql);
	$tsql = strtolower($sql);
	$n = my_strrpos($tsql," where ");
	$n1 = my_strrpos($tsql," group by ");
	$n2 = my_strrpos($tsql," order by ");
	if($n1===false)
		$n1=strlen($tsql);
	if($n2===false)
		$n2=strlen($tsql);
	if ($n1>$n2)
		$n1=$n2;
	if($n===false)
		return substr($sql,0,$n1)." where ".$where.substr($sql,$n1);
	else
		return substr($sql,0,$n+strlen(" where "))."(".substr($sql,$n+strlen(" where "),$n1-$n-strlen(" where ")).") and (".$where.")".substr($sql,$n1);
}

//	construct WHERE clause with key values
function KeyWhere(&$keys, $table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
	$strWhere="";

//	comments
	if($table=="comments")
	{
			$value=make_db_value("CommentID",$keys["CommentID"]);
		if($value==="null")
			$strWhere.=GetFullFieldName("CommentID")." is null";
		else
			$strWhere.=GetFullFieldName("CommentID")."=".make_db_value("CommentID",$keys["CommentID"]);
	}

//	main
	if($table=="main")
	{
			$value=make_db_value("CategoryID",$keys["CategoryID"]);
		if($value==="null")
			$strWhere.=GetFullFieldName("CategoryID")." is null";
		else
			$strWhere.=GetFullFieldName("CategoryID")."=".make_db_value("CategoryID",$keys["CategoryID"]);
	}

//	articles
	if($table=="articles")
	{
			$value=make_db_value("ArticleID",$keys["ArticleID"]);
		if($value==="null")
			$strWhere.=GetFullFieldName("ArticleID")." is null";
		else
			$strWhere.=GetFullFieldName("ArticleID")."=".make_db_value("ArticleID",$keys["ArticleID"]);
	}

//	article
	if($table=="article")
	{
			$value=make_db_value("ArticleID",$keys["ArticleID"]);
		if($value==="null")
			$strWhere.=GetFullFieldName("ArticleID")." is null";
		else
			$strWhere.=GetFullFieldName("ArticleID")."=".make_db_value("ArticleID",$keys["ArticleID"]);
	}

//	users
	if($table=="users")
	{
			$value=make_db_value("CategoryID",$keys["CategoryID"]);
		if($value==="null")
			$strWhere.=GetFullFieldName("CategoryID")." is null";
		else
			$strWhere.=GetFullFieldName("CategoryID")."=".make_db_value("CategoryID",$keys["CategoryID"]);
	}

//	comment
	if($table=="comment")
	{
			$value=make_db_value("CommentID",$keys["CommentID"]);
		if($value==="null")
			$strWhere.=GetFullFieldName("CommentID")." is null";
		else
			$strWhere.=GetFullFieldName("CommentID")."=".make_db_value("CommentID",$keys["CommentID"]);
	}

//	register
	if($table=="register")
	{
			$value=make_db_value("email",$keys["email"]);
		if($value==="null")
			$strWhere.=GetFullFieldName("email")." is null";
		else
			$strWhere.=GetFullFieldName("email")."=".make_db_value("email",$keys["email"]);
	}

//	_register
	if($table=="_register")
	{
			$value=make_db_value("email",$keys["email"]);
		if($value==="null")
			$strWhere.=GetFullFieldName("email")." is null";
		else
			$strWhere.=GetFullFieldName("email")."=".make_db_value("email",$keys["email"]);
	}
	return $strWhere;
}

//	consctruct SQL WHERE clause for simple search
function StrWhere($strField, $SearchFor, $strSearchOption, $SearchFor2)
{
	global $strTableName;
	$type=GetFieldType($strField);
	if($strSearchOption=='Empty')
	{
		if(ischartype($type))
			return "(".GetFullFieldName($strField)." is null or ".GetFullFieldName($strField)."='')";
		else
			return GetFullFieldName($strField)." is null";
	}
	$strQuote="";
	if(NeedQuotes($type))
		$strQuote = "'";
//	return none if trying to compare numeric field and string value
	$sSearchFor=$SearchFor;
	$sSearchFor2=$SearchFor2;
	if(IsBinaryType($type))
		return "";
	
	if(IsDateFieldType($type) && $strSearchOption!="Contains" && $strSearchOption!="Starts with ..." )
	{
		$time=localdatetime2db($SearchFor);
		if($time=="null")
			return "";
		$sSearchFor=db_datequotes($time);
		if($strSearchOption=="Between")
		{
			$time=localdatetime2db($SearchFor2);
			if($time=="null")
				$sSearchFor2="";
			else
				$sSearchFor2=db_datequotes($time);
		}
	}
	
	if(!$strQuote && !is_numeric($sSearchFor) && !is_numeric($sSearchFor))
		return "";
	else if(!$strQuote && $strSearchOption!="Contains" && $strSearchOption!="Starts with ...")
	{
		$sSearchFor = 0+$sSearchFor;
		$sSearchFor2 = 0+$sSearchFor2;
	}
	else if(!IsDateFieldType($type) && $strSearchOption!="Contains" && $strSearchOption!="Starts with ...")
	{
		{
			$sSearchFor=db_upper($strQuote.db_addslashes($sSearchFor).$strQuote);
			if($strSearchOption=="Between" && $sSearchFor2)
				$sSearchFor2=db_upper($strQuote.db_addslashes($sSearchFor2).$strQuote);
		}
	}
	else if(!IsDateFieldType($type) || $strSearchOption=="Contains" || $strSearchOption=="Starts with ..." )
		$sSearchFor=db_addslashes($sSearchFor);
		
	if(IsCharType($type) )
		$strField=db_upper(GetFullFieldName($strField));
	else
		$strField=GetFullFieldName($strField);
	$ret="";
	if($strSearchOption=="Contains")
	{
		if(IsCharType($type) )
			return $strField." like ".db_upper("'%".$sSearchFor."%'");
		else
			return $strField." like '%".$sSearchFor."%'";
	}
	else if($strSearchOption=="Equals") return $strField."=".$sSearchFor;
	else if($strSearchOption=="Starts with ...")
	{
		if(IsCharType($type) )
			return $strField." like ".db_upper("'".$sSearchFor."%'");
		else
			return $strField." like '".$sSearchFor."%'";
	}
	else if($strSearchOption=="More than ...") return $strField.">".$sSearchFor;
	else if($strSearchOption=="Less than ...") return $strField."<".$sSearchFor;
	else if($strSearchOption=="Equal or more than ...") return $strField.">=".$sSearchFor;
	else if($strSearchOption=="Equal or less than ...") return $strField."<=".$sSearchFor;
	else if($strSearchOption=="Between")
	{
		$ret=$strField.">=".$sSearchFor;
		if($sSearchFor2) $ret.=" and ".$strField."<=".$sSearchFor2;
			return $ret;
	}
	return "";
}

//	construct SQL WHERE clause for Advanced search
function StrWhereAdv($strField, $SearchFor, $strSearchOption, $SearchFor2, $etype)
{
	global $strTableName;
	$type=GetFieldType($strField);
	if(IsBinaryType($type))
		return "";
	if($strSearchOption=='Empty')
	{
		if(ischartype($type))
			return "(".GetFullFieldName($strField)." is null or ".GetFullFieldName($strField)."='')";
		else
			return GetFullFieldName($strField)." is null";
	}
	if(GetEditFormat($strField)==EDIT_FORMAT_LOOKUP_WIZARD)
	{
		$SearchFor=splitvalues($SearchFor);
		$ret="";
		foreach($SearchFor as $value)
		{
			if(!($value=="null" || $value=="Null" || $value==""))
			{
				if(strlen($ret))
					$ret.=" or ";
				if($strSearchOption=="Equals")
				{
					$value=make_db_value($strField,$value);
					if(!($value=="null" || $value=="Null"))
						$ret.=GetFullFieldName($strField).'='.$value;
				}
				else
					$ret.=GetFullFieldName($strField)." like '%".$value."%'";
			}
		}
		if(strlen($ret))
			$ret="(".$ret.")";
		return $ret;
	}
	if(GetEditFormat($strField)==EDIT_FORMAT_CHECKBOX)
	{
		if($SearchFor=="none")
			return "";
		if(NeedQuotes($type))
		{
			if($SearchFor=="on")
				return "(".GetFullFieldName($strField)."<>'0' and ".GetFullFieldName($strField)."<>'' and ".GetFullFieldName($strField)." is not null)";
			else
				return "(".GetFullFieldName($strField)."='0' or ".GetFullFieldName($strField)."='' or ".GetFullFieldName($strField)." is null)";
		}
		else
		{
			if($SearchFor=="on")
				return "(".GetFullFieldName($strField)."<>0 and ".GetFullFieldName($strField)." is not null)";
			else
				return "(".GetFullFieldName($strField)."=0 or ".GetFullFieldName($strField)." is null)";
		}
	}
	$value1=make_db_value($strField,$SearchFor,$etype);
	$value2=false;
	if($strSearchOption=="Between")
		$value2=make_db_value($strField,$SearchFor2,$etype);
	if($strSearchOption!="Contains" && $strSearchOption!="Starts with ..." && ($value1==="null" || $value2==="null" ))
		return "";
	if(ischartype($type) )
	{
		$value1=db_upper($value1);
		$value2=db_upper($value2);
		$strField=db_upper(GetFullFieldName($strField));
	}
	else
		$strField=GetFullFieldName($strField);
	$ret="";
	if($strSearchOption=="Contains")
	{
		if(ischartype($type) )
			return $strField." like ".db_upper("'%".db_addslashes($SearchFor)."%'");
		else
			return $strField." like '%".db_addslashes($SearchFor)."%'";
	}
	else if($strSearchOption=="Equals") return $strField."=".$value1;
	else if($strSearchOption=="Starts with ...")
	{
		if(ischartype($type) )
			return $strField." like ".db_upper("'".db_addslashes($SearchFor)."%'");
		else
			return $strField." like '".db_addslashes($SearchFor)."%'";
	}
	else if($strSearchOption=="More than ...") return $strField.">".$value1;
	else if($strSearchOption=="Less than ...") return $strField."<".$value1;
	else if($strSearchOption=="Equal or more than ...") return $strField.">=".$value1;
	else if($strSearchOption=="Equal or less than ...") return $strField."<=".$value1;
	else if($strSearchOption=="Between")
	{
		$ret=$strField.">=".$value1;
		$ret.=" and ".$strField."<=".$value2;
		return $ret;
	}
	return "";
}

//	get count of rows from the query
function gSQLRowCount($where)
{
	global $conn;
	global $gsqlHead,$gsqlFrom,$gsqlWhere,$gsqlTail;
	$strWhere=whereAdd($gsqlWhere,$where);
	if(strlen($strWhere))
		$strWhere=" where ".$strWhere." ";
	
	$countstr = "select count(*) ".$gsqlFrom.$strWhere.$gsqlTail;
	$countrs = db_query($countstr,$conn);
	$countdata = db_fetch_numarray($countrs);
	return $countdata[0];
}

//	get count of rows from the query
function GetRowCount($strSQL)
{
	global $conn;
	$strSQL=str_replace(array("\r\n","\n","\t")," ",$strSQL);
	$tstr = strtoupper($strSQL);
	$ind1 = strpos($tstr,"SELECT ");
	$ind2 = my_strrpos($tstr," FROM ");
	$ind3 = my_strrpos($tstr," GROUP BY ");
	if($ind3===false)
	{
		$ind3 = strpos($tstr," ORDER BY ");
		if($ind3===false)
			$ind3=strlen($strSQL);
	}
	$countstr=substr($strSQL,0,$ind1+6)." count(*) ".substr($strSQL,$ind2+1,$ind3-$ind2);
	$countrs = db_query($countstr,$conn);
	$countdata = db_fetch_numarray($countrs);
	return $countdata[0];
}

//	add MSSQL Server TOP clause
function AddTop($strSQL, $n)
{
	$tstr = strtoupper($strSQL);
	$ind1 = strpos($tstr,"SELECT");
	return substr($strSQL,0,$ind1+6)." top $n ".substr($strSQL,$ind1+6);
}

//	add Oracle ROWNUMBER checking
function AddRowNumber($strSQL, $n)
{
	return "select * from (".$strSQL.") where rownum<".($n+1);
}

// test database type if values need to be quoted
function NeedQuotesNumeric($type)
{
    if($type == 203 || $type == 8 || $type == 129 || $type == 130 || 
		$type == 7 || $type == 133 || $type == 134 || $type == 135 ||
		$type == 201 || $type == 205 || $type == 200 || $type == 202 || $type==72 || $type==13)
		return true;
	else
		return false;
}

//	using ADO DataTypeEnum constants
//	the full list available at:
//	http://msdn.microsoft.com/library/default.asp?url=/library/en-us/ado270/htm/mdcstdatatypeenum.asp

function IsNumberType($type)
{
	if($type==20 || $type==6 || $type==14 || $type==5 || $type==10 
	|| $type==3 || $type==131 || $type==4 || $type==2 || $type==16
	|| $type==21 || $type==19 || $type==18 || $type==17 || $type==139
	|| $type==11)
		return true;
	return false;
}

function NeedQuotes($type)
{
	return !IsNumberType($type);
}

function IsBinaryType($type)
{
	if($type==128 || $type==205 || $type==204)
		return true;
	return false;
}

function IsDateFieldType($type)
{
	if($type==7 || $type==133 || $type==135)
		return true;
	return false;
}

function IsTimeType($type)
{
	if($type==134)
		return true;
	return false;
}

function IsCharType($type)	
{
	if(IsTextType($type) || $type==8 || $type==129 || $type==200 || $type==202 || $type==130)
		return true;
	return false;
}

function IsTextType($type)
{
	if($type==201 || $type==203)
		return true;
	return false;
}

////////////////////////////////////////////////////////////////////////////////
// security functions
////////////////////////////////////////////////////////////////////////////////


//	return user permissions on the table
//	A - Add
//	D - Delete
//	E - Edit
//	S - List/View/Search
//	P - Print/Export

function GetUserPermissions($table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
	if($_SESSION["AccessLevel"] == ACCESS_LEVEL_ADMIN)
			return "ADESP";

	$sUserGroup=@$_SESSION["GroupID"];
	if($table=="comments" && $sUserGroup=="moderator")
				return "AEDSP";
	if($table=="comments" && $sUserGroup=="admin")
				return "AEDSP";
	if($table=="comments" && $sUserGroup=="<Guest>")
				return "SP";
//	default permissions	
	if($table=="comments")
		return "ADS";
	if($table=="main" && $sUserGroup=="moderator")
				return "AEDSP";
	if($table=="main" && $sUserGroup=="admin")
				return "AEDSP";
	if($table=="main" && $sUserGroup=="<Guest>")
				return "";
//	default permissions	
	if($table=="main")
		return "ADS";
	if($table=="articles" && $sUserGroup=="moderator")
				return "AEDSP";
	if($table=="articles" && $sUserGroup=="admin")
				return "AEDSP";
	if($table=="articles" && $sUserGroup=="<Guest>")
				return "SP";
//	default permissions	
	if($table=="articles")
		return "AEDS";
	if($table=="article" && $sUserGroup=="moderator")
				return "AEDSP";
	if($table=="article" && $sUserGroup=="admin")
				return "AEDSP";
	if($table=="article" && $sUserGroup=="<Guest>")
				return "SP";
//	default permissions	
	if($table=="article")
		return "";
	if($table=="users" && $sUserGroup=="moderator")
				return "AEDSP";
	if($table=="users" && $sUserGroup=="admin")
				return "AEDSP";
	if($table=="users" && $sUserGroup=="<Guest>")
				return "SP";
//	default permissions	
	if($table=="users")
		return "";
	if($table=="comment" && $sUserGroup=="moderator")
				return "AEDSP";
	if($table=="comment" && $sUserGroup=="admin")
				return "AEDSP";
	if($table=="comment" && $sUserGroup=="<Guest>")
				return "SP";
//	default permissions	
	if($table=="comment")
		return "";
	if($table=="register" && $sUserGroup=="moderator")
				return "";
	if($table=="register" && $sUserGroup=="admin")
				return "AEDSP";
	if($table=="register" && $sUserGroup=="<Guest>")
				return "";
//	default permissions	
	if($table=="register")
		return "";
	if($table=="_register" && $sUserGroup=="moderator")
				return "S";
	if($table=="_register" && $sUserGroup=="admin")
				return "AEDSP";
	if($table=="_register" && $sUserGroup=="<Guest>")
				return "S";
//	default permissions	
	if($table=="_register")
		return "S";
}


//	check whether field is viewable
function CheckFieldPermissions($field, $table="")
{
	return GetFieldData($table,$field,"FieldPermissions",false);
}

// 
function CheckSecurity($strValue, $strAction)
{
global $cAdvSecurityMethod, $strTableName;
	if($_SESSION["AccessLevel"]==ACCESS_LEVEL_ADMIN)
		return true;

	if(@$_SESSION["AccessLevel"]!=ACCESS_LEVEL_ADMINGROUP)
	{
		if($strTableName=="comments")
		{
				if(( $strAction=="Edit" || $strAction=="Delete") && !((string)$_SESSION["_".$strTableName."_OwnerID"]===(string)$strValue))
				return false;
		}
		if($strTableName=="main")
		{
				if(( $strAction=="Edit" || $strAction=="Delete") && !((string)$_SESSION["_".$strTableName."_OwnerID"]===(string)$strValue))
				return false;
		}
		if($strTableName=="articles")
		{
				if(( $strAction=="Edit" || $strAction=="Delete") && !((string)$_SESSION["_".$strTableName."_OwnerID"]===(string)$strValue))
				return false;
		}
		if($strTableName=="article")
		{
				if(( $strAction=="Edit" || $strAction=="Delete") && !((string)$_SESSION["_".$strTableName."_OwnerID"]===(string)$strValue))
				return false;
		}
		if($strTableName=="comment")
		{
				if(( $strAction=="Edit" || $strAction=="Delete") && !((string)$_SESSION["_".$strTableName."_OwnerID"]===(string)$strValue))
				return false;
		}
		if($strTableName=="register")
		{
				if(( $strAction=="Edit" || $strAction=="Delete") && !((string)$_SESSION["_".$strTableName."_OwnerID"]===(string)$strValue))
				return false;
		}
	}
	//	 check user group permissions
	$strPerm = GetUserPermissions();
	if($strAction=="Add" && !(strpos($strPerm, "A")===false) ||
	   $strAction=="Edit" && !(strpos($strPerm, "E")===false) ||
	   $strAction=="Delete" && !(strpos($strPerm, "D")===false) ||
	   $strAction=="Search" && !(strpos($strPerm, "S")===false) ||
	   $strAction=="Export" && !(strpos($strPerm, "P")===false) )
		return true;
	else
		return false;
	return true;
}


//	add security WHERE clause to SELECT SQL command
function SecuritySQL($strAction)
{
global $cAdvSecurityMethod,$strTableName;
   	$ownerid=@$_SESSION["_".$strTableName."_OwnerID"];
	$ret="";
	if(@$_SESSION["AccessLevel"]==ACCESS_LEVEL_ADMIN)
		return "";
	$ret="";

	if(@$_SESSION["AccessLevel"]!=ACCESS_LEVEL_ADMINGROUP)
	{

		if($strTableName=="comments")
		{
				if($strAction=="Edit" || $strAction=="Delete")
				$ret=GetFullFieldName(GetTableOwnerID())."=".make_db_value(GetTableOwnerID(),$ownerid);
		}
		if($strTableName=="main")
		{
				if($strAction=="Edit" || $strAction=="Delete")
				$ret=GetFullFieldName(GetTableOwnerID())."=".make_db_value(GetTableOwnerID(),$ownerid);
		}
		if($strTableName=="articles")
		{
				if($strAction=="Edit" || $strAction=="Delete")
				$ret=GetFullFieldName(GetTableOwnerID())."=".make_db_value(GetTableOwnerID(),$ownerid);
		}
		if($strTableName=="article")
		{
				if($strAction=="Edit" || $strAction=="Delete")
				$ret=GetFullFieldName(GetTableOwnerID())."=".make_db_value(GetTableOwnerID(),$ownerid);
		}
		if($strTableName=="comment")
		{
				if($strAction=="Edit" || $strAction=="Delete")
				$ret=GetFullFieldName(GetTableOwnerID())."=".make_db_value(GetTableOwnerID(),$ownerid);
		}
		if($strTableName=="register")
		{
				if($strAction=="Edit" || $strAction=="Delete")
				$ret=GetFullFieldName(GetTableOwnerID())."=".make_db_value(GetTableOwnerID(),$ownerid);
		}
	}

	$strPerm = GetUserPermissions();
	if($strAction=="Edit" && !(strpos($strPerm, "E")===false) ||
	   $strAction=="Delete" && !(strpos($strPerm, "D")===false) ||
	   $strAction=="Search" && !(strpos($strPerm, "S")===false) ||
	   $strAction=="Export" && !(strpos($strPerm, "P")===false) )
		return $ret;
	else
		return "1=0";
	return "";
}

////////////////////////////////////////////////////////////////////////////////
// editing functions
////////////////////////////////////////////////////////////////////////////////

function make_db_value($field,$value,$controltype="",$postfilename="")
{
	$ret=prepare_for_db($field,$value,$controltype,$postfilename);
	if($ret===false)
		return $ret;
	return add_db_quotes($field,$ret);
}

function add_db_quotes($field,$value)
{
	global $strTableName;
	$type=GetFieldType($field);
	if(IsBinaryType($type))
		return db_addslashesbinary($value);
	if(($value==="" || $value===FALSE) && !ischartype($type))
		return "null";
	if(NeedQuotes($type))
	{
		if(!IsDateFieldType($type))
			$value="'".db_addslashes($value)."'";
		else
			$value=db_datequotes($value);
	}
	else
	{
		$strvalue = (string)$value;
		$strvalue = str_replace(",",".",$strvalue);
		$value=0+$strvalue;
	}
	return $value;
}


function prepare_for_db($field,$value,$controltype="",$postfilename="")
{
	global $strTableName,$filename,$files_delete,$files_move;
	$filename="";
	$type=GetFieldType($field);
	if(!$controltype)
	{
		if(is_array($value))
			$value=combinevalues($value);
		if(($value==="" || $value===FALSE) && !ischartype($type))
			return "";
		return $value;
	}
	else if(substr($controltype,0,4)=="file")
	{
		$file=&$_FILES["value_".GoodFieldName($field)];
		if($file["error"] && $file["error"]!=4)
			return false;
		if(trim($postfilename))
			$filename=refine(trim($postfilename));
		else
			$filename=$file['name'];
		if(substr($controltype,4,1)=="1")
		{
			$filename="";
			return "";
		}
		if(substr($controltype,4,1)=="0")
			return false;
		$ret=myfile_get_contents($file['tmp_name']);
		if($ret===false)
			return false;
		return $ret;
	}
	else if(substr($controltype,0,6)=="upload")
	{
		$file=&$_FILES["file_".GoodFieldName($field)];
		if($file["error"] && $file["error"]!=4)
			return false;
		if(substr($controltype,6,1)=="1")
		{
			if(strlen($postfilename))
			{
				$files_delete[]=GetUploadFolder($field).$postfilename;
				if(GetCreateThumbnail($field))
					$files_delete[]=GetUploadFolder($field).GetThumbnailPrefix($field).$postfilename;
			}
			return "";
		}
		if(substr($controltype,6,1)=="0")
			return false;
		if(strlen($file['tmp_name']))
		{
			$file_move = array($file['tmp_name'],GetUploadFolder($field).$value);
			$files_move[] = $file_move;
		}
		return $value;
	}
	else if($controltype=="time")
	{
		if(!strlen($value))
			return "";
		$time=localtime2db($value);
		if(IsDateFieldType(GetFieldType($field)))
		{
			$time="2000-01-01 ".$time;
		}
		return $time;
	}
	else if(substr($controltype,0,4)=="date")
	{
		$dformat=substr($controltype,4);
		if($dformat==EDIT_DATE_SIMPLE || $dformat==EDIT_DATE_SIMPLE_DP)
		{
			$time=localdatetime2db($value);
			if($time=="null")
				return "";
			return $time;
		}
		else if($dformat==EDIT_DATE_DD || $dformat==EDIT_DATE_DD_DP)
		{
			$a=explode("-",$value);
			if(count($a)<3)
				return "";
			else
				list($y,$m,$d)=$a;
			if($y<100)
			{
				if($y<70)
					$y+=2000;
				else
					$y+=1900;
			}
			return sprintf("%04d-%02d-%02d",$y,$m,$d);
		}
		else
			return "";
	}
	else if(substr($controltype,0,8)=="checkbox")
	{
		if($value=="on")
			$ret=1;
		else if($value=="none")
			return "";
		else 
			$ret=0;
		return $ret;
	}
	else
		return false;
}

//	delete uploaded files when deleting the record
function DeleteUploadedFiles($where,$table="")
{
	global $conn,$gstrSQL;
	$sql = gSQLWhere($where);
	$rs = db_query($sql,$conn);
	if(!($data=db_fetch_array($rs)))
		return;
	foreach($data as $field=>$value)
	{
		if(strlen($value) && GetEditFormat($field)==EDIT_FORMAT_FILE)
		{
			if(file_exists(GetUploadFolder($field).$value))
				@unlink(GetUploadFolder($field).$value);
			if(GetCreateThumbnail($field) && file_exists(GetUploadFolder($field).GetThumbnailPrefix($field).$value))
				@unlink(GetUploadFolder($field).GetThumbnailPrefix($field).$value);
		}
	}
}

//	combine checked values from multi-select list box
function combinevalues($arr)
{
	$ret="";
	foreach($arr as $val)
	{
		if(strlen($ret))
			$ret.=",";
		if(strpos($val,",")===false && strpos($val,'"')===false)
			$ret.=$val;
		else
		{
			$val=str_replace('"','""',$val);
			$ret.='"'.$val.'"';
		}
	}
	return $ret;
}

//	split values for multi-select list box
function splitvalues($str)
{
	$arr=array();
	$start=0;
	$i=0;
	$inquot=false;
	while($i<=strlen($str))
	{
		if($i<strlen($str) && $str{$i}=='"')
			$inquot=!$inquot;
		else if($i==strlen($str) || !$inquot && $str{$i}==',')
		{
			$val=substr($str,$start,$i-$start);
			$start=$i+1;
			if(strlen($val) && $val{0}=='"')
			{
				$val=substr($val,1,strlen($val)-2);
				$val=str_replace('""','"',$val);
			}
			$arr[]=$val;
		}
		$i++;
	}
	return $arr;
}


////////////////////////////////////////////////////////////////////////////////
// edit controls creation functions
////////////////////////////////////////////////////////////////////////////////


//	write days dropdown
function WriteDays($d)
{
	$ret='<option value=""> </option>';
	for($i=1;$i<=31;$i++)
		$ret.='<option value="'.$i.'" '.($i==$d?"selected":"").'>'.$i."</option>\r\n";
	return $ret;
}

//	write months dropdown
function WriteMonths($m)
{
	$monthnames=array();
	$monthnames[1]="January";
	$monthnames[2]="February";
	$monthnames[3]="March";
	$monthnames[4]="April";
	$monthnames[5]="May";
	$monthnames[6]="June";
	$monthnames[7]="July";
	$monthnames[8]="August";
	$monthnames[9]="September";
	$monthnames[10]="October";
	$monthnames[11]="November";
	$monthnames[12]="December";
	$ret='<option value=""></option>';
	for($i=1;$i<=12;$i++)
		$ret.='<option value="'.$i.'" '.($i==$m?"selected":"").'>'.$monthnames[$i]."</option>\r\n";
	return $ret;
}

//	write years dropdown
function WriteYears($y)
{
	$tm=localtime(time(),true);
	$ret='<option value=""> </option>';
	$firstyear=$tm["tm_year"]+1900-100;
	if($y && $firstyear>$y-5)
		$firstyear=$y-10;
	$lastyear=$tm["tm_year"]+1900+10;
	if($y && $lastyear<$y+5)
		$lastyear=$y+10;
	for($i=$firstyear;$i<=$lastyear;$i++)
		$ret.='<option value="'.$i.'" '.($i==$y?"selected":"").'>'.$i."</option>\r\n";
	return $ret;
}

//	returns HTML code that represents required Date edit control
function GetDateEdit($field, $value, $type, $secondfield=false,$search=MODE_EDIT)
{	
	global $cYearRadius, $locale_info;
	$cfieldname=GoodFieldName($field);
	$cfield="value_".GoodFieldName($field);
	$ctype="type_".GoodFieldName($field);
	if($secondfield)
	{
		$cfield="value1_".GoodFieldName($field);
		$ctype="type1_".GoodFieldName($field);
	}
	$iname=$cfield;
	$tvalue=$value;
	if($search==MODE_SEARCH && ($type==EDIT_DATE_SIMPLE || $type==EDIT_DATE_SIMPLE_DP))
		$tvalue=localdatetime2db($value);
	$time=db2time($tvalue);
	if(!count($time))
		$time=array(0,0,0,0,0,0);
	$dp=0;
	$edit_type = postvalue("editType");
	$record_id = postvalue("recordID");
	
	switch($type)
	{
		case EDIT_DATE_SIMPLE_DP:
			$ovalue=$value;
			if($locale_info["LOCALE_IDATE"]==1)
			{
				$fmt="dd".$locale_info["LOCALE_SDATE"]."MM".$locale_info["LOCALE_SDATE"]."yyyy";
				$sundayfirst="false";
			}
			else if($locale_info["LOCALE_IDATE"]==0)
			{
				$fmt="MM".$locale_info["LOCALE_SDATE"]."dd".$locale_info["LOCALE_SDATE"]."yyyy";
				$sundayfirst="true";
			}
			else
			{
				$fmt="yyyy".$locale_info["LOCALE_SDATE"]."MM".$locale_info["LOCALE_SDATE"]."dd";
				$sundayfirst="false";
			}
			if(DateEditShowTime($field) )
			{
				if($time[5])
					$fmt.=" HH:mm:ss";
				else if($time[3] || $time[4])
					$fmt.=" HH:mm";
			}
			if($time[0])
				$ovalue=format_datetime_custom($time,$fmt);
			$ovalue1=$time[2]."-".$time[1]."-".$time[0];
			$showtime="false";
			if(DateEditShowTime($field))
			{
				$showtime="true";
				$ovalue1.=" ".$time[3].":".$time[4].":".$time[5];
			}
			if ( $edit_type == "inline" ) {
				$onblur="var dt=parse_datetime(this.value,".$locale_info["LOCALE_IDATE"]."); if(dt!=null) $('input#ts".$iname."_".$record_id."').get(0).value=print_datetime(dt,-1,".$showtime."); else $('input#ts".$iname."_".$record_id."').get(0).value='';";
			} else {
				$onblur="var dt=parse_datetime(this.value,".$locale_info["LOCALE_IDATE"]."); if(dt!=null) $('input[@name=ts".$iname."]').get(0).value=print_datetime(dt,-1,".$showtime."); else $('input[@name=ts".$iname."]').get(0).value='';";
			}
			$ret='<input type="Text" name="'.$iname.'" size = "20" value="'.$ovalue.'" onblur="'.$onblur.'">'; 
			$ret.='<input type="Hidden" name="ts'.$iname.'" value="'.$ovalue1.'">&nbsp;&nbsp;';
			if ( $edit_type == "inline" ) {
				$ret.='<a href="#" onclick="javascript:var v=show_calendar(\'update\', \''.$iname.'\',\''.$record_id.'\', $(\'input#ts'.$iname.'_'.$record_id.'\').get(0).value,'.$showtime.','.$sundayfirst.'); return false;">'.
					'<img src="images/cal.gif" width=16 height=16 border=0 alt="'."Click Here to Pick up the date".'"></a>';
				$ret.="\r\n<script language=JavaScript>".
					"	function update".$iname."_".$record_id."(newDate) ".
					"{ ";
				$ret.="		$('input#".$iname."_".$record_id."').get(0).value =  print_datetime(newDate,".$locale_info["LOCALE_IDATE"].",".$showtime.");";
				$ret.="		$('input#ts".$iname."_".$record_id."').get(0).value =  print_datetime(newDate,-1,".$showtime.");";
				$ret.="	}".	"</script>\r\n\r\n";
			} else {
				$ret.='<a href="#" onclick="javascript:var v=show_calendar(\'update'.$iname.'\', \'\',\'\', $(\'input[@name=ts'.$iname.']\').get(0).value,'.$showtime.','.$sundayfirst.'); return false;">'.
					'<img src="images/cal.gif" width=16 height=16 border=0 alt="'."Click Here to Pick up the date".'"></a>';
				$ret.="\r\n<script language=JavaScript>".
					"	function update".$iname."(newDate) ".
					"{ ";
				$ret.="		$('input[@name=".$iname."]').get(0).value =  print_datetime(newDate,".$locale_info["LOCALE_IDATE"].",".$showtime.");";
				$ret.="		$('input[@name=ts".$iname."]').get(0).value =  print_datetime(newDate,-1,".$showtime.");";
				$ret.="	}".	"</script>\r\n\r\n";
			}			
			echo $ret;
			return;
		case EDIT_DATE_DD_DP:
			$dp=1;
		case EDIT_DATE_DD:
			$ovalue=$value;
			if($time)
				$ovalue=format_datetime_custom($time,"yyyy-MM-dd");
			if ( $edit_type == "inline" ) {
				$retday='<select class=selects name="day'.$iname.'" onchange="SetDate(\''.$iname.'\',\''.$record_id.'\'); return true;">'.WriteDays($time[2])."</select>";
				$retmonth='<select class=selectm name="month'.$iname.'" onchange="SetDate(\''.$iname.'\',\''.$record_id.'\'); return true;">'.WriteMonths($time[1])."</select>";
				$retyear='<select class=selects name="year'.$iname.'" onchange="SetDate(\''.$iname.'\',\''.$record_id.'\'); return true;">'.WriteYears($time[0])."</select>";
			} else {
				$retday='<select class=selects name="day'.$iname.'" onchange="javascript: SetDate'.$iname.'(); return true;">'.WriteDays($time[2])."</select>";
				$retmonth='<select class=selectm name="month'.$iname.'" onchange="javascript: SetDate'.$iname.'(); return true;">'.WriteMonths($time[1])."</select>";
				$retyear='<select class=selects name="year'.$iname.'" onchange="javascript: SetDate'.$iname.'(); return true;">'.WriteYears($time[0])."</select>";
			}
			
			$sundayfirst="false";
			if($locale_info["LOCALE_ILONGDATE"]==1)
				$ret=$retday."&nbsp;".$retmonth."&nbsp;".$retyear;
			else if($locale_info["LOCALE_ILONGDATE"]==0)
			{
				$ret=$retmonth."&nbsp;".$retday."&nbsp;".$retyear;
				$sundayfirst="true";
			}
			else
				$ret=$retyear."&nbsp;".$retmonth."&nbsp;".$retday;
				
			if($dp)
			{
				if ( $edit_type == "inline" ) {
					$ret.="&nbsp;".
						"<a href=\"#\" onclick=\"javascript:var v=show_calendar('update','".$iname."','".$record_id."', $('input#ts".$iname."_".$record_id."').get(0).value,false,".$sundayfirst."); return false;\">".
						"<img src=images/cal.gif width=16 height=16 border=0 alt=\""."Click Here to Pick up the date"."\"></a>".
						"<input type=hidden name=\"ts".$iname."\" value=\"".$time[2]."-".$time[1]."-".$time[0]."\">";
				} else {
					$ret.="&nbsp;".
						"<a href=\"#\" onclick=\"javascript:var v=show_calendar('update".$iname."','','', $('input[@name=ts".$iname."]').get(0).value,false,".$sundayfirst."); return false;\">".
						"<img src=images/cal.gif width=16 height=16 border=0 alt=\""."Click Here to Pick up the date"."\"></a>".
						"<input type=hidden name=\"ts".$iname."\" value=\"".$time[2]."-".$time[1]."-".$time[0]."\">";
				}
			}
			if($time[0] && $time[1] && $time[2])
				$ret.="<input type=hidden name=\"".$iname."\" value=\"".$time[0]."-".$time[1]."-".$time[2]."\">";
			else
				$ret.="<input type=hidden name=\"".$iname."\" value=\"\">";
			
			if ( $edit_type == "inline" )
			{
				$ret.="<script language=JavaScript>"."\r\n".
					"function SetDate".$iname."_".$record_id."()"."\r\n".
					"{ "."\r\n".
					"  if ( $('select#month".$iname."_".$record_id."').get(0).value!='' && $('select#day".$iname."_".$record_id."').get(0).value!='' && $('select#year".$iname."_".$record_id."').get(0).value!='') {"."\r\n".
					"	$('input#".$iname."_".$record_id."').get(0).value= ''+$('select#year".$iname."_".$record_id."').get(0).value + "."\r\n".
					" 	'-' + $('select#month".$iname."_".$record_id."').get(0).value + '-' + $('select#day".$iname."_".$record_id."').get(0).value; "."\r\n";
				if($dp)
					$ret.="   $('input#ts".$iname."_".$record_id."').get(0).value='' + $('select#day".$iname."_".$record_id."').get(0).value+'-'+$('select#month".$iname."_".$record_id."').get(0).value+'-'+$('select#year".$iname."_".$record_id."').get(0).value;"."\r\n";
				$ret.="  } else {"."\r\n";
				if($dp)
					$ret.="	$('input#ts".$iname."_".$record_id."').get(0).value= '".$time[2]."-".$time[1]."-".$time[0]."';"."\r\n";
				$ret.="	$('input#".$iname."_".$record_id."').get(0).value= '';"."\r\n".
					"   } "."\r\n".
					" } "."\r\n".
					"\r\n";			
			} else {
				$ret.="<script language=JavaScript>"."\r\n".
					"function SetDate".$iname."()"."\r\n".
					"{ "."\r\n".
					"  if ( $('select[@name=month".$iname."]').get(0).value!='' && $('select[@name=day".$iname."]').get(0).value!='' && $('select[@name=year".$iname."]').get(0).value!='') {"."\r\n".
					"	$('input[@name=".$iname."]').get(0).value= ''+$('select[@name=year".$iname."]').get(0).value + "."\r\n".
					" 	'-' + $('select[@name=month".$iname."]').get(0).value + '-' + $('select[@name=day".$iname."]').get(0).value; "."\r\n";
				if($dp)
					$ret.="   $('input[@name=ts".$iname."]').get(0).value='' + $('select[@name=day".$iname."]').get(0).value+'-'+$('select[@name=month".$iname."]').get(0).value+'-'+$('select[@name=year".$iname."]').get(0).value;"."\r\n";
				$ret.="  } else {"."\r\n";
				if($dp)
					$ret.="	$('input[@name=ts".$iname."]').get(0).value= '".$time[2]."-".$time[1]."-".$time[0]."';"."\r\n";
				$ret.="	$('input[@name=".$iname."]').get(0).value= '';"."\r\n".
					"   } "."\r\n".
					" } "."\r\n".
					" SetDate".$iname."(); "."\r\n".
					"\r\n";
			}
				
			if($dp) {
				if ( $edit_type == "inline" ) {
					$ret.="	function update".$iname."_".$record_id."(newDate) "."\r\n".
					"{ "."\r\n".
					"	var dt_datetime; "."\r\n".
					" 	var curdate = new Date(); "."\r\n".
					"		dt_datetime = newDate;"."\r\n".
					"		$('input#".$iname."_".$record_id."').get(0).value =  dt_datetime.getFullYear() + '-' + (dt_datetime.getMonth()+1) + '-' + dt_datetime.getDate();"."\r\n".
					"		$('select#day".$iname."_".$record_id."').get(0).selectedIndex = dt_datetime.getDate();"."\r\n".
					"		$('select#month".$iname."_".$record_id."').get(0).selectedIndex = dt_datetime.getMonth()+1;"."\r\n".
					"		for(i=0; i<$('select#year".$iname."_".$record_id."').get(0).options.length;i++)".
					"			if($('select#year".$iname."_".$record_id."').get(0).options[i].value==dt_datetime.getFullYear())".
					"			{".
					"				$('select#year".$iname."_".$record_id."').get(0).selectedIndex=i;".
					"				break;".
					"			}".
					"  	$('input#ts".$iname."_".$record_id."').get(0).value = dt_datetime.getDate() + '-' + (dt_datetime.getMonth()+1) + '-' + dt_datetime.getFullYear();"."\r\n".
					"	}"."\r\n";
				} else {
					$ret.="	function update".$iname."(newDate) "."\r\n".
					"{ "."\r\n".
					"	var dt_datetime; "."\r\n".
					" 	var curdate = new Date(); "."\r\n".
					"		dt_datetime = newDate;"."\r\n".
					"		$('input[@name=".$iname."]').get(0).value =  dt_datetime.getFullYear() + '-' + (dt_datetime.getMonth()+1) + '-' + dt_datetime.getDate();"."\r\n".
					"		$('select[@name=day".$iname."]').get(0).selectedIndex = dt_datetime.getDate();"."\r\n".
					"		$('select[@name=month".$iname."]').get(0).selectedIndex = dt_datetime.getMonth()+1;"."\r\n".
					"		for(i=0; i<$('select[@name=year".$iname."]').get(0).options.length;i++)".
					"			if($('select[@name=year".$iname."]').get(0).options[i].value==dt_datetime.getFullYear())".
					"			{".
					"				$('select[@name=year".$iname."]').get(0).selectedIndex=i;".
					"				break;".
					"			}".
					"  	$('input[@name=ts".$iname."]').get(0).value = dt_datetime.getDate() + '-' + (dt_datetime.getMonth()+1) + '-' + dt_datetime.getFullYear();"."\r\n".
					"	}"."\r\n";				
				}
			}
			$ret.=" </script>\r\n";
			echo $ret;
			return;
		case EDIT_DATE_SIMPLE:
		default:
			$ovalue=$value;
			if($time[0])
			{
				if($time[3] || $time[4] || $time[5])
					$ovalue=format_datetime($time);
				else
					$ovalue=format_shortdate($time);
			}
			echo '<input type=text name="'.$iname.'" size = "20" value="'.htmlspecialchars($ovalue).'">';
	}
}

//	create javascript array with values for dependent dropdowns
function BuildSecondDropdownArray( $arrName, $strSQL)
{
	global $conn;

	echo $arrName . "=new Array();\r\n";
	$i=0;
	$rs = db_query($strSQL,$conn);
	while($row=db_fetch_numarray($rs))
	{
		echo $arrName."[".($i*3)."]='".jsreplace($row[0]). "';\r\n";
		echo $arrName."[".($i*3 + 1)."]='".jsreplace($row[1]). "';\r\n";
		echo $arrName."[".($i*3 + 2)."]='".jsreplace($row[2]). "';\r\n";
		$i++;
	}
}

//	create Lookup wizard control
function BuildSelectControl($field, $value, $values="", $secondfield=false, $mode, $id="")
{
	global $conn,$LookupSQL,$strTableName,$useAJAX;
	$LookupSQL ="";
	$strSize = 1;
	$cfieldname=GoodFieldName($field);
	$cfield="value_".GoodFieldName($field);
	$clookupfield="display_value_".GoodFieldName($field);
	$ctype="type_".GoodFieldName($field);
	if($secondfield)
	{
		$cfield="value1_".GoodFieldName($field);
		$ctype="type1_".GoodFieldName($field);
	}
	if($values)
		$arr=&$values;
	$addnewitem=false;

	$script="";
		if($strTableName=="articles" && $field=="Category") 
		{
			$addnewitem= true ;
			$LinkField="Category";
			$DisplayField="Category";
			$LookupTable="categories";
			$strSize=1;

			
			$LookupSQL = "select ";
						$LookupSQL .= "distinct ";
			$LookupSQL .= "`Category`";
						$LookupSQL .= ",`Category`";
						$LookupSQL .= " from `categories` ";
			
						
						$LookupSQL.=" ORDER BY `categories`.`Category`";
			
		}
		if($strTableName=="article" && $field=="Category") 
		{
			$addnewitem= true ;
			$LinkField="Category";
			$DisplayField="Category";
			$LookupTable="categories";
			$strSize=1;

			
			$LookupSQL = "select ";
						$LookupSQL .= "distinct ";
			$LookupSQL .= "`Category`";
						$LookupSQL .= ",`Category`";
						$LookupSQL .= " from `categories` ";
			
						
						$LookupSQL.=" ORDER BY `categories`.`Category`";
			
		}
	
//	multi-select
	$multiple="";
	$postfix="";
	if($strSize>1)
	{
		$avalue=splitvalues($value);
		$multiple=" multiple";
		$postfix="[]";
	}
	else 
		$avalue=array((string)$value);
	
	if($LookupSQL)
	{
//	fast-type control
		if ( FastType($field) && $useAJAX )
		{
			if(UseCategory($field))
			{
//	dependent dropdown
				$clookupfieldid=$clookupfield;
				$categoryFieldId = GoodFieldName(CategoryControl($field));
				if($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD)
				{
					$clookupfieldid.="_".$id;
					$categoryFieldId.="_".$id;
				}
				echo '<input type="text" categoryId="'.$categoryFieldId.'" autocomplete="off" id="'.$clookupfieldid.'" name="'.$clookupfield.'" onkeydown="return listenEvent(event,this,\'lookup\');" onkeyup="lookupSuggest(event,this,\''.htmlspecialchars(jsreplace($value)).'\',\''.$id.'\');" onblur="isSetFocus=false;showHideLookupError(this);" onfocus="isSetFocus=true;" >';
				$onchange="";
		   		if($onchange)
					$onchange="onchange=\"".$onchange."\"";
				$cfieldid=$cfield;
				if($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD)
					$cfieldid.="_".$id;
				echo '<input type="hidden" id="'.$cfieldid.'" name="'.$cfield.'" '.$onchange.'>';
			//	add new item
				return;
			}
//	get the initial value
			$lookup_SQL = "";
			$lookup_value = "";
			
			if($strTableName=="articles" && $field=="Category") 
			{
								$lookup_SQL = "SELECT ";
								$lookup_SQL .= "DISTINCT ";
				$lookup_SQL .= "`Category`";
								$lookup_SQL .= ",`Category`";
				$lookup_SQL .= " FROM `categories` ";
								$lookup_SQL .= " WHERE `Category`=".make_db_value($field,$value)."";
									$lookup_SQL.= " ORDER BY `Category`";
								}
			if($strTableName=="article" && $field=="Category") 
			{
								$lookup_SQL = "SELECT ";
								$lookup_SQL .= "DISTINCT ";
				$lookup_SQL .= "`Category`";
								$lookup_SQL .= ",`Category`";
				$lookup_SQL .= " FROM `categories` ";
								$lookup_SQL .= " WHERE `Category`=".make_db_value($field,$value)."";
									$lookup_SQL.= " ORDER BY `Category`";
								}
			
			$rs_lookup=db_query($lookup_SQL,$conn);
	
			if ( $data = db_fetch_numarray($rs_lookup) ) 
			{	
				$lookup_value = $data[1];
			} 
			else
			{
				if($strTableName=="articles" && $field=="Category") 
				{
										$lookup_SQL = "SELECT ";
										$lookup_SQL .= "DISTINCT ";
					$lookup_SQL .= "`Category`";
										$lookup_SQL .= ",`Category`";
					$lookup_SQL .= " FROM `categories` ";
					$lookup_SQL .= " WHERE `Category`=".make_db_value($field,$value)."";
											$lookup_SQL.= " ORDER BY `Category`";
										}
				if($strTableName=="article" && $field=="Category") 
				{
										$lookup_SQL = "SELECT ";
										$lookup_SQL .= "DISTINCT ";
					$lookup_SQL .= "`Category`";
										$lookup_SQL .= ",`Category`";
					$lookup_SQL .= " FROM `categories` ";
					$lookup_SQL .= " WHERE `Category`=".make_db_value($field,$value)."";
											$lookup_SQL.= " ORDER BY `Category`";
										}
			
				$rs_lookup=db_query($lookup_SQL,$conn);			
				
				if($data = db_fetch_numarray($rs_lookup))
					$lookup_value = $data[1];
			}
//	build the control
			$clookupfieldid=$clookupfield;
			if($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD)
				$clookupfieldid.="_".$id;
			
			
			echo '<input type="text" autocomplete="off" id="'.$clookupfieldid.'" name="'.$clookupfield.'" value="'.htmlspecialchars($lookup_value).'" 	onkeydown="return listenEvent( event,this,\'lookup\');" onkeyup="lookupSuggest(event,this,\''.htmlspecialchars(jsreplace($value)).'\',\''.$id.'\');" onblur="isSetFocus=false;showHideLookupError(this);" onfocus="isSetFocus=true;" >';
			$onchange="";
	   		if($onchange)
				$onchange="onchange=\"".$onchange."\"";
			$cfieldid=$cfield;
			if($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD)
				$cfieldid.="_".$id;
			
			echo '<input type="hidden" id="'.$cfieldid.'" name="'.$cfield.'" value="'.htmlspecialchars($value).'" '.$onchange.'>';
			//	add new item
			if($addnewitem &&  $mode!=MODE_SEARCH)
			{
				$extra="";
				if( $mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD )
					$extra="&mode=".$mode."&id=value_".GoodFieldName($field)."_".$id;
				echo "<a href=# onclick=\"window.open('".GetTableURL($strTableName)."_addnewitem.php?field=".htmlspecialchars(jsreplace(rawurlencode($field))).$extra."',\r\n".
				"'AddNewItem', 'width=250,height=100,status=no,resizable=yes,top=200,left=200');\">\r\n".
				"Add new"."</a>";
			}
		}
		else
		{
			LogInfo($LookupSQL);
			$rs=db_query($LookupSQL,$conn);
			$onchange="";
	   		if($onchange)
				$onchange="onchange=\"".$onchange."\"";
	      	if (true || $useAJAX  && $mode!=MODE_INLINE_EDIT && $mode!=MODE_INLINE_ADD) 
    	  		echo '<select size = "'.$strSize.'" id="'.$cfield.'" name="'.$cfield.$postfix.'"'.$multiple.' '.$onchange.'>';
      		else 
	      		echo '<select size = "'.$strSize.'" name="'.$cfield.$postfix.'"'.$multiple.' '.$onchange.'>';
			if($strSize<2)
				echo '<option value="">'."Please select".'</option>';
			else if($mode==MODE_SEARCH)
				echo '<option value=""> </option>';

	      	$found=false;
			while($data=db_fetch_numarray($rs))
			{
				$res=array_search((string)$data[0],$avalue,true);
				if(!($res===NULL || $res===FALSE))
				{
					$found=true;
	      			echo '<option value="'.htmlspecialchars($data[0]).'" selected>'.htmlspecialchars($data[1]).'</option>';
				}
	      		else
	      			echo '<option value="'.htmlspecialchars($data[0]).'">'.htmlspecialchars($data[1]).'</option>';
			}
			echo "</select>";
//	add new item
			if($addnewitem &&  $mode!=MODE_SEARCH && $mode!=MODE_INLINE_EDIT && $mode!=MODE_INLINE_ADD)
			{
				echo "<a href=# onclick=\"window.open('".GetTableURL($strTableName)."_addnewitem.php?field=".htmlspecialchars(jsreplace(rawurlencode($field)))."',\r\n".
				"'AddNewItem', 'width=250,height=100,status=no,resizable=yes,top=200,left=200');\">\r\n".
				"Add new"."</a>";
			}
			if($addnewitem &&  $mode!=MODE_SEARCH &&  ($mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD))
			{
				echo "<a href=# onclick=\"window.open('".GetTableURL($strTableName)."_addnewitem.php?field=".htmlspecialchars(jsreplace(rawurlencode($field)))."&mode=".$mode."&id=value_".GoodFieldName($field)."_".$id."',\r\n".
				"'AddNewItem', 'width=250,height=100,status=no,resizable=yes,top=200,left=200');\">\r\n".
				"Add new"."</a>";
			}
		}
	}
	else
	{
		echo '<select size = "'.$strSize.'" name="'.$cfield.$postfix.'" '.$multiple.'>';
		if($strSize<2 )
			echo '<option value="">'."Please select".'</option>';
		else if($mode==MODE_SEARCH)
			echo '<option value=""> </option>';
		foreach($arr as $opt)
		{
			$res=array_search((string)$opt,$avalue,true);
			if(!($res===NULL || $res===FALSE))
      			echo '<option value="'.htmlspecialchars($opt).'" selected>'.htmlspecialchars($opt).'</option>';
			else
      			echo '<option value="'.htmlspecialchars($opt).'">'.htmlspecialchars($opt).'</option>';
		}
		echo "</select>";
	}
	return;
}

function BuildRadioControl($field, $value,$secondfield=false,$id="")
{
	global $conn,$LookupSQL,$strTableName;
	$cfieldname=GoodFieldName($field);
	$cfield="value_".GoodFieldName($field);
	$cfieldid="value_".GoodFieldName($field);
	$ctype="type_".GoodFieldName($field);
	if($id<>"")
	{
		$cfieldname.="_".$id;
		$cfieldid.="_".$id;
	}
	if($secondfield)
	{
		$cfield="value1_".GoodFieldName($field);
		$ctype="type1_".GoodFieldName($field);
	}
	$LookupSQL ="";
	if($strTableName=="comments" && "access"==$field)
	{
					$arr=array(
				 "public" 				,  "private" 
			);
	}
	if($strTableName=="comment" && "access"==$field)
	{
					$arr=array(
				 "public" 				,  "private" 
			);
	}
	if($LookupSQL)
	{
      	LogInfo($LookupSQL);
		$rs=db_query($LookupSQL,$conn);
		if(!db_numrows($rs))
			return "";
		echo '<input type=hidden name="'.$cfield.'" value="'.htmlspecialchars($value).'" id="'.$cfieldid.'">';
      	while($data=db_fetch_numarray($rs))
		{
			$checked="";
			if($data[0]==$value)
				$checked=" checked";
			echo "<input type=\"Radio\" name=\"radio_".$cfieldname."\" onclick=\"javascript: $('#".$cfieldid."')[0].value='".htmlspecialchars($data[0])."'; return true;\" ".$checked.">".htmlspecialchars($data[1])."<br>";
		}
	}
	else
	{
		echo '<input type=hidden name="'.$cfield.'" value="'.htmlspecialchars($value).'" id="'.$cfieldid.'">';
		foreach($arr as $opt)
		{
			$checked="";
			if($opt==$value)
				$checked=" checked";
			echo "<input type=\"Radio\" name=\"radio_".$cfieldname."\" onclick=\"javascript: $('#".$cfieldid."')[0].value='".htmlspecialchars($opt)."'; return true;\" ".$checked.">".htmlspecialchars($opt)."<br>";
//			echo '<input type="Radio" name="radio_'.$cfieldname.'" onclick="javascript: $("#'.$cfield."\")[0].value='".db_addslashes($opt).'\'; return true;" '.$checked.'>'.htmlspecialchars($opt)."<br>";
		}
	}
	return;

}


function BuildEditControl($field , $value, $format, $edit, $secondfield=false, $id="")
{
	global $rs,$data,$strTableName,$filenamelist,$keys;
	$cfieldname=GoodFieldName($field);
	$cfield="value_".GoodFieldName($field);
	$ctype="type_".GoodFieldName($field);
	if($secondfield)
	{
		$cfield="value1_".GoodFieldName($field);
		$ctype="type1_".GoodFieldName($field);
	}
	$type=GetFieldType($field);
	$arr="";

	$iquery="field=".rawurlencode($field);
	$keylink="";
	if($strTableName=="comments")
	{
		$keylink.="&key1=".rawurlencode($keys["CommentID"]);
		$iquery.=$keylink;
	}
	if($strTableName=="main")
	{
		$keylink.="&key1=".rawurlencode($keys["CategoryID"]);
		$iquery.=$keylink;
	}
	if($strTableName=="articles")
	{
		$keylink.="&key1=".rawurlencode($keys["ArticleID"]);
		$iquery.=$keylink;
	}
	if($strTableName=="article")
	{
		$keylink.="&key1=".rawurlencode($keys["ArticleID"]);
		$iquery.=$keylink;
	}
	if($strTableName=="users")
	{
		$keylink.="&key1=".rawurlencode($keys["CategoryID"]);
		$iquery.=$keylink;
	}
	if($strTableName=="comment")
	{
		$keylink.="&key1=".rawurlencode($keys["CommentID"]);
		$iquery.=$keylink;
	}
	if($strTableName=="register")
	{
		$keylink.="&key1=".rawurlencode($keys["email"]);
		$iquery.=$keylink;
	}
	if($strTableName=="_register")
	{
		$keylink.="&key1=".rawurlencode($keys["email"]);
		$iquery.=$keylink;
	}
	if($format==EDIT_FORMAT_FILE && $edit==MODE_SEARCH)
		$format="";
	if($format==EDIT_FORMAT_TEXT_FIELD)
	{
		if(IsDateFieldType($type))
			echo '<input type="hidden" name="'.$ctype.'" value="date'.EDIT_DATE_SIMPLE.'">'.GetDateEdit($field,$value,0,$secondfield,$edit);
		else
	    {
			if($edit==MODE_SEARCH)
				echo '<input type="text" autocomplete="off" name="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($value).'">';
			else
				echo '<input type="text" name="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($value).'">';
		}
	}
	else if($format==EDIT_FORMAT_TIME)
	{
		echo '<input type="hidden" name="'.$ctype.'" value="time">';
		if(IsDateFieldType($type))
		{
			$dbtime=db2time($value);
		
			if(count($dbtime))
				$val=format_time($dbtime);
			else
				$val="";
		}
		else 
		{
			$arr=parsenumbers($value);
			if(count($arr))
			{
				$dbtime=array(0,0,0);
				while(count($arr)<3)
					$arr[]=0;
				$dbtime[]=$arr[0];
				$dbtime[]=$arr[1];
				$dbtime[]=$arr[2];
				$val=format_time($dbtime);
			}
			else
				$val="";
		}
		echo '<input type="text" name="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($val).'">';
	}
	else if($format==EDIT_FORMAT_TEXT_AREA)
	{
	
		$nWidth = GetNCols($field);
		$nHeight = GetNRows($field);
		if(UseRTE($field))
		{
			$value = RTESafe($value);

			
						if($edit==MODE_INLINE_EDIT || $edit==MODE_INLINE_ADD)
			{
				$cfield.="_".$id;
				$cfieldname.="_".$id;
				$browser="";
				if(@$_REQUEST["browser"]=="ie")
					$browser="&browser=ie";
				echo "<iframe scrolling=no name=\"".$cfield."\" style='width: " . ($nWidth+1) . "px;height: " . ($nHeight+1) . "px;'";
				echo " src=\"".GetTableURL($strTableName)."_rte.php?id=".$id."&".$iquery.$browser."\">";
				echo "</iframe>";
			}
			else
			{
				echo "<script language=\"JavaScript\" type=\"text/javascript\">\r\n".
				"writeRichText('".$cfield."', '".$value."', " . $nWidth . ", " . $nHeight . ", true, false);\r\n".
				"</script>";
			}
									
		}
		else
			echo '<textarea name="'.$cfield.'" style="width: ' . $nWidth . 'px;height: ' . $nHeight . 'px;">'.htmlspecialchars($value).'</textarea>';
	}
	else if($format==EDIT_FORMAT_PASSWORD)
		echo '<input type="Password" name="'.$cfield.'" '.GetEditParams($field).' value="'.htmlspecialchars($value).'">';
	else if($format==EDIT_FORMAT_DATE)
		echo '<input type="hidden" name="'.$ctype.'" value="date'.DateEditType($field).'">'.GetDateEdit($field,$value,DateEditType($field),$secondfield,$edit);
	else if($format==EDIT_FORMAT_RADIO)
		BuildRadioControl($field,$value,$secondfield,$id);
	else if($format==EDIT_FORMAT_CHECKBOX)
	{
		if($edit==MODE_ADD || $edit==MODE_INLINE_ADD || $edit==MODE_EDIT || $edit==MODE_INLINE_EDIT)
		{
			$checked="";
			if($value && $value!=0)
				$checked=" checked";
			echo '<input type="hidden" name="'.$ctype.'" value="checkbox"><input type="Checkbox" name="'.$cfield.'" '.$checked.'>';
		}
		else
		{
			echo '<input type="hidden" name="'.$ctype.'" value="checkbox">';
			echo '<select name="'.$cfield.'">';
			$val=array("none","on","off");
			$show=array("","True","False");
			foreach($val as $i=>$v)
			{
				$sel="";
				if($value===$v)
					$sel=" selected";
				echo '<option value="'.$v.'"'.$sel.'>'.$show[$i].'</option>';
			}
			echo "</select>";
		}
	}
	else if($format==EDIT_FORMAT_DATABASE_IMAGE || $format==EDIT_FORMAT_DATABASE_FILE)
	{
		$disp="";
		$strfilename="";
		$onchangefile="";
		if($edit==MODE_EDIT || $edit==MODE_INLINE_EDIT)
		{
			if($id<>"")
				$ctype.="_".$id;
			$value=db_stripslashesbinary($value);
			$itype=SupposeImageType($value);
			$thumbnailed=false;
			$thumbfield="";

			if($itype)
			{
				if($thumbnailed)
				{
					$disp="<a target=_blank href=\"".GetTableURL($strTableName)."_imager.php?".$iquery."\">";
					$disp.= "<img name=\"".$cfield."\" border=0";
					$disp.=" src=\"".GetTableURL($strTableName)."_imager.php?field=".rawurlencode($thumbfield)."&alt=".rawurlencode($field).$keylink."\">";
					$disp.= "</a>";
				}
				else
					$disp='<img name="'.$cfield.'" border=0 src="'.GetTableURL($strTableName).'_imager.php?'.$iquery.'">';
			}
			else
			{
				if(strlen($value))
					$disp='<img name="'.$cfield.'" border=0 src="images/file.gif">';
				else
					$disp='<img name="'.$cfield.'" border=0 src="images/no_image.gif">';
			}
//	filename
			if($format==EDIT_FORMAT_DATABASE_FILE && !$itype && strlen($value))
			{
				if(!($filename=@$data[GetFilenameField($field)]))
					$filename="file.bin";
				$disp='<a href="'.GetTableURL($strTableName).'_getfile.php?filename='.htmlspecialchars($filename).'&'.$iquery.'".>'.$disp.'</a>';
			}
//	filename edit
			if($format==EDIT_FORMAT_DATABASE_FILE && GetFilenameField($field))
			{
				if(!($filename=@$data[GetFilenameField($field)]))
					$filename="";
				if($edit==MODE_INLINE_EDIT)
				{
					$strfilename='<br>'."Filename".'&nbsp;&nbsp;<input id="filename_'.$cfieldname.'_'.$id.'" name="filename_'.$cfieldname.'" size="20" maxlength="50" value="'.htmlspecialchars($filename).'">';
					$onchangefile.="var path=$('#".$cfield."_".$id."').val(); var wpos=path.lastIndexOf('\\\\'); var upos=path.lastIndexOf('/'); var pos=wpos; if(upos>wpos) pos=upos; $('#filename_".$cfieldname."_".$id."').val(path.substr(pos+1));";
				}
				else
				{
					$strfilename='<br>'."Filename".'&nbsp;&nbsp;<input name="filename_'.$cfieldname.'" size="20" maxlength="50" value="'.htmlspecialchars($filename).'">';
					$onchangefile.="var path=this.form.elements['".jsreplace($cfield)."'].value; var wpos=path.lastIndexOf('\\\\'); var upos=path.lastIndexOf('/'); var pos=wpos; if(upos>wpos) pos=upos; this.form.elements['filename_".jsreplace($cfieldname)."'].value=path.substr(pos+1);";
				}
			}
			$strtype='<br><input type="Radio" name="'.$ctype.'" value="file0" checked>'."Keep";
			if(strlen($value) && !IsRequired($field))
			{
				$strtype.='<input type="Radio" name="'.$ctype.'" value="file1">'."Delete";
				if ($edit==MODE_INLINE_EDIT ) {
					$onchangefile.='$(\'input[@type=radio][@value=file2][@name='.$ctype.']\').get(0).checked=true;';
				} else {				
					$onchangefile.='this.form.elements[\''.jsreplace($ctype).'\'][2].checked=true;';
				}
			}
			else {
				if ($edit==MODE_INLINE_EDIT) {
					$onchangefile.='$(\'input[@type=radio][@value=file2][@name='.$ctype.']\').get(0).checked=true;';
				} else {			
					$onchangefile.='this.form.elements[\''.jsreplace($ctype).'\'][1].checked=true;';
				}
			}
			
			$strtype.='<input type="Radio" name="'.$ctype.'" value="file2">'."Update";
		}
		else
		{
//	if Add mode
			$strtype='<input type="hidden" name="'.$ctype.'" value="file2">';
			if($format==EDIT_FORMAT_DATABASE_FILE && GetFilenameField($field))
			{
				$strfilename='<br>'."Filename".'&nbsp;&nbsp;<input name="filename_'.$cfieldname.'" size="20" maxlength="50">';
				if($edit==MODE_INLINE_ADD)
					$onchangefile.="var path=$('#".$cfield."_".$id."').val(); var wpos=path.lastIndexOf('\\\\'); var upos=path.lastIndexOf('/'); var pos=wpos; if(upos>wpos) pos=upos; $('#filename_".$cfieldname."_".$id."').val(path.substr(pos+1));";
				else
					$onchangefile.="var path=this.form.elements['".jsreplace($cfield)."'].value; var wpos=path.lastIndexOf('\\\\'); var upos=path.lastIndexOf('/'); var pos=wpos; if(upos>wpos) pos=upos; this.form.elements['filename_".jsreplace($cfieldname)."'].value=path.substr(pos+1);";
			}
		}
		if($onchangefile)
			$onchangefile='onChange="'.$onchangefile.'"';
		if($edit==MODE_INLINE_EDIT && $format==EDIT_FORMAT_DATABASE_FILE)
			$disp="";
		echo $disp.$strtype.'<br><input type="File" id="'.$cfield."_".$id.'" name="'.$cfield.'" '.$onchangefile.'>'.$strfilename;
	}
	else if($format==EDIT_FORMAT_LOOKUP_WIZARD)
			BuildSelectControl($field, $value, $arr, $secondfield, $edit,$id);
	else if($format==EDIT_FORMAT_HIDDEN)
			echo '<input type="Hidden" name="'.$cfield.'" value="'.htmlspecialchars($value).'">';
	else if($format==EDIT_FORMAT_READONLY)
			echo '<input type="Hidden" name="'.$cfield.'" value="'.htmlspecialchars($value).'">';
	else if($format==EDIT_FORMAT_FILE)
	{
		$disp="";
		$strfilename="";
		$onchangefile="";
		$function="";
		if($edit==MODE_EDIT || $edit==MODE_INLINE_EDIT)
		{
//	show current file
			if($id<>"")
				$ctype.="_".$id;
			if(Format($field)==FORMAT_FILE || Format($field)==FORMAT_FILE_IMAGE)
			{
				$disp=GetData($data,$field,Format($field))."<br>";
			}
			$filename=$value;
			if ( $edit==MODE_INLINE_EDIT ) {
				$function="";
			} else {
				$function='<script language="Javascript">
				function controlfilename'.$cfieldname.'(enable)
				{
					if(enable)
					{
						document.forms.editform.'.$cfield.'.style.backgroundColor="white";
						document.forms.editform.'.$cfield.'.disabled=false;
					}
					else
					{
						document.forms.editform.'.$cfield.'.style.backgroundColor="gainsboro";
						document.forms.editform.'.$cfield.'.disabled=true;
					}
				}
				</script>';
			}
//	filename edit
			$filename_size=30;
			if(UseTimestamp($field))
				$filename_size=50;
			$strfilename='<input type=hidden name="filename_'.$cfieldname.'" value="'.htmlspecialchars($filename).'"><br>'."Filename".'&nbsp;&nbsp;<input style="background-color:gainsboro" disabled id="'.$cfield.'_'.$id.'" name="'.$cfield.'" size="'.$filename_size.'" maxlength="100" value="'.htmlspecialchars($filename).'">';
			if ( $edit==MODE_INLINE_EDIT ) {
				$onchangefile.="var path=$('[@id=file_".$cfieldname."_".$id."]').val(); var wpos=path.lastIndexOf('\\\\'); var upos=path.lastIndexOf('/'); var pos=wpos; if(upos>wpos) pos=upos; $('#".$cfield."_".$id."').css('backgroundColor','white');$('#".$cfield."_".$id."')[0].disabled=false;";
				if(UseTimestamp($field))
					$onchangefile.="$('[@id=".$cfield."_".$id."]').val(addTimestamp(path.substr(pos+1))); ";
				else
					$onchangefile.="$('[@id=".$cfield."_".$id."]').val(path.substr(pos+1)); ";
				$strtype='<br><input type="Radio" name="'.$ctype.'" value="upload0" checked onclick="$(\'[@id='.$cfield.'_'.$id.']\').css(\'backgroundColor\',\'gainsboro\');$(\'[@id='.$cfield.'_'.$id.']\')[0].disabled=true;">'."Keep";
			} else {
				$onchangefile.="var path=this.form.file_".$cfieldname.".value; var wpos=path.lastIndexOf('\\\\'); var upos=path.lastIndexOf('/'); var pos=wpos; if(upos>wpos) pos=upos; controlfilename".$cfieldname."(true);";
				if(UseTimestamp($field))
					$onchangefile.="this.form.".$cfield.".value=addTimestamp(path.substr(pos+1)); ";
				else
					$onchangefile.="this.form.".$cfield.".value=path.substr(pos+1); ";
				$strtype='<br><input type="Radio" name="'.$ctype.'" value="upload0" checked onclick="controlfilename'.$cfieldname.'(false)">'."Keep";
			}


			if(strlen($value) && !IsRequired($field))
			{
				if ($edit==MODE_INLINE_EDIT) {
					$strtype.='<input type="Radio" name="'.$ctype.'" value="upload1" onclick="$(\'[@id='.$cfield.'_'.$id.']\').css(\'backgroundColor\',\'gainsboro\');$(\'[@id='.$cfield.'_'.$id.']\')[0].disabled=true;">'."Delete";
					$onchangefile.='$(\'input[@type=radio][@value=upload2][@name='.$ctype.']\').get(0).checked=true;';
				} else {
					$strtype.='<input type="Radio" name="'.$ctype.'" value="upload1" onclick="controlfilename'.$cfieldname.'(false)">'."Delete";
					$onchangefile.='this.form.'.$ctype.'[2].checked=true;';
				}
			}
			else {
				if ($edit==MODE_INLINE_EDIT) {
					$onchangefile.='$(\'input[@type=radio][@value=upload2][@name='.$ctype.']\').get(0).checked=true;';
				} else {			
					$onchangefile.='this.form.'.$ctype.'[1].checked=true;';
				}
			}
			if ($edit==MODE_INLINE_EDIT) {
				$strtype.='<input type="Radio" name="'.$ctype.'" value="upload2" onclick="$(\'[@id='.$cfield.'_'.$id.']\').css(\'backgroundColor\',\'white\');$(\'[@id='.$cfield.'_'.$id.']\')[0].disabled=false;">'."Update";
			} else {
				$strtype.='<input type="Radio" name="'.$ctype.'" value="upload2" onclick="controlfilename'.$cfieldname.'(true)">'."Update";
			}
		}
		else
		{
//	if Adding record		
			$filename_size=30;
			if(UseTimestamp($field))
				$filename_size=50;
			$strtype='<input type="hidden" name="'.$ctype.'" value="upload2">';
			$strfilename='<br>'."Filename".'&nbsp;&nbsp;<input name="'.$cfield.'" size="'.$filename_size.'" maxlength="100">';
			if($edit==MODE_INLINE_ADD)
			{
				$onchangefile.="var path=$('[@id=file_".$cfieldname."_".$id."]').val(); var wpos=path.lastIndexOf('\\\\'); var upos=path.lastIndexOf('/'); var pos=wpos; if(upos>wpos) pos=upos;";
				if(UseTimestamp($field))
					$onchangefile.=" $('[@id=".$cfield."_".$id."]').val(addTimestamp(path.substr(pos+1)));";
				else
					$onchangefile.=" $('[@id=".$cfield."_".$id."]').val(path.substr(pos+1));";
			}
			else
			{
				$onchangefile.="var path=this.form.file_".$cfieldname.".value; var wpos=path.lastIndexOf('\\\\'); var upos=path.lastIndexOf('/'); var pos=wpos; if(upos>wpos) pos=upos;";
				if(UseTimestamp($field))
					$onchangefile.=" this.form.".$cfield.".value=addTimestamp(path.substr(pos+1));";
				else
					$onchangefile.=" this.form.".$cfield.".value=path.substr(pos+1);";
			}
		}
		if($onchangefile)
			$onchangefile='onChange="'.$onchangefile.'"';
		echo $function.$disp.$strtype.'<br><input type="File" id="file_'.$cfieldname.'" name="file_'.$cfieldname.'" '.$onchangefile.'>'.$strfilename;
	}
}
function my_stripos($str,$needle, $offest)
{
    if (strlen($needle)==0 || strlen($str)==0)
		return false;
	return strpos(strtolower($str),strtolower($needle), $offest);
} 

function in_assoc_array($name, $arr)
{
foreach ($arr as $key => $value) 
	if ($key==$name)
		return true;

return false;
}

function loadSelectContent($field, $value,$fvalue="")
{
	global $conn,$LookupSQL,$strTableName;
	
	$Lookup = "";
	$response = array();
	$output = "";

	$rs=db_query($LookupSQL,$conn);

	if(!FastType($field))
	{
		while ($data = db_fetch_numarray($rs)) 
		{
			$response[] = $data[0];
			$response[] = $data[1];
		}
	}
	else
	{
		$data=db_fetch_numarray($rs);
//	one record only
		if($data && (strlen($fvalue) || !db_fetch_numarray($rs)))
		{
			$response[] = $data[0];
			$response[] = $data[1];
		}
	}
	return $response;
}

function xmlencode($str)
{

	$str = str_replace("&","&amp;",$str);
	$str = str_replace("<","&lt;",$str);
	$str = str_replace(">","&gt;",$str);

	$out="";
	$len=strlen($str);
	$ind=0;
	for($i=0;$i<$len;$i++)
	{
		if(ord($str[$i])>=128)
		{
			$out.="&#".ord($str[$i]).";";
			if($ind<$i)
				$out.=substr($str,$ind,$i-$ind);
			$ind=$i+1;
		}
	}
	if($ind<$len)
		$out.=substr($str,$ind);
	return str_replace("'","&apos;",$out);

}

function print_inline_array(&$arr,$printkey=false)
{
	if(!$printkey)
	{
		foreach ( $arr as $key=>$val )
			echo str_replace(array("&","<","\\","\r","\n"),array("&amp;","&lt;","\\\\","\\r","\\n"),str_replace(array("\\","\r","\n"),array("\\\\","\\r","\\n"),$val))."\\n";
	}
	else
	{
		foreach ( $arr as $key=>$val )
			echo str_replace(array("&","<","\\","\r","\n"),array("&amp;","&lt;","\\\\","\\r","\\n"),str_replace(array("\\","\r","\n"),array("\\\\","\\r","\\n"),$key))."\\n";
	}
		
}
?>
