<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$Content = _class('content');

$config = get_config('content','content');
if(preg_match('~^([0-9]+)\-~s', $Bbc->mod['task'], $m))
{
	$Bbc->mod['task'] = 'tag';
	$_GET['id'] = $m[1];
}