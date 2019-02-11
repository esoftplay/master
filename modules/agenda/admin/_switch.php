<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Untuk mengatur data agenda beserta detail informasi per agenda yang telah dibuat
switch($Bbc->mod['task'])
{
	case 'main': // daftar agenda yang telah dibuat
	case 'agenda': // alias dari task 'main'
		include 'agenda.php';
		break;
	case 'agenda_edit':
		$id = @intval($_GET['id']);
		include 'agenda_update.php';
		break;
	case 'agenda_add': // Form untuk membuat agenda baru
		$cat_id = @intval($_GET['id']);unset($id);
		include 'agenda_update.php';
		break;

	default:
		echo "Invalid action <b>".$Bbc->mod['task']."</b> has been received...";
		break;
}