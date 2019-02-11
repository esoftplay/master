<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

preg_match('~^(.*?)([0-9]+)?$~', @$_GET['id'], $match);

$prefix = @$match[1];
if(@is_numeric($match[2]))
{
	$del_id		= $match[2];
	$av_menu	= array();
	$r				= @$_SESSION[$prefix.'content_category_menu'];
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
	$_SESSION[$prefix.'content_category_menu'] = $av_menu;
	include 'category-form-menu-available.php';
	$sys->stop();
}