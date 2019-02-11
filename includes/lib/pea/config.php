<?php
if ( ! defined('_ROOT'))
{
	$root = dirname(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))))).'/';
	if (file_exists($root.'config.php'))
	{
		include $root.'config.php';
	}else
	if (file_exists($root.'cfg.php'))
	{
		include $root.'cfg.php';
	}else{
		die('File '.$root.'config.php tidak ditemukan');
	}
}
if ( ! defined('_ADMIN'))
{
	define('_ADMIN', '');
}
$path = 'includes/lib/pea/';
define( '_LAYOUT_DIR', _URL._ADMIN );
define ('_PEA_ROOT', _ROOT.$path );
define ('_PEA_URL', _URL.$path );
