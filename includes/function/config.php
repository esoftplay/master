<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function config($index = '')
{
	global $_CONFIG;
	$output = '';
	if(!empty($index))
	{
		get_config();
		$j = func_num_args();
		$o = $_CONFIG;
		for($i=0;$i < $j;$i++)
		{
			$k = func_get_arg($i);
			if(isset($o[$k]) && is_array($o))
			{
				$o = $o[$k];
			}else{
				$o = '';
				break;
			}
		}
		if ($j > 0)
		{
			$output = $o;
		}
	}else{
		$output = $_CONFIG;
	}
	return $output;
}

function get_config($module_id = 'none', $index = '')
{
	global $db, $_CONFIG, $sys, $Bbc;
	$_CONFIG = is_array($_CONFIG) ? $_CONFIG : array();
	if(is_numeric($module_id))
	{
		$module_id = $module_id;
	} else
	if($module_id != 'none')
	{
		if(!empty($sys))
		{
			$module_id = $sys->get_module_id($module_id);
		}else{
			$r = $db->cacheGetAssoc("SELECT name, id FROM bbc_module WHERE active=1");
			if(!empty($r[$module_id]))
			{
				$module_id = $r[$module_id];
			}else{
				$module_id = 0;
			}
		}
	}else{
		$module_id = !empty($sys->module_id) ? $sys->module_id : 0;
	}
	$module_id = intval($module_id);
	$output    = array();
	$Bbc->get_config_idx = !empty($Bbc->get_config_idx) ? $Bbc->get_config_idx : array();
	if(!in_array($module_id, $Bbc->get_config_idx))
	{
		$q = "SELECT * FROM bbc_config WHERE module_id=".$module_id;
		$r = $db->cache('getAll', $q, 'config/'.$module_id.'.cfg');
		if (empty($r))
		{
			@unlink(_CACHE.'config/'.$module_id.'.cfg');
		}
		foreach((array) $r AS $dt)
		{
			$output[$dt['name']] = config_decode($dt['params']);
		}
		$_CONFIG = array_merge($_CONFIG, $output);
		$Bbc->get_config_idx[] = $module_id;
	}
  $output = $_CONFIG;
	if(!empty($index))
	{
		$j = func_num_args();
		if($j > 1)
		{
		  $output = $_CONFIG;
      for($i=1;$i < $j;$i++)
      {
        $k = func_get_arg($i);
        if(isset($output[$k]) && is_array($output))
        {
          $output = $output[$k];
        }else{
        	$output = '';
        	break;
        }
      }
		}
	}
	return $output;
}

function config_name($name, $module_id = 'none')
{
	global $db, $_CONFIG, $sys, $Bbc;
	if(is_numeric($module_id))
		$module_id = $module_id;
	else
	if($module_id != 'none')
	{
		$q = "SELECT id FROM bbc_module WHERE name='$module_id'";
		$module_id = $db->getOne($q);
	}else $module_id = @$sys->module_id;
	$module_id = intval($module_id);

	$Bbc->get_config_idx = !empty($Bbc->get_config_idx) ? $Bbc->get_config_idx : array();
	if(!in_array($module_id, $Bbc->get_config_idx))
	{
		$q = "SELECT * FROM bbc_config WHERE module_id=".$module_id;
		$r = $db->cache('getAll', $q, 'config/'.$module_id.'.cfg');
		foreach((array) $r AS $dt)
		{
			$output[$dt['name']] = @config_decode($dt['params']);
		}
		$_CONFIG = array_merge($_CONFIG, (array)$output);
		$Bbc->get_config_idx[] = $module_id;
	}
	if(isset($_CONFIG[$name]))
	{
		return $_CONFIG[$name];
	}else{
		return array();
	}
}

function set_config($name, $params, $module_id = '')
{
	global $db, $sys;
	$module_id = is_numeric($module_id) ? $module_id : (!empty($module_id) ? $sys->get_module_id($module_id) : $sys->module_id);
	$param = config_encode($params);
	$q = "UPDATE bbc_config SET params='{$param}' WHERE name='{$name}' AND module_id=".$module_id;
	$output = $db->Execute($q);
	$db->cache_clean('config/');
	return $output;
}

function config_decode($string)
{
	$out = urldecode_r(json_decode($string, 1));
	if (empty($out))
	{
		$out = array();
	}
	return $out;
}
function config_encode($array)
{
	return json_encode(urlencode_r($array));
}