<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk menampilkan daftar external link, jika anda ingin menggunakan nya untuk bertukar link dengan website lain maka anda harus menampilkan nya melalui Block Manager
switch( $Bbc->mod['task'] )
{
	case 'main' : // Daftar External Link yang telah dimasukkan oleh admin
	case 'list' : // task alias dari "main"
		include	'list.php';
		break;
	case 'ad';
		include 'ad.php';
		break;

	default:
		include 'default.php';
		break;
}
