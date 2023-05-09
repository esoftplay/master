<?php
/**
* Contoh Eksekusi Function
* _class('async')->run('function_name', [$input1, $input2...]);
* Contoh Eksekusi Class
* _class('async')->run(array('class_name', 'method_name'), [$input1, $input2...]);
*
* Cara instalasi:
* -- centos (Root) -> # curl -s fisip.net/fw/gearman|php|sh
*
* NB: Semua function yang bisa di panggil secara background hanya function dengan parameter berupa string, array, numeric dll tidak bisa memproses input parameter berupa object seperti $Bbc, $sys, $db dsb.
*/
if (!class_exists('async'))
{
	class async
	{
		private $tasks;
		private $task_ids;
		private $isExists = false;
		private $host;
		private $port;
		function __construct()
		{
			$this->isExists = file_exists('/opt/async.log');
			$this->tasks    = 0;
			$this->task_ids = array();
			if ($this->isExists)
			{
				$this->host = defined('_ASYNC_HOST') ? _ASYNC_HOST : '127.0.0.1';
				$this->port = defined('_ASYNC_PORT') ? _ASYNC_PORT : 4730;
			}
		}
		function __destruct()
		{
			if ($this->tasks > 0 && count($this->task_ids) > 0)
			{
				$task = [];
				foreach ($this->task_ids as $dt)
				{
					$task[] = bin2hex(json_encode(array(
									$_SERVER,
									_ROOT,
									_ADMIN,
									$dt[0],	# $object
									$dt[1]	# $insert_ID
									)));
				}
				file_put_contents('/opt/async.log', implode("\n", $task)."\n", FILE_APPEND);
			}
		}
		public function run($object, $params=array())
		{
			global $db;
			if (!is_array($params))
			{
				$params = array($params);
			}
			if ($this->isExists)
			{
				global $db;
				$exist = $db->getOne("SHOW TABLES LIKE 'bbc_async'");
				if (empty($exist))
				{
					$db->Execute("CREATE TABLE IF NOT EXISTS `bbc_async` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `function` varchar(255) DEFAULT '', `arguments` text, `created` datetime DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
				}
				$db->Execute("INSERT INTO `bbc_async` SET `function`='".json_encode($object)."', `arguments`='".urlencode(json_encode($params))."', `created`=NOW()");
				$this->task_ids[] = array($object, $db->Insert_ID());
				$this->tasks++;
			}else{
				if (is_array($object))
				{
					$obj = _class($object[0]);
					if ($obj)
					{
						if (method_exists($obj, $object[1]))
						{
							$object[0] = $obj;
							call_user_func_array($object, $params);
						}else{
							die('Maaf, method "'.$object[1].'" tidak ditemukan (Pesan ini muncul karena server belum mensupport asynchronous)');
						}
					}else{
						die('Maaf, class "'.$object[0].'" tidak ditemukan (Pesan ini muncul karena server belum mensupport asynchronous)');
					}
				}else{
					if (function_exists($object))
					{
						call_user_func_array($object, $params);
					}else{
						die('Maaf, function "'.$object.'" tidak ditemukan (Pesan ini muncul karena server belum mensupport asynchronous)');
					}
				}
			}
		}
		public function fix($async_id)
		{
			global $db;
			$sync   = $db->getRow("SELECT * FROM `bbc_async` WHERE `id`={$async_id} LIMIT 1");
			if (!empty($sync))
			{
				$object = json_decode($sync['function'], 1);
				$params = json_decode(urldecode($sync['arguments']), 1);
				if ($this->isExists)
				{
					$this->task_ids[] = array($object, $async_id);
					$this->tasks++;
				}else{
					$db->Execute("DELETE FROM `bbc_async` WHERE `id`=".$async_id);
					$db->Execute("ALTER TABLE `bbc_async` AUTO_INCREMENT=1");
					$this->run($object, $params);
				}
			}
		}
		public function restart($txt = '')
		{
			$act_file = '/tmp/tmp.sh';
			$tmp_file = '/tmp/async-tmp.txt';
			if (file_exists($tmp_file))
			{
				return false;
			}else{
				$data = "\n".'/etc/init.d/esoftplay_async restart';
				if (file_exists('/usr/local/bin/tm'))
				{
					$data .= "\n".'/bin/rm -f /tmp/async-tmp.txt'
					."\n".'/usr/local/bin/tm "restart async '.$txt.' di '.$_SERVER['HTTP_HOST'].' sudah selesai" -345399808';
					file_write($act_file, $data, 'a');
					file_write($tmp_file, date('r'));
				}else{
					if (function_exists('shell_exec'))
					{
						@shell_exec($data.' 2>&1 > /tmp/async.log');
					}
				}
			}
			$out = true;
			return $out;
		}
		public function status()
		{
			$exec = '/tmp/async.socket';
			if (file_exists($exec))
			{
				if (function_exists('shell_exec'))
				{
					$out = shell_exec('echo -n "ACTIVE async: $(wc -l < /opt/async.log) - $(wc -l < /tmp/async.log)"');
				}else{
					$out = 'exec is not available';
				}
			}else{
				$out = 'not supported';
			}
			return $out;
		}
	}
}
if (!defined('_VALID_BBC'))
{
	if (!empty($argv[1]))
	{
		$inputs = json_decode(hex2bin($argv[1]), 1);
		if (!empty($inputs))
		{
			define('_AsYnCtAsK', count($inputs));
			if (_AsYnCtAsK >= 4)
			{
				define('_VALID_BBC', 1);
				$_SERVER    = $inputs[0];
				$_AsYnCtAsK = array(
					'_ROOT'  => $inputs[1],
					'_ADMIN' => $inputs[2],
					'_OBJ'   => $inputs[3],
					'_ID'    => $inputs[4],
					'_VAR'   => ''
					);
				define('_ADMIN', $_AsYnCtAsK['_ADMIN']);
				define('bbcAuth', !empty($_AsYnCtAsK['_ADMIN']) ? 'bbcAuthAdmin' : 'bbcAuthUser');

				if (file_exists($_AsYnCtAsK['_ROOT'].'config.php'))
				{
					global $Bbc, $sys, $db, $user, $_CONFIG, $_LANG;
					$Bbc = new stdClass();
					$Bbc->no_log = 1;
					require_once $_AsYnCtAsK['_ROOT'].'config.php';
					include_once _ROOT.'includes/includes.php';
					$arguments = $db->getOne("SELECT `arguments` FROM `bbc_async` WHERE `id`=".$_AsYnCtAsK['_ID']);
					$_AsYnCtAsK['_VAR'] = json_decode(urldecode($arguments), 1);
					try {
						if (is_array($_AsYnCtAsK['_OBJ']))
						{
							$obj = _class($_AsYnCtAsK['_OBJ'][0]);
							if ($obj)
							{
								if (method_exists($obj, $_AsYnCtAsK['_OBJ'][1]))
								{
									$_AsYnCtAsK['_OBJ'][0] = $obj;
									call_user_func_array($_AsYnCtAsK['_OBJ'], $_AsYnCtAsK['_VAR']);
								}
							}
						}else{
							if (!function_exists($_AsYnCtAsK['_OBJ']))
							{
								$r = explode('_', $_AsYnCtAsK['_OBJ']);
								$O = '';
								foreach ($r as $o)
								{
									if (!empty($O))
									{
										$O .= '_';
									}
									$O .= $o;
									_func($O);
									if (function_exists($_AsYnCtAsK['_OBJ']))
									{
										break;
									}
								}
							}
							if (function_exists($_AsYnCtAsK['_OBJ']))
							{
								call_user_func_array($_AsYnCtAsK['_OBJ'], $_AsYnCtAsK['_VAR']);
							}
						}
						// echo $Bbc->debug;
						$db->Execute("DELETE FROM `bbc_async` WHERE `id`=".$_AsYnCtAsK['_ID']);
						$db->Execute("ALTER TABLE `bbc_async` AUTO_INCREMENT=1");

						$Bbc     = null;
						$sys     = null;
						$db      = null;
						$user    = null;
						$block   = null;
						$_CONFIG = null;
						$_LANG   = null;
						unset($Bbc, $sys, $db, $user, $block, $_CONFIG, $_LANG);
						$vars = array_keys(get_defined_vars());
						foreach ($vars as $var)
						{
							$$var = null;
							unset($$var);
						}
						unset($vars,$var);
						gc_collect_cycles();
						die();
					} catch (Exception $e) {
						die('Async: '.  $e->getMessage());
					}
				}
			}
		}
	}
}
