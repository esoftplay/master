<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk melakukan survey dalam bentuk quetionary saja, sedangkan untuk polling anda perlu menampilkan melalui Block Manager
switch($Bbc->mod['task'])
{
	case 'main': // Halaman untuk menampilkan halaman questionary
	case 'index':
		if($config['noreentry'] && isset($_SESSION['survey']['message']))	include 'index_6.php';
		else include 'index_1.php'; // select question...
		break;
	case 'index_2':
		survey_sess('index', $db->getCol("SELECT id FROM survey_question WHERE checked=1 AND publish=1 ORDER BY orderby ASC"));
		include 'index_2.php'; // select option...
		break;
	case 'index_3':
		include 'index_3.php'; // select option (if any custom)...
		break;
	case 'index_4':
		include 'index_4.php'; // particular (if not logged)...
		break;
	case 'index_5':
		include 'index_5.php'; // store session in DB...
		break;
	case 'index_6':
		include 'index_6.php'; // show finish message...
		break;

	case 'polling': // Halaman untuk menampilkan hasil polling. untuk form polling itu sendiri anda perlu menampilkan melalui menu 'Control Panel / Block Manager'
		include 'polling.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}
