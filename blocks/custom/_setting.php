<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'content' => array(
		'text' => 'Content',
		'tips' => 'Fill the custom content you wish, it will be executed as selected type',
		'type' => 'codearea',
		'attr' => array('syntax'=>'html','word_wrap'=>true,'syntax_selection_allow'=>'html,js,php','toolbar'=> "search, go_to_line, |, undo, redo, |,syntax_selection,word_wrap")
		),
	'type' => array(
		'text'   => 'Execution As',
		'type'   => 'select',
		'option' => array('none'/*,'php'*/)
		)
	);