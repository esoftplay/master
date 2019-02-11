<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk mengatur testimoni para user
switch( $Bbc->mod['task'] )
{
	case 'main': // daftar testimoni user yang telah masuk
	case 'list': // alias dari task "list"
		include 'list.php';
		break;
	case 'list_detail':
		include 'list_detail.php';
		break;
	case 'add': // Jika anda memiliki testimoni oleh user melalui tempat lain (bukan situs ini) maka anda bisa memasukkan secara manual
		include 'add.php';
		break;

	case 'setting': // Konfigurasi untuk module testimonial
		include 'setting.php';
		break;
	case 'setting_field': // Settingan untuk menentukan field/informasi apa aja yg perlu dimasukkan oleh user untuk men-submit form yang tersedia
		include 'setting_field.php';
		break;
	case 'setting_field_edit':
		include 'setting_field_edit.php';
		echo $form->edit->getForm();
		$sys->button($Bbc->mod['circuit'].'.setting_field');
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}