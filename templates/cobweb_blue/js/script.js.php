<?php
if ( ! defined('_VALID_BBC')){
	ob_start('ob_gzhandler');
	header('content-type: text/javascript; charset: UTF-8');
	header('cache-control: must-revalidate');
	$offset = 60 * 60 * 24 * 365;
	$expire = 'expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
	header($expire);
	$v = $_GET['v'];
	if(!empty($v)) {
		$file = str_replace('/', '_', $v).'.css';
	}
	if(is_file($file)) include $file;
	die();
}
