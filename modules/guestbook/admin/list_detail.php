<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$sys->nav_add('Edit Guestbook');
$form = _class('params');
$q = "SELECT * FROM guestbook_field WHERE active=1 ORDER BY orderby ASC";
$r = $db->getAll($q);
$params = array(
	'title'       => 'Guestbook Detail',
	'table'       => 'guestbook',
	'config_pre'  => array(),
	'config'      => $r,
	'config_post' => array(),
	'name'        => 'params',
	'id'          => $id
);

$params['config_pre'] = array(
	'name' => array(
		'text' => 'Name',
		'type' => 'text'
	)
);
array_unshift($params['config'], array(
	'id'         => 0,
	'type'       => 'text',
	'checked'    => 'any',
	'title'      => 'image',
	'tips'       => '',
	'attr'       => '',
	'default'    => '',
	'option'     => '',
	'manadatory' => '',
	'orderby'    => 0,
	'active'     => 1
));
$params['config_post'] = array(
	'email' => array(
		'text' => 'Email',
		'type' => 'text',
	),
	'date' => array(
		'text' => 'Posted',
		'type' => 'plain'
	),
	'message'	=> array(
		'text' => '<b>Message</b>',
		'type' => 'textarea',
		'attr' => 'cols=40 rows=4'
	),
	'publish' => array(
		'text'   => 'Publish',
		'Option' => 'Published',
		'type'   => 'checkbox'
	)
);
$form->set($params);
$form->set_encode(true);
echo $form->show();