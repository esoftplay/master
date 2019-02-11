<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$mod = explode('.', @$_GET['_mod']);
if (!empty($mod[0]))
{
	if (empty($mod[1]))
	{
		$mod[1] = 'main';
	}
	$_GET['mod'] = implode('.', $mod);
	$sys->module_change($mod[0]);
	$Bbc->mod['task'] = $mod[1];
	$sys->no_tpl = 1;
	$path = _ROOT.'modules/'.$mod[0].'/';
	if (file_exists($path.'_switch.php'))
	{
		ob_start();
			if (file_exists($path.'_function.php'))
			{
				include_once $path.'_function.php';
			}
			if (file_exists($path.'_setting.php'))
			{
				include $path.'_setting.php';
			}
			include $path.'_switch.php';
			$data_html = ob_get_contents();
		ob_get_clean();
		$sys->no_tpl = 0;
		$tpl = $Bbc->mod['task'];
		if ($Bbc->mod['name'] == 'content')
		{
			switch ($Bbc->mod['task'])
			{
				case 'main':
				case 'list':
				case 'tag':
				case 'type':
				case 'latest':
				case 'popular':
				case 'home':
				case 'search':
				case 'article':
				case 'gallery':
				case 'download':
				case 'video':
				case 'audio':
				case 'posted':
					$tpl = 'list.html.php';
					break;
			}
		}
		include tpl($Bbc->mod['name'].'/'.$tpl, 'default.html.php');
	}
}
