<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
/*==================================================
 * FORM ADD
 *================================================*/
$form1 = _lib('pea', 'survey_polling');
$form1->initEdit('WHERE id='.$id);
$form1->edit->setLanguage( 'polling_id');

$form1->edit->addInput('header','header');
$form1->edit->input->header->setTitle('Edit Polling');

$form1->edit->addInput('question','textarea');
$form1->edit->input->question->setTitle('Question');
$form1->edit->input->question->setSize(4, 80);
$form1->edit->input->question->setLanguage( true );

$form1->edit->addInput('publish','checkbox');
$form1->edit->input->publish->setTitle('Publish');
$form1->edit->input->publish->setCaption('Actived');
$form1->edit->input->publish->setDefaultValue(1);

echo $form1->edit->getForm();

$form2 = _lib('pea', 'survey_polling_option');
$form2->initAdd();
$form2->add->setFormName('option_edit');
$form2->add->setLanguage('polling_option_id');

$form2->add->addInput('header','header');
$form2->add->input->header->setTitle('Add Option');

$form2->add->addInput('title','text');
$form2->add->input->title->setTitle('Option');
$form2->add->input->title->setSize( 30 );
$form2->add->input->title->setLanguage();

$form2->add->addInput('polling_id','hidden');
$form2->add->input->polling_id->setDefaultValue($id);

$form2->add->addInput('voted','hidden');
$form2->add->input->voted->setDefaultValue(0);

$form2->add->addInput('publish','checkbox');
$form2->add->input->publish->setTitle('Publish');
$form2->add->input->publish->setCaption('Publish');
$form2->add->input->publish->setDefaultValue(1);

$form2->add->onSave('_option_add');
$form2->add->action();
function _option_add($id)
{
	global $db;
	if($id > 0)
	{
		$polling_id = @intval($_GET['id']);
		$q = "SELECT COUNT(*) FROM survey_polling_option WHERE polling_id=$polling_id";
		$orderby = $db->getOne($q);
		$q = "UPDATE survey_polling_option SET orderby=$orderby WHERE id=$id";
		$db->Execute($q);
	}
}
$form3 = _lib('pea',  'survey_polling_option' );

$form3->initRoll("WHERE polling_id=$id ORDER BY orderby ASC", 'id' );
$form3->roll->setFormName('option');
$form3->roll->setLanguage('polling_option_id');

$form3->roll->addInput('title','text');
$form3->roll->input->title->setTitle('Option');
$form3->roll->input->title->setSize(30);
$form3->roll->input->title->setLanguage();

$form3->roll->addInput('voted','sqlplaintext');
$form3->roll->input->voted->setTitle('voted');

$form3->roll->addInput('orderby','orderby');
$form3->roll->input->orderby->setTitle('orderby');

$form3->roll->addInput('publish','checkbox');
$form3->roll->input->publish->setTitle('Publish');
$form3->roll->input->publish->setCaption('Publish');

$tabs = array(
	'List Option' => $form3->roll->getForm(),
	'Add Option'	=> $form2->add->getForm()
);
echo '<br class="clear" />'.tabs($tabs);