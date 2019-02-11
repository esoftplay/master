<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tabs = array('Category' => '', 'Add New' => '');
$form = _lib('pea', 'imageslider_cat');
$form->initEdit('');

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Add New Category');

$form->edit->addInput('title', 'text');
$form->edit->input->title->setTitle('Title');
$form->edit->input->title->setSize('30');

$form->edit->addInput('width', 'text');
$form->edit->input->width->setTitle('Width');
$form->edit->input->width->setSize('5');

$form->edit->addInput('height', 'text');
$form->edit->input->height->setTitle('Height');
$form->edit->input->height->setSize('5');

$form->edit->action();
$tabs['Add New'] = $form->edit->getForm();

$form->initRoll('WHERE 1', 'id');

$form->roll->addInput('title', 'text');
$form->roll->input->title->setTitle('Title');
$form->roll->input->title->setSize('30');

$form->roll->addInput('width', 'text');
$form->roll->input->width->setTitle('Width');
$form->roll->input->width->setSize('5');

$form->roll->addInput('height', 'text');
$form->roll->input->height->setTitle('Height');
$form->roll->input->height->setSize('5');

$form->roll->action();
$form->roll->onDelete('imageslider_cat_delete');
$tabs['Category'] = $form->roll->getForm();

echo tabs($tabs, 1, 'tabs_links');
function imageslider_cat_delete($ids)
{
	global $db, $Bbc;
	$ids = implode(',', $ids);
	if(!empty($ids))
	{
		$q = "SELECT id FROM imageslider WHERE cat_id IN ($ids)";
		$r = $db->getCol($q);
		imageslider_delete($r);
	}
}
