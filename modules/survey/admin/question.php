<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*===============================================
 * START FORM ADD
 *==============================================*/
$form1 = _lib('pea', 'survey_question');
$form1->initAdd();
$form1->add->setLanguage( 'question_id');

$form1->add->addInput('header','header');
$form1->add->input->header->setTitle('Add Question');

$form1->add->addInput('title','text');
$form1->add->input->title->setTitle('Question');
$form1->add->input->title->setSize( 60 );
$form1->add->input->title->setLanguage( true );

$form1->add->addInput('description','textarea');
$form1->add->input->description->setTitle('Description');
$form1->add->input->description->setSize(4, 80);
$form1->add->input->description->setLanguage( true );

$form1->add->addInput('type','select');
$form1->add->input->type->setTitle('Type');
$form1->add->input->type->addOptionArray(array('checkbox','multiple','radio','select','text','none','custom'));

$form1->add->addInput('file','text');
$form1->add->input->file->setTitle('Path');
$form1->add->input->file->addTip('insert directory name if you select custom as Type');

$form1->add->addInput('voted','hidden');
$form1->add->input->voted->setTitle('Voted');
$form1->add->input->voted->setDefaultValue(0);

$form1->add->addInput('is_note','checkbox');
$form1->add->input->is_note->setTitle('Note');
$form1->add->input->is_note->setCaption('Use Note');
$form1->add->input->is_note->setDefaultValue(1);

$form1->add->addInput('checked','checkbox');
$form1->add->input->checked->setTitle('Checked');
$form1->add->input->checked->setCaption('Checked');
$form1->add->input->checked->setDefaultValue(1);
$form1->add->input->checked->addTip('automatically checked if this option is displayed');

$form1->add->addInput('publish','checkbox');
$form1->add->input->publish->setTitle('Publish');
$form1->add->input->publish->setCaption('Actived');
$form1->add->input->publish->setDefaultValue(1);

$form1->add->onSave('_question_add', $form1->add->getInsertId());
$form1->add->action();
function _question_add($id)
{
	global $db, $Bbc;
	if($id > 0)
	{
		$q = "SELECT COUNT(*) FROM survey_question";
		$orderby = $db->getOne($q);
		$q = "UPDATE survey_question SET orderby=$orderby WHERE id=$id";
		$db->Execute($q);
		redirect($Bbc->mod['circuit'].'.question_detail&id='.$id.'&return='.urlencode(seo_url()));
	}
}

/*===============================================
 * START LISTING
 *==============================================*/
$form = _lib('pea', 'survey_question');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('title,description', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form = _lib('pea',  'survey_question AS a LEFT JOIN survey_question_text AS t ON (a.id=t.question_id AND t.lang_id='.lang_id().')' );

$form->initRoll("$add_sql ORDER BY orderby ASC", 'id' );

$form->roll->addInput('title','sqllinks');
$form->roll->input->title->setTitle('Question');
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.question_detail');

$form->roll->addInput('type','sqlplaintext');
$form->roll->input->type->setTitle('Type');

$form->roll->addInput('file','sqlplaintext');
$form->roll->input->file->setTitle('Path');

$form->roll->addInput('voted','condition');
$form->roll->input->voted->setTitle('Voted');
$form->roll->input->voted->addCondition('>', '0', '<a href="'.$Bbc->mod['circuit'].'.question_report&id=#_id_#&return='.urlencode(seo_url()).'" title="view report">#_value_#</a>');
$form->roll->input->voted->addCondition('default', '', '#_value_#');

$form->roll->addInput('orderby','orderby');
$form->roll->input->orderby->setTitle('Orderby');

$form->roll->addInput('is_note','checkbox');
$form->roll->input->is_note->setTitle('Note');
$form->roll->input->is_note->setCaption('Note');

$form->roll->addInput('checked','checkbox');
$form->roll->input->checked->setTitle('Checked');
$form->roll->input->checked->setCaption('Checked');

$form->roll->addInput('publish','checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('Publish');

$form->roll->onDelete('_question_delete', $form->roll->getDeletedId(), false);

function _question_delete($ids)
{
	global $db;
	if(count($ids) > 0)
	{
		$q = "DELETE FROM survey_question WHERE id IN(".implode(',', $ids).")";
		$db->Execute($q);
		$q = "DELETE FROM survey_question_text WHERE question_id IN(".implode(',', $ids).")";
		$db->Execute($q);

		$q = "SELECT id FROM survey_question_option WHERE question_id IN(".implode(',', $ids).")";
		$q_ids = $db->getCol($q);
		if(count($q_ids) > 0)
		{
			$q = "DELETE FROM survey_question_option WHERE id IN(".implode(',', $q_ids).")";
			$db->Execute($q);
			$q = "DELETE FROM survey_question_option_text WHERE option_id IN(".implode(',', $q_ids).")";
			$db->Execute($q);
		}
		$q = "SELECT id, orderby FROM survey_question ORDER BY orderby ASC";
		$r = $db->getAssoc($q);
		$i = 0;
		foreach($r AS $id => $orderby)
		{
			$i++;
			if($i != $orderby)
			{
				$q = "UPDATE survey_question SET orderby=$i WHERE id=$id";
				$db->Execute($q);
			}
		}
	}
}
$tabs = array(
	'Questions' => $form->roll->getForm(),
	'Add'	=> $form1->add->getForm()
);
echo tabs($tabs);