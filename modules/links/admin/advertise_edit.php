<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sql = !empty($_GET['id']) ? 'WHERE id='.intval($_GET['id']) : '';
$form = _lib('pea', 'links_ad');
$form->initEdit($sql);

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Advertising Content');

$form->edit->addInput('name', 'text');
$form->edit->input->name->setTitle('ID Name');
$form->edit->input->name->setRequire();

$form->edit->addInput('content', 'textarea');
$form->edit->input->content->setTitle('Ad Content');
$form->edit->input->content->setSize(200, '100%');
$form->edit->input->content->setHtmlEditor(true);
$form->edit->input->content->setToolbar('Basic');

$form->edit->addInput('javascript', 'textarea');
$form->edit->input->javascript->setTitle('Ad JavaScript');
$form->edit->input->javascript->setCodeEditor(true, 'js');

$form->edit->addInput('publish', 'checkbox');
$form->edit->input->publish->setTitle('Publish');
$form->edit->input->publish->setCaption('Published');
$form->edit->input->publish->setDefaultValue('1');

$form->edit->onSave('links_advertise');
$form->edit->action();
function links_advertise($id)
{
	global $db;
	if(!empty($_GET['id']))
	{
		$id = intval($_GET['id']);
	}
	$txt = menu_save($db->getOne("SELECT name FROM links_ad WHERE id={$id}"));
	$db->Execute("UPDATE links_ad SET name='{$txt}' WHERE id={$id}");
}

echo explain('Use this embed script to display ad :<br /><code>&lt;script type=&quot;text/javascript&quot; src=&quot;'._URL.'links/ad/[ID Name]&quot;&gt;&lt;/script&gt;</code><br /><br /><b>NB:</b> <i>Replace [ID Name] with ID Name of ad. Place code above to where you want to display the ad</i>');
