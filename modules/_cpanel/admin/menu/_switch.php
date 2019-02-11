<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

switch($_GET['act'])
{
	case 'position';
		include 'position.php';
		break;
	case 'shortcut';
		include 'shortcut.php';
		break;
	case 'clean':
		include 'clean.php';
		break;
	case 'check_task':
		include 'check_task.php';;
		break;
	case 'check_link':
		include 'check_link.php';;
		break;
	case 'check_seo':
		include 'check_seo.php';
		break;
	default:
		$is_admin= @intval($_GET['is_admin']);
		$menu_id = @intval($_GET['id']);
		$add_link= ($is_admin) ? '&is_admin='.$is_admin : '';
		$add_url = ($menu_id > 0) ? '&id='.$menu_id : '';
		$mainLink= $Bbc->mod['circuit'].'.menu'.$add_link;
		include 'searchForm.php';
		include 'menuQRY.php';
		$data		= array();
		if($menu_id > 0){
			$prefix = 'edit_';
			include 'menuForm.php';
		}
		$prefix = 'add_';
		include 'menuForm.php';
		include 'menuList.php';
		include 'menuDisp.php';
		break;
}
