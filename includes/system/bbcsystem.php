<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (defined('_BBCSYS'))
{
	require_once _BBCSYS;
}else{
	if (function_exists('ioncube_loader_version'))
	{
		$i = intval(ioncube_loader_version());
		if (file_exists( __DIR__.'/'.$i.'.php'))
		{
			require_once __DIR__.'/'.$i.'.php';
		}else{
			echo 'ioncube_loader_version: '.$i.' is not ready yet';
		}
	}else{
		echo '<a href="https://www.ioncube.com/loader-wizard/loader-wizard.zip">click here</a> to get loader';
	}
}
