<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function smiley_parse($text = '')
{
	if(empty($text)) return false;
	$output = $text;
	$r = _get_smiley_array();
	foreach($r AS $code => $image)
	{
		$output = str_replace( $code, $image, $output );
	}
	$output = urldecode($output);
	return $output;
}

function _get_smiley_array()
{
	global $Bbc;
	if(isset($Bbc->smiley_array) AND is_array($Bbc->smiley_array))
	{
		return $Bbc->smiley_array['images'];
	}
	include_once _CONF.'smiley.php';
	$Bbc->smiley_array = array(
		'images'=> $r_smiley
	,	'icons' => $r_smiley_icon
	);
	return $Bbc->smiley_array['images'];
}

function smiley_icon()
{
	global $Bbc;
	if(isset($Bbc->smiley_array) AND is_array($Bbc->smiley_array)) {
		return $Bbc->smiley_array['icons'];
	}
	_get_smiley_array();
	return $Bbc->smiley_array['icons'];
}
