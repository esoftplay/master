<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

include_once $Bbc->mod['root'].'block/delete_block_file.php';
switch($_GET['act'])
{
	case 'editCSS':
		include 'editCSS.php';
	break;
	case 'scan':
		include 'template-scan.php';
	break;
	case 'update':
		include 'template-edit.php';
	break;
	default:
		include 'templateDsp.php';
	break;
}
