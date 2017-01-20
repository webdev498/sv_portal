<?php





































function AddOnLoad(&$params)
{
global $smarty;
$smarty->assign("Category",@$_REQUEST["SearchFor"]);

}












// After record added
function AfterAdd(&$values,&$keys,$inline)
{
//**********  Redirect to another page  ************
if(@$_REQUEST["editType"]!="inline")
{
header("Location: comments_list.php?a=return&SearchFor=".@$_REQUEST["SearchFor"]);
exit();
}
} // function AfterAdd
































































function ListOnLoad(&$params)
{
global $smarty;
$smarty->assign("Category",@$_REQUEST["SearchFor"]);

}

?>