<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$id= @intval(decode(urldecode($_GET['id'])));

$q = "SELECT * FROM bbc_user WHERE id=$id";
$data = $db->getRow($q);
if($db->Affected_rows())
{
	_func('user');
	$_POST = array(
		'usr' => $data['username']
	,	'pwd' => decode($data['password'])
	);
	include $Bbc->mod['root'].'login-action.php';
}
redirect(_URL);