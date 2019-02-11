<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$data['id'] = @intval($data['id']);
$form = _lib('pea',  'bbc_user_field' );
$form->initEdit( '', 'id' );

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle( 'Add User Field' );

$form->edit->addInput('group_id', 'hidden');
$form->edit->input->group_id->setDefaultValue( $data['id'] );

$q = "SELECT COUNT(*) FROM bbc_user_field WHERE group_id=".$data['id'];
$t = $db->getOne($q) + 1;
$form->edit->addInput('orderby', 'hidden');
$form->edit->input->orderby->setDefaultValue($t);

$form->edit->addInput('title', 'text');
$form->edit->input->title->setTitle( 'title' );
$form->edit->input->title->addTip( 'this field must be unique, every change made will affect all user profiles in database.' );

$form->edit->addInput('type', 'select');
$form->edit->input->type->setTitle( 'Type' );
$form->edit->input->type->addOption( 'text' );
$form->edit->input->type->addOption( 'textarea' );
$form->edit->input->type->addOption( 'select' );
$form->edit->input->type->addOption( 'radio' );
$form->edit->input->type->addOption( 'checkbox' );
$form->edit->input->type->addOption( 'file' );

$form->edit->addInput('checked', 'select');
$form->edit->input->checked->setTitle( 'Validation' );
$form->edit->input->checked->addOption( 'any' );
$form->edit->input->checked->addOption( 'email' );
$form->edit->input->checked->addOption( 'url' );
$form->edit->input->checked->addOption( 'phone' );
$form->edit->input->checked->addOption( 'number' );

$form->edit->addInput('attr', 'text');
$form->edit->input->attr->setTitle( 'Attributes' );
$form->edit->input->attr->setSize( 40 );
$form->edit->input->attr->addTip( 'Style attributes of input eg. size= height=' );

$form->edit->addInput('tips', 'textarea');
$form->edit->input->tips->setTitle( 'Tips' );
$form->edit->input->tips->setSize( 1, 40 );

$form->edit->addInput('default', 'text');
$form->edit->input->default->setTitle( 'Default' );
$form->edit->input->default->setSize( 40 );
$form->edit->input->default->addTip( 'set default value of field (multiple values separated by ;)' );

$form->edit->addInput('option', 'textarea');
$form->edit->input->option->setTitle( 'Options' );
$form->edit->input->option->setSize( 3, 40 );
$form->edit->input->option->addTip( 'if selection input type is checkbox (multioptions) or radio or select then this field must be declare and separate by ;' );

$form->edit->addInput('mandatory', 'checkbox');
$form->edit->input->mandatory->setTitle( 'Mandatory' );
$form->edit->input->mandatory->setCaption( 'yes' );
$form->edit->input->mandatory->setAlign( 'left' );
$form->edit->input->mandatory->addTip( 'the field must not empty' );

$form->edit->addInput('active', 'checkbox');
$form->edit->input->active->setTitle( 'Active' );
$form->edit->input->active->setCaption( 'active' );
$form->edit->input->active->setAlign( 'left' );
$form->edit->input->active->setDefaultValue( 1 );

$form->edit->onSave('user_field_repair');
$form->edit->action();

$form2 = _lib('pea',  'bbc_user_field' );

$form2->initRoll( 'WHERE group_id='.$data['id'].' ORDER BY orderby', 'id' );

$form2->roll->addInput( 'title', 'sqllinks' );
$form2->roll->input->title->setTitle( 'title' );
$form2->roll->input->title->setLinks( $Bbc->mod['circuit'].'.user&act=field-edit' );

$form2->roll->addInput( 'type', 'sqlplaintext' );
$form2->roll->input->type->setTitle( 'type' );

$form2->roll->addInput( 'checked', 'sqlplaintext' );
$form2->roll->input->checked->setTitle( 'validate' );

$form2->roll->addInput( 'tips', 'sqlplaintext' );
$form2->roll->input->tips->setTitle( 'tips' );

$form2->roll->addInput( 'orderby', 'orderby' );
$form2->roll->input->orderby->setTitle( 'Ordered' );

$form2->roll->addInput( 'mandatory', 'checkbox' );
$form2->roll->input->mandatory->setTitle( 'Mandatory' );
$form2->roll->input->mandatory->setCaption( 'yes' );

$form2->roll->addInput( 'active', 'checkbox' );
$form2->roll->input->active->setTitle( 'Active' );
$form2->roll->input->active->setCaption( 'active' );

$form2->roll->onSave('user_field_repair');
$form2->roll->onDelete('user_field_repair', '', true);

$tabs = array(
	'Fields'    => $form2->roll->getForm(),
	'Add Fields'=> $form->edit->getForm()
);
function user_field_repair($id =0)
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

echo '<br />'.tabs($tabs);
