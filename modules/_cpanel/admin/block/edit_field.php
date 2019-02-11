<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_POST['block_edit_field']))
{
	if ($_POST['block_edit_field'] == 'EDIT')
	{
		$_SESSION['block_edit_field'] = !empty($_POST['edit']) ? $_POST['edit'] : array();
	}else{
		unset($_SESSION['block_edit_field']);
	}
	redirect($_POST['return']);
}