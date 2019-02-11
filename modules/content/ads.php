<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$msg = lang('invalid content ad');
if (!empty($_GET['id']))
{
	$i  = str_replace(' ', '+', urldecode($_GET['id']));
	$i  = decode($i);
	$r  = explode('#', $i);
	$id = 0;
	if (is_numeric($r[0]))
	{
		if (strtotime($r[1]) > time())
		{
			$id = intval($r[0]);
		}
	}
	if ($id > 0)
	{
		$data = $db->getRow("SELECT * FROM `bbc_content_ad` WHERE `id`={$id}");
		if (!empty($data))
		{
			if (empty($data['active']))
			{
				$msg = lang('inactive content ad');
			}else{
				if (!empty($data['link']))
				{
					$hit = $data['hit']+1;
					$q   = "UPDATE `bbc_content_ad` SET `hit`={$hit}, `hit_last`=NOW() WHERE `id`={$id}";
					$db->Execute($q);
					if (!is_url($data['link']))
					{
						$data['link'] = 'http://'.$data['link'];
					}
					redirect($data['link']);
				}
			}
		}
	}
}
if (!empty($msg))
{
	echo msg($msg, 'danger');
}