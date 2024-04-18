<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$tabs = array(
	'Edit User'		=> $userEdit,
	'Edit Contact'=> $form->show()
);
echo implode('', $tabs);
$is_push = $db->getOne("SHOW TABLES LIKE 'bbc_user_push_topic_list'");
if (defined('_FCM_SENDER_ID') && !empty($is_push))
{
	$form = _lib('pea',  'bbc_user_push_topic_list');
	$form->initRoll("WHERE user_id={$id} ORDER BY list_id DESC", 'list_id');

	$form->roll->setFormName('userTopics');
	$form->roll->setSaveTool(false);
	$form->roll->setDeleteTool(false);

	$form->roll->addInput('topic_id', 'selecttable');
	$form->roll->input->topic_id->setTitle('Member of Topics');
	$form->roll->input->topic_id->setReferenceTable('bbc_user_push_topic');
	$form->roll->input->topic_id->setReferenceField( 'CONCAT(name, " ( ",description," )")', 'id' );
	$form->roll->input->topic_id->setLinks('index.php?mod=_cpanel.user&act=fcm_member');
	$form->roll->input->topic_id->setPlaintext(true);

	$form->roll->action();
	echo $form->roll->getForm();
}