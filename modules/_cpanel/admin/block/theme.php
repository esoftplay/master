<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea','bbc_block_theme');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('name, content', true);

$form->search->addInput('template_id','hidden');
$form->search->input->template_id->setDefaultValue($template_id);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form2 = _lib('pea',  $str_table = "bbc_block_theme" );

$form2->initAdd( '', 'id' );

$form2->add->addInput('header','header');
$form2->add->input->header->setTitle('Add Theme Block for "'.$_CONFIG['template'].'"');

$form2->add->addInput( 'name', 'text' );

$form2->add->addInput( 'content', 'textarea' );
$form2->add->input->content->setTitle( 'theme' );
$form2->add->input->content->setCodeEditor( true, 'html');

$form2->add->addInput( 'active', 'checkbox' );
$form2->add->input->active->setCaption( 'publish' );

$form2->add->addExtraField('template_id', $template_id );
$form2->add->action();

$form = _lib('pea',  $str_table = "bbc_block_theme" );
$form->initRoll( $add_sql.' ORDER BY `name` ASC', "id" );

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Block Themes on "'.$_CONFIG['template'].'"');

$form->roll->addInput( 'name', 'sqllinks' );
$form->roll->input->name->setLinks( $Bbc->mod['circuit'].'.block&act=theme_edit'.$add_link );

$q = "SELECT theme_id, COUNT(*) AS total FROM bbc_block WHERE template_id={$template_id} GROUP BY theme_id";
$form->roll->addInput( 'id', 'select' );
$form->roll->input->id->setTitle( 'Used' );
$form->roll->input->id->setFieldName( 'id AS theme_id' );
$form->roll->input->id->addOption($db->getAssoc($q));
$form->roll->input->id->setPlaintext( true );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setCaption( 'publish' );

echo $form->roll->getForm();
echo $form2->add->getForm();
