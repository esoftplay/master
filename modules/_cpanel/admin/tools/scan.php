<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Scan Error');
$output = '';
$form = _lib('pea', 'jcamp_log');

if($_GET['act'] == 'scan_files')
{
	_func('path');
	$folder_errors = array();

	tools_scan_check_dir_perm($folder_errors, _ROOT.'modules/', '755');
	tools_scan_check_dir_perm($folder_errors, _ROOT.'blocks/', '755');
	tools_scan_check_dir_perm($folder_errors, _ROOT.'templates/', '755');
	$r = $db->getCol("SELECT name FROM bbc_template");
	foreach((array)$r AS $d)
	{
		tools_scan_check_file_perm($folder_errors, _ROOT.'templates/'.$d.'/css/style.css', '777');
	}
	tools_scan_check_dir_perm($folder_errors, _ROOT.'images/', '777');
	$output .= implode('', $folder_errors);
	if(empty($output))
	{
		echo msg('All important folders have been checked and the permission access are okay!', 'Message : ');
	}else{
		echo $output;
	}
}
if (!empty($_GET['return']))
{
	echo $sys->button($_GET['return']);
}
echo $sys->button(tools_url('scan_files'), 'Scan Files', 'fa-search').' ';
echo $sys->button(tools_url('scan_command'), 'Get Command', 'fa-terminal').' ';
echo $sys->button(tools_url('scan_chmod'), 'Chmod Tool', 'fa-lock').' ';
echo $sys->button(tools_url('scan_database'), 'Repair Database', 'fa-database');
function tools_scan_check_file_perm(&$folder_errors, $baseDir, $right_chmod)
{
	$permision = file_octal_permissions(fileperms($baseDir));
	if($permision != $right_chmod)
		$folder_errors[] = msg("Incorrect permision file ({$permision}), it should be {$right_chmod}", 'File : '.preg_replace('~^'.preg_quote(_ROOT, '~').'~is', '', $baseDir).'<br />');
}
function tools_scan_check_dir_perm(&$folder_errors, $baseDir, $right_chmod)
{
	$r = path_list_r($baseDir, true);
	foreach((array)$r AS $d)
	{
		if(is_dir($baseDir.$d))
		{
			$folder = $baseDir.$d.'/';
			$permision = file_octal_permissions(fileperms($folder));
			if($permision != $right_chmod)
			{
				$folder_errors[] = msg("Incorrect permision directory ({$permision}), it should be {$right_chmod}", 'Directory : '.preg_replace('~^'.preg_quote(_ROOT, '~').'~is', '', $folder).'<br />');
			}
			tools_scan_check_dir_perm($folder_errors, $folder, $right_chmod);
		}
	}
}