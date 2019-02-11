<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (@$_GET['id']=='00')
{
	$session = !empty($_SESSION[bbcAuth]) ? $_SESSION[bbcAuth] : array();
	$where   = array('`user_id`=0 AND `group_id`=0');
	if (!empty($session['id']))
	{
		$where[] = sprintf('`user_id`=%d AND `group_id`=0', $session['id']);
	}
	if (!empty($session['group_ids']))
	{
		foreach ($session['group_ids'] as $i)
		{
			$where[] = sprintf('`user_id`=0 AND `group_id`=%d', $i);
		}
	}
	$bracket = count($where) > 1 ? array('(', ')') : array('','');
	$where   = $bracket[0].'('.implode(') OR (', $where).')'.$bracket[1];
	$db->Execute("UPDATE `bbc_alert` SET `is_open`=1, `updated`=NOW() WHERE {$where} AND `is_open`=0");
	output_json(array('ok' => 1));
}else{
	$Bbc->alert_no_redirect = 1;
	include __DIR__.'/alert_list_detail.php';
}
