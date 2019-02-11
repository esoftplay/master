<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*==================================================
 * FORM ADD
 *================================================*/
$form1 = _lib('pea', 'survey_polling');
$form1->initEdit();
$form1->edit->setLanguage('polling_id');

$form1->edit->addInput('header','header');
$form1->edit->input->header->setTitle('Add Polling');

$form1->edit->addInput('question','textarea');
$form1->edit->input->question->setTitle('Question');
$form1->edit->input->question->setSize(4, 80);
$form1->edit->input->question->setLanguage( true );
/*
$form1->edit->addInput('options','multiform');
$form1->edit->input->options->setTitle('Options');
$form1->edit->input->options->setReferenceTable('survey_polling_option AS o LEFT JOIN survey_polling_option_text AS t ON (t.polling_option_id=o.id AND t.lang_id='.lang_id().')');
$form1->edit->input->options->setReferenceField( 'polling_id', 'id' );
$form1->edit->input->options->setReferenceCondition( 'publish=1' );
$form1->edit->input->options->addInput('title', 'text', 'option');
*/
$form1->edit->addInput('publish','checkbox');
$form1->edit->input->publish->setTitle('Publish');
$form1->edit->input->publish->setCaption('Actived');
$form1->edit->input->publish->setDefaultValue(1);

$form1->edit->onSave('_polling_add');
$form1->edit->action();
function _polling_add($id)
{
	global $Bbc;
	if($id > 0)
	{
		redirect($Bbc->mod['circuit'].'.polling_edit&id='.$id.'&return='.urlencode(seo_url()));
	}
}

/*==================================================
 * FORM LIST
 *================================================*/
$form = _lib('pea', 'survey_polling');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('question', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form = _lib('pea',  'survey_polling AS p LEFT JOIN survey_polling_text AS t ON (p.id=t.polling_id AND lang_id='.lang_id().')' );

$form->initRoll("$add_sql ORDER BY id", 'id' );
#$form->roll->setSaveTool(false);

$form->roll->addInput('question','sqllinks');
$form->roll->input->question->setTitle('Question');
$form->roll->input->question->setLinks( $Bbc->mod['circuit'].'.polling_edit');

$form->roll->addInput('options','multicheckbox');
$form->roll->input->options->setTitle('Options');
$form->roll->input->options->setReferenceTable('survey_polling_option AS o LEFT JOIN survey_polling_option_text AS t ON (t.polling_option_id=o.id AND t.lang_id='.lang_id().')');
$form->roll->input->options->setReferenceField('title','polling_id');
$form->roll->input->options->setFieldName('id AS options');
$form->roll->input->options->setPlaintext( true );

$form->roll->addInput('publish','checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('Actived');

$form->roll->onDelete('polling_delete', $form->roll->getDeletedId(), false);

function polling_delete($ids)
{
	global $db;
	if(count($ids) > 0)
	{
		$ids[] = 0;
		$q = "SELECT id FROM survey_polling_option WHERE polling_id IN(".implode(',', $ids).")";
		$option_ids = $db->getCol($q);
		if(count($option_ids) > 0)
		{
			$q = "DELETE FROM survey_polling_option_text WHERE polling_option_id IN(".implode(',', $option_ids).")";
			$db->Execute($q);
			$q = "DELETE FROM survey_polling_option WHERE id IN(".implode(',', $option_ids).")";
			$db->Execute($q);
		}
		$q = "DELETE FROM survey_polling WHERE id IN(".implode(',', $ids).")";
		$db->Execute($q);
		$q = "DELETE FROM survey_polling_text WHERE polling_id IN(".implode(',', $ids).")";
		$db->Execute($q);
	}
}
$tabs = array(
	'List Polling' => $form->roll->getForm(),
	'Add Polling'  => $form1->edit->getForm()
);
echo tabs($tabs);
