<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$form = _lib('pea','bbc_email');
$form->initSearch();
if (!empty($keyword['module_id']))
{
	$form->search->addExtraField('module_id', $keyword['module_id']);
}else{
	$form->search->addInput('module_id','selecttable');
	$form->search->input->module_id->addOption('--Select Module--','');
	$form->search->input->module_id->setReferenceTable('bbc_module ORDER BY name');
	$form->search->input->module_id->setReferenceField('name','id');
}
$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('name,from_email,from_name,description', $isFullText = true);
$form->search->input->keyword->addSearchField('subject,content', $isFullText = false);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();