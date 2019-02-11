<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan Gambar / Banner / Logo secara custom. Dalam artian anda harus mengupload ataupun menentukan gambar mana yang ingin anda tampilkan
$output = array();
// GET IMAGE
if(!is_url($config['image']) && !is_file(_ROOT.$config['image'])) {
	$image = 'images/'.$_CONFIG['site']['logo'];
}else{
	$image = $config['image'];
}
// GET SIZES
if (!empty($config['size']))
{
	preg_match('/([0-9]+)(?:[x\*]([0-9]+))?/is', $config['size'], $match);
	$match[1] = @intval($match[1]);
	$match[2] = @intval($match[2]);
	$sizes    = array(
			0 => $match[1]
		,	1 => ($match[2] > 1) ? $match[2] : $match[1]
	);
}else{
	$sizes = '';
}
// GET TITLE
$title               = !empty($config['title']) ? $config['title'] : $_CONFIG['site']['title'];
$output['title']     = !empty($config['title']) ? $config['title'] : $_CONFIG['site']['title'];
$output['link']      = !empty($config['link']) ? $config['link'] : _URL;
$output['attribute'] = !empty($config['attribute']) ? ' '.$config['attribute'] : '';
$output['image']     = !is_url($config['image']) && !is_file(_ROOT.$config['image']) ? 'images/'.$_CONFIG['site']['logo'] : $config['image'];
$output['size']      = $sizes;

include tpl(@$config['template'].'.html.php', 'logo.html.php');
