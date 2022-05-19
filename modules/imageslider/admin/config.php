<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _class('bbcconfig');
$_setting = array(
	'thumbnail' => array(
		'text'    => 'Thumbnail Creation',
		'type'    => 'radio',
		'option'  => array('1'=>'yes','0'=>'no'),
		'default' => '0',
		'tips'    => 'if you need to create Thumbnail for every file in image slider you can choose Yes'
	),
	'thumbsize' => array(
		'text'    => 'Thumbnail Size',
		'type'    => 'text',
		'default' => '200',
		'tips'    => 'Please insert for default size for the Thumbnail, this is the maximum width and height'
	)
);
$output = array(
	'config'=> $_setting,
	'name'	=> 'config',
	'title'	=> 'Additional Configuration'
);
$form->set($output);
echo $form->show();
