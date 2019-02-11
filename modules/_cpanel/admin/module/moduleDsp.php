<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  $str_table = "bbc_module" );
$form->initRoll( "WHERE 1 ORDER BY name ASC", 'id' );
$form->roll->setDeleteTool(false);

$form->roll->addInput( 'name', 'sqllinks' );
$form->roll->input->name->setTitle( 'Name' );
$form->roll->input->name->setLinks( $Bbc->mod['circuit'].'.module&act=moduleEdit' );

$form->roll->addInput( 'created', 'datetime' );
$form->roll->input->created->setTitle( 'installed' );
$form->roll->input->created->setPlaintext( true );

$form->roll->addInput( 'protected', 'checkbox' );
$form->roll->input->protected->setTitle( 'protected' );
$form->roll->input->protected->setCaption( 'protect' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'enable' );

$form->roll->onSave('_module_repair');

echo $form->roll->getForm();
echo $sys->button($Bbc->mod['circuit'].'.module&act=update', 'update module', 'search');

function _module_repair()
{
	global $db;
	$db->cache_clean('modules.cfg');
}