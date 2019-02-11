<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk mengatur pencarian di halaman public, tidak ada task yang tersedia hanya ada task "main" saja
switch($Bbc->mod['task'])
{
	case 'main':
	case 'config':
		include 'config.php';
		break;

	default:
		echo "Invalid action <b>".$Bbc->mod['task']."</b> has been received...";
		break;
}