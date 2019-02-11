<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

// LOAD JQUERY..
$jquery = _lib('jquery');

// TENTUKAN TEMPLATE PUBLICNYA
$url = dirname($sys->template_url).'/'.$_CONFIG['template'].'/';
$dir = preg_replace('~^'.addslashes(_ROOT).'~', _URL, dirname($taskFile).'/');
$sys->set_layout(dirname($sys->template_dir).'/'.$_CONFIG['template'].'/index.php');
$sys->link_set($url.'css/style.css', 'css');
$sys->link_set('', 'js');
$sys->link_css($sys->template_url.'bootstrap/css/bootstrap.min.css');
$sys->link_css($sys->template_url.'bootstrap/css/bootstrap-theme.min.css');
$sys->link_css($dir.'block_position.css');


include 'block_position-information.php';

// FETCH ALL INSTALLED BLOCKS
$q = "SELECT * FROM bbc_block AS b LEFT JOIN bbc_block_text AS t ON
			(t.block_id=b.id AND t.lang_id=".lang_id().") WHERE template_id=".$template_id
		." ORDER BY position_id, orderby, title ASC";
$r = $db->getAll($q);
$r_block = array();
foreach($r AS $dt)
{
	if(!isset($r_block[$dt['position_id']])) {
		$r_block[$dt['position_id']] = array();
	}
	$r_block[$dt['position_id']][$dt['id']] = array('name' => @$r_ref[$dt['block_ref_id']], 'title' => strip_tags($dt['title']), 'content' => '');
}

// DECLARE BLOCKS POSITION
$block = array();
foreach($r_position AS $id => $name)
{
	$params = array(
		'position_name'	=> $name
	,	'position_id'		=> $id
	,	'blocks'				=> isset($r_block[$id]) ? $r_block[$id] : array()
	);
	ob_start();
	extract($params);
	include 'block_position.html.php';
	$out = ob_get_contents();
	ob_end_clean();
	$block[$name][] = $out;
}
ob_start();
$jquery->load('1.1.2', false);
$jquery->widget('interface', false);
$sys->link_js($dir.'block_position.js', false);
$block_js = ob_get_contents();
ob_end_clean();
$Bbc->content = '<h1 style="text-align: center;">WEB CONTENT</h1><span id="show_hidden">Show Hidden Block</span>'
							. '<script src="'._URL.'templates/admin/js/index.js" type="text/javascript"></script>'
							.	$block_js;
$sys->allContentBlocks  = $block;
$site                   = array();
$site['site']           = $_CONFIG['site'];
$site['site']['title']  = 'Block Position';
$_CONFIG                = $site;
include $sys->layout;
die();