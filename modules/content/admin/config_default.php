<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$show_form = false;
if (!isset($config_tabs))
{
	$config_tabs = array();
	$show_form   = true;
	$sys->nav_add('Default parameter');
	include_once '../_config.php';
}
if (!isset($conf))
{
	$conf = _class('bbcconfig');
}
$config_message = '';
$is_single_type = $db->getOne("SELECT COUNT(*) FROM `bbc_content_type`") == 1 ? true : false;
if (!$is_single_type)
{
	$config_message = explain('<br />Configuration below will not change anything, it only determines the default value when you add new content type. Go to <strong><a href="index.php?mod=content.type" class="admin_link">Content &raquo; Content Type</a></strong> to configure default parameters of content under specified type!', 'ATTENTION !!');
}

$conf->set(content_config_detail());
$config_tabs['Default Content Detail'] = $config_message.$conf->show();

$conf->set(content_config_list());
$config_tabs['Default Category List'] = $config_message.$conf->show();

if ($show_form)
{
	echo tabs($config_tabs);
}
if (!empty($_POST['config_detail_submit']) && $is_single_type)
{
	$detail = json_encode($_POST['detail']);
	$q = "UPDATE `bbc_content_type` SET `detail`='{$detail}'";
	$db->Execute($q);
}
if (!empty($_POST['config_list_submit']) && $is_single_type)
{
	$list = json_encode($_POST['list']);
	$q = "UPDATE `bbc_content_type` SET `list`='{$list}'";
	$db->Execute($q);
}
