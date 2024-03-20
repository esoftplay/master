<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

switch($_GET['act'])
{
	case 'fcm': // list topics
		include 'fcm.php';
		break;
	case 'fcm_detail': // detail topics
		include 'fcm_detail.php';
		break;
	case 'fcm_member': // member of topics
		include 'fcm_member.php';
		break;
	case 'fcm_topic': // list topic by member
		include 'fcm_topic.php';
		break;
	case 'fcm-activate':
		include 'fcm-activate.php';
		break;

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
		# // include 'user-form.php';
		include 'user-create.php';
		include 'user-list.php';
		include 'user-display.php';
		break;
}
