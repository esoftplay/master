<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$code = decode(urldecode(@$_GET['id']));
if (!empty($code))
{
	$codes = explode('-', $code);
	if (time() < $codes[0] && $codes[1] > 0)
	{
		$id = intval($codes[1]);
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
	}
}
