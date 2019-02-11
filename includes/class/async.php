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
	private $isExists = false;
	function __construct()
	{
		$this->isExists = class_exists('GearmanClient');
	}
	public function run($object, $params=array())
	{
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

			$client = new GearmanClient();
			$client->addServer();
			$result = $client->doBackground('esoftplay_async', json_encode(array(
				$_SERVER,
				_ROOT,
				_ADMIN,
				$object,
				$db->Insert_ID(),
				$params
				)));
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
				$client = new GearmanClient();
				$client->addServer();
				$result = $client->doBackground('esoftplay_async', json_encode(array(
					$_SERVER,
					_ROOT,
					_ADMIN,
					$object,
					$async_id,
					$params
					)));
			}else{
				$db->Execute("DELETE FROM `bbc_async` WHERE `id`=".$async_id);
				$db->Execute("ALTER TABLE `bbc_async` AUTO_INCREMENT=1");
				$this->run($object, $params);
			}
		}
	}
}
if (!defined('_VALID_BBC'))
{
	if (!empty($argv[1]))
	{
		$inputs = json_decode($argv[1], 1);
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
					if (is_array($_AsYnCtAsK['_OBJ']))
					{
						$obj = _class($_AsYnCtAsK['_OBJ'][0]);
						if ($obj)
						{
							if (method_exists($obj, $_AsYnCtAsK['_OBJ'][1]))
							{
								$_AsYnCtAsK['_OBJ'][0] = $obj;
								call_user_func_array($_AsYnCtAsK['_OBJ'], $_AsYnCtAsK['_VAR']);
								echo $Bbc->debug;
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
							echo $Bbc->debug;
						}
					}
					$db->Execute("DELETE FROM `bbc_async` WHERE `id`=".$_AsYnCtAsK['_ID']);
					$db->Execute("ALTER TABLE `bbc_async` AUTO_INCREMENT=1");
				}
			}
		}
	}
}
