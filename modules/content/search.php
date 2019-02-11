<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$keyword = @$_GET['id'];
$title   = lang('Search Result');
if(!$sys->menu_real)
{
	$sys->nav_change($title);
}
include tpl('search.html.php');
if(!empty($keyword))
{
	_func('image');
	$page   = @intval($_GET['page']);
	$config = config('list');
	$limit  = @intval($config['tot_list']);
	$_SESSION['currSearch'] = $keyword;

	$q = "SELECT SQL_CALC_FOUND_ROWS *,
			MATCH (title,description,keyword,tags,intro,content) AGAINST ('".$keyword."' IN BOOLEAN MODE) AS score,
			MATCH (title) AGAINST ('".$keyword."' IN BOOLEAN MODE) AS score_title
		FROM bbc_content AS c
			LEFT JOIN bbc_content_text AS t ON (c.id=t.content_id AND lang_id=".lang_id().")
		WHERE MATCH (title,description,keyword,tags,intro,content) AGAINST ('".$keyword."' IN BOOLEAN MODE) AND c.publish=1
		ORDER BY score_title DESC, score DESC, id DESC
			LIMIT $page, ".$limit;
	$r_list = $db->getAll($q);
	$show = count($r_list);
	if($show > 0)
	{
		$found = $db->GetOne('SELECT FOUND_ROWS(), "'.$keyword.'" AS `keyword_content`');
		$cat   = array(
			'id'         => $keyword,
			'title'      => $title,
			'list'       => $r_list,
			'link'       => site_url($Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&id='.urlencode($keyword)),
			'total'      => $found,
			'total_page' => ceil($found / $limit),
			'rss'        => '',
			'config'     => $config
			);
		include tpl('list.html.php');
	}else{
		echo msg(lang('Search not found'));
	}
}else{
		echo msg(lang('insert keyword'));
}