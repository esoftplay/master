<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
$_POST:
username && password
email
*/
$msg      = '';
$fail_txt = !empty($_SERVER['REMOTE_ADDR']) ? _CACHE.'failed_login/'.$_SERVER['REMOTE_ADDR'].'.txt' : '';
$fails_no = 0;
if (!empty($fail_txt))
{
	$fails_no = intval(file_read($fail_txt));
}
if ($fails_no >= 3)
{
	$msg = 'You\'ve been failed to login 3 times or more!';
}
if (!empty($msg))
{
	$data_output['message'] = $msg;
}else{
	// JIKA LOGIN HANYA MENGGUNAKAN PASSWORD
	if (empty($_POST['username']) && empty($_POST['password']) && !empty($_POST['email']))
	{
		$email = _class('crypt')->decode($_POST['email']);
		if (is_email($email))
		{
			$user_id = $db->getOne("SELECT `id` FROM `bbc_user` WHERE `username`='{$email}'");
			if (empty($user_id))
			{
				$user_id = $db->getOne("SELECT `user_id` FROM `bbc_account` WHERE `email`='{$email}'");
			}
			if (!empty($user_id))
			{
				$fetchuser   = $db->getRow("SELECT `username`, `password` FROM `bbc_user` WHERE `id`='{$user_id}'");
				$_POST['username'] = _class('crypt')->encode($fetchuser['username']);
				$_POST['password'] = _class('crypt')->encode(decode($fetchuser['password']));
			}
		}
	}
	// MULAI PROSES LOGIN
	if (!empty($_POST['username']) && !empty($_POST['password']))
	{
		$msg = '';
		$usr = _class('crypt')->decode($_POST['username']);
		$pwd = _class('crypt')->decode($_POST['password']);
		$out = user_login($usr, $pwd, 0);
		switch ($out)
		{
			case 'allowed':
				$data_output = array(
					'ok'      => 1,
					'message' => 'success',
					'result'  => $user
					);
				break;
			case 'inactive':
				$msg = "Your account has been disabled.\nFor further information, please contact administrator";
			break;
			case 'notallowed':
				$msg = "Your account is not allowed to access this section.";
			break;
			case 'none':
				$msg = "Invalid Username or Password";
			break;
		}
		if (!empty($msg))
		{
			$data_output['message'] = $msg;
		}
	}else{
		$data_output['message'] = 'Please insert username along with password';
	}
}