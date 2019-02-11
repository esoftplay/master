<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

# modules/content/category.php
$sys->no_tpl = 1;
if (!empty($subcat['publish']))
{
	$data          = $subcat;
	$data['image'] = content_src($cat['image'], false, true);
	$data_output   = _cpanel_result($data);
}