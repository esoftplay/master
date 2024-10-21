<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (empty($_SESSION['bbcAuthAdmin']['id']))
{
	echo msg('You must login as administrator to access this feature!', 'danger');
}else
if (defined('_IMAGE_STORAGE'))
{
	$sys->stop(true);
	echo msg('Mohon maaf, fitur file manager tidak akan berjalan jika menggunakan stateless server');
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