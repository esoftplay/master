<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$form = _lib('pea',  $str_table = 'bbc_lang' );
$form->initEdit();

$form->edit->addInput( 'header', 'header' );
$form->edit->input->header->setTitle( 'Add Language Reference' );

$form->edit->addInput( 'title', 'text' );
$form->edit->input->title->setTitle('Language');
$form->edit->input->title->setSize( 40 );

$form->edit->addInput( 'code', 'text' );
$form->edit->input->code->setTitle( 'Code' );

$form->edit->onSave('language_repair', $form, true);
$form->edit->action();

$form->initRoll( 'WHERE 1', 'id' );

$form->roll->addInput( 'title', 'text' );
$form->roll->input->title->setTitle( 'Language' );
$form->roll->input->title->setSize( 40 );

$form->roll->addInput( 'code', 'text' );
$form->roll->input->code->setTitle( 'Code' );

$form->roll->onSave('language_repair', $form, true);
$form->roll->onDelete('language_repair', $form, true);

function language_repair()
{
	global $db;
	$q = "SELECT id FROM bbc_lang";
	$lang_ids = $db->getCol($q);
	$q = "SHOW TABLES";
	$r = $db->getCol($q);
	$tables = array();
	foreach((array)$r AS $tbl)
	{
		if(preg_match('~_text$~is', $tbl))
			$tables[] = $tbl;
	}
	foreach((array)$tables AS $table)
	{
		$q = "DELETE FROM $table WHERE lang_id NOT IN(".implode(',', $lang_ids).")";
		$db->Execute($q);
	}
	lang_refresh();
}



$tabs = array(
  'Language' => $form->roll->getForm(),
  'Add' => $form->edit->getForm()
);

echo tabs($tabs);
