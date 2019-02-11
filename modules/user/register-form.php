<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*=========================================
 * THIS FORM FOR USER REGISTRATION ONLY
 * IF YOU NEED USER FORM PLEASE MAY DO
 * INCLUDE user_form.php FROM ANY WHERE
 *========================================*/
if($user->id > 0 && $is_register_only)
{
	redirect('index.php?mod=user.account');
}
$id     = @intval($_GET['id']);
$form   = _class('params');
$params = array(
	'title'       => 'Registration Form',
	'table'       => 'bbc_account_temp',
	'config_pre'  => array(),
	'config'      => user_field(0, $id),
	'config_post' => array(),
	'pre_func'    => '_is_email_unique',
	'post_func'   => '_action_register',
	'name'        => 'params',
	'id'          => 0
);

$params['config_pre'] = array(
/*
	'username' => array(
		'text' => 'Username',
		'type' => 'plain'
	),
#*/
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
		'checked'   => 'email',
		'mandatory' => 1,
		'tips'      => 'email as username'
	),
	'vcode' => array(
		'text' => 'Validation Code',
		'type' => 'captcha'
	)
);
if($id)
{
	$params['config']['group_ids'] = array(
		'text'    => 'Group',
		'type'    => 'hidden',
		'default' => $id,
		'force'   => $id
	);
}
$form->set($params);
$form->set_encode(false);
echo '<h1>'.lang('Registration Form').'</h1>';
echo $form->show();

function _is_email_unique(&$form)
{
	global $db;
	$output = '';
	if($form->is_updated)
	{
		$email = strtolower($_POST['email']);
		if(isset($email) && is_email($email))
		{
			$q = "SELECT 1 FROM bbc_account WHERE email='$email'";
			$email_exists = $db->getOne($q);
			if($email_exists)
			{
				$output = lang('email is already exist in member data');
				$form->is_updated = false;
			}else{
				$q = "SELECT 1 FROM bbc_account_temp WHERE email='$email'";
				$email_exists = $db->getOne($q);
				if($email_exists)
				{
					$output = lang('email is already registered');
					$form->is_updated = false;
				}
			}
		}
	}
	return $output;
}
function _action_register(&$form)
{
	global $Bbc, $sys, $db;
	if($sys->module_name == 'user')
	{
		$q = "SELECT * FROM bbc_account_temp WHERE id=".$form->table_id;
		$data = $db->getRow($q);
		// UPDATE EMAIL && USERNAME && DATE && CODE
		# email
		$data['email'] = strtolower($data['email']);
		#username
		if(!$data['username']) $data['username'] = $data['email'];
		else $data['username'] = strtolower($data['username']);
		#code
		$data['code'] = _action_register_code($data['username']);

		$q="UPDATE bbc_account_temp SET
			`code`     = '".$data['code']."',
			`date`     = DATE_ADD(NOW(), INTERVAL +".intval(config('rules', 'register_expired'))." DAY ),
			`username` = '".$data['username']."',
			`email`    = '".$data['email']."',
			`active`   = 1
			WHERE `id` = $form->table_id";
		$db->Execute($q);

		// SET PARAM FOR EMAIL
		foreach($data AS $key => $value)
		{
			if($key != 'params')
			{
				$GLOBALS[$key] = $value;
			}else{
				$r = config_decode($value);
				foreach($r AS $var => $val)
				{
					if(preg_match('~^[a-z0-9_]+$~is', $var))
						$GLOBALS[$var] = $val;
				}
			}
		}
		$email_to = array($data['email']);
		if(config('rules', 'register_monitor')) {
			$email_to[] = config('email', 'address');
		}
		if(config('rules', 'register_auto'))
		{
			$GLOBALS['validateLink'] = site_url($Bbc->mod['circuit'].'.register-validate&id='.$data['code']);
			$sys->mail_send($email_to, 'register_confirm');
			redirect($Bbc->mod['circuit'].'.register-finish');
		}else{
			$sys->mail_send($email_to, 'register_pending');
			redirect($Bbc->mod['circuit'].'.register-finish&pending=1');
		}
	}
}
function _action_register_code($code = '')
{
	global $db;
	$output = md5(encode(preg_replace("/[^a-z0-9]/is", "", base64_encode(rand().$code.rand()))));
	$q = "SELECT 1 FROM bbc_account_temp WHERE code='$output'";
	if($db->getOne($q)) return _action_register_code($output);
	else return $output;
}
