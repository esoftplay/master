<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

function cpanel_id($link, $less_link = false, $use_link = true)
{
	global $r_cpanel;
	if($less_link)
	{
		$v = array(
		  strrpos($link, '&')
		, strrpos($link, '-')
		, strrpos($link, '_')
		, strrpos($link, '?'));
		rsort($v);
		$link = substr($link, 0, intval($v[0]));
	}
	if(!empty($link))
	{
		$output = false;
		foreach($r_cpanel AS $data)
		{
			if($use_link)
			{
				if(strstr($data['link'], $link))
				{
					$output = $data;
					break;
				}
			}else{
				if($data['id'] == $link)
				{
					$output = $data;
					break;
				}
			}
		}
		if(!$output)
		{
			$output = call_user_func(__FUNCTION__, $link, true, $use_link);
		}
	}else{
		$output = array(
			'id'		=> 0
		,	'par_id'=> 0
		,	'title'	=> ''
		,	'image'	=> ''
		,	'link'	=> ''
		);
	}
	return $output;
}
function cpanel_nav($data, $add = array())
{
	$output = $add;
	if($data['id'] > 0)
	{
		$output[] = $data;
		if($data['par_id'] > 0)
		{
			$out = cpanel_id($data['par_id'], false, false);
			if($out['id'] > 0)
			{
				$output = call_user_func(__FUNCTION__, $out, $output);
			}
		}
	}
	return $output;
}
$r_cpanel = $Bbc->menu->cpanel_array;
$link = preg_replace(array('~^'.preg_quote(_URI).'~','~^'.preg_quote(_ADMIN).'~'), array('',''), $_SERVER['REQUEST_URI']);
$link = preg_replace('~^admin/~is', '', $link);
$link = preg_replace('~(&_?return=.*?)$~is', '', $link);
$link = preg_replace('~(&_?admin_id=[0-9]+)~is', '', $link);
$cpanel = cpanel_id($link, false, true);
if($cpanel['id'] > 0)
{
	$_SESSION['_cpanel']['lastPage'] = $cpanel['id'];
	if($cpanel['par_id']==0)
		$_SESSION['_cpanel']['lastIcon'] = $cpanel['image'];
}else{
	$cpanel = cpanel_id(@$_SESSION['_cpanel']['lastPage'], false, false);
}
$nav = array();
$r = cpanel_nav($cpanel);
foreach(array_reverse($r) AS $cpanel)
{
	if($cpanel['par_id'] == 0)
	{
		$nav['image'] = $cpanel['image'];
	}
	$sys->nav_add($cpanel['title'], $cpanel['link']);
}
$nav['title'] = $cpanel['title'];

// CHECK PERMISION
if(!in_array($r[0]['id'], $Bbc->menu->cpanel) && !in_array('all', $user->cpanel_ids))
{
	redirect($Bbc->mod['circuit']);
}
