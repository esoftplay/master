<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->module_change('content');
$Content = _class('content');
include_once $Bbc->mod['root'].'content.php';

$sys->module_clear();
