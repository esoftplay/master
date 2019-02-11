<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'testimonial');
$form->initSearch();

$form->search->addInput('publish','select');
$form->search->input->publish->addOption('All Status', '');
$form->search->input->publish->addOption('Published', '1');
$form->search->input->publish->addOption('Not Published', '0');

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('name,email,params,message', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form->initRoll("$add_sql ORDER BY `id` DESC", 'id');
#$form->roll->setSaveTool(false);

$form->roll->addInput('name', 'sqllinks');
$form->roll->input->name->setTitle('Name');
$form->roll->input->name->setLinks($Bbc->mod['circuit'].'.list_detail');

$form->roll->addInput('email', 'sqlplaintext');
$form->roll->input->email->setTitle('email');

$form->roll->addInput('date', 'datetime');
$form->roll->input->date->setTitle('date');
$form->roll->input->date->setPlainText(true);

$form->roll->addInput('message', 'text');
$form->roll->input->message->setTitle('Message');
$form->roll->input->message->setsubstr(0, 150);
$form->roll->input->message->setPlainText(true);

$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('Published');

$form->roll->action();

echo $form->roll->getForm();