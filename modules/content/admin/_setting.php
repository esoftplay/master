<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$Content = _class('content');
if(preg_match('~^([0-9]+)_(.*?)$~', $Bbc->mod['task'], $m))
{
	$_GET['type_id'] = $m[1];
	$Bbc->mod['task']= $m[2];
}