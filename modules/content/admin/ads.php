<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$db->Execute("UPDATE `bbc_content_ad` SET `active`=0 WHERE `expire`=1 AND `expire_date` < NOW()");
$q = "SELECT id, par_id, title FROM bbc_content_cat AS c
	LEFT JOIN bbc_content_cat_text AS t ON (c.id=t.cat_id AND t.lang_id=".lang_id().")
	ORDER BY c.par_id, t.title ASC";
$cats = $db->getAll($q);

$form = _lib('pea', 'bbc_content_ad');
$form->initSearch();

$form->search->addInput( 'cat_id', 'select' );
$form->search->input->cat_id->setTitle('Category');
$form->search->input->cat_id->addOption('--Select Category--', '');
$form->search->input->cat_id->addOption('All Category', '0');
$form->search->input->cat_id->addOption(_func('array', 'path', $cats, 0, '', '', '--'));

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('title, link', false);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form->initRoll("{$add_sql} ORDER BY id DESC");

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Content Ad List');

$form->roll->addInput('image','file');
$form->roll->input->image->setTitle('Image');
$form->roll->input->image->setFolder(_ROOT.'images/modules/content/ads/');
$form->roll->input->image->setImageClick();
$form->roll->input->image->setPlaintext(true);

$form->roll->addInput('title','sqllinks');
$form->roll->input->title->setLinks($Bbc->mod['circuit'].'.ads_edit');

$form->roll->addInput( 'type_id', 'select' );
$form->roll->input->type_id->setTitle('Type');
$form->roll->input->type_id->addOption(array('1'=>'Banner', '2'=>'Text Only', '0'=>'Logo Text'));
$form->roll->input->type_id->setPlaintext(true);

$form->roll->addInput( 'cat_id', 'select' );
$form->roll->input->cat_id->setTitle('Category');
$form->roll->input->cat_id->addOption('All Category', 0);
$form->roll->input->cat_id->addOption(_func('array', 'path', $cats));
$form->roll->input->cat_id->setPlaintext(true);

$form->roll->addInput('hit','texttip');
$form->roll->input->hit->setTitle('Click');
$form->roll->input->hit->setNumberFormat();
$form->roll->input->hit->setTemplate('Last Clicked: {hit_last}<br />Linked to: {link}');
$form->roll->input->hit_last->setDateFormat('M jS, Y H:i:s', 'Never');

$form->roll->addInput('created','texttip');
$form->roll->input->created->setTitle('Create');
$form->roll->input->created->setDateFormat();
$form->roll->input->created->setTemplate('Last Update: {updated}<br />Expired On: {expire_date}');
$form->roll->input->updated->setDateFormat();
$form->roll->input->expire_date->setDateFormat('M jS, Y', 'Never');

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle('Status');
$form->roll->input->active->setCaption('Active');

include 'ads_edit.php';
$form->roll->action();
echo $form->roll->getForm();
echo $form->edit->getForm();