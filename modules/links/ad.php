<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

header("Content-type: text/javascript");
$id = menu_save(@$_GET['id']);
$q = "SELECT * FROM links_ad WHERE name='$id' LIMIT 1";
$ad= $db->cacheGetRow($q);
if(!empty($ad['publish']))
{
	if(!empty($ad['content']))
	{
		?>function c(a){var b='';for(var i=0;i<a.length;i+=2)b+=String.fromCharCode(parseInt(a.substr(i,2),16));return b;};document.write(c('<?php echo bin2hex(str_replace("\r", '', $ad['content']));?>'));<?php
	}
	if(!empty($ad['javascript']))
	{
		echo $ad['javascript'];
	}
}
exit;
