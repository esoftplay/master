<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
UNTUK MEMPOSTING TOKEN PUSH DARI MOBILE APP (Method: POST)
ARGUMENTS:
$user_id   = [opsional]
$group_id  = [opsional] comma separated or Array
$username  = [opsional]
$token     = [wajib]
$push_id   = [optional] ID dari hasil return push-token sebelumnya untuk diupdate (biasanya krn update token krn tolen ada umurnya)
$old_id    = [optional] ID dari hasil return push-token sebelumnya untuk dihapus
$device    = [optional] nama atau jenis device yang digunakan untuk membuka app
$secretkey = _class('crypt')->encode(_SALT.'|'.date()'Y-m-d H:i:s');
*/
$output = array(
	'ok'      => 0,
	'message' => 'failed to save your data',
	'result'  => 0
	);
if (!empty($_POST['token']) && !empty($_POST['secretkey']))
{
	$token     = $_POST['token'];
	$user_id   = @intval($_POST['user_id']);
	$group_ids = @$_POST['group_id'];
	$push_id   = @intval($_POST['push_id']);
	$username  = @$_POST['username'];
	$device    = @$_POST['device'];
	$os        = @$_POST['os'];
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
				// iLog([$token, $user_id, $group_ids, $username, $device, $os, $push_id]);
				_func('alert');
				$out = alert_push_signup($token, $user_id, $group_ids, $username, $device, $os, $push_id);
				if($out)
				{
					if (!empty($_POST['old_id']))
					{
						$old_id = intval($_POST['old_id']);
						$db->Execute("DELETE FROM `bbc_user_push` WHERE `id`={$old_id}");
					}
					$output = array(
						'ok'      => 1,
						'message' => 'success',
						'result'  => $out
						);
				}else{
					// $output['message'] = 'tidak ada output di "alert_push_signup":'.pr($out, 1);
				}
			}else{
				// $output['message'] = 'tanggal kadaluarsa:'.$date;
			}
		}else{
			// $output['message'] = 'salt tidak sama: '.$secretkey;
		}
	}else{
		// $output['message'] = '$secretkey kosong';
	}
}else{
	// $output['result'] = $db->getAll("SELECT *, COUNT(1) AS tot FROM `bbc_user_push` WHERE 1 GROUP BY `token` having tot>1 ORDER BY tot DESC LIMIT 10");
	$output['result'] = [];
}
output_json($output);
/*
check database ada di /admin/index.php?mod=_cpanel.user&act=fcm-activate
di file: modules/_cpanel/admin/user/fcm-activate.php
*/