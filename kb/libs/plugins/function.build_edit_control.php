<?php 
function smarty_function_build_edit_control($params, &$smarty)
{
	$field=$params["field"];
	if($params["mode"]=="edit")
		$mode=MODE_EDIT;
	else if($params["mode"]=="add")
		$mode=MODE_ADD;
	else if($params["mode"]=="inline_edit")
		$mode=MODE_INLINE_EDIT;
	else if($params["mode"]=="inline_add")
		$mode=MODE_INLINE_ADD;
	else
		$mode=MODE_SEARCH;
	$second=false;
	if(@$params["second"])
		$second=true;
	$id="";
	if(@$params["id"]!="")
		$id=$params["id"];
	$format=GetEditFormat($field);
	if(($mode==MODE_EDIT || $mode==MODE_ADD || $mode==MODE_INLINE_EDIT || $mode==MODE_INLINE_ADD) && $format==EDIT_FORMAT_READONLY)
	{
		global $readonlyfields;
		echo $readonlyfields[$field];
	}
	if($mode==MODE_SEARCH)
	{
		global $editformats;
		$format=$editformats[$field];
	}
	BuildEditControl($field,@$params["value"],$format,$mode,$second,$id);

}
?>