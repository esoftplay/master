<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$output = msg(lang('register finish auto'));;
if (isset($_GET['pending']))
{
	if ($_GET['pending'])
	{
		$output = msg(lang('register finish pending'));
	}
}
if (isset($_GET['message']))
{
	$msg  = $_GET['message'];
	$stat = !empty($_GET['ok']) ? 'success' : 'danger';
	$output = msg($msg, $stat);
}
include tpl('register-finish.html.php');