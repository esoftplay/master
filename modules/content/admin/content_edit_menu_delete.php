<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

preg_match('~^(.*?)([0-9]+)?$~', @$_GET['id'], $match);
$form_act = @$match[1];
if(@is_numeric($match[2]))
{
	$del_id		= $match[2];
	$av_menu	= array();
	$r				= @$_SESSION[$form_act.'content_menus_exists'];
	foreach((array)$r AS $id => $dt)
	{
		if($id != $del_id )
		{
			$av_menu[] = $dt;
		}else
		if($dt['code'] != 'new')
		{
			$dt['code'] = 'delete';
			$av_menu[] = $dt;
		}
	}
	$_SESSION[$form_act.'content_menus_exists'] = $av_menu;
	include 'content_edit-menu-exists.php';
	$sys->stop();
}