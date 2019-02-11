<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');


$form = _lib('pea', 'contact');
$form->initSearch();

$form->search->addInput('followed','select');
$form->search->input->followed->addOption('All Status', '');
$form->search->input->followed->addOption('Followed', '1');
$form->search->input->followed->addOption('Not Followed', '0');

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('name,email,params,message,answer', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form = _lib('pea', 'contact');
$form->initRoll("$add_sql ORDER BY `followed`, post_date ASC", 'id');
$form->roll->setSaveTool(false);

$form->roll->addInput('name', 'sqllinks');
$form->roll->input->name->setTitle('Name');
$form->roll->input->name->setLinks($Bbc->mod['circuit'].'.posted_answer');

$form->roll->addInput('email', 'sqlplaintext');
$form->roll->input->email->setTitle('email');

$form->roll->addInput('post_date', 'sqlplaintext');
$form->roll->input->post_date->setTitle('date');
$form->roll->input->post_date->setDateFormat();

$form->roll->addInput('message', 'sqlplaintext');
$form->roll->input->message->setsubstr(0, 150);

$form->roll->addInput('answer', 'sqlplaintext');
$form->roll->input->answer->setsubstr(0, 150);

$form->roll->action();

echo $form->roll->getForm();