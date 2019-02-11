<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tabs = array('Links' => '', 'Add Link' => '');
$form = _lib('pea', 'links');
$form->initEdit('');

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Update Content');

$form->edit->addInput('title', 'text');
$form->edit->input->title->setTitle('Title');
$form->edit->input->title->setRequire();

$form->edit->addInput('image','file');
$form->edit->input->image->setTitle('Logo');

$form->edit->addInput('link', 'text');
$form->edit->input->link->setTitle('Link');
$form->edit->input->link->setRequire();

$form->edit->addInput('orderby','orderby');

$form->edit->addInput('publish', 'checkbox');
$form->edit->input->publish->setTitle('Publish');
$form->edit->input->publish->setCaption('Published');
$form->edit->input->publish->setDefaultValue('1');

$form->edit->action();
$tabs['Add Link'] = $form->edit->getForm();

$form = _lib('pea', 'links');
$form->initRoll('WHERE 1 ORDER BY orderby ASC', 'id');

$form->roll->addInput('title', 'text');
$form->roll->input->title->setTitle('Title');
$form->roll->input->title->setRequire();

$form->roll->addInput('image','file');
$form->roll->input->image->setTitle('Logo');
$form->roll->input->image->setImageClick( true );

$form->roll->addInput('link', 'text');
$form->roll->input->link->setTitle('Link');
$form->roll->input->link->setRequire();

$form->roll->addInput('orderby', 'orderby');
$form->roll->input->orderby->setTitle('Orderby');

$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setTitle('Publish');

$form->roll->action();
$tabs['Links'] = $form->roll->getForm();

echo tabs($tabs, 1, 'tabs_links');
