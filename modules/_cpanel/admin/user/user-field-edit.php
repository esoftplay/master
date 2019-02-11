<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$form = _lib('pea',  $str_table = 'bbc_user_field' );

$add_sql = ($id > 0) ? 'WHERE id='.$id : false;
$title	 = ($id > 0) ? 'Edit Custom User Field' : 'Add User Field';
$form->initEdit($add_sql);

$form->edit->addInput( 'header', 'header' );
$form->edit->input->header->setTitle( $title );

if(!$id)
{
	$form->edit->addInput('group_id', 'hidden');
	$form->edit->input->group_id->setDefaultValue('0');
	$q = "SELECT COUNT(*) FROM bbc_user_field WHERE group_id=0";
	$t = $db->getOne($q) + 1;
	$form->edit->addInput('orderby', 'hidden');
	$form->edit->input->orderby->setDefaultValue($t);
}

$form->edit->addInput( 'title', 'text' );
$form->edit->input->title->setTitle( 'title' );
$form->edit->input->title->addtip('this field must be unique, every change made will affect all the products in database.' );
if($id)
{
	$form->edit->input->title->setExtra( 'readonly' );
}else{
	$form->edit->input->title->setRequire();
}

$form->edit->addInput( 'type', 'select' );
$form->edit->input->type->setTitle( 'input type' );
$form->edit->input->type->addOption( 'text' );
$form->edit->input->type->addOption( 'textarea' );
$form->edit->input->type->addOption( 'select' );
$form->edit->input->type->addOption( 'radio' );
$form->edit->input->type->addOption( 'checkbox' );
$form->edit->input->type->addOption( 'file' );
$form->edit->input->type->setRequire();

$form->edit->addInput( 'tips', 'textarea' );
$form->edit->input->tips->setTitle( 'Text Tip' );

$form->edit->addInput( 'attr', 'text' );
$form->edit->input->attr->setTitle( 'Input Attribute' );
$form->edit->input->attr->addtip('Style attributes of input eg. size= height=' );

$form->edit->addInput( 'default', 'text' );
$form->edit->input->default->setTitle( 'Default Value' );
$form->edit->input->default->addtip('set default value of field (multiple values separated by ;' );

$form->edit->addInput( 'option', 'textarea' );
$form->edit->input->option->setTitle( 'Input Options (only for input type: select, checkbox, radio)' );
$form->edit->input->option->addtip('if "Input Type" is checkbox (multioptions) or radio or select then this field must be declare and separate by ;' );

$form->edit->addInput( 'mandatory', 'checkbox' );
$form->edit->input->mandatory->setTitle( 'Field Must not empty ?' );
$form->edit->input->mandatory->setCaption( 'yes' );

$form->edit->addInput( 'checked', 'radio' );
$form->edit->input->checked->setTitle( 'validate Input' );
$form->edit->input->checked->addRadio( 'any', 'any' );
$form->edit->input->checked->addRadio( 'number', 'number' );
$form->edit->input->checked->addRadio( 'phone', 'phone' );
$form->edit->input->checked->addRadio( 'email', 'email' );
$form->edit->input->checked->addRadio( 'url', 'url' );
$form->edit->input->checked->setDefaultValue( 'any' );

$form->edit->addInput( 'active', 'checkbox' );
$form->edit->input->active->setTitle( 'Active' );
$form->edit->input->active->setCaption( 'active' );
$form->edit->input->active->setDefaultValue( 1 );

$form->edit->onSave('user_field_repair');
$form->edit->action();
function user_field_repair()
{
	global $db;
	$i = 0;
	$q = "SELECT id, group_id, orderby FROM bbc_user_field ORDER BY group_id, orderby, id ASC";
	$r = $db->getAll($q);
	$group_id = 0;
	foreach($r AS $dt)
	{
		if($group_id != $dt['group_id']) $i = 0;
		$i++;
		if($dt['orderby'] != $i)
		{
			$q = "UPDATE bbc_user_field SET orderby=$i WHERE id=".$dt['id'];
			$db->Execute($q);
		}
		$group_id = $dt['group_id'];
	}
}
