<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include 'login-type.php';
if (empty($type))
{
	include 'user.password.php';
}else{
	$link = $arr_auth[$type][1];
	echo msg('It looks like you use '.$type_name.' account to login into this admin area, please
	         <a href="'.$link.'" onclick="window.open(this.href,\'login\',\'height=480,width=800\'); return false;" />Click here</a>
	         to change your '.$type_name.' password', 'warning');
}