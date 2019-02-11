<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*
Example:
download_file('myfiles.xml', '/absolute/path/file/name.xml');
*/
function download_file($filename = '', $file = '', $exit = true)
{
	$data	= @is_file($file) ? @file_get_contents($file) : $file;
	if ($filename == '' OR $data == '')
	{
		return FALSE;
	}
	if (FALSE === strpos($filename, '.'))
	{
		return FALSE;
	}
	$x = explode('.', $filename);
	$extension = end($x);

	@include(_CONF.'mimes.php');

	if ( ! isset($mimes[$extension]))
	{
		$mime = 'application/octet-stream';
	}else{
		$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
	}

	if (@strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
	{
		header('Content-Type: "'.$mime.'"');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Content-Transfer-Encoding: binary");
		header('Pragma: public');
		header("Content-Length: ".strlen($data));
	}else{
		header('Content-Type: "'.$mime.'"');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Pragma: no-cache');
		header("Content-Length: ".strlen($data));
	}
	if($exit)	exit($data);
	else echo $data;
	return true;
}

/*
Example:
download_excel('myfiles', array(array('nama' => 'Aku', 'Alamat' => 'Tau'), array('nama' => 'Kamu', 'Alamat' => 'Bodo')), 'sheet 1');
*/
function download_excel($filename = '', $datas = array(), $sheet = 'sheet 1')
{
	if (empty($filename) || empty($datas))
	{
		return FALSE;
	}
	$filename = menu_save($filename);
	$sheet    = menu_save($sheet);
	$headers  = array_keys($datas[0]);
	_ext($filename, '.xlsx');
	foreach ($headers as $i => $header)
	{
		$headers[$i] = ucwords(str_replace('_', ' ', $header));
	}
	$excel = array(
		$sheet => array(
			$headers
			)
		);
	foreach ($datas as $data)
	{
		$excel[$sheet][] = array_values($data);
	}
	_lib('excel')->create($excel)->download($filename);
	die();
}