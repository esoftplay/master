<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$form = _lib('pea','bbc_lang_code');
$form->initSearch();
if (!empty($keyword['module_id']))
{
	$form->search->addExtraField('module_id', $keyword['module_id']);
}else{
	$form->search->addInput('module_id','selecttable');
	$form->search->input->module_id->addOption('--Select Module--','');
	$form->search->input->module_id->addOption('--GLOBAL SITE--','0');
	$form->search->input->module_id->setReferenceTable('bbc_module ORDER BY name');
	$form->search->input->module_id->setReferenceField('name','id');
}
$r_lang = lang_assoc();
if (count($r_lang) > 1)
{
	$form->search->addInput( 'lang_id', 'selecttable' );
	$form->search->input->lang_id->setReferenceTable('bbc_lang');
	$form->search->input->lang_id->setReferenceField( 'title', 'id' );
}else{
	$form->search->addExtraField('lang_id', lang_id());
}
$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('code,content', false);
// $form->search->input->keyword->addSearchField('name,content,content_id', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();