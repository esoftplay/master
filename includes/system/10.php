<?php

$i = floatval(phpversion());
if ($i < 7.1)
{
	$f = '10-5.php';
}else{
	$f = '10-7.php';
}
require_once __DIR__.'/'.$f;