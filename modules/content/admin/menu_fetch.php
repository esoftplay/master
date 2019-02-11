<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

_func('array');
$q			="SELECT id, par_id, cat_id, title, seo, link, active FROM bbc_menu AS m 
					LEFT JOIN bbc_menu_text AS t ON (t.menu_id=m.id AND lang_id=".lang_id().")
					WHERE is_admin=0 ORDER BY cat_id, par_id, orderby";
$r			= $db->getAll($q);
$r_menu = array();
foreach($r AS $d)
{
	$r_menu[$d['cat_id']][$d['id']] = $d;
}
$r_position = $db->getAll("SELECT id, name FROM bbc_menu_cat ORDER BY orderby ASC");
$all_menus	= array();
foreach($r_position AS $d)
{
	$arr		= (isset($r_menu[$d['id']]) && is_array($r_menu[$d['id']]) ) ? $r_menu[$d['id']] : array();
	$menus	= array_path($arr, 0, '>', '', '--');
	$tmp		= array();
	foreach((array)$menus AS $i => $title)
	{
		$realtitle	= $arr[$i]['title'];
		$tmp[]			= "[{$i}, '".addslashes($title)."', ".$arr[$i]['par_id'].", '".addslashes($realtitle)."']";
	}
	$all_menus[] = '['.$d['id'].', "'.addslashes($d['name']).'", ['.implode(',', $tmp).']]';
}
$all_menus = '['.implode(',', $all_menus).']';
