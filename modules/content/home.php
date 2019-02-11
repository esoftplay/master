<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$output = array('config' => get_config('content', 'frontpage'), 'data' => content_frontpage());
include tpl('home.html.php');