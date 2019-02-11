<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$add_sql = $type_id > 0 ? 'WHERE c.type_id='.$type_id : ' WHERE 1';
$q = "SELECT * FROM bbc_content_cat AS c LEFT JOIN bbc_content_cat_text AS t 
			ON (t.cat_id=c.id AND t.lang_id=".lang_id().")
			$add_sql ORDER BY c.type_id, c.par_id, t.title ASC";
$o = $db->GetAll($q);
$r = array();
foreach($o AS $d)
{
	$d['title'] = addslashes($d['title']);
	$d['title'] = ($category_id==$d['id']) ? '<span class=nodeSel>'.trim($d['title']).'</span>' : trim($d['title']);
	$d['link']	= $base_link.'&id='.$d['id'];
	$r[] = $d;
}
$title = array('Category', $base_link);
$config = array(
	'useIcons' => false
);
_func('tree');
echo tree_list($r, $title, $config);