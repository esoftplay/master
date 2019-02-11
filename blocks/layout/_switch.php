<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Untuk menampilkan file template apapun yang telah dibuat oleh designer untuk template anda yang sedang aktif, ataupun yang telah disediakan. Tujuanya untuk ditambahkan di website anda
if (!empty($config['task']) && empty($config['template']))
{
	$config['template'] = $config['task'];
}
include tpl(@$config['template'].'.html.php');