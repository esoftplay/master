<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');
/*
$title    = 'judul';
$message  = 'message description';
$params   = array(
	'url'       => 'string (public url if user opened from member area)',
	'url_admin' => 'string (admin url for admin section)'
	);
$user_id  = number|array(numbers)|email|admin|member|public
$group_id = number|array

## MENGIRIM KE USER TERTENTU:
alert_add($title, $message, $params, 1);												// ke user dgn user_id=1
alert_add($title, $message, $params, array(1,2,3));							// ke banyak user dengan user_id= 1 atau 2 atau 3
alert_add($title, $message, $params, 'danang@fisip.net');				// ke user yang punya email tertentu

## MENGIRIM KE ADMIN (**)
alert_add($title, $message, $params, 'admin');									// siapapun yang login ke admin
alert_add($title, $message, $params, array('admin', 1, 2, 3));	// user_id=1/2/3 yang login ke admin

## MENGIRIM KE MEMBER (**)
alert_add($title, $message, $params, 'member');									// siapapun yang login ke member area
alert_add($title, $message, $params, array('member', 1, 2, 3));	// user_id=1/2/3 yang login ke member area

## MENGIRIM KE PUBLIC (PENGUNJUNG YANG BELUM LOGIN) (**)
alert_add($title, $message, $params, 'public');									// siapapun yang mengunjungi web (jika block notif di pasang di template public)

## MENGIRIM KE GROUP USER TERTENTU (**)
alert_add($title, $message, $params, 0, 1);											// ke user yang masuk ke dalam group_id=1
alert_add($title, $message, $params, 0, array(1,2,3));					// ke user dengan group_id 1/2/3

#NB:
(**) semua akan menerima alert jika salah satu user membuka maka notif dianggap `read` bagi semua user tersebut
-------------------------------------------------------------------------------------------------------------------
jika ingin mengirim ke tiap user secara personal maka kita harus memanggil alert_add satu persatu untuk setiap user
sehingga jika ada satu user membuka notif, maka notif untuk user lain status nya masih `unread`
*/
function alert_add($title, $description, $params = array(), $user_id='none', $group_id=0, $module = '')
{
	global $db, $user, $Bbc;
	$title       = addslashes($title);
	$description = addslashes($description);
	$is_admin    = 3; // any page
	$user_type   = array(
		'member' => 0,
		'admin'  => 1,
		'public' => 2,
		'any'    => 3
		);
	$user_rtype = array_keys($user_type);
	if (is_string($user_id) && in_array($user_id, $user_rtype))
	{
		$is_admin = $user_type[$user_id];
		$user_id  = array(0);
	}else{
		if (!is_array($user_id))
		{
			$user_id = array($user_id);
		}
		foreach ($user_id as $i => $usr_id)
		{
			if ($usr_id==='none')
			{
				$user_id[$i] = $user->id;
			}else
			if (is_numeric($usr_id))
			{
				$user_id[$i] = $usr_id;
			}else
			if (is_string($usr_id))
			{
				if (is_email($usr_id))
				{
					$j = user_name($usr_id, 'user_id');
					if ($j > 0)
					{
						$user_id[$i] = $j;
					}else{
						unset($user_id[$i]);
					}
				}else
				if (in_array($usr_id, $user_rtype))
				{
					$is_admin = $user_type[$usr_id];
					unset($user_id[$i]);
				}
			}
		}
	}
	// JIKA YG DI ALERT ADALAH PUBLIC (NON-LOGIN) MAKA UBAH SEMUA TANPA USER_ID DAN GROUP_ID
	if ($is_admin==2)
	{
		$user_id  = array(0);
		$group_id = array(0);
	}else{
		$user_id  = array_unique($user_id);
		if (!is_array($group_id))
		{
			$group_id = array(intval($group_id));
		}else{
			$group_id = array_unique($group_id);
		}
	}
	if (!empty($params))
	{
		$params = is_array($params) ? config_encode($params) : $params;
	}else{
		$params = '';
	}
	if (empty($module))
	{
		$module = $Bbc->mod['name'];
	}
	foreach ($group_id as $g_id)
	{
		// jika group_id ditentukan maka ubah ke any privilege agar tetap tampil
		$admin = $g_id > 0 ? 3 : $is_admin;
		foreach ($user_id as $u_id)
		{
			$q_alert = "INSERT INTO `bbc_alert` SET
				`user_id`     = {$u_id},
				`group_id`    = {$g_id},
				`module`      = '{$module}',
				`title`       = '{$title}',
				`description` = '{$description}',
				`params`      = '{$params}',
				`is_open`     = 0,
				`is_admin`    = {$admin},
				`updated`     = '0000-00-00 00:00:00',
				`created`     = NOW()";
			if (!$db->Execute($q_alert))
			{
				include _ROOT.'modules/user/repair-comment.php';
				$db->Execute($q_alert);
			}
			$alert_id = $db->Insert_ID();
			$alert_dt = $db->getRow("SELECT * FROM `bbc_alert` WHERE `id`={$alert_id}");
			user_call_func(__FUNCTION__, $alert_dt);
		}
	}
}
function alert_view($data)
{
	$check  = array('id', 'module', 'title', 'description', 'params');
	foreach ($check as $field)
	{
		if (!isset($data[$field]))
		{
			return array();
		}
	}
	global $Bbc, $sys, $user, $db;
	_func($data['module']);
	$_func = $data['module'].'_alert_view';
	if (function_exists($_func))
	{
		$output = $_func($data);
	}else
	if (!empty($data['params']))
	{
		$data['params'] = config_decode($data['params']);
		if (!empty($data['params']['url']))
		{
			$data['url'] = $data['params']['url'];
		}
		if (_ADMIN!="" && !empty($data['params']['url_admin']))
		{
			$data['url'] = $data['params']['url_admin'];
		}
	}else $data['params'] = array();
	$output = $data;
	$data['url']   = (empty($output['url']) && _ADMIN!='') ? 'index.php?mod='.$data['module'].'.main' : @$output['url'];
	if (!empty($data['url']))
	{
		$output['url'] = site_url($data['url']);
	}
	if (_ADMIN != '')
	{
		$i = _cpanel_check_link($output['url']);
		$output['ref_id'] = intval($i);
		if (empty($output['ref_id']))
		{
			$output['ref_id'] = 'alert-'.$output['id'];
		}
	}
	return $output;
}
function _cpanel_check_link($link)
{
	global $Bbc, $db;
	$output = '';
	if (!empty($link))
	{
		$menu = array();
		foreach ($Bbc->menu->left as $m)
		{
			if ($m['link']==$link)
			{
				$menu = $m;
				break;
			}
		}
		if (!empty($menu))
		{
			$output = $menu['id'];
		}else{
			foreach ($Bbc->menu->cpanel as $m)
			{
				if ($m['link']==$link)
				{
					$menu = $m;
					break;
				}
			}
			if (!empty($menu))
			{
				$lastID = $db->getOne("SELECT id FROM bbc_menu ORDER BY id DESC LIMIT 1");
				$output = $menu['id']+$lastID;
			}else{
				if (preg_match('~[\?\&_]~s', $link))
				{
					$output = call_user_func(__FUNCTION__, preg_replace('~([\?&_][^\?&_]+)$~is', '', $link));
				}
			}
		}
	}
	return $output;
}
/*
$to:
	- $user_id           = Integer dari field ID di table `bbc_user`
	- $user_ids          = Array yang berisi Integer dari field ID di table `bbc_user`
	- 'group:'.$group_id = String yang di awali dengan 'group:' kemudian diikuti Integer dari field ID di table `bbc_user_group`
*/
function alert_push($to, $title, $message, $module = 'content', $arguments = array(), $action = 'default')
{
	global $db, $sys;
	$ids      = array();
	$out      = false;
	$group_id = 0;
	if ($to == 0)
	{
		$ids[] = $to;
	}else
	if (is_numeric($to))
	{
		$ids[] = intval($to);
	}else
	if (is_array($to))
	{
		$ids = $to;
	}else
	if (!empty($to) && is_string($to))
	{
		if (substr($to, 0, 6) == 'group:')
		{
			$group_id = substr($to, 6, strlen($to)-1);
			if (!empty($group_id))
			{
				$ids = $db->getCol("SELECT `user_id` FROM `bbc_user_push` WHERE `group_ids` LIKE '%,{$group_id},%' WHERE `active`=1");
			}
		}else{
			$id = $db->getOne("SELECT `id` FROM `bbc_user` WHERE `username`='{$to}'");
			if (!empty($id))
			{
				$ids[] = $id;
			}
		}
	}
	if (!empty($ids))
	{
		$exist = $db->getOne("SHOW TABLES LIKE 'bbc_user_push_notif'");
		if (empty($exist))
		{
			$db->Execute("CREATE TABLE `bbc_user_push_notif` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` bigint(20) DEFAULT '0',
				`group_id` int(11) DEFAULT '0',
				`title` varchar(150) DEFAULT '',
				`message` varchar(255) DEFAULT '',
				`params` text COMMENT 'variable yang akan di proses dalam mobile app field wajib action, module, argument',
				`return` text COMMENT 'data return dari API notifikasi',
				`status` tinyint(1) DEFAULT '0' COMMENT '0=belum terkirim, 1=berhasil terkirim, 2=sudah terbaca, 3=gagal terkirim',
				`created` datetime DEFAULT CURRENT_TIMESTAMP,
				`updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				KEY `user_id` (`user_id`),
				KEY `group_id` (`group_id`),
				KEY `status` (`status`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table untuk menyimpan data notifikasi yang dikirim ke para pengguna mobile app'");
		}else{
			$timestamp = date('Y-m-d H:i:s', strtotime('-2 MONTH'));
			$db->Execute("DELETE FROM `bbc_user_push_notif` WHERE `created`<'{$timestamp}'");
		}
		$data = array(
			'title'   => $title,
			'message' => $message,
			'status'  => 0,
			'params'  => json_encode(
				array(
					'action'    => $action,
					'module'    => $module,
					'arguments' => $arguments
					)
				)
			);
		foreach ($ids as $id)
		{
			$data['user_id']  = $id;
			$data['group_id'] = $group_id;
			$i                = $db->Insert('bbc_user_push_notif', $data);
			if ($i)
			{
				_class('async')->run('alert_push_send', [$i, 0]);
				if (!$out)
				{
					$out = $i;
				}
			}
		}
	}
	return $out;
}

function alert_push_send($id, $last_id=0)
{
	global $db, $sys;
	$output = false;
	$data   = $db->getRow("SELECT * FROM `bbc_user_push_notif` WHERE id={$id}");
	if (!empty($data))
	{
		$last_id = intval($last_id);
		$to = array();
		if (empty($data['user_id']))
		{
			$tos = $db->getAll("SELECT * FROM `bbc_user_push` WHERE `id` > {$last_id} ORDER BY `id` ASC LIMIT 5");
		}else{
			$tos = $db->getAll("SELECT * FROM `bbc_user_push` WHERE `user_id`={$data['user_id']} AND `id` > {$last_id} ORDER BY `id` ASC LIMIT 5");
		}
		if (!empty($tos))
		{
			$params    = json_decode($data['params'], 1);
			$timestamp = date('Y-m-d H:i:s');
			foreach ($tos as $to)
			{
				$msg = array(
					'to'    => $to['token'],
					'title' => $data['title'],
					'body'  => $data['message'],
					'sound' => 'default',
					'data'  => array(
						'id'      => $data['id'],
						'action'  => $params['action'],
						'module'  => $params['module'],
						'title'   => $data['title'],
						'message' => $data['message'],
						'params'  => $params['arguments']
						)
					);
				$last_id = $to['id'];
				$return  = $sys->curl('https://exp.host/--/api/v2/push/send', $msg);
				try {
					$json = @json_decode($return, 1);
					if (!$output && !empty($json['data']))
					{
						if (!empty($json['data']['status']))
						{
							if ($json['data']['status'] == 'ok')
							{
								$output = true;
							}else{
								// remove data if no longer available
								if (preg_match('~DeviceNotRegistered~is', $return))
								{
									$db->Execute("DELETE FROM `bbc_user_push` WHERE `id`={$to['id']}");
								}
								// if (!empty($json['data']['details']['sns']['statusCode']))
								// {
								// 	if ($json['data']['details']['sns']['statusCode']=='400')
								// 	{
								// 		$db->Execute("DELETE FROM `bbc_user_push` WHERE `id`={$to['id']}");
								// 	}
								// }
							}
						}
					}
				} catch (Exception $e) {}
			}
			if ($data['status']==0)
			{
				$db->Update('bbc_user_push_notif', array(
						'status' => ($output ? 1 : 3),
						'return' => $return
					),
				$data['id']);
			}
			_class('async')->run('alert_push_send', [$data['id'], $last_id]);
		}
	}
	return $output;
}

// Example: modules/user/push-token.php
function alert_push_signup($token, $user_id, $group_ids, $username, $device, $push_id = 0)
{
	global $db;
	$exist = $db->getOne("SHOW TABLES LIKE 'bbc_user_push'");
	if (empty($exist))
	{
		$db->Execute("CREATE TABLE `bbc_user_push` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) DEFAULT '0',
			`group_ids` varchar(120) DEFAULT '0' COMMENT 'comma separated like repairImplode()',
			`username` varchar(120) DEFAULT '',
			`token` varchar(255) DEFAULT '',
			`device` varchar(255) DEFAULT '',
			`ipaddress` varchar(20) DEFAULT '',
			`created` datetime DEFAULT CURRENT_TIMESTAMP,
			`updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'setiap mengirim pesan ke table bbc_user_push_notif maka field ini akan di update',
			PRIMARY KEY (`id`),
			KEY `user_id` (`user_id`),
			KEY `group_ids` (`group_ids`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table untuk menyimpan token dari para pengguna mobile app';");
	}
	if (!empty($group_ids))
	{
		if (!is_array($group_ids))
		{
			$group_ids = explode(',', trim($group_ids, ','));
		}
		$group_ids = array_map('intval', array_unique($group_ids));
	}else{
		$group_ids = array(0);
	}
	$group_ids = repairImplode($group_ids);
	$input     = array(
		'user_id'   => $user_id,
		'group_id'  => $group_ids,
		'username'  => $username,
		'token'     => $token,
		'device'    => $device,
		'ipaddress' => @$_SERVER['REMOTE_ADDR'],
		'updated'   => date('Y-m-d H:i:s')
		);
	$output = $db->Update('bbc_user_push', $input, $push_id);
	if ($output)
	{
		user_call_func(__FUNCTION__, $token, $user_id, $group_ids, $username, $output);
	}
	return $output;
}