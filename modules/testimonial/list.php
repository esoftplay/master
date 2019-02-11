<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!$sys->menu_real)
{
	$sys->nav_change(lang('Testimonial'));
}
$conf = get_config('testimonial', 'testimonial');
echo '<h1>'.lang('Testimonial').'</h1>';
if(!$sys->menu_real)
	$sys->nav_add(lang('Testimonial'));
if($conf['animated'])
{
	$q	= "SELECT COUNT(*) FROM testimonial WHERE publish=1 ";
	$found= $db->getOne($q);
	echo page_ajax($found, $conf['tot'], $Bbc->mod['circuit'].'.list_show&id=');
}else{
	include 'list_show.php';
}
include tpl('list.html.php');
