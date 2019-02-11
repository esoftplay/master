<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function user_form($func, $title = 'Registration form', $group_ids = array(), $use_password = false, $use_vcode = true)
{
	global $db, $sys;
	lang_fetch($sys->get_module_id('user'));
	$params = array(
		'title'				=> $title
	,	'table'				=> 'bbc_account_temp'
	,	'config_pre'	=> array()
	,	'config'			=> user_field_group($group_ids)
	,	'config_post'	=> array()
	,	'name'				=> 'params'
	,	'id'					=> 0
	,	'pre_func'		=> $func
	,	'post_func'		=> ''
	);
	$params['config_pre'] = array(
		'name'=> array(
			'text'	=> 'Name'
		,	'type'	=> 'text'
		,	'attr'			=> 'size="30"'
		,	'mandatory'	=> '1'
		)
	);
	$params['config_post'] = array(
		'email'	=> array(
			'text'			=> 'Main Email'
		,	'type'			=> 'text'
		,	'mandatory'	=> '1'
		,	'attr'			=> 'size="30"'
		,	'checked'		=> 'email'
		)
	,	'password'	=> array(
			'text'			=> 'Password'
		,	'type'			=> 'text'
		,	'mandatory'	=> '1'
		,	'attr'			=> 'size="30"'
		)
	,	'vcode'	=> array(
			'text'	=> 'Validation Code'
		,	'type'	=> 'captcha'
		)
	);
	if(!$use_password)
	{
		unset($params['config_post']['password']);
	}
	if(!$use_vcode)
	{
		unset($params['config_post']['vcode']);
	}
	$form = _class('params');
	$form->set($params);
	$form->set_encode(true);
	return $form->show();
}
