<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(isset($_POST['Submit']))
{
	if(isset($_POST['ids']) && count($_POST['ids']) > 0)
	{
		survey_sess('index', $_POST['ids']);
		redirect($Bbc->mod['circuit'].'.index_2');
	}else{
		$errorMsg = lang('No Selection');
	}
}
if( !empty($errorMsg) ){
	echo msg( $errorMsg);
}
include tpl('index_1.html.php');