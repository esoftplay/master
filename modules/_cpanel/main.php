<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$config = get_config('_cpanel', 'config');
$home   = $Bbc->home;
if (!empty($config['home_list']))
{
	switch ($config['home_list'])
	{
		case '3': // Most popular content in a month
			$home = 'content.popular';
			break;
		case '2': // Latest content
			$home = 'content.latest';
			break;
	}
}
$mod        = explode('.', $home);
$home_limit = !empty($config['home_limit']) ? intval($config['home_limit']) : 0;

if (!empty($mod[0]))
{
	if (empty($mod[1]))
	{
		$mod[1] = 'main';
	}
	$sys->module_change($mod[0]);
	$Bbc->mod['task'] = $mod[1];
	$sys->no_tpl      = 1;
	$path             = _ROOT.'modules/'.$mod[0].'/';
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
		$tpl         = $Bbc->mod['task'];
		if ($home_limit > 0 && !empty($cat['total']) && !empty($cat['total_page']) && !empty($cat['config']['tot_list']))
		{
			$cat['total_page'] = ceil($home_limit/$cat['config']['tot_list']);
			$cat['total']      = $cat['total'] < $home_limit ? intval($cat['total']) : $home_limit;
			if (($page+1)==$cat['total_page'] && !empty($cat['list']))
			{
				$count = count($cat['list']);
				$limit = $home_limit - ($page*$cat['config']['tot_list']);
				if ($count > $limit)
				{
					$cat['list'] = array_slice($cat['list'], 0, $limit);
				}
			}
		}
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
		include tpl($Bbc->mod['name'].'/'.$tpl, 'main.html.php');
	}
}
