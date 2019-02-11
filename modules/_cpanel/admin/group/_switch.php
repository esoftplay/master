<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

switch($_GET['act'])
{
	case 'edit';
		include 'group_edit.php';
		include 'group_edit-dsp.php';
	break;
	case 'main';
	default:
		include 'group_edit.php';
		include 'group-list.php';
		include 'group-edit.php';
	break;
}
