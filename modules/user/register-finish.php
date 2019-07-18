<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(@$_GET['pending'])
{
	$output = msg(lang('register finish pending'));
}else{
	$output = msg(lang('register finish auto'));
}
include tpl('register-finish.html.php');