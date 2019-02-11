<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
UNTUK MELIHAT DAFTAR NOTIFIKASI BERDASARKAN USER_ID MAUPUN GLOBAL (Method: POST)
ARGUMENTS:
$user_id   = [opsional]
$last_id   = [opsional] ID notifikasi yang paling terakhir diambil (Method: GET)
$secretkey = _class('crypt')->encode(_SALT.'|'.date()'Y-m-d H:i:s');
*/

$output = array(
	'ok'      => 0,
	'message' => 'no available notifications',
	'result'  => []
	);
if (!empty($_POST['secretkey']))
{
	$user_id   = @intval($_POST['user_id']);
	$last_id   = @intval($_GET['last_id']);
	$secretkey = _class('crypt')->decode($_POST['secretkey']);
	if (!empty($secretkey))
	{
		list($salt, $date) = explode('|', $secretkey);
		$time  = time()+60;
		$stamp = strtotime($date);
		if (_SALT == $salt)
		{
			if ($time > $stamp)
			{
				if (!empty($user_id))
				{
					$user_id .= ',0';
				}
				$data = $db->getAll("SELECT * FROM `bbc_user_push_notif` WHERE `user_id` IN ({$user_id}) AND `id`>{$last_id} ORDER BY `id` ASC LIMIT 6");
				$next = '';
				if (!empty($data))
				{
					$dt = end($data);
					$is = $db->getOne("SELECT 1 FROM `bbc_user_push_notif` WHERE `user_id` IN ({$user_id}) AND `id`>{$dt['id']} ORDER BY `id`");
					if (!empty($is))
					{
						$next = _URL.'user/push-notif?last_id='.$dt['id'];
					}
				}
				$output = array(
					'ok'      => 1,
					'message' => 'success',
					'result'  => array(
						'list' => $data,
						'next' => $next
						)
					);
			}else{
				// $output['message'] = 'tanggal kadaluarsa:'.$date;
			}
		}else{
			// $output['message'] = 'salt tidak sama: '.$secretkey;
		}
	}else{
		// $output['message'] = '$secretkey kosong';
	}
}
output_json($output);