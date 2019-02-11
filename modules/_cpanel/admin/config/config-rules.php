<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$q1 = "SELECT id, name FROM bbc_user_group WHERE is_admin=0 ORDER BY score DESC";
$q2 = "SELECT id, title FROM bbc_lang ORDER BY title";
$_setting = array(
	'content_max' => array(
		'text' => 'Maximum Sum of Content',
		'tips' => 'This is the total of content which will be keep in database. To optimize Database speed, oldest content will be save in static file. Leave it zero or blank for unlimited total content.',
		'type' => 'text',
		'add'  => 'item(s)'
	),
	'content_date' => array(
		'text' => 'Date Format',
		'tips' => 'This format will be used to display created date in content',
		'type' => 'text'
	),
	'content_rss' => array(
		'text' => 'RSS list',
		'tips' => 'Insert the number of displayed contents in RSS',
		'type' => 'text',
		'add'  => 'item(s)'
	),
	'num_rows' => array(
		'text' => 'Num. Rows',
		'tips' => 'how many data will display in listing view in all administration page',
		'type' => 'text',
		'add'  => 'row(s)'
	),
	'register_auto' => array(
		'text'   => 'User Registration',
		'tips'   => 'By having this checked, the validation code will be sent to registrar by email. The registrar will be a member, after they click the validation code link in their email inbox.',
		'type'   => 'checkbox',
		'option' => 'Auto Approved'
	),
	'register_expired' => array(
		'text' => 'Expired Link',
		'tips' => 'This is where you can define the number of days for validation code\'s expired, the link will not work after specified days in this field. "validation code" is the link which is sent to registrant after they register',
		'type' => 'text',
		'add'  => 'day(s)'
	),
	'register_monitor' => array(
		'text'   => 'Register Monitor',
		'tips'   => 'System will notify admin by email for every new registration',
		'type'   => 'checkbox',
		'option' => 'Yes'
	),
	'register_groups' => array(
		'text'   => 'User Groups',
		'tips'   => 'Which user group will be included if registrar is approved',
		'type'   => 'checkbox',
		'option' => $db->getAssoc($q1)
	),
	'lang_default' => array(
		'text'   => 'Language Default',
		'tips'   => 'Select default language from available languages',
		'type'   => 'select',
		'option' => $db->getAll($q2)
	),
	'lang_auto' => array(
		'text'   => 'Language Save',
		'tips'   => 'If language is not available, the language will be saved automatically and ready to be translated for the next',
		'type'   => 'checkbox',
		'option' => 'Auto Save'
	),
	'permitted_uri' => array(
		'text'    => 'Permitted URI',
		'tips'    => 'Insert allowing character for URI (do not change any character if you do not understand)',
		'default' => 'a-z0-9~%\.:_\-',
		'type'    => 'text'
	),
	'uri_separator' => array(
		'text'    => 'Word Separator in URL',
		'tips'    => 'Please specified ONE character to separate between words in URL, this character also will be used in "menu_save" function',
		'default' => '_',
		'type'    => 'text'
	)
);
$params = array(
	'config' => $_setting,
	'name'   => 'rules',
	'title'  => 'Rules Configuration',
	'id'     => 0
);
$conf->set($params);
