<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$fileasync   = '/opt/async/bin/manager.php';
$filecheck   = _CACHE.'async.cfg';
$fileexecute = _CACHE.'async-execute.cfg';
$filefailed  = _CACHE.'async-failed.cfg';
$num_worker  = 5; // check file /opt/async/bin/manager.php for $config['worker_num']
$notify      = '';
$data        = $db->getRow("SELECT * FROM `bbc_async` WHERE 1 ORDER BY id ASC LIMIT 1");
if (!empty($data))
{
	$checknow  = $data['id'].'-'.$data['function'];
	$checklast = file_read($filecheck);

	$last      = strtotime($data['created']);
	$threshold = strtotime('-5 minutes');
	$thresmax  = strtotime('-35 minutes');
	if ($last < $threshold)
	{
		$async     = _class('async');
		if ($last < $thresmax)
		{
			$notify = 'ada async yang umur lebih dari 35 menit';
			// $async->restart($notify);
		}else{
			if (preg_match('~worker_num.*?([0-9]+)~is', file_read($fileasync), $match))
			{
				$num_worker = intval($match[1]);
			}
			$pending   = 0;
			$process   = 0;
			$worker    = 0;
			$gearadmin = shell_exec('gearadmin --status');
			if (!empty($gearadmin) && preg_match('~esoftplay_async\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)~is', $gearadmin, $status))
			{
				if (file_exists($filefailed))
				{
					@unlink($filefailed);
				}
				$pending = @intval($status[1]);
				$process = @intval($status[2]);
				$worker  = @intval($status[3]);
				/* JIKA JUMLAH WORKER DIBAWAH DARI CONFIG MAKA RESTART ASYNC SAJA */
				if ($worker < $num_worker)
				{
					$async->restart('karena jumlah worker hanya: '.money($worker).' yang seharusnya '.money($num_worker));
					user_async_cron_clean();
				}
				/* JIKA JUMLAH WORKER MASIH UTUH MAKA EKSEKUSI SAJA TASK PERTAMA SEJUMLAH WORKER AKTIF */
				if ($worker >= $num_worker)
				{
					$r_stuck = $db->getAll("SELECT id, function FROM `bbc_async` WHERE `created` < '".date('Y-m-d H:i:s', $threshold)."' ORDER BY id ASC LIMIT 0, {$worker}");
					$total   = count($r_stuck);
					/* TAPI JIKA YANG DIEKSEKUSI SEBELUMNYA MSH BELUM HILANG MAKA NOTIF DEVELOPER */
					if ($checknow == $checklast)
					{
						$notify = 'fix sebelumnya masih belum selesai';
					}else{
						/* EKSEKUSI ASYNC YANG MACET LALU SIMPAN APA AJA YANG SUDAH DIEKSEKUSI  */
						$arr_execute = @json_decode(file_read($fileexecute), 1);
						$on_execute  = array();
						$no_execute  = array(); // Hitung jumlah yang sudah dieksekusi sebelumnya tapi msh belum selesai
						if (!is_array($arr_execute))
						{
							$arr_execute = array();
						}
						foreach ($r_stuck as $dt)
						{
							if (!in_array($dt['id'], $arr_execute))
							{
								$async->fix($dt['id']);
								$on_execute[] = $dt['id'];
							}else{
								$no_execute[] = $dt['id'];
							}
						}
						file_write($fileexecute, json_encode($on_execute));
						file_write($filecheck, $data['id'].'-'.$data['function']);
						/* JIKA YANG DIEKSEKUSI SEBELUMNYA MSH BELUM SELESAI SAMPAI LEBIH DARI 5 MAKA NOTIF DEVELOPER */
						if (count($no_execute) >= 5)
						{
							$notify = 'ada yang stuck sejumlah '.money($i).":\n".json_encode($no_execute);
						}
					}
				} // jika worker masih dibawah config bisa jd msh dalam proses restart, jd tdk perlu notify
			}else{
				if (!file_exists($filefailed))
				{
					file_write($filefailed, strtotime('now'));
				}else{
					$lastfailed  = file_read($filefailed);
					$limitfailed = strtotime('-30 MINUTES');
					if ($lastfailed < $limitfailed)
					{
						$async->restart('karena gagal check lebih dari 30menit');
						unlink($filefailed);
						$notify = 'check async gagal selama 30menit akan direstart';
					}
				}
			}
		}
		if ($notify)
		{
			_func('date');
			$url = str_replace(['://', '/', '?', '=', '&'], ['_sc_s_s_', '_slash_', '_questionmark_', '_equalto_', '_andthe_'], _URL.'user/async?act=');
			$msg = '#'.@$_SERVER['HTTP_HOST']."\n".' : '.$pending.' - '.$process.' - '.$worker
				."\nfunction: ".$data['function']
				."\ncreated: ".$data['created'].' ('.timespan(strtotime($data['created'])).')'
				."\ntotal: ".money($db->getOne("SELECT COUNT(*) FROM `bbc_async` WHERE 1"));
			$msg = array(
				'text'         => $msg."\n".$notify,
				'reply_markup' => json_encode([
							'inline_keyboard' => [
								[
									['text' => 'Async', 'callback_data' => $url.'async'],
									['text' => 'Gearman', 'callback_data' => $url.'status']
								],
								[
									['text' => 'Restart', 'callback_data' => $url.'restart'],
									['text' => 'Execute', 'url' => _URL.'user/async']
								]
							],
							'resize_keyboard' => true,
							'selective'       => true
						])
				);
			if (function_exists('tm'))
			{
				$chatID = defined('_ASYNC_CHAT') ? _ASYNC_CHAT : -345399808;
				tm($msg, $chatID);
			}
		}
	}else{
		user_async_cron_clean();
	}
}else{
	user_async_cron_clean();
}

function user_async_cron_clean()
{
	global $filecheck, $fileexecute;
	if (file_exists($filecheck))
	{
		@unlink($filecheck);
	}
	if (file_exists($fileexecute))
	{
		@unlink($fileexecute);
	}
}