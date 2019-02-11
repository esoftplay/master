<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk melakukan survey baik dalam bentuk quetionary maupun hanya sekedar polling di halaman public
switch($Bbc->mod['task'])
{
	case 'main': // daftar pengunjung yang telah mengisi form questionary
	case 'posted': // task alias dari "main"
		include 'posted.php';
		break;
	case 'posted_detail':
		include 'posted_detail.php';
		break;

	case 'question': // daftar pertanyaan 2 untuk questionary yang anda buat
		include 'question.php';
		break;
	case 'question_detail':
		include 'question_detail.php';
		break;
	case 'question_report':
		include 'question_report.php';
		break;
	case 'question_reset':
		include 'question_reset.php';
		break;

	case 'polling': // daftar pertanyaan untuk polling simple yang akan ditampilkan melalui block di 'Control Panel / Block Manager'
		include 'polling.php';
		break;
	case 'polling_edit':
		include 'polling_edit.php';
		$sys->button($Bbc->mod['circuit'].'.polling');
		break;

	case 'config': // Konfigurasi untuk module survey baik untuk questionary maupun polling
		include 'config.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}