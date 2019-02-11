<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!$sys->menu_real)
{
	$sys->nav_change(lang('Guest Book'));
}
$conf = get_config('guestbook', 'guestbook');
echo '<h1>'.lang('guestbook').'</h1>';
if(!$sys->menu_real)
{
	$sys->nav_add(lang('guestbook'));
}
if($conf['animated'])
{
	$q	= "SELECT COUNT(*) FROM guestbook WHERE publish=1 ";
	$found= $db->getOne($q);
	echo page_ajax($found, $conf['tot'], _URL.'guestbook/list_show/');
}else{
	include 'list_show.php';
}
include tpl('list.html.php');
