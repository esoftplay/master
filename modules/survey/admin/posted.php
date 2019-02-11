<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'survey_posted');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('name,email,params,question_titles', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form = _lib('pea',  'survey_posted' );

$form->initRoll("$add_sql ORDER BY id DESC", 'id' );

$form->roll->addInput('name','sqllinks');
$form->roll->input->name->setTitle('Name');
$form->roll->input->name->setLinks( $Bbc->mod['circuit'].'.posted_detail');

$form->roll->addInput('email','sqlplaintext');
$form->roll->input->email->setTitle('Email');

$form->roll->addInput('question_titles','sqlplaintext');
$form->roll->input->question_titles->setTitle('Question');

$form->roll->addInput('date','datetime');
$form->roll->input->date->setTitle('Date');
$form->roll->input->date->setPlaintext(true);

$form->roll->addInput('publish','checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('Actived');

$form->roll->onDelete('_posted_delete', $form->roll->getDeletedId(), false);

function _posted_delete($ids)
{
	global $db;
	if(count($ids) > 0)
	{
		$q = "SELECT * FROM survey_posted_question WHERE posted_id IN(".implode(',', $ids).")";
		$r = $db->getAll($q);
		$q_ids = array();
		foreach($r AS $d) {
			if($d['question_id'] > 0) {
				if(!isset($q_ids[$d['question_id']])) $q_ids[$d['question_id']] = 1;
				else $q_ids[$d['question_id']] += 1;
			}
			if($d['question_id'] > 0) $q_ids[] = $d['question_id'];
			if($d['option_ids']) {
				$q = "UPDATE survey_question_option SET voted=(voted-1) WHERE id IN (".$d['option_ids'].")";
				$db->Execute($q);
			}
		}
		foreach((array)$q_ids AS $id => $min_voted) {
			$q = "UPDATE survey_question SET voted=(voted-$min_voted) WHERE id=$id";
			$db->Execute($q);
		}
		$q = "DELETE FROM survey_posted WHERE id IN(".implode(',', $ids).")";
		$db->Execute($q);
		$q = "DELETE FROM survey_posted_question WHERE posted_id IN(".implode(',', $ids).")";
		$db->Execute($q);
	}
}
echo $form->roll->getForm();