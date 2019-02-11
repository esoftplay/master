<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'title'=> array(
		'text'		=> 'Meta Title',
		'type'		=> 'text'
	),
	'desc'		=> array(
		'text'		=> 'Meta Description',
		'type'		=> 'textarea',
		'attr'		=> "cols=100 rows=2"
	),
	'keyword'		=> array(
		'text'		=> 'Meta Keyword',
		'type'		=> 'textarea',
		'attr'		=> "cols=100 rows=2"
	),
	'url'=> array(
		'text'		=> 'Site Domain',
		'type'		=> 'text'
	),
	'icon'=> array(
		'text'		=> 'Site Icon',
		'type'		=> 'file',
		'attr'		=> '',
		'path'		=> 'images/'
	),
	'logo'=> array(
		'text'		=> 'Site Logo',
		'type'		=> 'file',
		'attr'		=> '',
		'path'		=> 'images/'
	),
	'footer'		=> array(
		'text'		=> 'Site Footer',
		'type'		=> 'htmlarea',
		'attr'		=> array('Width'=>'650px','Height'=>'100px')
	)
);
$params = array(
	'config'=> $_setting,
	'name'	=> 'site',
	'title'	=> 'Site Parameters',
	'id'		=> 0
);
$conf->set($params);
