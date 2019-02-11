<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function search_permission($r, $user)
{
	$output = array();
	$r1= @repairExplode($user->group_id);
	foreach((array)$r AS $d)
	{
		if(!$d['protected'])
		{
			$output[] = $d['id'];
		}else{
			if($d['allow_group'] == ',all,')
			{
				if($user->id > 0) $output[] = $d['id'];
			}elseif($user->id > 0){
				$r0 = repairExplode($d['allow_group']);
				foreach($r0 AS $i)
				{
					if(in_array($i, $r1))
					{
						$output[] = $d['id'];
						break;
					}
				}
			}
		}
	}
	return $output;
}
