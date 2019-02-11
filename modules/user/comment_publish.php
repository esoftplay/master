<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['token']))
{
	@list($token, $config) = _class('comment')->decode($_GET['token']);
	if (!empty($config['db']))
	{
		$db = ${$config['db']};
		unset($config['db']);
	}

	$output  = array('success' => 0);
	$id      = (!empty($_GET['cid']) && is_numeric($_GET['cid'])) ? intval($_GET['cid']) : intval(decode(str_replace(' ', '+', urldecode(@$_GET['cid']))));
	$publish = $_GET['id'] == 'publish' ? 1 : 0;
	if ( !empty($id)
		&& !empty($config['id'])
		&& !empty($config['expire'])
		&& $config['expire'] > time())
	{
		$q = "UPDATE `".$config['table']."` SET `publish`={$publish} WHERE `id`=".$id;
		if ($db->Execute($q))
		{
			if ($db->Affected_rows())
			{
				$output = array('success'=>1);
				$par_id = $db->getOne("SELECT `par_id` FROM `".$config['table']."` WHERE `id`={$id}");
				if ($par_id)
				{
					$a_q = $publish ? '+' : '-';
					$db->Execute("UPDATE `".$config['table']."` SET `reply_on`=(`reply_on`{$a_q}1) WHERE id={$par_id}");
				}
			}
		}
	}
	output_json($output);
}