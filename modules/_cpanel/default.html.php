<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$data_result = array();
if (!empty($output))
{
	$data_result = $output;
}else
if (!empty($cat))
{
	$data_result = $cat;
}else
if (!empty($r_list))
{
	$data_result = $r_list;
}else
if(!empty($data))
{
	$data_result = $data;
}
$data_output = _cpanel_result($data_result);