<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$data_result = array();
if (!empty($output))
{
	$data_result = $output;
}else
if (!empty($r_list))
{
	$data_result = $r_list;
}else
if(!empty($data))
{
	$data_result = $data;
}

$data['image']  = "";

$data['total'] = count($data_result['data']);

$data['config'] = $data_result['config'];

$r = array();
foreach ($data_result['data'] as $d)
{
	$r[] =array(
		'id'          => $d['id'],
		'title'       => $d['title'],
		'intro'       => $d['content'],
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
}
$data['list'] = $r;

$data_output = _cpanel_result($data);