<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$q = "SELECT COUNT(*) FROM bbc_content_type";
if($db->getOne($q) == 1)
{
	$q = "SELECT id FROM bbc_content_type WHERE 1 LIMIT 1";
	redirect($Bbc->mod['circuit'].'.type_edit&id='.$db->getOne($q));
}
include 'type_edit.php';

$form = _lib('pea', 'bbc_content_type');
$form->initRoll( 'WHERE 1 ORDER BY id DESC', 'id' );

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'Title' );
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.type_edit' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'Active' );

$form->roll->onSave('content_type_refresh');
$form->roll->onDelete('content_type_delete');

$tabs = array(
	'Type List'=> $form->roll->getForm(),
	'Add Type' => $type_form
);

echo tabs($tabs);