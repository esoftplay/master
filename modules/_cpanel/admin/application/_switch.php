<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

switch($_GET['act'])
{
	case 'contentads':
		include 'contentads.php';
		break;
	default:
		include 'main.php';
		break;
}