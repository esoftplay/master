<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$Msg = '';
if(!empty($_POST['submit']))
{
	$c = _lib('captcha');
	if(!$c->Validate())
	{
		$Msg = $c->msg();
	} else {
		if(user_reminder($_POST['email'], true))
		{
			redirect($Bbc->mod['circuit'].'.forget-finished');
		}else{
			$Msg = lang('forget password failed');
		}
	}
}
include tpl('forget-password.html.php');
