<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include 'setting_field_edit.php';
$tab_output = array(
	'Fields'    => '',
	'New Field' => $form->edit->getForm()
);

$form = _lib('pea',  'guestbook_field' );
$form->initRoll( "WHERE 1 ORDER BY orderby", 'id' );

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'title' );
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.setting_field_edit' );

$form->roll->addInput( 'type', 'sqlplaintext' );
$form->roll->input->type->setTitle( 'type' );

$form->roll->addInput( 'checked', 'sqlplaintext' );
$form->roll->input->checked->setTitle( 'validate' );

$form->roll->addInput( 'tips', 'sqlplaintext' );
$form->roll->input->tips->setTitle( 'tips' );

$form->roll->addInput( 'orderby', 'orderby' );
$form->roll->input->orderby->setTitle( 'Ordered' );

$form->roll->addInput( 'mandatory', 'checkbox' );
$form->roll->input->mandatory->setTitle( 'not null' );
$form->roll->input->mandatory->setCaption( 'yes' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'active' );

$form->roll->onSave('_field_orderby', $form, true);
$form->roll->onDelete('_field_orderby', $form, true);

$tab_output['Fields'] = $form->roll->getForm();

echo tabs($tab_output, 1, 'guestbook_field');
