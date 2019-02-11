<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Apabila anda menggunakan multi language (Control Panel / Language / Language Reference) anda bisa menampilkan pilihan bahasa dengan block ini
$output  = array();
$r       = lang_assoc();
$seo_url = seo_url();
if (preg_match('~^'._URL.'([a-z]{2}/)~is', $seo_url, $m))
{
	$regex   = '~^('._URL.$m[1].')~is';
	$replace = _URL.'%s/';
}else{
	$regex   = '~^('._URL.')~is';
	$replace = _URL.'%s/';
}
foreach ($r as $i => $dt)
{
	$r[$i]['link']   = preg_replace($regex, sprintf($replace, $dt['code']), $seo_url);
	$r[$i]['active'] = ($dt['id']==lang_id()) ? 1 : 0;
}
include tpl(@$config['template'].'.html.php', 'link.html.php');