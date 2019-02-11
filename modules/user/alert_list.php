<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$page    = @intval($_GET['page']);
$limit   = 10;
$start   = intval($page*$limit);
$session = !empty($_SESSION[bbcAuth]) ? $_SESSION[bbcAuth] : array();
// SET WHERE STATEMENT
$where  = array('`user_id`=0 AND `group_id`=0');
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
$where    = implode(' OR ', $where);
$is_admin = _ADMIN == '' ? 0 : 1; // check is this opened by admin
if (empty($is_admin))
{
	if (empty($session['id']))
	{
		$is_admin = 2;
	}
}

$query = "SELECT * FROM `bbc_alert` WHERE ({$where}) AND `is_admin` IN ({$is_admin},3) ORDER BY `id` DESC LIMIT {$start}, {$limit}";
$list  = $db->getAll($query);
if (!$db->resid)
{
	include_once __DIR__.'/repair-comment.php';
	$list = $db->getAll($query);
}
if (!empty($list))
{
	_func('alert');
	foreach ($list as $i => $data)
	{
		$list[$i] = alert_view($data);
	}
}
$found  = $db->getOne("SELECT COUNT(*) FROM `bbc_alert` WHERE {$where}");
$output = array(
	'ok'         => 1,
	'list'       => $list,
	'page'       => $page,
	'total_item' => intval($found),
	'total_page' => ceil($found/$limit)
	);
output_json($output);