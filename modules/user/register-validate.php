<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(!empty($_GET['id']))
{
	$Msg = '';
	$q="SELECT * FROM bbc_account_temp WHERE code='".$_GET['id']."'";
	$tmp_data = $db->getRow($q);
	if(!$db->Affected_rows() || !$tmp_data['active'])
	{
		$Msg = lang('register success failed');
	}else{
		$exp = strtotime($tmp_data['date']);
		$now = strtotime('NOW');
		if($now > $exp)
		{
			$Msg = lang('register success expired');
		}else{
			$user_id = user_create($tmp_data);
			if($user_id > 0)
			{
				// FECTH USER DATA
				$q = "SELECT * FROM bbc_account WHERE `user_id`=".$user_id;
				$data = $db->getRow($q);
				$q = "SELECT * FROM bbc_user WHERE id=".$user_id;
				$data = array_merge($data, $db->getRow($q));
				$data['password'] = decode($data['password']);

				// FETCH EMAIL DESTINATION...
				$email_to = array($data['email']);
				if(config('rules', 'register_monitor'))
				{
					$email_to[] = config('email', 'address');
				}

				// FETCH PARAMS FOR EMAIL AND AUTO LOGIN...
				foreach($data AS $key => $value)
				{
					if($key != 'params')
					{
						$GLOBALS[$key] = $value;
					}else{
						$r = config_decode($value);
						foreach($r AS $var => $val)
						{
							if(preg_match('~^[a-z0-9_]+$~is', $var))
								$GLOBALS[strtolower($var)] = $val;
						}
					}
				}

				$sys->mail_send($email_to, 'register_success');
				$Msg = $sys->text_replace(lang('register success commit'));
				user_login($data['username'], $data['password']);
			}else{
				$Msg = user_create_validate_msg();
			}
		}
		$q = "DELETE FROM bbc_account_temp WHERE id=".$tmp_data['id'];
		$db->Execute($q);
	}
	if(!empty($Msg))
	echo msg($Msg);
	$q = "DELETE FROM bbc_account_temp WHERE `date` < NOW()";
	$db->Execute($q);
}