<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$dir = _ROOT.'modules/';
include _ROOT.'modules/index.php';
_func('path');
$r = path_list($dir);
$r_module = $r_module_sql = $r_module_delete = $r_module_insert = array();
foreach($r AS $name)
{
	if(!in_array($name, $denied_module))
	{
		$thisDir = $dir.$name.'/';
		if(is_file($thisDir.'_switch.php')) $r_module[] = $name;
	}
}

$q = "SELECT * FROM bbc_module";
$r = $db->getAll($q);
foreach($r AS $dt)
{
	$r_module_sql[] = $dt['name'];
	if(!in_array($dt['name'], $r_module))
	{
		$r_module_delete[$dt['id']] = $dt['name'];
	}
}
foreach($r_module AS $module)
{
	if(!in_array($module, $r_module_sql))
	{
		$r_module_insert[] = $module;
	}
}

//DELETE ALL PARAMS OF UNDETECTED MODULES
if(count($r_module_delete) > 0)
{
	$r_module_id_delete = array_keys($r_module_delete);
	$q = "DELETE FROM bbc_module WHERE id IN(".implode(',', $r_module_id_delete).")";
	$db->Execute($q);

	// DELETE CONFIG
	$q = "DELETE FROM bbc_config WHERE module_id IN(".implode(',', $r_module_id_delete).")";
	$db->Execute($q);
	$db->cache_clean('config/');

	// DELETE EMAIL TEMPLATE
	$q     = "SELECT id FROM bbc_email WHERE module_id IN(".implode(',', $r_module_id_delete).")";
	$ids   = $db->getCol($q);
	$ids[] = 0;
	$q     = "DELETE FROM bbc_email WHERE id IN(".implode(',', $ids).")";
	$db->Execute($q);
	$q = "DELETE FROM bbc_email_text WHERE email_id IN(".implode(',', $ids).")";
	$db->Execute($q);

	// DELETE MENU
	$q = "SELECT id FROM bbc_menu WHERE module_id IN(".implode(',', $r_module_id_delete).")";
	$menu_ids = $db->getCol($q);
	menu_delete($menu_ids);

	// DELETE FOLDER
	foreach($r_module_delete AS $name)
	{
		if(!empty($name))
		{
			path_delete(_ROOT.'images/modules/'.$name);
		}
	}

	// DELETE LANGUAGE
	$q = "SELECT id FROM bbc_lang_code WHERE module_id IN(".implode(',', $r_module_id_delete).")";
	$code_ids = $db->getCol($q);
	if(count($code_ids) > 0)
	{
		$q = "DELETE FROM bbc_lang_text WHERE code_id IN(".implode(',', $code_ids).")";
		$db->Execute($q);
		$q = "DELETE FROM bbc_lang_code WHERE module_id IN(".implode(',', $r_module_id_delete).")";
		$db->Execute($q);
	}
}

foreach($r_module_insert AS $module)
{
	$q = "INSERT INTO bbc_module SET name='$module', created=NOW(), protected='0', allow_group=',all,', search_func='', active=1";
	$db->Execute($q);
}
$db->cache_clean('modules.cfg');