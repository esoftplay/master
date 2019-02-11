<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if(isset($_POST['title'][lang_id()]) && isset($_POST['form_act']))
{
	$form_act		= $_POST['form_act'];
	$av_menu		= @$_SESSION[$form_act.'content_menus_exists'];
	$av_menu[]	= array(
		'code'	=>	'new'
	,	'id'		=> '0'
	,	'par_id'=>	$_POST['par_id']
	,	'cat_id'=>	$_POST['cat_id']
	,	'title'	=>	$_POST['title'][lang_id()]
	,	'seo'		=>	$_POST['seo']
	,	'orderby'=>	$_POST['orderby']
	,	'link'	=>	'none'
	,	'active'=>	2
	,	'titles'=>	$_POST['title']
	);
	$_SESSION[$form_act.'content_menus_exists'] = $av_menu;
	include 'content_edit-menu-exists.php';
	$sys->stop();
}
