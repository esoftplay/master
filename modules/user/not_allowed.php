<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

echo msg(lang('NOT ALLOWED'));
$_CONFIG['site']['title']	 .= " - 403 Forbidden";
$_CONFIG['site']['desc']		= "403 Forbidden";
$_CONFIG['site']['keyword']	= "403 Forbidden";
if(!@$user->is_login)
{
	include 'login-form.php';
}
