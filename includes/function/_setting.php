<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*
$config = array(
	'characters' => array(
		'text'      => 'Sample Text Input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'text', // type of input
		'language'  => true, 	// is input support multiple language, default value is false
		'attr'      => ' size="40"', // additional attribute for the input
		'default'   => 'insert default value',
		'mandatory' => 1, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any'	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'plain' => array(
		'text'    => 'Sample Plaintext',
		'tips'    => 'text to display under the input in small font-size',
		'add'     => 'additional text after the input',
		'help'    => 'popup tips to display right after the title',
		'type'    => 'plain',
		'default' => 'this is the text'
		),
	'radio' => array(
		'text'      => 'Sample Radio input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'radio',
		'delim'     => "<br />\n",
		'option'    => array('yes', 'no'),
		'default'   => '0',
		'mandatory' => 1, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any'	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'select' => array(
		'text'			=> 'Sample Select input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'select',
		'is_arr'    => true,			// if this is true, user has multiple selection
		'option'    => array(1 => 'yes', 0 => 'no'),
		'default'   => 'no',
		'mandatory' => 0, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any'	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'checkbox'	=> array(
		'text'			=> 'Sample Checkbox input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'checkbox',
		'delim'     => "<br />\n",
		'option'    => array(1 => 'yes', 0 => 'no'), // leave it empty or unset for one checkbox and value
		'default'   => 1,
		'mandatory' => 0, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any',	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'checkbox2'	=> array(
		'text'			=> 'Sample Checkbox with one option',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'checkbox',
		'option'    => 'activate',
		'default'   => 1,
		'mandatory' => 0, // is this field must be filled in (compulsory). Eg. 1 or 0
		'checked'   => 'any'	// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'textarea'	=> array(
		'text'			=> 'Sample textarea input',
		'tips'      => 'text to display under the input in small font-size',
		'add'       => 'additional text after the input',
		'help'      => 'popup tips to display right after the title',
		'type'      => 'textarea',
		'language'  => true, 			// is input support multiple language, default value is false
		'default'   => 'sfdghgfhg',// default value
		'mandatory' => 0,				// is this field must be filled in (compulsory). Eg. 1 or 0
		'format'    => 'none', 		// what format you want to use eg. none | code | html
		'checked'   => 'any'			// validate input before it save in database eg. 'any' || 'email' || 'url' || 'phone' || 'number' default is 'any'
		),
	'file'			=> array(
		'text'		=> 'Sample input for single file',
		'tips'		=> 'text to display under the input in small font-size',
		'add'			=> 'additional text after the input',
		'help'		=> 'popup tips to display right after the title',
		'type'		=> 'file',
		'default'	=> 'sfdghgfhg',
		'path'		=> 'images/uploads/'
		)
	);
#*/
function _setting($config, $default = array(), $form_title = 'Additional Parameter', $name = 'config')
{
	foreach ($config as $key => $value)
	{
		if (!empty($value['help']) && empty($value['tips']))
		{
			$config[$key]['tips'] = $value['help'];
			unset($config[$key]['help']);
		}
	}
	$c = _class('bbcconfig', $config, $name, '', $form_title);
	$c->default = $default;
	$c->show_param($c->config, $c->default, $c->title, $c->name);
}
