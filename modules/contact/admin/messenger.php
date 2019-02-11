<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$conf = get_config('contact', 'widget');
$tabs = array('Messenger' => '', 'Insert New' => '');
$sql = ($id > 0) ? 'WHERE id='.$id : '';

$form = _lib('pea', 'contact_messenger');
$form->initEdit($sql);

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Insert Yahoo Messenger Account');

$form->edit->addInput('name', 'text');
$form->edit->input->name->setTitle('Name');
$form->edit->input->name->setSize('30');

$form->edit->addInput('username', 'text');
$form->edit->input->username->setTitle('Username');
$form->edit->input->username->setSize('40');

$form->edit->addInput('code', 'textarea');
$form->edit->input->code->setTitle('Pingbox Code');
$form->edit->input->code->setSize(3, 40);
$form->edit->input->code->setNl2br(false);
$form->edit->input->code->addTip('Please insert your pingbox code, if you wish to use pingbox for your customer support with Yahoo Messenger\'s pingbox. Please visit http://messenger.yahoo.com/pingbox/ to get your pingbox code');

$q = "SELECT COUNT(*) FROM contact_messenger";
$d = isset($_POST['add_publish']) ? 2 : 1;
$form->edit->addInput('orderby', 'hidden');
$form->edit->input->orderby->setTitle('Orderby');
$form->edit->input->orderby->setDefaultValue($db->getOne($q)+$d);

if(!$conf['auto_check'])
{
	$form->edit->addInput('online', 'checkbox');
	$form->edit->input->online->setTitle('Online');
	$form->edit->input->online->setCaption('Online');
	$form->edit->input->online->setDefaultValue(1);
}

$form->edit->addInput('publish', 'checkbox');
$form->edit->input->publish->setTitle('Publish');
$form->edit->input->publish->setCaption('Published');
$form->edit->input->publish->setDefaultValue(1);

$form->edit->onSave('contact_messenger_refresh');
$tabs['Insert New'] = $form->edit->getForm();

$form = _lib('pea', 'contact_messenger');
$form->initRoll('WHERE 1', 'id');

$form->roll->addInput('name', 'text');

$form->roll->addInput('username', 'text');

$form->roll->addInput('code', 'textarea');
$form->roll->input->code->setTitle('Pingbox Code');
$form->roll->input->code->setNl2br(false);
$form->roll->input->code->addHelp('Please insert your pingbox code, if you wish to use pingbox for your customer support with Yahoo Messenger\'s pingbox. Please visit http://messenger.yahoo.com/pingbox/ to get your pingbox code');

$form->roll->addInput('orderby', 'orderby');
$form->roll->input->orderby->setTitle('Orderby');

if(!$conf['auto_check'])
{
	$form->roll->addInput('online', 'checkbox');
	$form->roll->input->online->setTitle('Online');
	$form->roll->input->online->setCaption('Online');
}

$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setCaption('Publish');

$form->roll->onSave('contact_messenger_refresh');
$form->roll->onDelete('contact_messenger_refresh');
$tabs['Messenger'] = $form->roll->getForm();
echo tabs($tabs);
function contact_messenger_refresh()
{
	global $sys;
	$sys->clean_cache();
}
