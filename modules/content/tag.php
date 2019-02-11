<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (config('manage', 'webtype') == '1')
{
	$id     = @intval($_GET['id']);
	$page   = @intval($_GET['page']);

	$q = "SELECT * FROM bbc_content_tag WHERE id={$id}";
	$cat = $db->getRow($q);
	if (!empty($cat))
	{
		meta_title($cat['title'], 1);
		if (!$sys->menu_real)
		{
			$sys->nav_change($cat['title']);
		}
		$config = get_config('content', 'list');
		$start  = $page * $config['tot_list'];
		$q      = "SELECT SQL_CALC_FOUND_ROWS l.content_id, c.*, t.title, t.intro, t.content
				FROM bbc_content_tag_list AS l
				LEFT JOIN bbc_content AS c ON (c.id=l.content_id)
				LEFT JOIN bbc_content_text AS t ON (c.id=t.content_id AND t.lang_id=".lang_id().")
				WHERE l.tag_id={$id} AND c.publish=1 ORDER BY c.id DESC LIMIT {$start}, ".$config['tot_list'];

		$cat['list']       = $db->cacheGetAll($q);
		foreach ($cat['list'] as $i => $c)
		{
			if (empty($c['id']))
			{
				$cat['list'][$i] = content_fetch($c['content_id']);
			}
		}
		$cat['total']      = $db->cacheGetOne("SELECT FOUND_ROWS(), {$id} AS tag_id");
		$cat['config']     = $config;
		$cat['link']       = content_tag_link($id, $cat['title']);
		$cat['total_page'] = ceil($cat['total'] / $config['tot_list']);
		include tpl(@$config['template'], 'list.html.php');
	}else{
		echo msg(lang('not found'));
	}
}else{
	echo msg(lang('Content Tags is only available for News Article Website'), 'danger');
}
