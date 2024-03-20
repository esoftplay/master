<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = intval($_GET['id']);
$group = $db->getRow("SELECT * FROM `bbc_user_push_topic_list` WHERE `user_id`={$id}");
if (!empty($group['list_id']))
{
	$usr  = $db->getOne("SELECT `username` FROM `bbc_user` WHERE `id`={$id}");
	$form = _lib('pea',  'bbc_user_push_topic_list');
	$form->initRoll("WHERE user_id={$id} ORDER BY list_id DESC", 'list_id');

	$form->roll->setSaveTool(false);
	if (!empty($_GET['is_ajax']))
	{
		$form->roll->setDeleteTool(false);
	}else{
		$form->roll->setDeleteButton( 'submit_delete', 'Unsubs', 'ban-circle', 'Unsubscribe' );
	}

	$form->roll->addInput('header', 'header');
	$form->roll->input->header->setTitle('Topics of '.$usr);

	$form->roll->addInput('topic_id', 'selecttable');
	$form->roll->input->topic_id->setTitle('Topic');
	$form->roll->input->topic_id->setReferenceTable('bbc_user_push_topic');
	$form->roll->input->topic_id->setReferenceField( 'name', 'id' );
	$form->roll->input->topic_id->setLinks('index.php?mod=_cpanel.user&act=fcm_member');
	$form->roll->input->topic_id->setPlaintext(true);

	$form->roll->onDelete('_cpanel_user_member_topic_delete');
	echo $form->roll->getForm();
}else{
	echo msg('no topic is found', 'danger');
}
if (!empty($_GET['is_ajax']))
{
	die();
}else{
	$sys->nav_add('Topics of '.@$usr);
	echo msg('if you delete it, "'.@$usr.'" will unsubscribe automatically', 'warning');
}

function _cpanel_user_member_topic_delete($ids)
{
	global $db, $id;
	ids($ids);
	if (!empty($ids))
	{
		_func('alert');
		$tokens = $db->getCol("SELECT `token` FROM `bbc_user_push` WHERE `user_id`={$id} AND `type`=1");
		$topics = $db->getCol("SELECT t.`name` FROM `bbc_user_push_topic_list` AS l LEFT JOIN `bbc_user_push_topic` AS t ON (t.`id`=l.`topic_id`) WHERE l.`list_id` IN ({$ids})");
		foreach ($topics as $topic)
		{
			alert_fcm_topic_unsubscribe($tokens, $topic);
		}
	}
}