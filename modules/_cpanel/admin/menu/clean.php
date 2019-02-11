<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$r_del  = array();
$r_par  = array();
$r_sub  = array();
$r_cat  = $db->getCol("SELECT id FROM bbc_menu_cat");
$r_mod  = $db->getCol("SELECT id FROM bbc_module");
$r_menu = $db->getAssoc("SELECT id, par_id, module_id, is_admin, cat_id, orderby FROM bbc_menu WHERE 1 ORDER BY is_admin, cat_id, par_id, orderby ASC");

// PARSING ALL MENU
foreach ($r_menu as $menu_id => $menu)
{
	$is_pass = true;
	$msg = array();
	if (!in_array($menu['module_id'], $r_mod)) {
		$is_pass = false;
		$msg[]   = 'Module not found';
	}
	if ($is_pass) {
		if (!in_array($menu['cat_id'], $r_cat)) {
			$is_pass = false;
			$msg[]   = 'Menu Position not found';
		}
	}
	if ($is_pass)
	{
		if ($menu['par_id']=='0')
		{
			$r_par[$menu['is_admin']][$menu['cat_id']][] = $menu_id;
		}else{
			if (isset($r_menu[$menu['par_id']])) {
				$r_sub[$menu['par_id']][] = $menu_id;
			}else{
				$is_pass = false;
				$msg[]   = 'Parent menu not found';
			}
		}
	}
	if (!$is_pass) {
		$r_del[] = array($menu_id, $msg, $menu);
	}
}

// REPAIR MENU CATEGORY IN ADMIN
foreach ($r_par[1] as $cat_id => $ids)
{
	if ($cat_id!='1')
	{
		foreach ($ids as $id)
		{
			do_exec("UPDATE bbc_menu SET cat_id=1 WHERE menu_id={$id}");
			$r_par[1][1][] = $id;
		}
		unset($r_par[1][$cat_id]);
	}
}

// REPAIR MENU SORTING
foreach ($r_par as $is_admin => $section) {
	foreach ($section as $cat_id => $menus) {
		$orderby = 0;
		foreach ($menus as $id) {
			$orderby++;
			$menu = $r_menu[$id];
			if ($menu['orderby']!=$orderby) {
				do_exec("UPDATE bbc_menu SET orderby={$orderby} WHERE menu_id={$id}");
			}
		}
	}
}
foreach ($r_sub as $par_id => $menus) {
	$orderby=0;
	foreach ($menus as $id) {
		$orderby++;
		$menu = $r_menu[$id];
		if ($menu['orderby']!=$orderby) {
				do_exec("UPDATE bbc_menu SET orderby={$orderby} WHERE menu_id={$id}");
		}
	}
}

// DELETE UNUSED MENU
foreach ($r_del as $d) {
	do_exec("DELETE FROM bbc_menu WHERE menu_id=".$d[0]);
}


function do_exec($q)
{
	global $db;
	$db->Execute($q);
}
$return = !empty($_GET['return']) ? $_GET['return'] : _URL.'admin/index.php?mod=_cpanel.menu';
redirect($return);