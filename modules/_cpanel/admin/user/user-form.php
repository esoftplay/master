<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$id       = @intval($_GET['id']);
$data     = $db->getRow("SELECT * FROM bbc_user WHERE id={$id}");
$userType = !empty($data) ? 'sqlplaintext' : 'text';
$actText  = !empty($data) ? 'Edit' : 'Add';
$add_sql  = !empty($data) ? 'WHERE id='.$id : '';

if (!empty($_POST['add_username']))
{
	$exist = 0;
	$usr   = trim(strtolower($_POST['add_username']));
	$q     = "SELECT 1 FROM `bbc_user` WHERE `username`='{$usr}'";
	$exist = $db->getOne($q);
	if (!$exist)
	{
		$exist = $db->getOne("SELECT 1 FROM `bbc_account` WHERE `username`='{$usr}' OR `email`='{$usr}'");
	}
	if ($exist)
	{
		$_POST = array();
		echo msg('Username is already exists in database', 'danger');
	}else{
		$_POST['add_username'] = $usr;
	}
}

$form3 = _lib('pea', 'bbc_user');
$form3->initEdit($add_sql);

$form3->edit->addInput('header', 'header');
$form3->edit->input->header->setTitle($actText.' User');
if (empty($data))
{
	$form3->edit->addInput('name', 'text');
	$form3->edit->input->name->setIsIncludedInUpdateQuery(false);
	$form3->edit->input->name->setRequire('any');
}
$form3->edit->addInput('username', $userType);
if ($userType == 'text')
{
	$form3->edit->input->username->setRequire('email');
}
$form3->edit->addInput('password', 'passwordConfirm');

$form3->edit->addInput('group_ids','multicheckbox');
$form3->edit->input->group_ids->setTitle('User Group');
$form3->edit->input->group_ids->setReferenceTable('bbc_user_group');
$form3->edit->input->group_ids->setReferenceField("CONCAT(IF(is_admin=0, 'public : ', 'admin : '), name)",'id');
$form3->edit->input->group_ids->setRequire('any');
$form3->edit->input->group_ids->addTip('<a href="index.php?mod=_cpanel.group" class="admin_link">Click here</a> to manage your User Groups with privilege of each group');

$form3->edit->addInput('active', 'checkbox');
$form3->edit->input->active->setTitle('Status');
$form3->edit->input->active->setCaption('Activate');

$form3->edit->onSave('cpanel_user_profile');
$userEdit = $form3->edit->getForm();
function cpanel_user_profile($id=0)
{
	global $db, $form3;
	if (empty($id))
	{
		$id = $GLOBALS['id'];
	}
	$data = $db->getRow("SELECT * FROM bbc_user WHERE id={$id}");
	$account = $db->getRow("SELECT * FROM bbc_account WHERE user_id={$id}");
	if (empty($account))
	{
		$name = $_POST[$form3->edit->input->name->name];
		$username = trim(strtolower($data['username']));
		$db->Execute("INSERT INTO bbc_account SET `user_id`={$id}, `name`='{$name}', `email`='{$username}', `username`='{$username}'");
		$db->Execute("UPDATE bbc_user SET `username`='{$username}', created=NOW() WHERE id={$id}");
		user_call_func('user_create', $id); // user_id
	}
}