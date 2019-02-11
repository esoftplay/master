<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

switch($_GET['act'])
{
	case 'edit':
		include 'trashDetail.php';
	break;
	case 'main':
	default:
		include 'trashList.php';
	break;
}
