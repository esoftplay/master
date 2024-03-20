<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Detail');
$id = intval($_GET['id']);

$form = _lib('pea',  'bbc_user_push_topic');
$form->initEdit(!empty($id) ? 'WHERE id='.$id : '');

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle(!empty($id) ? 'Edit Topic' : 'Add Topic');

$form->edit->addInput('name','sqlplaintext');
$form->edit->input->name->setTitle('Topic');

$form->edit->addInput('description', 'textarea');

$form->edit->addInput('ids', 'textarea');
$form->edit->input->ids->setTitle('New subscribers (Insert $user_ids with space separated)');
// $form->edit->input->ids->setCodeEditor(true, 'basic');
$form->edit->input->ids->setIsIncludedInUpdateQuery(false);
$form->edit->input->ids->setIsIncludedInSelectQuery(false);

$form->edit->addInput('user_id', 'selecttable');
$form->edit->input->user_id->setTitle('Creator');
$form->edit->input->user_id->setReferenceTable('bbc_user');
$form->edit->input->user_id->setReferenceField( 'username', 'id' );
$form->edit->input->user_id->addOption( 'system', '0' );
$form->edit->input->user_id->setPlaintext(true);

$form->edit->addInput('created', 'sqlplaintext');
$form->edit->input->created->setTitle('Created');
$form->edit->input->created->setDateFormat();

$form->edit->addInput('updated', 'sqlplaintext');
$form->edit->input->updated->setTitle('updated');
$form->edit->input->updated->setDateFormat();

$form->edit->onSave('_cpanel_user_fcm_topic_nusubs');
$form->edit->action();
echo $form->edit->getForm();

function _cpanel_user_fcm_topic_nusubs($id)
{
	global $db;
	if (!empty($_POST['edit_ids']))
	{
		$topic = $db->getRow("SELECT * FROM `bbc_user_push_topic` WHERE `id`={$id}");
		if (!empty($topic['id']))
		{
			_func('alert');
			$ids = preg_replace('~\s+~s', ',', $_POST['edit_ids']);
			$ids = explode(',', $ids);
			alert_fcm_subscribe($ids, $topic);
		}
	}
}

include 'fcm_member.php';