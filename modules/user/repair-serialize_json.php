<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tables = array(
'bbc_account', 
'bbc_account_temp', 
'bbc_block', 
'bbc_config', 
'bbc_content', 
'bbc_content_cat', 
'bbc_content_trash', 
'bbc_content_type'
);
$row_per_action = 10;
foreach ($tables as $table)
{
	$count = $db->getOne("SELECT COUNT(*) FROM {$table} WHERE 1");
	if ($count > 0)
	{
		$done = 0;
		while ($done < $count)
		{
			$q = "SELECT * FROM {$table} WHERE 1 ORDER BY id ASC LIMIT {$done}, {$row_per_action}";
			$r = $db->getAll($q);
			foreach ($r as $data)
			{
				$fields = array();
				foreach ($data as $field => $value)
				{
					if (preg_match('~^[sibao]:.*?[\}\;]$~is', trim($value)))
					{
						$fields[$field] = json_encode(unserialize($value));
					}
				}
				if (!empty($fields))
				{
					$add_sql = array();
					foreach ($fields as $field => $value)
					{
						$add_sql[] = "`{$field}`='{$value}'";
					}
					$q = "UPDATE {$table} SET ".implode(', ', $add_sql)." WHERE id=".$data['id'];
					$db->Execute($q);
				}
				$done++;
			}
		}
	}
}
$sys->clean_cache();
