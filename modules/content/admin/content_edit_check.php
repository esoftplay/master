<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = 0;
if (!empty($_GET['url']))
{
	if(preg_match('~'._URL.'(.*?)\.html~', $_GET['url'], $m))
	{
		$q = "SELECT * FROM bbc_menu WHERE is_admin=0 AND seo='{$m[1]}'";
		$menu = $db->getRow($q);
		if (!empty($menu))
		{
			if (!empty($menu['content_id']))
			{
				$_GET['id'] = $menu['content_id'];
			}else{
				if (preg_match('~content\.detail&id([0-9]+)~is', $menu['link'], $m))
				{
					$_GET['id'] = $m[1];
				}
			}
		}
	}else
	if (preg_match('~_([0-9]+)\.htm~is', $_GET['url'], $m))
	{
		$_GET['id'] = $m[1];
	}
}
if (!empty($_GET['id']))
{
	$id = content_check($_GET['id']);
}
echo output_json(array('found' => $id));
