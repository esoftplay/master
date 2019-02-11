<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tabs = array('Links' => '', 'Add Link' => '');
include 'advertise_edit.php';
$tabs['Add Link'] = $form->edit->getForm();

$form = _lib('pea', 'links_ad');
$form->initRoll('WHERE 1 ORDER BY name ASC', 'id');

$form->roll->addInput('name', 'sqllinks');
$form->roll->input->name->setTitle('ID Name');
$form->roll->input->name->setLinks($Bbc->mod['circuit'].'.advertise_edit');

$form->roll->addInput('content', 'sqlplaintext');
$form->roll->input->content->setTitle('Content');

$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('Publish');

$form->roll->action();
$tabs['Links'] = $form->roll->getForm();

echo implode('<br />', $tabs);
