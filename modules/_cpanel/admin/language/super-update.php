<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$q = "SELECT id FROM bbc_lang_code";
$code_ids = $db->getCol($q);
$q = "SELECT text_id FROM bbc_lang_text WHERE code_id NOT IN (".implode(',', $code_ids).")";
$ids = $db->getCol($q);
if(count($ids) > 0)
{
	$q = "DELETE FROM bbc_lang_text WHERE text_id IN (".implode(',', $ids).")";
	$db->Execute($q);
}

$q = "SELECT code_id FROM bbc_lang_text";
$code_ids = $db->getCol($q);
$q = "SELECT id FROM bbc_lang_code WHERE id NOT IN (".implode(',', $code_ids).")";
$ids = $db->getCol($q);
if(count($ids) > 0)
{
	$q = "DELETE FROM bbc_lang_code WHERE id IN (".implode(',', $ids).")";
	$db->Execute($q);
}
lang_refresh();
redirect();
