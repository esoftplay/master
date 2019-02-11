<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$q = "SELECT code FROM bbc_lang WHERE id=$id";
$code = $db->getOne($q);
if(!$db->Affected_rows()) redirect($Bbc->mod['circuit'].'.language');
$q = "SELECT LOWER(a.code) AS name, b.name AS module, c.content
			FROM bbc_lang_code AS a 
			LEFT JOIN bbc_module AS b ON(a.module_id=b.id)
			LEFT JOIN bbc_lang_text AS c ON(a.id=c.code_id)
			WHERE c.lang_id=$id
			";
$r = $db->getAll($q);
_func('download');
download_file('language_'.$code.'.cfg', json_encode($r));