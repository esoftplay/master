<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->link_js('http://www.google.com/jsapi');
preg_match('~(?:www\.)?(.*?)$~is', $_SERVER['HTTP_HOST'], $m);
include tpl('search-google.html.php');
