<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

# modules/content/list.php
if ((isset($cat['publish']) && !empty($cat['publish'])) || (!isset($cat['publish']) && !empty($cat['list'])))
{
	$data = $cat;
	$i_ad = rand(1, count($cat['list']));

	$data['image'] = empty($cat['image']) ? '' : content_src($cat['image'], false, true);

	/* LIST */
	$i = 0;
	$r = array();
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
			);
		$i++;
		if ($i==$i_ad)
		{
			_cpanel_ads($r, @intval($cat['id']));
		}
	}
	if (!empty($r))
	{
		$data['list'] = $r;
		$data_output = _cpanel_result($data);
	}
}else{
	$data   = array(
		'id'         => 0,
		'title'      => $Bbc->mod['task'],
		'list'       => array(),
		'link'       => site_url($Bbc->mod['circuit'].'.'.$Bbc->mod['task']),
		'total'      => 0,
		'total_page' => 0,
		'rss'        => '',
		'config'     => config('list')
		);
	$data_output = _cpanel_result($data);
}