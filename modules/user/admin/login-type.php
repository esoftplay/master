<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$r = config('logged');
$type = @intval($r['method_admin']);
if($type == 1 || $type == 2 || $type == 3 || $type == 4)
{
	$arr_auth = array(
		array()
	, array('Google'  , 'https://accounts.google.com/b/0/EditPasswd?hl=en')
	, array('Yahoo'   , 'https://edit.yahoo.com/config/change_pw')
	, array('Facebook', 'https://www.facebook.com/settings?tab=account&section=password')
	, array('Twitter' , 'https://twitter.com/settings/password')
	);
	$type_name = $arr_auth[$type][0];
}