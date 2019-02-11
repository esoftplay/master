<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Modules Installation');
/*================================================
 * CEHCK PERMISSION FILE FOR TEMPLATE PATH...
 *==============================================*/
$root_path = _ROOT.'modules/';
$root_path2 = _ROOT.'blocks/';
$permision_path = file_octal_permissions($root_path);
$permision_path2 = file_octal_permissions($root_path2);

$form = _lib('pea', 'bbc_module');
$form->initAdd();

$form->add->addInput( 'header', 'header' );
$form->add->input->header->setTitle( 'Upload New Module' );

$form->add->addInput( 'name', 'file' );
$form->add->input->name->setTitle( 'File' );
$form->add->input->name->setFolder( _CACHE );
$form->add->input->name->setAllowedExtension( array('zip') );
$form->add->input->name->addTip('Select File to upload new module ');

$form->add->onInsert('tools_module_install','',true);
$form->add->action();

include_once $Bbc->mod['root'].'module/updateModule.php';

$form->initRoll( "ORDER BY name ASC", 'id' );
$form->roll->setSaveTool(false);
if(ini_get('safe_mode'))	$form->roll->setDeleteTool(false);



$form->roll->addInput( 'name', 'sqllinks' );
$form->roll->input->name->setTitle( 'Download' );
$form->roll->input->name->setLinks( $Bbc->mod['circuit'].'.tools&act=module_download' );

$form->roll->addInput( 'created', 'datetime' );
$form->roll->input->created->setTitle( 'installed' );
$form->roll->input->created->setPlaintext( true );

$form->roll->onDelete('tools_module_uninstall');

echo $form->roll->getForm();
echo $form->add->getForm();
