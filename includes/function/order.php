<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function order_delete($order_ids = array())
{
	if(empty($order_ids)) return false;
	$ids = is_array($order_ids) ? $order_ids : array($order_ids);
	if(count($ids) > 0)
	{
		global $db;
		$_func = array('pre' => array(), 'post' => array());
		$q = "SELECT name, order_func_pre AS pre, order_func_post AS post
					FROM bbc_module WHERE order_func_pre != '' || order_func_post != ''";
		$r = $db->getAll($q);
		foreach($r AS $dt){
			if(!empty($dt['pre']))	$_func['pre'][$dt['name']]	= $dt['pre'];
			if(!empty($dt['post']))	$_func['post'][$dt['name']]	= $dt['post'];
		}
		foreach((array)$_func['pre'] AS $module => $func)
		{
			if(is_file(_ROOT.'modules/'.$module.'/_function.php'))
			{
				include_once _ROOT.'modules/'.$module.'/_function.php';
				if(function_exists($func))
				{
					call_user_func($func, $ids);
				}
			}
		}
		$q="DELETE FROM `bbc_order` WHERE `id` IN(".implode(',', $ids).")";
		$db->Execute($q);
		$q="DELETE FROM `bbc_billing` WHERE `order_id` IN(".implode(',', $ids).")";
		$db->Execute($q);
		foreach((array)$_func['post'] AS $module => $func)
		{
			if(is_file(_ROOT.'modules/'.$module.'/_function.php'))
			{
				include_once _ROOT.'modules/'.$module.'/_function.php';
				if(function_exists($func))
				{
					call_user_func($func, $ids);
				}
			}
		}
		return true;
	}
	return false;
}
function order_code($price = 0)
{
	$output = 0;
	return $output;
}