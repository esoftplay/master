<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$r_country = array();
if($sys->module_name != 'user')
{
	$module_id = $sys->get_module_id('user');
	include _SYS.'language.php';
}
