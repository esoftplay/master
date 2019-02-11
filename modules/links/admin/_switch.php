<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk mengatur Link External, dimana external link tersebut harus ditampilkan melalui block di 'Control Panel / Block Manager' untuk bisa dilihat oleh pngujung situs
switch( $Bbc->mod['task'] )
{
	case 'main': // daftar External Link
	case 'list': // alias dari task "main"
		include 'list.php';
		break;
	case 'advertise': // Pengaturan API jika anda ingin beriklan ditempat lain, pihak lain hanya perlu meng include kan script yang akan di render otomatis oleh sistem
		include 'advertise.php';
		break;
	case 'advertise_edit':
		include 'advertise_edit.php';
		echo $form->edit->getForm();
		$sys->nav_add('Edit');
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}