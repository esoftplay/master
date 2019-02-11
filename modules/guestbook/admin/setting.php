<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tabs = array();
$form = _class('bbcconfig');

$_setting = array(
	'alert' => array(
		'text'    => 'Alert New Post',
		'type'    => 'radio',
		'option'  => array('1'=>'yes','0'=>'no'),
		'default' => '1',
		'tips'    => 'Alert admin by email and notification for every new post'
	),
	'email' => array(
		'text' => 'Email',
		'type' => 'text',
		'tips' => 'Insert email address as destination of new GuestBook posted, or leave it blank to use <a href="index.php?mod=_cpanel.config" rel="admin_link">global email</a>'
	),
	'approved' => array(
		'text'    => 'Approved',
		'type'    => 'radio',
		'option'  => array('1'=>'auto','0'=>'manual'),
		'default' => '0',
		'tips'    => 'if auto, all incoming GuestBook will automaticaly publish'
	),
	'tot' => array(
		'text'    => 'Total per page',
		'type'    => 'text',
		'default' => '12',
		'tips'    => 'Items to show per page'
	),
	'avatar' => array(
		'text'    => 'Use Avatar',
		'type'    => 'radio',
		'option'  => array('1'=>'yes','0'=>'no'),
		'default' => '1',
		'tips'    => 'Show user profile picture, if you select "yes" everytime user wants to post guestbook they will be forced to identify him self using their own social media. Go to <a href="index.php?mod=_cpanel.language" rel="admin_link">Control Panel / Language</a> and search "You must validate your profile" in module "guestbook" to change the default message (you can create one if not exists)'
	),
	'animated' => array(
		'text'    => 'Load Page',
		'type'    => 'radio',
		'option'  => array('1'=>'Animated','0'=>'Manual'),
		'default' => '0',
		'tips'    => 'Select method to show guestbook list per page'
	),
	'orderby' => array(
		'text'    => 'Sequence from',
		'type'    => 'radio',
		'option'  => array('1'=>'Last Posted','2'=>'First Posted', '3'=>'Alphabetically'),
		'default' => '1'
	)
);
$output = array(
	'config'=> $_setting,
	'name'	=> 'guestbook',
	'title'	=> 'guestbook List'
);
$form->set($output);
$tabs['List'] = $form->show();



$form = _lib('pea', 'guestbook_field');
$form->initRoll( "WHERE 1 ORDER BY orderby", 'id' );
$form->roll->setDeleteTool(false);

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'Title' );
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.setting_field_edit' );

$form->roll->addInput( 'orderby', 'orderby' );
$form->roll->input->orderby->setTitle( 'Ordered' );

$form->roll->addInput( 'mandatory', 'checkbox' );
$form->roll->input->mandatory->setTitle( 'not null' );
$form->roll->input->mandatory->setCaption( 'yes' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'active' );

$tabs['Fields'] = $form->roll->getForm();
$tabs['Fields'] .= $sys->button($Bbc->mod['circuit'].'.setting_field&return='.urlencode(seo_uri()), 'Manage Fields', 'tasks');

echo tabs($tabs);