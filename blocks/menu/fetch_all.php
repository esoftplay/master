<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$q     = "SELECT id, name FROM bbc_menu_cat ORDER BY orderby ASC";
$r_cat = $db->getAssoc($q);
$arr   = $sys->menu_get_all();
$r     = array();

if (isset($user)) // ada error variable global $user tidak ada di sini. PHP Warning:  Attempt to assign property of non-object in /var/www/html/master/blocks/menu/fetch_all.php on line 9
{
	if (empty($user->menu_ids) || !is_array($user->menu_ids))
	{
		$user->menu_ids = array();
	}
}
foreach($arr AS $m)
{
	if($m['protected'])
	{
		if (isset($user)) // ada error variable global $user tidak ada di sini. PHP Notice:  Trying to get property of non-object in /var/www/html/master/blocks/menu/fetch_all.php on line 15
		{
			if(intval($user->id) > 0)
			{
				if(in_array('all', $user->menu_ids) ||	in_array($m['id'], $user->menu_ids))
					$r[] = $m;
			}
		}
	}else{
		$r[] = $m;
	}
}
$Bbc->menu_array = array();
$s               = preg_match('~^https://~is', _URL) ? 's' : '';
$main_url        = $sys->wildcard ? 'http'.$s.'://'.config('site', 'url')._URI : _URL;
foreach($r AS $dt)
{
	$data = array(
		'id' 				=> $dt['id']
	,	'par_id'		=> $dt['par_id']
	,	'title'			=> $dt['title']
	,	'link'			=> getLinkFromMenu($dt['link'], $dt['seo'], $main_url)
	,	'cat_id'		=> $dt['cat_id']
	,	'protected'	=> $dt['protected']
	,	'is_content'=> $dt['is_content']
	);
	$Bbc->menu_array[$dt['cat_id']][] = $data;
}
function getLinkFromMenu($link, $seo, $main_url)
{
	if(empty($link))
	{
		$output = $main_url;
	}else
	if(preg_match('~^#~s', $link))
	{
		$output = $link;
	}else
	if(preg_match('~^(?:ht|f)tps?://~', $link))
	{
		$output = $link;
	}else
	if(_SEO and !empty($seo))
	{
		$output = $main_url.$seo.'.html';
	}else{
		$output = $main_url.$link;
	}
	return $output;
}