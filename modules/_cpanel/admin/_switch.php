<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

// Module untuk melakukan konfigurasi website
switch($Bbc->mod['task'])
{
	case 'main': // ini hanya satu2 nya task yang tersedia
		include 'cpanel.home.php';
		break;

	default:
		include 'cpanel.nav.php';
		$taskFile = $Bbc->mod['root'].$Bbc->mod['task'].'/_switch.php';
		if(file_exists($taskFile))
		{
			$_GET['act'] = isset($_GET['act']) ? $_GET['act'] : '';
			$Bbc->currDir1 = getcwd().'/';
			chdir($Bbc->mod['root'].$Bbc->mod['task'].'/');
			ob_start();
			include $taskFile;
			$layout = ob_get_contents();
			ob_end_clean();
			chdir($Bbc->currDir1);
			if(strstr($sys->layout, 'index.php') && @$_GET['mod'] != '_cpanel.block' && @$_GET['act']!='block_position_edit')
			{
				include 'cpanel.layout.php';
			}else{
				echo $layout;
			}
		}else echo "Invalid action <b>".$Bbc->mod['task']."</b> has been received...";
		break;
}