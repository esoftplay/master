<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'home' => array(
		'text'    => 'Home Title',
		'tips'    => 'Insert title for home menu in third party application, leave it blank if you don\'t want to use home menu',
		'type'    => 'text',
		'default' => 'home'
		),
	'home_icon'=> array(
		'text'		=> 'Home Icon',
		'type'		=> 'file',
		'attr'		=> '',
		'path'		=> 'images/'
		),
	'home_list'=> array(
		'text'   => 'Home Listing Content',
		'type'   => 'select',
		'option' => array(
			1 => 'Use website configuration',
			2 => 'Latest content',
			3 => 'Most popular content in a month',
			),
		'default' => 2
		),
	'home_limit'=> array(
		'text'    => 'Home Limit items',
		'tips'    => 'Insert 0 (zero) if you don\'t want to limit total item to show in home screen',
		'type'    => 'text',
		'default' => 0,
		'add'     => 'item(s)'
		)
	);
$params = array(
	'config' => $_setting,
	'name'   => 'config',
	'title'  => 'Third Party Application Configuration'
	);
$conf = _class('bbcconfig');
$conf->set($params);
echo msg('this configuration will only used by third party apps for other platform such as Android, iOS or Blackberry which is connected to this system. Please contact your developer if you need the native application for this system ', 'warning');
echo $conf->show();
$f = $db->getCol("SHOW TABLES LIKE 'bbc_content_ad'");
if (empty($f))
{
	?>
	<a class="btn btn-default btn-lg" href="index.php?mod=_cpanel.application&act=contentads" role="button"><?php echo icon('fa-mobile'); ?> Activate Mobile Content Ads</a>
	<?php
}