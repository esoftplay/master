<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if(!isset($user->is_login) || $user->is_login != 1)
{
	if(empty($_GET['mod']))
	{
		$_GET['mod'] = $Bbc->login;
	}else
	if($_GET['mod']!= $Bbc->login)
	{
		header('location:'._URL.'admin');	die();
	}
	$Bbc->home = $Bbc->login;
}
else
{
	$Bbc->menu = new stdClass;
	$user->menu_ids = is_array($user->menu_ids) ? $user->menu_ids : array();
	$q="SELECT * FROM bbc_menu AS m LEFT JOIN bbc_menu_text AS t 
			ON (m.id=t.menu_id AND lang_id=".lang_id().")
			WHERE is_admin=1 AND active=1 ORDER BY cat_id, par_id, orderby ASC";
	$r = $db->getAll($q);
	$Bbc->menu->all_array = array();
	foreach($r AS $i => $d) $Bbc->menu->all_array[$d['id']] = $d;
	if(in_array('all', $user->menu_ids))
	{
		$Bbc->menu->left = $Bbc->menu->all_array;
	}else{
		foreach($Bbc->menu->all_array AS $dt)
		{
			if(in_array($dt['id'], $user->menu_ids))
			{
				$Bbc->menu->left[] = $dt;
			}
		}
	}

	$user->cpanel_ids = is_array($user->cpanel_ids) ? $user->cpanel_ids : array();
	$q = "SELECT * FROM bbc_cpanel	WHERE active=1 ORDER BY par_id, orderby ASC";
	$Bbc->menu->cpanel_array = $db->getAll($q);
	if(in_array('all', $user->cpanel_ids))
	{
		$Bbc->menu->cpanel = $Bbc->menu->cpanel_array;
	}else{
		foreach($Bbc->menu->cpanel_array AS $dt)
		{
			if(in_array($dt['id'], $user->cpanel_ids))
			{
				$Bbc->menu->cpanel[] = $dt;
			}
		}
	}
}