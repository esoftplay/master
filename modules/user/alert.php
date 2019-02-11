<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->stop();
$session = !empty($_SESSION[bbcAuth]) ? $_SESSION[bbcAuth] : array();
$alert   = array(
	'ok'      => 1,
	'found'   => 0,
	'checked' => (!empty($_POST['checked']) && is_numeric($_POST['checked'])) ? $_POST['checked'] : 0,
	);
if (!empty($session['Alert']))
{
	if (!empty($alert['checked']))
	{
		$session['Alert']['checked'] = $alert['checked'];
	}
	$alert = $session['Alert'];
}
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
$bracket = count($where) > 1 ? array('(', ')') : array('','');
$where   = $bracket[0].'('.implode(') OR (', $where).')'.$bracket[1];
if (!empty($alert['checked']))
{
	$checked = date('Y-m-d H:i:s', $alert['checked']);
	$where  .= sprintf(' AND `created`>\'%s\'', $checked);
}
$is_admin = _ADMIN=='' ? 0 : 1; // check is this opened by admin
if (empty($is_admin))
{
	if (empty($session['id']))
	{
		$is_admin = 2;
	}
}
$where  .= ' AND `is_admin` IN ('.$is_admin.', 3)';
/* CLEAN OLD NOTIFICATION */
$db->Execute("DELETE FROM `bbc_alert` WHERE `created` < DATE_SUB(NOW(), INTERVAL 3 MONTH)");
if (!$db->resid)
{
	include_once __DIR__.'/repair-comment.php';
}
$q_found = "SELECT COUNT(*) FROM `bbc_alert` WHERE {$where} AND `is_open`=0";
$found   = $db->getOne($q_found);
if (!$db->resid)
{
	include_once __DIR__.'/repair-comment.php';
	$found = $db->getOne($q_found);
}
$alert['found'] = intval($found);
$alert['list']  = array();
if ($alert['found'] > 0)
{
	_func('alert');
	$limit = 5;
	$limit = $alert['found'] < $limit ? $alert['found'] : $limit;
	$q = "SELECT * FROM `bbc_alert` WHERE {$where} AND `is_open`=0 ORDER BY id DESC LIMIT 0, {$limit}";
	$r = $db->getAll($q);
	foreach ($r as $d)
	{
		$alert['list'][] = alert_view($d);
	}
}
$alert['checked'] = time();
// unset($_SESSION[bbcAuth]['Alert']); pr($Bbc->debug);
$_SESSION[bbcAuth]['Alert'] = $alert; output_json($alert);