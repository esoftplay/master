<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function _func($file)
{
	if(!empty($file))
	{
		_ext($file);
		$path = str_replace('.php', '', $file);
		$files= array();
		if(file_exists(_ROOT.'modules/'.$path.'/_function.php'))
		{
			$f = $files[] = _ROOT.'modules/'.$path.'/_function.php';
		  include_once $f;
		}
		if(is_file(_FUNC.$file))
		{
			$f = $files[] = _FUNC.$file;
			include_once $f;
		}
		$j = func_num_args();
		if ($j > 1)
		{
			$func = $path.'_'.func_get_arg(1);
			if (function_exists($func))
			{
				if ($j > 2)
				{
					$param = array();
					for($i=2;$i < $j;$i++)
					{
						$param[] = func_get_arg($i);
					}
					return call_user_func_array($func, $param);
				}else{
					return $func();
				}
			}else{
				$msg = 'Function "'.$func.'" not found';
				if (!empty($files)) {
					$msg .= ' in '.implode(' and ', $files);
				}
				die($msg.' !');
			}
		}
	}
}
function _class($file)
{
	global $Bbc;
	if(!empty($file))
	{
		$class = preg_replace('~\.php~s', '', $file);
		if(isset($Bbc->$class) && $Bbc->$class != false && func_num_args()==1) return $Bbc->$class;
		_ext($file);
		$filename = '';
		if(file_exists(_ROOT.'modules/'.str_replace('.php', '/_class.php', $file)))
		{
		  $filename = _ROOT.'modules/'.str_replace('.php', '/_class.php', $file);
		  $class .= '_class';
		}else
		if(is_file(_CLASS.$file))
		{
		  $filename = _CLASS.$file;
		}
		if(!empty($filename))
		{
			include_once $filename;
			if (class_exists($class))
			{
				$j = func_num_args();
				if($j > 1)
				{
					$l = array();
					for($i=1;$i < $j;$i++)
					{
						$k = 'l'.$i;
						$$k = func_get_arg($i);
						$l[] = '$'.$k;
					}
					eval('$Bbc->'.$class.' = new '.$class.'('.implode(',', $l).');');
				}else{
					$Bbc->$class = new $class();
				}
			}else $Bbc->$class = false;
		}
	}
	return !empty($Bbc->$class) ? $Bbc->$class : false;
}
function _lib($file)
{
	global $Bbc;
	if(!empty($file))
	{
		$class = preg_replace('~\.php~s', '', $file);
		_ext($file);
		if(is_file(_LIB.$class.'/'.$file))
		{
			include_once _LIB.$class.'/'.$file;
		}
		if (class_exists($class))
		{
			$j = func_num_args();
			if($j > 1)
			{
				$l = array();
				for($i=1;$i < $j;$i++)
				{
					$k = 'l'.$i;
					$$k = func_get_arg($i);
					$l[] = '$'.$k;
				}
				eval('$Bbc->'.$class.' = new '.$class.'('.implode(',', $l).');');
			}else{
				$Bbc->$class = new $class();
			}
		}else $Bbc->$class = false;
	}
	return $Bbc->$class;
}
function _ext(&$file, $ext = '.php')
{
	if(substr($file, (strlen($ext)*-1)) != $ext) $file .= $ext;
}
function tpl($file, $default_file='')
{
	global $sys;
	$output = _SYS.'none.html.php';
	if (empty($file) && empty($default_file))
	{
		return $output;
	}
	$bt     = debug_backtrace();
	$path   = str_replace(_ROOT, '', dirname($bt[0]['file'])).'/';
	$path   = str_replace('templates/'.config('template').'/', '', $path);
	if (defined('_MST'))
	{
		$r = explode('|', _MST);
		foreach ($r as $p)
		{
			$p = trim($p);
			if (!empty($p))
			{
				$path = preg_replace('~^'.preg_quote($p, '~').'~s', '', $path);
			}
		}
	}
	if (!preg_match('~\.[a-z0-9]+$~is', $file))
	{
		$file .= '.html.php';
	}
	if (file_exists($sys->template_dir.$path.$file))
	{
		return $sys->template_dir.$path.$file;
	}else
	if (file_exists(_ROOT.$path.$file))
	{
		return _ROOT.$path.$file;
	}else
	if (!empty($default_file))
	{
		if (!preg_match('~\.[a-z0-9]+$~is', $default_file))
		{
			$default_file .= '.html.php';
		}
		if (file_exists($sys->template_dir.$path.$default_file))
		{
			return $sys->template_dir.$path.$default_file;
		}else
		if (file_exists(_ROOT.$path.$default_file))
		{
			return _ROOT.$path.$default_file;
		}
	}
	return $output;
}
function is_url($text)
{
	$regex		= '/^((?:ht|f)tps?:\/\/)(?:[a-z0-9\-]+\.[a-z0-9\-\.]+|localhost)\/?/is';
	$output = (preg_match($regex, (string)$text)) ? true : false;
	return $output;
}
function is_email($text)
{
	$regex	= '/^[a-z0-9\_\.]+@(?:[a-z0-9\-\.]){1,66}\.(?:[a-z]){2,6}$/is';
	$output = (preg_match($regex, (string)$text)) ? true : false;
	return $output;
}
function is_phone($text)
{
	$regex	= '/^\+?([0-9]+[\s\.\-]?(?:[0-9\s\.\-]+)?){5,}$/is';
	$output = (preg_match($regex, (string)$text)) ? true : false;
	return $output;
}
function get_account_id($code = 'none')
{
	global $db, $user;
	if(is_numeric($code) and $code > 0) $sql = "user_id=$code";
	elseif($code != 'none') $sql = "username='$code'";
	else $sql = "user_id=$user->id";
	$q = "SELECT id FROM bbc_account WHERE $sql";
	$output = intval($db->getOne($q));
	return $output;
}
function redirect($url='')
{
	global $sys, $Bbc;
	if (empty($url))
	{
		if (!empty($_GET['return']))
		{
			$url = $_GET['return'];
		}else
		if (!empty($_GET['_return']))
		{
			$url = $_GET['_return'];
		}else
		if (!empty($_SERVER['HTTP_REFERER']))
		{
			$url = $_SERVER['HTTP_REFERER'];
		}
	}
	if (!empty($url))
	{
		$url = (_ADMIN=='') ? site_url($url) : $url;
	}else{
		$url = _URL._ADMIN;
	}
	header('location:'.$url);
	die($url);
}
function repairExplode($data, $delimeter = ',')
{
	$arr = array();
	if (is_array($data))
	{
		$r = $data;
	}else{
		$r = explode($delimeter, $data);
	}
	foreach($r AS $value)
	{
		if(!empty($value) || $value == '0')
		{
			$arr[] = $value;
		}
	}
	$output = array_unique($arr);
	return $output;
}
function repairImplode($data, $delimeter = ',')
{
	$arr = array();
	foreach((array)$data AS $value)
	{
		if(!empty($value))
		{
			$arr[] = $value;
		}
	}
	$output = implode($delimeter, array_unique($arr));
	if(!empty($output))
	{
		$output = $delimeter.$output.$delimeter;
	}
	return $output;
}
function paramImplode($arr = array())
{
	$output = '';
	if(is_array($arr) and !empty($arr))
	{
		$r = array();
		foreach($arr AS $var => $val){
			if(is_array($val)){
				$val = urlencode(urlencode(paramImplode($val)));
			}else{
				$val = urlencode($val);
			}
			$r[] = $var.'='.$val;
		}
		$output = implode('&', $r);
	}
	return $output;
}
function paramExplode($txt = '')
{
	$output = array();
	if(!empty($txt))
	{
		$arr = explode('&', $txt);
		foreach($arr AS $data){
			$var = substr($data, 0, strpos($data, '='));
			$val = urldecode(substr(strchr($data, '='), 1));
			if(preg_match("/\=/", urldecode(str_replace('=', '', $val)))){
				$val = paramExplode(urldecode($val));
			}
			$output[$var] = $val;
		}
	}
	return $output;
}
function money($price = 0, $is_shorten= false)
{
	$output = $price;
	if ($is_shorten)
	{
	  $x = round($price);
	  $x_number_format = number_format($x);
	  $x_array = explode(',', $x_number_format);
	  $x_parts = array(lang('k'), lang('m'), lang('b'), lang('t'));
	  $x_count_parts = count($x_array) - 1;
	  $output = $x;
	  if (isset($x_array[0]))
	  {
		  $output = $x_array[0];
	  }
	  if (isset($x_array[1]))
	  {
		  $output .= ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
		  $output .= $x_parts[$x_count_parts - 1];
	  }
	}else{
		$output = number_format(floatval($price), 2, '.', ',');
		$output = preg_replace('~0+$~', '',  $output);
		$output = preg_replace('~\.$~', '',  $output);
	}
	return $output;
}
function items($qty, $singular = 'item', $particular = '', $translate = true)
{
	$i = abs(intval($qty));
	if($i > 1)
	{
		if(empty($particular))
		{
			$particular = $singular.'s';
		}
		$txt = $translate ? lang($particular) : $particular;
	}else{
		$txt = $translate ? lang($singular) : $singular;
	}
	return money($qty).' '.$txt;
}
function ids(&$ids, $out_array = false)
{
	if(empty($ids))
	{
		$ids = (!$out_array) ? '' : array();
		return false;
	}
	$ids = is_array($ids) ? $ids : array($ids);
	if(!$out_array) $ids = implode(',', $ids);
	return true;
}
function parseToArray($r_select, $r_all)
{
	$output = array();
	if(is_array($r_select))
		foreach($r_select AS $id => $dt)
			if($r_all[$id]!='')
				$output[] = $r_all[$id];
	return $output;
}
function fixArray($val = array())
{
	if(!empty($val)){
		$output = is_array($val) ? array_unique($val) : array($val);
	}else{
		$output = array(0);
	}
	return $output;
}
function fixValue($value = 0)
{
	if(!empty($value)){
		$r = repairExplode($value);
		$output = implode(',', $r);
	}else{
		$output = 0;
	}
	return $output;
}
function addslashes_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : addslashes($vars);
	return $vars;
}
function stripslashes_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : stripslashes($vars);
	return $vars;
}
function urlencode_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : urlencode($vars);
	return $vars;
}
function urldecode_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : urldecode($vars);
	return $vars;
}
function htmlentities_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : htmlentities($vars);
	return $vars;
}
function unhtmlentities($cadena)
{
	return html_entity_decode($cadena);
  $cadena = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $cadena);
  $cadena = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $cadena);
  $trans_tbl = get_html_translation_table(HTML_ENTITIES);
  $trans_tbl = array_flip($trans_tbl);
  return strtr($cadena, $trans_tbl);
}
function unhtmlentities_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : unhtmlentities($vars);
	return $vars;
}
function justify_text($txt, $txt2, $spaces, $add = ' ')
{
  $i = strlen($txt);
  $j = strlen($txt2);
  $out = $txt;
  $k = $spaces - $i - $j;
  if($k > 0)
  {
    $out .= str_repeat($add, $k);
  }else{

    $x = 4;
    $y = $spaces - $j - $x;
    $out = substr($txt, 0, $y);
    $out .= str_repeat('.', $x - 1).$add;
  }
  $out .= $txt2;
  return $out;
}