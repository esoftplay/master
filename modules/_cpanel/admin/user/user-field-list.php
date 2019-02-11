<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

include 'user-field-edit.php';
$form->initRoll( 'WHERE group_id=0 ORDER BY orderby, title ASC', 'id' );

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'title' );
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&act=field-edit' );

$form->roll->addInput( 'type', 'sqlplaintext' );

$form->roll->addInput( 'checked', 'sqlplaintext' );
$form->roll->input->checked->setTitle( 'validate' );

$form->roll->addInput( 'orderby', 'orderby' );
$form->roll->input->orderby->setTitle( 'Ordered' );

$form->roll->addInput( 'mandatory', 'checkbox' );
$form->roll->input->mandatory->setCaption( 'yes' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setCaption( 'active' );

$form->roll->onSave('user_field_repair');
$form->roll->onDelete('user_field_repair');

$tabs = array(
	'Field List' => $form->roll->getForm(),
	'Add Field' => $form->edit->getForm()
	);

echo tabs($tabs);
