<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// block untuk menampilkan apapun script maupun tag yang anda masukkan untuk ditambahkan di website, hanya support dalam bahasa html/javascript maupun PHP
if(@$config['type']=='php')
{
	@eval(unhtmlentities($config['content']));
}else{
	echo unhtmlentities(@$config['content']);
}
