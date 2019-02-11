<?php
ob_start('ob_gzhandler');
header('content-type: text/javascript; charset: UTF-8');
header('cache-control: must-revalidate');
$offset = 60 * 60 * 24 * 365; //cache 1 millennium
$expire = 'expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
header($expire);
$id = @$_GET['f'];
if(is_file('jquery.'.$id.'.js'))
include 'jquery.'.$id.'.js';