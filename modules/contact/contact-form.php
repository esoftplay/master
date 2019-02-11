<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form   = _class('params');
$q      = "SELECT * FROM contact_field WHERE active=1 ORDER BY orderby";
$params = array(
	'title'       => 'Title',
	'table'       => 'contact',
	'config_pre'  => array() ,
	'config'      => $db->getAll($q),
	'config_post' => array() ,
	'name'        => 'params',
	'id'          => 0,
	'post_func'   => 'contact_form_mail'
	);

$params['config_pre'] = array(
	'name' => array(
		'text'      => 'Name',
		'type'      => 'text',
		'mandatory' => 1
		)
	);
$params['config_post'] = array(
	'email'	=> array(
		'text'      => 'Email',
		'type'      => 'text',
		'mandatory' => 1
		),
	'message' => array(
		'text'      => 'Message',
		'type'      => 'textarea',
		'mandatory' => 1
		),
	'vcode' => array(
		'text' => 'Validation Code',
		'type' => 'captcha'
		)
	);
function contact_form_mail(&$form)
{
	global $sys, $Bbc;
	$q = "UPDATE $form->table SET post_date=NOW() WHERE id=$form->table_id";
	$form->db->Execute($q);

	$q = "SELECT * FROM $form->table WHERE id=$form->table_id";
	$data		= $form->db->getRow($q);

	$params	= array_merge($data, config_decode($data['params']));
	unset($params['params']);
	$d = 'User Input :';
	foreach($params AS $key => $value)
	{
		if (!in_array($key, array('id', 'params', 'answer', 'post_date', 'answer_date', 'followed')))
		{
			$d .= "\n".ucwords($key).' : '.$value;
		}
	}
	$params['detail'] = $d;

	$c     = get_config('contact', 'form');
	$email = is_email($c['email']) ? $c['email'] : config('email','address');
	$to    = array($data['email'], $email);
	$sys->mail_send($to, 'contact', $params);

	$post    = array(
		'url_admin' => 'index.php?mod=contact.posted_answer&id='.$form->table_id
		);
	_func('alert');
	alert_add(lang('Contact Us::').' '.$data['name'], $params['message'], $post, 'admin');

	$message             = $sys->text_replace(lang('finished'));
	$_SESSION['contact'] = $message;
	redirect($Bbc->mod['circuit'].'.finished');
}
$form->set($params);
$form->set_encode(false);
$contact_form =  $form->show();

include tpl('contact-form.html.php');