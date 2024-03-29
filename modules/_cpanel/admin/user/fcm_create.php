<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'bbc_user_push_topic');
$form->initEdit('');

$form->edit->setFormName('fcmtopicnew');

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle('New Topic');

$form->edit->addInput('name', 'text');
$form->edit->input->name->setTitle('Topic\'s name');
$form->edit->input->name->setRequire('any');
$form->edit->input->name->setTip('use character 0-1_a-z only');

$form->edit->addInput('description', 'textarea');
$form->edit->input->description->setRequire('any');


$form->edit->addInput('ids', 'textarea');
$form->edit->input->ids->setTitle('New subscribers (Insert $user_ids with space separated)');
$form->edit->input->ids->setRequire('any');
// $form->edit->input->ids->setCodeEditor(true, 'basic');
$form->edit->input->ids->setIsIncludedInUpdateQuery(false);

$form->edit->addInput('user_id', 'hidden');
$form->edit->input->user_id->setDefaultValue($user->id);

$form->edit->onSave('_cpanel_user_fcm_topic_create');
$form->edit->action();

echo $form->edit->getForm();

function _cpanel_user_fcm_topic_create($id)
{
	global $db;
	$topic = $db->getRow("SELECT * FROM `bbc_user_push_topic` WHERE `id`={$id}");
	if (!empty($topic['id']))
	{
		$name = menu_save($topic['name'], false, '_');
		if ($name != $topic['name'])
		{
			$topic['name'] = $name;
			$db->Update('bbc_user_push_topic', ['name' => $name], $id);
		}
		_func('alert');
		$ids = preg_replace('~\s+~s', ',', $_POST['fcmtopicnew_ids']);
		$ids = explode(',', $ids);
		alert_fcm_subscribe($ids, $topic);
	}
}
