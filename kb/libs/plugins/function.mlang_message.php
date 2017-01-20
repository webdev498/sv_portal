<?php
function smarty_function_mlang_message($params, &$smarty)
{
	return htmlspecialchars(mlang_message($params["tag"]));
}
?>