<?php

































// List page: Before record processed
function BeforeProcessRowList(&$data)
{
//**********  Custom code  ************
// put your custom code here
if(isset($categ))
{
	global $categ;
	$categ="";
}
return true; 

// return true if you like to display row on in the list
// return false in other case 


} // function BeforeProcessRowList







































// List page: After record processed
function BeforeMoveNextList(&$data,&$row,$col)
{
//**********  Custom code  ************
// put your custom code here
global $categ;
		if(GetData($data,"Category", "Custom")!=$categ)
		{
			$row[$col."categ_value"]=true;
			$categ=GetData($data,"Category", "Custom");
		}
		else
		{
			$row[$col."categ_value"]=false;
		}

} // function BeforeMoveNextList





























// List page: Before display
function BeforeShowList(&$smarty,&$templatefile)
{
//**********  Custom code  ************
// put your custom code here
global $categ;
$categ="";

} // function BeforeShowList













?>