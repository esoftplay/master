<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$param = array();
//DATA PARAM
$q="SELECT * FROM bbc_module WHERE id=".$id;
$param['data'] = $db->getRow($q);
if(!$db->Affected_rows()) redirect($Bbc->mod['circuit'].'.tools&act=module');
$r = array('content','search','user','mobile','_cpanel','');
if(in_array($param['data']['name'], $r))
{
	echo explain('This module is contained the system core script. You\'re not allowed to download this module, please <a href="'.$Bbc->mod['circuit'].'.tools&act=module">click here</a> to return!');
}else{
	$q = "SELECT id, LOWER(code) AS code FROM bbc_lang";
	$r_lang = $db->getAssoc($q);

	//DATA MENU
	$q="SELECT * FROM bbc_menu WHERE module_id=".$id." ORDER BY is_admin DESC, par_id ASC, orderby ASC, id ASC";
	$param['menu'] = $db->getAll($q);
	$menu_ids = array();
	foreach($param['menu'] AS $dt)
	{
		$menu_ids[] = $dt['id'];
	}
	if(!empty($menu_ids))
	{
		$q = "SELECT * FROM bbc_menu_text WHERE menu_id IN (".implode(',', $menu_ids).")";
		$r = $db->getAll($q);
		foreach($r AS $dt)
		{
			if(isset($r_lang[$dt['lang_id']]))
			{
				$param['menu']['title'][$dt['menu_id']][$r_lang[$dt['lang_id']]] = $dt['title'];
			}
		}
	}

	//DATA CONFIG
	$q = "SELECT name, params FROM bbc_config WHERE module_id=$id";
	$param['config'] = $db->getAssoc($q);

	//DATA LANGUAGE
	$q = "SELECT LOWER(c.code) AS name, t.content, l.code
	FROM bbc_lang_code AS c
	LEFT JOIN bbc_lang_text AS t ON(c.id=t.code_id)
	LEFT JOIN bbc_lang AS l ON(l.id=t.lang_id)
	WHERE c.module_id=$id";
	$arr = array();
	$r = $db->getAll($q);
	foreach((array)$r AS $dt)
	{
		$arr[$dt['name']][$dt['code']] = $dt['content'];
	}
	$param['language']	= $arr;

	//DATA EMAIL TEMPLATE
	$q="SELECT * FROM bbc_email WHERE module_id=".$id;
	$param['email']	= $db->getAll($q);
	$ids = array();
	foreach($param['email'] AS $dt)
	{
		$ids[] = $dt['id'];
	}
	if(!empty($ids))
	{
		$q = "SELECT * FROM bbc_email_text WHERE email_id IN (".implode(',', $ids).")";
		$r = $db->getAll($q);
		foreach($r AS $dt)
		{
			if(isset($r_lang[$dt['lang_id']]))
			{
				$param['email_text'][$dt['email_id']][$r_lang[$dt['lang_id']]] = $dt;
			}
		}
	}

	/*===============================================
	 * FETCHING DATABASE...
	 *=============================================*/
	$r = $db->getCol("SHOW TABLES");
	$r_db_tbl = array();
	foreach((array)$r AS $tbl)
	{
		if(preg_match('~^'.$param['data']['name'].'_?~is', $tbl))
		{
			$r_db_tbl[] = $tbl;
		}
	}

	/*===============================================
	 * ZIP FILE ...
	 *=============================================*/
	$path = _ROOT.'images/'.$param['data']['name'].'.zip';

	_func('mysql');
	$zip = _class('zip');
	$zip->add_data('params.json', json_encode($param));
	$zip->add_data('database.sql', mysql_dump($r_db_tbl));

	$dir = (chdir(_ROOT.'modules/')) ? '' : _ROOT.'modules/';
	$zip->read_dir($dir.$param['data']['name'].'/');

	if(is_dir(_ROOT.'images/modules/'.$param['data']['name']))
	{
		chdir(_ROOT.'images/modules/');
		$zip->read_dir($param['data']['name'].'/', 'uploads');
	}

	_func('path');
	$r = path_list(_ROOT.'blocks/');
	$blocks = array();
	foreach($r AS $block_name)
	{
		if(is_file(_ROOT.'blocks/'.$block_name.'/_switch.php')
		&& preg_match('~^'.$param['data']['name'].'_?~is', $block_name))
		{
			$blocks[] = $block_name;
		}
	}
	if(count($blocks) > 0)
	{
		chdir(_ROOT.'blocks/');
		foreach($blocks AS $block)
		{
			$zip->read_dir($block.'/', 'blocks');
		}
	}

	$zip->download('module_'.$param['data']['name'].'_'.current(get_lang()).'.zip');
}