<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$user = new stdClass();
$user->id = 0;
if (empty($Bbc->no_log))
{
	/*===================================================
	 * IT'S DECLARE VARIABLE $user
	 *==================================================*/
	if(@intval($_SESSION[bbcAuth]['id']) > 0)
	{
		foreach((array)@$_SESSION[bbcAuth] AS $id => $value)
		{
			if(!empty($id))
			{
				$user->$id = $value;
			}
		}
		$is_login = true;
	}else{
		$is_login = false;
	}
	$_CONFIG = get_config(0);
	$suffix  = (_ADMIN != '') ? '_admin' : '';
	$dur     = @$_CONFIG['logged']['duration'.$suffix];
	$per     = @$_CONFIG['logged']['period'.$suffix];
	if (!is_numeric($dur))
	{
		$dur = 2;
	}
	if (!is_string($per))
	{
		$per = 'HOUR';
	}
	/*===================================================
	 * AUTO LOGIN "REMEMBER ME USER"...
	 *==================================================*/
	if(!$is_login && _ADMIN == '')
	{
		if(user_auto_login())
		{
			foreach((array)@$_SESSION[bbcAuth] AS $id => $value)
			{
				if(!empty($id))
				{
					$user->$id = $value;
				}
			}
			$is_login = true;
		}
	}
	/*===================================================
	 * LOGOUT ALL USER IF IDLE TIME TOO LONG
	 *==================================================*/
	$q = "UPDATE bbc_user SET exp_checked='0000-00-00 00:00:00' WHERE exp_checked < NOW() AND exp_checked!='0000-00-00 00:00:00'";
	$db->Execute($q);

	/*===================================================
	 * MARK USER IF STILL ONLINE...
	 *==================================================*/
	if($is_login)
	{
		$q = "SELECT 1 FROM bbc_user WHERE exp_checked < NOW() AND id={$user->id}";
		if($db->getOne($q) AND @$_GET['mod'] != 'user.logout')
		{
			redirect('index.php?mod=user.logout');
		}
		$q = "UPDATE bbc_user SET exp_checked=DATE_ADD(NOW(), INTERVAL {$dur} {$per}) WHERE id={$user->id}";
		$db->Execute($q);
	}

	/*===================================================
	 * MARK ALL VISITOR...
	 *==================================================*/
	if(!isset($_SESSION[bbcAuth.'_log']))
	{
		$q = "INSERT INTO bbc_log	SET name='".session_id()."',	ip='".$_SERVER['REMOTE_ADDR']."', `datetime`=NOW()";
		$db->Execute($q);
		$_SESSION[bbcAuth.'_log'] = $db->Insert_ID();
	}else{
		$q = "UPDATE bbc_log	SET `datetime`=NOW() WHERE id=".intval($_SESSION[bbcAuth.'_log']);
		$db->Execute($q);
	}
	$q = "DELETE FROM bbc_log	WHERE datetime < DATE_ADD(NOW(), INTERVAL -{$dur} {$per})";
	$db->Execute($q);
}