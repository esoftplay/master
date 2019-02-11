<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(isset($_POST['usr']))
{
	$output = user_login($_POST['usr'], $_POST['pwd'], 0, @intval($_POST['rememberme']));
	$msg = '';
	switch($output)
	{
		case 'allowed':
			$user_url = empty($_POST['url']) ? 'index.php?mod='.$Bbc->home_user : $_POST['url'];
			redirect($user_url);
		break;
		case 'inactive':
			$msg = "Your account has been disabled.<br />For further information, please contact administrator";
		break;
		case 'notallowed':
			$msg = "Your account is not allowed to access this section.";
		break;
		case 'none':
			$msg = "Invalid Username or Password";
		break;
	}
	echo msg($msg, 'Error : ');
}
