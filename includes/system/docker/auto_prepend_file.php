<?php

date_default_timezone_set('Asia/Jakarta');
function pr($text='', $return = false)
{
	$is_multiple = (func_num_args() > 2) ? true : false;
	if(!$is_multiple)
	{
		if(is_numeric($return))
		{
			if($return==1 || $return==0)
			{
				$return = $return ? true : false;
			}else $is_multiple = true;
		}
		if(!is_bool($return)) $is_multiple = true;
	}
	if($is_multiple)
	{
		echo "<pre>\n";
		echo "<b>1 : </b>";
		print_r($text);
		$i = func_num_args();
		if($i > 1)
		{
			$j = array();
			$k = 1;
			for($l=1;$l < $i;$l++)
			{
				$k++;
				echo "\n<b>$k : </b>";
				print_r(func_get_arg($l));
			}
		}
		echo "\n</pre>";
	}else{
		if($return)
		{
			ob_start();
		}
		echo "<pre>\n";
		print_r($text);
		echo "\n</pre>";
		if($return)
		{
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	}
}
function iLog($text, $is_print = false, $is_html = true)
{
	$debug = debug_backtrace();
	$file  = $debug[0]['file'];
	$line  = $debug[0]['line'];
	if (defined('_MST'))
	{
		$r = explode('|', _MST);
		foreach ($r as $p)
		{
			$p = trim($p);
			if (!empty($p))
			{
				$file = preg_replace('~^'.preg_quote($p, '~').'~s', '', $file);
			}
		}
		$file = preg_replace('~^'.preg_quote(_ROOT, '~').'~s', '', $file);
	}
	$f = !empty($debug[0]) ? $file.':'.$line : '';
	if ($is_print)
	{
		if ($is_html)
		{
			pr($text, $f);
		}else{
			print_r($text);
			echo "\n".$f."\n";
		}
	}else{
		file_put_contents('/tmp/esoftplay-log.txt', date('Y-m-d H:i:s: ').$f."\n".print_r($text, 1)."\n\n", FILE_APPEND);
	}
}
/*
https://core.telegram.org/bots/api#available-methods
Example:
tm('pesan text');
tm('pesan text', 'danang');
tm(['message_id'=>83724, 'text'=>'ganti pesan'], 'danang', 'editMessageText');
tm(['chat_id'=>88096365, 'message_id'=>83724], 'deleteMessage');
*/
function tm($msg, $no = -1001681033483, $method = 'sendMessage', $token='')
{
	if (empty($msg) || empty($token))
	{
		return false;
	}
	global $db;
	if (!empty($db->bg_check))
	{
		$db->add_bg('tm', func_get_args());
		return true;
	}
	$token  = !empty($token) ? $token : $tkn;
	$init   = curl_init( 'https://api.telegram.org/bot'.$token.'/'.$method);
	$output = array();
	$post   = array();
	$tm_id  = is_array($no) ? $no : array($no);
	foreach ($tm_id as $id)
	{
		if (is_numeric($id))
		{
			if (is_array($msg))
			{
				$post = array_merge($post, $msg);
				if (empty($post['chat_id']))
				{
					$post['chat_id'] = $id;
				}
			}else{
				$post   = array(
					'chat_id'    => $id,
					'text'       => $msg,
					'parse_mode' => 'markdown'
					);
			}
			curl_setopt($init, CURLOPT_POST, 1);
		  curl_setopt($init, CURLOPT_POSTFIELDS, $post);
		  curl_setopt($init, CURLOPT_FOLLOWLOCATION, 0);
		  curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($init, CURLOPT_SSL_VERIFYPEER, 0);
		  curl_setopt($init, CURLOPT_SSL_VERIFYHOST , 0);
		  $output[] = curl_exec($init);
		  /*
		  SAMPLE OUTPUT:
			{
			    "ok": true,
			    "result": {
			        "message_id": 1178,
			        "from": {
			            "id": 132809481,
			            "first_name": "Fisip Net",
			            "username": "fisip_bot"
			        },
			        "chat": {
			            "id": 88096365,
			            "first_name": "Danang",
			            "last_name": "Widiantoro",
			            "username": "bbc_danang",
			            "type": "private"
			        },
			        "date": 1468733790,
			        "text": "2016-07-17 12:36:27"
			    }
			}
		  */
		}
	}
	curl_close($init);
  return $output;
}
