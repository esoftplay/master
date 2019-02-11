<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

switch( $_GET['act'] )
{
	case 'reference':
		include 'language-reference.php';
	break;
	case 'edit':
		$id = intval($_GET['id']);
		include 'language-update.php';
		echo $language_update;
	break;
	case 'import':
		include 'language-import.php';
	break;
	case 'super-update':
		include 'super-update.php';
	break;
	default:
		$id = 0;
		include 'language-search.php';
		include 'language-update.php';
		include 'language-list.php';
		include 'language-form.php';
	break;
}
