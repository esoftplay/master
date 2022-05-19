<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk mengatur gambar sliding, dimana gambar ini harus ditampilkan melalui block di 'Control Panel / Block Manager' untuk bisa dilihat oleh pngujung situs
switch( $Bbc->mod['task'] )
{
	case 'main': // melihat daftar image slide nya
	case 'list': // alias dari task "main"
		include 'list.php';
		break;
	case 'list_edit':
		include 'list_edit.php';
		break;
	case 'category': // daftar kategori, karena setiap image slide harus masuk kedalam kategori yang akan dipilih ketika admin akan menampilkan ke public di 'Control Panel / Block Manager'
		include 'category.php';
		break;

	case 'new_desc':
		include 'new_desc.php';
		break;

	case 'config': // konfigurasi tambahan untuk imageslider
		include 'config.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}