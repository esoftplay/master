<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$page = @intval($_GET['page']);
$type = array();
if(empty($_GET['id']))
{
    $q  = "SELECT * FROM `bbc_content_type` WHERE active=1 LIMIT 1";
    $type= $db->cacheGetRow($q);
    $id = $type['id'];
}else{
    $id = intval($_GET['id']);
    $q  = "SELECT * FROM `bbc_content_type` WHERE id=$id";
    $type= $db->cacheGetRow($q);
}
if(!empty($type['active']))
{
	if(!$sys->menu_real)
	{
		$sys->nav_change(lang($type['title']));
	}
	$config = config_decode($type['list']);
	$cfg    = config('manage');
	$_url   = 'index.php?mod=content.type&id='.$id;
	if(empty($cfg['webtype']))
	{
		include 'type-corporate.php';
	}else{
		include 'type-news.php';
	}
}else{
	echo msg(lang('not found'));
}
