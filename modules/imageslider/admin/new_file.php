<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$form = _lib('pea', 'imageslider');
$form->initEdit();
$form->edit->setLanguage();

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Add New Image');

$form->edit->addInput('cat_id', 'text');
$form->edit->input->cat_id->setTitle('cat');
$form->edit->input->cat_id->setDefaultValue(219);

$form->edit->addInput('title','text');
$form->edit->input->title->setLanguage();

$form->edit->addInput('image','file');
$form->edit->input->image->setRequire();
$form->edit->input->image->setAllowedExtension(array('jpg', 'gif', 'png', 'bmp'));
$form->edit->input->image->setFolder(_ROOT.'images/testupload/');

echo $form->edit->getForm();
