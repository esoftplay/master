<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sql = (@$_GET['id'] > 0) ? "WHERE id=".intval($_GET['id']) : '';
$txt = (!empty($sql)) ? 'Edit Contact Field' : 'Add Contact Field';

$form = _lib('pea',  'contact_field' );

$form->initEdit( $sql, 'id' );

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle($txt);

$form->edit->addInput( 'title', 'text' );
$form->edit->input->title->setTitle( 'Title' );
$form->edit->input->title->addtip('this field must be unique, every change made will affect all user contact in database.' );
if($sql)
$form->edit->input->title->setExtra( 'readOnly' );

$form->edit->addInput( 'type', 'select' );
$form->edit->input->type->setTitle( 'Type' );
$form->edit->input->type->addOption( 'text' );
$form->edit->input->type->addOption( 'textarea' );
$form->edit->input->type->addOption( 'select' );
$form->edit->input->type->addOption( 'radio' );
$form->edit->input->type->addOption( 'checkbox' );
$form->edit->input->type->addOption( 'file' );

$form->edit->addInput( 'checked', 'select' );
$form->edit->input->checked->setTitle( 'Validation' );
$form->edit->input->checked->addOption( 'any' );
$form->edit->input->checked->addOption( 'email' );
$form->edit->input->checked->addOption( 'url' );
$form->edit->input->checked->addOption( 'phone' );
$form->edit->input->checked->addOption( 'number' );

$form->edit->addInput( 'attr', 'text' );
$form->edit->input->attr->setTitle( 'Attributes' );
$form->edit->input->attr->setSize( 40 );
$form->edit->input->attr->addtip('Style attributes of input eg. size= height=' );

$form->edit->addInput( 'tips', 'textarea' );
$form->edit->input->tips->setTitle( 'Tips' );
$form->edit->input->tips->setSize( 1, 40 );

$form->edit->addInput( 'default', 'text' );
$form->edit->input->default->setTitle( 'Default' );
$form->edit->input->default->setSize( 40 );
$form->edit->input->default->addtip('set default value of field (multiple values separated by ;' );

$form->edit->addInput( 'option', 'textarea' );
$form->edit->input->option->setTitle( 'Options' );
$form->edit->input->option->setSize( 3, 40 );
$form->edit->input->option->addtip('if selection input type is checkbox (multioptions) or radio or select then this field must be declare and separate by ;' );

$form->edit->addInput( 'mandatory', 'checkbox' );
$form->edit->input->mandatory->setTitle( 'Not Null' );
$form->edit->input->mandatory->setCaption( 'yes' );
$form->edit->input->mandatory->setAlign( 'left' );
$form->edit->input->mandatory->addtip('the field must not null' );

$form->edit->addInput( 'active', 'checkbox' );
$form->edit->input->active->setTitle( 'Active' );
$form->edit->input->active->setCaption( 'active' );
$form->edit->input->active->setAlign( 'left' );
$form->edit->input->active->setDefaultValue( 1 );

$form->edit->onSave('_field_orderby', $form, true);
$form->edit->action();

function _field_orderby()
{
	global $db;
	$i = 0;
	$q = "SELECT id, orderby FROM contact_field ORDER BY orderby ASC";
	$r = $db->getAll($q);
	foreach($r AS $dt)
	{
		$i++;
		if($dt['orderby'] != $i) {
			$q = "UPDATE contact_field SET orderby=$i WHERE id=".$dt['id'];
			$db->Execute($q);
		}
	}
}
