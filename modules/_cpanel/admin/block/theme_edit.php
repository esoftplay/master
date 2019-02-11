<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$form = _lib('pea',  $str_table = "bbc_block_theme" );

$form->initEdit( "WHERE id='".$_GET['id']."'", 'id' );

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle('Edit Block Theme in "'.$_CONFIG['template'].'"');

$form->edit->addInput( 'name', 'text' );

$form->edit->addInput( 'content', 'textarea' );
$form->edit->input->content->setTitle( 'Theme' );
$form->edit->input->content->setCodeEditor( true, 'html');

$form->edit->addInput( 'active', 'checkbox' );
$form->edit->input->active->setTitle( 'Active' );
$form->edit->input->active->setCaption( 'publish' );

$form->edit->action();
echo $form->edit->getForm();
