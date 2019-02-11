<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk menampilkan form testimoni beserta hasil dari testimoni para user yang telah masuk
switch( $Bbc->mod['task'] )
{
	case 'main' : // Menampilkan daftar testimoni oleh para user
	case 'list' : // task alias dari "main"
		include	'list.php';
		break;
	case 'list_show':
		include 'list_show.php';
		break;
	case 'form': // Form untuk memasukkan testimoni dimana field-field yang perlu dimasukkan oleh user telah ditentukan oleh admin sebelumnya
		include 'form.php';
		break;
	case 'form-finished':
	include 'form-finished.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}
