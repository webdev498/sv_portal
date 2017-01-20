<?php 
function smarty_function_doevent($params, &$smarty)
{
	if(function_exists($params["name"]))
		eval($params["name"].'($params);');
}
?>