<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  $str_table = 'bbc_user_field' );

$form->initRoll( 'WHERE group_id=0 ORDER BY orderby, id ASC', 'id' );

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'title' );
$form->roll->input->title->setLinks( 'index.php?mod=_cpanel.user&act=field-edit' );

$form->roll->addInput( 'type', 'sqlplaintext' );
$form->roll->input->type->setTitle( 'type' );

$form->roll->addInput( 'orderby', 'orderby' );
$form->roll->input->orderby->setTitle( 'Ordered' );

$form->roll->addInput( 'mandatory', 'checkbox' );
$form->roll->input->mandatory->setTitle( 'not null' );
$form->roll->input->mandatory->setCaption( 'not null' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'active' );

$form->roll->onSave('field_repair');
$form->roll->onDelete('field_repair');

function field_orderby()
{
	global $form, $db;
	if($form->type == 'add') {
		$id = $form->edit->getInsertId();
		$q = "SELECT COUNT(*) FROM bbc_user_field WHERE group_id=0";
		$orderby = $db->getOne($q);
		$q = "UPDATE bbc_user_field SET `orderby`=$orderby WHERE id=$id";
		$db->Execute($q);
		field_repair();
	}
}
function field_repair()
{
	global $db;
	$i = 0;
	$q = "SELECT id, orderby FROM bbc_user_field WHERE group_id=0 ORDER BY orderby ASC";
	$r = $db->getAll($q);
	foreach($r AS $dt)
	{
		$i++;
		if($dt['orderby'] != $i) {
			$q = "UPDATE bbc_user_field SET orderby=$i WHERE id=".$dt['id'];
			$db->Execute($q);
		}
	}
}