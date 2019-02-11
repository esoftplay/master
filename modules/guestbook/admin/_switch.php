<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module ini dipakai untuk melihat daftar pengujung website yang telah memasukkan ke dalam form gustbook yg tersedia
switch( $Bbc->mod['task'] )
{
	case 'main': // melihat daftar user yang telah memasukkan kedalah form buku tamu
	case 'list': // opsi alias dari "main"
		include 'list.php';
		break;
	case 'list_detail':
		include 'list_detail.php';
		break;

	case 'setting': // Konfigurasi untuk menentukan settingan dari form guestbook. meliputi alamat email yang akan dituju untuk notifikasi admin jika ada pengunjung baru yang mengisi, field apa aja yang harus di masukkan oleh user dan lain2
		include 'setting.php';
		break;
	case 'setting_field': // settingan untuk menentukan field apa saja yang diperlukan bagi user untuk men-submit form guestbook
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