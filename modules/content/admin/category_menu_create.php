<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if(isset($_POST['title'][lang_id()]) && isset($_POST['prefix']))
{
	$prefix		= $_POST['prefix'];
	$av_menu = @$_SESSION[$prefix.'content_category_menu'];
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
	$_SESSION[$prefix.'content_category_menu'] = $av_menu;
	include 'category-form-menu-available.php';
	$sys->stop();
}
