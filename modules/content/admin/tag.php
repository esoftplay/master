<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'bbc_content_tag');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('title', false);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form->initRoll("{$add_sql} ORDER BY id DESC", 'id');
$form->roll->setSaveTool(false);

$form->roll->addInput('tag_id', 'sqlplaintext');
$form->roll->input->tag_id->setTitle('ID');
$form->roll->input->tag_id->setFieldName('id AS tag_id');

$form->roll->addInput('col','multiinput');
$form->roll->input->col->setTitle('Title');
$form->roll->input->col->setDelimiter(' ');
$form->roll->input->col->addInput('title', 'sqllinks');
$form->roll->input->col->addInput('visit', 'editlinks');

$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.tag_detail');
$form->roll->input->title->setExtra( 'title="edit page"' );

$form->roll->input->visit->setIcon('fa-external-link', 'open page');
$form->roll->input->visit->setLinks(_URL.'id.htm');
$form->roll->input->visit->setGetName('tag_id');
$form->roll->input->visit->setExtra('target="external"');
$form->roll->input->visit->setFieldName('id AS visit');

$form->roll->addInput('total', 'sqlplaintext');
$form->roll->input->total->setNumberFormat();

$form->roll->addInput('created', 'sqlplaintext');
$form->roll->input->created->setDateFormat();

$form->roll->onDelete('content_tag_delete');
echo $form->roll->getForm();

function content_tag_delete($ids)
{
	global $db;
	ids($ids);
	if (!empty($ids))
	{
		$db->Execute("DELETE FROM bbc_content_tag WHERE id IN ({$ids})");
		$db->Execute("DELETE FROM bbc_content_tag_list WHERE tag_id IN ({$ids})");
	}
}