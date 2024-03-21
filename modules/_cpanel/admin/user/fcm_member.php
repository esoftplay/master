<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = intval($_GET['id']);
$group = $db->getRow("SELECT * FROM `bbc_user_push_topic` WHERE `id`={$id}");
if (!empty($group['id']))
{
	$form = _lib('pea',  'bbc_user_push_topic_list');
	$form->initRoll("WHERE topic_id={$id} ORDER BY list_id DESC", 'list_id');

	// $form->roll->setNumRows(1);
	$form->roll->setSaveTool(false);
	if (!empty($_GET['is_ajax']))
	{
		$form->roll->setDeleteTool(false);
	}else{
		$form->roll->setDeleteButton( 'submit_delete', 'Unsubs', 'ban-circle', 'Unsubscribe' );
	}

	$form->roll->addInput('header', 'header');
	$form->roll->input->header->setTitle('Member of '.$group['name']);

	$form->roll->addInput('push_id', 'selecttable');
	$form->roll->input->push_id->setTitle('Username');
	$form->roll->input->push_id->setReferenceTable('bbc_user_push');
	$form->roll->input->push_id->setReferenceField( 'CONCAT(username, " (",device,")")', 'id' );
	$form->roll->input->push_id->setPlaintext(true);

	$form->roll->onDelete('_cpanel_user_fcm_member_delete');
	echo $form->roll->getForm();
}else{
	echo msg('Group is not found', 'danger');
	$group['name'] = 'none';
}
if (!empty($_GET['is_ajax']))
{
	die();
}else{
	$sys->nav_add('Member '.$group['name']);
	echo msg('if you delete it, the deleted members will unsubscribe automatically', 'warning');
}

function _cpanel_user_fcm_member_delete($ids)
{
	global $db, $group;
	ids($ids);
	if (!empty($ids))
	{
		_func('alert');
		$topics = $group['name'];
		$tokens = $db->getCol("SELECT p.`token` FROM `bbc_user_push_topic_list` AS l LEFT JOIN `bbc_user_push` AS p ON (p.`id`=l.`push_id`) WHERE l.`list_id` IN ({$ids})");
		alert_fcm_topic_unsubscribe($tokens, $topics);
	}
}