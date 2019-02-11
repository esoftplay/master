<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (empty($_SESSION['bbcAuthAdmin']['id']))
{
	echo msg('You must login as administrator to access this feature!', 'danger');
}else{
	chdir(_ROOT.'includes/lib/ckeditor/filemanager/');
	$file = @$_GET['id'];
	if (substr($file, -4)!='.php')
	{
		$file .= '.php';
	}
	if (empty($file) || !file_exists($file))
	{
		$file = 'ajaxfilemanager.php';
	}
	unset($_GET['id'], $_GET['mod']);
	include $file;
	die();
}