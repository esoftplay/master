<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*=============================================
 * sample $from = array('from' => $_CONFIG['email']['address']
 *								, 'from_name'=> $_CONFIG['email']['name']);
 * sample $param = array('WordWrap' => 50
 *								, 'IsHTML'=> array($boolean));
 *								, 'AddCC'	=> array(array($email1, $name1)
 *																,	 array($email2, $name2));
 * JIKA INGIN MENGIRIMKAN EMAIL MELALUI SMTP SILAHKAN TAMBAHKAN VARIABLE DI BAWAH KE /config.php
 *	define('SMTP_HOST', 'smtp.gmail.com');
 *	define('SMTP_PORT', '465');
 *	define('SMTP_SECURE', 'ssl');
 *	define('SMTP_USERNAME', 'username@gmail.com');
 *	define('SMTP_PASSWORD', '****');
 *============================================*/
function sendmail($to, $subj, $msg, $f = array(), $param = array())
{
	global $mail;
	if(empty($mail->Mailer))
	{
		$mail = _lib('phpmailer');
		if (defined('SMTP_HOST') && defined('SMTP_USERNAME') && defined('SMTP_PASSWORD'))
		{
			$mail->IsSMTP();
			// $mail->SMTPDebug  = true;
			$mail->SMTPAuth   = true;
			$mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'ssl';
			$mail->Host       = SMTP_HOST;
			if (defined('SMTP_PORT'))
			{
				$mail->Port = SMTP_PORT;
			}
			$mail->Username = SMTP_USERNAME;
			$mail->Password = SMTP_PASSWORD;
		}else{
			$mail->IsMail();
		}
	}
	$from = is_array($f) ? array_values($f) : array($f);
	$mail->From     = (isset($from[0]) && is_email($from[0])) ?  $from[0] : config('email','address');
	$mail->FromName = isset($from[1]) ?  $from[1] : config('email','name');
	if(!empty($to)){
		if(is_array($to))
		{
			foreach($to AS $email)
			{
				$mail->AddAddress($email);
			}
		}else
		{
			$mail->AddAddress($to);
		}
	}else return false;
	if(!empty($param))
	{
		foreach((array)$param AS $obj => $value)
		{
			if(is_array($value))
			{
				foreach($value AS $data)
				{
					if(is_array($data))
					{
						$mail->call_user_func($obj, $data);
					}else{
						$mail->$obj($data);
					}
				}
			}else{
				if (method_exists($mail, $obj))
				{
					$mail->$obj($value);
				}else{
					$mail->$obj = $value;
				}
			}
		}
	}
	$mail->Subject 	= $subj;
	$mail->Body 	= $msg;
	$mail->Send();
	$mail->ClearAddresses();
	return true;
}
