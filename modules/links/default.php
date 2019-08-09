<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$txt = preg_replace('~^'._URL.'links/~is', '', seo_uri());
$out = 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
if (!empty($txt))
{
	$id = decode($txt);
	if (!empty($id) && is_numeric($id))
	{
		$data = $db->getRow("SELECT * FROM `links_share` WHERE id={$id}");
		if (!empty($data))
		{
			if (!empty($data['publish']))
			{
				$db->Update('links_share', ['total' => ($data['total']+1)], $data['id']);
				redirect($data['link']);
			}else{
				$out = msg(lang('this link is no longer available'), 'warning');
			}
		}
	}
}
echo $out;