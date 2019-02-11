<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module image slider tidak memiliki task apapun, sedangkan untuk menampilkan imageslider harus melalui block manager di menu 'Control Panel / Block Manager'
switch( $Bbc->mod['task'] )
{
	case 'main' :
		break;
	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}
