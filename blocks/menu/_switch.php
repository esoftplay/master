<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan Menu dari "Control Panel / Menu Manager" berdasarkan "Menu Position" yang anda pilih. Tampilan bisa diubah sesuai template dari bawaan Site Template
include_once 'fetch_all.php';
$menus  = (array)@$Bbc->menu_array[$config['cat_id']];

if (empty($config['template']) && !empty($config['layout']))
{
	$config['template'] = 'menu-'.$config['layout'];
}
include tpl($config['template'].'.html.php');