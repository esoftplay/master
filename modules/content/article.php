<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id    = 0;
$kinds = array_flip(content_kind());
$task  = $Bbc->mod['task'];
$title = lang('Content List');
if (isset($kinds[$task]))
{
	$id    = $kinds[$task];
	$title = lang($task.' list');
}
$config = config('list');
$page   = @intval($_GET['page']);
$limit  = (@$config['tot_list'] > 0) ? $config['tot_list'] : 12;
$start  = $page*$limit;
$cat    = array();

/* START LISTING CONTENT */
$q  = "SELECT SQL_CALC_FOUND_ROWS c.*, t.`title`, t.`intro`
	FROM  `bbc_content` AS c
	LEFT JOIN `bbc_content_text` AS t ON (c.`id`=t.`content_id` AND t.`lang_id`=".lang_id().")
	WHERE c.`publish`=1 AND c.`kind_id`={$id} ORDER BY c.`id` DESC LIMIT {$start}, {$limit}";
$r_list = $db->cacheGetAll($q);
$total	= $db->cacheGetOne("SELECT FOUND_ROWS(), '".md5($q)."'");
$cat = array(
	'id'         => $id,
	'title'      => $title,
	'list'       => $r_list,
	'link'       => site_url($Bbc->mod['circuit'].'.'.$Bbc->mod['task']),
	'total'      => $total,
	'total_page' => ceil($total / $limit),
	'rss'        => '',
	'config'     => $config
	);
include tpl(@$config['template'], 'list.html.php');