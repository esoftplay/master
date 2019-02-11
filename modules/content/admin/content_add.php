<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$q = "SELECT id FROM bbc_content_type";
$r = $db->getCol($q);
if(count($r) == 1)
{
	redirect($Bbc->mod['circuit'].'.content_add&type_id='.$r[0]);
}

$form = _lib('pea', 'bbc_content_type');
$form->initRoll( 'WHERE 1 ORDER BY id DESC', 'id' );
$form->roll->setDeleteTool(false);
$form->roll->setSaveTool(false);

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'Select Type of Content' );
$form->roll->input->title->setGetName( 'type_id' );
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.content_add' );

echo $form->roll->getForm();