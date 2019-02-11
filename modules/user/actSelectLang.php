<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$lang = $_GET['lang'];
$_SESSION['lang'] = $lang;
$next_to = (!empty($_SERVER['HTTP_REFERER']) and stristr($_SERVER['HTTP_REFERER'], _URL)) ? $_SERVER['HTTP_REFERER'] : _URL ;
header("location:$next_to");
