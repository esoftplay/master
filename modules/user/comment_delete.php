<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['token']))
{
	@list($token, $config) = _class('comment')->decode($_GET['token']);
	if (!empty($config['db']))
	{
		$db = ${$config['db']};
		unset($config['db']);
	}

	$output = array('success'=>0);
	$id     = (!empty($_GET['cid']) && is_numeric($_GET['cid'])) ? intval($_GET['cid']) : intval(decode(str_replace(' ', '+', urldecode(@$_GET['cid']))));
	if ( !empty($id)
		&& !empty($config['id'])
		&& !empty($config['expire'])
		&& $config['expire'] > time())
	{
		$data = $db->getRow("SELECT * FROM `".$config['table']."` WHERE `id`={$id}");
		if (!empty($data))
		{
			$ids = user_comment_ids($id, $config['table']);
			ids($ids);
			$q = "DELETE FROM `".$config['table']."` WHERE id IN ({$ids})";
			$db->Execute($q);
			if ($db->Affected_rows())
			{
				$output['success'] = 1;
				if (!empty($data['par_id']))
				{
					$fields = array('`reply_all`=(`reply_all` - 1)');
					if ($data['publish'])
					{
						$fields[] = '`reply_on`=(`reply_on` - 1)';
					}
					$db->Execute("UPDATE `".$config['table']."` SET ".implode(', ', $fields)." WHERE id=".$data['par_id']);
				}
			}
		}
	}
	output_json($output);
}

function user_comment_ids($id, $table, $is_first_call = true)
{
	global $db;
	$output = array();
	if ($id > 0)
	{
		$q = "SELECT `id` FROM `{$table}` WHERE `par_id`={$id}";
		$r = $db->getCol($q);
		if ($is_first_call)
		{
			$output = array_merge(array($id), $r);
		}else{
			$output = $r;
		}
		foreach ($r as $i)
		{
			$output = array_merge($output, call_user_func(__FUNCTION__, $i, $table, false));
		}
	}
	return $output;
}