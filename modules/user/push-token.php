<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
UNTUK MEMPOSTING TOKEN PUSH DARI MOBILE APP (Method: POST)
ARGUMENTS:
$user_id   = [opsional]
$username  = [opsional]
$token     = [wajib]
$old_id    = [optional] ID dari hasil return push-token sebelumnya untuk dihapus pada system
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
	$push_id   = @intval($_POST['push_id']);
	$username  = @$_POST['username'];
	$device    = @$_POST['device'];
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
				_func('alert');
				$out = alert_push_signup($token, $user_id, $username, $device, $push_id);
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
}
output_json($output);