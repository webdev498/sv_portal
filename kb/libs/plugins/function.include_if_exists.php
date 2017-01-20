<?php 
function smarty_function_include_if_exists($params, &$smarty)
{
	global $data;
	$file=$params["file"];
	if(file_exists($file))
		@include($file);
}
?>