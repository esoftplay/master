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
			if (defined('_NO_ALERT') && _NO_ALERT == 1)
			{
				$alert_dt = array(
					'user_id'     => $u_id,
					'group_id'    => $g_id,
					'module'      => $module,
					'title'       => $title,
					'description' => $description,
					'params'      => $params,
					'is_open'     => 0,
					'is_admin'    => $admin,
					'updated'     => '0000-00-00 00:00:00'
				);
			}else{
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
					// include _ROOT.'modules/user/repair-comment.php'; # sudah tidak terpakai lagi
					$db->Execute($q_alert);
				}
				$alert_id = $db->Insert_ID();
				$alert_dt = $db->getRow("SELECT * FROM `bbc_alert` WHERE `id`={$alert_id}");
			}
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
		$i = _alert_view_cpanel($output['url']);
		$output['ref_id'] = intval($i);
		if (empty($output['ref_id']))
		{
			$output['ref_id'] = 'alert-'.$output['id'];
		}
	}
	return $output;
}

function _alert_view_cpanel($link)
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
	- $user_id-$group_id = Integer dari field ID di table `bbc_user` dengan ID dari table `bbc_user_group`
	- /topics/$topicname = akan mengirim user yg ada di table bbc_user_push_topic_list
*/
function alert_push($to, $title, $message, $module = 'content', $arguments = array(), $action = 'default', $sending_id=0)
{
	global $db, $sys;
	$ids      = array();
	$out      = false;
	$group_id = 0;
	$tos      = [];
	// Jika 0 maka tidak akan dikirim
	if (is_numeric($to) && $to == 0)
	{
		return false;
	}else
	// Jika berupa angka maka akan terkirim ke user_id tersebut (gak perduli di group apapun)
	if (is_numeric($to))
	{
		$id    = intval($to);
		$ids[] = $id;
		$tos[] = '/topics/user_'.$id;
	}else
	// Jika dikirim ke banyak user_id (atau user_id yang di group tertentu misal beberapa user yang di aplikasi driver)
	if (is_array($to))
	{
		$ids = $to;
		$tos = array_map(function($a){ return '/topics/user_'.$a;}, $to);
	}else
	// Jika formatnya user_id-group_id maka akan dikirimkan ke user_id tersebut khusus untuk group tersebut
	// contoh kasus user tersebut register sebagai driver sekaligus sebagai penumpang (sedangkan aplikasi driver & penumpang beda app)
	if (preg_match('~^([0-9]+)\-([0-9]+)$~is', $to, $m))
	{
		if ($m[1] == 0)
		{
			return false;
		}else{
			$ids[]    = $to;
			$push_ids = $db->getCol("SELECT `token` FROM `bbc_user_push` WHERE `user_id`={$m[1]} AND `type`=1 AND `group_ids` LIKE '%,{$m[2]},%'");
			if (!empty($push_ids))
			{
				$tos = $push_ids;
			}
		}
	}else
	// Jika tujuan dalam format string
	if (!empty($to) && is_string($to))
	{
		// Jika ingin mengirim untuk semua user yang ada di group tertentu
		if (substr($to, 0, 6) == 'group:')
		{
			$group_id = substr($to, 6);
			if (!empty($group_id))
			{
				$topic_id = $db->getOne("SELECT `id` FROM `bbc_user_push_topic` WHERE `name`='group_{$group_id}'");
				if (!empty($topic_id))
				{
					$tos[] = '/topics/group_'.$group_id;
					$ids   = $topic_id;
				}
			}
		}else
		// misal kirim ke topic tetentu misal
		if (substr($to, 0, 8) == '/topics/')
		{
			$topic = substr($to, 8);
			if ($topic == 'userAll')
			{
				$ids[] = 0;
				$tos[] = $to;
			}else{
				$topic_id = $db->getOne("SELECT `id` FROM `bbc_user_push_topic` WHERE `name`='{$topic}' LIMIT 1");
				if (!empty($topic_id))
				{
					$ids = $topic_id;
					if (!empty($ids))
					{
						$tos[] = $to;
					}
				}
			}
		}else
		// Jika mengirim notif ke username tertentu
		if (is_email($to))
		{
				$id = $db->getOne("SELECT `id` FROM `bbc_user` WHERE `username`='{$to}' LIMIT 1");
				if (!empty($id))
				{
					$ids[] = $id;
					$tos[] = '/topics/user_'.$id;
				}
		}
	}
	if (!empty($ids))
	{
		$timestamp = date('Y-m-d H:i:s', strtotime('-2 MONTH'));
		$db->Execute("DELETE FROM `bbc_user_push_notif` WHERE `created`<'{$timestamp}'");
		$title   = strip_tags($title);
		$message = strip_tags($message, '<br>');
		$message = preg_replace('~<br(?:\s+?/?)?>~is', "\n", $message);
		$params  = [
			'action' => $action,
			'module' => $module
		];
		if (!empty($arguments))
		{
			foreach ($arguments as $key => $value)
			{
				$params[$key] = $value;
			}
		}
		$data = array(
			'title'   => $title,
			'message' => $message,
			'status'  => 0,
			'params'  => json_encode($params)
			);
		// iLog([$ids, $data, $tos, $group_id, $sending_id]);
		_alert_push_insert($ids, $data, $tos, $group_id, $sending_id);
		$out = true;
	}
	return $out;
}

function _alert_push_insert($ids, $data, $tos, $group_id, $sending_id, $last_id = 0)
{
	global $db, $sys;
	$out = 0;
	$id  = null;
	// jika $ids berisi array maka itu adalah kumpulan id dr user_id
	if (is_array($ids))
	{
		if (isset($ids[$last_id]))
		{
			$id = $ids[$last_id];
		}
	}else{
		// jika $ids berisi integer maka itu adalah topic_id
		$i = $db->getOne("SELECT DISTINCT `user_id` FROM `bbc_user_push_topic_list` WHERE `topic_id`={$ids} LIMIT {$last_id}, 1");
		if (!empty($i))
		{
			$id = $i;
		}
	}
	if (isset($id))
	{
		$r = explode('-', $id);
		if (is_numeric($r[0]))
		{
			$data['user_id']  = $r[0];
			$data['group_id'] = (!empty($r[1]) && is_numeric($r[1])) ? $r[1] : $group_id;
			$push_notif_id    = $db->Insert('bbc_user_push_notif', $data);
			$out              = $push_notif_id;
			if (!empty($push_notif_id) && $tos != ['/topics/userAll'])
			{
				_class('async')->run('alert_push_send', [$push_notif_id, 0]);
			}
			if (!empty($sending_id))
			{
				$db->Execute("UPDATE `bbc_user_push_sending` SET `sent`=(`sent`+1) WHERE `id`={$sending_id}");
			}
			_class('async')->run(__FUNCTION__, [$ids, $data, $tos, $group_id, $sending_id, ++$last_id]);
		}
	}else
	if ($last_id > 0 && !empty($tos))
	{
		if (defined('_FCM_SENDER_ID') && defined('_FCM_SERVER_JSON'))
		{
			// https://php-fcm.readthedocs.io/en/latest/message.html#notification-sending-options
			$url    = 'https://fcm.googleapis.com/v1/projects/'.alert_fcm('project_id').'/messages:send';
			$params = json_decode($data['params'], 1);
			$args   = [];
			foreach ($params as $key => $value)
			{
				$args[$key] = $value;
			}
			$post = [
				'message' => [
					'notification' => [
						'title'    => $data['title'],
						'body'     => $data['message'],
					],
					'android' => [
						'notification' => [
							'sound' => !empty($args['sound']) ? $args['sound'] : 'default',
							'icon'  => 'notification_icon',
							'color' => defined('_FCM_ICON_BG') ? _FCM_ICON_BG : '@color/notification_icon_color'
						]
					],
					'apns' => [
						'payload' => [
							'aps' => [
								'sound' => !empty($args['sound']) ? $args['sound'] : 'default'
							]
						]
					],
					'data' => $args
				]
			];
			$header = array(
				'CURLOPT_HTTPHEADER' => array(
					'Authorization: Bearer '.alert_fcm_token(),
					'Content-Type: application/json')
				);
			if (empty($post['message']['data']))
			{
				unset($post['message']['data']);
			}
			foreach ($tos as $to)
			{
				$msg = [];
				if (substr($to, 0, 8) == '/topics/')
				{
					$msg['message']['topic'] = substr($to, 8);
				}else{
					$msg['message']['token'] = $to;
				}
				$msg    = array_merge_recursive($msg, $post);
				$output = json_decode($sys->curl($url, json_encode($msg), $header), 1);
				// iLog([$output, $msg]);
				// {"error": {"code": 404, "message": "Requested entity was not found.", "status": "NOT_FOUND", "details": [{"@type": "type.googleapis.com/google.firebase.fcm.v1.FcmError", "errorCode": "UNREGISTERED" }] } }
				if (!empty($output['error']['status']))
				{
					$db->Execute("DELETE FROM `bbc_user_push` WHERE `token`='{$to}' AND `type`=1");
				}
			}
		}
		if (!empty($sending_id))
		{
			$db->Execute("UPDATE `bbc_user_push_sending` SET `status`=1 WHERE `id`={$sending_id}");
		}
	}
	return $out;
}

function alert_push_send($id, $last_id=0)
{
	global $db, $sys;
	$output = false;
	$limit  = 100;
	$data   = $db->getRow("SELECT * FROM `bbc_user_push_notif` WHERE id={$id}");
	if (!empty($data))
	{
		$updatedb = 0;
		$return   = '';

		if ($data['status'] != 0 && $data['user_id'] != 0) // yang belum dikirim saja
		{
			$output = true;
		}else{
			$unread  = $db->getOne("SELECT count(id) FROM `bbc_user_push_notif` WHERE `user_id`={$data['user_id']} AND `status` IN(0,1)"); // status 1 = berhasil terkirim, 0 = belum terkirim (tetapi pada mobile app tetap bisa di list)
			$tos     = array();
			$last_id = intval($last_id);
			$add_sql = ' AND `type`=0';
			$add_sql.= !empty($data['group_id']) ? ' AND `group_ids` LIKE \'%,'.$data['group_id'].',%\'' : '';
			if (!empty($data['user_id']))
			{
				if ($data['user_id'] == -1) // Jika ini broadcast message
				{
					$sql = "`id` > {$last_id}{$add_sql}"; // digunakan untuk pencarian device selanjutnya jika device per user lebih dari 100
					$tos = $db->getAll("SELECT * FROM `bbc_user_push` WHERE {$sql} ORDER BY `id` ASC LIMIT {$limit}");
				}else{
					$sql = "`user_id`={$data['user_id']} AND `id` > {$last_id}{$add_sql}";
					$tos = $db->getAll("SELECT * FROM `bbc_user_push` WHERE {$sql} ORDER BY `id` ASC LIMIT {$limit}");
				}
			}else{
				return false;
			}
			if (!empty($tos))
			{
				$params    = json_decode($data['params'], 1);
				$timestamp = date('Y-m-d H:i:s');
				$messages  = array();
				foreach ($tos as $to)
				{
					$tmp_title         = preg_replace('~^#[A-Za-z]+\s{0,}~is', '', $data['title']);
					$messages[$to['id']][] = array(
						'id'        => $to['id'],
						'to'        => $to['token'],
						'title'     => $tmp_title,
						'body'      => $data['message'],
						'sound'     => 'default',
						'badge'     => $unread,
						'channelId' => 'android',
						'data'      => array(
														'id'      => $data['id'],
														'action'  => $params['action'],
														'module'  => $params['module'],
														'title'   => $tmp_title,
														'message' => $data['message'],
														'params'  => $params['arguments']
													)
						);
				}
				$last_id = $to['id'];

				$r_ch = array();
				foreach ($messages as $to_id => $message)
				{
					$message = json_encode($message);
					$ch      = curl_init();
					curl_setopt($ch, CURLOPT_URL,"https://exp.host/--/api/v2/push/send");
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$r_ch[] = $ch;
				}

				// create the multiple cURL handle
				$mh = curl_multi_init();
				foreach ($r_ch as $ch)
				{
					curl_multi_add_handle($mh, $ch);
				}
				// execute the multi handle
				do {
					$status = curl_multi_exec($mh, $active);
					if ($active)
					{
						curl_multi_select($mh);
					}
				} while ($active && $status == CURLM_OK);
				foreach ($r_ch as $ch)
				{
					$return = curl_multi_getcontent($ch);
					try {
						$json = @json_decode($return, 1);
						if (!empty($json['data']) && is_array($json['data']))
						{
							$i = 0;
							foreach ($json['data'] as $out)
							{
								$to = $tos[$i];
								$i++;
								if (!empty($out['status']))
								{
									if ($out['status'] == 'ok')
									{
										$output = true;
									}else{
										switch (@$out['details']['error'])
										{
											// the device cannot receive push notifications anymore and you should stop sending messages to the corresponding Expo push token.
											case 'DeviceNotRegistered':
												$db->Execute("DELETE FROM `bbc_user_push` WHERE `id`={$to['id']}");
												break;
											// the total notification payload was too large. On Android and iOS the total payload must be at most 4096 bytes.
											case 'MessageTooBig':
												break;
											// you are sending messages too frequently to the given device. Implement exponential backoff and slowly retry sending messages.
											case 'MessageRateExceeded':
												break;
											// your push notification credentials for your standalone app are invalid (ex: you may have revoked them). Run `expo build:ios -c` to regenerate new push notification credentials for iOS.
											case 'InvalidCredentials':
												/*
												When your push notification credentials have expired, simply run expo build:ios -c --no-publish
												to clear your expired credentials and generate new ones. The new credentials will take effect within a few minutes of being generated.
												You do not have to submit a new build!
												*/
												break;
										}
									}
								}
							}
						}
					} catch (Exception $e) {}
					curl_multi_remove_handle($mh, $ch);
				}
				curl_multi_close($mh);

				// Jika status masih belum terkirim
				if ($data['status']==0)
				{
					// check apakah masih ada data setelah $last_id
					$dtpush = $db->getRow("SELECT * FROM `bbc_user_push` WHERE `id`>{$last_id} AND {$sql} ORDER BY `id` ASC LIMIT 1");
					// Jika data setelah yang terakhir diproses sudah tidak ada lagi maka update row di `alert_push_send`
					if (empty($dtpush))
					{
						$updatedb = 1;
					}
				}
				if (!$updatedb)
				{
					_class('async')->run('alert_push_send', [$data['id'], $last_id]);
				}
			}
		}
		if ($updatedb)
		{
			$db->Update('bbc_user_push_notif', array(
					'status' => ($output ? 1 : 3),
					'return' => $return
				),
			$data['id']);
		}
	}
	return $output;
}

// Example: modules/user/push-token.php
function alert_push_signup($token, $user_id, $group_ids, $username, $device, $os, $push_id = 0)
{
	global $db;
	$topics = [];
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
	$db->Execute("DELETE FROM `bbc_user_push` WHERE `token`='{$token}' AND `id`!={$push_id} AND `user_id` IN (0,{$user_id})");
	$input     = array(
		'user_id'   => $user_id,
		'group_ids' => $group_ids,
		'username'  => $username,
		'token'     => $token,
		'type'      => preg_match('~^ExponentPushToken~is', $token) ? 0 : 1,
		'device'    => $device,
		'os'        => $os,
		'ipaddress' => @$_SERVER['REMOTE_ADDR'],
		'updated'   => date('Y-m-d H:i:s')
		);
	$old = $db->getRow("SELECT * FROM `bbc_user_push` WHERE `id`={$push_id}");
	if (!empty($old['id']))
	{
		if ($old['user_id'] != $user_id)
		{
			$db->Update('bbc_user_push_topic_list', ['user_id'=>$user_id], 'push_id='.$push_id);
			if (!empty($input['type']))
			{
				$GLOBALS['token'] = $token;
				$unsubs = ['user_'.$old['user_id']];
				alert_push_topic($push_id, $user_id, false, $unsubs);
			}
		}
		$output = $push_id;
		$db->Update('bbc_user_push', $input, $push_id);
	}else{
		$output = $db->Insert('bbc_user_push', $input);
	}
	if ($output)
	{
		// panggil semua function alert_push_signup di semua module
		user_call_func(__FUNCTION__, $token, $user_id, $group_ids, $username, $device, $os, $push_id, $output);
		if (!empty($input['type']))
		{
			$GLOBALS['token'] = $token;
			alert_push_topic($output, $user_id, true, $topics);
		}
		$output = [
			'push_id' => $output,
			'topics'  => $topics
		];
	}
	return $output;
}

// untuk mendaftarkan device ke topic yang dibutuhkan dan menyimpan di DB
function alert_push_topic($push_id, $user_id, $is_subscribe=true, &$topics=array())
{
	global $db, $token;
	if ($is_subscribe)
	{
		/* JIKA user_id==0 berrti dia logout dan perlu unsubscribe kesemua kecuali userAll */
		if ($user_id == 0)
		{
			$dels        = [];
			$old_user_id = $db->getOne("SELECT `user_id` FROM `bbc_user_push_topic_list` WHERE `push_id`={$push_id} LIMIT 1");
			if (!empty($old_user_id))
			{
				$dels[] = 'user_'.$old_user_id;
			}
			$topics = $db->getCol("SELECT t.`name` FROM `bbc_user_push_topic` AS t LEFT JOIN `bbc_user_push_topic_list` AS l on(t.`id`=l.`topic_id`) WHERE `push_id`={$push_id}");
			foreach($topics as $topic)
			{
				if (!in_array($topic, $dels))
				{
					$dels[] = $topic;
				}
			}
			$db->Execute("DELETE FROM `bbc_user_push_topic_list` WHERE `push_id`={$push_id}");
			if (!empty($dels))
			{
				call_user_func_array(__FUNCTION__, [$push_id, $user_id, false, &$dels]);
			}
		}else{
			$topics['info'] = []; // function hook bs menambahkan info dengan menjadikan nama topic sebagai key nya
			$topics[]       = 'userAll';
			$topics[]       = 'user_'.$user_id;
			$group          = $db->getOne("SELECT `group_ids` FROM `bbc_user_push` WHERE `id`={$push_id}");
			$ids            = repairExplode($group);
			foreach ($ids as $g_id)
			{
				$topics[] = 'group_'.$g_id;
				if (empty($topics['info']['group_'.$g_id]))
				{
					$topics['info']['group_'.$g_id] = 'User group: '.$db->getOne("SELECT `name` FROM `bbc_user_group` WHERE `id`={$g_id}");
				}
			}
			$mods           = user_modules();
			foreach ($mods as $mod)
			{
				if (function_exists($mod.'_'.__FUNCTION__))
				{
					call_user_func_array($mod.'_'.__FUNCTION__, [$push_id, $user_id, $is_subscribe, &$topics]);
				}
			}
			// list topic yg sudah terdaftar sblumnya
			$listed = $db->getAll("SELECT t.`id`, t.`name`, t.`user_id`, l.`list_id` FROM `bbc_user_push_topic` AS t LEFT JOIN `bbc_user_push_topic_list` AS l ON(l.`topic_id`=t.`id`) WHERE l.`push_id`={$push_id} AND l.`user_id`={$user_id}");

			$olds = [];
			$news = [];
			$dels = [];
			/* HAPUS YG SUDAH TERDAFTAR JIKA TOPICS BARU GK ADA LAGI (SUDAH KELUAR DR GROUP DLL) */
			foreach ($listed as $dt)
			{
				$olds[] = $dt['name'];
				if (!in_array($dt['name'], $topics))
				{
					// jika topic adalah buatan admin maka pertahankan subscribe nya
					if (!empty($dt['user_id']))
					{
						$news[] = $dt['name'];
					}else{
						$dels[] = $dt['name'];
						$db->Execute("DELETE FROM `bbc_user_push_topic_list` WHERE `list_id`={$dt['list_id']}");
					}
				}
			}

			/* MASUKKAN DB JIKA BELUM ADA DI LISTED */
			foreach ($topics as $topic)
			{
				if (!is_array($topic))
				{
					if (!in_array($topic, $olds))
					{
						if (!preg_match('~^user[_A]~s', $topic))
						{
							$dt = $db->getRow("SELECT * FROM `bbc_user_push_topic` WHERE `name`='{$topic}'");
							if (empty($dt))
							{
								$dt = [
									'id'   => $db->Insert('bbc_user_push_topic', ['name' => $topic, 'description' => @$topics['info'][$topic], 'user_id' => '0']),
									'name' => $topic
								];
							}
							$db->Insert('bbc_user_push_topic_list', ['push_id' => $push_id, 'topic_id' => $dt['id'], 'user_id' => $user_id]);
						}
					}
				}
			}
			if (!empty($news))
			{
				$topics = array_merge($topics, $news);
			}
			unset($topics['info']);
			// _class('async')->run('alert_fcm_topic_subscribe', [$token, $topics]);
			if (!empty($dels))
			{
				call_user_func_array(__FUNCTION__, [$push_id, $user_id, false, $dels]);
			}
		}
	}else{
		$mods = user_modules();
		foreach ($mods as $mod)
		{
			if (function_exists($mod.'_'.__FUNCTION__))
			{
				call_user_func_array($mod.'_'.__FUNCTION__, [$push_id, $user_id, $is_subscribe, &$topics]);
			}
		}
		// _class('async')->run('alert_fcm_topic_unsubscribe', [$token, $topics]);
	}
}

function alert_push_topic_delete($topic)
{
	global $db;
	if (!empty($topic['id']))
	{
		$tokens = [];
		$r_data = $db->getAll("SELECT l.`list_id`, p.`token` FROM `bbc_user_push_topic_list` AS l LEFT JOIN `bbc_user_push` AS p ON (p.`id`=l.`push_id`) WHERE l.`topic_id`={$topic['id']} ORDER BY l.`list_id` ASC LIMIT 0, 100");
		if (!empty($r_data))
		{
			foreach ($r_data as $data)
			{
				$tokens[] = $data['token'];
				$db->Execute("DELETE FROM `bbc_user_push_topic_list` WHERE `list_id`={$data['list_id']}");
			}
			alert_fcm_topic_unsubscribe($tokens, $topic['name']);
			_class('async')->run(__FUNCTION__, [$topic]);
		}else{
			$db->Execute("DELETE FROM `bbc_user_push_topic` WHERE `id`={$topic['id']}");
		}
	}
}

function alert_fcm_subscribe($user_ids, $topic, $last_id = 0 )
{
	if (!empty($user_ids[$last_id]))
	{
		global $db;
		$tokens = [];
		$next   = 1;

		for ($i=0; $i < 100; $i++)
		{
			if (isset($user_ids[$last_id]))
			{
				$id = $user_ids[$last_id];
				if (is_numeric($id))
				{
					# check apakah user_id valid
					$arr = $db->getAll("SELECT * FROM `bbc_user_push` WHERE `user_id`={$id} AND `type`=1");
					if (!empty($arr))
					{
						foreach ($arr as $push)
						{
							# check apakah sudah ada di bbc_user_push_topic_list
							$is_exists = $db->getOne("SELECT 1 FROM `bbc_user_push_topic_list` WHERE `push_id`={$push['id']} AND `topic_id`={$topic['id']} AND `user_id`={$id}");
							if (!$is_exists)
							{
								# masukkan ke array tokens
								$tokens[] = $push['token'];
								$db->Insert('bbc_user_push_topic_list', [
									'push_id'  => $push['id'],
									'topic_id' => $topic['id'],
									'user_id'  => $id
								]);
							}
						}
					}
				}
				$last_id++;
			}else{
				$next=0;
				break;
			}
		}
		if (!empty($tokens))
		{
			_class('async')->run('alert_fcm_topic_subscribe', [$tokens, $topic['name']]);
		}
		if ($next)
		{
			_class('async')->run(__FUNCTION__, [$user_ids, $topic, $last_id]);
		}
	}
}

function alert_fcm($key='')
{
	$out = json_decode(_FCM_SERVER_JSON, 1);
	if (!empty($key))
	{
		return @$out[$key];
	}else{
		return $out;
	}
}

function alert_fcm_token()
{
	/*
	// "google/apiclient": "^2.15",
	require_once _ROOT.'modules/images/vendor/autoload.php';
	$client = new \Google_Client();
	// $client->setAuthConfig($credentialsFilePath);
	$client->setAuthConfig(alert_fcm());
	$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
	$client->refreshTokenWithAssertion();
	$token = $client->getAccessToken();
	return $token['access_token'];
	*/
	$tmp  = sys_get_temp_dir().'/jwt-token'.menu_save(_URL);
	$txt  = file_read($tmp);
	$json = json_decode($txt, 1);
	$time = time(); // Get seconds since 1 January 1970
	if (!empty($json['expire']) && $json['expire'] > $time)
	{
		return $json['result']['access_token'];
	}

	function alert_fcm_token_encode($text)
	{
		return str_replace(
			['+', '/', '='],
			['-', '_', ''],
			base64_encode($text)
		);
	}
	// https://developers.google.com/identity/protocols/oauth2/service-account#httprest
	// Parse service account details
	$authConfig = alert_fcm();

	// Read private key from service account details
	$secret = openssl_get_privatekey($authConfig['private_key']);

	// Create the token header
	$header = json_encode([
		'typ' => 'JWT',
		'alg' => 'RS256'
	]);

	// Allow 1 minute time deviation between client en server (not sure if this is necessary)
	$start = $time - 60;
	$end   = $start + 3600;

	// Create payload
	$payload = json_encode([
		'iss'   => $authConfig['client_email'],
		'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
		'aud'   => 'https://oauth2.googleapis.com/token',
		'exp'   => $end,
		'iat'   => $start
	]);

	// Encode Header
	$base64UrlHeader = alert_fcm_token_encode($header);

	// Encode Payload
	$base64UrlPayload = alert_fcm_token_encode($payload);

	// Create Signature Hash
	$result = openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $secret, OPENSSL_ALGO_SHA256);

	// Encode Signature to Base64Url String
	$base64UrlSignature = alert_fcm_token_encode($signature);

	// Create JWT
	$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

	//-----Request token, with an http post request------
	$options = array(
		'http' => array(
			'method'  => 'POST',
			'content' => 'grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion='.$jwt,
			'header'  => 'Content-Type: application/x-www-form-urlencoded'
		)
	);
	$context      = stream_context_create($options);
	$responseText = file_get_contents('https://oauth2.googleapis.com/token', false, $context);

	$response = json_decode($responseText, 1);
	file_write($tmp, json_encode(['result' => $response, 'expire' => strtotime('+30 MINUTES')]));
	return $response['access_token'];
}

function alert_fcm_topic_subscribe($tokens, $topics, $i=0)
{
	if (defined('_FCM_SENDER_ID'))
	{
		global $sys;
		$log    = '';
		$url    = 'https://iid.googleapis.com/iid/v1:batchAdd';
		// $url    = 'https://iid.googleapis.com/iid/v1/%s/rel/topics/%s';
		$header = array(
			'CURLOPT_HTTPHEADER' => array(
				'Authorization: Bearer '.alert_fcm_token(),
				 'access_token_auth: true',
				 'Content-Type: application/json;'
				)
		);
		 // JIKA YANG ARRAY ADALAH TOPICS
		if (is_array($topics) && !empty($topics[$i]))
		{
			// https://php-fcm.readthedocs.io/en/latest/topic.html#subscribing-to-a-topic
			$post = array(
				'to'                  => '/topics/'.$topics[$i],
				'registration_tokens' => $tokens
				);
			$log = $sys->curl($url, json_encode($post), $header);
			$i++;
			// $log = $sys->curl(sprintf($url, $tokens, $topics[$i]), '{}', $header);
			// $i++;
			if (!empty($topics[$i]))
			{
				_class('async')->run(__FUNCTION__, [$tokens, $topics, $i]);
			}
		}else
		 // JIKA YANG ARRAY ADALAH TOKEN
		if (is_array($tokens) && !empty($tokens[$i]))
		{
			$post = array(
				'to'                  => '/topics/'.$topics,
				'registration_tokens' => []
				);
			for ($j=0; $j < 100; $j++)
			{
				if (!empty($tokens[$i]))
				{
					$post['registration_tokens'][] = $tokens[$i];
					$i++;
				}else{
					break;
				}
			}
			$log = $sys->curl($url, json_encode($post), $header);
			// $log = $sys->curl(sprintf($url, $tokens[$i], $topics), '{}', $header);
			// $i++;
			if (!empty($tokens[$i]))
			{
				_class('async')->run(__FUNCTION__, [$tokens, $topics, $i]);
			}
		}
		if (!empty($log))
		{
			// iLog([$log, $post, 'subscribe']);
		}
	}
}

function alert_fcm_topic_unsubscribe($tokens, $topics, $i=0)
{
	if (defined('_FCM_SENDER_ID'))
	{
		global $sys;
		$log    = '';
		$url    = 'https://iid.googleapis.com/iid/v1:batchRemove';
		$header = array(
			'CURLOPT_HTTPHEADER' => array(
				'Authorization: Bearer '.alert_fcm_token(),
				 'access_token_auth: true',
				 'Content-Type: application/json;'
				)
		);
		 // JIKA YANG ARRAY ADALAH TOPICS
		if (is_array($topics) && !empty($topics[$i]))
		{
			// https://php-fcm.readthedocs.io/en/latest/topic.html#unsubscribing-from-a-topic
			$post = array(
				'to'                  => '/topics/'.$topics[$i],
				'registration_tokens' => $tokens
				);
			$log = $sys->curl($url, json_encode($post), $header);
			$i++;
			if (!empty($topics[$i]))
			{
				_class('async')->run(__FUNCTION__, [$tokens, $topics, $i]);
			}
		}else
		 // JIKA YANG ARRAY ADALAH TOKEN
		if (is_array($tokens) && !empty($tokens[$i]))
		{
			$post = array(
				'to'                  => '/topics/'.$topics,
				'registration_tokens' => []
				);
			for ($j=0; $j < 100; $j++)
			{
				if (!empty($tokens[$i]))
				{
					$post['registration_tokens'][] = $tokens[$i];
					$i++;
				}else{
					break;
				}
			}
			$log = $sys->curl($url, json_encode($post), $header);
			if (!empty($tokens[$i]))
			{
				_class('async')->run(__FUNCTION__, [$tokens, $topics, $i]);
			}
		}
		if (!empty($log))
		{
			// iLog([$log, $post, 'unsubscribe']);
		}
	}
}

function alert_fcm_verify($limit=1000, $i=0)
{
	global $db, $sys;
	if ($limit > $i && defined('_FCM_SENDER_ID'))
	{
		$data = $db->getRow("SELECT * FROM `bbc_user_push` WHERE `type`=1 AND `updated` < '".date('Y-m-d H:i:s', strtotime('-3 MONTHS'))."' ORDER BY `updated` ASC LIMIT 1");
		if (!empty($data))
		{
			// https://developers.google.com/instance-id/reference/server#example_get_request
			$url    = 'https://iid.googleapis.com/iid/info/'.$data['token'];
			$header = array(
				'CURLOPT_HTTPHEADER' => array(
					'Authorization: Bearer '.alert_fcm_token(),
					'access_token_auth: true',
					'Content-Type: application/json;'
				)
			);
			$output = $sys->curl($url, '{}', $header);
			$output = json_decode($output, 1);
			if (empty($output['authorizedEntity']) && !empty($output['error']))
			{
				$db->Execute("DELETE FROM `bbc_user_push` WHERE `id`={$data['id']}");
			}else{
				$db->Update('bbc_user_push', ['updated' => date('Y-m-d H:i:s')], $data['id']);
			}
			$i++;
			_class('async')->run(__FUNCTION__, [$limit, $i]);
		}
	}
}