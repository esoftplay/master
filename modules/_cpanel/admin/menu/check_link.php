<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_POST['link']))
{
	include 'menuQRY.php';
	$link1     = $_POST['link'];
	$link2     = link_parse($link1);
	$module_id = 0;
	if (preg_match('~mod=([^\.]+)~', $link2, $m))
	{
		$module = $m[1];
		foreach ($r_module as $d)
		{
			if ($module==$d['name'])
			{
				$module_id = $d['id'];
				break;
			}
		}
	}
	$output = array(
		'ok'     => ($link1!=$link2) ? 1 : 0,
		'result' => array(
			'link'      => $link2,
			'module_id' => $module_id,
			)
		);
	output_json($output);
}