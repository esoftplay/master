<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (empty($table))
{
	$table  = 'bbc_content_comment';
}
if (empty($i_field))
{
	$i_field  = 'content';
}
if (empty($i_list))
{
	$i_list  = $i_field;
}
if (empty($i_func))
{
	$i_func  = 'content_link';
}

$form = _lib('pea', $table);
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField($i_field.'_title,name,email,website,content', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form->initRoll( $add_sql.' ORDER BY id DESC', 'id');

$form->roll->addInput('email', 'sqllinks');
$form->roll->input->email->setLinks( $Bbc->mod['circuit'].'.comment_edit');
$form->roll->input->email->setDisplayColumn(true);

$form->roll->addInput('name', 'sqlplaintext');
$form->roll->input->name->setDisplayColumn(false);

$form->roll->addInput('website', 'sqlplaintext');
$form->roll->input->website->setDisplayColumn(false);

$title = $i_field.'_title';
$form->roll->addInput($title,'sqlplaintext');
$form->roll->input->$title->setTitle('Title');
$form->roll->input->$title->setDisplayColumn(true);

$form->roll->addInput('content', 'sqlplaintext');
$form->roll->input->content->setTitle('Message');
$form->roll->input->content->setDisplayColumn(true);

$form->roll->addInput('date', 'sqlplaintext');
$form->roll->input->date->setDateFormat();
$form->roll->input->date->setDisplayColumn(false);

$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('publish');

echo $form->roll->getForm();
