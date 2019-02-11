<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$form = _lib('pea', 'bbc_content_tag');

$form->initEdit('WHERE id='.$id, 'id');
$form->edit->setDeleteTool(true);

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle("Content Tag Detail");

$form->edit->addInput('col','multiinput');
$form->edit->input->col->setTitle('Title of Tag');
$form->edit->input->col->setDelimiter(' ');
$form->edit->input->col->addInput('title', 'text');
$form->edit->input->col->addInput('visit', 'editlinks');

$form->edit->input->visit->setIcon('fa-external-link', 'open page');
$form->edit->input->visit->setLinks(_URL.'id.htm');
$form->edit->input->visit->setGetName('tag_id');
$form->edit->input->visit->setExtra('target="external"');
$form->edit->input->visit->setFieldName('id AS visit');

echo $form->edit->getForm();

if (!empty($_POST[$form->edit->deleteButton->name]) && $id)
{
	$db->Execute("DELETE FROM bbc_content_tag WHERE id={$id}");
	$db->Execute("DELETE FROM bbc_content_tag_list WHERE tag_id={$id}");
}else{
	$form1 = _lib('pea', 'bbc_content_tag_list');
	$form1->initRoll("WHERE tag_id={$id} ORDER BY content_id DESC", 'content_id');
	$form1->roll->setSaveTool(false);

	$form1->roll->addInput('content_id', 'selecttable');
	$form1->roll->input->content_id->setTitle('Content');
	$form1->roll->input->content_id->setReferenceTable('bbc_content_text');
	$form1->roll->input->content_id->setReferenceField( 'title', 'content_id' );
	$form1->roll->input->content_id->setReferenceCondition( 'lang_id='.lang_id() );
	$form1->roll->input->content_id->setLinks($Bbc->mod['circuit'].'.content_edit');

	$form1->roll->onDelete('content_tag_detail_delete');
	echo $form1->roll->getForm();
}

function content_tag_detail_delete($ids)
{
	global $db, $id, $form1;
	ids($ids);
	if (!empty($ids))
	{
		$db->Execute("DELETE FROM bbc_content_tag_list WHERE content_id IN ({$ids}) AND tag_id={$id}");
		$total = $db->getOne("SELECT COUNT(*) FROM bbc_content_tag_list WHERE tag_id={$id}");
		$db->Execute("UPDATE bbc_content_tag SET total={$total}, updated=NOW() WHERE id={$id}");
		$form1->roll->setActionExecute(false, 'Selected contents have been deleted from current tags');
	}
}