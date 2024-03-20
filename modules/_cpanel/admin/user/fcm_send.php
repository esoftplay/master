<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'bbc_user_push_sending');
$form->initEdit('');

$form->edit->setFormName('fcmsendnew');
$form->edit->setSaveButton( 'submit_send', 'SEND', 'send' );

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle('Send Notification');

$form->edit->addInput('to','text');
$form->edit->input->to->setTitle('Recipient');
$form->edit->input->to->setRequire();
$form->edit->input->to->AddTip('Example:<ul>
	<li><b>userAll</b>: all members</li>
	<li><b>$id</b>: single member by user_id</li>
	<li><b>$topic_name</b>: all subscribers in particular topic </li>
	<li><b>$username</b>: member\'s username </li>
	<li><b>group:$group_id</b>: all member in user group (only if it\'s available in app) </li>
</ul>');

$form->edit->addInput('title','text');
$form->edit->input->title->setTitle('Title');
$form->edit->input->title->setRequire();
$form->edit->input->title->setCaption('Max. 80 chars');

$form->edit->addInput('message', 'textarea');
$form->edit->input->message->setTitle('Body Message');
$form->edit->input->message->setRequire();
$form->edit->input->message->setCaption('Max. 141 chars');

$form->edit->addInput('module', 'text');
$form->edit->input->module->setTitle('Mobile App\'s module');
$form->edit->input->module->setDefaultValue('content');
$form->edit->input->module->AddTip('Eg. $module/$task');

$form->edit->addInput('arguments', 'textarea');
$form->edit->input->arguments->setTitle('Module\'s arguments in json');
// $form->edit->input->arguments->setDefaultValue('{}');
// $form->edit->input->arguments->setCodeEditor(true, 'js');

$form->edit->addInput('action', 'text');
$form->edit->input->action->setTitle('Action');
$form->edit->input->action->setDefaultValue('default');
$form->edit->input->action->AddTip('Identification string if an action is added where the notification will be treated differently following the script in the mobile app. Eg. default or alert');

$form->edit->addInput('user_id', 'hidden');
$form->edit->input->user_id->setDefaultValue($user->id);

$form->edit->onSave('_cpanel_user_fcm_send_create');
$form->edit->action();

$form->initSearch();
$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('to,title,message,module,arguments,action', false);

$form->search->addInput('status', 'select');
$form->search->input->status->setTitle('Status');
$form->search->input->status->addOption('--status--', '');
$form->search->input->status->addOption('on process', '0');
$form->search->input->status->addOption('done', '1');

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

echo $form->search->getForm();

$form->initRoll("{$add_sql} ORDER BY id DESC");
$form->roll->setFormName('fcmsendlist');
$form->roll->setSaveTool(false);

$form->roll->addInput('to','sqlplaintext');
$form->roll->input->to->setTitle('Recipient');
$form->roll->input->to->setDisplayColumn(true);

$form->roll->addInput('title','sqlplaintext');
$form->roll->input->title->setTitle('Title');
$form->roll->input->title->setDisplayColumn(true);

$form->roll->addInput('message','sqlplaintext');
$form->roll->input->message->setDisplayColumn(false);

$form->roll->addInput('sent', 'sqlplaintext');
$form->roll->input->sent->setNumberFormat();
$form->roll->input->sent->setDisplayColumn(true);

$form->roll->addInput('status', 'select');
$form->roll->input->status->setTitle('Status');
$form->roll->input->status->addOption('on process', '0');
$form->roll->input->status->addOption('done', '1');
$form->roll->input->status->setDisplayColumn(true);
$form->roll->input->status->setPlaintext(true);

$form->roll->addInput('user_id', 'selecttable');
$form->roll->input->user_id->setTitle('Sender');
$form->roll->input->user_id->setReferenceTable('bbc_user');
$form->roll->input->user_id->setReferenceField( 'username', 'id' );
$form->roll->input->user_id->setDisplayColumn(false);
$form->roll->input->user_id->setPlaintext(true);

$form->roll->onDelete('_cpanel_user_fcm_send_delete', '', true);

echo $form->roll->getForm();
echo $form->edit->getForm();

function _cpanel_user_fcm_send_create($id)
{
	global $db;
	$data = $db->getRow("SELECT * FROM `bbc_user_push_sending` WHERE `id`={$id}");
	_func('alert');
	if (is_string($data['to']) && !is_email($data['to']) && !is_numeric($data['to']) && substr($data['to'], 0, 6) != 'group:')
	{
		$data['to'] = '/topics/'.$data['to'];
	}
	$data['arguments'] = json_decode($data['arguments'], 1);
	alert_push($data['to'], $data['title'], $data['message'], $data['module'], $data['arguments'], $data['action'], $data['id']);
}

function _cpanel_user_fcm_send_delete($ids)
{
	global $db;
	$db->Execute("ALTER TABLE `bbc_user_push_sending` AUTO_INCREMENT = 1");
}