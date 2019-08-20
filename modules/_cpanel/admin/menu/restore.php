<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Backup & Restore');
require_once 'menuQRY.php';
$filename = @$_GET['file'];
if (!is_file(_CACHE.$filename.'.json'))
{
	redirect();
}
$json   = file_read(_CACHE.$filename.'.json');
$r_file = json_decode($json, 1);
$r_curr = menu_builder();
$error  = '';
$fields = array();
$admins = array();
$checks = ['module', 'cat', 'group', 'public', 'admin'];
foreach ($checks as $check)
{
	if (!@is_array($r_file[$check]))
	{
		$error = 'invalid json file has been detected. the json must contain:'.implode(' ', $checks);
	}
}

if (empty($error))
{
	if ($r_curr['module'] != $r_file['module'])
	{
		$error = 'this site has different module combination, please sync the modules before the menu';
	}else
	if ($r_curr['cat']!=$r_file['cat'])
	{
		$error   = 'You can only sync menu if the menu category are the same, please make sure your menu category are the same!';
		$cat_ids = array();
		foreach ($r_curr['cat'] as $id => $cat)
		{
			if ($cat != @$r_file['cat'][$id])
			{
				$cat_ids[] = $id;
				$fields['cat'][]    = array(
					pr(json_encode([$id, $cat], JSON_PRETTY_PRINT), 1),
					pr(json_encode([$id, @$r_file['cat'][$id]], JSON_PRETTY_PRINT), 1),
					);
			}
		}
		foreach ($r_file['cat'] as $id => $cat)
		{
			if (!in_array($id, $cat_ids) && $cat != @$r_curr['cat'][$id])
			{
				$fields['cat'][] = array(
					pr(json_encode([$id, $cat], JSON_PRETTY_PRINT), 1),
					pr(json_encode([$id, @$r_curr['cat'][$id]], JSON_PRETTY_PRINT), 1),
					);
			}
		}
	}else{
		if ($r_curr['group'] != $r_file['group'])
		{
			$group_ids = array();
			foreach ($r_curr['group'] as $id => $group)
			{
				if ($group != @$r_file['group'][$id])
				{
					$group_ids[] = $id;
					$fields['group'][]    = array(
						pr(json_encode([$id, $group], JSON_PRETTY_PRINT), 1),
						pr(json_encode([$id, @$r_file['group'][$id]], JSON_PRETTY_PRINT), 1),
						);
				}
			}
			foreach ($r_file['group'] as $id => $group)
			{
				if (!in_array($id, $group_ids) && $group != @$r_curr['group'][$id])
				{
					$fields['group'][] = array(
						pr(json_encode([$id, $group], JSON_PRETTY_PRINT), 1),
						pr(json_encode([$id, @$r_curr['group'][$id]], JSON_PRETTY_PRINT), 1),
						);
				}
			}
		}

		/* DISTINGUISH ADMIN MENU */
		$curr = $r_curr['admin'][1];
		$file = $r_file['admin'][1];
		if ($curr!=$file)
		{
			$admin_ids = array();
			$fields['admin'] = menu_restore_sync($curr, $file);
		}

		/* DISTINGUISH PUBLIC MENU */
		$curr = $r_curr['public'];
		$file = $r_file['public'];
		if ($curr!=$file)
		{
			$ids = array();
			$out = array();
			foreach ($curr as $cat_id => $menus)
			{
				$out[$cat_id] = array();
				foreach ($menus as $id => $menu)
				{
					$ids[] = $id;
					$your  = $file[$cat_id][$id];
					if ($menu != $your)
					{
						$out[$cat_id][] = array(
							pr(json_encode([$menu['id'], $menu], JSON_PRETTY_PRINT), 1),
							pr(json_encode([$your['id'], $your], JSON_PRETTY_PRINT), 1),
							);
					}
				}
			}
			foreach ($file as $cat_id => $menus)
			{
				if (!is_array($out[$cat_id]))
				{
					$out[$cat_id] = array();
				}
				foreach ($menus as $id => $menu)
				{
					$mine  = $curr[$cat_id][$id];
					if (!in_array($id, $ids) && $menu != $mine)
					{
						$out[$cat_id][] = array(
							pr(json_encode([$menu['id'], $menu], JSON_PRETTY_PRINT), 1),
							pr(json_encode([$mine['id'], $mine], JSON_PRETTY_PRINT), 1),
							);
					}
				}
			}
			foreach ($out as $cat_id => $field)
			{
				if (!empty($field))
				{
					$fields['public'][$r_curr['cat'][$cat_id]['name'].' Menu'] = $field;
				}
			}
		}
	}
}
if (!empty($error))
{
	echo msg($error, 'danger');
}
if (!empty($fields))
{
	if (!empty($fields['cat']))
	{
		echo table($fields['cat'], ['site', 'file'], 'Menu Category');
	}
	if (!empty($fields['group']))
	{
		echo table($fields['group'], ['site', 'file'], 'USER GROUP');
	}
	if (!empty($fields['admin']))
	{
		echo table($fields['admin'], ['site', 'file'], 'ADMIN MENU');
	}
	if (!empty($fields['public']))
	{
		foreach ($fields['public'] as $cat => $value)
		{
			echo table($value, ['site', 'file'], $cat);
		}
	}
}else{
	if (empty($error))
	{
		echo msg('there is no difference between your menu file with this site');
	}
}

echo $sys->button($_GET['return']);

function menu_restore_sync($curr, $file)
{
	$output = array();
	$ids    = array();

	foreach ($curr as $i => $dt)
	{
		if ($dt != @$file[$i])
		{
			$ids[] = $dt['id'];
			$output[] = array(
				pr(json_encode([$dt['id'], $dt], JSON_PRETTY_PRINT), 1),
				pr(json_encode([$dt['id'], @$file[$i]], JSON_PRETTY_PRINT), 1),
				);
		}
	}

	foreach ($file as $i => $dt)
	{
		if (!in_array($dt['id'], $ids) && $dt != $curr[$i])
		{
			$output[] = array(
				pr(json_encode([$dt['id'], @$curr[$i]], JSON_PRETTY_PRINT), 1),
				pr(json_encode([$dt['id'], $dt], JSON_PRETTY_PRINT), 1),
				);
		}
	}
	return $output;
}