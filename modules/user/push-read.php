<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
UNTUK MENANDAI BAHWA NOTIFIKASI TELAH BERHASIL DIBACA (Method: POST)
ARGUMENTS:
$notif_id  = [wajib] field ID dari table 'bbc_user_push_notif' || multiIDs sprti 2,3,5,6,12
$secretkey = _class('crypt')->encode(_SALT.'|'.date()'Y-m-d H:i:s');
*/

$output = array(
	'ok'      => 0,
	'message' => 'failed to save your data',
	'result'  => 0
	);
if (!empty($_POST['notif_id']) && !empty($_POST['secretkey']))
{
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
				$ok       = false;
				$notif_id = $_POST['notif_id'];
				if (is_numeric($notif_id))
				{
					$ok = $db->Update('bbc_user_push_notif', array('status' => 2), $notif_id);
				}else
				if (strpos($notif_id, ',') !== FALSE)
				{
					$ok = $db->Execute('UPDATE `bbc_user_push_notif` SET `status`=2 WHERE `id` IN ('.trim(preg_replace(['~[^0-9\,]+~s', '~\,{2,}~s'], ['', ','], $notif_id), ',').')');
				}
				if ($ok)
				{
					$output = array(
						'ok'      => 1,
						'message' => 'success',
						'result'  => $ok
						);
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