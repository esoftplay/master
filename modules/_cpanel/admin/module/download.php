<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$_GET['module_id'] = intval($_GET['module_id']);
$param = array();

//DATA PARAM
$q="SELECT * FROM bbc_module WHERE id=".$_GET['module_id'];
$param['data']			= $db->getRow($q);
if(!$db->Affected_rows()) die();

//DATA CONFIG
$q="SELECT name, params FROM bbc_config WHERE module_id=".$_GET['module_id'];
$param['config'] = $db->getAssoc($q);

//DATA EMAIL TEMPLATE
$r_lang = get_lang();
$q="SELECT * FROM bbc_email WHERE module_id=".$_GET['module_id'];
$param['email']	= $db->getAssoc($q);
if(!empty($param['email']))
{
	$email_ids = array_keys($param['email']);
	$q = "SELECT * FROM bbc_email_text WHERE email_id IN (".implode(",", $email_ids).")";
	$r = $db->getAll($q);
	$text = array();
	foreach($r AS $dt) {
		$text[$dt['email_id']][$r_lang[$dt['lang_id']]] = array('subject' => $dt['subject'], 'content' => $dt['content']);
	}
	$param['email_text'] = $text;
}else{
	$param['email_text'] = array();
}

//DATA MENU
$q="SELECT * FROM bbc_menu WHERE module_id=".$_GET['module_id']." ORDER BY is_admin DESC, par_id ASC, orderby ASC, id ASC";
$param['menu'] = $db->getAll($q);
$r_lang = get_lang();
$ids = $r_text = array();
foreach($param['menu'] AS $dt) {
	$ids[] = $dt['id'];
}
if(count($ids) > 0)
{
	$q = "SELECT * FROM bbc_menu_text WHERE menu_id IN(".implode(',', $ids).")";
	$r = $db->getAll($q);
	foreach($r AS $dt)
	{
		$r_text[$dt['menu_id']][$r_lang[$dt['lang_id']]] = $dt['title'];
	}
}
$param['menu_text'] = $r_text;


//DATA MODULE DIRECTORY
_func('path');
$_path = _ROOT.'images/modules/'.$param['data']['name'].'/';
$r = path_list($_path);
$arr = array();
foreach($r AS $dt)
	if(is_dir($_path.$dt))
		$arr[] = $dt;
$param['directory'] = $arr;

//DATA LANGUAGE
$r_lang = get_lang();
$q = "SELECT c.code, t.lang_id, t.content FROM bbc_lang_text AS t
LEFT JOIN bbc_lang_code AS c ON (c.id=t.code_id)
WHERE c.module_id=".$_GET['module_id'];
$r = $db->getAssoc($q);
$lang = array();
foreach($r AS $code => $dt) {
	$lang[$code][$r_lang[$dt['lang_id']]] = $dt['content'];
}
$param['language'] = $lang;
header("Cache-control: private"); // fix for IE
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Description: Module Parameters");            
header("Content-Disposition: attachment; filename=\"param_".$param['data']['name'].".json\"");
if (defined('JSON_PRETTY_PRINT'))
{
	echo json_encode($param, JSON_PRETTY_PRINT);
}else{
	echo json_encode($param);
}
die();
