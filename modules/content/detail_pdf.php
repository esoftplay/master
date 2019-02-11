<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if(!$id) $sys->denied();

$data = content_fetch($id, true);
if(empty($data)) $sys->denied();

$config = $data['config'];
if($data['publish'] && $config['pdf'])
{
	meta_title($data['title'], 2);
	meta_desc($data['description'], 2);
	meta_keyword($data['keyword'], 2);
	$param = array(
		'title'		=> ''
	,	'content'	=> ''
	,	'created'	=> ''
	,	'category'=> ''
	,	'author'	=> ''
	,	'modified'=> ''
	);
	if($config['title'])
	{
		$param['title'] = $data['title'];
	}
	$image = (!empty($data['is_popimage']) && !empty($data['image'])) ? content_src($data['image'], false, true) : '';
	if(!empty($image))
	{
		$param['image'] = $image;
	}
	$param['content'] = $data['content'];
	if($config['created'])
	{
		$param['created'] = date(config('rules','content_date'), strtotime($data['created']));
	}
	if($config['tag'])
	{
		$r = content_category($data['id'], $config['tag_link']);
		$param['category'] = lang('Tags :').' '.strip_tags(implode(', ', $r));
	}
	if($config['author'])
	{
		$param['author'] = lang('author').$data['created_by_alias'];
	}
	if($config['modified'])
	{
		$param['modified'] = lang('Last modified').content_date($data['modified']);
	}
	_func('pdf');
	pdf_write($param);
}