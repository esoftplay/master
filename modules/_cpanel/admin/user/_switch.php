<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

switch($_GET['act'])
{
	case 'force2Logout':
		include 'user-logout.php';
		break;
	case 'force2Login':
		include 'force2Login.php';
		break;
	case 'field':
		include 'user-field-list.php';
		break;
	case 'field-edit':
		include 'user-field-edit.php';
		echo $form->edit->getForm();
		break;
	case 'edit':
		include 'user-form.php';
		include 'edit-account.php';
		include 'edit-display.php';
		break;
	case 'user-create':
		include 'user-create.php';
		break;
	default:
		include 'user-search.php';
		include 'user-register.php';
		// include 'user-form.php';
		include 'user-create.php';
		include 'user-list.php';
		include 'user-display.php';
		break;
}
