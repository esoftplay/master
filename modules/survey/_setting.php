<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$config = get_config('survey', 'main');
$sess		=  isset($_SESSION['survey']) ? $_SESSION['survey'] : array();
if($Bbc->mod['task'] != 'polling')
{
	if(!empty($config['template']))
	{
		$sys->link_set($Bbc->mod['root'].'templates/'.$config['template'].'/css/style.css');
		$sys->set_layout($Bbc->mod['root'].'templates/'.$config['template'].'/index.php');
	}else{
		$sys->link_css($Bbc->mod['url'].'templates/style.css');
	}
}
