<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$params = array(
	'title'       => 'Add Testimonial',
	'table'       => 'testimonial',
	'config_pre'  => array(),
	'config'      => $db->getAll("SELECT * FROM `testimonial_field` WHERE `active`=1 ORDER BY `orderby` ASC"),
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
	'tips'       => 'insert image URL for user profile picture',
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

$form = _class('params');
$form->set($params);
$form->set_encode(false);
echo $form->show();

function _change_date($form)
{
	global $sys, $db;
	$conf = get_config('testimonial', 'testimonial');
	$q = "UPDATE $form->table SET `date`=NOW(), publish=".@intval($conf['approved'])." WHERE id=$form->table_id";
	$db->Execute($q);
}
