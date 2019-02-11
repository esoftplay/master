<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*=====================================================
 * $data[]	= array(
 			'id'			=> $id
 		, 'par_id'	=> $par_id
 		, 'title'		=> $title);
 *====================================================*/
function array_path($data, $par_id = 0, $separate = ' / ', $prefix = '', $load_parent = '')
{
	$output = array();
	foreach((array)$data AS $dt)
	{
		if($dt['par_id'] == $par_id)
		{
			if(empty($load_parent))
			{
				$text = ($par_id==0) ? $prefix.$dt['title'] : $prefix.$separate.$dt['title'];
				$output[$dt['id']] = $text;
			}else{
				$output[$dt['id']] = ($par_id==0) ? $prefix.$dt['title'] : $prefix.$separate.$dt['title'];
				$text	= ($par_id==0) ? $prefix.$load_parent : $prefix.$separate.$load_parent;
			}
			$r = array_path($data, $dt['id'], $separate, $text, $load_parent);
			if(!empty($r)) {
				foreach($r AS $i => $j)
					$output[$i] = $j;
			}
		}
	}
	return $output;
}
/*=====================================================
 * $data[]	= array(
 			'id'			=> $id
 		, 'par_id'	=> $par_id
 		, 'title'		=> $title
 		, 'cat_name'=> $cat_name);
 *====================================================*/
function array_option($data, $par_id = 0, $lastCatID = '', $delimeter = ' | ',  $prefix = '')
{
	$output = array();
	$prefix = $prefix ? trim($prefix).$delimeter : '';
	foreach($data AS $dt)
	{
		if($dt['par_id'] == $par_id)
		{
			if($lastCatID != $dt['cat_name'])
				$output[]	= array('', '--------------------------------------------');
			$lastCatID	= $dt['cat_name'];
			$output[]		= array($dt['id'], $lastCatID.$prefix.$dt['title']);
			$r = array_option($data, $dt['id'], $lastCatID, $delimeter,  $prefix.$dt['title']);
			if(count($r) > 0)
			{
				foreach($r AS $j)
					$output[] = $j;
			}
		}
	}
	return $output;
}

function array_json($arr = array())
{
	if(empty($arr)) return false;
	$output = array();
	foreach((array)$arr AS $key => $value)
	{
		$out = '"'.preg_replace('~[^a-z0-9\-]~i', '_', $key).'":';
		if(is_array($value) && !empty($value)) $out .= array_json($value);
		elseif(is_bool($value)) $out .= $value ? 'true' : 'false';
		elseif(is_numeric($value))$out .= $value;
		else $out .= '"'.addslashes($value).'"';
		$output[] = $out;
	}
	return '{'.implode(',', $output).'}';
}

