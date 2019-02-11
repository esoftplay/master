<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$found = $db->getOne("SELECT COUNT(*) FROM bbc_content_tag");
$page  = @intval($_GET['page']);
$limit = 30;
$start = $page*$limit;
$total = ceil($found/$limit);

$q = "SELECT id, title, total FROM bbc_content_tag ORDER BY id LIMIT {$start}, {$limit}";
$r = $db->getAll($q);
foreach ($r as &$d)
{
	$d['url'] = _URL.$d['id'].'-'.menu_save($d['title']).'.htm';
}
$data_output = _cpanel_result(array(
	'list'  => $r,
	'pages' => $total,
	'page'  => ++$page
	));