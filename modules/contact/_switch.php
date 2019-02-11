<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk menampilkan form contact us atau kontak kami
switch( $Bbc->mod['task'] )
{
	case 'main' : // ini adalah satu satunya task yang tersedia
	case 'contact' :
		include 'contact-form.php';
		break;
	case 'finished' :
		echo msg(@$_SESSION['contact']);
		break;
	case 'chat':
		include 'chat.php';
		break;
	case 'widget':
		include 'widget.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}