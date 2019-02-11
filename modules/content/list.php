<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$config = get_config('content', 'list');
_func('image');
$page = @intval($_GET['page']);
$cat = content_cat_list(@intval($_GET['id']), $page);
if(@$cat['publish'])
{
	meta_title($cat['title'], 1);
	meta_desc($cat['description'], 2);
	meta_keyword($cat['keyword'], 2);
	if(!$sys->menu_real)
	{
		$sys->nav_change($cat['title']);
	}
	$cat = array(
		'id'         => $cat['id'],
		'title'      => $cat['title'],
		'list'       => $cat['list'],
		'link'       => $cat['link'],
		'total'      => $cat['total'],
		'total_page' => $cat['total_page'],
		'rss'        => $cat['rss'],
		'config'     => $cat['config'],
		);
	if (!empty($cat['config']))
	{
		$config = $cat['config'];
	}
	include tpl(@$config['template'], 'list.html.php');
}else{
	echo msg(lang('not found'));
}
