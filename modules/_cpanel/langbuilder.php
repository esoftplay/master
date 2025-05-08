<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	$lang_ids = $db->getCol("SELECT `id` FROM `bbc_lang` WHERE 1");
	foreach ($lang_ids as $i)
	{
		$_GET['lang_id'] = $i;
		lang_fetch($id);
	}
	$data_output = array(
		'ok'      => 1,
		'message' => 'language module '.$id.' sudah diindex'
	);
}else{
	$urls     = [];
	$baseURL  = _URL.'_cpanel/langbuilder/';
	$r_module = $db->getAll("SELECT `id`, `name` FROM `bbc_module` WHERE `active`=1");
	foreach ($r_module as $dt)
	{
		$urls[] = $baseURL.$dt['id'];
	}
	$data_output = array(
		'ok'      => 1,
		'urls'    => $urls,
		'message' => 'silahkan di curl satu'
	);
}
