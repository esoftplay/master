<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$start   = $config['tot_list'] * $page;
$content = ($config['intro']) ? 't.intro' : 't.content' ;
$q       = "SELECT SQL_CALC_FOUND_ROWS c.*, t.title, $content AS intro
FROM  bbc_content AS c
LEFT JOIN bbc_content_text AS t ON (c.id=t.content_id AND t.lang_id=".lang_id().")
WHERE c.type_id={$id} AND c.publish=1 ORDER BY id DESC LIMIT ".$start.", ".$config['tot_list'];
$r_type = $db->cacheGetAll($q);
$total	= $db->cacheGetOne("SELECT FOUND_ROWS(), {$id}");
$cat = array(
	'id'         => $id,
	'title'      => $type['title'],
	'list'       => $r_type,
	'link'       => site_url($Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&id='.$id),
	'total'      => $total,
	'total_page' => ceil($total / $config['tot_list']),
	'rss'        => '',
	'config'     => $config,
	);
include tpl(@$config['template'], 'list.html.php');