<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$q             = "SELECT id, CONCAT(title,' (',width,'x',height,' pixel)') AS title FROM imageslider_cat ORDER BY title ASC";
$r_cat         = $db->getAssoc($q);
$r_cat_key     = array_keys($r_cat);
$is_desc_exist = imageslider_isdesc();

$form = _lib('pea', 'imageslider');
$form->initEdit('WHERE id='.@intval($_GET['id']));
$form->edit->setLanguage();

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Edit Image');

$form->edit->addInput('cat_id','select');
$form->edit->input->cat_id->setTitle('Category');
$form->edit->input->cat_id->addOptionArray($r_cat);

$form->edit->addInput('title','text');
$form->edit->input->title->setRequire();
$form->edit->input->title->setLanguage();
if (!empty($is_desc_exist))
{
	$form->edit->addInput('description','textarea');
	$form->edit->input->description->setLanguage();
}
$form->edit->addInput('image','file');
$form->edit->input->image->setImageClick();
$form->edit->input->image->setRequire('any');
$form->edit->input->image->setAllowedExtension(array('jpg', 'gif', 'png', 'bmp'));
if (get_config('imageslider', 'config', 'thumbnail'))
{
	$form->edit->input->image->setThumbnail(get_config('imageslider', 'config', 'thumbnail'), $prefix = 'thumb', false);
}

$form->edit->addInput('link', 'text');
// $form->edit->input->link->setRequire('url', false);
$form->edit->input->link->addTip('This is the real link in the system, normal format will be index.php?mod=[module_name].[task_name] you can also copy from URL bar and the system will automatically find out the real Link is.');

$form->edit->addInput('publish', 'checkbox');
$form->edit->input->publish->setTitle('Publish');
$form->edit->input->publish->setCaption('Published');

$form->edit->onSave('imageslider_save');
$form->edit->action();
echo $form->edit->getForm();
link_js('list_edit.js');