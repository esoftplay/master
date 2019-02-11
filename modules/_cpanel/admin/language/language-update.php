<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$id = @intval($id);
$where = $id ? 'WHERE id='.$id : '';
$title = $id ? 'Edit Language' : 'Add Language';
if ($id) {
	$sys->nav_add('Edit Language');
}
$sys->link_js(_URL.'includes/lib/ckeditor/ckeditor.js');
$sys->link_js(_URL.'modules/_cpanel/admin/language/language-list.js', false);

$form1 = _lib('pea', 'bbc_lang_code');
$form1->initEdit($where);
$form1->edit->setLanguage('code_id', 'bbc_lang_text');

$form1->edit->addInput('header', 'header');
$form1->edit->input->header->setTitle($title);

$form1->edit->addInput('module_id', 'selecttable');
$form1->edit->input->module_id->setTitle('Module');
$form1->edit->input->module_id->addOption('GLOBAL SITE', '0');
$form1->edit->input->module_id->setReferenceTable('bbc_module ORDER BY name');
$form1->edit->input->module_id->setReferenceField('name', 'id');
$form1->edit->input->module_id->setDefaultValue(@$keyword['module_id']);

$form1->edit->addInput('code', 'text');
$form1->edit->input->code->setTitle('Words');
$form1->edit->input->code->setSize(40);

$form1->edit->addInput('content', 'textarea');
$form1->edit->input->content->setTitle('Content');
$form1->edit->input->content->setLanguage();
$form1->edit->input->content->setHTMLEditor();
$form1->edit->input->content->setToolbar( 'basic' );
$form1->edit->input->content->setDefaultValue( ' ' );

$_func = $id ? '_language_lower_edit' : '_language_lower_add';
$form1->edit->onSave( $_func );

$language_update= $form1->edit->getForm();
function _language_lower_edit()
{
	global $db;
	$id = intval($_GET['id']);
	$q = "UPDATE bbc_lang_code SET code=LOWER(code) WHERE id=$id";
	$db->Execute($q);
	lang_refresh();
}
function _language_lower_add($id)
{
	global $db;
	$q = "UPDATE bbc_lang_code SET code=LOWER(code) WHERE id=$id";
	$db->Execute($q);
	lang_refresh();
}