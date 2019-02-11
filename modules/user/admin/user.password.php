<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if(isset($_POST['newpass']))
{
	$realpass= @$_SESSION['password'];
	$oldpass = @$_POST['oldpass'];
	$newpass = @$_POST['newpass'];
	$confirmpass = $_POST['confirmpass'];
	$q = "SELECT password FROM bbc_user WHERE id='".$user->id."'";
	$realpass = decode($db->getOne($q));
	if($realpass != $oldpass){
		echo msg(lang('Failed to change password'), 'danger');
	}else{
		echo msg(lang('Your password has been Changed'), 'success');
		$q = "UPDATE `bbc_user` SET `password`='".encode($confirmpass)."' WHERE `id`=".$user->id;
		$db->Execute($q);
	}
}
include tpl('user.password.html.php');
