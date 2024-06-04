<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk mengatur user account yang saat itu login
switch($Bbc->mod['task'])
{
	case 'main':
		include 'layout.main.php';
		break;

 	case 'login':
		include 'login.php';
		break;

	case 'repair':
		chdir(dirname(dirname(__FILE__)));
		include 'repair.php';
		break;

	case 'clean_cache':
		include 'clean_cache.php';
		break;

	case 'alert':
	case 'alert_list':
	case 'alert_list_detail':
	case 'alert_click':
	case 'alert_remove':
		chdir(dirname(__DIR__));
		include '_switch.php';
		break;

	case 'password': // Untuk mengganti password dari user yg saat itu login, Jika anda mensetting login menggunakan thirdparty semisal google/facebook/yahoo dll untuk login di admin, maka user hanya bisa merubah password mereka di thirdparty tersebut
		include 'password.php';
		break;

	case 'link':
		include 'link.php';
		break;

	case 'menu':
		include 'menu.php';
		break;

	case 'logout': // link untuk user logout atau keluar dari mode admin
		include 'logout.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received..2.';
		break;
}
