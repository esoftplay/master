<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// ini adalah module untuk menampilkan daftar 'Kontak kami' bagi user yang telah memasukkan data di form 'contact us'
switch($Bbc->mod['task'])
{
	case 'main': // daftar user yang telah memasukkan data
	case 'posted': // alias dari task "main"
		include 'posted.php';
		break;
	case 'posted_answer':
		include 'posted_answer.php';
		break;
	case 'posted_detail':
		include 'posted_detail.php';
		break;
	case 'messenger': // Jika anda memiliki account di Yahoo Messenger bisa dimasukkan ke sini lalu akan di tampilkan di block "contact" (Perhatian:: Yahoo Messenger telah depricated oleh Yahoo Inc. jadi sering gak jalan)
		include 'messenger.php';
		break;

	case 'setting': // Konfigurasi untuk menentukan settingan dari form contact us. meliputi alamat email yang akan dituju untuk notifikasi admin jika ada pengunjung baru yang mengisi, field apa aja yang harus di masukkan oleh user dan lain2
		include 'setting.php';
		break;
	case 'setting_field': // settingan untuk menentukan field apa saja yang diperlukan bagi user untuk men-submit form contact us
		include 'setting_field.php';
		break;
	case 'setting_field_edit':
		include 'setting_field_edit.php';
		echo $form->edit->getForm();
		break;
	default:
		echo "Invalid action <b>".$Bbc->mod['task']."</b> has been received...";
		break;
}