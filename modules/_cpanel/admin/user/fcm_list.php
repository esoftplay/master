<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');


$form = _lib('pea',  'bbc_user_push_topic');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('name, description', false);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

echo $form->search->getForm();

$form = _lib('pea',  'bbc_user_push_topic');
$form->initRoll("{$add_sql} ORDER BY id ASC");

$form->roll->setFormName('userFcm');
$form->roll->setSaveTool(false);

$form->roll->addInput( 'name', 'sqllinks' );
$form->roll->input->name->setTitle( 'Topic' );
$form->roll->input->name->setLinks('index.php?mod=_cpanel.user&act=fcm_detail');

$form->roll->addInput( 'total', 'sqllinks' );
$form->roll->input->total->setTitle( 'Member' );
$form->roll->input->total->setFieldName( 'id AS total' );
$form->roll->input->total->setLinks('index.php?mod=_cpanel.user&act=fcm_member');
$form->roll->input->total->setModal(true);
$form->roll->input->total->setDisplayColumn(false);
$form->roll->input->total->setDisplayFunction(function($topic_id) {
	global $db;
	$total = $db->getOne("SELECT COUNT(`list_id`) FROM `bbc_user_push_topic_list` WHERE `topic_id`={$topic_id}");
	return money($total, $is_shorten= false);
});

$form->roll->addInput('description','sqlplaintext');
$form->roll->input->description->setTitle('Info');
$form->roll->input->description->setDisplayColumn(true);

$form->roll->addInput('user_id', 'selecttable');
$form->roll->input->user_id->setTitle('Creator');
$form->roll->input->user_id->setReferenceTable('bbc_user');
$form->roll->input->user_id->setReferenceField( 'username', 'id' );
$form->roll->input->user_id->addOption( 'system', '0' );
$form->roll->input->user_id->setLinks('index.php?mod=_cpanel.user&act=edit');
$form->roll->input->user_id->setPlaintext(true);
$form->roll->input->user_id->setDisplayColumn(false);

$form->roll->addInput('created', 'sqlplaintext');
$form->roll->input->created->setTitle('Created');
$form->roll->input->created->setDateFormat();
$form->roll->input->created->setDisplayColumn(false);

$form->roll->addInput('updated', 'sqlplaintext');
$form->roll->input->updated->setTitle('updated');
$form->roll->input->updated->setDateFormat();
$form->roll->input->updated->setDisplayColumn(false);

$form->roll->onDelete('_cpanel_user_fcm_delete');
$form->roll->action();
echo $form->roll->getForm();
echo msg('Please do not remove topic\'s created by system!!', 'warning');

function _cpanel_user_fcm_delete($ids)
{
	global $db, $form;
	ids($ids);
	if (!empty($ids))
	{
		_func('alert');
		$arr = $db->getAll("SELECT * FROM `bbc_user_push_topic` WHERE `id` IN ({$ids})");
		foreach ($arr as $topic)
		{
			alert_push_topic_delete($topic);
		}
		$form->roll->setFailDeleteMessage('system will proceed to delete the topic(s) after removing all the subscribers');
		return false;
	}
}