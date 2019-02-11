<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if (empty($keyword['lang_id']))
{
	$keyword['lang_id'] = lang_id();
}
$form1 = _lib('pea', "bbc_lang_code AS c LEFT JOIN bbc_lang_text AS t ON (c.id=t.code_id AND t.lang_id=".$keyword['lang_id'].")" );

$form1->initRoll("$add_sql ORDER BY id DESC", 'id');
$form1->roll->setLanguage('code_id', 'bbc_lang_text');

$form1->roll->addReport('excel');
$form1->roll->addReport('html');

$form1->roll->addInput('header', 'header');
$form1->roll->input->header->setTitle('<label><input type="checkbox" id="is_htmleditor"> Use HTML Editor</label>');

$form1->roll->addInput('code', 'sqlplaintext' );
$form1->roll->input->code->setTitle( 'key' );

$form1->roll->addInput('content', 'textarea' );
$form1->roll->input->content->setExtra( 'class="words"' );
$form1->roll->input->content->setSize( 1, '60' );
$form1->roll->input->content->setNl2br( false );
$form1->roll->input->content->setLanguage();

if (empty($keyword['module_id']))
{
	$form1->roll->addInput('module_id', 'selecttable' );
	$form1->roll->input->module_id->setTitle( 'Module' );
	$form1->roll->input->module_id->setReferenceTable( 'bbc_module' );
	$form1->roll->input->module_id->setReferenceField( 'name', 'id' );
	$form1->roll->input->module_id->addOption( 'GLOBAL SITE', '0' );
	$form1->roll->input->module_id->setPlainText(true);
}

$form1->roll->addInput( 'links1', 'editlinks' );
$form1->roll->input->links1->setFieldName( 'id' );
$form1->roll->input->links1->setTitle( 'edit' );
$form1->roll->input->links1->setLinks( $Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&act=edit');

$form1->roll->onSave( 'lang_refresh');
$form1->roll->onDelete( 'delete_lang' );
function delete_lang()
{
	global $form1, $db;
	$ids = $form1->roll->getDeletedId();
	if(count($ids) > 0)
	{
		$q = "DELETE FROM bbc_lang_code WHERE id IN (".implode(',', $ids).")";
		$db->Execute($q);
		$q = "DELETE FROM bbc_lang_text WHERE code_id IN (".implode(',', $ids).")";
		$db->Execute($q);
		lang_refresh();
	}
}
