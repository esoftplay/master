<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

#!/includes/lib/pea/form/FormOrderby.php
if (!empty($_POST['token']) && !empty($_POST['ids']))
{
	$output = array('success' => 0, 'output' => 'Failed to save data sorting!');
	$token  = json_decode(decode(str_replace(' ', '+', urldecode($_POST['token']))), 1);
	$ids    = trim(preg_replace('~[^0-9,]+~s', ',', $_POST['ids']), ',');
	if (!empty($ids) && !empty($token['expire']) && $token['expire'] > time())
	{
		// extract($token);// tableId, fieldName, tableName, sqlCondition, sqlOrder
		$tableId      = @$token['tableId'];
		$fieldName    = @$token['fieldName'];
		$tableName    = @$token['tableName'];
		$sqlCondition = @$token['sqlCondition'];
		$sqlOrder     = @$token['sqlOrder'];
		if (preg_match('~([a-z0-9_]+)~is', $tableName, $m))
		{
			$tableName = $m[1];
		}
		if (!empty($token['db']))
		{
			$db = ${$token['db']};
		}
		$j    = 0;
		$q    = "SELECT `{$tableId}`, `{$fieldName}` FROM `{$tableName}` {$sqlCondition} {$sqlOrder} ";
		$data = $db->getAssoc($q);
		$r_id = array_unique(explode(',', $ids));
		foreach ($r_id as $id)
		{
			$j++;
			if ($data[$id] != $j)
			{
				if (!$db->Execute("UPDATE `{$tableName}` SET `{$fieldName}`=$j WHERE `{$tableId}`={$id}"))
				{
					$output['output'] = 'Failed to save sorting number '.$j;
					break;
				}
			}
		}
		if (count($r_id)==$j)
		{
			$output = array('success' => 1, 'output' => 'Data sorting has been saved');
		}
	}
	output_json($output);
}