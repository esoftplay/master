<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

switch($_GET['act'])
{
	case 'edit':
	  $sys->nav_add('Edit');
		include 'email_form.php';
		echo $form1->edit->getForm();
	break;

	case 'main':
	default:
		include 'emailSearch.php';
		include 'email_form.php';
		include 'email_list.php';
		include 'email_display.php';
	break;
}
