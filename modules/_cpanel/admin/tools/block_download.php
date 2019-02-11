<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$q = "SELECT name FROM bbc_block_ref WHERE id=$id";
$block_name = $db->getOne($q);
if(!$db->Affected_rows()) redirect($Bbc->mod['circuit'].'.tools&act=block');

// ZIP FILE...
$dir = (chdir(_ROOT.'blocks/')) ? '' : _ROOT.'blocks/';
$zip = _class('zip');
$zip->read_dir($dir.$block_name.'/');
$zip->download('block_'.$block_name.'_'.current(get_lang()).'.zip');
