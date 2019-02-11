<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*=====================================
$vars = array(
	'var1' => 'val1',
	'var2' => 'val2'
	);
 *===================================*/
function url_add($var, $val)
{
	$arr = array();
	foreach($_GET as $_var => $_val){
	  $value = ($var!=$_var) ? $_val : $val;
	  $arr[] = "$_var=$value";
	}
	if(!in_array("$var=$val", $arr )){
	  $arr[] = "$var=$val";
	}
	$output = $_SERVER['SCRIPT_NAME'];
	$output .= implode('&', $arr) ? '?'.implode('&', $arr) : '';
	return $output;
}
function url_add2($vars = array(), $index = '')
{
	$vars = is_array($vars) ? $vars : array($vars);
	$gets = array_keys($vars);
	$index= $index ? $index : $_SERVER['SCRIPT_NAME'];
	$out	= $index;
	$add	='';
	foreach($_GET AS $_var => $_val){
		$d		= ($out == $index) ? '?' : '&';
		$_val	= (in_array($_var, $gets)) ? $vars[$_var] : $_val;
		if(!empty($_val)){
			$out .= "$d$_var=$_val";
		}else{
			$add .= "$d$_var=";
		}
	}
	if(!empty($add)) $out .= $add;
	$output = site_url($out);
	return $output;
}
?>