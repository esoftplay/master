<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$q = "SELECT name FROM bbc_template WHERE id=$id";
$template_name = $db->getOne($q);
if(!$db->Affected_rows()) redirect($Bbc->mod['circuit'].'.tools&act=template');

// FETCH PARAMS..
$q = "SELECT * FROM bbc_block_theme WHERE template_id=$id";
$t = $db->getAll($q);
$q = "SELECT b.*, r.name AS block_name, p.name AS position_name FROM bbc_block AS b
			LEFT JOIN bbc_block_ref AS r ON (r.id=b.block_ref_id)
			LEFT JOIN bbc_block_position AS p ON (p.id=b.position_id)
			WHERE b.template_id=$id";
$b = $db->getAll($q);

$q = "SELECT id, LOWER(code) AS code FROM bbc_lang";
$r_lang = $db->getAssoc($q);
$q = "SELECT id FROM bbc_block WHERE template_id=$id";
$ids= $db->getCol($q);$ids[] = 0;
$q = "SELECT * FROM bbc_block_text WHERE block_id IN (".implode(',', $ids).")";
$r = $db->getAll($q); $c = array();
foreach($r AS $dt)
{
	$c[$dt['block_id']][$r_lang[$dt['lang_id']]] = $dt['title'];
}
$params = array('block' => $b, 'text' => $c, 'theme' => $t);

// ZIP FILE...
$path = _ROOT.'images/'.$template_name.'.zip';
$dir = (chdir(_ROOT.'templates/')) ? '' : _ROOT.'templates/';

$zip = _class('zip');
$zip->add_data('params.json', json_encode($params));
$zip->read_dir($dir.$template_name.'/');
#$zip->archive($path);
$zip->download('template_'.$template_name.'.zip');
#_func('download');
#$content = file_get_contents($path);
#@unlink($path);
#download_file('template_'.$template_name.'.zip', $zip->get_zip());
