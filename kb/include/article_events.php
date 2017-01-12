<?php





































function AddOnLoad(&$params)
{
global $smarty;
$smarty->assign("value_Category",@$_REQUEST["SearchFor"]);

}












// After record added
function AfterAdd(&$values,&$keys,$inline)
{
//**********  Redirect to another page  ************
if(@$_REQUEST["editType"]!="inline")
{
header("Location: articles_list.php?a=search&value=1&SearchFor=".@$_REQUEST["SearchFor"]."&SearchOption=Contains&SearchField=Category");
exit();
}

} // function AfterAdd
































































function ListOnLoad(&$params)
{
global $smarty;
$smarty->assign("row11Category_value",@$_REQUEST["SearchFor"]);

}

















// After record updated
function AfterEdit(&$values, $where, &$oldvalues, &$keys,$inline)
{
//**********  Redirect to another page  ************
if(@$_REQUEST["editType"]!="inline")
{
header("Location: articles_list.php?a=search&value=1&SearchFor=".@$_REQUEST["SearchFor"]."&SearchOption=Contains&SearchField=Category");
exit();
}

} // function AfterEdit



























































function ViewOnLoad(&$params)
{
global $smarty;
$smarty->assign("allow_edit",CheckSecurity(@$_SESSION["OwnerID"],"Edit"));

global $strWhereClause,$conn;
$str = "select * from articles where ".$strWhereClause;
$rs = db_query($str,$conn);
$data = db_fetch_array($rs);
$_SESSION["comments_masterkey1"] = $data["ArticleID"];
$_SESSION["comments_mastertable"] = "articles";


$views=$data["Views"]+1;
$strUpdate = "update articles set Views=".$views." where ". $strWhereClause;
db_exec($strUpdate,$conn);

}

?>