<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

# modules/focus/main.php
$sys->no_tpl = 1;
if (!empty($cat['publish']))
{
	$data          = $cat;
	$data['image'] = content_src($cat['image'], false, true);

	$r = array();
	$i = 0;
	$j = rand(1, count($cat['list']));
	foreach ($cat['list'] as $d)
	{
		$r[] = array(
			'id'          => $d['id'],
			'title'       => $d['title'],
			'intro'       => $d['intro'],
			'description' => '',
			'image'       => content_src($d['image'], false, true),
			'created'     => $d['created'],
			'updated'     => $d['modified'],
			'url'         => content_link($d['id'], $d['title']),
			'publish'     => $d['publish'],
			// additional info
			// 'type_id'          => $d['type_id'],
			// 'created_by'       => $d['created_by'],
			// 'created_by_alias' => $d['created_by_alias'],
			// 'modified_by'      => $d['modified_by'],
			// 'revised'          => $d['revised'],
			// 'hits'             => $d['hits'],
			// 'rating'           => $d['rating'],
			// 'last_hits'        => $d['last_hits'],
			// 'is_popimage'      => $d['is_popimage'],
			// 'is_front'         => $d['is_front'],
			// 'is_config'        => $d['is_config'],
			// 'config'           => $d['config'],
			// 'content'          => $d['content'],
			);
			$i++;
			if ($i==$j)
			{
				_cpanel_ads($r, 0);
			}
	}
	if (!empty($r))
	{
		$data['list'] = $r;
		$data_output = _cpanel_result($data);
	}
}