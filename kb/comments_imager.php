<?php 
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
set_magic_quotes_runtime(0);

include("include/dbcommon.php");
include("include/comments_variables.php");
if(!@$_SESSION["UserID"] || !CheckSecurity(@$_SESSION["_".$strTableName."_OwnerID"],"Search"))
{ 
	header("Location: login.php"); 
	return;
}

$field = @$_GET["field"];
if(!CheckFieldPermissions($field))
	return DisplayNoImage();

//	construct sql

$keys=array();
$keys["CommentID"]=postvalue("key1");
$where=KeyWhere($keys);

//$sql=$gstrSQL;
//$sql = AddWhere($sql,$where);

$conn=db_connect();


$sql = gSQLWhere($where);

$rs = db_query($sql,$conn);
if(!$rs || !($data=db_fetch_array($rs)))
  return DisplayNoImage();


$value=db_stripslashesbinary($data[$field]);
if(!$value)
{
	if(@$_GET["alt"])
	{
		$value=db_stripslashesbinary($data[$_GET["alt"]]);
		if(!$value)
			return DisplayNoImage();
	}
	else
		return DisplayNoImage();
}

$itype=SupposeImageType($value);
if($itype)
	header("Content-type: $itype");
else
	return DisplayFile();
echobig($value);
return;


function DisplayNoImage()
{
	$img=myfile_get_contents("images/no_image.gif");
	header("Content-type: image/gif");
	echo $img;
}

function DisplayFile()
{
	$img=myfile_get_contents("images/file.gif");
	header("Content-type: image/gif");
	echo $img;
}

function echobig($string, $bufferSize = 8192)
{
	for ($chars=strlen($string)-1,$start=0;$start <= $chars;$start += $bufferSize) 
		echo substr($string,$start,$bufferSize);
}

?>
