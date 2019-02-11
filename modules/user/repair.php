<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$file = 'repair-'.@menu_save($_GET['id']).'.php';

if (file_exists($file))
{
	include $file;
	if (!empty($_GET['redirect']))
	{
		redirect($_GET['redirect']);
	}else{
		redirect();
	}
}
