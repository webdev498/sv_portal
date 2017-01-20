<?php 
function smarty_function_show_chart($params, &$smarty)
{
?>
<object 
classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" 
width="<?php echo $params["width"];?>" 
height="<?php echo $params["height"];?>" 
align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="libs/swf/<?php echo GetChartType($params["name"]);?>.swf?XMLFile=<?php echo $params["name"];?>_chartdata.php%3Fwidth%3D<?php echo $params["width"];?>%26height%3D<?php echo $params["height"];?>" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" /> 
<embed src="libs/swf/<?php echo GetChartType($params["name"]);?>.swf?XMLFile=<?php echo $params["name"];?>_chartdata.php%3Fwidth%3D<?php echo $params["width"];?>%26height%3D<?php echo $params["height"];?>" quality="high" bgcolor="#ffffff" 
width="<?php echo $params["width"];?>" height="<?php echo $params["height"];?>" name="RELEASE" align="middle" allowScriptAccess="sameDomain" 
type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
<?php
	if(function_exists($params["name"]))
		eval($params["name"]."();");
}
?>