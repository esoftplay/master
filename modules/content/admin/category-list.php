<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$category_list_title = ($category_id > 0) ? 'Sub Category' : 'List Category';

$form = _lib('pea', 'bbc_content_cat AS c LEFT JOIN bbc_content_cat_text AS t ON (c.id=t.cat_id AND t.lang_id='.lang_id().')');

$form->initRoll("WHERE par_id=$category_id $q_list ORDER BY title");

$form->roll->addInput('id', 'sqlplaintext');
$form->roll->input->id->setFieldName('`id` AS list_id');
if (config('manage', 'cat_img') == '1')
{
	$form->roll->addInput('image','file');
	$form->roll->input->image->setTitle('image');
	$form->roll->input->image->setPlaintext( true );
	$form->roll->input->image->setImageClick( true );
}
$form->roll->addInput('col','multiinput');
$form->roll->input->col->setTitle('Title');
$form->roll->input->col->setDelimiter(' ');
$form->roll->input->col->addInput('title', 'sqllinks');
$form->roll->input->col->addInput('visit', 'editlinks');

$form->roll->input->title->setLinks( $base_link );
$form->roll->input->title->setExtra( 'title="edit page"' );

$form->roll->input->visit->setIcon('fa-external-link', 'open page');
$form->roll->input->visit->setLinks(_URL.'id.htm');
$form->roll->input->visit->setGetName('cat_id');
$form->roll->input->visit->setExtra('target="external"');
$form->roll->input->visit->setFieldName('id AS visit');
$show_config = true;
if (!$sub_content)
{
	$c = $db->getOne("SELECT COUNT(*) FROM `bbc_content_type` WHERE 1");
	if ($c > 1)
	{
		$form->roll->addInput( 'type_id', 'selecttable' );
		$form->roll->input->type_id->setTitle('Type');
		$form->roll->input->type_id->setReferenceTable('bbc_content_type');
		$form->roll->input->type_id->setReferenceField( 'title', 'id' );
		$form->roll->input->type_id->setPlaintext(true);
		$show_config = false;
	}
}
if ($show_config)
{
	$form->roll->addInput('is_config', 'select');
	$form->roll->input->is_config->setTitle('Param');
	$form->roll->input->is_config->addOption('Global', '0');
	$form->roll->input->is_config->addOption('Custom', '1');
	$form->roll->input->is_config->setPlaintext(true);
}

$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('publish');

$form->roll->onSave('content_category_update');
$form->roll->onDelete('content_category_delete');

$category_list = $form->roll->GetForm().explain('Deleting category will not affect to the contents inside it, you may reset/manage the  content\'s category from "Content List" menu and edit its content', 'PS : ');
if (empty($db->resid))
{
	$r = $db->getCol("SHOW COLUMNS FROM `bbc_content_cat`");
	if (!in_array('image', $r))
	{
		$db->Execute("ALTER TABLE `bbc_content_cat` ADD `image` VARCHAR(255)  NULL  DEFAULT ''  AFTER `type_id`");
		redirect(seo_uri());
	}
}
