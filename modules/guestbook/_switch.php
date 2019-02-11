<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk menampilkan form Buku Tamu
switch( $Bbc->mod['task'] )
{
	case 'main' : // Daftar tamu atau pengunjung website yang telah masuk
	case 'list' : // task alias dari "main"
		include	'list.php';
		break;
	case 'list_show':
		include 'list_show.php';
		break;
	case 'form': // menampilkan form buku tamu untuk diisi oleh user
		include 'form.php';
		break;
	case 'form-finished':
		include 'form-finished.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}
