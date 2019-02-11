<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$form = _lib('pea',  'bbc_menu_cat');

$form->initEdit();

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle('Add Menu Position');

$form->edit->addInput( 'name', 'text' );
$form->edit->input->name->setTitle( 'Position' );

$form->edit->addInput( 'orderby', 'orderby' );
$form->edit->action();

$form->initRoll( 'WHERE 1 ORDER BY orderby ASC', "id" );

$form->roll->addInput( 'name', 'text' );
$form->roll->input->name->setTitle( 'Name' );

$form->roll->addInput( 'orderby', 'orderby' );
$form->roll->input->orderby->setTitle( 'ordered' );

$form->roll->onSave('menu_repair');
$form->roll->onDelete('menu_position_delete');

echo $form->roll->getForm();
echo $form->edit->getForm();
function menu_position_delete($ids)
{
	global $db;
	ids($ids);
	if(!empty($ids))
	{
		$q = "SELECT id FROM bbc_menu WHERE cat_id IN ($ids) AND is_admin=0";
		$r = $db->getCol($q);
		menu_delete($r);
		menu_repair();
	}
}