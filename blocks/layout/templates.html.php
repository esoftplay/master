<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$q = "SELECT name FROM bbc_template WHERE 1 ORDER BY id ASC";
$r = $db->cacheGetCol($q);
$template		= substr($sys->layout_fetch(), 0,-1);
$r_template = array();
foreach($r AS $d)
{
	$v = ucwords(strtolower(str_replace('_', ' ', $d)));
	$r_template[$d] = $v;
}
$user_url = !empty($_POST['url']) ? $_POST['url'] : seo_uri();
?>
<form name="template_form" id="template_form<?php echo $block->id;?>" action="user/option" method="POST" target="_parent">
	<p class="text text-justify">
		<?php echo lang('select template option'); ?>
	</p> 
	<select class="form-control" name="template" onchange="document.forms['template_form'].submit();return false;">
		<?php echo createOption($r_template, $template);?>
	</select>
	<input type="hidden" name="back" value="<?php echo $user_url;?>" />
</form>
