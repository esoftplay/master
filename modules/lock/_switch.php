<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk pengetestan penggunaan distribution lock
switch ($Bbc->mod['task'])
{
	case 'main':
		include 'main.php';
		break;
	default:
		echo "Invalid action <b>".$Bbc->mod['task']."</b> has been received...";
		break;
}
