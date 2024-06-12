<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$r_tbl = $db->getCol("SHOW TABLES LIKE 'bbc_async%'");
if (empty($r_tbl))
{
	die();
}
$is_plan = 0;
if (in_array('bbc_async_plan', $r_tbl))
{
	$is_plan = 1;
	$r_data  = $db->getAll("SELECT `id`, `function`, `arguments` FROM `bbc_async_plan` WHERE `ontime` < NOW() ORDER BY `ontime` ASC LIMIT 100");
	if (!empty($r_data))
	{
		foreach ($r_data as $data)
		{
			$db->Execute("DELETE FROM `bbc_async_plan` WHERE `id`={$data['id']}");
			_class('async')->run(json_decode($data['function'], 1), json_decode(urldecode($data['arguments']), 1));
		}
	}
}
if (get_once('clean_bbc_async', 1, 'day'))
{
	$db->Execute("ALTER TABLE `bbc_async` AUTO_INCREMENT = 1");
	if ($is_plan)
	{
		$db->Execute("ALTER TABLE `bbc_async_plan` AUTO_INCREMENT = 1");
	}
}

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
	$threshold = strtotime('-2 minutes');
	$thresmax  = strtotime('-35 minutes');
	if ($last < $threshold)
	{
		if ($last < $thresmax)
		{
			$pending   = '?';
			$process   = '?';
			$worker    = '?';
			$notify = 'ada async yang umur lebih dari 35 menit';
		}else{
			_class('async')->fix($data['id']);
		}
		if ($notify)
		{
			_func('date');
			$url = _URL.'user/async?act=';
			$msg = '#'.@$_SERVER['HTTP_HOST'].' : '.$pending.' - '.$process.' - '.$worker
				."\nfunction: ".$data['function']
				."\ncreated: ".$data['created'].' ('.timespan(strtotime($data['created'])).')'
				."\ntotal: ".money($db->getOne("SELECT COUNT(1) FROM `bbc_async` WHERE 1"));
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
				$out = tm($msg, $chatID);
				// pr($out, $msg, __FILE__.':'.__LINE__);
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