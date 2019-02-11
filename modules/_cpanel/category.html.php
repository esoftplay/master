<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

_func('content');
$q = "SELECT * FROM bbc_content_cat AS c
	LEFT JOIN bbc_content_cat_text AS t
		ON (c.id=t.cat_id AND t.lang_id=".lang_id().")
	WHERE publish=1 ORDER BY par_id, title, id ASC";
$result  = array(
	'type' => $db->getAll("SELECT id, title FROM bbc_content_type WHERE active=1"),
	'list' => array()
	);
$r  = $db->getAll($q);
$r2 = array();
foreach ($r as $d)
{
	if (!empty($d['image']))
	{
		if (file_exists(_ROOT.'images/modules/content/'.$d['image']))
		{
			$d['image'] = _URL.'images/modules/content/'.$d['image'];
		}
	}
	$d['url'] = content_cat_link($d['id'], $d['title']);
	$r2[$d['type_id']][] = $d;
}
foreach ($result['type'] as $type)
{
	$result['list'][] = !empty($r2[$type['id']]) ? $r2[$type['id']] : array();
}
$data_output = _cpanel_result($result);