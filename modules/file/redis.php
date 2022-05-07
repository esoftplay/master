<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

require_once __DIR__.'/vendor/autoload.php';
$Bbc->redis = new Predis\Client();

function file_read($file = '', $method = 'r')
{
	global $Bbc;
	$file = str_replace(_ROOT, config('site', 'url')._URI, $file);
	return $Bbc->redis->get($file);
}

function file_write($path, $data='', $mode = 'w+')
{
	global $Bbc;
	$path = str_replace(_ROOT, config('site', 'url')._URI, $path);
	return $Bbc->redis->set($path, $data);
}

function file_delete($file='')
{
	global $Bbc;
	$file = str_replace(_ROOT, config('site', 'url')._URI, $file);
	return $Bbc->redis->delete($file);
}