<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Blocks Installation');
/*================================================
 * CEHCK PERMISSION FILE FOR TEMPLATE PATH...
 *==============================================*/
$root_path = _ROOT.'blocks/';
$permision_path = file_octal_permissions($root_path);

$form = _lib('pea', 'bbc_block_ref');
$form->initAdd();

$form->add->addInput( 'header', 'header' );
$form->add->input->header->setTitle( 'Upload new Block' );

$form->add->addInput( 'name', 'file' );
$form->add->input->name->setTitle( 'File' );
$form->add->input->name->setFolder( _CACHE );
$form->add->input->name->setAllowedExtension( array('zip') );
$form->add->input->name->addTip('Select File to upload new blocks');

$form->add->onInsert('tools_block_install','',true);
$form->add->action();

$r_block_del = array();
$r_block_db  = $db->getAssoc("SELECT id, name FROM bbc_block_ref");
$r_block_dir = tools_block_list();
$r_block_db_r= array_flip($r_block_db);
foreach($r_block_db AS $dt)
{
	if(!in_array($dt, $r_block_dir))
	{
		if($r_block_db_r[$dt] > 0 ) $r_block_del[] = $r_block_db_r[$dt];
	}
}
tools_block_delete($r_block_del);
tools_block_insert($r_block_dir, $r_block_db);

$form->initRoll( "ORDER BY name ASC", 'id' );
$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

$form->roll->addInput( 'name', 'sqllinks' );
$form->roll->input->name->setTitle( 'Download' );
$form->roll->input->name->setLinks( $Bbc->mod['circuit'].'.tools&act=block_download' );

$form->roll->addInput( 'id', 'selecttable' );
$form->roll->input->id->setTitle( 'Installed' );
$form->roll->input->id->setReferenceTable( 'bbc_block GROUP BY block_ref_id' );
$form->roll->input->id->setReferenceField( 'COUNT(*)', 'block_ref_id' );
$form->roll->input->id->setPlaintext( true );

$form->roll->onDelete('tools_block_uninstall');//, $form->roll->getDeletedId(), false);

echo $form->roll->getForm();
echo $form->add->getForm();
