<?php
if ( ! defined('_VALID_BBC')) {
	ob_start('ob_gzhandler');
	header('content-type: text/javascript; charset: UTF-8');
	header('cache-control: must-revalidate');
	$offset = 60 * 60 * 24 * 365;
	$expire = 'expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
	header($expire);
	include 'search.js';
	die();
}

$keyword		= @urldecode($_GET['id']);
$limitstart	= @intval($_GET['page']);
link_css('search.css');
$sys->nav_add('Search Result');
$conf = get_config('search', 'search');
if($conf['from'] == 2)
{
	include 'search-google.php';
}else{
	include 'search-database.php';
}
