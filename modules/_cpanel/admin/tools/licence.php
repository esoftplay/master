<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Logo Licence');
$_setting = array(
	'html' => array(
		'text' => 'HTML',
		'type' => 'htmlarea',
		'attr' => array('ToolbarSet'=>'Default')
	),
	'active' => array(
		'text'   => 'License',
		'type'   => 'checkbox',
		'option' => 'Enable'
	)
);
$output = array(
	'config' => $_setting,
	'name'   => 'activation',
	'title'  => 'Logo Licence',
	'id'     => 0
);
$form = _class('bbcconfig');
$form->set($output);
echo $form->show();