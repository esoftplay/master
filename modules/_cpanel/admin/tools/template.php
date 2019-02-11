<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Template Installation');
/*================================================
 * CEHCK PERMISSION FILE FOR TEMPLATE PATH...
 *==============================================*/
$root_path = _ROOT.'templates/';
$permision_path = file_octal_permissions($root_path);

$form = _lib('pea', 'bbc_template');
$form->initAdd();

$form->add->addInput( 'header', 'header' );
$form->add->input->header->setTitle( 'Upload new Template' );

$form->add->addInput( 'name', 'file' );
$form->add->input->name->setTitle( 'File' );
$form->add->input->name->setFolder( _CACHE );
$form->add->input->name->setAllowedExtension( array('zip') );
$form->add->input->name->addTip('Select File template to upload new template');

$form->add->onInsert('tools_template_install','',true);
$form->add->action();

$r_temp_del = array();
$r_temp_db	= $db->getAssoc("SELECT id, name FROM bbc_template");
$r_temp_dir = tools_template_list();
$r_temp_db_r= array_flip($r_temp_db);
foreach($r_temp_db AS $dt)
{
	if(!in_array($dt, $r_temp_dir))
	{
		if($r_temp_db_r[$dt] > 0 ) $r_temp_del[] = $r_temp_db_r[$dt];
	}
}
tools_template_delete($r_temp_del);
tools_template_insert($r_temp_dir, $r_temp_db);

$form->initRoll( "ORDER BY name ASC", 'id' );
$form->roll->setSaveTool(false);
if(ini_get('safe_mode'))	$form->roll->setDeleteTool(false);



$form->roll->addInput( 'name', 'sqllinks' );
$form->roll->input->name->setTitle( 'Download' );
$form->roll->input->name->setLinks( $Bbc->mod['circuit'].'.tools&act=template_download' );

$form->roll->addInput( 'thumb', 'file' );
$form->roll->input->thumb->setTitle( 'thumb' );
$form->roll->input->thumb->setFieldName( "CONCAT('/',`name`,'/thumbnail.png') AS thumb" );
$form->roll->input->thumb->setFolder( _ROOT.'templates/' );
$form->roll->input->thumb->setPlaintext(true);
$form->roll->input->thumb->setImageClick(true);

$form->roll->addInput( 'installed', 'datetime' );
$form->roll->input->installed->setTitle( 'installed' );
$form->roll->input->installed->setPlaintext( true );

$form->roll->addInput( 'syncron_to', 'select' );
$form->roll->input->syncron_to->setTitle( 'Syncron' );
$form->roll->input->syncron_to->setPlaintext( true );
foreach((array)$r_temp_db AS $option => $value)
	$form->roll->input->syncron_to->addOption( $value, $option );

$form->roll->onDelete('tools_template_uninstall');//, $form->roll->getDeletedId(), false);

echo $form->roll->getForm();

if($permision_path == '777' || !ini_get('safe_mode'))
{
	echo $form->add->getForm();
}else{
	echo (msg('Please chmod 777 path: '._ROOT.'templates/ if you want to upload new template', 'Warning : '));
}
#*/
