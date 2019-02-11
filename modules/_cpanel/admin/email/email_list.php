<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$form = _lib('pea',  $str_table = "bbc_email AS e LEFT JOIN bbc_email_text AS t ON (e.id=t.email_id AND t.lang_id=".lang_id().")" );
$form->initRoll( $add_sql, 'id' );

$form->roll->addInput( 'name', 'sqllinks' );
$form->roll->input->name->setTitle( 'template' );
$form->roll->input->name->setLinks( $Bbc->mod['circuit'].'.email&act=edit' );

$form->roll->addInput( 'subject', 'sqlplaintext' );

$form->roll->addInput( 'description', 'sqlplaintext' );

if (empty($keyword['module_id']))
{
	$form->roll->addInput( 'module_id', 'selecttable' );
	$form->roll->input->module_id->setTitle( 'Module' );
	$form->roll->input->module_id->setReferenceTable( 'bbc_module ORDER BY name' );
	$form->roll->input->module_id->setReferenceField( 'name', 'id' );
	$form->roll->input->module_id->setPlainText( true );
}

$form->roll->onDelete('email_template_delete');
$form->roll->action();
function email_template_delete($ids)
{
	if(!empty($ids))
	{
		global $db;
		$ids = implode(',', $ids);
		$q = "DELETE FROM bbc_email WHERE id IN($ids)";
		$db->Execute($q);
		$q = "DELETE FROM bbc_email_text WHERE email_id IN($ids)";
		$db->Execute($q);
	}
}