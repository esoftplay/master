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
class async
{
	private $tasks;
	private $task_ids;
	private $isExists = false;
	private $host;
	private $port;
	function __construct()
	{
		$this->isExists = class_exists('GearmanClient');
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
			$client = new GearmanClient();
			try {
				$client->addServer($this->host, $this->port);
				foreach ($this->task_ids as $dt)
				{
					try {
						$result = $client->addTaskBackground('esoftplay_async', json_encode(array(
							$_SERVER,
							_ROOT,
							_ADMIN,
							$dt[0],	# $object
							$dt[1],	# $insert_ID
							$dt[2]	# $params
							)));
					} catch (Exception $e) {
						$log = 'Async::'.json_encode($object).' '.  $e->getMessage();
						if (function_exists('iLog'))
						{
							iLog($log);
						}
					}
				}
				$client->runTasks();
			} catch (Exception $e) {
				$this->restart();
			}
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
			$db->Execute("INSERT INTO `bbc_async` SET `function`='".json_encode($object)."', `arguments`='".json_encode($params)."', `created`=NOW()");
			$this->task_ids[] = array($object, $db->Insert_ID(), $params);
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
					die('Maaf, function "'.$object[1].'" tidak ditemukan (Pesan ini muncul karena server belum mensupport asynchronous)');
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
			$params = json_decode($sync['arguments'], 1);
			if ($this->isExists)
			{
				$this->task_ids[] = array($object, $async_id, $params);
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
			$data = "\n".'/etc/init.d/esoftplay_async restart'
			."\n".'/usr/local/bin/tm "restart async '.$txt.' di '.$_SERVER['HTTP_HOST'].' sudah selesai" -345399808'
			."\n".'/bin/rm -f /tmp/async-tmp.txt';
			file_write($act_file, $data, 'a');
			file_write($tmp_file, date('r'));
		}
		$out = true;
		return $out;
	}
	public function status()
	{
		$status = array();
		try {
			$handle = fsockopen($this->host, $this->port, $errorNumber, $errorString, 30);
			if($handle!=null)
			{
				fwrite($handle,"status\n");
				while (!feof($handle))
				{
					$line = fgets($handle, 4096);
					if( $line==".\n")
					{
						break;
					}
					if (empty($status['operations']))
					{
						$status['operations'] = array();
					}
					if( preg_match("~^(.*)[ \t](\d+)[ \t](\d+)[ \t](\d+)~",$line,$matches) )
					{
						$function = $matches[1];
						$status['operations'][$function] = array(
							'function'         => $function,
							'total'            => $matches[2],
							'running'          => $matches[3],
							'connectedWorkers' => $matches[4],
						);
					}
				}
				fclose($handle);
			}
		} catch (Exception $e) {
			// print_r($e);
		}
		return $status;
	}
}
if (!defined('_VALID_BBC'))
{
	if (!empty($argv[1]))
	{
		$inputs = json_decode(str_replace('&#39;', "'", $argv[1]), 1);
		if (!empty($inputs))
		{
			define('_AsYnCtAsK', count($inputs));
			if (_AsYnCtAsK > 5)
			{
				define('_VALID_BBC', 1);
				$_SERVER    = $inputs[0];
				$_AsYnCtAsK = array(
					'_ROOT'  => $inputs[1],
					'_ADMIN' => $inputs[2],
					'_OBJ'   => $inputs[3],
					'_ID'    => $inputs[4],
					'_VAR'   => $inputs[5]
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
