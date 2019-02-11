<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

switch($_GET['act'])
{
	case "main":
	case 'update':
		include 'updateModule.php';
		redirect();
	break;
	case 'moduleEdit':
		include 'moduleEdit.php';
	break;
	case 'download':
		include 'download.php';
	break;
	case 'upload':
		include 'upload.php';
	break;
	default:
		include 'moduleDsp.php';
	break;
}
