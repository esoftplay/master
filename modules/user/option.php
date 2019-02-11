<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['id']) && empty($_POST['template']) && preg_match('~^[a-z0-9\-_]+$~is', $_GET['id']))
{
	$q = "SELECT 1 FROM `bbc_template` WHERE `name`='".$_GET['id']."'";
	if ($db->getOne($q))
	{
		$_POST['template'] = $_GET['id'];
	}
}
if(!empty($_POST['template']))
{
	$t = $db->getOne("SELECT params FROM bbc_config WHERE module_id=0 AND name='template'");
	$t = json_decode($t);
	if (!empty($t) && $t == $_POST['template'])
	{
		$_SESSION['option']['template'] = '';
	}else{
		$_SESSION['option'] = array('template'=> $_POST['template']);
	}
	if(empty($_POST['back']))
	{
		$_POST['back'] = _URL;
	}
	redirect($_POST['back']);
}
