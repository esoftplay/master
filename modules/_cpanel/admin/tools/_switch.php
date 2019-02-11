<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$is_connect = false;
$ftp				= _class('ftp');
if(empty($_SESSION[_ROOT.'system_tools_ftp']))
{
	include 'ftp_connect.php';
}else{
	$con_ftp = (array)$_SESSION[_ROOT.'system_tools_ftp'];
	$con = $ftp->connect($con_ftp);
	if($con)
	{
		if($ftp->changedir($con_ftp['root']))
		{
			$is_connect = true;
		}
	}
	if(!$is_connect)
	{
		$_SESSION[_ROOT.'system_tools_ftp'] = array();
		unset($_SESSION[_ROOT.'system_tools_ftp']);
		$_SESSION[_ROOT.'system_tools_msg'] = 'Your session is expire, you need to re-login to your FTP account !';
		redirect($Bbc->mod['circuit'].'.tools');
	}
}
function tools_delete($path)
{
	global $ftp, $con_ftp;
	$filepath = preg_replace('~^'._ROOT.'~s', '', $path);
	return $ftp->delete_dir($filepath);
}
function tools_move($old, $new)
{
	global $ftp, $con_ftp;
	$old = preg_replace('~^'._ROOT.'~s', '', $old);
	$new = preg_replace('~^'._ROOT.'~s', '', $new);
	return $ftp->move($old, $new);
}
function tools_url($act, $is_return = true)
{
	global $Bbc;
	return $Bbc->mod['circuit'].'.tools&act='.$act.'&return='.urlencode(seo_uri());
}
if($is_connect)
{
	switch($_GET['act'])
	{
		case 'scan':
		case 'scan_files':
			include 'scan.php';
		break;
		case 'scan_command':
			include 'scan_command.php';
		break;
		case 'scan_chmod':
			include 'scan_chmod.php';
		break;
		case 'scan_database':
			include 'scan_database.php';
		break;
	// START INI BELUM KELAR
		case 'module':
			include 'module_function.php';
			include 'module.php';
		break;
		case 'module_download':
			include 'module_download.php';
		break;
	// END INI BELUM KELAR
		case 'block':
			include 'block_function.php';
			include 'block.php';
		break;
		case 'block_download':
			include 'block_download.php';
		break;

		case 'template':
			include 'template_function.php';
			include 'template.php';
		break;
		case 'template_download':
			include 'template_download.php';
		break;
		case 'language':
			include 'language.php';
		break;
		case 'language_download':
			include 'language_download.php';
		break;

		case 'licence':
			include 'licence.php';
		break;
		default:
			include 'index.php';
		break;
	}
	$ftp->close();
}
