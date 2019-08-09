<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$q = "SHOW TABLES LIKE 'links_share'";
if (!$db->getOne($q))
{
	$q = "CREATE TABLE `links_share` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`title` varchar(150) DEFAULT NULL,
		`description` text,
		`image` varchar(255) DEFAULT NULL,
		`link` varchar(255) DEFAULT NULL,
		`total` bigint(20) DEFAULT '0',
		`publish` tinyint(1) DEFAULT '1',
		`created` datetime DEFAULT CURRENT_TIMESTAMP,
		`updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
		KEY `total` (`total`),
		KEY `publish` (`publish`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	$db->Execute($q);
}
$form = _lib('pea',  'links_share');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('title,description,link', false);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

echo $form->search->getForm();

$form->initEdit('');

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Add Share Link');

$form->edit->addInput('title', 'text');
$form->edit->input->title->setTitle('Title');
$form->edit->input->title->setRequire();

$form->edit->addInput('link', 'text');
$form->edit->input->link->setTitle('Link');
$form->edit->input->link->setRequire('url', false);
$form->edit->input->link->setCaption('Leave it blank to direct to homepage');

$form->edit->addInput('description', 'textarea');
$form->edit->input->description->setTitle('Notes');

$form->edit->addInput('publish', 'checkbox');
$form->edit->input->publish->setTitle('Active');
$form->edit->input->publish->setCaption('Activate');
$form->edit->input->publish->setDefaultValue('1');

$form->edit->onSave('links_share');
$form->edit->action();


$form->initRoll($add_sql.' ORDER BY id DESC', 'id');

$form->roll->addInput('header', 'header');
$form->roll->input->header->setTitle('Share Links in QR Code');

$form->roll->addInput('image', 'file');
$form->roll->input->image->setTitle('QR');
$form->roll->input->image->setFolder($Bbc->mod['dir']);
$form->roll->input->image->setImageClick();
$form->roll->input->image->setPlaintext(true);
$form->roll->input->image->setDisplayColumn(true);

$form->roll->addInput('total', 'sqlplaintext');
$form->roll->input->total->setTitle('Hits');
$form->roll->input->total->setNumberFormat();
$form->roll->input->total->setDisplayColumn(true);

$form->roll->addInput('title', 'text');
$form->roll->input->title->setTitle('Title');
$form->roll->input->title->setRequire();

$form->roll->addInput('description', 'textarea');
$form->roll->input->description->setDisplayColumn(false);

$form->roll->addInput('link', 'text');
$form->roll->input->link->setDisplayColumn(true);

$form->roll->addInput('result', 'sqlplaintext');
$form->roll->input->result->setFieldName('id AS result');
$form->roll->input->result->setDisplayColumn(false);
$form->roll->input->result->setDisplayFunction(function($id) {
	return _URL.'links/'.trim(encode($id), '=');
});

$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setTitle('Active');
$form->roll->input->publish->setDisplayColumn(true);

$form->roll->addInput('created', 'sqlplaintext');
$form->roll->input->created->setTitle('Create');
$form->roll->input->created->setDateformat();
$form->roll->input->created->setDisplayColumn(false);

$form->roll->addInput('updated', 'sqlplaintext');
$form->roll->input->updated->setTitle('Update');
$form->roll->input->updated->setDateformat();
$form->roll->input->updated->setDisplayColumn(false);

$form->roll->action();

echo $form->roll->getForm();
echo $form->edit->getForm();

function links_share($id)
{
	global $db, $Bbc;
	$qr = _lib('qr', _URL.'links/'.trim(encode($id), '='));
	$qr->setLevel('H');
	$qr->setSize(10);
	$img = $qr->getDir();
	if (is_file($img))
	{
		rename($img, $Bbc->mod['dir'].$id.'.png');
		$db->Update('links_share', ['image' => $id.'.png'], $id);
	}
}