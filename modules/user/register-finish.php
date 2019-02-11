<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(@$_GET['pending'])
{
	echo msg(lang('register finish pending'));
}else{
	echo msg(lang('register finish auto'));
}
