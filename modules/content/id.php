<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id     = @intval($_GET['id']);
$cat_id = @intval($_GET['cat_id']);
$tag_id = @intval($_GET['tag_id']);
$url    = '';
if (!empty($id))
{
	$url = content_link($id);
}
if (empty($url) && !empty($cat_id))
{
	$url = content_cat_link($cat_id);
}
if (empty($url) && !empty($tag_id))
{
	$url = content_tag_link($tag_id);
}
if (!empty($url))
{
	redirect($url);
}else{
	echo msg(lang('not found'), 'danger');
}