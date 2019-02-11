<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$output = array('success'=>0,'link' => 0);
if (!empty($_REQUEST['link']))
{
	$link = !empty($_REQUEST['dlink']) ? $_REQUEST['dlink'] : $_REQUEST['link'];
	$link = str_replace(_URL, '', $link);
	$link = preg_replace('~^admin/~is', '', $link);
	$link = preg_replace('~(&_?return=.*?)$~is', '', $link);
	$link = preg_replace('~(&admin_id=[0-9]+)~is', '', $link);
	$link = user_check_link($link);
	if (!empty($link))
	{
		$output['link']    = intval($link);
		$output['success'] = 1;
	}
}
output_json($output);

function user_check_link($link)
{
	global $Bbc, $db;
	$output = '';
	if (!empty($link))
	{
		$menu = array();
		foreach ($Bbc->menu->left as $m)
		{
			if ($m['link']==$link)
			{
				$menu = $m;
				break;
			}
		}
		if (!empty($menu))
		{
			$output = $menu['id'];
		}else{
			foreach ($Bbc->menu->cpanel as $m)
			{
				if ($m['link']==$link)
				{
					$menu = $m;
					break;
				}
			}
			if (!empty($menu))
			{
				$lastID = $db->getOne("SELECT id FROM bbc_menu ORDER BY id DESC LIMIT 1");
				$output = $menu['id']+$lastID;
			}else{
				if (preg_match('~[\?\&_]~s', $link))
				{
					$output = call_user_func(__FUNCTION__, preg_replace('~([\?&_][^\?&_]+)$~is', '', $link));
				}
			}
		}
	}
	return $output;
}
