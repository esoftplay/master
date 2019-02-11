<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan daftar content. Sebaiknya anda menggunakan block "content" selama kebutuhan anda masih bisa di atasi oleh block "content" itu sendiri
$output              = array();
$config['cat_id']    = @intval($config['cat_id']);
$config['show_numb'] = @intval($config['show_numb']);
$output['config']    = $config;
$output['data']      = array();
$q                   = "SELECT c.id, t.title, c.publish FROM bbc_content_cat AS c
		LEFT JOIN bbc_content_cat_text AS t ON(t.cat_id=c.id AND t.lang_id=".lang_id().")
		WHERE id=".$config['cat_id'];
$cat = $db->getRow($q);
if(!empty($cat['publish']))
{
	switch($config['orderby'])
	{
		case 1:
			$add_sql = 'c.id DESC';
		break;
		case 2:
			$add_sql = 'c.id ASC';
		break;
		default:
			$add_sql = 'RAND()';
		break;
	}
	$q = "SELECT c.id, t.title FROM bbc_content_category AS cc
	LEFT JOIN bbc_content AS c ON (c.id=cc.content_id)
	LEFT JOIN bbc_content_text AS t ON (t.content_id=cc.content_id AND t.lang_id=".lang_id().")
	WHERE cc.cat_id=".$config['cat_id']." AND c.publish=1 ORDER BY ".$add_sql." LIMIT ".$config['show_numb'];
	$r = $db->getAll($q);
	foreach((array)$r AS $dt)
	{
		$output['data'][]=array(
			'title' => $dt['title'],
			'href'=> content_link($dt['id'], $dt['title'])
			);
	}
}
$title           = lang('more').' '.$cat['title'];
$output['title'] = !empty($config['more']) ? $title : '';
$output['href']  = content_cat_link($cat['id'],$cat['title']);
$output['id']    = $cat['id'];
$output['title'] = $cat['title'];
$output['cat']   = $cat['title'];
include tpl(@$config['template'].'.html.php', 'list.html.php');