<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$id         = @intval($_GET['id']);
$account_id = $db->getOne("SELECT id FROM bbc_account WHERE user_id={$id}");
$form       = _class('params');
$params     = array(
	'title'       => $account_id ? 'Edit User Profile' : 'Add User Profile',
	'table'       => 'bbc_account',
	'config_pre'  => array(),
	'config'      => user_field($id),
	'config_post' => array(),
	'pre_func'    => '_is_email_unique',
	'post_func'   => '_user_change',
	'name'        => 'params',
	'id'          => $account_id
	);

$params['config_pre'] = array(
	'username' => array(
		'text' => 'Username',
		'type' => 'plain'
		),
	'name' => array(
		'text'      => 'Name',
		'type'      => 'text',
		'mandatory' => '1'
		)
	);
$params['config_post'] = array(
	'email' => array(
		'text'      => 'Email',
		'type'      => 'text',
		'mandatory' => '1',
		'checked'   => 'email'
		)
	);
$form->set($params);
$form->set_encode(true);

function _is_email_unique(&$form)
{
	global $db;
	$output = '';
	if($form->is_updated)
	{
		$email = strtolower($_POST['email']);
		$q = "SELECT 1 FROM bbc_account WHERE email='{$email}' AND id!=".@intval($form->table_id);
		if($db->getOne($q))
		{
			$output = 'email is already in use by another user';
			$form->is_updated = false;
		}
	}
	return $output;
}
function _user_change($form)
{
	global $user, $id;
	user_call_func('user_change', $id);
}