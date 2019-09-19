<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$notFile = array('_switch.php', 'index.php');
$M_DIR   = '/opt/tools/';
$M_DIR2  = _ROOT.'templates/'.config('template').'/modules/tools/';
$M_DIR3  = _ROOT.'modules/';
$key     = 'isAllowTools';
// unset($_SESSION['isAllowTools']);
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1')
{
	$_SESSION[$key] = 1;
}else{
	if (!isset($_SESSION[$key]))
	{
		$m = $sys->login('google');
		if (preg_match('~\@fisip\.net$~s', $m['email']))
		{
			$_SESSION[$key] = 1;
			redirect(_URL.'tools');
		}else{
			$_SESSION[$key] = 0;
		}
	}
}
if(file_exists($M_DIR) && ini_get('display_errors')==1 && !empty($_SESSION[$key]))
{
	meta_title('tools');
	chdir($M_DIR);
	$sys->stop();
	$sys->set_layout('blank');
	$_URL = _URL.$Bbc->mod['name'].'/'.$Bbc->mod['task'];
	// Module ini hanya digunakan untuk programmer maupun developer saja, jangan menampilkan module ini di menu apalagi untuk user public !
	switch( $Bbc->mod['task'] )
	{
		case 'main' :
			ob_start();
				?>
				<table>
				  <tr>
				    <td style="width:200px;">
				    	<IFRAME name="navigation" src="<?php echo site_url($Bbc->mod['circuit'].'.list');?>" frameBorder="0" width="100%" height="100%" scrolling="auto"></IFRAME>
				    </td>
				    <td>
				    	<IFRAME name="tasks" src="<?php echo site_url($Bbc->mod['circuit'].'.PHP');?>" frameBorder="0" width="100%" height="100%" scrolling="auto"></IFRAME>
				    </td>
				  </tr>
				</table>
				<?php
				$output = ob_get_contents();
			ob_end_clean();
			show_css($output);
			break;
		case 'list' :
			ob_start();
				echo '<div style="float: left;"><a href="" onClick="window.location.reload( true );return false" title="Refresh Left">V refresh</a></div>';
				echo '<div style="float:right;"><a href="" onClick=\'window.parent.frames["tasks"].window.location.reload( true );return false\' title="Refresh right"><b>&gt;</b> refresh</a></div>';
				echo '<br /><br />';
				if ($dir = @opendir($M_DIR))
				{
					$r_data = array();
					while (($data = readdir($dir)) !== false)
					{
						if(is_file($M_DIR.$data)
							&& !in_array($data, $notFile)
							&& substr(strtolower($data),-4)=='.php')
						{
							$r_data[] = preg_replace('~\.php$~is', '', $data);
						}
					}
					closedir($dir);
					/* FETCH FROM ACTIVE TEMPLATE  */
					if (file_exists($M_DIR2))
					{
						if ($dir = @opendir($M_DIR2))
						{
							while (($data = readdir($dir)) !== false)
							{
								if(is_file($M_DIR2.$data)
									&& !in_array($data, $notFile)
									&& substr(strtolower($data),-4)=='.php')
								{
									$r_data[] = preg_replace('~\.php$~is', '', $data);
								}
							}
							closedir($dir);
						}
					}
					/* FETCH FROM ALL MODULES  */
					$mods = $db->cacheGetCol("SELECT `name` FROM `bbc_module` WHERE `active`=1");
					foreach ($mods as $mod)
					{
						$Dir = $M_DIR3.$mod.'/tools/';
						if (file_exists($Dir) && is_dir($Dir))
						{
							if ($dir = @opendir($Dir))
							{
								while (($data = readdir($dir)) !== false)
								{
									if(is_file($Dir.$data)
										&& !in_array($data, $notFile)
										&& substr(strtolower($data),-4)=='.php')
									{
										$r_data[] = $mod.'-'.preg_replace('~\.php$~is', '', $data);
									}
								}
								closedir($dir);
							}
						}
					}
					$r_data = array_unique($r_data);
					asort ($r_data);
				}

				echo '<ul>';
				foreach((array)$r_data as $data)
				{
					echo '<li><a href="'.$Bbc->mod['circuit'].".{$data}\" target=\"tasks\">{$data}</a></li>";
				}
				echo '</ul>';
				$output = ob_get_contents();
			ob_end_clean();
			show_css($output);
			break;
		default:
			$file  = $Bbc->mod['task'].'.php';
			$file2 = preg_replace('~([^\-]+)\-~is', '$1/tools/', $Bbc->mod['task']).'.php';
			if (is_file($M_DIR2.$file))
			{
				chdir($M_DIR2);
				$file = $M_DIR2.$file;
			}else
			if (is_file($M_DIR3.$file2))
			{
				chdir(dirname($M_DIR3.$file2));
				if (preg_match('~^([^/]+)~is', $file2, $m))
				{
					$sys->module_change($m[1]);
				}
				$file = $M_DIR3.$file2;
			}else
			if (is_file($M_DIR.$file))
			{
				chdir($M_DIR);
				$file = $M_DIR.$file;
			}else{
				$file = '';
			}
			if (!empty($file) && file_exists($file))
			{
				meta_title($Bbc->mod['task'].' - tools', 2);
				include $file;
			}
			break;
	}
}
function show_css($data = '')
{
	?>
	<html>
		<head>
			<title>Test Script List</title>
			<style type="text/css">
				body{margin: 0px; padding: 0px; font-family:verdana, arial, sans-serif; font-size: 12px; color: #666666 }
				table{margin: 0px; padding: 0px; width: 100%; height: 100%; border: 0px solid #307b9a; }
				td{vertical-align:top; }
				ul {clear: both; list-style: dotted; margin: 0px !important; padding: 0px !important; }
				ul li{padding-top: 5px !important; padding-left: 2px !important; }
				a{color: #666666; text-decoration: none; border-bottom: 1px #ccc dotted; }
				a:hover{color: #a00000; text-decoration: none; }
				a:active{color: #ff0000; text-decoration: none; }
			</style>
			<script type="text/javascript">var _ROOT="<?php echo _URI; ?>";var _URL="<?php echo _URL; ?>";</script>
		</head>
		<body bgcolor="#ffffff">
			<?php echo $data;?>
		</body>
	</html>
	<?php
}
