<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function sring_space($txt, $txt2, $spaces, $add = ' ', $align = 'right')
{
	$i = strlen($txt);
	$out = $txt;
	if($align == 'left') {
		$k = $spaces - $i;
		for($l=0; $l < $k; $l++) {
			$out .= $add;
		}
	}else{
		$j = strlen($txt2);
		$k = $spaces - $i - $j;
		for($l=0; $l < $k; $l++) {
			$out .= $add;
		}
	}
	$out .= $txt2;
	return $out;
}
function string_limit($txt, $limit=150, $add_txt = '')
{
	$len    = strlen($txt);
	$limit -= strlen($add_txt);
	if($len > $limit)
	{
		$output = substr($txt, 0, $limit).$add_txt;
	}else{
		$output = $txt.$add_txt;
	}
	return $output;
}