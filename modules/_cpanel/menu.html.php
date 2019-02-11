<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$config = get_config('_cpanel', 'config');
$manage = get_config('content', 'manage');
$result = array();
if (!empty($config['home']))
{
	$icon = '';
	if (!empty($manage['cat_img']))
	{
		if (!empty($config['home_icon']))
		{
			if (is_file(_ROOT.'images/'.$config['home_icon']))
			{
				$icon = _URL.'images/'.$config['home_icon'];
			}
		}
		if (empty($icon))
		{
			$site = config('site');
			if (!empty($site['logo']))
			{
				if (is_file(_ROOT.'images/'.$site['logo']))
				{
					$icon = _URL.'images/'.$site['logo'];
				}
			}
		}
	}
	$result['home'] = array(
		'type'  => 'content',
		'title' => $config['home'],
		'image' => $icon,
		'url'   => _URL
		);
}
$result['type']   = array();
$result['list']   = array();
$config['module'] = array('content');
$tables           = $db->getCol('SHOW TABLES');
$r_func           = array('_cat_link', '_link_cat');

foreach ($config['module'] as $module)
{
	$tbl = $module=='content' ? 'bbc_content' : $module;
	if (in_array($tbl, $tables))
	{
		_func($module);
		$path = 'images/modules/'.$module.'/';
		$func = '';
		foreach ($r_func as $f)
		{
			if (function_exists($module.$f))
			{
				$func = $module.$f;
			}
		}
		switch ($module)
		{
			case 'audio':
			case 'watch':
			case 'download':
				$q = "SELECT `id`, '0' AS `par_id`, '{$module}' AS `type`, `title`, '' AS `image` FROM `{$tbl}_cat` AS c
					LEFT JOIN `{$tbl}_cat_text` AS t ON (t.cat_id=c.id AND t.lang_id=".lang_id().")
					WHERE publish=1 ORDER BY title, id ASC";
				break;
			case 'content':
			case 'gallery':
			default:
				$q = "SELECT `id`, `par_id`, '{$module}' AS `type`, `title`, `image` FROM `{$tbl}_cat` AS c
					LEFT JOIN `{$tbl}_cat_text` AS t ON (t.cat_id=c.id AND t.lang_id=".lang_id().")
					WHERE publish=1 ORDER BY par_id, title, id ASC";
				break;
		}
		$r = $db->getAll($q);
		$l = array();
		foreach ($r as $d)
		{
			if (!empty($d['image']))
			{
				if (!empty($manage['cat_img']) && is_file(_ROOT.$path.$d['image']))
				{
					$d['image'] = _URL.$path.$d['image'];
				}else{
					$d['image'] = '';
				}
			}
			if (!empty($func))
			{
				$d['url'] = $func($d['id'], $d['title']);
			}
			$l[] = $d;
		}
		$result['type'][] = $module;
		$result['list'][] = $l;
	}
}
if (!empty($result))
{
	$data_output = _cpanel_result($result);
}