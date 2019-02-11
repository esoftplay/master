<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (config('guestbook', 'avatar') == '1')
{
	$auth   = user_auth('You must validate your profile');
}
if (!$sys->menu_real)
{
	$sys->nav_change(lang('Guest Book'), 'guestbook');
	$sys->nav_add(lang('Post Guestbook'));
}
echo '<h1>'.lang('Post Guestbook').'</h1>';
$params = array(
	'title'      => 'Use form below',
	'table'      => 'guestbook',
	'config_pre' => array(
		'name' => array(
			'text'      => 'Name',
			'type'      => 'text',
			'default'   => @$auth['name'],
			'mandatory' => 1
			)
		),
	'config'      => $db->getAll("SELECT * FROM `guestbook_field` WHERE `active`=1 ORDER BY `orderby` ASC"),
	'config_post' => array(
		'email' => array(
			'text'      => 'Email',
			'type'      => 'text',
			'default'   => @$auth['email'],
			'mandatory' => 1
			),
		'message' => array(
			'text'      => 'Message',
			'type'      => 'textarea',
			'attr'      => 'rows="5" cols="50"',
			'mandatory' => 1
			),
		'vcode' => array(
			'text' => 'Validation Code',
			'type' => 'captcha'
			)
		),
	'name'      => 'params',
	'id'        => 0,
	'post_func' => '_send_mail'
	);
$form = _class('params', $params);
$form->set_encode(false);
echo $form->show();

function _send_mail($form)
{
	global $sys, $db, $Bbc, $auth;
	$conf   = get_config('guestbook', 'guestbook');
	$q      = "SELECT * FROM $form->table WHERE id=$form->table_id";
	$data   = $db->getRow($q);
	$arr    = config_decode($data['params']);
	$params = array_merge($data, $arr);
	if (!empty($auth))
	{
		$arr['image'] = $auth['image'];
		$add_sql      = ', `params`=\''.json_encode($arr).'\'';
	}else{
		$add_sql = '';
	}

	$q    = "UPDATE $form->table SET `date`=NOW(){$add_sql}, publish=".@intval($conf['approved'])." WHERE id=$form->table_id";
	$db->Execute($q);
	unset($params['id'], $params['date'], $params['publish'], $params['params']);
	if($conf['alert'])
	{
		$d = 'User Profile :';
		foreach($params AS $key => $value)
		{
			$d .= "\n".$key.' : '.$value;
		}
		$params['detail'] = $d;
		$email = is_email($conf['email']) ? $conf['email'] : config('email','address');
		$to = array($data['email'], $email);
		$sys->mail_send($to, 'guestbook', $params);

		$post    = array(
			'url_admin' => 'index.php?mod=guestbook.list_detail&id='.$form->table_id
			);
		_func('alert');
		alert_add(lang('Guest Book::').' '.$data['name'], $params['message'], $post, 'admin');
	}
	$message = $sys->text_replace(lang('finished'));
	$_SESSION['guestbook'] = $message;
	redirect($Bbc->mod['circuit'].'.form-finished');
}
