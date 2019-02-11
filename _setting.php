<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$Bbc->home			= 'content.home';			// ini adalah halaman pertama atau bisa di bilang indexnya
$Bbc->home_user	= 'user.account';			// ini adalah halaman pertama untuk user yang login
$Bbc->login			= 'user.login';				// ini adalah halaman untuk login jika bukan haknya
$Bbc->notfound	= 'user.notfound';		// ini adalah halaman pengganti jika tidak ditemukan
$Bbc->denied		= 'user.notAllowed';	// ini adalah halaman jika mengakses yang bukan haknya
$Bbc->load      = array(
	'func'  => array('meta', 'language', 'menu', 'config', 'layout', 'password', 'user'),
	'class' => array(),
	'lib'   => array(),
	'sys'   => array('db.class', 'seo', 'login.condition')
	);
