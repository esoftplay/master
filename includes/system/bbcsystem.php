<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (function_exists('ioncube_loader_version'))
{
	$i = intval(ioncube_loader_version());
	switch ($i)
	{
		case 9:
		case 10:
			require_once __DIR__.'/'.$i.'.php';
			break;
		default:
			require_once __DIR__.'/8.php';
			break;
	}
}else{
	echo '<a href="https://www.ioncube.com/loader-wizard/loader-wizard.zip">click here</a> to get loader';
}