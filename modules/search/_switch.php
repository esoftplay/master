<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk menampilkan hasil pencarian melalui form pencarian yang bisa ditampilkan di Block Manager. module ini hanya memiliki task "main saja"
switch( $Bbc->mod['task'] )
{
	case 'main':
	default:
	if(isset($_POST['keyword'])) redirect($Bbc->mod['circuit'].'.result&id='.urlencode(str_replace('/', '', stripslashes($_POST['keyword']))));
			include 'search.php';
		break;
}
